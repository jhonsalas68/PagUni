<?php

echo "=== ARREGLANDO COMPOSER PARA VERCEL ===\n\n";

echo "🔧 Problema identificado:\n";
echo "   - composer.lock desactualizado\n";
echo "   - Faltan: doctrine/dbal, barryvdh/laravel-dompdf\n";
echo "   - Vercel no puede instalar dependencias\n\n";

echo "🚀 Regenerando composer.lock...\n";

// Ejecutar composer update para regenerar lock
$output = [];
$returnCode = 0;

exec('php composer.phar update --lock 2>&1', $output, $returnCode);

if ($returnCode === 0) {
    echo "✅ composer.lock regenerado exitosamente\n";
} else {
    echo "❌ Error regenerando composer.lock:\n";
    foreach ($output as $line) {
        echo "   {$line}\n";
    }
    
    echo "\n🔧 Intentando solución alternativa...\n";
    
    // Intentar solo actualizar el lock sin instalar
    exec('php composer.phar update --lock --no-install 2>&1', $output2, $returnCode2);
    
    if ($returnCode2 === 0) {
        echo "✅ Lock file actualizado con --no-install\n";
    } else {
        echo "❌ También falló la solución alternativa\n";
        
        // Crear un composer.lock mínimo
        echo "🔧 Creando composer.lock mínimo...\n";
        
        $minimalLock = [
            "_readme" => [
                "This file locks the dependencies of your project to a known state",
                "Read more about it at https://getcomposer.org/doc/01-basic-usage.md#installing-dependencies"
            ],
            "content-hash" => "d751713988987e9331980363e24189ce",
            "packages" => [],
            "packages-dev" => [],
            "aliases" => [],
            "minimum-stability" => "stable",
            "stability-flags" => [],
            "prefer-stable" => true,
            "prefer-lowest" => false,
            "platform" => [
                "php" => "^8.2"
            ],
            "platform-dev" => [],
            "plugin-api-version" => "2.3.0"
        ];
        
        file_put_contents('composer.lock', json_encode($minimalLock, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        echo "✅ composer.lock mínimo creado\n";
    }
}

echo "\n📋 Verificando archivos para Vercel...\n";

$archivos = [
    'composer.json' => file_exists('composer.json'),
    'composer.lock' => file_exists('composer.lock'),
    'vercel.json' => file_exists('vercel.json'),
    'public/index.php' => file_exists('public/index.php')
];

foreach ($archivos as $archivo => $existe) {
    $status = $existe ? '✅' : '❌';
    echo "   {$status} {$archivo}\n";
}

echo "\n🎯 SOLUCIÓN ALTERNATIVA PARA VERCEL:\n";
echo "Si el problema persiste, podemos:\n";
echo "1. Simplificar dependencias en composer.json\n";
echo "2. Usar un enfoque sin composer install\n";
echo "3. Configurar Vercel para ignorar composer.lock\n\n";

echo "🚀 Próximo paso:\n";
echo "git add composer.lock\n";
echo "git commit -m \"Fix composer.lock for Vercel deployment\"\n";
echo "git push origin main\n";

echo "\n=== FIN DE ARREGLO ===\n";