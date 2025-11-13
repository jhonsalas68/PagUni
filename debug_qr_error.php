<?php
// Debug del error 400 en QR

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\AsistenciaDocente;
use App\Models\Horario;
use App\Models\Profesor;

echo "=== DEBUG ERROR 400 QR ===\n\n";

// Obtener el token del QR que está fallando
echo "Ingresa el token del QR (o presiona Enter para usar uno de prueba): ";
$token = trim(fgets(STDIN));

if (empty($token)) {
    echo "Generando QR de prueba...\n";
    
    $profesor = Profesor::where('estado', 'activo')->first();
    $horario = Horario::whereHas('cargaAcademica', function($query) use ($profesor) {
        $query->where('profesor_id', $profesor->id);
    })->first();
    
    if (!$profesor || !$horario) {
        echo "❌ No hay datos para generar QR de prueba\n";
        exit;
    }
    
    $asistencia = AsistenciaDocente::generarQR($profesor->id, $horario->id, 'presencial');
    $token = $asistencia->qr_token;
    echo "✅ QR generado: " . substr($token, 0, 30) . "...\n\n";
}

echo "🔍 Buscando QR con token: " . substr($token, 0, 30) . "...\n\n";

// 1. Verificar que el QR existe
$qr = AsistenciaDocente::where('qr_token', $token)->first();

if (!$qr) {
    echo "❌ ERROR: QR no encontrado en la base de datos\n";
    echo "   Posibles causas:\n";
    echo "   - Token incorrecto o incompleto\n";
    echo "   - QR fue eliminado de la base de datos\n";
    exit;
}

echo "✅ QR encontrado en la base de datos\n";
echo "   - ID: {$qr->id}\n";
echo "   - Estado: {$qr->estado}\n";
echo "   - Generado: {$qr->qr_generado_at}\n";
echo "   - Escaneado: " . ($qr->qr_escaneado_at ?? 'No') . "\n\n";

// 2. Verificar condiciones
echo "🔍 Verificando condiciones:\n\n";

// Condición 1: qr_generado_at no es null
if (is_null($qr->qr_generado_at)) {
    echo "❌ FALLA: qr_generado_at es NULL\n";
} else {
    echo "✅ qr_generado_at: {$qr->qr_generado_at}\n";
}

// Condición 2: qr_escaneado_at es null
if (!is_null($qr->qr_escaneado_at)) {
    echo "❌ FALLA: QR ya fue escaneado el {$qr->qr_escaneado_at}\n";
    echo "   Este es el problema: El QR ya fue usado\n";
    echo "   Solución: Genera un nuevo QR\n";
} else {
    echo "✅ qr_escaneado_at: NULL (no ha sido escaneado)\n";
}

// Condición 3: No ha expirado (30 minutos)
$minutosTranscurridos = $qr->qr_generado_at->diffInMinutes(now());
echo "\n⏰ Tiempo transcurrido: {$minutosTranscurridos} minutos\n";

if ($minutosTranscurridos > 30) {
    echo "❌ FALLA: QR expirado (más de 30 minutos)\n";
    echo "   Solución: Genera un nuevo QR\n";
} else {
    echo "✅ QR válido (menos de 30 minutos)\n";
}

// 3. Intentar procesar el QR
echo "\n🧪 Intentando procesar el QR...\n";

try {
    $resultado = AsistenciaDocente::procesarEscaneoQR($token, '127.0.0.1', null);
    echo "✅ QR procesado exitosamente!\n";
    echo "   - Estado: {$resultado->estado}\n";
    echo "   - Hora entrada: {$resultado->hora_entrada}\n";
} catch (\Exception $e) {
    echo "❌ ERROR al procesar QR:\n";
    echo "   Mensaje: {$e->getMessage()}\n";
    echo "   Archivo: {$e->getFile()}\n";
    echo "   Línea: {$e->getLine()}\n\n";
    
    echo "📋 Diagnóstico:\n";
    if (strpos($e->getMessage(), 'inválido o ya utilizado') !== false) {
        echo "   ⚠️ El QR ya fue usado o no es válido\n";
        echo "   ✅ Solución: Genera un nuevo QR desde el dashboard\n";
    } elseif (strpos($e->getMessage(), 'expirado') !== false) {
        echo "   ⚠️ El QR ha expirado (más de 30 minutos)\n";
        echo "   ✅ Solución: Genera un nuevo QR\n";
    } else {
        echo "   ⚠️ Error desconocido\n";
        echo "   ✅ Revisa los logs en storage/logs/laravel.log\n";
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "RESUMEN:\n";
echo str_repeat("=", 50) . "\n";

if (!is_null($qr->qr_escaneado_at)) {
    echo "🔴 PROBLEMA IDENTIFICADO: QR ya fue usado\n";
    echo "   El QR fue escaneado el: {$qr->qr_escaneado_at}\n";
    echo "   Cada QR solo puede usarse UNA VEZ\n\n";
    echo "✅ SOLUCIÓN:\n";
    echo "   1. Ve al dashboard del profesor\n";
    echo "   2. Busca la clase actual\n";
    echo "   3. Haz clic en 'Nueva Sesión' o 'Generar QR'\n";
    echo "   4. Comparte el NUEVO QR\n";
} elseif ($minutosTranscurridos > 30) {
    echo "🔴 PROBLEMA IDENTIFICADO: QR expirado\n";
    echo "   El QR fue generado hace {$minutosTranscurridos} minutos\n";
    echo "   Los QR expiran después de 30 minutos\n\n";
    echo "✅ SOLUCIÓN:\n";
    echo "   Genera un nuevo QR (el anterior ya no sirve)\n";
} else {
    echo "✅ El QR está válido y debería funcionar\n";
    echo "   Si sigue fallando, revisa:\n";
    echo "   - Conexión a internet\n";
    echo "   - Logs del servidor (storage/logs/laravel.log)\n";
    echo "   - Consola del navegador (F12)\n";
}
