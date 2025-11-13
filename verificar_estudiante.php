<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Verificar estudiante
$estudiante = App\Models\Estudiante::where('codigo_estudiante', 'INGS2024001')->first();

if ($estudiante) {
    echo "Estudiante: {$estudiante->nombre} {$estudiante->apellido}\n";
    echo "ID: {$estudiante->id}\n\n";
    
    // Ver inscripciones activas
    $inscripciones = App\Models\Inscripcion::with(['grupo.materia', 'grupo.horarios'])
        ->where('estudiante_id', $estudiante->id)
        ->where('estado', 'activo')
        ->get();
    
    echo "Inscripciones activas: " . $inscripciones->count() . "\n\n";
    
    foreach ($inscripciones as $inscripcion) {
        echo "- {$inscripcion->grupo->materia->nombre} (Grupo: {$inscripcion->grupo->identificador})\n";
        echo "  Horarios:\n";
        foreach ($inscripcion->grupo->horarios as $horario) {
            $dias = implode(', ', $horario->dias_semana ?? []);
            echo "    {$dias}: {$horario->hora_inicio} - {$horario->hora_fin}\n";
        }
        echo "\n";
    }
    
    // Ver qué día es hoy
    $diaHoy = strtolower(Carbon\Carbon::now()->locale('es')->dayName);
    echo "Día de hoy: {$diaHoy}\n";
    
} else {
    echo "Estudiante no encontrado\n";
}
