<?php

namespace App\Http\Controllers;

use App\Models\AsistenciaDocente;
use App\Models\Horario;
use App\Models\Profesor;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AsistenciaDocenteController extends Controller
{
    /**
     * CU-14: Registrar Entrada del Docente
     */
    public function registrarEntrada(Request $request)
    {
        $request->validate([
            'profesor_id' => 'required|exists:profesores,id',
            'horario_id' => 'required|exists:horarios,id',
            'hora_entrada' => 'nullable|date_format:H:i',
        ]);

        try {
            $horaEntrada = $request->hora_entrada 
                ? Carbon::createFromFormat('H:i', $request->hora_entrada)
                : now();

            $asistencia = AsistenciaDocente::registrarEntrada(
                $request->profesor_id,
                $request->horario_id,
                $horaEntrada
            );

            $horario = Horario::with(['cargaAcademica.grupo.materia', 'aula'])->find($request->horario_id);

            return response()->json([
                'success' => true,
                'message' => 'Entrada registrada exitosamente',
                'data' => [
                    'asistencia_id' => $asistencia->id,
                    'estado' => $asistencia->estado,
                    'estado_texto' => $asistencia->estado_texto,
                    'hora_entrada' => $asistencia->hora_entrada,
                    'validado_en_horario' => $asistencia->validado_en_horario,
                    'materia' => $horario->cargaAcademica->grupo->materia->nombre ?? 'N/A',
                    'aula' => $horario->aula->codigo_aula ?? 'N/A',
                    'horario_programado' => $horario->hora_inicio . '-' . $horario->hora_fin,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar entrada: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * CU-15: Registrar Salida del Docente
     */
    public function registrarSalida(Request $request)
    {
        $request->validate([
            'profesor_id' => 'required|exists:profesores,id',
            'horario_id' => 'required|exists:horarios,id',
            'hora_salida' => 'nullable|date_format:H:i',
        ]);

        try {
            $horaSalida = $request->hora_salida 
                ? Carbon::createFromFormat('H:i', $request->hora_salida)
                : now();

            $asistencia = AsistenciaDocente::registrarSalida(
                $request->profesor_id,
                $request->horario_id,
                $horaSalida
            );

            return response()->json([
                'success' => true,
                'message' => 'Salida registrada exitosamente',
                'data' => [
                    'asistencia_id' => $asistencia->id,
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
                'message' => 'Error al registrar salida: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * CU-16: Validar Registro dentro de Horario
     */
    public function validarRegistroDentroDeHorario(Request $request)
    {
        $request->validate([
            'horario_id' => 'required|exists:horarios,id',
            'hora_registro' => 'required|date_format:H:i',
            'tipo' => 'required|in:entrada,salida',
        ]);

        $horario = Horario::find($request->horario_id);
        $horaRegistro = Carbon::createFromFormat('H:i', $request->hora_registro);

        if ($request->tipo === 'entrada') {
            $esValido = AsistenciaDocente::validarDentroDeHorario($horario, $horaRegistro);
        } else {
            $esValido = AsistenciaDocente::validarSalidaDentroDeHorario($horario, $horaRegistro);
        }

        return response()->json([
            'valido' => $esValido,
            'horario_programado' => [
                'inicio' => $horario->hora_inicio,
                'fin' => $horario->hora_fin,
            ],
            'hora_registro' => $request->hora_registro,
            'mensaje' => $esValido 
                ? 'Registro dentro del horario permitido'
                : 'Registro fuera del horario permitido'
        ]);
    }

    /**
     * CU-17: Justificar Asistencia/Falta Posterior
     */
    public function justificarAsistencia(Request $request)
    {
        $request->validate([
            'asistencia_id' => 'required|exists:asistencia_docente,id',
            'justificacion' => 'required|string|max:1000',
            'tipo_justificacion' => 'required|in:medica,personal,academica,administrativa,otra',
        ]);

        try {
            $asistencia = AsistenciaDocente::findOrFail($request->asistencia_id);
            
            // Verificar permisos (solo el profesor o un administrador pueden justificar)
            $usuarioActual = session('user_id');
            $tipoUsuario = session('user_type');
            
            if ($tipoUsuario !== 'administrador' && $asistencia->profesor_id != $usuarioActual) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para justificar esta asistencia'
                ], 403);
            }

            $asistencia->justificar(
                $request->justificacion,
                $request->tipo_justificacion,
                $usuarioActual
            );

            return response()->json([
                'success' => true,
                'message' => 'Asistencia justificada exitosamente',
                'data' => [
                    'estado' => $asistencia->estado,
                    'estado_texto' => $asistencia->estado_texto,
                    'justificacion' => $asistencia->justificacion,
                    'tipo_justificacion' => $asistencia->tipo_justificacion,
                    'fecha_justificacion' => $asistencia->fecha_justificacion,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al justificar asistencia: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * CU-18: Visualizar Horario Propio (Docente)
     */
    public function verHorarioPropio(Request $request)
    {
        $profesorId = session('user_id');
        $fecha = $request->get('fecha', today()->toDateString());

        if (session('user_type') !== 'profesor') {
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado'
            ], 403);
        }

        $horarios = Horario::with(['cargaAcademica.grupo.materia', 'aula'])
            ->whereHas('cargaAcademica', function($query) use ($profesorId) {
                $query->where('profesor_id', $profesorId);
            })
            ->orderBy('hora_inicio')
            ->get();

        // Obtener asistencias del día
        $asistenciasHoy = AsistenciaDocente::with('horario')
            ->where('profesor_id', $profesorId)
            ->whereDate('fecha', $fecha)
            ->get()
            ->keyBy('horario_id');

        $horariosConAsistencia = $horarios->map(function($horario) use ($asistenciasHoy) {
            $asistencia = $asistenciasHoy->get($horario->id);
            
            return [
                'horario_id' => $horario->id,
                'dia_semana' => $horario->dia_semana,
                'dia_texto' => ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'][$horario->dia_semana] ?? 'N/A',
                'hora_inicio' => $horario->hora_inicio,
                'hora_fin' => $horario->hora_fin,
                'materia' => $horario->cargaAcademica->grupo->materia->nombre ?? 'N/A',
                'aula' => $horario->aula->codigo_aula ?? 'N/A',
                'tipo_clase' => $horario->tipo_clase,
                'asistencia' => $asistencia ? [
                    'estado' => $asistencia->estado,
                    'estado_texto' => $asistencia->estado_texto,
                    'estado_color' => $asistencia->estado_color,
                    'hora_entrada' => $asistencia->hora_entrada,
                    'hora_salida' => $asistencia->hora_salida,
                    'validado_en_horario' => $asistencia->validado_en_horario,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'fecha' => $fecha,
                'horarios' => $horariosConAsistencia,
                'resumen' => [
                    'total_clases' => $horarios->count(),
                    'clases_asistidas' => $asistenciasHoy->whereIn('estado', ['presente', 'en_clase'])->count(),
                    'clases_ausentes' => $asistenciasHoy->where('estado', 'ausente')->count(),
                    'clases_justificadas' => $asistenciasHoy->where('estado', 'justificado')->count(),
                ]
            ]
        ]);
    }

    /**
     * CU-19: Consultar Horario de Aula
     */
    public function consultarHorarioAula(Request $request, $aulaId)
    {
        $fecha = $request->get('fecha', today()->toDateString());
        $diaSemana = Carbon::parse($fecha)->dayOfWeek;
        $diaSemana = $diaSemana === 0 ? 7 : $diaSemana; // Convertir domingo de 0 a 7

        $horarios = Horario::with(['cargaAcademica.profesor', 'cargaAcademica.grupo.materia'])
            ->where('aula_id', $aulaId)
            ->whereJsonContains('dias_semana', strtolower($this->getDiaNombre($diaSemana)))
            ->orderBy('hora_inicio')
            ->get();

        // Obtener asistencias del día para esta aula
        $asistenciasHoy = AsistenciaDocente::with('profesor')
            ->whereHas('horario', function($query) use ($aulaId) {
                $query->where('aula_id', $aulaId);
            })
            ->whereDate('fecha', $fecha)
            ->get()
            ->keyBy('horario_id');

        $ocupacionAula = $horarios->map(function($horario) use ($asistenciasHoy) {
            $asistencia = $asistenciasHoy->get($horario->id);
            
            return [
                'horario_id' => $horario->id,
                'hora_inicio' => $horario->hora_inicio,
                'hora_fin' => $horario->hora_fin,
                'profesor' => $horario->cargaAcademica->profesor->nombre_completo ?? 'N/A',
                'materia' => $horario->cargaAcademica->grupo->materia->nombre ?? 'N/A',
                'grupo' => $horario->cargaAcademica->grupo->identificador ?? 'N/A',
                'tipo_clase' => $horario->tipo_clase,
                'estado_profesor' => $asistencia ? $asistencia->estado_texto : 'Sin registro',
                'estado_color' => $asistencia ? $asistencia->estado_color : 'secondary',
                'en_clase_ahora' => $asistencia && $asistencia->estado === 'en_clase',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'aula_id' => $aulaId,
                'fecha' => $fecha,
                'dia_semana' => $diaSemana,
                'ocupacion' => $ocupacionAula,
                'resumen' => [
                    'total_clases' => $horarios->count(),
                    'profesores_presentes' => $asistenciasHoy->whereIn('estado', ['presente', 'en_clase'])->count(),
                    'profesores_ausentes' => $asistenciasHoy->where('estado', 'ausente')->count(),
                    'clases_en_curso' => $asistenciasHoy->where('estado', 'en_clase')->count(),
                ]
            ]
        ]);
    }

    /**
     * CU-20: Ver Estatus de Asistencia del Día (Panel de Control)
     */
    public function panelControlAsistencia(Request $request)
    {
        if (session('user_type') !== 'administrador') {
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado'
            ], 403);
        }

        $fecha = $request->get('fecha', today()->toDateString());
        $diaSemana = Carbon::parse($fecha)->dayOfWeek;
        $diaSemana = $diaSemana === 0 ? 7 : $diaSemana;

        // Obtener todos los horarios del día
        $horariosDelDia = Horario::with(['cargaAcademica.profesor', 'cargaAcademica.grupo.materia', 'aula'])
            ->whereJsonContains('dias_semana', strtolower($this->getDiaNombre($diaSemana)))
            ->orderBy('hora_inicio')
            ->get();

        // Obtener todas las asistencias del día
        $asistenciasDelDia = AsistenciaDocente::with(['profesor', 'horario'])
            ->whereDate('fecha', $fecha)
            ->get()
            ->keyBy(function($item) {
                return $item->profesor_id . '_' . $item->horario_id;
            });

        // Construir panel de control
        $panelControl = $horariosDelDia->map(function($horario) use ($asistenciasDelDia) {
            $key = $horario->cargaAcademica->profesor_id . '_' . $horario->id;
            $asistencia = $asistenciasDelDia->get($key);
            
            return [
                'horario_id' => $horario->id,
                'profesor_id' => $horario->cargaAcademica->profesor_id,
                'profesor' => $horario->cargaAcademica->profesor->nombre_completo ?? 'N/A',
                'materia' => $horario->cargaAcademica->grupo->materia->nombre ?? 'N/A',
                'aula' => $horario->aula->codigo_aula ?? 'N/A',
                'hora_inicio' => $horario->hora_inicio,
                'hora_fin' => $horario->hora_fin,
                'estado' => $asistencia ? $asistencia->estado : 'sin_registro',
                'estado_texto' => $asistencia ? $asistencia->estado_texto : 'Sin Registro',
                'estado_color' => $asistencia ? $asistencia->estado_color : 'secondary',
                'hora_entrada' => $asistencia ? $asistencia->hora_entrada : null,
                'hora_salida' => $asistencia ? $asistencia->hora_salida : null,
                'validado_en_horario' => $asistencia ? $asistencia->validado_en_horario : false,
                'justificacion' => $asistencia ? $asistencia->justificacion : null,
            ];
        });

        // Estadísticas generales
        $estadisticas = [
            'total_clases' => $horariosDelDia->count(),
            'presentes' => $asistenciasDelDia->whereIn('estado', ['presente', 'en_clase'])->count(),
            'ausentes' => $asistenciasDelDia->where('estado', 'ausente')->count(),
            'en_clase' => $asistenciasDelDia->where('estado', 'en_clase')->count(),
            'tardanzas' => $asistenciasDelDia->where('estado', 'tardanza')->count(),
            'justificados' => $asistenciasDelDia->where('estado', 'justificado')->count(),
            'sin_registro' => $horariosDelDia->count() - $asistenciasDelDia->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'fecha' => $fecha,
                'dia_semana' => $diaSemana,
                'panel_control' => $panelControl,
                'estadisticas' => $estadisticas,
                'ultima_actualizacion' => now()->toISOString(),
            ]
        ]);
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
