<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use App\Models\Horario;
use App\Models\AsistenciaDocente;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AulaConsultaController extends Controller
{
    /**
     * Mostrar lista de aulas para consulta
     */
    public function index()
    {
        $aulas = Aula::orderBy('codigo_aula')->get();
        return view('consulta.aulas.index', compact('aulas'));
    }

    /**
     * Consultar horario de un aula específica
     */
    public function consultarHorario($aulaId, Request $request)
    {
        $aula = Aula::findOrFail($aulaId);
        
        $fecha = $request->get('fecha', today()->toDateString());
        $fechaCarbon = Carbon::parse($fecha);
        $diaSemana = $fechaCarbon->dayOfWeek;
        $diaSemana = $diaSemana === 0 ? 7 : $diaSemana;

        // Obtener horarios del aula para el día seleccionado
        $horarios = Horario::with(['cargaAcademica.grupo.materia', 'cargaAcademica.profesor'])
            ->where('aula_id', $aulaId)
            ->whereJsonContains('dias_semana', strtolower($this->getDiaNombre($diaSemana)))
            ->orderBy('hora_inicio')
            ->get();

        // Obtener asistencias para esa fecha
        $asistencias = AsistenciaDocente::whereIn('horario_id', $horarios->pluck('id'))
            ->whereDate('fecha', $fecha)
            ->get()
            ->keyBy('horario_id');

        return view('consulta.aulas.horario', compact('aula', 'horarios', 'asistencias', 'fecha', 'fechaCarbon'));
    }

    /**
     * API para consultar ocupación de aula
     */
    public function apiConsultarOcupacion($aulaId, Request $request)
    {
        $aula = Aula::findOrFail($aulaId);
        
        $fecha = $request->get('fecha', today()->toDateString());
        $fechaCarbon = Carbon::parse($fecha);
        $diaSemana = $fechaCarbon->dayOfWeek;
        $diaSemana = $diaSemana === 0 ? 7 : $diaSemana;

        $horarios = Horario::with(['cargaAcademica.grupo.materia', 'cargaAcademica.profesor'])
            ->where('aula_id', $aulaId)
            ->whereJsonContains('dias_semana', strtolower($this->getDiaNombre($diaSemana)))
            ->orderBy('hora_inicio')
            ->get();

        $asistencias = AsistenciaDocente::whereIn('horario_id', $horarios->pluck('id'))
            ->whereDate('fecha', $fecha)
            ->get()
            ->keyBy('horario_id');

        $ocupacion = $horarios->map(function($horario) use ($asistencias) {
            $asistencia = $asistencias->get($horario->id);
            
            return [
                'horario_id' => $horario->id,
                'hora_inicio' => $horario->hora_inicio,
                'hora_fin' => $horario->hora_fin,
                'materia' => $horario->cargaAcademica->grupo->materia->nombre ?? 'N/A',
                'profesor' => ($horario->cargaAcademica->profesor->nombre ?? '') . ' ' . ($horario->cargaAcademica->profesor->apellido ?? ''),
                'grupo' => $horario->cargaAcademica->grupo->identificador ?? 'N/A',
                'estado_asistencia' => $asistencia ? $asistencia->estado : 'sin_registro',
                'modalidad' => $asistencia ? $asistencia->modalidad : null,
            ];
        });

        return response()->json([
            'aula' => [
                'id' => $aula->id,
                'codigo' => $aula->codigo_aula,
                'nombre' => $aula->nombre,
                'capacidad' => $aula->capacidad,
            ],
            'fecha' => $fecha,
            'dia_semana' => $diaSemana,
            'ocupacion' => $ocupacion,
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
