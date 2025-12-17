<?php

echo "=== CONFIGURACIÓN AUTOMÁTICA PARA NEON ===\n\n";

// Leer el archivo .env actual
$envFile = '.env';
$envContent = file_get_contents($envFile);

echo "📝 CONFIGURANDO CONEXIÓN NEON...\n";

// Configuración para Neon
$neonConfig = [
    'DB_CONNECTION=pgsql',
    'DB_HOST=ep-calm-glitter-adgesoqd-pooler.c-2.us-east-1.aws.neon.tech',
    'DB_PORT=5432',
    'DB_DATABASE=neondb',
    'DB_USERNAME=neondb_owner',
    'DB_PASSWORD=npg_U0PA6dWCqayo',
    'DB_SSLMODE=require',
    'DB_ENDPOINT=ep-calm-glitter-adgesoqd'
];

// Remover configuraciones de DB existentes
$lines = explode("\n", $envContent);
$newLines = [];

foreach ($lines as $line) {
    if (!preg_match('/^DB_/', trim($line))) {
        $newLines[] = $line;
    }
}

// Agregar nueva configuración
$newLines = array_merge($newLines, [''], $neonConfig);

// Escribir archivo .env
file_put_contents($envFile, implode("\n", $newLines));

echo "✅ Archivo .env actualizado\n";

// Actualizar config/database.php
echo "\n📝 ACTUALIZANDO CONFIG/DATABASE.PHP...\n";

$databaseConfig = file_get_contents('config/database.php');

// Buscar la configuración de pgsql y reemplazarla
$newPgsqlConfig = "        'pgsql' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => env('DB_SSLMODE', 'prefer'),
            'options' => [
                PDO::ATTR_TIMEOUT => 30,
            ],
        ],";

// Usar regex para reemplazar la configuración pgsql
$pattern = "/'pgsql'\s*=>\s*\[[^\]]*\],/s";
$databaseConfig = preg_replace($pattern, $newPgsqlConfig, $databaseConfig);

file_put_contents('config/database.php', $databaseConfig);

echo "✅ Config/database.php actualizado\n";

echo "\n🧪 PROBANDO CONEXIÓN...\n";

// Probar conexión directa con PDO
try {
    $host = 'ep-calm-glitter-adgesoqd-pooler.c-2.us-east-1.aws.neon.tech';
    $port = '5432';
    $dbname = 'neondb';
    $user = 'neondb_owner';
    $password = 'npg_U0PA6dWCqayo';
    $endpoint = 'ep-calm-glitter-adgesoqd';
    
    $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=require;options=endpoint={$endpoint}";
    
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 30,
    ]);
    
    echo "✅ Conexión PDO directa exitosa\n";
    
    $stmt = $pdo->query('SELECT version() as version');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ PostgreSQL: " . substr($result['version'], 0, 50) . "...\n";
    
} catch (PDOException $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "\n";
}

echo "\n🎯 PRÓXIMOS PASOS:\n";
echo "1. Ejecutar: php artisan config:clear\n";
echo "2. Ejecutar: php artisan migrate\n";
echo "3. Si hay problemas, usar: php artisan migrate --force\n";

echo "\n=== CONFIGURACIÓN COMPLETADA ===\n";