<?php

echo "=== SOLUCIÓN FINAL PARA NEON ===\n\n";

// Crear un conector personalizado que extienda PostgresConnector
$connectorPath = 'app/Database/Connectors/NeonPostgresConnector.php';

// Crear directorio si no existe
if (!is_dir('app/Database')) {
    mkdir('app/Database', 0755, true);
}
if (!is_dir('app/Database/Connectors')) {
    mkdir('app/Database/Connectors', 0755, true);
}

$connectorCode = '<?php

namespace App\Database\Connectors;

use Illuminate\Database\Connectors\PostgresConnector;

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
        // Construir DSN básico
        $dsn = parent::getDsn($config);
        
        // Si es un host de Neon, agregar el endpoint ID automáticamente
        if (isset($config[\'host\']) && strpos($config[\'host\'], \'neon.tech\') !== false) {
            // Extraer endpoint ID del hostname (primera parte antes del primer punto)
            $hostParts = explode(\'.\', $config[\'host\']);
            if (count($hostParts) > 0) {
                $endpointId = $hostParts[0]; // ep-calm-glitter-adgesoqd
                
                // Agregar el parámetro endpoint al DSN
                $dsn .= \';options=endpoint=\' . $endpointId;
            }
        }
        
        return $dsn;
    }
}';

file_put_contents($connectorPath, $connectorCode);
echo "✅ Conector personalizado creado: {$connectorPath}\n";

// Crear un service provider para registrar el conector
$providerPath = 'app/Providers/DatabaseServiceProvider.php';

$providerCode = '<?php

namespace App\Providers;

use App\Database\Connectors\NeonPostgresConnector;
use Illuminate\Database\Connection;
use Illuminate\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
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
        // Reemplazar el conector pgsql con nuestro conector personalizado
        $this->app[\'db\']->extend(\'pgsql\', function ($config, $name) {
            $connector = new NeonPostgresConnector();
            $connection = $connector->connect($config);
            
            return new Connection(
                $connection,
                $config[\'database\'],
                $config[\'prefix\'] ?? \'\',
                $config
            );
        });
    }
}';

file_put_contents($providerPath, $providerCode);
echo "✅ Service provider creado: {$providerPath}\n";

// Registrar el service provider en config/app.php
echo "\n📝 REGISTRANDO SERVICE PROVIDER...\n";

$appConfigPath = 'config/app.php';
$appConfig = file_get_contents($appConfigPath);

// Buscar la sección de providers y agregar nuestro provider
if (strpos($appConfig, 'App\Providers\DatabaseServiceProvider::class') === false) {
    // Buscar donde están los otros providers de App
    $pattern = '/(App\\\\Providers\\\\RouteServiceProvider::class,)/';
    $replacement = '$1' . "\n        App\\Providers\\DatabaseServiceProvider::class,";
    
    $appConfig = preg_replace($pattern, $replacement, $appConfig);
    
    if (strpos($appConfig, 'App\Providers\DatabaseServiceProvider::class') !== false) {
        file_put_contents($appConfigPath, $appConfig);
        echo "✅ Service provider registrado en config/app.php\n";
    } else {
        echo "⚠️  No se pudo registrar automáticamente. Agregar manualmente:\n";
        echo "   App\\Providers\\DatabaseServiceProvider::class,\n";
    }
} else {
    echo "✅ Service provider ya está registrado\n";
}

// Actualizar .env para usar configuración simple
echo "\n📝 ACTUALIZANDO .ENV...\n";

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
DB_HOST=ep-calm-glitter-adgesoqd-pooler.c-2.us-east-1.aws.neon.tech
DB_PORT=5432
DB_DATABASE=neondb
DB_USERNAME=neondb_owner
DB_PASSWORD=npg_U0PA6dWCqayo
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

// Actualizar config/database.php
echo "\n📝 ACTUALIZANDO CONFIG/DATABASE.PHP...\n";

$databaseConfigContent = '<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    \'default\' => env(\'DB_CONNECTION\', \'sqlite\'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Below are all of the database connections defined for your application.
    | An example configuration is provided for each database system which
    | is supported by Laravel. You are free to add / remove connections.
    |
    */

    \'connections\' => [

        \'sqlite\' => [
            \'driver\' => \'sqlite\',
            \'url\' => env(\'DB_URL\'),
            \'database\' => env(\'DB_DATABASE\', database_path(\'database.sqlite\')),
            \'prefix\' => \'\',
            \'foreign_key_constraints\' => env(\'DB_FOREIGN_KEYS\', true),
            \'busy_timeout\' => null,
            \'journal_mode\' => null,
            \'synchronous\' => null,
        ],

        \'mysql\' => [
            \'driver\' => \'mysql\',
            \'url\' => env(\'DB_URL\'),
            \'host\' => env(\'DB_HOST\', \'127.0.0.1\'),
            \'port\' => env(\'DB_PORT\', \'3306\'),
            \'database\' => env(\'DB_DATABASE\', \'laravel\'),
            \'username\' => env(\'DB_USERNAME\', \'root\'),
            \'password\' => env(\'DB_PASSWORD\', \'\'),
            \'unix_socket\' => env(\'DB_SOCKET\', \'\'),
            \'charset\' => env(\'DB_CHARSET\', \'utf8mb4\'),
            \'collation\' => env(\'DB_COLLATION\', \'utf8mb4_unicode_ci\'),
            \'prefix\' => \'\',
            \'prefix_indexes\' => true,
            \'strict\' => true,
            \'engine\' => null,
            \'options\' => extension_loaded(\'pdo_mysql\') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env(\'MYSQL_ATTR_SSL_CA\'),
            ]) : [],
        ],

        \'mariadb\' => [
            \'driver\' => \'mariadb\',
            \'url\' => env(\'DB_URL\'),
            \'host\' => env(\'DB_HOST\', \'127.0.0.1\'),
            \'port\' => env(\'DB_PORT\', \'3306\'),
            \'database\' => env(\'DB_DATABASE\', \'laravel\'),
            \'username\' => env(\'DB_USERNAME\', \'root\'),
            \'password\' => env(\'DB_PASSWORD\', \'\'),
            \'unix_socket\' => env(\'DB_SOCKET\', \'\'),
            \'charset\' => env(\'DB_CHARSET\', \'utf8mb4\'),
            \'collation\' => env(\'DB_COLLATION\', \'utf8mb4_unicode_ci\'),
            \'prefix\' => \'\',
            \'prefix_indexes\' => true,
            \'strict\' => true,
            \'engine\' => null,
            \'options\' => extension_loaded(\'pdo_mysql\') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env(\'MYSQL_ATTR_SSL_CA\'),
            ]) : [],
        ],

        \'pgsql\' => [
            \'driver\' => \'pgsql\',
            \'host\' => env(\'DB_HOST\', \'127.0.0.1\'),
            \'port\' => env(\'DB_PORT\', \'5432\'),
            \'database\' => env(\'DB_DATABASE\', \'laravel\'),
            \'username\' => env(\'DB_USERNAME\', \'root\'),
            \'password\' => env(\'DB_PASSWORD\', \'\'),
            \'charset\' => env(\'DB_CHARSET\', \'utf8\'),
            \'prefix\' => \'\',
            \'prefix_indexes\' => true,
            \'search_path\' => \'public\',
            \'sslmode\' => env(\'DB_SSLMODE\', \'prefer\'),
        ],

        \'sqlsrv\' => [
            \'driver\' => \'sqlsrv\',
            \'url\' => env(\'DB_URL\'),
            \'host\' => env(\'DB_HOST\', \'localhost\'),
            \'port\' => env(\'DB_PORT\', \'1433\'),
            \'database\' => env(\'DB_DATABASE\', \'laravel\'),
            \'username\' => env(\'DB_USERNAME\', \'root\'),
            \'password\' => env(\'DB_PASSWORD\', \'\'),
            \'charset\' => env(\'DB_CHARSET\', \'utf8\'),
            \'prefix\' => \'\',
            \'prefix_indexes\' => true,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven\'t actually been run on the database.
    |
    */

    \'migrations\' => [
        \'table\' => \'migrations\',
        \'update_date_on_publish\' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. You may define your connection settings here.
    |
    */

    \'redis\' => [

        \'client\' => env(\'REDIS_CLIENT\', \'phpredis\'),

        \'options\' => [
            \'cluster\' => env(\'REDIS_CLUSTER\', \'redis\'),
            \'prefix\' => env(\'REDIS_PREFIX\', Str::slug(env(\'APP_NAME\', \'laravel\'), \'_\').\'_database_\'),
        ],

        \'default\' => [
            \'url\' => env(\'REDIS_URL\'),
            \'host\' => env(\'REDIS_HOST\', \'127.0.0.1\'),
            \'username\' => env(\'REDIS_USERNAME\'),
            \'password\' => env(\'REDIS_PASSWORD\'),
            \'port\' => env(\'REDIS_PORT\', \'6379\'),
            \'database\' => env(\'REDIS_DB\', \'0\'),
        ],

        \'cache\' => [
            \'url\' => env(\'REDIS_URL\'),
            \'host\' => env(\'REDIS_HOST\', \'127.0.0.1\'),
            \'username\' => env(\'REDIS_USERNAME\'),
            \'password\' => env(\'REDIS_PASSWORD\'),
            \'port\' => env(\'REDIS_PORT\', \'6379\'),
            \'database\' => env(\'REDIS_CACHE_DB\', \'1\'),
        ],

    ],

];
';

file_put_contents('config/database.php', $databaseConfigContent);
echo "✅ Config/database.php restaurado a configuración limpia\n";

echo "\n🎯 PASOS FINALES:\n";
echo "1. php artisan config:clear\n";
echo "2. php artisan migrate\n";

echo "\n=== CONFIGURACIÓN NEON COMPLETADA ===\n";
echo "El conector personalizado manejará automáticamente el endpoint de Neon.\n";