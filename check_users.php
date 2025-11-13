<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "\n=== USUARIOS EN EL SISTEMA ===\n\n";

// Administradores
echo "ADMINISTRADORES:\n";
$admins = DB::table('administradores')->get(['codigo_admin', 'nombre', 'apellido']);
foreach ($admins as $user) {
    echo "  Usuario: {$user->codigo_admin} | Nombre: {$user->nombre} {$user->apellido}\n";
}

// Docentes
echo "\nDOCENTES (primeros 5):\n";
$docentes = DB::table('profesores')->limit(5)->get(['codigo_docente', 'nombre', 'apellido']);
foreach ($docentes as $user) {
    echo "  Usuario: {$user->codigo_docente} | Nombre: {$user->nombre} {$user->apellido}\n";
}

// Estudiantes
echo "\nESTUDIANTES (primeros 10):\n";
$estudiantes = DB::table('estudiantes')->limit(10)->get(['codigo_estudiante', 'nombre', 'apellido']);
foreach ($estudiantes as $user) {
    echo "  Usuario: {$user->codigo_estudiante} | Nombre: {$user->nombre} {$user->apellido}\n";
}

echo "\n=== CONTRASEÑA PARA TODOS (excepto admin): password ===\n";
echo "=== CONTRASEÑA PARA ADMIN: admin123 ===\n\n";
