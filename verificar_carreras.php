<?php

require_once 'vendor/autoload.php';

// Cargar configuración de Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Carrera;
use App\Models\Materia;

echo "=== CARRERAS Y MATERIAS EXISTENTES ===\n\n";

$carreras = Carrera::with(['facultad', 'materias'])->get();

foreach ($carreras as $carrera) {
    echo "🎓 {$carrera->nombre}\n";
    echo "   Facultad: " . ($carrera->facultad->nombre ?? 'N/A') . "\n";
    echo "   Materias: " . $carrera->materias->count() . "\n";
    
    if ($carrera->materias->count() > 0) {
        foreach ($carrera->materias as $materia) {
            echo "      - {$materia->codigo}: {$materia->nombre} (Sem: {$materia->semestre})\n";
        }
    } else {
        echo "      ⚠️  Sin materias\n";
    }
    echo "\n";
}

echo "Total carreras: " . $carreras->count() . "\n";
echo "Total materias: " . Materia::count() . "\n";