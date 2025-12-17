<?php

namespace Database\Seeders;

use App\Models\TipoEvaluacion;
use Illuminate\Database\Seeder;

class TipoEvaluacionSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['nombre' => 'Parcial 1', 'ponderacion' => 30],
            ['nombre' => 'Parcial 2', 'ponderacion' => 30],
            ['nombre' => 'Examen Final', 'ponderacion' => 40], // Sums to 100
        ];

        foreach ($tipos as $tipo) {
            TipoEvaluacion::firstOrCreate(['nombre' => $tipo['nombre']], $tipo);
        }
    }
}
