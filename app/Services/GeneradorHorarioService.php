<?php

namespace App\Services;

use App\Models\CargaAcademica;
use App\Models\Aula;
use App\Models\Horario;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GeneradorHorarioService
{
    private $periodo;
    private $conflictos = [];
    private $asignaciones = [];
    
    // Matriz de ocupación: [recurso_id][dia][hora] = true
    private $ocupacionAulas = [];
    private $ocupacionProfesores = [];

    /**
     * Genera horarios automáticamente para el periodo dado.
     * Retorna array con 'exito' (count) y 'conflictos' (array).
     */
    public function generar($periodo)
    {
        $this->periodo = $periodo;
        $this->conflictos = [];
        $this->asignaciones = [];
        $this->inicializarMatrices();

        // 1. Obtener Cargas sin horario completo
        \Illuminate\Support\Facades\Log::info("Generando horario para periodo: '{$periodo}'");
        
        $cargas = CargaAcademica::with(['materia', 'grupo', 'profesor'])
            ->where('periodo', $periodo)
            ->where('estado', '!=', 'cancelado') // Asumiendo estado de carga
            ->get();

        \Illuminate\Support\Facades\Log::info("Cargas encontradas para el periodo: " . $cargas->count());

        // Filtrar las que ya tienen horario cubierto (simple check si existe algun horario)
        // O mejor, intentamos programar lo que falta. Por simplicidad, asumiremos regeneración de pendientes.
        $cargasPendientes = $cargas->filter(function($carga) {
            $hasHorario = $carga->horarios()->count() > 0;
            if ($hasHorario) {
                 \Illuminate\Support\Facades\Log::info("Carga ID {$carga->id} omitida (ya tiene horario).");
            }
            return !$hasHorario;
        });
        
        \Illuminate\Support\Facades\Log::info("Cargas pendientes de procesar: " . $cargasPendientes->count());
        
        // 2. Ordenar por dificultad (Heurística)
        // Más horas requeridas primero, grupos con más alumnos primero
        $cargasPendientes = $cargasPendientes->sortByDesc(function($carga) {
            return ($carga->materia->horas_teoricas + $carga->materia->horas_practicas) * 1000 + $carga->grupo->capacidad_maxima;
        });

        // 3. Obtener Aulas disponibles
        $aulas = Aula::where('estado', 'disponible')->orderBy('capacidad', 'asc')->get();

        // 4. Algoritmo Greedy
        foreach ($cargasPendientes as $carga) {
            $this->programarCarga($carga, $aulas);
        }

        // 5. Guardar asignaciones
        DB::beginTransaction();
        try {
            foreach ($this->asignaciones as $asignacion) {
                Horario::create($asignacion);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return [
            'procesados' => $cargasPendientes->count(),
            'asignados' => count($this->asignaciones),
            'conflictos' => $this->conflictos
        ];
    }

    private function inicializarMatrices()
    {
        // Cargar horarios YA existentes para marcar ocupación
        $horariosExistentes = Horario::where('periodo_academico', $this->periodo)
                                     ->with('cargaAcademica') // Para saber el profesor
                                     ->get();

        foreach ($horariosExistentes as $h) {
            $dias = is_array($h->dias_semana) ? $h->dias_semana : json_decode($h->dias_semana, true);
            $inicio = Carbon::parse($h->hora_inicio);
            $fin = Carbon::parse($h->hora_fin);
            
            // Marcar Aula
            if ($h->aula_id) {
                $this->marcarOcupacion($this->ocupacionAulas, $h->aula_id, $dias, $inicio, $fin);
            }

            // Marcar Profesor
            if ($h->cargaAcademica && $h->cargaAcademica->profesor_id) {
                $this->marcarOcupacion($this->ocupacionProfesores, $h->cargaAcademica->profesor_id, $dias, $inicio, $fin);
            }
        }
    }

    private function programarCarga($carga, $aulas)
    {
        $horasNecesarias = $carga->materia->horas_teoricas + $carga->materia->horas_practicas;
        if ($horasNecesarias == 0) $horasNecesarias = 4; // Default si no tiene datos

        // Estrategia simplificada: Bloques de 2 horas
        // Si necesita 4 horas -> 2 bloques de 2h
        // Si necesita 3 horas -> 1 de 2h + 1 de 1h (o 1 de 3h)
        // Asumiremos bloques de 2 horas máx para facilitar "slots" comunes (7-9, 9-11...)
        
        $horasRestantes = $horasNecesarias;
        $intentos = 0;
        $maxIntentos = 500; // Evitar bucles infinitos

        while ($horasRestantes > 0 && $intentos < $maxIntentos) {
            $duracionBloque = min(2, $horasRestantes);
            
            // Buscar Slot
            $slot = $this->buscarSlot($carga, $aulas, $duracionBloque);

            if ($slot) {
                // Registrar Asignación Temporal
                $this->asignaciones[] = [
                    'carga_academica_id' => $carga->id,
                    'aula_id' => $slot['aula_id'],
                    'dias_semana' => json_encode([$slot['dia']]),
                    'hora_inicio' => $slot['inicio'],
                    'hora_fin' => $slot['fin'],
                    'duracion_horas' => $duracionBloque,
                    'periodo_academico' => $this->periodo,
                    'tipo_asignacion' => 'automatica',
                    'estado' => 'activo'
                ];

                // Actualizar Matrices en memoria
                $inicioC = Carbon::parse($slot['inicio']);
                $finC = Carbon::parse($slot['fin']);
                
                $this->marcarOcupacion($this->ocupacionAulas, $slot['aula_id'], [$slot['dia']], $inicioC, $finC);
                $this->marcarOcupacion($this->ocupacionProfesores, $carga->profesor_id, [$slot['dia']], $inicioC, $finC);

                $horasRestantes -= $duracionBloque;
            } else {
                // No se pudo asignar este bloque
                $this->conflictos[] = "No se encontró aula/horario para: {$carga->materia->nombre} (Grupo {$carga->grupo->identificador}) - Faltan {$horasRestantes} horas.";
                break; // Fallo crítico para esta carga, pasar a la siguiente
            }
            $intentos++;
        }
    }

    private function buscarSlot($carga, $aulas, $duracion)
    {
        $diasPosibles = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes'];
        $horasInicio = ['07:00', '09:00', '11:00', '14:00', '16:00', '18:00', '20:00']; // Slots estándar predefinidos

        // Randomizar para evitar llenar siempre Lunes 7am primero
        shuffle($diasPosibles);
        
        // Filtrar aulas por capacidad mínima
        $aulasCandidatas = $aulas->filter(function($a) use ($carga) {
            return $a->capacidad >= $carga->grupo->capacidad_maxima;
        });

        if ($aulasCandidatas->isEmpty()) {
            // Si no hay aulas con esa capacidad, intentar con las más grandes disponibles
            // (Fallback: asignar a la más grande aunque falte espacio, o error)
            // Por ahora, fallback a todas las aulas para intentar meterlo
             $aulasCandidatas = $aulas; 
        }

        foreach ($diasPosibles as $dia) {
            foreach ($horasInicio as $hora) {
                $inicio = Carbon::parse($hora);
                $fin = (clone $inicio)->addMinutes($duracion * 60);
                $horaFinStr = $fin->format('H:i:s');

                // 1. Validar Profesor libre
                if ($this->estaOcupado($this->ocupacionProfesores, $carga->profesor_id, $dia, $inicio, $fin)) {
                    continue;
                }

                // 2. Buscar Aula libre
                foreach ($aulasCandidatas as $aula) {
                    if (!$this->estaOcupado($this->ocupacionAulas, $aula->id, $dia, $inicio, $fin)) {
                        return [
                            'dia' => $dia,
                            'inicio' => $hora,
                            'fin' => $horaFinStr,
                            'aula_id' => $aula->id
                        ];
                    }
                }
            }
        }
        return null;
    }

    private function marcarOcupacion(&$matriz, $id, $dias, $inicio, $fin)
    {
        foreach ($dias as $dia) {
            if (!isset($matriz[$id][$dia])) {
                $matriz[$id][$dia] = [];
            }
            // Guardar rango ocupado como timestamps para comparación fácil
            $matriz[$id][$dia][] = ['start' => $inicio->timestamp, 'end' => $fin->timestamp];
        }
    }

    private function estaOcupado($matriz, $id, $dia, $inicio, $fin)
    {
        if (!isset($matriz[$id][$dia])) return false;

        $startReq = $inicio->timestamp;
        $endReq = $fin->timestamp;

        foreach ($matriz[$id][$dia] as $rango) {
            // Check overlap
            // (StartA <= EndB) and (EndA >= StartB)
            if ($startReq < $rango['end'] && $endReq > $rango['start']) {
                return true;
            }
        }
        return false;
    }
}
