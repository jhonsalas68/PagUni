<?php

namespace App\Http\Controllers;

use App\Models\Profesor;
use App\Models\Horario;
use App\Models\AsistenciaDocente;
use Illuminate\Http\Request;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ProfesorController extends Controller
{
    /**
     * Dashboard del profesor - Vista principal
     */
    public function dashboard()
    {
$profesorId = session('user_id');
        $hoy = today();
        $diaSemana = $hoy->dayOfWeek;
        $diaSemana = $diaSemana === 0 ? 7 : $diaSemana; // Convertir domingo de 0 a 7

        // Obtener horarios del profesor para hoy
        $horariosHoy = Horario::with(['cargaAcademica.grupo.materia', 'aula'])
            ->whereHas('cargaAcademica', function($query) use ($profesorId) {
                $query->where('profesor_id', $profesorId);
            })
            ->whereJsonContains('dias_semana', strtolower($this->getDiaNombre($diaSemana)))
            ->orderBy('hora_inicio')
            ->get();

        // Obtener asistencias de hoy (la más reciente por horario)
        $asistenciasHoy = AsistenciaDocente::where('profesor_id', $profesorId)
            ->whereDate('fecha', $hoy)
            ->orderBy('numero_sesion', 'desc')
            ->get()
            ->groupBy('horario_id')
            ->map(function($asistencias) {
                return $asistencias->first(); // Obtener la sesión más reciente
            });

        // Obtener todos los horarios de la semana
        $horariosSemana = Horario::with(['cargaAcademica.grupo.materia', 'aula'])
            ->whereHas('cargaAcademica', function($query) use ($profesorId) {
                $query->where('profesor_id', $profesorId);
            })
            ->orderBy('hora_inicio')
            ->get();

        // Estadísticas del profesor
        $estadisticas = [
            'clases_hoy' => $horariosHoy->count(),
            'clases_asistidas_hoy' => $asistenciasHoy->whereIn('estado', ['presente', 'en_clase'])->count(),
            'clases_pendientes_hoy' => $horariosHoy->count() - $asistenciasHoy->count(),
            'total_materias' => $horariosSemana->pluck('cargaAcademica.grupo.materia.nombre')->unique()->count(),
        ];

        return view('profesor.dashboard-funcional', compact('horariosHoy', 'horariosSemana', 'asistenciasHoy', 'estadisticas', 'hoy'));
    }

    /**
     * Dashboard simple del profesor para debugging
     */
    public function dashboardSimple()
    {
$profesorId = session('user_id');
        $hoy = today();
        $diaSemana = $hoy->dayOfWeek;
        $diaSemana = $diaSemana === 0 ? 7 : $diaSemana;

        // Obtener horarios del profesor para hoy
        $horariosHoy = Horario::with(['cargaAcademica.grupo.materia', 'aula'])
            ->whereHas('cargaAcademica', function($query) use ($profesorId) {
                $query->where('profesor_id', $profesorId);
            })
            ->whereJsonContains('dias_semana', strtolower($this->getDiaNombre($diaSemana)))
            ->orderBy('hora_inicio')
            ->get();

        // Obtener asistencias de hoy
        $asistenciasHoy = AsistenciaDocente::where('profesor_id', $profesorId)
            ->whereDate('fecha', $hoy)
            ->orderBy('numero_sesion', 'desc')
            ->get()
            ->groupBy('horario_id')
            ->map(function($asistencias) {
                return $asistencias->first();
            });

        // Obtener todos los horarios de la semana
        $horariosSemana = Horario::with(['cargaAcademica.grupo.materia', 'aula'])
            ->whereHas('cargaAcademica', function($query) use ($profesorId) {
                $query->where('profesor_id', $profesorId);
            })
            ->orderBy('hora_inicio')
            ->get();

        return view('profesor.dashboard-simple', compact('horariosHoy', 'horariosSemana', 'asistenciasHoy', 'hoy'));
    }

    /**
     * Dashboard funcional del profesor (versión que definitivamente funciona)
     */
    public function dashboardFuncional()
    {
$profesorId = session('user_id');
        $hoy = today();
        $diaSemana = $hoy->dayOfWeek;
        $diaSemana = $diaSemana === 0 ? 7 : $diaSemana;

        // Obtener horarios del profesor para hoy
        $horariosHoy = Horario::with(['cargaAcademica.grupo.materia', 'aula'])
            ->whereHas('cargaAcademica', function($query) use ($profesorId) {
                $query->where('profesor_id', $profesorId);
            })
            ->whereJsonContains('dias_semana', strtolower($this->getDiaNombre($diaSemana)))
            ->orderBy('hora_inicio')
            ->get();

        // Obtener asistencias de hoy
        $asistenciasHoy = AsistenciaDocente::where('profesor_id', $profesorId)
            ->whereDate('fecha', $hoy)
            ->orderBy('numero_sesion', 'desc')
            ->get()
            ->groupBy('horario_id')
            ->map(function($asistencias) {
                return $asistencias->first();
            });

        // Obtener todos los horarios de la semana
        $horariosSemana = Horario::with(['cargaAcademica.grupo.materia', 'aula'])
            ->whereHas('cargaAcademica', function($query) use ($profesorId) {
                $query->where('profesor_id', $profesorId);
            })
            ->orderBy('hora_inicio')
            ->get();

        // Estadísticas del profesor
        $estadisticas = [
            'clases_hoy' => $horariosHoy->count(),
            'clases_asistidas_hoy' => $asistenciasHoy->whereIn('estado', ['presente', 'en_clase'])->count(),
            'clases_pendientes_hoy' => $horariosHoy->count() - $asistenciasHoy->count(),
            'total_materias' => $horariosSemana->pluck('cargaAcademica.grupo.materia.nombre')->unique()->count(),
        ];

        return view('profesor.dashboard-funcional', compact('horariosHoy', 'horariosSemana', 'asistenciasHoy', 'estadisticas', 'hoy'));
    }

    /**
     * Vista para mostrar QR completo
     */
    public function vistaQR($token)
    {
try {
            $asistencia = AsistenciaDocente::where('qr_token', $token)
                ->whereNotNull('qr_generado_at')
                ->whereNull('qr_escaneado_at')
                ->with(['horario.cargaAcademica.grupo.materia', 'horario.aula', 'profesor'])
                ->first();

            if (!$asistencia) {
                return view('profesor.qr-invalido');
            }

            // Verificar expiración
            if ($asistencia->qr_generado_at->diffInMinutes(now()) > 30) {
                return view('profesor.qr-expirado');
            }

            return view('profesor.mostrar-qr', compact('asistencia', 'token'));

        } catch (\Exception $e) {
            return view('profesor.qr-error', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Mostrar QR como imagen SVG
     */
    public function mostrarQR($token)
    {
        try {
            $asistencia = AsistenciaDocente::where('qr_token', $token)
                ->whereNotNull('qr_generado_at')
                ->whereNull('qr_escaneado_at')
                ->first();

            if (!$asistencia) {
                abort(404, 'QR no encontrado o ya utilizado');
            }

            // Verificar expiración
            if ($asistencia->qr_generado_at->diffInMinutes(now()) > 30) {
                abort(410, 'QR expirado');
            }

            $qrUrl = route('profesor.escanear-qr', ['token' => $token]);
            
            return response(
                QrCode::size(300)
                    ->margin(2)
                    ->generate($qrUrl)
            )->header('Content-Type', 'image/svg+xml');

        } catch (\Exception $e) {
            abort(500, 'Error generando QR');
        }
    }

    /**
     * Generar código QR para una clase
     */
    public function generarQR(Request $request)
    {
$request->validate([
            'horario_id' => 'required|exists:horarios,id',
            'modalidad' => 'required|in:presencial,virtual',
        ]);

        $profesorId = session('user_id');
        
        try {
            // Verificar que el horario pertenece al profesor
            $horario = Horario::whereHas('cargaAcademica', function($query) use ($profesorId) {
                $query->where('profesor_id', $profesorId);
            })->find($request->horario_id);

            if (!$horario) {
                return response()->json(['error' => 'Este horario no te pertenece'], 403);
            }

            $asistencia = AsistenciaDocente::generarQR($profesorId, $request->horario_id, $request->modalidad);

            // Generar URLs del QR
            $qrUrl = route('profesor.escanear-qr', ['token' => $asistencia->qr_token]);
            $qrImageUrl = route('profesor.qr-image', ['token' => $asistencia->qr_token]);

            return response()->json([
                'success' => true,
                'message' => 'Código QR generado exitosamente',
                'data' => [
                    'qr_token' => $asistencia->qr_token,
                    'qr_url' => $qrUrl,
                    'qr_image_url' => $qrImageUrl,
                    'modalidad' => $asistencia->modalidad,
                    'numero_sesion' => $asistencia->numero_sesion,
                    'expira_en' => $asistencia->qr_generado_at->addMinutes(30)->format('H:i'),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al generar QR: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Procesar escaneo de código QR
     */
    public function escanearQR(Request $request, $token)
    {
        try {
            $asistencia = AsistenciaDocente::procesarEscaneoQR(
                $token,
                $request->ip(),
                $request->get('ubicacion')
            );

            // Cargar relaciones para mostrar información completa
            $asistencia->load(['horario.cargaAcademica.grupo.materia', 'horario.aula']);

            return response()->json([
                'success' => true,
                'message' => '¡Asistencia confirmada exitosamente!',
                'data' => [
                    'estado' => $asistencia->estado,
                    'estado_texto' => $asistencia->estado_texto,
                    'modalidad' => $asistencia->modalidad,
                    'numero_sesion' => $asistencia->numero_sesion,
                    'hora_entrada' => $asistencia->hora_entrada,
                    'validado_en_horario' => $asistencia->validado_en_horario,
                    'materia' => $asistencia->horario->cargaAcademica->grupo->materia->nombre ?? 'N/A',
                    'aula' => $asistencia->horario->aula->codigo_aula ?? 'N/A',
                    'grupo' => $asistencia->horario->cargaAcademica->grupo->identificador ?? 'N/A',
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Vista para escanear QR (página web)
     */
    public function vistaEscanearQR($token)
    {
        try {
            $asistencia = AsistenciaDocente::where('qr_token', $token)
                ->whereNotNull('qr_generado_at')
                ->whereNull('qr_escaneado_at')
                ->with(['horario.cargaAcademica.grupo.materia', 'horario.aula', 'profesor'])
                ->first();

            if (!$asistencia) {
                return view('profesor.qr-invalido');
            }

            // Verificar expiración
            if ($asistencia->qr_generado_at->diffInMinutes(now()) > 30) {
                return view('profesor.qr-expirado');
            }

            return view('profesor.confirmar-asistencia', compact('asistencia', 'token'));

        } catch (\Exception $e) {
            return view('profesor.qr-error', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Marcar entrada del profesor (método legacy - mantener compatibilidad)
     */
    public function marcarEntrada(Request $request)
    {
$request->validate([
            'horario_id' => 'required|exists:horarios,id',
        ]);

        $profesorId = session('user_id');
        
        try {
            // Verificar que el horario pertenece al profesor
            $horario = Horario::whereHas('cargaAcademica', function($query) use ($profesorId) {
                $query->where('profesor_id', $profesorId);
            })->find($request->horario_id);

            if (!$horario) {
                return response()->json(['error' => 'Este horario no te pertenece'], 403);
            }

            $asistencia = AsistenciaDocente::registrarEntrada($profesorId, $request->horario_id);

            return response()->json([
                'success' => true,
                'message' => 'Entrada registrada exitosamente',
                'data' => [
                    'estado' => $asistencia->estado,
                    'estado_texto' => $asistencia->estado_texto,
                    'hora_entrada' => $asistencia->hora_entrada,
                    'validado_en_horario' => $asistencia->validado_en_horario,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al registrar entrada: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marcar salida del profesor
     */
    public function marcarSalida(Request $request)
    {
$request->validate([
            'horario_id' => 'required|exists:horarios,id',
        ]);

        $profesorId = session('user_id');
        
        try {
            $asistencia = AsistenciaDocente::registrarSalida($profesorId, $request->horario_id);

            return response()->json([
                'success' => true,
                'message' => 'Salida registrada exitosamente',
                'data' => [
                    'estado' => $asistencia->estado,
                    'estado_texto' => $asistencia->estado_texto,
                    'hora_entrada' => $asistencia->hora_entrada,
                    'hora_salida' => $asistencia->hora_salida,
                    'duracion_clase' => $asistencia->duracion_clase,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al registrar salida: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Visualizar horario propio del profesor
     */
    public function miHorario()
    {
$profesorId = session('user_id');

        // Obtener todos los horarios del profesor
        $horarios = Horario::with(['cargaAcademica.grupo.materia', 'aula'])
            ->whereHas('cargaAcademica', function($query) use ($profesorId) {
                $query->where('profesor_id', $profesorId);
            })
            ->orderBy('hora_inicio')
            ->get();

        return view('profesor.mi-horario', compact('horarios'));
    }

    /**
     * Justificar asistencia/falta
     */
    public function justificarAsistencia(Request $request)
    {
$request->validate([
            'horario_id' => 'required|exists:horarios,id',
            'fecha' => 'required|date',
            'justificacion' => 'required|string|max:500',
            'tipo_justificacion' => 'required|in:medica,personal,academica,administrativa,otra',
        ]);

        $profesorId = session('user_id');

        try {
            // Verificar que el horario pertenece al profesor
            $horario = Horario::whereHas('cargaAcademica', function($query) use ($profesorId) {
                $query->where('profesor_id', $profesorId);
            })->find($request->horario_id);

            if (!$horario) {
                return response()->json(['error' => 'Este horario no te pertenece'], 403);
            }

            $asistencia = AsistenciaDocente::justificarFaltaPosterior(
                $profesorId,
                $request->horario_id,
                $request->fecha,
                $request->justificacion,
                $request->tipo_justificacion,
                $profesorId
            );

            return response()->json([
                'success' => true,
                'message' => 'Justificación registrada exitosamente',
                'data' => [
                    'estado' => $asistencia->estado,
                    'justificacion' => $asistencia->justificacion,
                    'tipo_justificacion' => $asistencia->tipo_justificacion,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al registrar justificación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ver historial de asistencias
     */
    public function historialAsistencias(Request $request)
    {
$profesorId = session('user_id');
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->toDateString());
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth()->toDateString());

        $asistencias = AsistenciaDocente::with(['horario.cargaAcademica.grupo.materia', 'horario.aula'])
            ->where('profesor_id', $profesorId)
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_entrada', 'desc')
            ->paginate(20);

        return view('profesor.historial-asistencias', compact('asistencias', 'fechaInicio', 'fechaFin'));
    }

    // Métodos API originales (mantener compatibilidad)
    public function index()
    {
        $profesores = Profesor::all();
        return response()->json($profesores);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:profesores',
            'cedula' => 'required|string|max:20|unique:profesores',
            'tipo_contrato' => 'required|in:tiempo_completo,medio_tiempo,catedra',
            'telefono' => 'nullable|string|max:20',
            'especialidad' => 'nullable|string',
        ]);

        $profesor = Profesor::create($request->all());
        return response()->json($profesor, 201);
    }

    public function show(Profesor $profesor)
    {
        return response()->json($profesor->load('inscripciones.materia'));
    }

    public function update(Request $request, Profesor $profesor)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:profesores,email,' . $profesor->id,
            'cedula' => 'required|string|max:20|unique:profesores,cedula,' . $profesor->id,
            'tipo_contrato' => 'required|in:tiempo_completo,medio_tiempo,catedra',
            'telefono' => 'nullable|string|max:20',
            'especialidad' => 'nullable|string',
        ]);

        $profesor->update($request->all());
        return response()->json($profesor);
    }

    public function destroy(Profesor $profesor)
    {
        $profesor->delete();
        return response()->json(null, 204);
    }

    /**
     * Convertir número de día a nombre
     */
    private function getDiaNombre($numeroDia)
    {
        $dias = [
            1 => 'lunes',
            2 => 'martes',
            3 => 'miercoles',
            4 => 'jueves',
            5 => 'viernes',
            6 => 'sabado',
            7 => 'domingo'
        ];
        
        return $dias[$numeroDia] ?? 'lunes';
    }
}
