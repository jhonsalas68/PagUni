<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Ver todos los registros de asistencia docente de hoy
$registros = App\Models\AsistenciaDocente::whereDate('fecha', today())
    ->with(['profesor', 'horario.cargaAcademica.grupo.materia'])
    ->get();

echo "Registros de asistencia de hoy: " . $registros->count() . "\n\n";

foreach ($registros as $reg) {
    echo "ID: {$reg->id}\n";
    echo "Profesor: " . ($reg->profesor->nombre ?? 'N/A') . "\n";
    echo "Materia: " . ($reg->horario->cargaAcademica->grupo->materia->nombre ?? 'N/A') . "\n";
    echo "Estado: {$reg->estado}\n";
    echo "QR Token: " . ($reg->qr_token ? substr($reg->qr_token, 0, 50) . '...' : 'NULL') . "\n";
    echo "QR Generado: " . ($reg->qr_generado_at ?? 'NULL') . "\n";
    echo "QR Escaneado: " . ($reg->qr_escaneado_at ?? 'NULL') . "\n";
    echo "---\n\n";
}
