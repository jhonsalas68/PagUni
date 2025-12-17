<?php

require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== DEBUG TABLAS EN NEON ===\n\n";

try {
    echo "🔍 LISTANDO TODAS LAS TABLAS...\n";
    
    $tables = DB::select("
        SELECT schemaname, tablename 
        FROM pg_tables 
        ORDER BY schemaname, tablename
    ");
    
    echo "Total de tablas encontradas: " . count($tables) . "\n\n";
    
    foreach ($tables as $table) {
        echo "- {$table->schemaname}.{$table->tablename}\n";
    }
    
    echo "\n🔍 VERIFICANDO ESQUEMA ACTUAL...\n";
    
    $currentSchema = DB::select('SELECT current_schema() as schema');
    echo "Esquema actual: " . $currentSchema[0]->schema . "\n";
    
    $currentDatabase = DB::select('SELECT current_database() as database');
    echo "Base de datos actual: " . $currentDatabase[0]->database . "\n";
    
    $currentUser = DB::select('SELECT current_user as user');
    echo "Usuario actual: " . $currentUser[0]->user . "\n";
    
    echo "\n🔍 VERIFICANDO MIGRACIONES...\n";
    
    $migrations = DB::table('migrations')->orderBy('id')->get();
    echo "Migraciones registradas: " . $migrations->count() . "\n";
    
    if ($migrations->count() > 0) {
        echo "Últimas 5 migraciones:\n";
        $lastMigrations = $migrations->take(-5);
        foreach ($lastMigrations as $migration) {
            echo "   - {$migration->migration} (batch: {$migration->batch})\n";
        }
    }
    
    echo "\n🔍 PROBANDO CONEXIÓN DIRECTA PDO...\n";
    
    $host = 'ep-calm-glitter-adgesoqd-pooler.c-2.us-east-1.aws.neon.tech';
    $port = '5432';
    $dbname = 'neondb';
    $user = 'neondb_owner';
    $password = 'npg_U0PA6dWCqayo';
    $endpoint = 'ep-calm-glitter-adgesoqd';
    
    $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=require;options=endpoint={$endpoint}";
    
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    
    $stmt = $pdo->query("
        SELECT schemaname, tablename 
        FROM pg_tables 
        WHERE schemaname = 'public'
        ORDER BY tablename
    ");
    
    $pdoTables = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    echo "Tablas vía PDO directo: " . count($pdoTables) . "\n";
    foreach ($pdoTables as $table) {
        echo "   - {$table->tablename}\n";
    }
    
    echo "\n🔍 COMPARANDO CONEXIONES...\n";
    
    $laravelCount = count($tables);
    $pdoCount = count($pdoTables);
    
    if ($laravelCount === $pdoCount) {
        echo "✅ Ambas conexiones ven las mismas tablas\n";
    } else {
        echo "⚠️  Diferencia detectada:\n";
        echo "   - Laravel ve: {$laravelCount} tablas\n";
        echo "   - PDO directo ve: {$pdoCount} tablas\n";
    }

} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
}

echo "\n=== FIN DEL DEBUG ===\n";