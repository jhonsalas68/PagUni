<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inscripcion;
use App\Models\Estudiante;
use App\Models\Grupo;
use App\Models\PeriodoInscripcion;
use Carbon\Carbon;

class InscripcionesTestSeeder extends Seeder
{
    public function run(): void
    {
        echo "🎓 Creando periodo de inscripción...\n";

        // Crear periodo de inscripción activo
        $periodo = PeriodoInscripcion::create([
            'nombre' => 'Inscripciones Semestre 2024-2',
            'periodo_academico' => '2024-2',
            'fecha_inicio' => Carbon::now()->subDays(7),
            'fecha_fin' => Carbon::now()->addDays(7),
            'activo' => true,
            'descripcion' => 'Periodo de inscripción para el segundo semestre 2024'
        ]);

        echo "✓ Periodo creado: {$periodo->nombre}\n\n";

        // Obtener estudiantes y grupos
        $estudiantes = Estudiante::limit(5)->get();
        $grupos = Grupo::with('materia')->where('estado', 'activo')->limit(10)->get();

        if ($estudiantes->isEmpty() || $grupos->isEmpty()) {
            echo "⚠️ No hay estudiantes o grupos disponibles\n";
            return;
        }

        echo "📝 Creando inscripciones de prueba...\n";

        $inscripcionesCreadas = 0;

        foreach ($estudiantes as $estudiante) {
            // Inscribir a cada estudiante en 3-5 materias aleatorias
            $gruposAleatorios = $grupos->random(rand(3, min(5, $grupos->count())));
            $materiasInscritas = []; // Rastrear materias ya inscritas

            foreach ($gruposAleatorios as $grupo) {
                // Verificar que no esté ya inscrito en este grupo
                $yaInscritoGrupo = Inscripcion::where('estudiante_id', $estudiante->id)
                    ->where('grupo_id', $grupo->id)
                    ->where('periodo_academico', '2024-2')
                    ->exists();

                // Verificar que no esté inscrito en la misma materia en otro grupo
                $yaInscritoMateria = in_array($grupo->materia_id, $materiasInscritas);

                if (!$yaInscritoGrupo && !$yaInscritoMateria && $grupo->cupo_actual < $grupo->cupo_maximo) {
                    Inscripcion::create([
                        'estudiante_id' => $estudiante->id,
                        'grupo_id' => $grupo->id,
                        'periodo_academico' => '2024-2',
                        'fecha_inscripcion' => Carbon::now()->subDays(rand(1, 7)),
                        'estado' => 'activo',
                    ]);

                    // Incrementar cupo
                    $grupo->increment('cupo_actual');

                    // Agregar materia a la lista de inscritas
                    $materiasInscritas[] = $grupo->materia_id;

                    $inscripcionesCreadas++;
                    echo "✓ {$estudiante->nombre_completo} inscrito en {$grupo->materia->nombre} - Grupo {$grupo->identificador}\n";
                }
            }
        }

        echo "\n✅ Se crearon {$inscripcionesCreadas} inscripciones de prueba\n";
    }
}
