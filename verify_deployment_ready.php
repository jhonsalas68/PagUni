<?php

echo "=== VERIFICACIÓN DEPLOYMENT LARAVEL ===\n";

$critical_files = [
    'bootstrap/app.php' => 'Laravel bootstrap',
    'bootstrap/providers.php' => 'Service providers (Laravel 11+)',
    'public/index.php' => 'Entry point',
    'config/app.php' => 'App configuration',
    'composer.json' => 'Dependencies',
    'vercel.json' => 'Vercel config'
];

$all_good = true;

foreach ($critical_files as $file => $desc) {
    if (file_exists($file)) {
        echo "✓ {$file} - {$desc}\n";
    } else {
        echo "✗ {$file} - {$desc} [MISSING]\n";
        $all_good = false;
    }
}

// Verificar composer.json
if (file_exists('composer.json')) {
    $composer = json_decode(file_get_contents('composer.json'), true);
    $php_version = $composer['require']['php'] ?? 'NOT SET';
    $laravel_version = $composer['require']['laravel/framework'] ?? 'NOT SET';
    
    echo "\nDependencias:\n";
    echo "✓ PHP: {$php_version}\n";
    echo "✓ Laravel: {$laravel_version}\n";
}

// Verificar .vercelignore
if (file_exists('.vercelignore')) {
    $ignore_content = file_get_contents('.vercelignore');
    $includes_bootstrap = strpos($ignore_content, '!bootstrap/*.php') !== false;
    $includes_config = strpos($ignore_content, '!config/*.php') !== false;
    
    echo "\nVercelignore:\n";
    echo ($includes_bootstrap ? "✓" : "✗") . " Incluye bootstrap/*.php\n";
    echo ($includes_config ? "✓" : "✗") . " Incluye config/*.php\n";
}

echo "\nEstado: " . ($all_good ? "✓ LISTO PARA DEPLOYMENT" : "✗ REQUIERE CORRECCIONES") . "\n";
echo "=== FIN VERIFICACIÓN ===\n";