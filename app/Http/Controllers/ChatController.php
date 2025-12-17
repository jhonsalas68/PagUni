<?php

namespace App\Http\Controllers;

use App\Models\Chat\Conversation;
use App\Models\Chat\Message;
use App\Models\Chat\Participant;
use App\Models\Profesor;
use App\Models\Estudiante;
use App\Models\Administrador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\MessageSent;
use App\Models\Materia;
use App\Models\Inscripcion;
use App\Models\Grupo;
use App\Models\Chat\UserOnlineStatus;
use App\Models\CargaAcademica;

class ChatController extends Controller
{
    private function getCurrentUser()
    {
        $userId = session('user_id');
        $userType = session('user_type');

        if (!$userId || !$userType) {
            abort(401, 'Usuario no autenticado.');
        }

        switch ($userType) {
            case 'administrador':
                return Administrador::find($userId);
            case 'profesor':
                return Profesor::find($userId);
            case 'estudiante':
                return Estudiante::find($userId);
            default:
                abort(403, 'Tipo de usuario inválido.');
        }
    }

    private function getCurrentUserTypeModel()
    {
        $userType = session('user_type');
        switch ($userType) {
            case 'administrador':
                return Administrador::class;
            case 'profesor':
                return Profesor::class;
            case 'estudiante':
                return Estudiante::class;
            default:
                return null;
        }
    }

    public function index()
    {
        $user = $this->getCurrentUser();
        $userTypeModel = $this->getCurrentUserTypeModel();

        // Actualizar estado en línea del usuario actual
        UserOnlineStatus::updateUserStatus($user->id, $userTypeModel);

        // Get conversations where the current user is a participant
        $conversations = Conversation::whereHas('participants', function ($query) use ($user, $userTypeModel) {
            $query->where('participant_id', $user->id)
                  ->where('participant_type', $userTypeModel);
        })->with(['participants.participant', 'messages' => function ($query) {
            $query->latest()->limit(1); // Get last message
        }])->latest('updated_at')->get();

        // Agregar información de estado en línea para cada conversación
        foreach ($conversations as $conversation) {
            foreach ($conversation->participants as $participant) {
                if ($participant->participant_id != $user->id || $participant->participant_type != $userTypeModel) {
                    $participant->is_online = UserOnlineStatus::isUserOnline(
                        $participant->participant_id, 
                        $participant->participant_type
                    );
                }
            }
        }

        return view('chat.index', compact('conversations'));
    }

    public function show($id)
    {
        $conversation = Conversation::with(['messages.sender', 'participants.participant'])->findOrFail($id);
        
        // Authorization check: User must be a participant
        $user = $this->getCurrentUser();
        $userTypeModel = $this->getCurrentUserTypeModel();
        
        $isParticipant = $conversation->participants()
            ->where('participant_id', $user->id)
            ->where('participant_type', $userTypeModel)
            ->exists();

        if (!$isParticipant) {
            abort(403, 'No tienes permiso para ver esta conversación.');
        }

        // Agregar información de estado en línea para cada participante
        foreach ($conversation->participants as $participant) {
            $participant->is_online = UserOnlineStatus::isUserOnline(
                $participant->participant_id, 
                $participant->participant_type
            );
        }

        return response()->json([
            'conversation' => $conversation,
            'messages' => $conversation->messages,
            'current_user_id' => $user->id,
            'current_user_type' => session('user_type'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'content' => 'required|string',
        ]);

        $user = $this->getCurrentUser();
        $userTypeModel = $this->getCurrentUserTypeModel();

        // Check if user is participant
        $conversation = Conversation::findOrFail($request->conversation_id);
        $isParticipant = $conversation->participants()
            ->where('participant_id', $user->id)
            ->where('participant_type', $userTypeModel)
            ->exists();

        if (!$isParticipant) {
            abort(403, 'No eres participante de esta conversación.');
        }

        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'sender_type' => $userTypeModel,
            'content' => $request->content,
        ]);

        $conversation->touch(); // Update updated_at of conversation

        // Broadcast event
        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message->load('sender'));
    }

    public function createConversation(Request $request)
    {
        // Logic to start a new conversation (e.g., private chat)
        $request->validate([
            'recipient_id' => 'required',
            'recipient_type' => 'required|in:administrador,profesor,estudiante',
        ]);

        $currentUser = $this->getCurrentUser();
        $currentUserModel = $this->getCurrentUserTypeModel();

        $recipientModelClass = match($request->recipient_type) {
            'administrador' => Administrador::class,
            'profesor' => Profesor::class,
            'estudiante' => Estudiante::class,
        };

        DB::beginTransaction();
        try {
            // Check if conversation already exists (for private chats)
            // This logic can be complex for polymorphic relationships. 
            // Simplified: Create new one.
            
            $conversation = Conversation::create([
                'type' => 'private',
            ]);

            // Add Sender
            $conversation->participants()->create([
                'participant_id' => $currentUser->id,
                'participant_type' => $currentUserModel,
            ]);

            // Add Recipient
            $conversation->participants()->create([
                'participant_id' => $request->recipient_id,
                'participant_type' => $recipientModelClass,
            ]);

            DB::commit();

            return response()->json($conversation);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function getSearchOptions()
    {
        $user = $this->getCurrentUser();
        $userType = session('user_type');
        
        // Si es profesor, mostrar solo sus materias
        if ($userType === 'profesor') {
            $materias = Materia::whereHas('grupos.cargaAcademica', function($q) use ($user) {
                $q->where('profesor_id', $user->id);
            })->where('estado', 'activo')->orderBy('nombre')->get(['id', 'nombre', 'codigo']);
        } else {
            // Para admin y estudiantes, mostrar todas las materias activas
            $materias = Materia::where('estado', 'activo')->orderBy('nombre')->get(['id', 'nombre', 'codigo']);
        }
        
        return response()->json(['materias' => $materias]);
    }

    public function searchUsers(Request $request)
    {
        $query = $request->input('query');
        $role = $request->input('role', 'all'); // all, profesor, estudiante
        $materiaId = $request->input('materia');

        $users = collect();

        // Si se busca por materia, buscar alumnos inscritos
        if ($materiaId) {
            $estudiantes = Estudiante::whereHas('inscripciones.grupo', function($q) use ($materiaId) {
                $q->where('materia_id', $materiaId);
            })->when($query, function($q) use ($query) {
                $q->where(function($sub) use ($query) {
                    $sub->where('nombre', 'like', "%{$query}%")
                        ->orWhere('apellido', 'like', "%{$query}%");
                });
            })->limit(20)->get();

            foreach($estudiantes as $est) {
                $users->push([
                    'id' => $est->id,
                    'name' => $est->nombre . ' ' . $est->apellido,
                    'type' => 'estudiante',
                    'role_label' => 'Estudiante',
                    'email' => $est->email,
                    'initials' => substr($est->nombre, 0, 1) . substr($est->apellido, 0, 1)
                ]);
            }
            
            return response()->json($users);
        }

        // Búsqueda por nombre/rol
        if ($query && ($role === 'all' || $role === 'profesor')) {
            $profesores = Profesor::where('estado', 'activo')
                ->where(function($q) use ($query) {
                    $q->whereRaw('LOWER(nombre) LIKE ?', ['%'.strtolower($query).'%'])
                      ->orWhereRaw('LOWER(apellido) LIKE ?', ['%'.strtolower($query).'%'])
                      ->orWhereRaw('LOWER(CONCAT(nombre, \' \', apellido)) LIKE ?', ['%'.strtolower($query).'%']);
                })->limit(10)->get();

            foreach($profesores as $prof) {
                $users->push([
                    'id' => $prof->id,
                    'name' => $prof->nombre . ' ' . $prof->apellido,
                    'type' => 'profesor',
                    'role_label' => 'Docente',
                    'email' => $prof->email,
                    'initials' => substr($prof->nombre, 0, 1) . substr($prof->apellido, 0, 1)
                ]);
            }
        }

        if ($query && ($role === 'all' || $role === 'estudiante')) {
             $estudiantes = Estudiante::where(function($q) use ($query) {
                    $q->whereRaw('LOWER(nombre) LIKE ?', ['%'.strtolower($query).'%'])
                      ->orWhereRaw('LOWER(apellido) LIKE ?', ['%'.strtolower($query).'%'])
                      ->orWhereRaw('LOWER(CONCAT(nombre, \' \', apellido)) LIKE ?', ['%'.strtolower($query).'%']);
                })->limit(10)->get();

             foreach($estudiantes as $est) {
                $users->push([
                    'id' => $est->id,
                    'name' => $est->nombre . ' ' . $est->apellido,
                    'type' => 'estudiante',
                    'role_label' => 'Estudiante',
                    'email' => $est->email,
                    'initials' => substr($est->nombre, 0, 1) . substr($est->apellido, 0, 1)
                ]);
            }
        }

        // Si es admin logueado, quizás quiera buscar otros admins
        if ($role === 'all') {
             // ... logic for admins if needed
        }

        return response()->json($users);
    }

    public function createGroupConversation(Request $request)
    {
        $request->validate([
            'materia_id' => 'required|exists:materias,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $currentUser = $this->getCurrentUser();
        $currentUserModel = $this->getCurrentUserTypeModel();
        $userType = session('user_type');

        // Solo profesores pueden crear grupos de materia
        if ($userType !== 'profesor') {
            return response()->json(['error' => 'Solo los profesores pueden crear grupos de materia'], 403);
        }

        // Verificar que el profesor enseña esta materia
        $cargaAcademica = CargaAcademica::where('profesor_id', $currentUser->id)
            ->whereHas('grupo', function($q) use ($request) {
                $q->where('materia_id', $request->materia_id);
            })
            ->first();

        if (!$cargaAcademica) {
            return response()->json(['error' => 'No tienes permiso para crear un grupo de esta materia'], 403);
        }

        DB::beginTransaction();
        try {
            // Crear conversación grupal
            $conversation = Conversation::create([
                'type' => 'group',
                'subject_id' => $request->materia_id,
                'title' => $request->title,
                'description' => $request->description,
                'created_by_type' => $currentUserModel,
                'created_by_id' => $currentUser->id,
                'metadata' => json_encode([
                    'materia_id' => $request->materia_id,
                    'created_by' => $currentUser->nombre . ' ' . $currentUser->apellido,
                ])
            ]);

            // Agregar al profesor como administrador del grupo
            $conversation->participants()->create([
                'participant_id' => $currentUser->id,
                'participant_type' => $currentUserModel,
                'role' => 'admin',
                'joined_at' => now(),
            ]);

            // Obtener todos los estudiantes inscritos en la materia
            $estudiantes = Estudiante::whereHas('inscripciones.grupo', function($q) use ($request) {
                $q->where('materia_id', $request->materia_id);
            })->get();

            // Agregar estudiantes al grupo
            foreach ($estudiantes as $estudiante) {
                $conversation->participants()->create([
                    'participant_id' => $estudiante->id,
                    'participant_type' => Estudiante::class,
                    'role' => 'member',
                    'joined_at' => now(),
                ]);
            }

            // Crear mensaje de bienvenida del sistema
            $conversation->messages()->create([
                'sender_id' => $currentUser->id,
                'sender_type' => $currentUserModel,
                'content' => "¡Bienvenidos al grupo de {$conversation->title}! 👋",
                'message_type' => 'system',
                'metadata' => json_encode([
                    'system_message' => true,
                    'action' => 'group_created'
                ])
            ]);

            DB::commit();

            return response()->json([
                'conversation' => $conversation->load('participants'),
                'students_added' => $estudiantes->count(),
                'message' => 'Grupo creado exitosamente con ' . $estudiantes->count() . ' estudiantes'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateOnlineStatus(Request $request)
    {
        $user = $this->getCurrentUser();
        $userTypeModel = $this->getCurrentUserTypeModel();
        
        $status = $request->input('status', 'online');
        
        UserOnlineStatus::updateUserStatus($user->id, $userTypeModel, $status);
        
        return response()->json(['status' => 'updated']);
    }

    public function getOnlineUsers(Request $request)
    {
        $userIds = $request->input('user_ids', []);
        $userType = $request->input('user_type');
        
        if (empty($userIds) || !$userType) {
            return response()->json([]);
        }
        
        $onlineStatus = UserOnlineStatus::getUsersOnlineStatus($userIds, $userType);
        
        return response()->json($onlineStatus);
    }
}
