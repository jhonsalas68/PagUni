<?php
// Test para verificar generación de QR del profesor

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\AsistenciaDocente;
use App\Models\Horario;
use App\Models\Profesor;

echo "=== TEST GENERACIÓN QR PROFESOR ===\n\n";

// 1. Buscar un profesor activo
$profesor = Profesor::where('estado', 'activo')->first();
if (!$profesor) {
    echo "❌ No hay profesores activos\n";
    exit;
}
echo "✅ Profesor encontrado: {$profesor->nombre_completo} (ID: {$profesor->id})\n";

// 2. Buscar un horario del profesor
$horario = Horario::whereHas('cargaAcademica', function($query) use ($profesor) {
    $query->where('profesor_id', $profesor->id);
})->with(['cargaAcademica.grupo.materia', 'aula'])->first();

if (!$horario) {
    echo "❌ No hay horarios asignados a este profesor\n";
    exit;
}

echo "✅ Horario encontrado:\n";
echo "   - Materia: " . ($horario->cargaAcademica->grupo->materia->nombre ?? 'N/A') . "\n";
echo "   - Aula: " . ($horario->aula->codigo_aula ?? 'N/A') . "\n";
echo "   - Horario: {$horario->hora_inicio} - {$horario->hora_fin}\n\n";

// 3. Intentar generar QR
try {
    echo "🔄 Generando QR...\n";
    $asistencia = AsistenciaDocente::generarQR($profesor->id, $horario->id, 'presencial');
    
    echo "✅ QR generado exitosamente!\n";
    echo "   - Token: {$asistencia->qr_token}\n";
    echo "   - Sesión: #{$asistencia->numero_sesion}\n";
    echo "   - Estado: {$asistencia->estado}\n";
    echo "   - Modalidad: {$asistencia->modalidad}\n";
    echo "   - Generado: {$asistencia->qr_generado_at}\n";
    echo "   - Expira: {$asistencia->qr_expiracion}\n\n";
    
    // 4. Verificar que el QR se puede recuperar
    $qrRecuperado = AsistenciaDocente::where('qr_token', $asistencia->qr_token)
        ->whereNotNull('qr_generado_at')
        ->whereNull('qr_escaneado_at')
        ->first();
    
    if ($qrRecuperado) {
        echo "✅ QR se puede recuperar correctamente\n";
        
        // Verificar expiración
        $minutosTranscurridos = $qrRecuperado->qr_generado_at->diffInMinutes(now());
        echo "   - Minutos transcurridos: {$minutosTranscurridos}\n";
        echo "   - ¿Expirado?: " . ($minutosTranscurridos > 30 ? 'SÍ' : 'NO') . "\n\n";
        
        // 5. Generar URL del QR
        $qrUrl = url("/profesor/qr-vista/{$asistencia->qr_token}");
        echo "✅ URL del QR:\n";
        echo "   {$qrUrl}\n\n";
        
        echo "🎉 TODO FUNCIONA CORRECTAMENTE!\n";
        echo "   Puedes abrir esta URL en tu navegador para ver el QR\n";
        
    } else {
        echo "❌ No se pudo recuperar el QR generado\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error al generar QR: " . $e->getMessage() . "\n";
    echo "   Archivo: " . $e->getFile() . "\n";
    echo "   Línea: " . $e->getLine() . "\n";
}
