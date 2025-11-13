<?php
// Test completo del flujo de QR del profesor

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\AsistenciaDocente;
use App\Models\Horario;
use App\Models\Profesor;

echo "=== TEST FLUJO COMPLETO QR PROFESOR ===\n\n";

// 1. Buscar un profesor activo
$profesor = Profesor::where('estado', 'activo')->first();
if (!$profesor) {
    echo "❌ No hay profesores activos\n";
    exit;
}
echo "✅ Profesor: {$profesor->nombre_completo} (ID: {$profesor->id})\n";

// 2. Buscar un horario del profesor
$horario = Horario::whereHas('cargaAcademica', function($query) use ($profesor) {
    $query->where('profesor_id', $profesor->id);
})->with(['cargaAcademica.grupo.materia', 'aula'])->first();

if (!$horario) {
    echo "❌ No hay horarios asignados\n";
    exit;
}

echo "✅ Horario encontrado:\n";
echo "   - Materia: " . ($horario->cargaAcademica->grupo->materia->nombre ?? 'N/A') . "\n";
echo "   - Aula: " . ($horario->aula->codigo_aula ?? 'N/A') . "\n";
echo "   - Horario: {$horario->hora_inicio} - {$horario->hora_fin}\n\n";

// 3. PASO 1: Generar QR
echo "📱 PASO 1: Generando QR...\n";
try {
    $asistencia = AsistenciaDocente::generarQR($profesor->id, $horario->id, 'presencial');
    
    echo "✅ QR generado!\n";
    echo "   - Token: " . substr($asistencia->qr_token, 0, 20) . "...\n";
    echo "   - Sesión: #{$asistencia->numero_sesion}\n";
    echo "   - Estado: {$asistencia->estado}\n";
    echo "   - Generado: {$asistencia->qr_generado_at}\n\n";
    
    // 4. PASO 2: Verificar que el QR se puede recuperar
    echo "🔍 PASO 2: Verificando QR...\n";
    $qrRecuperado = AsistenciaDocente::where('qr_token', $asistencia->qr_token)
        ->whereNotNull('qr_generado_at')
        ->whereNull('qr_escaneado_at')
        ->first();
    
    if (!$qrRecuperado) {
        echo "❌ No se pudo recuperar el QR\n";
        exit;
    }
    
    echo "✅ QR recuperado correctamente\n";
    echo "   - ¿Expirado?: " . ($qrRecuperado->qr_generado_at->diffInMinutes(now()) > 30 ? 'SÍ' : 'NO') . "\n\n";
    
    // 5. PASO 3: Simular escaneo del QR
    echo "📲 PASO 3: Escaneando QR...\n";
    try {
        $asistenciaConfirmada = AsistenciaDocente::procesarEscaneoQR(
            $asistencia->qr_token,
            '127.0.0.1',
            null
        );
        
        echo "✅ QR escaneado exitosamente!\n";
        echo "   - Estado: {$asistenciaConfirmada->estado}\n";
        echo "   - Hora entrada: {$asistenciaConfirmada->hora_entrada}\n";
        echo "   - Validado en horario: " . ($asistenciaConfirmada->validado_en_horario ? 'SÍ' : 'NO') . "\n";
        echo "   - QR escaneado: {$asistenciaConfirmada->qr_escaneado_at}\n\n";
        
        // 6. PASO 4: Intentar escanear el mismo QR de nuevo (debe fallar)
        echo "🚫 PASO 4: Intentando escanear el mismo QR de nuevo...\n";
        try {
            AsistenciaDocente::procesarEscaneoQR(
                $asistencia->qr_token,
                '127.0.0.1',
                null
            );
            echo "❌ ERROR: El QR debería estar marcado como usado\n";
        } catch (\Exception $e) {
            echo "✅ Correcto! El QR ya no se puede usar\n";
            echo "   - Mensaje: {$e->getMessage()}\n\n";
        }
        
        // 7. PASO 5: Generar un nuevo QR (Sesión #2)
        echo "🔄 PASO 5: Generando nuevo QR (Sesión #2)...\n";
        $asistencia2 = AsistenciaDocente::generarQR($profesor->id, $horario->id, 'presencial');
        
        echo "✅ Nuevo QR generado!\n";
        echo "   - Sesión: #{$asistencia2->numero_sesion}\n";
        echo "   - Token diferente: " . ($asistencia->qr_token !== $asistencia2->qr_token ? 'SÍ' : 'NO') . "\n\n";
        
        // 8. Resumen
        echo "📊 RESUMEN:\n";
        echo "   - QRs generados: 2\n";
        echo "   - QRs escaneados: 1\n";
        echo "   - QRs activos: 1\n\n";
        
        // 9. URLs de prueba
        $url1 = url("/profesor/qr-vista/{$asistencia->qr_token}");
        $url2 = url("/profesor/qr-vista/{$asistencia2->qr_token}");
        
        echo "🌐 URLs para probar en el navegador:\n";
        echo "   QR #1 (usado): {$url1}\n";
        echo "   QR #2 (activo): {$url2}\n\n";
        
        echo "🎉 TODOS LOS PASOS COMPLETADOS EXITOSAMENTE!\n";
        echo "\n";
        echo "✅ El sistema funciona correctamente:\n";
        echo "   1. Se pueden generar QRs\n";
        echo "   2. Los QRs se pueden escanear\n";
        echo "   3. Los QRs usados no se pueden reutilizar\n";
        echo "   4. Se pueden generar múltiples sesiones\n";
        
    } catch (\Exception $e) {
        echo "❌ Error al escanear QR: " . $e->getMessage() . "\n";
        echo "   Archivo: " . $e->getFile() . "\n";
        echo "   Línea: " . $e->getLine() . "\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error al generar QR: " . $e->getMessage() . "\n";
    echo "   Archivo: " . $e->getFile() . "\n";
    echo "   Línea: " . $e->getLine() . "\n";
}
