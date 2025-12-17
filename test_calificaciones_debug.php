<?php

require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Grupo;
use App\Models\Inscripcion;
use App\Models\TipoEvaluacion;
use App\Models\Calificacion;
use App\Models\Profesor;

echo "=== TEST SISTEMA DE CALIFICACIONES ===\n\n";

try {
    // 1. Verificar que existe el profesor PROF001
    $profesor = Profesor::where('codigo_docente', 'PROF001')->first();
    if (!$profesor) {
        echo "❌ ERROR: No se encontró el profesor PROF001\n";
        exit;
    }
    echo "✅ Profesor encontrado: {$profesor->nombre} {$profesor->apellido}\n";

    // 2. Obtener grupos del profesor
    $grupos = Grupo::whereHas('cargaAcademica', function ($query) use ($profesor) {
        $query->where('profesor_id', $profesor->id);
    })->with('materia')->get();

    if ($grupos->isEmpty()) {
        echo "❌ ERROR: No se encontraron grupos para el profesor\n";
        exit;
    }

    echo "✅ Grupos encontrados: " . $grupos->count() . "\n";
    foreach ($grupos as $grupo) {
        echo "   - Grupo {$grupo->identificador}: {$grupo->materia->nombre}\n";
    }

    // 3. Tomar el primer grupo para pruebas
    $grupo = $grupos->first();
    echo "\n📋 Trabajando con grupo: {$grupo->identificador} - {$grupo->materia->nombre}\n";

    // 4. Verificar inscripciones
    $inscripciones = $grupo->inscripciones()->where('estado', 'activo')->with('estudiante')->get();
    echo "✅ Inscripciones activas: " . $inscripciones->count() . "\n";
    
    if ($inscripciones->isEmpty()) {
        echo "❌ ERROR: No hay estudiantes inscritos en este grupo\n";
        exit;
    }

    foreach ($inscripciones as $inscripcion) {
        echo "   - {$inscripcion->estudiante->codigo_estudiante}: {$inscripcion->estudiante->nombre} {$inscripcion->estudiante->apellido}\n";
    }

    // 5. Verificar tipos de evaluación
    $tiposEvaluacion = TipoEvaluacion::where('grupo_id', $grupo->id)->where('estado', 'activo')->get();
    echo "\n📊 Tipos de evaluación: " . $tiposEvaluacion->count() . "\n";
    
    if ($tiposEvaluacion->isEmpty()) {
        echo "⚠️  No hay tipos de evaluación específicos, creando por defecto...\n";
        
        // Crear tipos por defecto
        $defaults = [
            ['nombre' => 'Primer Parcial', 'ponderacion' => 30],
            ['nombre' => 'Segundo Parcial', 'ponderacion' => 30],
            ['nombre' => 'Examen Final', 'ponderacion' => 40]
        ];
        
        foreach ($defaults as $default) {
            TipoEvaluacion::create([
                'grupo_id' => $grupo->id,
                'nombre' => $default['nombre'],
                'ponderacion' => $default['ponderacion'],
                'estado' => 'activo'
            ]);
        }
        
        $tiposEvaluacion = TipoEvaluacion::where('grupo_id', $grupo->id)->where('estado', 'activo')->get();
        echo "✅ Tipos de evaluación creados: " . $tiposEvaluacion->count() . "\n";
    }

    foreach ($tiposEvaluacion as $tipo) {
        echo "   - {$tipo->nombre}: {$tipo->ponderacion}%\n";
    }

    // 6. Simular guardado de calificaciones
    echo "\n💾 SIMULANDO GUARDADO DE CALIFICACIONES...\n";
    
    $primerTipo = $tiposEvaluacion->first();
    $primeraInscripcion = $inscripciones->first();
    
    echo "Guardando nota para: {$primeraInscripcion->estudiante->nombre} en {$primerTipo->nombre}\n";
    
    // Simular los datos que llegarían del formulario
    $datosFormulario = [
        'grupo_id' => $grupo->id,
        'tipo_evaluacion_id' => $primerTipo->id,
        'notas' => [
            $primeraInscripcion->id => 85.5
        ]
    ];
    
    echo "Datos del formulario simulado:\n";
    echo "- grupo_id: {$datosFormulario['grupo_id']}\n";
    echo "- tipo_evaluacion_id: {$datosFormulario['tipo_evaluacion_id']}\n";
    echo "- nota para inscripción {$primeraInscripcion->id}: {$datosFormulario['notas'][$primeraInscripcion->id]}\n";

    // Intentar guardar
    try {
        $calificacion = Calificacion::updateOrCreate(
            [
                'inscripcion_id' => $primeraInscripcion->id,
                'tipo_evaluacion_id' => $primerTipo->id,
            ],
            [
                'nota' => $datosFormulario['notas'][$primeraInscripcion->id],
                'fecha' => now(),
            ]
        );
        
        echo "✅ Calificación guardada exitosamente!\n";
        echo "   - ID: {$calificacion->id}\n";
        echo "   - Nota: {$calificacion->nota}\n";
        echo "   - Fecha: {$calificacion->fecha}\n";
        echo "   - ¿Recién creada?: " . ($calificacion->wasRecentlyCreated ? 'Sí' : 'No (actualizada)') . "\n";
        
    } catch (Exception $e) {
        echo "❌ ERROR al guardar calificación: " . $e->getMessage() . "\n";
        echo "Archivo: " . $e->getFile() . "\n";
        echo "Línea: " . $e->getLine() . "\n";
    }

    // 7. Verificar que se guardó correctamente
    echo "\n🔍 VERIFICANDO CALIFICACIÓN GUARDADA...\n";
    
    $calificacionVerificacion = Calificacion::where('inscripcion_id', $primeraInscripcion->id)
        ->where('tipo_evaluacion_id', $primerTipo->id)
        ->first();
    
    if ($calificacionVerificacion) {
        echo "✅ Calificación encontrada en la base de datos:\n";
        echo "   - ID: {$calificacionVerificacion->id}\n";
        echo "   - Nota: {$calificacionVerificacion->nota}\n";
        echo "   - Fecha: {$calificacionVerificacion->fecha}\n";
    } else {
        echo "❌ ERROR: No se encontró la calificación en la base de datos\n";
    }

    // 8. Mostrar todas las calificaciones existentes para este grupo
    echo "\n📋 TODAS LAS CALIFICACIONES DEL GRUPO:\n";
    
    $todasCalificaciones = Calificacion::whereHas('inscripcion', function($query) use ($grupo) {
        $query->where('grupo_id', $grupo->id);
    })->with(['inscripcion.estudiante', 'tipoEvaluacion'])->get();
    
    if ($todasCalificaciones->isEmpty()) {
        echo "⚠️  No hay calificaciones registradas para este grupo\n";
    } else {
        echo "Total de calificaciones: " . $todasCalificaciones->count() . "\n";
        foreach ($todasCalificaciones as $cal) {
            echo "   - {$cal->inscripcion->estudiante->codigo_estudiante}: {$cal->tipoEvaluacion->nombre} = {$cal->nota} pts\n";
        }
    }

} catch (Exception $e) {
    echo "❌ ERROR GENERAL: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== FIN DEL TEST ===\n";