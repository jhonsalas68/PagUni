<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Buscar un profesor
$profesor = App\Models\Profesor::first();

if (!$profesor) {
    echo "No hay profesores en la base de datos\n";
    exit;
}

echo "Profesor: {$profesor->nombre} {$profesor->apellido}\n";
echo "ID: {$profesor->id}\n\n";

// Buscar horarios del profesor
$horarios = App\Models\Horario::whereHas('cargaAcademica', function($query) use ($profesor) {
    $query->where('profesor_id', $profesor->id);
})->with(['cargaAcademica.grupo.materia', 'aula'])->get();

echo "Horarios del profesor: " . $horarios->count() . "\n\n";

if ($horarios->isEmpty()) {
    echo "El profesor no tiene horarios asignados\n";
    exit;
}

// Mostrar primer horario
$horario = $horarios->first();
echo "Primer horario:\n";
echo "- Materia: " . ($horario->cargaAcademica->grupo->materia->nombre ?? 'N/A') . "\n";
echo "- Días: " . implode(', ', $horario->dias_semana ?? []) . "\n";
echo "- Hora: {$horario->hora_inicio} - {$horario->hora_fin}\n";
echo "- Aula: " . ($horario->aula->codigo_aula ?? 'N/A') . "\n\n";

// Intentar generar QR
try {
    $asistencia = App\Models\AsistenciaDocente::generarQR($profesor->id, $horario->id, 'presencial');
    echo "✓ QR generado exitosamente\n";
    echo "Token: {$asistencia->qr_token}\n";
    echo "Generado: {$asistencia->qr_generado_at}\n";
    echo "Expira: {$asistencia->qr_expiracion}\n";
    echo "Estado: {$asistencia->estado}\n\n";
    
    // Verificar si se puede escanear
    $verificar = App\Models\AsistenciaDocente::where('qr_token', $asistencia->qr_token)
        ->whereNotNull('qr_generado_at')
        ->whereNull('qr_escaneado_at')
        ->first();
    
    if ($verificar) {
        echo "✓ El QR se puede escanear\n";
    } else {
        echo "✗ El QR NO se puede escanear\n";
    }
    
} catch (\Exception $e) {
    echo "✗ Error al generar QR: " . $e->getMessage() . "\n";
}
