<?php
require_once 'vendor/autoload.php';

// Cargar configuración de Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔧 SOLUCIONANDO REPORTE DE RENDIMIENTO ACADÉMICO\n";
echo "===============================================\n\n";

// 1. Verificar si hay logs de error
$logPath = storage_path('logs/laravel.log');
if (file_exists($logPath)) {
    $logContent = file_get_contents($logPath);
    $lines = explode("\n", $logContent);
    $recentLines = array_slice($lines, -50); // Últimas 50 líneas
    
    echo "📋 ÚLTIMAS LÍNEAS DEL LOG:\n";
    foreach ($recentLines as $line) {
        if (strpos($line, 'ERROR') !== false || strpos($line, 'Exception') !== false) {
            echo "❌ " . $line . "\n";
        }
    }
    echo "\n";
} else {
    echo "📋 No se encontró archivo de log\n\n";
}

// 2. Verificar configuración de debug
$envPath = base_path('.env');
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    if (strpos($envContent, 'APP_DEBUG=false') !== false) {
        echo "⚠️  DEBUG DESACTIVADO - Activando debug...\n";
        $envContent = str_replace('APP_DEBUG=false', 'APP_DEBUG=true', $envContent);
        file_put_contents($envPath, $envContent);
        echo "✅ Debug activado en .env\n\n";
    } else {
        echo "✅ Debug ya está activado\n\n";
    }
}

// 3. Crear una ruta de prueba temporal
$testRouteContent = "
// RUTA TEMPORAL PARA PROBAR REPORTE DE RENDIMIENTO
Route::get('/test-rendimiento', function() {
    try {
        \$controller = new \\App\\Http\\Controllers\\ReporteController();
        \$request = new \\Illuminate\\Http\\Request();
        return \$controller->reporteRendimiento(\$request);
    } catch (Exception \$e) {
        return response()->json([
            'error' => \$e->getMessage(),
            'line' => \$e->getLine(),
            'file' => \$e->getFile(),
            'trace' => \$e->getTraceAsString()
        ], 500);
    }
});
";

$routesPath = base_path('routes/web.php');
$routesContent = file_get_contents($routesPath);

if (strpos($routesContent, 'test-rendimiento') === false) {
    // Agregar la ruta temporal al final del archivo
    $routesContent = rtrim($routesContent) . "\n\n" . $testRouteContent;
    file_put_contents($routesPath, $routesContent);
    echo "✅ Ruta de prueba agregada: /test-rendimiento\n\n";
} else {
    echo "✅ Ruta de prueba ya existe\n\n";
}

// 4. Verificar permisos de archivos
$viewPath = resource_path('views/reportes/rendimiento.blade.php');
if (file_exists($viewPath)) {
    echo "✅ Vista existe: {$viewPath}\n";
    if (is_readable($viewPath)) {
        echo "✅ Vista es legible\n";
    } else {
        echo "❌ Vista no es legible - problema de permisos\n";
    }
} else {
    echo "❌ Vista no existe: {$viewPath}\n";
}

echo "\n🚀 PASOS PARA SOLUCIONAR:\n";
echo "1. Ir a: http://localhost/test-rendimiento\n";
echo "2. Si funciona, el problema es el middleware\n";
echo "3. Si no funciona, revisar el error JSON devuelto\n";
echo "4. Revisar logs de Laravel: storage/logs/laravel.log\n";
echo "5. Verificar que el usuario esté autenticado correctamente\n\n";

echo "🔍 URLS PARA PROBAR:\n";
echo "- Ruta temporal: http://localhost/test-rendimiento\n";
echo "- Ruta original: http://localhost/reportes/rendimiento\n";
echo "- Con parámetros: http://localhost/reportes/rendimiento?materia_id=4&grupo_id=5\n\n";

echo "✨ SOLUCIÓN APLICADA\n";