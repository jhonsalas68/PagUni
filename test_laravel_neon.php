<?php

require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== TEST LARAVEL CON NEON ===\n\n";

try {
    echo "🔌 PROBANDO CONEXIÓN LARAVEL...\n";
    
    $pdo = DB::connection()->getPdo();
    echo "✅ Conexión Laravel exitosa\n";
    
    echo "\n📊 PROBANDO QUERY CON LARAVEL...\n";
    
    $result = DB::select('SELECT version() as version');
    echo "✅ Query Laravel exitosa\n";
    echo "Versión PostgreSQL: " . $result[0]->version . "\n";
    
    echo "\n🏗️  INFORMACIÓN DEL ESQUEMA:\n";
    
    $schema = DB::select('SELECT current_schema() as schema');
    $user = DB::select('SELECT current_user as user');
    $database = DB::select('SELECT current_database() as database');
    
    echo "Esquema: " . $schema[0]->schema . "\n";
    echo "Usuario: " . $user[0]->user . "\n";
    echo "Base de datos: " . $database[0]->database . "\n";
    
    echo "\n📋 VERIFICANDO TABLA MIGRATIONS...\n";
    
    try {
        $exists = DB::select("SELECT EXISTS (
            SELECT 1 FROM pg_class c, pg_namespace n 
            WHERE n.nspname = current_schema() 
            AND c.relname = 'migrations' 
            AND c.relkind IN ('r', 'p') 
            AND n.oid = c.relnamespace
        )");
        
        $tableExists = $exists[0]->exists;
        echo $tableExists ? "✅ Tabla migrations existe\n" : "⚠️  Tabla migrations no existe (normal para primera migración)\n";
        
    } catch (\Exception $e) {
        echo "⚠️  Error al verificar migrations: " . $e->getMessage() . "\n";
    }
    
    echo "\n🎉 LARAVEL + NEON FUNCIONANDO CORRECTAMENTE\n";
    echo "✅ Listo para ejecutar: php artisan migrate\n";

} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
}

echo "\n=== FIN DEL TEST ===\n";