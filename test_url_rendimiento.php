<?php
require_once 'vendor/autoload.php';

// Cargar configuración de Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\ReporteController;

echo "🧪 PROBANDO CONTROLADOR DE RENDIMIENTO DIRECTAMENTE\n";
echo "===================================================\n\n";

try {
    // Crear una instancia del controlador
    $controller = new ReporteController();
    
    // Crear un request simulado
    $request = new Request();
    
    echo "1. Probando sin parámetros...\n";
    $response = $controller->reporteRendimiento($request);
    echo "✅ Respuesta sin parámetros: OK\n\n";
    
    echo "2. Probando con materia_id...\n";
    $request = new Request(['materia_id' => 4]); // Programación I (ISC-101)
    $response = $controller->reporteRendimiento($request);
    echo "✅ Respuesta con materia_id: OK\n\n";
    
    echo "3. Probando con grupo_id...\n";
    $request = new Request(['materia_id' => 4, 'grupo_id' => 5]); // Grupo con estudiantes
    $response = $controller->reporteRendimiento($request);
    echo "✅ Respuesta con grupo_id: OK\n\n";
    
    echo "🎉 TODAS LAS PRUEBAS EXITOSAS\n";
    echo "El controlador funciona correctamente.\n";
    echo "El error 500 debe estar en otro lugar.\n\n";
    
    echo "🔍 POSIBLES CAUSAS DEL ERROR 500:\n";
    echo "1. Error en la vista (sintaxis Blade)\n";
    echo "2. Problema con middleware o autenticación\n";
    echo "3. Error en JavaScript del frontend\n";
    echo "4. Problema con la URL o routing\n";
    echo "5. Error en el servidor web\n\n";
    
    echo "📋 PARA DEBUGGEAR:\n";
    echo "1. Revisar logs de Laravel: storage/logs/laravel.log\n";
    echo "2. Revisar logs del servidor web\n";
    echo "3. Activar debug en .env: APP_DEBUG=true\n";
    echo "4. Probar con diferentes navegadores\n";
    
} catch (Exception $e) {
    echo "❌ ERROR EN EL CONTROLADOR:\n";
    echo "Mensaje: " . $e->getMessage() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}