<?php

namespace App\Http\Controllers;

use App\Models\AsistenciaEstudiante;
use App\Models\Inscripcion;
use App\Models\Horario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class AsistenciaEstudianteController extends Controller
{
    // Mostrar clases del día para marcar asistencia
    public function mostrarClasesHoy()
    {
        $estudianteId = Session::get('user_id');
        
        // Obtener inscripciones activas del estudiante
        $inscripciones = Inscripcion::with(['grupo.materia', 'grupo.horarios.aula'])
            ->where('estudiante_id', $estudianteId)
            ->where('estado', 'activo')
            ->get();

        // Obtener día de la semana actual
        $diaHoy = strtolower(Carbon::now()->locale('es')->dayName);
        
        // Filtrar horarios de hoy (todas las clases, no solo virtuales)
        $clasesHoy = [];
        foreach ($inscripciones as $inscripcion) {
            foreach ($inscripcion->grupo->horarios as $horario) {
                // Mostrar si es hoy (sin filtrar por tipo de clase)
                if (in_array($diaHoy, $horario->dias_semana ?? [])) {
                    // Verificar si ya marcó asistencia
                    $yaMarco = AsistenciaEstudiante::where('inscripcion_id', $inscripcion->id)
                        ->where('horario_id', $horario->id)
                        ->whereDate('fecha', today())
                        ->exists();

                    // Verificar si está en el horario de clase (con 15 min de tolerancia antes y después)
                    $horaInicio = Carbon::parse($horario->hora_inicio)->subMinutes(15);
                    $horaFin = Carbon::parse($horario->hora_fin)->addMinutes(15);
                    $ahora = Carbon::now();
                    $enHorario = $ahora->between($horaInicio, $horaFin);

                    $clasesHoy[] = [
                        'inscripcion' => $inscripcion,
                        'horario' => $horario,
                        'ya_marco' => $yaMarco,
                        'en_horario' => $enHorario,
                        'hora_inicio' => $horario->hora_inicio,
                        'hora_fin' => $horario->hora_fin,
                    ];
                }
            }
        }

        return view('estudiante.asistencia.marcar', compact('clasesHoy'));
    }

    // Marcar asistencia (checkbox simple)
    public function marcarAsistencia(Request $request)
    {
        $request->validate([
            'inscripcion_id' => 'required|exists:inscripciones,id',
            'horario_id' => 'required|exists:horarios,id',
        ]);

        $estudianteId = Session::get('user_id');

        try {
            DB::beginTransaction();

            // Verificar que la inscripción pertenece al estudiante
            $inscripcion = Inscripcion::where('id', $request->inscripcion_id)
                ->where('estudiante_id', $estudianteId)
                ->where('estado', 'activo')
                ->firstOrFail();

            $horario = Horario::findOrFail($request->horario_id);

            // Verificar que está en el horario de clase (con 15 min de tolerancia)
            $horaInicio = Carbon::parse($horario->hora_inicio)->subMinutes(15);
            $horaFin = Carbon::parse($horario->hora_fin)->addMinutes(15);
            $ahora = Carbon::now();

            if (!$ahora->between($horaInicio, $horaFin)) {
                throw new \Exception('Solo puedes marcar asistencia durante el horario de clase (15 min antes/después).');
            }

            // Verificar si ya marcó asistencia hoy
            $yaMarco = AsistenciaEstudiante::where('inscripcion_id', $inscripcion->id)
                ->where('horario_id', $horario->id)
                ->whereDate('fecha', today())
                ->exists();

            if ($yaMarco) {
                throw new \Exception('Ya marcaste tu asistencia para esta clase.');
            }

            // Registrar asistencia como PRESENTE
            AsistenciaEstudiante::create([
                'inscripcion_id' => $inscripcion->id,
                'horario_id' => $horario->id,
                'fecha' => today(),
                'hora_registro' => now(),
                'estado' => 'presente',
                'metodo_registro' => 'manual',
            ]);

            DB::commit();

            return back()->with('success', '✓ Asistencia marcada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    // Ver historial de asistencias
    public function historial(Request $request)
    {
        $estudianteId = Session::get('user_id');

        $inscripciones = Inscripcion::with([
            'grupo.materia',
            'asistencias' => function($query) use ($request) {
                if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
                    $query->whereBetween('fecha', [$request->fecha_inicio, $request->fecha_fin]);
                }
            }
        ])
        ->where('estudiante_id', $estudianteId)
        ->where('estado', 'activo')
        ->get();

        return view('estudiante.asistencia.historial', compact('inscripciones'));
    }
}
