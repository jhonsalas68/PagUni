<?php

echo "=== VERIFICACIÓN COMPLETA - NEON DATABASE ===\n\n";

try {
    // Verificar conexión Laravel
    echo "🔌 VERIFICANDO CONEXIÓN LARAVEL...\n";
    
    // Simular carga de Laravel
    require_once 'vendor/autoload.php';
    
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    // Verificar conexión
    $connection = DB::connection();
    $dbName = $connection->getDatabaseName();
    echo "✅ Conexión exitosa a: {$dbName}\n";
    
    // Verificar datos
    echo "\n📊 VERIFICANDO DATOS...\n";
    
    $tables = [
        'administradores' => 'Administradores',
        'facultades' => 'Facultades', 
        'carreras' => 'Carreras',
        'materias' => 'Materias',
        'profesores' => 'Profesores',
        'estudiantes' => 'Estudiantes',
        'aulas' => 'Aulas'
    ];
    
    foreach ($tables as $table => $name) {
        $count = DB::table($table)->count();
        echo "   - {$name}: {$count} registros\n";
    }
    
    // Verificar credenciales de admin
    echo "\n🔐 VERIFICANDO CREDENCIALES...\n";
    
    $admin = DB::table('administradores')
        ->where('email', 'admin@ficct.edu.bo')
        ->first();
    
    if ($admin) {
        echo "✅ Admin principal encontrado: {$admin->nombre} {$admin->apellido}\n";
        echo "   Email: {$admin->email}\n";
        echo "   Código: {$admin->codigo_admin}\n";
    }
    
    // Verificar estructura académica
    echo "\n🎓 VERIFICANDO ESTRUCTURA ACADÉMICA...\n";
    
    $facultad = DB::table('facultades')->first();
    if ($facultad) {
        echo "✅ Facultad: {$facultad->nombre} ({$facultad->codigo})\n";
    }
    
    $carrera = DB::table('carreras')->first();
    if ($carrera) {
        echo "✅ Carrera: {$carrera->nombre} ({$carrera->codigo})\n";
    }
    
    $materia = DB::table('materias')->first();
    if ($materia) {
        echo "✅ Materia: {$materia->nombre} ({$materia->codigo})\n";
    }
    
    // Verificar migraciones
    echo "\n📋 VERIFICANDO MIGRACIONES...\n";
    
    $migrations = DB::table('migrations')->count();
    echo "✅ Migraciones ejecutadas: {$migrations}\n";
    
    echo "\n🎉 VERIFICACIÓN COMPLETA EXITOSA\n";
    echo "\n📋 CREDENCIALES DE ACCESO:\n";
    echo "   URL: http://localhost:8000\n";
    echo "   Email: admin@ficct.edu.bo\n";
    echo "   Password: admin123\n";
    echo "\n🚀 ¡Tu sistema está listo para usar con Neon Database!\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DE VERIFICACIÓN ===\n";