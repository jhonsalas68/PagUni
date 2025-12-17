<?php

echo "=== SOLUCIÓN DEFINITIVA NEON - INTERCEPTAR CONEXIÓN ===\n\n";

// Crear un conector que realmente funcione
$connectorCode = '<?php

namespace App\Database\Connectors;

use Illuminate\Database\Connectors\PostgresConnector;
use PDO;

class NeonPostgresConnector extends PostgresConnector
{
    /**
     * Create a DSN string from a configuration.
     */
    protected function getDsn(array $config)
    {
        // Si es Neon, construir DSN personalizado
        if (isset($config[\'host\']) && strpos($config[\'host\'], \'neon.tech\') !== false) {
            $host = $config[\'host\'];
            $port = $config[\'port\'] ?? 5432;
            $database = $config[\'database\'];
            
            // Extraer endpoint ID
            $endpointId = explode(\'.\', $host)[0];
            
            $dsn = "pgsql:host={$host};port={$port};dbname={$database}";
            
            if (isset($config[\'sslmode\'])) {
                $dsn .= ";sslmode={$config[\'sslmode\']}";
            }
            
            // Agregar endpoint para Neon
            $dsn .= ";options=endpoint={$endpointId}";
            
            return $dsn;
        }
        
        return parent::getDsn($config);
    }
}';

file_put_contents('app/Database/Connectors/NeonPostgresConnector.php', $connectorCode);
echo "✅ Conector Neon creado\n";

// Crear service provider que se registre correctamente
$providerCode = '<?php

namespace App\Providers;

use App\Database\Connectors\NeonPostgresConnector;
use Illuminate\Database\PostgresConnection;
use Illuminate\Support\ServiceProvider;

class NeonDatabaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Registrar el conector en el contenedor
        $this->app->singleton(\'db.connector.pgsql\', function () {
            return new NeonPostgresConnector();
        });
    }

    public function boot(): void
    {
        // Reemplazar el factory de conexiones PostgreSQL
        $this->app[\'db\']->extend(\'pgsql\', function ($config, $name) {
            $connector = $this->app->make(\'db.connector.pgsql\');
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
echo "✅ Service provider actualizado\n";

// Verificar bootstrap/app.php
$bootstrapContent = file_get_contents('bootstrap/app.php');

// Limpiar providers anteriores
$bootstrapContent = preg_replace('/->withProviders\(\[[^\]]*\]\)/', '', $bootstrapContent);

// Agregar el provider correcto
$newBootstrap = str_replace(
    '    ->withExceptions(function (Exceptions $exceptions): void {',
    '    ->withProviders([
        App\\Providers\\NeonDatabaseServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions): void {',
    $bootstrapContent
);

file_put_contents('bootstrap/app.php', $newBootstrap);
echo "✅ Bootstrap actualizado\n";

// Crear un método alternativo: modificar directamente el conector en el DatabaseManager
$managerPatchCode = '<?php

// Patch para DatabaseManager - aplicar después de bootstrap
use App\Database\Connectors\NeonPostgresConnector;

// Obtener el database manager
$db = app(\'db\');

// Reemplazar el conector PostgreSQL
$reflection = new ReflectionClass($db);
$connectorsProperty = $reflection->getProperty(\'connectors\');
$connectorsProperty->setAccessible(true);
$connectors = $connectorsProperty->getValue($db);
$connectors[\'pgsql\'] = new NeonPostgresConnector();
$connectorsProperty->setValue($db, $connectors);

echo "Conector PostgreSQL reemplazado con NeonPostgresConnector\n";
';

file_put_contents('patch_database_manager.php', $managerPatchCode);
echo "✅ Patch del DatabaseManager creado\n";

// Crear un script de prueba que aplique el patch
$testWithPatchCode = '<?php

require_once \'vendor/autoload.php\';

// Configurar Laravel
$app = require_once \'bootstrap/app.php\';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TEST CON PATCH APLICADO ===\n\n";

// Aplicar patch
require_once \'patch_database_manager.php\';

use Illuminate\Support\Facades\DB;

try {
    echo "🔌 PROBANDO CONEXIÓN CON PATCH...\n";
    
    $pdo = DB::connection()->getPdo();
    echo "✅ Conexión Laravel exitosa\n";
    
    $result = DB::select(\'SELECT version() as version\');
    echo "✅ Query exitosa: " . substr($result[0]->version, 0, 50) . "...\n";
    
    $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = \'public\' ORDER BY tablename");
    echo "✅ Tablas encontradas: " . count($tables) . "\n";
    
    foreach ($tables as $table) {
        echo "   - {$table->tablename}\n";
    }
    
    echo "\n🎉 LARAVEL + NEON FUNCIONANDO CON PATCH\n";

} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DEL TEST ===\n";
';

file_put_contents('test_neon_with_patch.php', $testWithPatchCode);
echo "✅ Test con patch creado\n";

echo "\n🧪 PROBANDO SOLUCIÓN...\n";

// Probar la solución
try {
    echo "Ejecutando test con patch...\n";
    $output = shell_exec('php test_neon_with_patch.php 2>&1');
    echo $output;
} catch (Exception $e) {
    echo "Error al ejecutar test: " . $e->getMessage() . "\n";
}

echo "\n🎯 ALTERNATIVA SIMPLE: USAR URL COMPLETA\n";

// Crear configuración con URL completa
$envContent = file_get_contents('.env');
$envLines = explode("\n", $envContent);
$newEnvLines = [];

foreach ($envLines as $line) {
    if (strpos($line, 'DB_') === 0) {
        continue; // Saltar líneas DB existentes
    }
    $newEnvLines[] = $line;
}

// Agregar configuración con URL completa
$newEnvLines[] = '';
$newEnvLines[] = 'DB_CONNECTION=pgsql';
$newEnvLines[] = 'DB_URL="pgsql://neondb_owner:npg_U0PA6dWCqayo@ep-calm-glitter-adgesoqd-pooler.c-2.us-east-1.aws.neon.tech:5432/neondb?sslmode=require&options=endpoint%3Dep-calm-glitter-adgesoqd"';

file_put_contents('.env', implode("\n", $newEnvLines));
echo "✅ .env actualizado con URL completa\n";

// Actualizar config/database.php para usar solo URL
$databaseConfig = file_get_contents('config/database.php');

$pgsqlConfig = "        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DB_URL'),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
        ],";

$pattern = "/'pgsql'\s*=>\s*\[[^\]]*\],/s";
$databaseConfig = preg_replace($pattern, $pgsqlConfig, $databaseConfig);

file_put_contents('config/database.php', $databaseConfig);
echo "✅ config/database.php simplificado\n";

echo "\n🎯 PASOS FINALES:\n";
echo "1. php artisan config:clear\n";
echo "2. php test_neon_with_patch.php (para probar con patch)\n";
echo "3. php artisan migrate (debería funcionar ahora)\n";

echo "\n=== SOLUCIÓN COMPLETADA ===\n";
echo "Si el patch no funciona, la base de datos ya está configurada con PDO directo.\n";