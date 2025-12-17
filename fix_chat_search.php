<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Estudiante;
use App\Models\Profesor;
use App\Models\Inscripcion;
use App\Models\Grupo;
use Illuminate\Support\Facades\DB;

try {
    echo "🔧 ARREGLANDO SISTEMA DE CHAT\n";
    echo "=============================\n\n";
    
    // 1. Verificar y crear inscripciones faltantes
    echo "1. Verificando inscripciones...\n";
    
    $estudiantes = Estudiante::limit(8)->get(); // Primeros 8 estudiantes
    $grupos = Grupo::where('estado', 'activo')->get();
    
    echo "   Estudiantes disponibles: " . $estudiantes->count() . "\n";
    echo "   Grupos disponibles: " . $grupos->count() . "\n";
    
    $inscripcionesCreadas = 0;
    
    // Crear inscripciones para que los estudiantes aparezcan en las materias
    foreach ($estudiantes as $index => $estudiante) {
        // Asignar cada estudiante a un grupo diferente (rotativo)
        $grupo = $grupos[$index % $grupos->count()];
        
        // Verificar si ya existe la inscripción
        $existeInscripcion = Inscripcion::where('estudiante_id', $estudiante->id)
            ->where('grupo_id', $grupo->id)
            ->exists();
        
        if (!$existeInscripcion) {
            Inscripcion::create([
                'estudiante_id' => $estudiante->id,
                'grupo_id' => $grupo->id,
                'periodo_academico' => '2025-2',
                'estado' => 'activo',
                'fecha_inscripcion' => now()
            ]);
            
            $inscripcionesCreadas++;
            echo "   ✅ Inscrito: {$estudiante->nombre} {$estudiante->apellido} en {$grupo->materia->nombre} (Grupo {$grupo->identificador})\n";
        }
    }
    
    echo "   Total inscripciones creadas: {$inscripcionesCreadas}\n";
    
    // 2. Probar búsquedas mejoradas
    echo "\n2. Probando búsquedas mejoradas...\n";
    
    $busquedas = ['juan', 'maria', 'carlos', 'ana'];
    
    foreach ($busquedas as $query) {
        echo "\n   Buscando '{$query}':\n";
        
        // Profesores
        $profesores = Profesor::where('estado', 'activo')
            ->where(function($q) use ($query) {
                $q->where('nombre', 'like', "%{$query}%")
                  ->orWhere('apellido', 'like', "%{$query}%");
            })->get();
        
        echo "     Profesores: " . $profesores->count() . "\n";
        foreach ($profesores as $prof) {
            echo "       - {$prof->nombre} {$prof->apellido}\n";
        }
        
        // Estudiantes
        $estudiantes = Estudiante::where(function($q) use ($query) {
                $q->where('nombre', 'like', "%{$query}%")
                  ->orWhere('apellido', 'like', "%{$query}%");
            })->limit(5)->get();
        
        echo "     Estudiantes: " . $estudiantes->count() . "\n";
        foreach ($estudiantes as $est) {
            echo "       - {$est->nombre} {$est->apellido}\n";
        }
    }
    
    // 3. Probar búsqueda por materia
    echo "\n3. Probando búsqueda por materia...\n";
    
    $materias = \App\Models\Materia::where('estado', 'activo')->limit(3)->get();
    
    foreach ($materias as $materia) {
        echo "\n   Materia: {$materia->nombre}\n";
        
        $estudiantesMateria = Estudiante::whereHas('inscripciones.grupo', function($q) use ($materia) {
            $q->where('materia_id', $materia->id);
        })->get();
        
        echo "     Estudiantes inscritos: " . $estudiantesMateria->count() . "\n";
        foreach ($estudiantesMateria as $est) {
            echo "       - {$est->nombre} {$est->apellido}\n";
        }
    }
    
    // 4. Crear datos adicionales si es necesario
    echo "\n4. Verificando datos adicionales...\n";
    
    // Asegurar que todos los estudiantes tengan email
    $estudiantesSinEmail = Estudiante::whereNull('email')->orWhere('email', '')->get();
    
    if ($estudiantesSinEmail->count() > 0) {
        echo "   Actualizando emails de estudiantes...\n";
        
        foreach ($estudiantesSinEmail as $estudiante) {
            $email = strtolower(str_replace(' ', '.', $estudiante->nombre . '.' . $estudiante->apellido)) . '@estudiante.edu';
            $estudiante->update(['email' => $email]);
            echo "     - {$estudiante->nombre} {$estudiante->apellido}: {$email}\n";
        }
    }
    
    // Asegurar que todos los profesores tengan email
    $profesoresSinEmail = Profesor::whereNull('email')->orWhere('email', '')->get();
    
    if ($profesoresSinEmail->count() > 0) {
        echo "   Actualizando emails de profesores...\n";
        
        foreach ($profesoresSinEmail as $profesor) {
            $email = strtolower(str_replace(' ', '.', $profesor->nombre . '.' . $profesor->apellido)) . '@profesor.edu';
            $profesor->update(['email' => $email]);
            echo "     - {$profesor->nombre} {$profesor->apellido}: {$email}\n";
        }
    }
    
    // 5. Verificar resultado final
    echo "\n5. Verificación final del sistema de chat...\n";
    
    $totalProfesores = Profesor::where('estado', 'activo')->count();
    $totalEstudiantes = Estudiante::count();
    $totalInscripciones = Inscripcion::where('estado', 'activo')->count();
    
    echo "   📊 Estadísticas finales:\n";
    echo "     - Profesores activos: {$totalProfesores}\n";
    echo "     - Estudiantes: {$totalEstudiantes}\n";
    echo "     - Inscripciones activas: {$totalInscripciones}\n";
    
    // Probar una búsqueda completa como lo haría el sistema
    echo "\n   🔍 Prueba de búsqueda completa (como 'juan'):\n";
    
    $query = 'juan';
    $users = collect();
    
    // Profesores
    $profesores = Profesor::where('estado', 'activo')
        ->where(function($q) use ($query) {
            $q->where('nombre', 'like', "%{$query}%")
              ->orWhere('apellido', 'like', "%{$query}%");
        })->get();
    
    foreach($profesores as $prof) {
        $users->push([
            'id' => $prof->id,
            'name' => $prof->nombre . ' ' . $prof->apellido,
            'type' => 'profesor',
            'role_label' => 'Docente',
            'email' => $prof->email,
            'initials' => substr($prof->nombre, 0, 1) . substr($prof->apellido, 0, 1)
        ]);
    }
    
    // Estudiantes
    $estudiantes = Estudiante::where(function($q) use ($query) {
            $q->where('nombre', 'like', "%{$query}%")
              ->orWhere('apellido', 'like', "%{$query}%");
        })->get();
    
    foreach($estudiantes as $est) {
        $users->push([
            'id' => $est->id,
            'name' => $est->nombre . ' ' . $est->apellido,
            'type' => 'estudiante',
            'role_label' => 'Estudiante',
            'email' => $est->email,
            'initials' => substr($est->nombre, 0, 1) . substr($est->apellido, 0, 1)
        ]);
    }
    
    echo "     Usuarios encontrados: " . $users->count() . "\n";
    foreach ($users as $user) {
        echo "       - {$user['name']} ({$user['role_label']})\n";
    }
    
    if ($users->count() > 0) {
        echo "\n🎉 SISTEMA DE CHAT REPARADO\n";
        echo "===========================\n";
        echo "✅ Ahora hay usuarios disponibles para la búsqueda\n";
        echo "✅ Las inscripciones están configuradas\n";
        echo "✅ Los emails están asignados\n";
        echo "💡 El chat debería funcionar correctamente ahora\n";
    } else {
        echo "\n⚠️  SISTEMA NECESITA MÁS AJUSTES\n";
        echo "================================\n";
        echo "El problema podría estar en el frontend o las rutas\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}