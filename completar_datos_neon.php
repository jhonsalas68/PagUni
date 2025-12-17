<?php

echo "=== COMPLETANDO DATOS PARA NEON ===\n\n";

try {
    // Cargar Laravel
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "🔧 Agregando más materias...\n";
    
    // Agregar más materias
    $materias = [
        ['nombre' => 'Desarrollo Móvil', 'codigo' => 'SIS-601', 'carrera_id' => 1, 'semestre' => 6, 'creditos' => 4],
        ['nombre' => 'Seguridad Informática', 'codigo' => 'SIS-602', 'carrera_id' => 1, 'semestre' => 6, 'creditos' => 4],
        ['nombre' => 'Gestión de Proyectos', 'codigo' => 'SIS-701', 'carrera_id' => 1, 'semestre' => 7, 'creditos' => 4],
        ['nombre' => 'Arquitectura de Software', 'codigo' => 'SIS-702', 'carrera_id' => 1, 'semestre' => 7, 'creditos' => 4],
        ['nombre' => 'Tesis I', 'codigo' => 'SIS-901', 'carrera_id' => 1, 'semestre' => 9, 'creditos' => 6],
        ['nombre' => 'Tesis II', 'codigo' => 'SIS-1001', 'carrera_id' => 1, 'semestre' => 10, 'creditos' => 6],
        
        // TELECOMUNICACIONES
        ['nombre' => 'Señales y Sistemas', 'codigo' => 'TEL-101', 'carrera_id' => 2, 'semestre' => 1, 'creditos' => 4],
        ['nombre' => 'Circuitos Eléctricos', 'codigo' => 'TEL-102', 'carrera_id' => 2, 'semestre' => 1, 'creditos' => 4],
        ['nombre' => 'Comunicaciones Digitales', 'codigo' => 'TEL-201', 'carrera_id' => 2, 'semestre' => 2, 'creditos' => 4],
        ['nombre' => 'Antenas y Propagación', 'codigo' => 'TEL-301', 'carrera_id' => 2, 'semestre' => 3, 'creditos' => 4],
        
        // INFORMÁTICA
        ['nombre' => 'Fundamentos de Programación', 'codigo' => 'INF-101', 'carrera_id' => 3, 'semestre' => 1, 'creditos' => 4],
        ['nombre' => 'Lógica Digital', 'codigo' => 'INF-102', 'carrera_id' => 3, 'semestre' => 1, 'creditos' => 4],
        ['nombre' => 'Programación Orientada a Objetos', 'codigo' => 'INF-201', 'carrera_id' => 3, 'semestre' => 2, 'creditos' => 4],
        ['nombre' => 'Arquitectura de Computadoras', 'codigo' => 'INF-301', 'carrera_id' => 3, 'semestre' => 3, 'creditos' => 4],
    ];
    
    foreach ($materias as $materia) {
        $materia['created_at'] = now();
        $materia['updated_at'] = now();
        DB::table('materias')->insert($materia);
    }
    
    echo "✅ " . count($materias) . " materias agregadas\n";
    
    echo "\n👥 Creando más grupos...\n";
    
    // Crear más grupos
    $grupoCounter = 11;
    $materiasNuevas = DB::table('materias')->where('id', '>', 20)->get();
    
    foreach ($materiasNuevas as $materia) {
        DB::table('grupos')->insert([
            'identificador' => 'G' . str_pad($grupoCounter, 2, '0', STR_PAD_LEFT),
            'materia_id' => $materia->id,
            'capacidad_maxima' => rand(25, 35),
            'estado' => 'activo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $grupoCounter++;
    }
    
    echo "✅ Grupos adicionales creados\n";
    
    echo "\n📅 Creando horarios para nuevos grupos...\n";
    
    // Crear horarios para grupos sin horarios
    $gruposSinHorarios = DB::table('grupos')
        ->leftJoin('horarios', 'grupos.id', '=', 'horarios.grupo_id')
        ->whereNull('horarios.id')
        ->select('grupos.*')
        ->get();
    
    $dias = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes'];
    $horas = [
        ['inicio' => '08:00:00', 'fin' => '10:00:00'],
        ['inicio' => '10:00:00', 'fin' => '12:00:00'],
        ['inicio' => '14:00:00', 'fin' => '16:00:00'],
        ['inicio' => '16:00:00', 'fin' => '18:00:00'],
    ];
    
    foreach ($gruposSinHorarios as $grupo) {
        $horario = $horas[array_rand($horas)];
        
        DB::table('horarios')->insert([
            'grupo_id' => $grupo->id,
            'aula_id' => rand(1, 8),
            'dia_semana' => $dias[array_rand($dias)],
            'hora_inicio' => $horario['inicio'],
            'hora_fin' => $horario['fin'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    
    echo "✅ Horarios creados para " . count($gruposSinHorarios) . " grupos\n";
    
    echo "\n📋 Asignando profesores a nuevos grupos...\n";
    
    // Crear carga académica para grupos sin asignación
    $gruposSinCarga = DB::table('grupos')
        ->leftJoin('carga_academica', 'grupos.id', '=', 'carga_academica.grupo_id')
        ->whereNull('carga_academica.id')
        ->select('grupos.*')
        ->get();
    
    foreach ($gruposSinCarga as $grupo) {
        DB::table('carga_academica')->insert([
            'profesor_id' => rand(1, 8),
            'grupo_id' => $grupo->id,
            'gestion' => 2024,
            'periodo' => '2-2024',
            'estado' => 'activo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    
    echo "✅ Carga académica asignada a " . count($gruposSinCarga) . " grupos\n";
    
    echo "\n📊 RESUMEN FINAL:\n";
    
    $resumen = [
        'Administradores' => DB::table('administradores')->count(),
        'Facultades' => DB::table('facultades')->count(),
        'Carreras' => DB::table('carreras')->count(),
        'Materias' => DB::table('materias')->count(),
        'Profesores' => DB::table('profesores')->count(),
        'Estudiantes' => DB::table('estudiantes')->count(),
        'Aulas' => DB::table('aulas')->count(),
        'Grupos' => DB::table('grupos')->count(),
        'Horarios' => DB::table('horarios')->count(),
        'Carga Académica' => DB::table('carga_academica')->count(),
    ];
    
    foreach ($resumen as $tabla => $count) {
        echo "   - {$tabla}: {$count} registros\n";
    }
    
    echo "\n🎉 BASE DE DATOS COMPLETAMENTE POBLADA\n";
    echo "📧 Credenciales: admin@ficct.edu.bo / admin123\n";
    echo "🚀 Sistema listo para despliegue en producción\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DE COMPLETADO ===\n";