<?php

require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Grupo;
use App\Models\Profesor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

echo "=== TEST ACCESO WEB CALIFICACIONES ===\n\n";

try {
    // 1. Simular sesión de profesor
    $profesor = Profesor::where('codigo_docente', 'PROF001')->first();
    if (!$profesor) {
        echo "❌ ERROR: Profesor PROF001 no encontrado\n";
        exit;
    }
    
    echo "✅ Profesor: {$profesor->nombre} {$profesor->apellido}\n";
    
    // Simular sesión
    Session::put('profesor_id', $profesor->id);
    Session::put('user_type', 'profesor');
    
    echo "✅ Sesión simulada para profesor ID: {$profesor->id}\n";

    // 2. Obtener grupo para pruebas
    $grupo = Grupo::whereHas('cargaAcademica', function ($query) use ($profesor) {
        $query->where('profesor_id', $profesor->id);
    })->with('materia')->first();
    
    if (!$grupo) {
        echo "❌ ERROR: No se encontró grupo para el profesor\n";
        exit;
    }
    
    echo "✅ Grupo encontrado: {$grupo->identificador} - {$grupo->materia->nombre}\n";

    // 3. Probar acceso a la vista de gestión
    echo "\n🌐 PROBANDO ACCESO A VISTA DE GESTIÓN...\n";
    
    $controller = new \App\Http\Controllers\CalificacionController();
    
    try {
        $response = $controller->gestionNotas($grupo->id);
        echo "✅ Vista de gestión cargada exitosamente\n";
        echo "Tipo de respuesta: " . get_class($response) . "\n";
        
        if ($response instanceof \Illuminate\View\View) {
            $data = $response->getData();
            echo "Variables disponibles en la vista:\n";
            foreach (array_keys($data) as $key) {
                echo "   - {$key}\n";
            }
            
            // Verificar datos específicos
            if (isset($data['grupo'])) {
                echo "✅ Grupo en vista: {$data['grupo']->identificador}\n";
            }
            if (isset($data['tiposEvaluacion'])) {
                echo "✅ Tipos de evaluación: " . $data['tiposEvaluacion']->count() . "\n";
            }
            if (isset($data['inscripciones'])) {
                echo "✅ Inscripciones: " . $data['inscripciones']->count() . "\n";
            }
        }
        
    } catch (\Exception $e) {
        echo "❌ ERROR al cargar vista: " . $e->getMessage() . "\n";
        echo "Archivo: " . $e->getFile() . "\n";
        echo "Línea: " . $e->getLine() . "\n";
    }

    // 4. Simular envío de formulario con datos reales
    echo "\n📝 SIMULANDO ENVÍO DE FORMULARIO...\n";
    
    // Obtener datos reales de la base de datos
    $inscripciones = $grupo->inscripciones()->where('estado', 'activo')->get();
    $tiposEvaluacion = \App\Models\TipoEvaluacion::where('grupo_id', $grupo->id)->where('estado', 'activo')->get();
    
    if ($inscripciones->isEmpty()) {
        echo "❌ ERROR: No hay inscripciones para probar\n";
        exit;
    }
    
    if ($tiposEvaluacion->isEmpty()) {
        echo "❌ ERROR: No hay tipos de evaluación para probar\n";
        exit;
    }
    
    // Crear request simulando el formulario web
    $request = Request::create('/profesor/calificaciones', 'POST', [
        '_token' => csrf_token(),
        'grupo_id' => $grupo->id,
        'tipo_evaluacion_id' => $tiposEvaluacion->first()->id,
        'notas' => [
            $inscripciones->first()->id => 88.5
        ]
    ]);
    
    echo "Datos del request:\n";
    echo "- URL: {$request->url()}\n";
    echo "- Método: {$request->method()}\n";
    echo "- Token CSRF: " . ($request->has('_token') ? 'Presente' : 'Ausente') . "\n";
    echo "- grupo_id: {$request->grupo_id}\n";
    echo "- tipo_evaluacion_id: {$request->tipo_evaluacion_id}\n";
    echo "- notas: " . json_encode($request->notas) . "\n";

    // 5. Probar el método store del controlador
    echo "\n💾 PROBANDO MÉTODO STORE...\n";
    
    try {
        $response = $controller->store($request);
        echo "✅ Método store ejecutado exitosamente\n";
        echo "Tipo de respuesta: " . get_class($response) . "\n";
        
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            echo "URL de redirección: " . $response->getTargetUrl() . "\n";
            
            // Verificar mensajes de sesión
            $session = $response->getSession();
            if ($session) {
                if ($session->has('success')) {
                    echo "✅ Mensaje de éxito: " . $session->get('success') . "\n";
                }
                if ($session->has('error')) {
                    echo "❌ Mensaje de error: " . $session->get('error') . "\n";
                }
            }
        }
        
    } catch (\Exception $e) {
        echo "❌ ERROR en método store: " . $e->getMessage() . "\n";
        echo "Archivo: " . $e->getFile() . "\n";
        echo "Línea: " . $e->getLine() . "\n";
    }

    // 6. Verificar que la calificación se guardó
    echo "\n🔍 VERIFICANDO CALIFICACIÓN GUARDADA...\n";
    
    $calificacion = \App\Models\Calificacion::where('inscripcion_id', $inscripciones->first()->id)
        ->where('tipo_evaluacion_id', $tiposEvaluacion->first()->id)
        ->first();
    
    if ($calificacion) {
        echo "✅ Calificación encontrada:\n";
        echo "   - ID: {$calificacion->id}\n";
        echo "   - Nota: {$calificacion->nota}\n";
        echo "   - Fecha: {$calificacion->fecha}\n";
    } else {
        echo "❌ ERROR: Calificación no encontrada en la base de datos\n";
    }

    // 7. Probar validación con datos incorrectos
    echo "\n🚫 PROBANDO VALIDACIÓN CON DATOS INCORRECTOS...\n";
    
    $requestInvalido = Request::create('/profesor/calificaciones', 'POST', [
        '_token' => csrf_token(),
        'grupo_id' => $grupo->id,
        'tipo_evaluacion_id' => $tiposEvaluacion->first()->id,
        'notas' => [
            $inscripciones->first()->id => 150 // Nota inválida (mayor a 100)
        ]
    ]);
    
    try {
        $response = $controller->store($requestInvalido);
        echo "⚠️  Validación no detectó error (esto podría ser un problema)\n";
    } catch (\Illuminate\Validation\ValidationException $e) {
        echo "✅ Validación funcionando correctamente\n";
        echo "Errores de validación:\n";
        foreach ($e->errors() as $field => $errors) {
            foreach ($errors as $error) {
                echo "   - {$field}: {$error}\n";
            }
        }
    } catch (\Exception $e) {
        echo "❌ ERROR inesperado: " . $e->getMessage() . "\n";
    }

} catch (Exception $e) {
    echo "❌ ERROR GENERAL: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
}

echo "\n=== FIN DEL TEST ===\n";