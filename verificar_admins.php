<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n📋 ADMINISTRADORES EN EL SISTEMA:\n";
echo "================================\n\n";

$admins = DB::table('administradores')
    ->select('codigo_admin', 'nombre', 'apellido', 'email', 'nivel_acceso')
    ->orderBy('codigo_admin')
    ->get();

if ($admins->isEmpty()) {
    echo "❌ No hay administradores en el sistema.\n";
    echo "Ejecuta: php artisan db:seed --class=AdminSeeder\n\n";
} else {
    foreach ($admins as $admin) {
        echo "👤 {$admin->nombre} {$admin->apellido}\n";
        echo "   Email: {$admin->email}\n";
        echo "   Código: {$admin->codigo_admin}\n";
        echo "   Nivel: {$admin->nivel_acceso}\n";
        echo "   --------------------------------\n";
    }
    
    echo "\n✅ Total de administradores: " . $admins->count() . "\n\n";
    
    echo "🔐 CREDENCIALES EXISTENTES:\n";
    echo "================================\n\n";
    
    // Buscar admin principal
    $mainAdmin = DB::table('administradores')
        ->where('email', 'admin@universidad.edu')
        ->orWhere('email', 'admin@uagrm.edu.bo')
        ->first();
    
    if ($mainAdmin) {
        echo "Email: {$mainAdmin->email}\n";
        echo "Password: admin123 (si es del seeder original)\n";
        echo "         o Admin2024! (si es del nuevo seeder)\n\n";
    }
    
    // Buscar admin académico
    $acadAdmin = DB::table('administradores')
        ->where('email', 'academico@universidad.edu')
        ->orWhere('email', 'academico@uagrm.edu.bo')
        ->first();
    
    if ($acadAdmin) {
        echo "Email: {$acadAdmin->email}\n";
        echo "Password: admin123 (si es del seeder original)\n";
        echo "         o Academico2024! (si es del nuevo seeder)\n\n";
    }
}

echo "🌐 URL de acceso: http://localhost/login\n\n";
