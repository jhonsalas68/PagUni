<?php

require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TEST CON PATCH APLICADO ===\n\n";

// Aplicar patch
require_once 'patch_database_manager.php';

use Illuminate\Support\Facades\DB;

try {
    echo "🔌 PROBANDO CONEXIÓN CON PATCH...\n";
    
    $pdo = DB::connection()->getPdo();
    echo "✅ Conexión Laravel exitosa\n";
    
    $result = DB::select('SELECT version() as version');
    echo "✅ Query exitosa: " . substr($result[0]->version, 0, 50) . "...\n";
    
    $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public' ORDER BY tablename");
    echo "✅ Tablas encontradas: " . count($tables) . "\n";
    
    foreach ($tables as $table) {
        echo "   - {$table->tablename}\n";
    }
    
    echo "\n🎉 LARAVEL + NEON FUNCIONANDO CON PATCH\n";

} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DEL TEST ===\n";
