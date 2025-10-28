<?php

require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Horario;
use App\Models\Aula;
use App\Models\CargaAcademica;

echo "🔍 Probando validación con horarios realmente libres...\n\n";

// Obtener un horario existente para editar
$horarioParaEditar = Horario::with(['cargaAcademica', 'aula'])->first();
if (!$horarioParaEditar) {
    echo "❌ No hay horarios en el sistema\n";
    exit;
}

echo "📋 Horario que vamos a 'editar':\n";
echo "- ID: {$horarioParaEditar->id}\n";
echo "- Día actual: {$horarioParaEditar->dia_semana}\n";
echo "- Horario actual: {$horarioParaEditar->hora_inicio}-{$horarioParaEditar->hora_fin}\n";
echo "- Aula actual: " . ($horarioParaEditar->aula->codigo_aula ?? 'N/A') . "\n\n";

// Buscar un horario completamente libre
$horariosLibres = [
    ['dia' => 7, 'inicio' => '06:00', 'fin' => '08:00'], // Domingo temprano
    ['dia' => 6, 'inicio' => '20:00', 'fin' => '22:00'], // Sábado noche
    ['dia' => 1, 'inicio' => '12:00', 'fin' => '14:00'], // Lunes mediodía
    ['dia' => 2, 'inicio' => '16:00', 'fin' => '18:00'], // Martes tarde
];

foreach ($horariosLibres as $i => $libre) {
    echo "🧪 CASO " . ($i + 1) . ": Probando horario libre - ";
    $dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
    echo ($dias[$libre['dia']] ?? "Día {$libre['dia']}") . " {$libre['inicio']}-{$libre['fin']}\n";
    
    // Verificar si realmente está libre
    $ocupado = Horario::where('dia_semana', $libre['dia'])
        ->where('periodo_academico', $horarioParaEditar->periodo_academico)
        ->where('aula_id', $horarioParaEditar->aula_id)
        ->where('hora_inicio', '<', $libre['fin'])
        ->where('hora_fin', '>', $libre['inicio'])
        ->where('id', '!=', $horarioParaEditar->id)
        ->exists();
    
    if ($ocupado) {
        echo "⚠️  Este horario NO está libre, saltando...\n\n";
        continue;
    }
    
    echo "✅ Horario confirmado como libre\n";
    
    // Probar validación
    $validacion = Horario::validarConflictos(
        $horarioParaEditar->carga_academica_id,
        $horarioParaEditar->aula_id,
        $libre['dia'],
        $libre['inicio'],
        $libre['fin'],
        $horarioParaEditar->periodo_academico,
        $horarioParaEditar->id // IMPORTANTE: Excluir el horario que estamos editando
    );
    
    echo "📊 Resultado de validación:\n";
    echo "- Disponible: " . ($validacion['disponible'] ? 'SÍ' : 'NO') . "\n";
    echo "- Conflicto aula: " . ($validacion['conflicto_aula'] ? 'SÍ' : 'NO') . "\n";
    echo "- Conflicto profesor: " . ($validacion['conflicto_profesor'] ? 'SÍ' : 'NO') . "\n";
    
    if (!$validacion['disponible']) {
        echo "🚫 PROBLEMA: Horario libre detectado como ocupado!\n";
        if (!empty($validacion['detalles_aula'])) {
            echo "Conflictos de aula:\n";
            foreach ($validacion['detalles_aula'] as $detalle) {
                echo "  - Ocupada por: {$detalle['profesor']} con {$detalle['materia']}\n";
                echo "  - Horario: {$detalle['hora_inicio']}-{$detalle['hora_fin']}\n";
            }
        }
        if (!empty($validacion['detalles_profesor'])) {
            echo "Conflictos de profesor:\n";
            foreach ($validacion['detalles_profesor'] as $detalle) {
                echo "  - Profesor ocupado con: {$detalle['materia']} en {$detalle['aula']}\n";
                echo "  - Horario: {$detalle['hora_inicio']}-{$detalle['hora_fin']}\n";
            }
        }
    } else {
        echo "✅ Correcto: Horario libre detectado correctamente\n";
    }
    
    echo "\n";
    break; // Solo probar el primer horario libre encontrado
}

echo "✅ Prueba completada.\n";