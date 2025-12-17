<?php

echo "=== SCRIPT DE MIGRACIÓN PARA NEON ===\n\n";

// Configuración de Neon
$host = 'ep-calm-glitter-adgesoqd-pooler.c-2.us-east-1.aws.neon.tech';
$port = '5432';
$dbname = 'neondb';
$user = 'neondb_owner';
$password = 'npg_U0PA6dWCqayo';
$endpoint = 'ep-calm-glitter-adgesoqd';

echo "🔧 CONFIGURACIÓN NEON:\n";
echo "Host: {$host}\n";
echo "Database: {$dbname}\n";
echo "User: {$user}\n";
echo "Endpoint: {$endpoint}\n\n";

try {
    // Conectar directamente con PDO
    $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=require;options=endpoint={$endpoint}";
    
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 30,
    ]);
    
    echo "✅ Conexión PDO exitosa\n";
    
    // Crear tabla migrations manualmente
    echo "\n📋 CREANDO TABLA MIGRATIONS...\n";
    
    $createMigrationsTable = "
        CREATE TABLE IF NOT EXISTS migrations (
            id SERIAL PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            batch INTEGER NOT NULL
        )
    ";
    
    $pdo->exec($createMigrationsTable);
    echo "✅ Tabla migrations creada\n";
    
    // Verificar tablas existentes
    echo "\n📚 VERIFICANDO TABLAS EXISTENTES...\n";
    
    $stmt = $pdo->query("
        SELECT tablename 
        FROM pg_tables 
        WHERE schemaname = 'public' 
        ORDER BY tablename
    ");
    
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "⚠️  No hay tablas en el esquema público\n";
    } else {
        echo "Tablas encontradas:\n";
        foreach ($tables as $table) {
            echo "   - {$table}\n";
        }
    }
    
    // Ahora configurar Laravel para usar parámetros individuales
    echo "\n📝 CONFIGURANDO LARAVEL PARA NEON...\n";
    
    $envContent = "APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:vhyFGfuZypDI5i0SaA+4HWjR/hGpiwDxCKIqHZ7w0D8=
APP_DEBUG=true
APP_URL=http://192.168.1.6:8000
APP_TIMEZONE=America/La_Paz

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US  

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST={$host}
DB_PORT={$port}
DB_DATABASE={$dbname}
DB_USERNAME={$user}
DB_PASSWORD={$password}
DB_SSLMODE=require

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS=\"hello@example.com\"
MAIL_FROM_NAME=\"\${APP_NAME}\"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME=\"\${APP_NAME}\"

VAPID_PUBLIC_KEY=BPCoWpiAZsSE7urpoydcszvkgXsF7REKAZ-jQ3cgYMvDMc8w6M3b2EJsoOazKVvtKwWTFF9G_RvSUtPdkigRiQE
VAPID_PRIVATE_KEY=qyf6bBO-0-MFcuQEwzeHqYZCOXD7ChxudJJKa5h0o2dM
VAPID_SUBJECT=mailto:admin@uagrm.edu.bo
";
    
    file_put_contents('.env', $envContent);
    echo "✅ Archivo .env actualizado\n";
    
    // Crear un conector personalizado más simple
    echo "\n🔧 CREANDO CONECTOR PERSONALIZADO...\n";
    
    $connectorCode = '<?php

namespace App\Database\Connectors;

use Illuminate\Database\Connectors\PostgresConnector;
use PDO;

class NeonConnector extends PostgresConnector
{
    /**
     * Establish a database connection.
     *
     * @param  array  $config
     * @return \PDO
     */
    public function connect(array $config)
    {
        // Si es Neon, usar DSN personalizado
        if (isset($config[\'host\']) && strpos($config[\'host\'], \'neon.tech\') !== false) {
            $dsn = $this->getNeonDsn($config);
            
            $connection = $this->createConnection($dsn, $config, $this->getOptions($config));
            
            $this->configureEncoding($connection, $config);
            $this->configureTimezone($connection, $config);
            $this->configureSearchPath($connection, $config);
            $this->configureSynchronousCommit($connection, $config);
            
            return $connection;
        }
        
        // Para otros hosts, usar el conector estándar
        return parent::connect($config);
    }
    
    /**
     * Create a DSN string for Neon.
     *
     * @param  array  $config
     * @return string
     */
    protected function getNeonDsn(array $config)
    {
        $host = $config[\'host\'];
        $port = $config[\'port\'] ?? 5432;
        $database = $config[\'database\'];
        
        // Extraer endpoint ID
        $endpointId = explode(\'.\', $host)[0];
        
        return "pgsql:host={$host};port={$port};dbname={$database};sslmode=require;options=endpoint={$endpointId}";
    }
}';
    
    if (!is_dir('app/Database/Connectors')) {
        mkdir('app/Database/Connectors', 0755, true);
    }
    
    file_put_contents('app/Database/Connectors/NeonConnector.php', $connectorCode);
    echo "✅ Conector Neon creado\n";
    
    // Crear service provider
    $providerCode = '<?php

namespace App\Providers;

use App\Database\Connectors\NeonConnector;
use Illuminate\Database\PostgresConnection;
use Illuminate\Support\ServiceProvider;

class NeonServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app[\'db\']->extend(\'pgsql\', function ($config, $name) {
            $connector = new NeonConnector();
            $connection = $connector->connect($config);
            
            return new PostgresConnection(
                $connection,
                $config[\'database\'],
                $config[\'prefix\'] ?? \'\',
                $config
            );
        });
    }
}';
    
    file_put_contents('app/Providers/NeonServiceProvider.php', $providerCode);
    echo "✅ Service provider Neon creado\n";
    
    // Registrar en bootstrap/app.php
    $bootstrapContent = file_get_contents('bootstrap/app.php');
    
    if (strpos($bootstrapContent, 'NeonServiceProvider') === false) {
        $bootstrapContent = str_replace(
            '    ->withExceptions(function (Exceptions $exceptions): void {',
            '    ->withProviders([
        App\\Providers\\NeonServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions): void {',
            $bootstrapContent
        );
        
        file_put_contents('bootstrap/app.php', $bootstrapContent);
        echo "✅ Service provider registrado\n";
    }
    
    echo "\n🎯 PRÓXIMOS PASOS:\n";
    echo "1. php artisan config:clear\n";
    echo "2. php artisan migrate\n";
    
    echo "\n🎉 CONFIGURACIÓN NEON COMPLETADA\n";
    echo "La base de datos está lista para las migraciones de Laravel.\n";

} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DEL SCRIPT ===\n";