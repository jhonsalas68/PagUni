<?php

namespace App\Http\Controllers;

use App\Models\AsistenciaDocente;
use App\Models\Horario;
use App\Models\Profesor;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PanelAsistenciaController extends Controller
{
    /**
     * Parsear hora de forma segura con múltiples formatos
     */
    private function parseHoraSafe($hora)
    {
        if (!$hora) return null;
        
        $formatos = ['H:i:s', 'H:i', 'G:i:s', 'G:i'];
        
        foreach ($formatos as $formato) {
            try {
                return Carbon::createFromFormat($formato, $hora);
            } catch (\Exception $e) {
                continue;
            }
        }
        
        return null;
    }

    /**
     * Panel de control de asistencias del día
     */
    public function panelControlDia(Request $request)
    {
        // Solo administradores pueden acceder
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }

        try {
            $fecha = $request->get('fecha', Carbon::today()->toDateString());
            
            // Validar y parsear la fecha de forma segura
            if (!$fecha || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
                $fecha = Carbon::today()->toDateString();
            }
            
            $fechaCarbon = Carbon::createFromFormat('Y-m-d', $fecha);
            $diaSemana = $fechaCarbon->dayOfWeek;
            $diaSemana = $diaSemana === 0 ? 7 : $diaSemana;
        } catch (\Exception $e) {
            // Si hay error con la fecha, usar hoy
            $fecha = Carbon::today()->toDateString();
            $fechaCarbon = Carbon::today();
            $diaSemana = $fechaCarbon->dayOfWeek;
            $diaSemana = $diaSemana === 0 ? 7 : $diaSemana;
        }

        // Obtener todos los horarios del día
        $horariosDelDia = Horario::with(['cargaAcademica.grupo.materia', 'cargaAcademica.profesor', 'aula'])
            ->whereJsonContains('dias_semana', strtolower($this->getDiaNombre($diaSemana)))
            ->orderBy('hora_inicio')
            ->get();

        // Obtener asistencias del día
        $asistenciasDelDia = AsistenciaDocente::with(['profesor'])
            ->whereDate('fecha', $fecha)
            ->get()
            ->groupBy('horario_id')
            ->map(function($asistencias) {
                return $asistencias->sortByDesc('numero_sesion')->first();
            });

        // Estadísticas del día
        $estadisticas = [
            'total_clases' => $horariosDelDia->count(),
            'profesores_presentes' => $asistenciasDelDia->whereIn('estado', ['presente', 'en_clase'])->count(),
            'profesores_ausentes' => $horariosDelDia->count() - $asistenciasDelDia->count(),
            'tardanzas' => $asistenciasDelDia->where('estado', 'tardanza')->count(),
            'justificados' => $asistenciasDelDia->where('estado', 'justificada')->count(),
            'clases_virtuales' => $asistenciasDelDia->where('modalidad', 'virtual')->count(),
            'clases_presenciales' => $asistenciasDelDia->where('modalidad', 'presencial')->count(),
        ];

        // Estado actual por profesor
        $estadoProfesores = $horariosDelDia->map(function($horario) use ($asistenciasDelDia) {
            $asistencia = $asistenciasDelDia->get($horario->id);
            $ahora = Carbon::now();
            
            try {
                // Determinar estado actual con manejo seguro de horas
                $horaInicio = $this->parseHoraSafe($horario->hora_inicio);
                $horaFin = $this->parseHoraSafe($horario->hora_fin);
                
                $estadoActual = 'programado';
                if ($asistencia) {
                    if ($asistencia->estado === 'en_clase') {
                        $estadoActual = 'en_clase';
                    } elseif ($asistencia->estado === 'presente') {
                        $estadoActual = 'completado';
                    } elseif ($asistencia->estado === 'tardanza') {
                        $estadoActual = 'tardanza';
                    } elseif ($asistencia->estado === 'justificada') {
                        $estadoActual = 'justificado';
                    }
                } elseif ($horaFin && $ahora->gt($horaFin)) {
                    $estadoActual = 'ausente';
                } elseif ($horaInicio && $horaFin && $ahora->between($horaInicio, $horaFin)) {
                    $estadoActual = 'en_horario_sin_registro';
                }
            } catch (\Exception $e) {
                $estadoActual = 'error';
            }

            return [
                'horario' => $horario,
                'asistencia' => $asistencia,
                'estado_actual' => $estadoActual,
                'profesor' => $horario->cargaAcademica->profesor ?? null,
            ];
        });

        return view('admin.panel-asistencia', compact('estadoProfesores', 'estadisticas', 'fecha', 'fechaCarbon'));
    }

    /**
     * API para obtener estado en tiempo real
     */
    public function apiEstadoTiempoReal(Request $request)
    {
        try {
            $fecha = $request->get('fecha', Carbon::today()->toDateString());
            
            // Validar formato de fecha
            if (!$fecha || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
                $fecha = Carbon::today()->toDateString();
            }
            
            $fechaCarbon = Carbon::createFromFormat('Y-m-d', $fecha);
            $diaSemana = $fechaCarbon->dayOfWeek;
            $diaSemana = $diaSemana === 0 ? 7 : $diaSemana;
        } catch (\Exception $e) {
            $fecha = Carbon::today()->toDateString();
            $fechaCarbon = Carbon::today();
            $diaSemana = $fechaCarbon->dayOfWeek;
            $diaSemana = $diaSemana === 0 ? 7 : $diaSemana;
        }

        $horariosDelDia = Horario::with(['cargaAcademica.grupo.materia', 'cargaAcademica.profesor', 'aula'])
            ->whereJsonContains('dias_semana', strtolower($this->getDiaNombre($diaSemana)))
            ->orderBy('hora_inicio')
            ->get();

        $asistenciasDelDia = AsistenciaDocente::with(['profesor'])
            ->whereDate('fecha', $fecha)
            ->get()
            ->groupBy('horario_id')
            ->map(function($asistencias) {
                return $asistencias->sortByDesc('numero_sesion')->first();
            });

        $estadoActual = $horariosDelDia->map(function($horario) use ($asistenciasDelDia) {
            $asistencia = $asistenciasDelDia->get($horario->id);
            $ahora = Carbon::now();
            
            try {
                $horaInicio = $this->parseHoraSafe($horario->hora_inicio);
                $horaFin = $this->parseHoraSafe($horario->hora_fin);
                
                $estado = 'programado';
                if ($asistencia) {
                    $estado = $asistencia->estado;
                } elseif ($horaFin && $ahora->gt($horaFin)) {
                    $estado = 'ausente';
                } elseif ($horaInicio && $horaFin && $ahora->between($horaInicio, $horaFin)) {
                    $estado = 'en_horario_sin_registro';
                }
            } catch (\Exception $e) {
                $estado = 'error';
            }

            return [
                'horario_id' => $horario->id,
                'profesor' => [
                    'id' => $horario->cargaAcademica->profesor->id ?? null,
                    'nombre' => ($horario->cargaAcademica->profesor->nombre ?? '') . ' ' . ($horario->cargaAcademica->profesor->apellido ?? ''),
                ],
                'materia' => $horario->cargaAcademica->grupo->materia->nombre ?? 'N/A',
                'aula' => $horario->aula->codigo_aula ?? 'N/A',
                'horario' => ($horario->hora_inicio ?? 'N/A') . ' - ' . ($horario->hora_fin ?? 'N/A'),
                'estado' => $estado,
                'modalidad' => $asistencia->modalidad ?? null,
                'hora_entrada' => $asistencia->hora_entrada ?? null,
            ];
        });

        return response()->json([
            'fecha' => $fecha,
            'hora_actual' => Carbon::now()->format('H:i:s'),
            'estados' => $estadoActual,
            'resumen' => [
                'total' => $horariosDelDia->count(),
                'presentes' => $estadoActual->whereIn('estado', ['presente', 'en_clase'])->count(),
                'ausentes' => $estadoActual->where('estado', 'ausente')->count(),
                'en_clase' => $estadoActual->where('estado', 'en_clase')->count(),
            ]
        ]);
    }

    /**
     * Justificar asistencia desde el panel administrativo
     */
    public function justificarDesdePanel(Request $request)
    {
        if (session('user_type') !== 'administrador') {
            return response()->json(['error' => 'Acceso denegado'], 403);
        }

        $request->validate([
            'profesor_id' => 'required|exists:profesores,id',
            'horario_id' => 'required|exists:horarios,id',
            'fecha' => 'required|date',
            'justificacion' => 'required|string|max:500',
            'tipo_justificacion' => 'required|in:medica,personal,academica,administrativa,otra',
        ]);

        try {
            $asistencia = AsistenciaDocente::justificarFaltaPosterior(
                $request->profesor_id,
                $request->horario_id,
                $request->fecha,
                $request->justificacion,
                $request->tipo_justificacion,
                session('user_id')
            );

            return response()->json([
                'success' => true,
                'message' => 'Justificación registrada por el administrador',
                'data' => [
                    'estado' => $asistencia->estado,
                    'justificacion' => $asistencia->justificacion,
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