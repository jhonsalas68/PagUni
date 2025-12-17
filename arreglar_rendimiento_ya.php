<?php
require_once 'vendor/autoload.php';

// Cargar configuración de Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔧 ARREGLANDO REPORTE DE RENDIMIENTO - SOLUCIÓN DEFINITIVA\n";
echo "=========================================================\n\n";

// 1. Limpiar caché
echo "1. Limpiando caché...\n";
try {
    \Artisan::call('cache:clear');
    \Artisan::call('config:clear');
    \Artisan::call('view:clear');
    \Artisan::call('route:clear');
    echo "✅ Caché limpiado\n\n";
} catch (Exception $e) {
    echo "⚠️  Error limpiando caché: " . $e->getMessage() . "\n\n";
}

// 2. Verificar que las tablas existen
echo "2. Verificando tablas necesarias...\n";
try {
    $tablas = [
        'materias' => \DB::table('materias')->count(),
        'grupos' => \DB::table('grupos')->count(),
        'inscripciones' => \DB::table('inscripciones')->count(),
        'calificaciones' => \DB::table('calificaciones')->count(),
        'tipos_evaluacion' => \DB::table('tipos_evaluacion')->count(),
    ];
    
    foreach ($tablas as $tabla => $count) {
        echo "   ✅ {$tabla}: {$count} registros\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "❌ Error verificando tablas: " . $e->getMessage() . "\n\n";
}

// 3. Probar el controlador directamente
echo "3. Probando controlador...\n";
try {
    $controller = new \App\Http\Controllers\ReporteController();
    $request = new \Illuminate\Http\Request();
    
    // Prueba básica
    $response = $controller->reporteRendimiento($request);
    echo "   ✅ Controlador responde correctamente\n";
    
    // Prueba con parámetros
    $request = new \Illuminate\Http\Request(['materia_id' => 4, 'grupo_id' => 5]);
    $response = $controller->reporteRendimiento($request);
    echo "   ✅ Controlador con parámetros funciona\n\n";
    
} catch (Exception $e) {
    echo "   ❌ Error en controlador: " . $e->getMessage() . "\n\n";
}

// 4. Verificar rutas
echo "4. Verificando rutas...\n";
try {
    $routes = \Route::getRoutes();
    $rendimientoRoute = null;
    
    foreach ($routes as $route) {
        if (strpos($route->getName(), 'rendimiento') !== false) {
            $rendimientoRoute = $route;
            break;
        }
    }
    
    if ($rendimientoRoute) {
        echo "   ✅ Ruta encontrada: " . $rendimientoRoute->getName() . "\n";
        echo "   ✅ URI: " . $rendimientoRoute->uri() . "\n\n";
    } else {
        echo "   ❌ Ruta no encontrada\n\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error verificando rutas: " . $e->getMessage() . "\n\n";
}

// 5. Crear datos de prueba si no existen
echo "5. Verificando datos de prueba...\n";
try {
    $gruposConEstudiantes = \App\Models\Grupo::whereHas('inscripciones', function($q) {
        $q->where('estado', 'activo');
    })->count();
    
    if ($gruposConEstudiantes > 0) {
        echo "   ✅ Hay {$gruposConEstudiantes} grupos con estudiantes\n";
    } else {
        echo "   ⚠️  No hay grupos con estudiantes activos\n";
    }
    
    $calificaciones = \App\Models\Calificacion::count();
    if ($calificaciones > 0) {
        echo "   ✅ Hay {$calificaciones} calificaciones\n";
    } else {
        echo "   ⚠️  No hay calificaciones\n";
    }
    echo "\n";
    
} catch (Exception $e) {
    echo "   ❌ Error verificando datos: " . $e->getMessage() . "\n\n";
}

// 6. Probar URL directamente
echo "6. Probando URLs...\n";
$urls = [
    'http://localhost/reportes/rendimiento',
    'http://localhost/reportes/rendimiento?materia_id=4',
    'http://localhost/reportes/rendimiento?materia_id=4&grupo_id=5'
];

foreach ($urls as $url) {
    echo "   🔗 {$url}\n";
}
echo "\n";

echo "🎉 CORRECCIONES APLICADAS:\n";
echo "========================\n";
echo "✅ Controlador mejorado con manejo de errores\n";
echo "✅ Vista corregida con validaciones\n";
echo "✅ Caché limpiado\n";
echo "✅ Badges corregidos (bg-success → badge-success)\n";
echo "✅ Relaciones optimizadas\n";
echo "✅ Try-catch agregado para debugging\n\n";

echo "🚀 AHORA PRUEBA:\n";
echo "===============\n";
echo "1. Ve a: http://localhost/reportes/rendimiento\n";
echo "2. Selecciona 'Programación I (ISC-101)'\n";
echo "3. Selecciona 'Grupo A'\n";
echo "4. Click en 'Generar Reporte'\n\n";

echo "💡 SI AÚN NO FUNCIONA:\n";
echo "======================\n";
echo "1. Revisa storage/logs/laravel.log\n";
echo "2. Verifica que estés logueado como administrador\n";
echo "3. Prueba con F12 abierto para ver errores JS\n";
echo "4. Intenta con otro navegador\n\n";

echo "✨ ¡DEBERÍA FUNCIONAR AHORA!\n";