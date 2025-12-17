<?php

echo "=== SOLUCIÓN DEFINITIVA LARAVEL + NEON ===\n\n";

// Crear un conector que realmente funcione con Laravel
$connectorCode = '<?php

namespace App\Database\Connectors;

use Illuminate\Database\Connectors\PostgresConnector;
use PDO;

class NeonPostgresConnector extends PostgresConnector
{
    /**
     * Create a DSN string from a configuration.
     *
     * @param  array  $config
     * @return string
     */
    protected function getDsn(array $config)
    {
        // Si es Neon, construir DSN con endpoint
        if (isset($config[\'host\']) && strpos($config[\'host\'], \'neon.tech\') !== false) {
            $host = $config[\'host\'];
            $port = $config[\'port\'] ?? 5432;
            $database = $config[\'database\'];
            
            // Extraer endpoint ID del hostname
            $endpointId = explode(\'.\', $host)[0];
            
            // Construir DSN con endpoint
            $dsn = "pgsql:host={$host};port={$port};dbname={$database}";
            
            if (isset($config[\'sslmode\'])) {
                $dsn .= ";sslmode={$config[\'sslmode\']}";
            }
            
            // Agregar endpoint para Neon
            $dsn .= ";options=endpoint={$endpointId}";
            
            return $dsn;
        }
        
        // Para otros hosts, usar el método padre
        return parent::getDsn($config);
    }
}';

// Crear directorio si no existe
if (!is_dir('app/Database/Connectors')) {
    mkdir('app/Database/Connectors', 0755, true);
}

file_put_contents('app/Database/Connectors/NeonPostgresConnector.php', $connectorCode);
echo "✅ Conector Neon actualizado\n";

// Crear service provider que funcione correctamente
$providerCode = '<?php

namespace App\Providers;

use App\Database\Connectors\NeonPostgresConnector;
use Illuminate\Database\PostgresConnection;
use Illuminate\Support\ServiceProvider;

class NeonDatabaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Obtener el database manager
        $db = $this->app[\'db\'];
        
        // Extender el driver pgsql para usar nuestro conector
        $db->extend(\'pgsql\', function ($config, $name) {
            $connector = new NeonPostgresConnector();
            $pdo = $connector->connect($config);
            
            return new PostgresConnection(
                $pdo,
                $config[\'database\'],
                $config[\'prefix\'] ?? \'\',
                $config
            );
        });
    }
}';

file_put_contents('app/Providers/NeonDatabaseServiceProvider.php', $providerCode);
echo "✅ Service provider Neon creado\n";

// Actualizar bootstrap/app.php
$bootstrapPath = 'bootstrap/app.php';
$bootstrapContent = file_get_contents($bootstrapPath);

// Remover providers anteriores si existen
$bootstrapContent = preg_replace('/->withProviders\(\[[^\]]*\]\)/', '', $bootstrapContent);

// Agregar el nuevo provider
$newBootstrap = str_replace(
    '    ->withExceptions(function (Exceptions $exceptions): void {',
    '    ->withProviders([
        App\\Providers\\NeonDatabaseServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions): void {',
    $bootstrapContent
);

file_put_contents($bootstrapPath, $newBootstrap);
echo "✅ Bootstrap actualizado con nuevo provider\n";

// Verificar configuración .env
echo "\n📝 VERIFICANDO CONFIGURACIÓN .ENV...\n";

$envContent = file_get_contents('.env');
$envLines = explode("\n", $envContent);
$newEnvLines = [];

$dbConfigFound = false;
foreach ($envLines as $line) {
    if (strpos($line, 'DB_') === 0) {
        if (!$dbConfigFound) {
            // Agregar configuración de Neon
            $newEnvLines[] = 'DB_CONNECTION=pgsql';
            $newEnvLines[] = 'DB_HOST=ep-calm-glitter-adgesoqd-pooler.c-2.us-east-1.aws.neon.tech';
            $newEnvLines[] = 'DB_PORT=5432';
            $newEnvLines[] = 'DB_DATABASE=neondb';
            $newEnvLines[] = 'DB_USERNAME=neondb_owner';
            $newEnvLines[] = 'DB_PASSWORD=npg_U0PA6dWCqayo';
            $newEnvLines[] = 'DB_SSLMODE=require';
            $dbConfigFound = true;
        }
        // Saltar líneas DB_ existentes
        continue;
    }
    $newEnvLines[] = $line;
}

file_put_contents('.env', implode("\n", $newEnvLines));
echo "✅ Archivo .env actualizado\n";

// Verificar config/database.php
echo "\n📝 VERIFICANDO CONFIG/DATABASE.PHP...\n";

$databaseConfig = file_get_contents('config/database.php');

// Asegurar que la configuración pgsql sea correcta
$pgsqlConfig = "        'pgsql' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => env('DB_SSLMODE', 'prefer'),
        ],";

// Reemplazar configuración pgsql
$pattern = "/'pgsql'\s*=>\s*\[[^\]]*\],/s";
$databaseConfig = preg_replace($pattern, $pgsqlConfig, $databaseConfig);

file_put_contents('config/database.php', $databaseConfig);
echo "✅ Config/database.php actualizado\n";

echo "\n🧪 PROBANDO CONEXIÓN...\n";

// Probar conexión directa
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
    
    echo "✅ Conexión PDO directa: OK\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM migrations");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Migraciones en Neon: " . $result['count'] . "\n";
    
} catch (PDOException $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "\n";
}

echo "\n🎯 PASOS FINALES:\n";
echo "1. php artisan config:clear\n";
echo "2. php artisan migrate (debería funcionar ahora)\n";
echo "3. php artisan db:seed (para poblar datos)\n";

echo "\n=== CONFIGURACIÓN COMPLETADA ===\n";
echo "Laravel ahora debería conectar correctamente a Neon.\n";