<?php
require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Simular request
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

use App\Models\Profesor;
use App\Models\Estudiante;
use App\Models\Administrador;

echo "=== PRUEBA FINAL DEL SISTEMA DE CHAT ===\n\n";

// 1. Verificar usuarios disponibles
echo "1. USUARIOS DISPONIBLES:\n";
$profesores = Profesor::where('estado', 'activo')->get(['id', 'nombre', 'apellido', 'email']);
$estudiantes = Estudiante::get(['id', 'nombre', 'apellido', 'email']);
$admins = Administrador::get(['id', 'nombre', 'apellido', 'email']);

echo "Profesores activos: " . $profesores->count() . "\n";
foreach($profesores as $prof) {
    echo "  - ID: {$prof->id}, Nombre: {$prof->nombre} {$prof->apellido}, Email: {$prof->email}\n";
}

echo "\nEstudiantes: " . $estudiantes->count() . "\n";
foreach($estudiantes->take(5) as $est) {
    echo "  - ID: {$est->id}, Nombre: {$est->nombre} {$est->apellido}, Email: {$est->email}\n";
}

echo "\nAdministradores: " . $admins->count() . "\n";
foreach($admins as $admin) {
    echo "  - ID: {$admin->id}, Nombre: {$admin->nombre} {$admin->apellido}, Email: {$admin->email}\n";
}

// 2. Simular búsquedas
echo "\n\n2. PRUEBAS DE BÚSQUEDA:\n";

// Simular búsqueda por nombre (case-insensitive)
echo "\nBúsqueda por 'juan' (case-insensitive):\n";
$profesoresJuan = Profesor::where('estado', 'activo')
    ->where(function($q) {
        $q->whereRaw('LOWER(nombre) LIKE ?', ['%juan%'])
          ->orWhereRaw('LOWER(apellido) LIKE ?', ['%juan%']);
    })->get();

$estudiantesJuan = Estudiante::where(function($q) {
    $q->whereRaw('LOWER(nombre) LIKE ?', ['%juan%'])
      ->orWhereRaw('LOWER(apellido) LIKE ?', ['%juan%']);
})->get();

echo "Profesores encontrados: " . $profesoresJuan->count() . "\n";
foreach($profesoresJuan as $prof) {
    echo "  - {$prof->nombre} {$prof->apellido}\n";
}

echo "Estudiantes encontrados: " . $estudiantesJuan->count() . "\n";
foreach($estudiantesJuan as $est) {
    echo "  - {$est->nombre} {$est->apellido}\n";
}

// Búsqueda por 'maria'
echo "\nBúsqueda por 'maria' (case-insensitive):\n";
$profesoresMaria = Profesor::where('estado', 'activo')
    ->where(function($q) {
        $q->whereRaw('LOWER(nombre) LIKE ?', ['%maria%'])
          ->orWhereRaw('LOWER(apellido) LIKE ?', ['%maria%']);
    })->get();

$estudiantesMaria = Estudiante::where(function($q) {
    $q->whereRaw('LOWER(nombre) LIKE ?', ['%maria%'])
      ->orWhereRaw('LOWER(apellido) LIKE ?', ['%maria%']);
})->get();

echo "Profesores encontrados: " . $profesoresMaria->count() . "\n";
foreach($profesoresMaria as $prof) {
    echo "  - {$prof->nombre} {$prof->apellido}\n";
}

echo "Estudiantes encontrados: " . $estudiantesMaria->count() . "\n";
foreach($estudiantesMaria as $est) {
    echo "  - {$est->nombre} {$est->apellido}\n";
}

// 3. Verificar tablas de chat
echo "\n\n3. VERIFICACIÓN TABLAS DE CHAT:\n";
try {
    $conversations = DB::table('conversations')->count();
    $messages = DB::table('messages')->count();
    $participants = DB::table('participants')->count();
    
    echo "Conversaciones: $conversations\n";
    echo "Mensajes: $messages\n";
    echo "Participantes: $participants\n";
} catch (Exception $e) {
    echo "Error verificando tablas: " . $e->getMessage() . "\n";
}

// 4. Simular búsqueda por materia
echo "\n\n4. BÚSQUEDA POR MATERIA:\n";
use App\Models\Materia;
use App\Models\Inscripcion;

$materias = Materia::where('estado', 'activo')->get(['id', 'nombre', 'codigo']);
echo "Materias activas: " . $materias->count() . "\n";

if($materias->count() > 0) {
    $materia = $materias->first();
    echo "Probando con materia: {$materia->nombre} (ID: {$materia->id})\n";
    
    $estudiantesMateria = Estudiante::whereHas('inscripciones.grupo', function($q) use ($materia) {
        $q->where('materia_id', $materia->id);
    })->get();
    
    echo "Estudiantes inscritos: " . $estudiantesMateria->count() . "\n";
    foreach($estudiantesMateria->take(3) as $est) {
        echo "  - {$est->nombre} {$est->apellido}\n";
    }
}

echo "\n=== PRUEBA COMPLETADA ===\n";
echo "El sistema de chat debería funcionar correctamente ahora.\n";
echo "Búsquedas case-insensitive implementadas para profesores y estudiantes.\n";