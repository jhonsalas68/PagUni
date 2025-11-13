<?php

namespace App\Http\Controllers;

use App\Models\Inscripcion;
use App\Models\Grupo;
use App\Models\Estudiante;
use App\Models\PeriodoInscripcion;
use App\Models\Horario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class InscripcionController extends Controller
{
    // Listar materias disponibles para inscripción
    public function index(Request $request)
    {
        $estudianteId = Session::get('user_id');
        $estudiante = Estudiante::findOrFail($estudianteId);
        
        // Verificar si hay un periodo de inscripción activo
        $periodoActivo = PeriodoInscripcion::periodoActual();
        
        if (!$periodoActivo) {
            return view('estudiante.inscripciones.index', [
                'grupos' => collect([]),
                'periodoActivo' => null,
                'mensaje' => 'No hay periodo de inscripción activo en este momento.'
            ]);
        }

        // Obtener IDs de materias en las que ya está inscrito
        $materiasInscritas = Inscripcion::where('estudiante_id', $estudianteId)
            ->where('periodo_academico', $periodoActivo->periodo_academico)
            ->where('estado', 'activo')
            ->with('grupo')
            ->get()
            ->pluck('grupo.materia_id')
            ->toArray();

        // Obtener grupos disponibles de la carrera del estudiante
        $grupos = Grupo::with(['materia', 'cargaAcademica.profesor', 'horarios.aula'])
            ->whereHas('materia', function($query) use ($estudiante) {
                $query->where('carrera_id', $estudiante->carrera_id);
            })
            ->where('estado', 'activo')
            ->where('permite_inscripcion', true)
            ->whereColumn('cupo_actual', '<', 'cupo_maximo')
            ->get()
            ->map(function($grupo) use ($estudianteId, $periodoActivo, $materiasInscritas) {
                // Verificar si el estudiante ya está inscrito en este grupo específico
                $yaInscritoGrupo = Inscripcion::where('estudiante_id', $estudianteId)
                    ->where('grupo_id', $grupo->id)
                    ->where('periodo_academico', $periodoActivo->periodo_academico)
                    ->where('estado', 'activo')
                    ->exists();

                // Verificar si ya está inscrito en la materia (cualquier grupo)
                $yaInscritoMateria = in_array($grupo->materia_id, $materiasInscritas);

                $grupo->ya_inscrito_grupo = $yaInscritoGrupo;
                $grupo->ya_inscrito_materia = $yaInscritoMateria;
                $grupo->cupos_disponibles = $grupo->cuposDisponibles();
                
                return $grupo;
            });

        return view('estudiante.inscripciones.index', compact('grupos', 'periodoActivo'));
    }

    // Procesar inscripción
    public function store(Request $request)
    {
        $request->validate([
            'grupo_id' => 'required|exists:grupos,id',
        ]);

        $estudianteId = Session::get('user_id');
        $estudiante = Estudiante::findOrFail($estudianteId);
        $grupo = Grupo::findOrFail($request->grupo_id);

        // Verificar periodo activo
        $periodoActivo = PeriodoInscripcion::periodoActual();
        if (!$periodoActivo) {
            return back()->with('error', 'No hay periodo de inscripción activo.');
        }

        try {
            DB::beginTransaction();

            // Verificar cupo disponible
            if (!$grupo->tieneCupoDisponible()) {
                throw new \Exception('No hay cupos disponibles para este grupo.');
            }

            // Verificar si ya está inscrito en este grupo específico
            $yaInscritoGrupo = Inscripcion::where('estudiante_id', $estudianteId)
                ->where('grupo_id', $grupo->id)
                ->where('periodo_academico', $periodoActivo->periodo_academico)
                ->where('estado', 'activo')
                ->exists();

            if ($yaInscritoGrupo) {
                throw new \Exception('Ya estás inscrito en este grupo.');
            }

            // Verificar si ya está inscrito en la misma materia en otro grupo
            $yaInscritoMateria = Inscripcion::where('estudiante_id', $estudianteId)
                ->where('periodo_academico', $periodoActivo->periodo_academico)
                ->where('estado', 'activo')
                ->whereHas('grupo', function($query) use ($grupo) {
                    $query->where('materia_id', $grupo->materia_id);
                })
                ->exists();

            if ($yaInscritoMateria) {
                throw new \Exception('Ya estás inscrito en esta materia en otro grupo. Debes dar de baja primero si deseas cambiar de grupo.');
            }

            // Verificar conflictos de horario
            $horariosGrupo = Horario::where('carga_academica_id', function($query) use ($grupo) {
                $query->select('id')
                    ->from('carga_academica')
                    ->where('grupo_id', $grupo->id)
                    ->limit(1);
            })->get();

            $inscripcionesActivas = Inscripcion::where('estudiante_id', $estudianteId)
                ->where('periodo_academico', $periodoActivo->periodo_academico)
                ->where('estado', 'activo')
                ->pluck('grupo_id');

            foreach ($horariosGrupo as $horarioNuevo) {
                $conflicto = Horario::whereIn('carga_academica_id', function($query) use ($inscripcionesActivas) {
                    $query->select('id')
                        ->from('carga_academica')
                        ->whereIn('grupo_id', $inscripcionesActivas);
                })
                ->where(function($query) use ($horarioNuevo) {
                    foreach ($horarioNuevo->dias_semana as $dia) {
                        $query->orWhereJsonContains('dias_semana', $dia);
                    }
                })
                ->where(function($query) use ($horarioNuevo) {
                    $query->where('hora_inicio', '<', $horarioNuevo->hora_fin)
                          ->where('hora_fin', '>', $horarioNuevo->hora_inicio);
                })
                ->exists();

                if ($conflicto) {
                    throw new \Exception('Hay un conflicto de horario con otra materia inscrita.');
                }
            }

            // Crear inscripción
            $inscripcion = Inscripcion::create([
                'estudiante_id' => $estudianteId,
                'grupo_id' => $grupo->id,
                'periodo_academico' => $periodoActivo->periodo_academico,
                'fecha_inscripcion' => now(),
                'estado' => 'activo',
            ]);

            // Incrementar cupo
            $grupo->incrementarCupo();

            DB::commit();

            return redirect()->route('estudiante.mis-materias')
                ->with('success', 'Inscripción realizada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    // Ver materias inscritas
    public function misInscripciones()
    {
        $estudianteId = Session::get('user_id');
        
        $inscripciones = Inscripcion::with([
            'grupo.materia',
            'grupo.cargaAcademica.profesor',
            'grupo.horarios.aula',
            'asistencias'
        ])
        ->where('estudiante_id', $estudianteId)
        ->where('estado', 'activo')
        ->get();

        // Preparar datos adicionales
        $diaHoy = strtolower(\Carbon\Carbon::now()->locale('es')->dayName);
        $clasesHoyPorInscripcion = [];
        
        foreach ($inscripciones as $inscripcion) {
            // Calcular porcentajes y permisos
            $inscripcion->porcentaje_asistencia = $inscripcion->calcularPorcentajeAsistencia();
            $inscripcion->puede_dar_baja = $inscripcion->puedeSerDadoDeBaja();
            
            // Buscar clases de hoy para esta inscripción
            $clasesHoy = [];
            
            foreach ($inscripcion->grupo->horarios as $horario) {
                if (in_array($diaHoy, $horario->dias_semana ?? [])) {
                    // Verificar si ya marcó asistencia hoy
                    $yaMarco = \App\Models\AsistenciaEstudiante::where('inscripcion_id', $inscripcion->id)
                        ->where('horario_id', $horario->id)
                        ->whereDate('fecha', today())
                        ->first();
                    
                    // Verificar si está en horario (15 min antes y después)
                    $horaInicio = \Carbon\Carbon::parse($horario->hora_inicio)->subMinutes(15);
                    $horaFin = \Carbon\Carbon::parse($horario->hora_fin)->addMinutes(15);
                    $ahora = \Carbon\Carbon::now();
                    $enHorario = $ahora->between($horaInicio, $horaFin);
                    
                    $clasesHoy[] = [
                        'horario' => $horario,
                        'ya_marco' => $yaMarco ? true : false,
                        'estado_asistencia' => $yaMarco ? $yaMarco->estado : null,
                        'en_horario' => $enHorario,
                        'puede_marcar' => $enHorario && !$yaMarco,
                    ];
                }
            }
            
            $clasesHoyPorInscripcion[$inscripcion->id] = $clasesHoy;
        }

        return view('estudiante.mis-materias.index', compact('inscripciones', 'clasesHoyPorInscripcion'));
    }

    // Dar de baja una materia
    public function destroy(Request $request, $id)
    {
        $estudianteId = Session::get('user_id');
        
        $inscripcion = Inscripcion::where('id', $id)
            ->where('estudiante_id', $estudianteId)
            ->firstOrFail();

        if (!$inscripcion->puedeSerDadoDeBaja()) {
            return back()->with('error', 'No puedes dar de baja esta materia fuera del periodo de inscripción.');
        }

        try {
            DB::beginTransaction();

            $motivo = $request->input('motivo', 'Baja voluntaria');
            $inscripcion->darDeBaja($motivo);

            DB::commit();

            return back()->with('success', 'Materia dada de baja exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al dar de baja la materia: ' . $e->getMessage());
        }
    }
}
