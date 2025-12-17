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
use Illuminate\Http\Request;
use App\Http\Controllers\CalificacionController;

echo "=== TEST FORMULARIO DE CALIFICACIONES ===\n\n";

try {
    // 1. Obtener datos del profesor y grupo
    $profesor = Profesor::where('codigo_docente', 'PROF001')->first();
    $grupo = Grupo::whereHas('cargaAcademica', function ($query) use ($profesor) {
        $query->where('profesor_id', $profesor->id);
    })->with('materia')->first();
    
    $inscripciones = $grupo->inscripciones()->where('estado', 'activo')->with('estudiante')->get();
    $tiposEvaluacion = TipoEvaluacion::where('grupo_id', $grupo->id)->where('estado', 'activo')->get();
    
    echo "📋 Datos del formulario:\n";
    echo "- Grupo: {$grupo->identificador} - {$grupo->materia->nombre}\n";
    echo "- Inscripciones: " . $inscripciones->count() . "\n";
    echo "- Tipos de evaluación: " . $tiposEvaluacion->count() . "\n\n";

    // 2. Simular datos del formulario como llegarían del POST
    $datosPost = [
        'grupo_id' => $grupo->id,
        'tipo_evaluacion_id' => $tiposEvaluacion->first()->id,
        'notas' => []
    ];
    
    // Agregar notas para cada inscripción
    foreach ($inscripciones as $inscripcion) {
        $datosPost['notas'][$inscripcion->id] = rand(60, 100); // Nota aleatoria entre 60-100
    }
    
    echo "📝 Datos POST simulados:\n";
    echo "- grupo_id: {$datosPost['grupo_id']}\n";
    echo "- tipo_evaluacion_id: {$datosPost['tipo_evaluacion_id']}\n";
    echo "- notas:\n";
    foreach ($datosPost['notas'] as $inscripcionId => $nota) {
        $inscripcion = $inscripciones->find($inscripcionId);
        echo "  * Inscripción {$inscripcionId} ({$inscripcion->estudiante->codigo_estudiante}): {$nota}\n";
    }
    echo "\n";

    // 3. Crear un Request simulado
    $request = new Request();
    $request->merge($datosPost);
    
    echo "🔧 Request creado con datos:\n";
    echo "- all(): " . json_encode($request->all()) . "\n";
    echo "- grupo_id: " . $request->grupo_id . "\n";
    echo "- tipo_evaluacion_id: " . $request->tipo_evaluacion_id . "\n";
    echo "- notas: " . json_encode($request->notas) . "\n\n";

    // 4. Simular la validación
    echo "✅ VALIDACIÓN:\n";
    
    $rules = [
        'grupo_id' => 'required|exists:grupos,id',
        'tipo_evaluacion_id' => 'required|exists:tipos_evaluacion,id',
        'notas' => 'required|array',
        'notas.*' => 'nullable|numeric|min:0|max:100',
    ];
    
    $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
    
    if ($validator->fails()) {
        echo "❌ Errores de validación:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "   - {$error}\n";
        }
    } else {
        echo "✅ Validación exitosa\n";
    }
    echo "\n";

    // 5. Simular el guardado manual (sin usar el controlador)
    echo "💾 GUARDADO MANUAL:\n";
    
    $tipoEvaluacionId = $request->tipo_evaluacion_id;
    $notas = $request->notas;
    
    \Illuminate\Support\Facades\DB::beginTransaction();
    try {
        $guardadas = 0;
        foreach ($notas as $inscripcionId => $notaValor) {
            if ($notaValor !== null && $notaValor !== '') {
                echo "Guardando: Inscripción {$inscripcionId} = {$notaValor}\n";
                
                $calificacion = Calificacion::updateOrCreate(
                    [
                        'inscripcion_id' => $inscripcionId,
                        'tipo_evaluacion_id' => $tipoEvaluacionId,
                    ],
                    [
                        'nota' => $notaValor,
                        'fecha' => now(),
                    ]
                );
                
                echo "   ✅ Guardada con ID: {$calificacion->id}\n";
                $guardadas++;
            }
        }
        
        \Illuminate\Support\Facades\DB::commit();
        echo "✅ Transacción completada. Calificaciones guardadas: {$guardadas}\n\n";
        
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\DB::rollBack();
        echo "❌ ERROR en transacción: " . $e->getMessage() . "\n\n";
    }

    // 6. Verificar las calificaciones guardadas
    echo "🔍 VERIFICACIÓN FINAL:\n";
    
    $calificacionesGuardadas = Calificacion::where('tipo_evaluacion_id', $tipoEvaluacionId)
        ->whereIn('inscripcion_id', array_keys($notas))
        ->with(['inscripcion.estudiante', 'tipoEvaluacion'])
        ->get();
    
    echo "Calificaciones encontradas: " . $calificacionesGuardadas->count() . "\n";
    foreach ($calificacionesGuardadas as $cal) {
        echo "   - {$cal->inscripcion->estudiante->codigo_estudiante}: {$cal->tipoEvaluacion->nombre} = {$cal->nota} pts (ID: {$cal->id})\n";
    }

    // 7. Probar el controlador real
    echo "\n🎯 PROBANDO CONTROLADOR REAL:\n";
    
    // Crear una nueva instancia del controlador
    $controller = new CalificacionController();
    
    // Crear un nuevo request con datos diferentes para distinguir
    $requestController = new Request();
    $datosController = [
        'grupo_id' => $grupo->id,
        'tipo_evaluacion_id' => $tiposEvaluacion->last()->id, // Usar el último tipo
        'notas' => []
    ];
    
    foreach ($inscripciones as $inscripcion) {
        $datosController['notas'][$inscripcion->id] = rand(70, 95); // Notas diferentes
    }
    
    $requestController->merge($datosController);
    
    echo "Datos para controlador:\n";
    echo "- tipo_evaluacion_id: {$datosController['tipo_evaluacion_id']}\n";
    echo "- notas: " . json_encode($datosController['notas']) . "\n";
    
    try {
        $response = $controller->store($requestController);
        echo "✅ Controlador ejecutado exitosamente\n";
        echo "Tipo de respuesta: " . get_class($response) . "\n";
        
        // Si es una RedirectResponse, obtener el mensaje de sesión
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            $session = $response->getSession();
            if ($session && $session->has('success')) {
                echo "Mensaje de éxito: " . $session->get('success') . "\n";
            }
            if ($session && $session->has('error')) {
                echo "Mensaje de error: " . $session->get('error') . "\n";
            }
        }
        
    } catch (\Exception $e) {
        echo "❌ ERROR en controlador: " . $e->getMessage() . "\n";
        echo "Archivo: " . $e->getFile() . "\n";
        echo "Línea: " . $e->getLine() . "\n";
    }

} catch (Exception $e) {
    echo "❌ ERROR GENERAL: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
}

echo "\n=== FIN DEL TEST ===\n";