<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Horario;

class HorarioTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "Creando horarios de ejemplo...\n";
        
        // Crear algunos horarios de ejemplo
        $horarios = [
            [
                'carga_academica_id' => 1,
                'aula_id' => 1,
                'dia_semana' => 1, // Lunes
                'hora_inicio' => '08:00',
                'hora_fin' => '10:00',
                'duracion_horas' => 2.0,
                'tipo_clase' => 'teorica',
                'periodo_academico' => '2024-2',
                'es_semestral' => true,
                'semanas_duracion' => 16,
            ],
            [
                'carga_academica_id' => 2,
                'aula_id' => 2,
                'dia_semana' => 2, // Martes
                'hora_inicio' => '10:00',
                'hora_fin' => '12:00',
                'duracion_horas' => 2.0,
                'tipo_clase' => 'practica',
                'periodo_academico' => '2024-2',
                'es_semestral' => true,
                'semanas_duracion' => 16,
            ],
            [
                'carga_academica_id' => 3,
                'aula_id' => 3,
                'dia_semana' => 3, // Miércoles
                'hora_inicio' => '14:00',
                'hora_fin' => '16:00',
                'duracion_horas' => 2.0,
                'tipo_clase' => 'laboratorio',
                'periodo_academico' => '2024-2',
                'es_semestral' => true,
                'semanas_duracion' => 16,
            ],
            [
                'carga_academica_id' => 4,
                'aula_id' => 1,
                'dia_semana' => 4, // Jueves
                'hora_inicio' => '08:00',
                'hora_fin' => '09:30',
                'duracion_horas' => 1.5,
                'tipo_clase' => 'teorica',
                'periodo_academico' => '2024-2',
                'es_semestral' => true,
                'semanas_duracion' => 16,
            ],
            [
                'carga_academica_id' => 5,
                'aula_id' => 2,
                'dia_semana' => 5, // Viernes
                'hora_inicio' => '16:00',
                'hora_fin' => '18:00',
                'duracion_horas' => 2.0,
                'tipo_clase' => 'practica',
                'periodo_academico' => '2024-2',
                'es_semestral' => true,
                'semanas_duracion' => 16,
            ],
        ];

        foreach ($horarios as $horarioData) {
            try {
                $horario = Horario::create($horarioData);
                echo "✅ Horario creado: ID {$horario->id} - {$horario->cargaAcademica->grupo->materia->nombre} - {$horario->hora_inicio}-{$horario->hora_fin}\n";
            } catch (\Exception $e) {
                echo "❌ Error creando horario: " . $e->getMessage() . "\n";
            }
        }

        echo "\n🎉 Proceso completado!\n";
        echo "Total horarios creados: " . Horario::count() . "\n";
    }
}
