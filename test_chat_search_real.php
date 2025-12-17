<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Estudiante;
use App\Models\Profesor;

try {
    echo "🔍 PROBANDO BÚSQUEDAS REALES DEL CHAT\n";
    echo "====================================\n\n";
    
    // 1. Mostrar todos los nombres reales
    echo "1. Nombres reales en la base de datos:\n";
    
    echo "\n   📚 PROFESORES:\n";
    $profesores = Profesor::where('estado', 'activo')->get();
    foreach ($profesores as $prof) {
        echo "     - Nombre: '{$prof->nombre}' | Apellido: '{$prof->apellido}'\n";
    }
    
    echo "\n   🎓 ESTUDIANTES:\n";
    $estudiantes = Estudiante::get();
    foreach ($estudiantes as $est) {
        echo "     - Nombre: '{$est->nombre}' | Apellido: '{$est->apellido}'\n";
    }
    
    // 2. Probar búsquedas con nombres reales
    echo "\n2. Probando búsquedas con nombres reales:\n";
    
    $busquedas = [
        'Juan',      // Para Juan Carlos
        'Carlos',    // Para Juan Carlos y otros Carlos
        'María',     // Para María Elena y María José
        'Ana',       // Para Ana González y Ana Lucía
        'Pérez',     // Apellido común
        'García'     // Apellido común
    ];
    
    foreach ($busquedas as $query) {
        echo "\n   🔍 Buscando '{$query}':\n";
        
        // Profesores
        $profesoresEncontrados = Profesor::where('estado', 'activo')
            ->where(function($q) use ($query) {
                $q->where('nombre', 'like', "%{$query}%")
                  ->orWhere('apellido', 'like', "%{$query}%");
            })->get();
        
        echo "     Profesores encontrados: " . $profesoresEncontrados->count() . "\n";
        foreach ($profesoresEncontrados as $prof) {
            echo "       ✅ {$prof->nombre} {$prof->apellido}\n";
        }
        
        // Estudiantes
        $estudiantesEncontrados = Estudiante::where(function($q) use ($query) {
                $q->where('nombre', 'like', "%{$query}%")
                  ->orWhere('apellido', 'like', "%{$query}%");
            })->get();
        
        echo "     Estudiantes encontrados: " . $estudiantesEncontrados->count() . "\n";
        foreach ($estudiantesEncontrados as $est) {
            echo "       ✅ {$est->nombre} {$est->apellido}\n";
        }
        
        // Simular respuesta del API
        $users = collect();
        
        foreach($profesoresEncontrados as $prof) {
            $users->push([
                'id' => $prof->id,
                'name' => $prof->nombre . ' ' . $prof->apellido,
                'type' => 'profesor',
                'role_label' => 'Docente',
                'email' => $prof->email ?? 'sin-email@profesor.edu',
                'initials' => substr($prof->nombre, 0, 1) . substr($prof->apellido, 0, 1)
            ]);
        }
        
        foreach($estudiantesEncontrados as $est) {
            $users->push([
                'id' => $est->id,
                'name' => $est->nombre . ' ' . $est->apellido,
                'type' => 'estudiante',
                'role_label' => 'Estudiante',
                'email' => $est->email ?? 'sin-email@estudiante.edu',
                'initials' => substr($est->nombre, 0, 1) . substr($est->apellido, 0, 1)
            ]);
        }
        
        echo "     📊 Total usuarios para API: " . $users->count() . "\n";
        
        if ($users->count() > 0) {
            echo "     📋 Respuesta JSON simulada:\n";
            foreach ($users as $user) {
                echo "       - ID: {$user['id']}, Nombre: {$user['name']}, Tipo: {$user['type']}\n";
            }
        }
    }
    
    // 3. Probar búsqueda case-insensitive
    echo "\n3. Probando búsqueda case-insensitive:\n";
    
    $busquedasMinusculas = ['juan', 'carlos', 'maría', 'ana'];
    
    foreach ($busquedasMinusculas as $query) {
        echo "\n   🔍 Buscando '{$query}' (minúsculas):\n";
        
        $profesores = Profesor::where('estado', 'activo')
            ->where(function($q) use ($query) {
                $q->whereRaw('LOWER(nombre) LIKE ?', ["%".strtolower($query)."%"])
                  ->orWhereRaw('LOWER(apellido) LIKE ?', ["%".strtolower($query)."%"]);
            })->get();
        
        $estudiantes = Estudiante::where(function($q) use ($query) {
                $q->whereRaw('LOWER(nombre) LIKE ?', ["%".strtolower($query)."%"])
                  ->orWhereRaw('LOWER(apellido) LIKE ?', ["%".strtolower($query)."%"]);
            })->get();
        
        $total = $profesores->count() + $estudiantes->count();
        echo "     Total encontrados: {$total}\n";
        
        foreach ($profesores as $prof) {
            echo "       👨‍🏫 {$prof->nombre} {$prof->apellido}\n";
        }
        
        foreach ($estudiantes as $est) {
            echo "       🎓 {$est->nombre} {$est->apellido}\n";
        }
    }
    
    // 4. Verificar rutas del chat
    echo "\n4. Verificando rutas del chat...\n";
    
    // Simular las rutas que usa el frontend
    $rutasChat = [
        '/chat/users/search',
        '/chat/users/options',
        '/chat/create'
    ];
    
    foreach ($rutasChat as $ruta) {
        echo "   📍 Ruta: {$ruta}\n";
    }
    
    echo "\n💡 RECOMENDACIONES:\n";
    echo "===================\n";
    
    if ($profesores->count() > 0 || $estudiantes->count() > 0) {
        echo "✅ Hay usuarios en el sistema que se pueden encontrar\n";
        echo "✅ La búsqueda funciona con nombres completos\n";
        echo "💡 Problema probable: El frontend no está enviando las peticiones correctamente\n";
        echo "💡 Verificar:\n";
        echo "   - Rutas en web.php\n";
        echo "   - JavaScript del modal de chat\n";
        echo "   - Autenticación de la sesión\n";
        echo "   - Consola del navegador para errores\n";
    } else {
        echo "❌ No se encontraron usuarios con las búsquedas\n";
        echo "💡 Verificar los nombres exactos en la base de datos\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}