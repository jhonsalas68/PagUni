<?php

require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Profesor;
use App\Models\Grupo;
use App\Models\TipoEvaluacion;
use App\Models\Inscripcion;
use App\Models\Calificacion;
use Illuminate\Support\Facades\Session;

echo "=== DEBUG CALIFICACIONES - SIMULACIÓN REAL ===\n\n";

try {
    // 1. Simular login exitoso (establecer sesión como lo haría el navegador)
    $profesor = Profesor::where('codigo_docente', 'PROF001')->first();
    
    if (!$profesor) {
        echo "❌ ERROR: Profesor PROF001 no encontrado\n";
        exit;
    }
    
    echo "✅ Profesor: {$profesor->nombre} {$profesor->apellido} (ID: {$profesor->id})\n";
    
    // Establecer sesión exactamente como lo hace el AuthController
    Session::put('user_id', $profesor->id);
    Session::put('user_type', 'profesor');
    Session::put('user_codigo', 'PROF001');
    Session::put('user_name', $profesor->nombre . ' ' . $profesor->apellido);
    Session::save();
    
    echo "✅ Sesión establecida correctamente\n";

    // 2. Obtener datos reales para el formulario
    $grupos = Grupo::whereHas('cargaAcademica', function ($query) use ($profesor) {
        $query->where('profesor_id', $profesor->id);
    })->with('materia')->get();
    
    if ($grupos->isEmpty()) {
        echo "❌ ERROR: No hay grupos para el profesor\n";
        exit;
    }
    
    $grupo = $grupos->first();
    echo "✅ Grupo seleccionado: {$grupo->identificador} - {$grupo->materia->nombre}\n";
    
    $inscripciones = $grupo->inscripciones()->where('estado', 'activo')->with('estudiante')->get();
    $tiposEvaluacion = TipoEvaluacion::where('grupo_id', $grupo->id)->where('estado', 'activo')->get();
    
    echo "✅ Inscripciones: " . $inscripciones->count() . "\n";
    echo "✅ Tipos de evaluación: " . $tiposEvaluacion->count() . "\n";
    
    if ($inscripciones->isEmpty() || $tiposEvaluacion->isEmpty()) {
        echo "❌ ERROR: Faltan datos para la prueba\n";
        exit;
    }

    // 3. Mostrar el estado actual de las calificaciones
    echo "\n📋 ESTADO ACTUAL DE CALIFICACIONES:\n";
    
    foreach ($inscripciones as $inscripcion) {
        echo "Estudiante: {$inscripcion->estudiante->codigo_estudiante} - {$inscripcion->estudiante->nombre}\n";
        
        $calificacionesExistentes = $inscripcion->calificaciones()->with('tipoEvaluacion')->get();
        if ($calificacionesExistentes->isEmpty()) {
            echo "   - Sin calificaciones\n";
        } else {
            foreach ($calificacionesExistentes as $cal) {
                echo "   - {$cal->tipoEvaluacion->nombre}: {$cal->nota} pts\n";
            }
        }
    }

    // 4. Simular el proceso completo del formulario
    echo "\n📝 SIMULANDO PROCESO COMPLETO DEL FORMULARIO...\n";
    
    $tipoEvaluacion = $tiposEvaluacion->first();
    $inscripcion = $inscripciones->first();
    $notaNueva = 95.0;
    
    echo "Datos del formulario:\n";
    echo "   - Grupo ID: {$grupo->id}\n";
    echo "   - Tipo Evaluación ID: {$tipoEvaluacion->id} ({$tipoEvaluacion->nombre})\n";
    echo "   - Inscripción ID: {$inscripcion->id}\n";
    echo "   - Estudiante: {$inscripcion->estudiante->codigo_estudiante}\n";
    echo "   - Nota: {$notaNueva}\n";

    // 5. Ejecutar el guardado paso a paso (como lo hace el controlador)
    echo "\n💾 EJECUTANDO GUARDADO PASO A PASO...\n";
    
    // Paso 1: Validación
    echo "1. Validando datos...\n";
    $rules = [
        'grupo_id' => 'required|exists:grupos,id',
        'tipo_evaluacion_id' => 'required|exists:tipos_evaluacion,id',
        'notas' => 'required|array',
        'notas.*' => 'nullable|numeric|min:0|max:100',
    ];
    
    $datos = [
        'grupo_id' => $grupo->id,
        'tipo_evaluacion_id' => $tipoEvaluacion->id,
        'notas' => [$inscripcion->id => $notaNueva]
    ];
    
    $validator = \Illuminate\Support\Facades\Validator::make($datos, $rules);
    
    if ($validator->fails()) {
        echo "❌ Errores de validación:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "   - {$error}\n";
        }
        exit;
    }
    echo "✅ Validación exitosa\n";
    
    // Paso 2: Verificar permisos (que el grupo pertenezca al profesor)
    echo "2. Verificando permisos...\n";
    $grupoVerificacion = Grupo::whereHas('cargaAcademica', function ($query) use ($profesor) {
        $query->where('profesor_id', $profesor->id);
    })->where('id', $grupo->id)->first();
    
    if (!$grupoVerificacion) {
        echo "❌ ERROR: El profesor no tiene permisos sobre este grupo\n";
        exit;
    }
    echo "✅ Permisos verificados\n";
    
    // Paso 3: Guardar calificación
    echo "3. Guardando calificación...\n";
    
    \Illuminate\Support\Facades\DB::beginTransaction();
    try {
        $calificacionAnterior = Calificacion::where('inscripcion_id', $inscripcion->id)
            ->where('tipo_evaluacion_id', $tipoEvaluacion->id)
            ->first();
        
        if ($calificacionAnterior) {
            echo "   - Calificación anterior encontrada: {$calificacionAnterior->nota}\n";
        } else {
            echo "   - No hay calificación anterior\n";
        }
        
        $calificacion = Calificacion::updateOrCreate(
            [
                'inscripcion_id' => $inscripcion->id,
                'tipo_evaluacion_id' => $tipoEvaluacion->id,
            ],
            [
                'nota' => $notaNueva,
                'fecha' => now(),
            ]
        );
        
        \Illuminate\Support\Facades\DB::commit();
        
        echo "✅ Calificación guardada:\n";
        echo "   - ID: {$calificacion->id}\n";
        echo "   - Nota: {$calificacion->nota}\n";
        echo "   - ¿Nueva?: " . ($calificacion->wasRecentlyCreated ? 'Sí' : 'No') . "\n";
        
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\DB::rollBack();
        echo "❌ ERROR al guardar: " . $e->getMessage() . "\n";
        exit;
    }

    // 6. Verificar que se guardó correctamente
    echo "\n🔍 VERIFICACIÓN FINAL...\n";
    
    $calificacionFinal = Calificacion::where('inscripcion_id', $inscripcion->id)
        ->where('tipo_evaluacion_id', $tipoEvaluacion->id)
        ->with(['inscripcion.estudiante', 'tipoEvaluacion'])
        ->first();
    
    if ($calificacionFinal) {
        echo "✅ Calificación verificada en la base de datos:\n";
        echo "   - Estudiante: {$calificacionFinal->inscripcion->estudiante->codigo_estudiante}\n";
        echo "   - Evaluación: {$calificacionFinal->tipoEvaluacion->nombre}\n";
        echo "   - Nota: {$calificacionFinal->nota} pts\n";
        echo "   - Fecha: {$calificacionFinal->fecha}\n";
    } else {
        echo "❌ ERROR: Calificación no encontrada después del guardado\n";
    }

    // 7. Mostrar todas las calificaciones del estudiante
    echo "\n📊 TODAS LAS CALIFICACIONES DEL ESTUDIANTE:\n";
    
    $todasLasCalificaciones = Calificacion::whereHas('inscripcion', function($query) use ($inscripcion) {
        $query->where('estudiante_id', $inscripcion->estudiante_id);
    })->with(['tipoEvaluacion', 'inscripcion.grupo.materia'])->get();
    
    foreach ($todasLasCalificaciones as $cal) {
        echo "   - {$cal->inscripcion->grupo->materia->nombre}: {$cal->tipoEvaluacion->nombre} = {$cal->nota} pts\n";
    }

    // 8. Calcular promedio
    echo "\n🧮 CÁLCULO DE PROMEDIO:\n";
    
    $calificacionesGrupo = Calificacion::whereHas('inscripcion', function($query) use ($grupo, $inscripcion) {
        $query->where('grupo_id', $grupo->id)->where('estudiante_id', $inscripcion->estudiante_id);
    })->with('tipoEvaluacion')->get();
    
    $promedioAcumulado = 0;
    $totalPonderacion = 0;
    
    foreach ($calificacionesGrupo as $cal) {
        $ponderacion = $cal->tipoEvaluacion->ponderacion;
        $puntos = ($cal->nota / 100) * $ponderacion;
        $promedioAcumulado += $puntos;
        $totalPonderacion += $ponderacion;
        
        echo "   - {$cal->tipoEvaluacion->nombre}: {$cal->nota}/100 × {$ponderacion}% = {$puntos} pts\n";
    }
    
    echo "   - Promedio acumulado: {$promedioAcumulado} pts\n";
    echo "   - Ponderación total: {$totalPonderacion}%\n";
    
    if ($totalPonderacion == 100) {
        $estado = $promedioAcumulado >= 51 ? 'APROBADO' : 'REPROBADO';
        echo "   - Estado: {$estado}\n";
    } else {
        echo "   - Estado: INCOMPLETO (faltan evaluaciones)\n";
    }

} catch (Exception $e) {
    echo "❌ ERROR GENERAL: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== FIN DEL DEBUG ===\n";