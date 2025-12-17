<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Profesor;
use App\Models\Materia;
use App\Models\Grupo;
use App\Models\CargaAcademica;
use App\Models\Estudiante;
use App\Models\Inscripcion;
use App\Models\PeriodoAcademico;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AssignSubjectsAndEnrollStudentsSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            // 1. Find Professor
            $profesor = Profesor::where('codigo_docente', 'PROF001')->first();

            if (!$profesor) {
                $this->command->error("Profesor PROF001 not found!");
                return;
            }
            $this->command->info("Profesor found: {$profesor->nombre_completo}");

            // 2. Create Materias
            $materiasData = [
                ['sigla' => 'MAT101', 'nombre' => 'Cálculo I', 'nivel' => 1],
                ['sigla' => 'FIS101', 'nombre' => 'Física I', 'nivel' => 1],
                ['sigla' => 'INF110', 'nombre' => 'Introducción a la Informática', 'nivel' => 1],
            ];

            $periodoActual = '1-2025'; // Periodo hardcoded or fetched

            try {
                foreach ($materiasData as $mData) {
                    // Ensure Materia exists
                    $this->command->info("Processing materia: {$mData['nombre']}");
                    $materia = Materia::firstOrCreate(
                        ['sigla' => $mData['sigla']],
                        [
                            'nombre' => $mData['nombre'],
                            'nivel' => $mData['nivel'],
                            'descripcion' => $mData['nombre'],
                            'estado' => 'activo'
                        ]
                    );

                    // 3. Create Grupo
                    $this->command->info("Creating/Finding Grupo A for {$materia->sigla}");
                    $grupo = Grupo::firstOrCreate(
                        [
                            'identificador' => 'A',
                            'materia_id' => $materia->id
                        ],
                        [
                            'capacidad_maxima' => 50,
                            'cupo_maximo' => 50,
                            'cupo_actual' => 0,
                            'estado' => 'activo',
                            'permite_inscripcion' => true
                        ]
                    );

                    // 4. Create CargaAcademica (Assign to Professor)
                    $this->command->info("Assigning load to professor");
                    
                    // Check if already exists individually
                    $exists = CargaAcademica::where('profesor_id', $profesor->id)
                        ->where('grupo_id', $grupo->id)
                        ->where('periodo', $periodoActual)
                        ->exists();

                    if (!$exists) {
                        CargaAcademica::create([
                            'profesor_id' => $profesor->id,
                            'grupo_id' => $grupo->id,
                            'periodo' => $periodoActual,
                            'estado' => 'asignado'
                        ]);
                    }

                    $this->command->info("Assigned {$materia->nombre} (Grupo A) to {$profesor->nombre_completo}");

                    // 5. Enroll All Students
                    $estudiantes = Estudiante::all();
                    
                    foreach ($estudiantes as $estudiante) {
                        // Check if already enrolled
                        $existingInscripcion = Inscripcion::where('estudiante_id', $estudiante->id)
                            ->where('grupo_id', $grupo->id)
                            ->exists();

                        if (!$existingInscripcion) {
                            Inscripcion::create([
                                'estudiante_id' => $estudiante->id,
                                'grupo_id' => $grupo->id,
                                'fecha_inscripcion' => Carbon::now(),
                                'periodo_academico' => $periodoActual,
                                'estado' => 'activo'
                            ]);
                            
                            // Increment cupo
                            $grupo->increment('cupo_actual');
                        }
                    }
                    
                    $count = $estudiantes->count();
                    $this->command->info("Enrolled {$count} students in {$materia->nombre}");
                }
            } catch (\Exception $e) {
                $this->command->error("Error: " . $e->getMessage());
                throw $e;
            }
        });
    }
}
