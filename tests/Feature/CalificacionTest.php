<?php

namespace Tests\Feature;

use App\Models\Calificacion;
use App\Models\Estudiante;
use App\Models\Grupo;
use App\Models\Inscripcion;
use App\Models\Materia;
use App\Models\Profesor;
use App\Models\TipoEvaluacion;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions; // Add this
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CalificacionTest extends TestCase
{
    use DatabaseTransactions; // Use real DB but rollback after test

    public function test_profesor_puede_ver_gestion_notas()
    {
        // 1. Setup User and Professor
        $user = User::factory()->create(['tipo' => 'profesor']);
        $profesor = Profesor::factory()->create(['user_id' => $user->id]);
        
        // 2. Setup Academic Data
        $materia = Materia::factory()->create();
        $grupo = Grupo::factory()->create(['materia_id' => $materia->id]);
        
        // Link professor to group via CargaAcademica
        \App\Models\CargaAcademica::factory()->create([
            'profesor_id' => $profesor->id,
            'grupo_id' => $grupo->id,
            'materia_id' => $materia->id,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('profesor.calificaciones.gestion', $grupo->id));

        $response->assertStatus(200);
        $response->assertSee($materia->nombre);
    }

    public function test_profesor_puede_guardar_notas()
    {
        // 1. Setup Data
        $user = User::factory()->create(['tipo' => 'profesor']);
        $profesor = Profesor::factory()->create(['user_id' => $user->id]);
        $materia = Materia::factory()->create();
        $grupo = Grupo::factory()->create(['materia_id' => $materia->id]);
        \App\Models\CargaAcademica::factory()->create([
            'profesor_id' => $profesor->id,
            'grupo_id' => $grupo->id,
            'materia_id' => $materia->id,
        ]);

        $estudiante = Estudiante::factory()->create();
        $inscripcion = Inscripcion::factory()->create([
            'estudiante_id' => $estudiante->id,
            'grupo_id' => $grupo->id,
            'estado' => 'activo'
        ]);

        // Ensure Tipos exists (seeder was run)
        $tipo = TipoEvaluacion::first(); 
        if(!$tipo) {
            $tipo = TipoEvaluacion::create(['nombre' => 'Test', 'ponderacion' => 50]);
        }

        $this->actingAs($user);

        // 2. Action: Post Grades
        $response = $this->post(route('profesor.calificaciones.store'), [
            'grupo_id' => $grupo->id,
            'tipo_evaluacion_id' => $tipo->id,
            'notas' => [
                $inscripcion->id => 18.5
            ]
        ]);

        $response->assertSessionHas('success');
        
        // 3. Verify DB
        $this->assertDatabaseHas('calificaciones', [
            'inscripcion_id' => $inscripcion->id,
            'tipo_evaluacion_id' => $tipo->id,
            'nota' => 18.5
        ]);
    }
}
