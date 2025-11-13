<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Inscripcion;
use App\Models\Estudiante;

echo "\n=== VERIFICACIÓN DE INSCRIPCIONES ===\n\n";

$estudiantes = Estudiante::limit(3)->get();

foreach ($estudiantes as $estudiante) {
    echo "Estudiante: {$estudiante->nombre_completo} ({$estudiante->codigo_estudiante})\n";
    
    $inscripciones = Inscripcion::with('grupo.materia')
        ->where('estudiante_id', $estudiante->id)
        ->where('estado', 'activo')
        ->get();
    
    if ($inscripciones->isEmpty()) {
        echo "  Sin inscripciones\n\n";
        continue;
    }
    
    $materias = [];
    foreach ($inscripciones as $inscripcion) {
        $materia = $inscripcion->grupo->materia->nombre;
        $grupo = $inscripcion->grupo->identificador;
        
        echo "  - {$materia} (Grupo: {$grupo})\n";
        
        // Verificar duplicados
        if (isset($materias[$inscripcion->grupo->materia_id])) {
            echo "    ⚠️ DUPLICADO DETECTADO!\n";
        }
        $materias[$inscripcion->grupo->materia_id] = true;
    }
    
    echo "\n";
}

echo "✅ Verificación completada\n\n";
