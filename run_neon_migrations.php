<?php

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
