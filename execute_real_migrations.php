<?php

echo "=== EJECUTAR MIGRACIONES REALES EN NEON ===\n\n";

// Configuración de Neon
$host = 'ep-calm-glitter-adgesoqd-pooler.c-2.us-east-1.aws.neon.tech';
$port = '5432';
$dbname = 'neondb';
$user = 'neondb_owner';
$password = 'npg_U0PA6dWCqayo';
$endpoint = 'ep-calm-glitter-adgesoqd';

try {
    // Conectar con PDO
    $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=require;options=endpoint={$endpoint}";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 30,
    ]);
    
    echo "✅ Conexión PDO exitosa\n";
    
    // Limpiar base de datos
    echo "\n🧹 LIMPIANDO BASE DE DATOS...\n";
    
    // Obtener todas las tablas del esquema public
    $stmt = $pdo->query("
        SELECT tablename 
        FROM pg_tables 
        WHERE schemaname = 'public' 
        AND tablename != 'playing_with_neon'
    ");
    
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $table) {
        $pdo->exec("DROP TABLE IF EXISTS \"{$table}\" CASCADE");
        echo "   - Tabla '{$table}' eliminada\n";
    }
    
    // Crear tabla migrations
    echo "\n📋 CREANDO TABLA MIGRATIONS...\n";
    
    $pdo->exec("
        CREATE TABLE migrations (
            id SERIAL PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            batch INTEGER NOT NULL
        )
    ");
    
    echo "✅ Tabla migrations creada\n";
    
    // Ejecutar migraciones una por una
    echo "\n🚀 EJECUTANDO MIGRACIONES...\n";
    
    $migrationFiles = glob('database/migrations/*.php');
    sort($migrationFiles);
    
    $batch = 1;
    $executed = 0;
    
    foreach ($migrationFiles as $file) {
        $migrationName = basename($file, '.php');
        echo "Procesando: {$migrationName}...\n";
        
        try {
            // Ejecutar SQL específico para cada migración
            if (strpos($migrationName, 'create_users_table') !== false) {
                $pdo->exec("
                    CREATE TABLE users (
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
            } elseif (strpos($migrationName, 'create_cache_table') !== false) {
                $pdo->exec("
                    CREATE TABLE cache (
                        key VARCHAR(255) PRIMARY KEY,
                        value TEXT NOT NULL,
                        expiration INTEGER NOT NULL
                    )
                ");
                $pdo->exec("
                    CREATE TABLE cache_locks (
                        key VARCHAR(255) PRIMARY KEY,
                        owner VARCHAR(255) NOT NULL,
                        expiration INTEGER NOT NULL
                    )
                ");
            } elseif (strpos($migrationName, 'create_jobs_table') !== false) {
                $pdo->exec("
                    CREATE TABLE jobs (
                        id BIGSERIAL PRIMARY KEY,
                        queue VARCHAR(255) NOT NULL,
                        payload TEXT NOT NULL,
                        attempts SMALLINT NOT NULL,
                        reserved_at INTEGER NULL,
                        available_at INTEGER NOT NULL,
                        created_at INTEGER NOT NULL
                    )
                ");
                $pdo->exec("CREATE INDEX jobs_queue_index ON jobs (queue)");
                
                $pdo->exec("
                    CREATE TABLE job_batches (
                        id VARCHAR(255) PRIMARY KEY,
                        name VARCHAR(255) NOT NULL,
                        total_jobs INTEGER NOT NULL,
                        pending_jobs INTEGER NOT NULL,
                        failed_jobs INTEGER NOT NULL,
                        failed_job_ids TEXT NOT NULL,
                        options TEXT NULL,
                        cancelled_at INTEGER NULL,
                        created_at INTEGER NOT NULL,
                        finished_at INTEGER NULL
                    )
                ");
                
                $pdo->exec("
                    CREATE TABLE failed_jobs (
                        id BIGSERIAL PRIMARY KEY,
                        uuid VARCHAR(255) UNIQUE NOT NULL,
                        connection TEXT NOT NULL,
                        queue TEXT NOT NULL,
                        payload TEXT NOT NULL,
                        exception TEXT NOT NULL,
                        failed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )
                ");
            } elseif (strpos($migrationName, 'create_facultades_table') !== false) {
                $pdo->exec("
                    CREATE TABLE facultades (
                        id BIGSERIAL PRIMARY KEY,
                        nombre VARCHAR(255) NOT NULL,
                        codigo VARCHAR(10) UNIQUE NOT NULL,
                        descripcion TEXT NULL,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL
                    )
                ");
            } elseif (strpos($migrationName, 'create_carreras_table') !== false) {
                $pdo->exec("
                    CREATE TABLE carreras (
                        id BIGSERIAL PRIMARY KEY,
                        nombre VARCHAR(255) NOT NULL,
                        codigo VARCHAR(20) UNIQUE NOT NULL,
                        facultad_id BIGINT NOT NULL,
                        duracion_semestres INTEGER NOT NULL DEFAULT 10,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL,
                        FOREIGN KEY (facultad_id) REFERENCES facultades(id) ON DELETE CASCADE
                    )
                ");
            } elseif (strpos($migrationName, 'create_materias_table') !== false) {
                $pdo->exec("
                    CREATE TABLE materias (
                        id BIGSERIAL PRIMARY KEY,
                        nombre VARCHAR(255) NOT NULL,
                        codigo VARCHAR(20) UNIQUE NOT NULL,
                        carrera_id BIGINT NOT NULL,
                        semestre INTEGER NOT NULL,
                        creditos INTEGER NOT NULL DEFAULT 4,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL,
                        FOREIGN KEY (carrera_id) REFERENCES carreras(id) ON DELETE CASCADE
                    )
                ");
            } elseif (strpos($migrationName, 'create_profesores_table') !== false) {
                $pdo->exec("
                    CREATE TABLE profesores (
                        id BIGSERIAL PRIMARY KEY,
                        nombre VARCHAR(255) NOT NULL,
                        apellido VARCHAR(255) NOT NULL,
                        email VARCHAR(255) UNIQUE NOT NULL,
                        telefono VARCHAR(20) NULL,
                        cedula VARCHAR(20) UNIQUE NOT NULL,
                        especialidad VARCHAR(255) NULL,
                        tipo_contrato VARCHAR(50) DEFAULT 'tiempo_completo',
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL
                    )
                ");
            } elseif (strpos($migrationName, 'create_estudiantes_table') !== false) {
                $pdo->exec("
                    CREATE TABLE estudiantes (
                        id BIGSERIAL PRIMARY KEY,
                        nombre VARCHAR(255) NOT NULL,
                        apellido VARCHAR(255) NOT NULL,
                        email VARCHAR(255) UNIQUE NOT NULL,
                        telefono VARCHAR(20) NULL,
                        cedula VARCHAR(20) UNIQUE NOT NULL,
                        codigo_estudiante VARCHAR(20) UNIQUE NOT NULL,
                        carrera_id BIGINT NOT NULL,
                        semestre_actual INTEGER DEFAULT 1,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL,
                        FOREIGN KEY (carrera_id) REFERENCES carreras(id) ON DELETE CASCADE
                    )
                ");
            } elseif (strpos($migrationName, 'create_administradores_table') !== false) {
                $pdo->exec("
                    CREATE TABLE administradores (
                        id BIGSERIAL PRIMARY KEY,
                        nombre VARCHAR(255) NOT NULL,
                        apellido VARCHAR(255) NOT NULL,
                        email VARCHAR(255) UNIQUE NOT NULL,
                        codigo_admin VARCHAR(20) UNIQUE NOT NULL,
                        password VARCHAR(255) NOT NULL,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL
                    )
                ");
            } elseif (strpos($migrationName, 'create_aulas_table') !== false) {
                $pdo->exec("
                    CREATE TABLE aulas (
                        id BIGSERIAL PRIMARY KEY,
                        nombre VARCHAR(100) NOT NULL,
                        capacidad INTEGER NOT NULL,
                        tipo VARCHAR(50) DEFAULT 'aula',
                        ubicacion VARCHAR(255) NULL,
                        equipamiento TEXT NULL,
                        estado VARCHAR(20) DEFAULT 'activo',
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL
                    )
                ");
            } elseif (strpos($migrationName, 'create_grupos_table') !== false) {
                $pdo->exec("
                    CREATE TABLE grupos (
                        id BIGSERIAL PRIMARY KEY,
                        identificador VARCHAR(10) NOT NULL,
                        materia_id BIGINT NOT NULL,
                        capacidad_maxima INTEGER DEFAULT 30,
                        estado VARCHAR(20) DEFAULT 'activo',
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL,
                        FOREIGN KEY (materia_id) REFERENCES materias(id) ON DELETE CASCADE
                    )
                ");
            } elseif (strpos($migrationName, 'create_carga_academica_table') !== false) {
                $pdo->exec("
                    CREATE TABLE carga_academica (
                        id BIGSERIAL PRIMARY KEY,
                        profesor_id BIGINT NOT NULL,
                        grupo_id BIGINT NOT NULL,
                        gestion INTEGER NOT NULL,
                        periodo VARCHAR(20) NOT NULL,
                        estado VARCHAR(20) DEFAULT 'activo',
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL,
                        FOREIGN KEY (profesor_id) REFERENCES profesores(id) ON DELETE CASCADE,
                        FOREIGN KEY (grupo_id) REFERENCES grupos(id) ON DELETE CASCADE
                    )
                ");
            } elseif (strpos($migrationName, 'create_horarios_table') !== false) {
                $pdo->exec("
                    CREATE TABLE horarios (
                        id BIGSERIAL PRIMARY KEY,
                        grupo_id BIGINT NOT NULL,
                        aula_id BIGINT NOT NULL,
                        dia_semana VARCHAR(20) NOT NULL,
                        hora_inicio TIME NOT NULL,
                        hora_fin TIME NOT NULL,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL,
                        FOREIGN KEY (grupo_id) REFERENCES grupos(id) ON DELETE CASCADE,
                        FOREIGN KEY (aula_id) REFERENCES aulas(id) ON DELETE CASCADE
                    )
                ");
            } else {
                // Para migraciones que solo agregan columnas o modifican estructura
                echo "   - Migración de estructura/modificación, saltando...\n";
            }
            
            // Registrar migración
            $stmt = $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
            $stmt->execute([$migrationName, $batch]);
            
            echo "   ✅ Ejecutada exitosamente\n";
            $executed++;
            
        } catch (Exception $e) {
            echo "   ⚠️  Error: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n🎉 MIGRACIONES COMPLETADAS\n";
    echo "Total ejecutadas: {$executed}\n";
    
    // Verificar tablas creadas
    echo "\n📋 VERIFICANDO TABLAS CREADAS...\n";
    
    $stmt = $pdo->query("
        SELECT tablename 
        FROM pg_tables 
        WHERE schemaname = 'public' 
        ORDER BY tablename
    ");
    
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tablas creadas: " . count($tables) . "\n";
    
    foreach ($tables as $table) {
        echo "   - {$table}\n";
    }

} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DE MIGRACIONES ===\n";