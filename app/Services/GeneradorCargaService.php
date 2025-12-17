<?php

namespace App\Services;

use App\Models\Materia;
use App\Models\Grupo;
use App\Models\Profesor;
use App\Models\CargaAcademica;
use Illuminate\Support\Facades\DB;

class GeneradorCargaService
{
    private $periodo;
    private $conflictos = [];
    private $asignaciones = [];

    /**
     * Genera cargas académicas automáticamente para el periodo dado.
     */
    public function generar($periodo)
    {
        $this->periodo = $periodo;
        $this->conflictos = [];
        $this->asignaciones = [];

        // 1. Obtener todas las materias activas
        $materias = Materia::where('estado', 'activo')->get();
        
        // 2. Obtener todos los profesores activos
        $profesores = Profesor::where('estado', 'activo')->get();
        
        if ($profesores->isEmpty()) {
            return [
                'procesados' => 0,
                'asignados' => 0,
                'conflictos' => ['No hay profesores activos disponibles.']
            ];
        }

        $profesorIndex = 0;
        
        DB::beginTransaction();
        try {
            foreach ($materias as $materia) {
                // Verificar si ya existe un grupo para esta materia en este periodo
                $grupoExistente = Grupo::where('materia_id', $materia->id)
                    ->where('periodo_academico', $periodo)
                    ->first();
                
                if (!$grupoExistente) {
                    // Crear grupo automáticamente
                    $grupo = Grupo::create([
                        'materia_id' => $materia->id,
                        'identificador' => 'A', // Grupo A por defecto
                        'capacidad_maxima' => 30,
                        'periodo_academico' => $periodo,
                        'estado' => 'activo'
                    ]);
                } else {
                    $grupo = $grupoExistente;
                }

                // Verificar si ya existe carga para este grupo
                $cargaExistente = CargaAcademica::where('grupo_id', $grupo->id)
                    ->where('periodo', $periodo)
                    ->first();
                
                if ($cargaExistente) {
                    $this->conflictos[] = "Ya existe carga para {$materia->nombre} - Grupo {$grupo->identificador}";
                    continue;
                }

                // Asignar profesor (rotación simple)
                $profesor = $profesores[$profesorIndex % $profesores->count()];
                $profesorIndex++;

                // Crear carga académica
                $carga = CargaAcademica::create([
                    'profesor_id' => $profesor->id,
                    'grupo_id' => $grupo->id,
                    'periodo' => $periodo,
                    'estado' => 'asignado'
                ]);

                $this->asignaciones[] = [
                    'materia' => $materia->nombre,
                    'grupo' => $grupo->identificador,
                    'profesor' => $profesor->nombre_completo ?? ($profesor->nombres . ' ' . $profesor->apellidos)
                ];
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return [
            'procesados' => $materias->count(),
            'asignados' => count($this->asignaciones),
            'conflictos' => $this->conflictos,
            'asignaciones' => $this->asignaciones
        ];
    }
}
