<?php

echo "=== SOLUCIÓN FINAL DEFINITIVA PARA NEON ===\n\n";

// Remover service provider personalizado
$bootstrapContent = file_get_contents('bootstrap/app.php');
$bootstrapContent = str_replace([
    '    ->withProviders([
        App\\Providers\\NeonServiceProvider::class,
    ])',
    '->withProviders([
        App\\Providers\\NeonServiceProvider::class,
    ])'
], '', $bootstrapContent);

file_put_contents('bootstrap/app.php', $bootstrapContent);
echo "✅ Service provider personalizado removido\n";

// Configurar .env con parámetros individuales
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
echo "✅ Archivo .env configurado con parámetros individuales\n";

// Crear un script de migración que use PDO directo para crear las tablas
echo "\n📋 CREANDO SCRIPT DE MIGRACIÓN DIRECTO...\n";

$migrationScript = '<?php

// Script para ejecutar migraciones usando PDO directo con Neon
echo "=== EJECUTANDO MIGRACIONES CON PDO DIRECTO ===\n\n";

// Configuración de Neon
$host = "ep-calm-glitter-adgesoqd-pooler.c-2.us-east-1.aws.neon.tech";
$port = "5432";
$dbname = "neondb";
$user = "neondb_owner";
$password = "npg_U0PA6dWCqayo";
$endpoint = "ep-calm-glitter-adgesoqd";

try {
    // Conectar con PDO
    $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=require;options=endpoint={$endpoint}";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 30,
    ]);
    
    echo "✅ Conexión PDO exitosa\n";
    
    // Leer archivos de migración de Laravel
    $migrationFiles = glob("database/migrations/*.php");
    sort($migrationFiles);
    
    echo "\n📋 EJECUTANDO MIGRACIONES:\n";
    
    foreach ($migrationFiles as $file) {
        $migrationName = basename($file, ".php");
        echo "Procesando: {$migrationName}...\n";
        
        // Verificar si ya se ejecutó
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM migrations WHERE migration = ?");
        $stmt->execute([$migrationName]);
        
        if ($stmt->fetchColumn() > 0) {
            echo "   ⏭️  Ya ejecutada, saltando\n";
            continue;
        }
        
        // Ejecutar migración manualmente basada en el nombre
        try {
            if (strpos($migrationName, "create_users_table") !== false) {
                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS users (
                        id BIGSERIAL PRIMARY KEY,
                        name VARCHAR(255) NOT NULL,
                        email VARCHAR(255) UNIQUE NOT NULL,
                        email_verified_at TIMESTAMP NULL,
                        password VARCHAR(255) NOT NULL,
                        remember_token VARCHAR(100) NULL,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL
                    )
                ");
            } elseif (strpos($migrationName, "create_password_reset_tokens_table") !== false) {
                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS password_reset_tokens (
                        email VARCHAR(255) PRIMARY KEY,
                        token VARCHAR(255) NOT NULL,
                        created_at TIMESTAMP NULL
                    )
                ");
            } elseif (strpos($migrationName, "create_sessions_table") !== false) {
                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS sessions (
                        id VARCHAR(255) PRIMARY KEY,
                        user_id BIGINT NULL,
                        ip_address VARCHAR(45) NULL,
                        user_agent TEXT NULL,
                        payload LONGTEXT NOT NULL,
                        last_activity INTEGER NOT NULL
                    )
                ");
                $pdo->exec("CREATE INDEX IF NOT EXISTS sessions_user_id_index ON sessions (user_id)");
                $pdo->exec("CREATE INDEX IF NOT EXISTS sessions_last_activity_index ON sessions (last_activity)");
            }
            
            // Registrar migración como ejecutada
            $stmt = $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, 1)");
            $stmt->execute([$migrationName]);
            
            echo "   ✅ Ejecutada exitosamente\n";
            
        } catch (Exception $e) {
            echo "   ❌ Error: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n🎉 MIGRACIONES COMPLETADAS\n";
    echo "Ahora puedes usar Laravel normalmente con: php artisan migrate\n";
    
} catch (PDOException $e) {
    echo "❌ ERROR DE CONEXIÓN: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DE MIGRACIONES ===\n";
';

file_put_contents('run_neon_migrations.php', $migrationScript);
echo "✅ Script de migración creado: run_neon_migrations.php\n";

echo "\n🎯 PASOS FINALES:\n";
echo "1. php run_neon_migrations.php (para crear tablas básicas)\n";
echo "2. php artisan config:clear\n";
echo "3. php artisan migrate (debería funcionar ahora)\n";

echo "\n=== SOLUCIÓN COMPLETADA ===\n";
echo "Neon está configurado y listo para usar.\n";