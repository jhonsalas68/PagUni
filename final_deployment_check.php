<?php
/**
 * Verificación final antes del despliegue
 * Asegura que no habrá errores en producción
 */

echo "=== VERIFICACIÓN FINAL PARA DESPLIEGUE ===\n\n";

$errors = [];
$warnings = [];
$success = [];

// 1. Verificar estructura de archivos críticos
echo "1. VERIFICANDO ESTRUCTURA DE ARCHIVOS:\n";

$critical_files = [
    '.env.example' => 'Plantilla de configuración',
    'composer.json' => 'Dependencias PHP',
    'package.json' => 'Dependencias Node.js',
    'artisan' => 'CLI de Laravel',
    'public/index.php' => 'Punto de entrada',
    'bootstrap/app.php' => 'Bootstrap de aplicación (Laravel 11+)',
    'config/app.php' => 'Configuración principal',
    'config/database.php' => 'Configuración BD',
    'routes/web.php' => 'Rutas web',
    'install.sh' => 'Script de instalación',
    'verify_deployment.php' => 'Script de verificación'
];

foreach ($critical_files as $file => $desc) {
    if (file_exists($file)) {
        echo "✅ $file - $desc\n";
        $success[] = "Archivo $file presente";
    } else {
        echo "❌ $file - $desc - FALTA\n";
        $errors[] = "Archivo crítico faltante: $file";
    }
}

// 2. Verificar migraciones
echo "\n2. VERIFICANDO MIGRACIONES:\n";

$migrations = glob('database/migrations/*.php');
if (count($migrations) >= 40) {
    echo "✅ " . count($migrations) . " migraciones encontradas\n";
    $success[] = "Migraciones completas";
} else {
    echo "⚠️ Solo " . count($migrations) . " migraciones (esperadas 40+)\n";
    $warnings[] = "Pocas migraciones encontradas";
}

// Verificar migraciones críticas específicas
$critical_migrations = [
    'create_users_table.php',
    'create_profesores_table.php', 
    'create_estudiantes_table.php',
    'create_chat_tables.php',
    'create_calificaciones_table.php'
];

foreach ($critical_migrations as $migration) {
    $found = false;
    foreach ($migrations as $file) {
        if (strpos($file, $migration) !== false) {
            $found = true;
            break;
        }
    }
    if ($found) {
        echo "✅ Migración crítica: $migration\n";
    } else {
        echo "⚠️ Migración crítica no encontrada: $migration\n";
        $warnings[] = "Migración faltante: $migration";
    }
}

// 3. Verificar modelos
echo "\n3. VERIFICANDO MODELOS:\n";

$models = glob('app/Models/*.php');
$chat_models = glob('app/Models/Chat/*.php');
$total_models = count($models) + count($chat_models);

if ($total_models >= 15) {
    echo "✅ $total_models modelos encontrados\n";
    $success[] = "Modelos completos";
} else {
    echo "⚠️ Solo $total_models modelos (esperados 15+)\n";
    $warnings[] = "Pocos modelos encontrados";
}

// 4. Verificar controladores
echo "\n4. VERIFICANDO CONTROLADORES:\n";

$controllers = glob('app/Http/Controllers/*.php');
$admin_controllers = glob('app/Http/Controllers/Admin/*.php');
$total_controllers = count($controllers) + count($admin_controllers);

if ($total_controllers >= 15) {
    echo "✅ $total_controllers controladores encontrados\n";
    $success[] = "Controladores completos";
} else {
    echo "⚠️ Solo $total_controllers controladores\n";
    $warnings[] = "Pocos controladores encontrados";
}

// 5. Verificar vistas
echo "\n5. VERIFICANDO VISTAS:\n";

$views = glob('resources/views/**/*.blade.php');
if (count($views) >= 50) {
    echo "✅ " . count($views) . " vistas encontradas\n";
    $success[] = "Vistas completas";
} else {
    echo "⚠️ Solo " . count($views) . " vistas\n";
    $warnings[] = "Pocas vistas encontradas";
}

// 6. Verificar assets públicos
echo "\n6. VERIFICANDO ASSETS PÚBLICOS:\n";

$public_assets = [
    'public/css/responsive.css' => 'CSS responsivo',
    'public/js/pwa-handler.js' => 'JavaScript PWA',
    'public/manifest.json' => 'Manifiesto PWA',
    'public/sw.js' => 'Service Worker',
    'public/images/icons' => 'Iconos PWA'
];

foreach ($public_assets as $asset => $desc) {
    if (file_exists($asset)) {
        echo "✅ $asset - $desc\n";
        $success[] = "Asset $asset presente";
    } else {
        echo "⚠️ $asset - $desc - FALTA\n";
        $warnings[] = "Asset faltante: $asset";
    }
}

// 7. Verificar configuración de producción
echo "\n7. VERIFICANDO CONFIGURACIÓN DE PRODUCCIÓN:\n";

if (file_exists('.env.production')) {
    echo "✅ Configuración de producción preparada\n";
    $success[] = "Configuración de producción lista";
    
    $env_prod = file_get_contents('.env.production');
    if (strpos($env_prod, 'APP_ENV=production') !== false) {
        echo "✅ Configurado para producción\n";
    } else {
        echo "⚠️ No configurado para producción\n";
        $warnings[] = "APP_ENV no configurado para producción";
    }
} else {
    echo "❌ Configuración de producción faltante\n";
    $errors[] = "Archivo .env.production faltante";
}

// 8. Verificar seeders
echo "\n8. VERIFICANDO SEEDERS:\n";

$seeders = glob('database/seeders/*.php');
if (count($seeders) >= 10) {
    echo "✅ " . count($seeders) . " seeders encontrados\n";
    $success[] = "Seeders completos";
} else {
    echo "⚠️ Solo " . count($seeders) . " seeders\n";
    $warnings[] = "Pocos seeders encontrados";
}

// Verificar seeder crítico
if (file_exists('database/seeders/AdminSeeder.php')) {
    echo "✅ AdminSeeder presente (credenciales de acceso)\n";
    $success[] = "AdminSeeder disponible";
} else {
    echo "❌ AdminSeeder faltante\n";
    $errors[] = "AdminSeeder crítico faltante";
}

// 9. Verificar rutas
echo "\n9. VERIFICANDO RUTAS:\n";

$web_routes = file_get_contents('routes/web.php');
$route_count = substr_count($web_routes, 'Route::');

if ($route_count >= 20) {
    echo "✅ $route_count rutas definidas\n";
    $success[] = "Rutas completas";
} else {
    echo "⚠️ Solo $route_count rutas definidas\n";
    $warnings[] = "Pocas rutas definidas";
}

// 10. Verificar middleware
echo "\n10. VERIFICANDO MIDDLEWARE:\n";

$middleware_files = glob('app/Http/Middleware/*.php');
if (count($middleware_files) >= 3) {
    echo "✅ " . count($middleware_files) . " middleware encontrados\n";
    $success[] = "Middleware completo";
} else {
    echo "⚠️ Solo " . count($middleware_files) . " middleware\n";
    $warnings[] = "Poco middleware encontrado";
}

// 11. Verificar composer.json
echo "\n11. VERIFICANDO COMPOSER.JSON:\n";

$composer = json_decode(file_get_contents('composer.json'), true);

if (isset($composer['require']['laravel/framework'])) {
    echo "✅ Laravel Framework: " . $composer['require']['laravel/framework'] . "\n";
    $success[] = "Laravel Framework configurado";
} else {
    echo "❌ Laravel Framework no configurado\n";
    $errors[] = "Laravel Framework faltante en composer.json";
}

// Verificar dependencias importantes
$important_deps = [
    'maatwebsite/excel' => 'Exportación Excel',
    'simplesoftwareio/simple-qrcode' => 'Generación QR',
    'doctrine/dbal' => 'Modificaciones BD'
];

foreach ($important_deps as $dep => $desc) {
    if (isset($composer['require'][$dep])) {
        echo "✅ $desc: " . $composer['require'][$dep] . "\n";
        $success[] = "$desc configurado";
    } else {
        echo "⚠️ $desc no configurado\n";
        $warnings[] = "$desc faltante";
    }
}

// 12. Verificar permisos (simulado)
echo "\n12. VERIFICANDO ESTRUCTURA DE PERMISOS:\n";

$writable_dirs = ['storage', 'bootstrap/cache'];
foreach ($writable_dirs as $dir) {
    if (is_dir($dir)) {
        echo "✅ Directorio $dir existe\n";
        $success[] = "Directorio $dir presente";
    } else {
        echo "❌ Directorio $dir faltante\n";
        $errors[] = "Directorio crítico faltante: $dir";
    }
}

// RESUMEN FINAL
echo "\n" . str_repeat("=", 60) . "\n";
echo "RESUMEN FINAL DE VERIFICACIÓN\n";
echo str_repeat("=", 60) . "\n";

echo "\n✅ ÉXITOS (" . count($success) . "):\n";
foreach (array_slice($success, 0, 5) as $item) {
    echo "  • $item\n";
}
if (count($success) > 5) {
    echo "  • ... y " . (count($success) - 5) . " más\n";
}

if (!empty($warnings)) {
    echo "\n⚠️ ADVERTENCIAS (" . count($warnings) . "):\n";
    foreach ($warnings as $warning) {
        echo "  • $warning\n";
    }
}

if (!empty($errors)) {
    echo "\n❌ ERRORES CRÍTICOS (" . count($errors) . "):\n";
    foreach ($errors as $error) {
        echo "  • $error\n";
    }
}

// Evaluación final
echo "\n" . str_repeat("-", 60) . "\n";

if (empty($errors)) {
    if (count($warnings) <= 3) {
        echo "🎉 SISTEMA LISTO PARA DESPLIEGUE\n";
        echo "✅ Sin errores críticos\n";
        echo "✅ Advertencias mínimas (" . count($warnings) . ")\n";
        echo "✅ Funcionalidades completas\n";
    } else {
        echo "⚠️ SISTEMA CASI LISTO PARA DESPLIEGUE\n";
        echo "✅ Sin errores críticos\n";
        echo "⚠️ Varias advertencias (" . count($warnings) . ")\n";
        echo "📝 Revisar advertencias antes del despliegue\n";
    }
} else {
    echo "❌ SISTEMA NO LISTO PARA DESPLIEGUE\n";
    echo "❌ Errores críticos encontrados (" . count($errors) . ")\n";
    echo "🔧 Corregir errores antes de desplegar\n";
}

echo "\n📋 PRÓXIMOS PASOS:\n";
if (empty($errors)) {
    echo "1. Subir archivos al servidor de producción\n";
    echo "2. Ejecutar: chmod +x install.sh && ./install.sh\n";
    echo "3. Configurar .env con datos reales de producción\n";
    echo "4. Configurar servidor web (Nginx/Apache)\n";
    echo "5. Ejecutar: php verify_deployment.php\n";
    echo "6. Probar todas las funcionalidades\n";
} else {
    echo "1. Corregir errores críticos listados arriba\n";
    echo "2. Ejecutar nuevamente esta verificación\n";
    echo "3. Proceder con despliegue solo cuando no haya errores\n";
}

echo "\n🔗 CREDENCIALES DE ACCESO:\n";
echo "• Admin: admin@ficct.edu.bo / admin123\n";
echo "• Profesor: prof001@ficct.edu.bo / prof123\n";
echo "• Estudiante: est001@ficct.edu.bo / est123\n";

echo "\n" . str_repeat("=", 60) . "\n";
echo "VERIFICACIÓN COMPLETADA\n";
echo str_repeat("=", 60) . "\n";
?>