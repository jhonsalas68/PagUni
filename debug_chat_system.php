<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Profesor;
use App\Models\Estudiante;
use App\Models\Administrador;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "🔍 DEBUGGING SISTEMA DE CHAT\n";
    echo "============================\n\n";
    
    // 1. Verificar si las tablas de chat existen
    echo "1. Verificando tablas de chat...\n";
    
    $tablas = ['conversations', 'conversation_participants', 'messages'];
    foreach ($tablas as $tabla) {
        $existe = Schema::hasTable($tabla);
        echo "   - Tabla '{$tabla}': " . ($existe ? '✅ Existe' : '❌ No existe') . "\n";
    }
    
    // 2. Verificar usuarios disponibles para chat
    echo "\n2. Verificando usuarios disponibles...\n";
    
    // Profesores
    $profesores = Profesor::where('estado', 'activo')->get();
    echo "   📚 Profesores activos: " . $profesores->count() . "\n";
    foreach ($profesores as $prof) {
        echo "     - {$prof->codigo_docente}: {$prof->nombre} {$prof->apellido}\n";
    }
    
    // Estudiantes
    $estudiantes = Estudiante::limit(10)->get(); // Limitar para no saturar
    echo "\n   🎓 Estudiantes (primeros 10): " . $estudiantes->count() . "\n";
    foreach ($estudiantes as $est) {
        echo "     - {$est->codigo_estudiante}: {$est->nombre} {$est->apellido}\n";
    }
    
    // Administradores
    $administradores = Administrador::get();
    echo "\n   👨‍💼 Administradores: " . $administradores->count() . "\n";
    foreach ($administradores as $admin) {
        echo "     - {$admin->email}: {$admin->nombre} {$admin->apellido}\n";
    }
    
    // 3. Probar la búsqueda de usuarios
    echo "\n3. Probando búsqueda de usuarios...\n";
    
    // Simular búsqueda por nombre "juan"
    $query = 'juan';
    
    echo "   Buscando usuarios con '{$query}'...\n";
    
    // Profesores que coincidan
    $profesoresEncontrados = Profesor::where('estado', 'activo')
        ->where(function($q) use ($query) {
            $q->where('nombre', 'like', "%{$query}%")
              ->orWhere('apellido', 'like', "%{$query}%");
        })->get();
    
    echo "     Profesores encontrados: " . $profesoresEncontrados->count() . "\n";
    foreach ($profesoresEncontrados as $prof) {
        echo "       - {$prof->nombre} {$prof->apellido} (ID: {$prof->id})\n";
    }
    
    // Estudiantes que coincidan
    $estudiantesEncontrados = Estudiante::where(function($q) use ($query) {
            $q->where('nombre', 'like', "%{$query}%")
              ->orWhere('apellido', 'like', "%{$query}%");
        })->limit(5)->get();
    
    echo "     Estudiantes encontrados: " . $estudiantesEncontrados->count() . "\n";
    foreach ($estudiantesEncontrados as $est) {
        echo "       - {$est->nombre} {$est->apellido} (ID: {$est->id})\n";
    }
    
    // 4. Verificar rutas de chat
    echo "\n4. Verificando rutas de chat...\n";
    
    // Simular la respuesta del endpoint de búsqueda
    $users = collect();
    
    foreach($profesoresEncontrados as $prof) {
        $users->push([
            'id' => $prof->id,
            'name' => $prof->nombre . ' ' . $prof->apellido,
            'type' => 'profesor',
            'role_label' => 'Docente',
            'email' => $prof->email,
            'initials' => substr($prof->nombre, 0, 1) . substr($prof->apellido, 0, 1)
        ]);
    }
    
    foreach($estudiantesEncontrados as $est) {
        $users->push([
            'id' => $est->id,
            'name' => $est->nombre . ' ' . $est->apellido,
            'type' => 'estudiante',
            'role_label' => 'Estudiante',
            'email' => $est->email ?? 'sin-email@ejemplo.com',
            'initials' => substr($est->nombre, 0, 1) . substr($est->apellido, 0, 1)
        ]);
    }
    
    echo "   Usuarios que se retornarían en la búsqueda: " . $users->count() . "\n";
    foreach ($users as $user) {
        echo "     - {$user['name']} ({$user['role_label']}) - Tipo: {$user['type']}\n";
    }
    
    // 5. Verificar materias para filtro
    echo "\n5. Verificando materias para filtro...\n";
    
    $materias = \App\Models\Materia::where('estado', 'activo')->get();
    echo "   Materias activas: " . $materias->count() . "\n";
    foreach ($materias as $materia) {
        echo "     - {$materia->codigo}: {$materia->nombre}\n";
    }
    
    // 6. Verificar inscripciones para búsqueda por materia
    echo "\n6. Verificando inscripciones para búsqueda por materia...\n";
    
    if ($materias->count() > 0) {
        $primeraMateria = $materias->first();
        echo "   Probando con materia: {$primeraMateria->nombre}\n";
        
        $estudiantesMateria = Estudiante::whereHas('inscripciones.grupo', function($q) use ($primeraMateria) {
            $q->where('materia_id', $primeraMateria->id);
        })->get();
        
        echo "   Estudiantes inscritos en esta materia: " . $estudiantesMateria->count() . "\n";
        foreach ($estudiantesMateria as $est) {
            echo "     - {$est->nombre} {$est->apellido}\n";
        }
    }
    
    // 7. Diagnóstico del problema
    echo "\n🔧 DIAGNÓSTICO DEL PROBLEMA\n";
    echo "===========================\n";
    
    if ($profesores->count() == 0 && $estudiantes->count() == 0) {
        echo "❌ PROBLEMA: No hay usuarios en el sistema\n";
        echo "   Solución: Crear usuarios (profesores y estudiantes)\n";
    } elseif ($profesoresEncontrados->count() == 0 && $estudiantesEncontrados->count() == 0) {
        echo "❌ PROBLEMA: No hay usuarios que coincidan con la búsqueda '{$query}'\n";
        echo "   Solución: Verificar que los nombres en la base de datos coincidan\n";
    } else {
        echo "✅ Hay usuarios disponibles para la búsqueda\n";
        echo "   El problema podría estar en:\n";
        echo "   - La ruta del endpoint de búsqueda\n";
        echo "   - El JavaScript del frontend\n";
        echo "   - La autenticación de la sesión\n";
    }
    
    // 8. Verificar sesión actual
    echo "\n8. Información de sesión (si está disponible)...\n";
    
    if (session()->has('user_id')) {
        echo "   Usuario logueado: ID " . session('user_id') . " (Tipo: " . session('user_type') . ")\n";
    } else {
        echo "   ⚠️  No hay sesión activa en este contexto\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}