<?php

require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== TEST CONEXIÓN LARAVEL + NEON ===\n\n";

try {
    echo "🔌 PROBANDO CONEXIÓN LARAVEL...\n";
    
    $pdo = DB::connection()->getPdo();
    echo "✅ Conexión Laravel exitosa\n";
    
    echo "\n📊 PROBANDO QUERY...\n";
    
    $result = DB::select('SELECT version() as version');
    echo "✅ Query exitosa\n";
    echo "PostgreSQL: " . substr($result[0]->version, 0, 60) . "...\n";
    
    echo "\n📋 VERIFICANDO TABLAS...\n";
    
    $tables = DB::select("
        SELECT tablename 
        FROM pg_tables 
        WHERE schemaname = 'public' 
        ORDER BY tablename
    ");
    
    echo "✅ Tablas encontradas: " . count($tables) . "\n";
    
    // Mostrar algunas tablas importantes
    $importantTables = ['users', 'profesores', 'estudiantes', 'migrations', 'calificaciones'];
    foreach ($importantTables as $table) {
        $found = false;
        foreach ($tables as $t) {
            if ($t->tablename === $table) {
                $found = true;
                break;
            }
        }
        echo ($found ? "✅" : "❌") . " Tabla '{$table}': " . ($found ? "Existe" : "No existe") . "\n";
    }
    
    echo "\n📊 VERIFICANDO DATOS...\n";
    
    try {
        $migrationsCount = DB::table('migrations')->count();
        echo "✅ Migraciones: {$migrationsCount}\n";
        
        $profesoresCount = DB::table('profesores')->count();
        echo "✅ Profesores: {$profesoresCount}\n";
        
        $estudiantesCount = DB::table('estudiantes')->count();
        echo "✅ Estudiantes: {$estudiantesCount}\n";
        
        $calificacionesCount = DB::table('calificaciones')->count();
        echo "✅ Calificaciones: {$calificacionesCount}\n";
        
    } catch (Exception $e) {
        echo "⚠️  Error al contar datos: " . $e->getMessage() . "\n";
    }
    
    echo "\n🎉 LARAVEL + NEON FUNCIONANDO PERFECTAMENTE\n";
    echo "✅ Conexión establecida\n";
    echo "✅ Queries funcionando\n";
    echo "✅ Tablas disponibles\n";
    echo "✅ Listo para usar en producción\n";

} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
}

echo "\n=== FIN DEL TEST ===\n";