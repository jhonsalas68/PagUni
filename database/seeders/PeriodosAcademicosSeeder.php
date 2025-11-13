<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PeriodoAcademico;
use Carbon\Carbon;

class PeriodosAcademicosSeeder extends Seeder
{
    public function run(): void
    {
        $periodos = [
            [
                'codigo' => '2024-1',
                'nombre' => 'Primer Semestre 2024',
                'anio' => 2024,
                'semestre' => 1,
                'fecha_inicio' => '2024-02-01',
                'fecha_fin' => '2024-06-30',
                'estado' => 'finalizado',
                'es_actual' => false,
                'observaciones' => 'Primer semestre del año académico 2024'
            ],
            [
                'codigo' => '2024-2',
                'nombre' => 'Segundo Semestre 2024',
                'anio' => 2024,
                'semestre' => 2,
                'fecha_inicio' => '2024-08-01',
                'fecha_fin' => '2024-12-20',
                'estado' => 'activo',
                'es_actual' => true,
                'observaciones' => 'Segundo semestre del año académico 2024 - Periodo actual'
            ],
            [
                'codigo' => '2025-1',
                'nombre' => 'Primer Semestre 2025',
                'anio' => 2025,
                'semestre' => 1,
                'fecha_inicio' => '2025-02-01',
                'fecha_fin' => '2025-06-30',
                'estado' => 'inactivo',
                'es_actual' => false,
                'observaciones' => 'Primer semestre del año académico 2025'
            ],
        ];

        foreach ($periodos as $periodo) {
            PeriodoAcademico::create($periodo);
        }

        $this->command->info('✓ Periodos académicos creados exitosamente');
    }
}
