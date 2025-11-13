<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Grupo;

echo "\n=== TEST DE RELACIÓN HORARIOS ===\n\n";

$grupo = Grupo::with(['materia', 'horarios.aula'])->first();

if (!$grupo) {
    echo "No hay grupos en la base de datos\n";
    exit;
}

echo "Grupo: {$grupo->identificador}\n";
echo "Materia: {$grupo->materia->nombre}\n";
echo "Horarios:\n";

$horarios = $grupo->horarios;

if ($horarios->isEmpty()) {
    echo "  Sin horarios asignados\n";
} else {
    foreach ($horarios as $horario) {
        $dias = implode(', ', array_map('ucfirst', $horario->dias_semana ?? []));
        $aula = $horario->aula ? $horario->aula->codigo_aula : 'Sin aula';
        echo "  - {$dias} {$horario->hora_inicio}-{$horario->hora_fin} ({$aula})\n";
    }
}

echo "\n✅ Test completado\n\n";
