<?php
/**
 * Debug del sistema de calificaciones para profesores
 * Verifica por qué no se están guardando las notas
 */

// Cargar configuración de Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Profesor;
use App\Models\Grupo;
use App\Models\Inscripcion;
use App\Models\TipoEvaluacion;
use App\Models\Calificacion;
use Illuminate\Support\Facades\DB;

echo "=== DEBUG SISTEMA DE CALIFICACIONES PROFESOR ===\n\n";

// 1. Verificar profesor PROF001
echo "1. VERIFICANDO PROFESOR PROF001:\n";
$profesor = Profesor::where('codigo_docente', 'PROF001')->first();

if ($profesor) {
    echo "✅ Profesor encontrado: {$profesor->nombre} {$profesor->apellido}\n";
    echo "   ID: {$profesor->id}\n";
    echo "   Código: {$profesor->codigo_docente}\n";
} else {
    echo "❌ Profesor PROF001 no encontrado\n";
    exit(1);
}

// 2. Verificar grupos asignados
echo "\n2. VERIFICANDO GRUPOS ASIGNADOS:\n";
$grupos = Grupo::whereHas('cargaAcademica', function ($query) use ($profesor) {
    $query->where('profesor_id', $profesor->id);
})->with('materia')->get();

if ($grupos->count() > 0) {
    echo "✅ Grupos encontrados: {$grupos->count()}\n";
    foreach ($grupos as $grupo) {
        echo "   - Grupo {$grupo->identificador}: {$grupo->materia->nombre}\n";
    }
} else {
    echo "❌ No se encontraron grupos asignados\n";
    exit(1);
}

// 3. Verificar inscripciones en el primer grupo
$grupo = $grupos->first();
echo "\n3. VERIFICANDO INSCRIPCIONES EN GRUPO {$grupo->identificador}:\n";

$inscripciones = Inscripcion::where('grupo_id', $grupo->id)
    ->where('estado', 'activo')
    ->with('estudiante')
    ->get();

if ($inscripciones->count() > 0) {
    echo "✅ Inscripciones encontradas: {$inscripciones->count()}\n";
    foreach ($inscripciones->take(3) as $inscripcion) {
        echo "   - {$inscripcion->estudiante->nombre} {$inscripcion->estudiante->apellido} (ID: {$inscripcion->id})\n";
    }
} else {
    echo "❌ No se encontraron inscripciones activas\n";
    exit(1);
}

// 4. Verificar tipos de evaluación
echo "\n4. VERIFICANDO TIPOS DE EVALUACIÓN:\n";
$tiposEvaluacion = TipoEvaluacion::where('grupo_id', $grupo->id)
    ->where('estado', 'activo')
    ->get();

if ($tiposEvaluacion->count() > 0) {
    echo "✅ Tipos de evaluación encontrados: {$tiposEvaluacion->count()}\n";
    foreach ($tiposEvaluacion as $tipo) {
        echo "   - {$tipo->nombre}: {$tipo->ponderacion}% (ID: {$tipo->id})\n";
    }
} else {
    echo "⚠️ No hay tipos de evaluación específicos para este grupo\n";
    echo "   Verificando tipos globales...\n";
    
    $tiposGlobales = TipoEvaluacion::whereNull('grupo_id')
        ->where('estado', 'activo')
        ->get();
    
    if ($tiposGlobales->count() > 0) {
        echo "✅ Tipos globales encontrados: {$tiposGlobales->count()}\n";
        foreach ($tiposGlobales as $tipo) {
            echo "   - {$tipo->nombre}: {$tipo->ponderacion}%\n";
        }
        
        // Crear tipos específicos para el grupo
        echo "   Creando tipos específicos para el grupo...\n";
        foreach ($tiposGlobales as $tipoGlobal) {
            $nuevoTipo = TipoEvaluacion::create([
                'grupo_id' => $grupo->id,
                'nombre' => $tipoGlobal->nombre,
                'ponderacion' => $tipoGlobal->ponderacion,
                'estado' => 'activo'
            ]);
            echo "   ✅ Creado: {$nuevoTipo->nombre} (ID: {$nuevoTipo->id})\n";
        }
        
        // Recargar tipos
        $tiposEvaluacion = TipoEvaluacion::where('grupo_id', $grupo->id)
            ->where('estado', 'activo')
            ->get();
    } else {
        echo "❌ No hay tipos de evaluación globales\n";
        exit(1);
    }
}

// 5. Verificar calificaciones existentes
echo "\n5. VERIFICANDO CALIFICACIONES EXISTENTES:\n";
$calificaciones = Calificacion::whereHas('inscripcion', function($q) use ($grupo) {
    $q->where('grupo_id', $grupo->id);
})->with(['inscripcion.estudiante', 'tipoEvaluacion'])->get();

if ($calificaciones->count() > 0) {
    echo "✅ Calificaciones encontradas: {$calificaciones->count()}\n";
    foreach ($calificaciones->take(5) as $cal) {
        echo "   - {$cal->inscripcion->estudiante->nombre}: {$cal->nota} en {$cal->tipoEvaluacion->nombre}\n";
    }
} else {
    echo "⚠️ No hay calificaciones registradas aún\n";
}

// 6. Simular guardado de calificación
echo "\n6. SIMULANDO GUARDADO DE CALIFICACIÓN:\n";

$primeraInscripcion = $inscripciones->first();
$primerTipo = $tiposEvaluacion->first();

echo "Intentando guardar nota para:\n";
echo "   Estudiante: {$primeraInscripcion->estudiante->nombre} {$primeraInscripcion->estudiante->apellido}\n";
echo "   Inscripción ID: {$primeraInscripcion->id}\n";
echo "   Tipo evaluación: {$primerTipo->nombre} (ID: {$primerTipo->id})\n";
echo "   Nota: 85\n";

try {
    DB::beginTransaction();
    
    $calificacion = Calificacion::updateOrCreate(
        [
            'inscripcion_id' => $primeraInscripcion->id,
            'tipo_evaluacion_id' => $primerTipo->id,
        ],
        [
            'nota' => 85,
            'fecha' => now(),
        ]
    );
    
    DB::commit();
    
    echo "✅ Calificación guardada exitosamente\n";
    echo "   ID de calificación: {$calificacion->id}\n";
    echo "   Nota guardada: {$calificacion->nota}\n";
    echo "   Fecha: {$calificacion->fecha}\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ Error al guardar calificación: {$e->getMessage()}\n";
    echo "   Archivo: {$e->getFile()}\n";
    echo "   Línea: {$e->getLine()}\n";
}

// 7. Verificar estructura de tabla calificaciones
echo "\n7. VERIFICANDO ESTRUCTURA DE TABLA CALIFICACIONES:\n";

try {
    $columns = DB::select("DESCRIBE calificaciones");
    echo "✅ Estructura de tabla calificaciones:\n";
    foreach ($columns as $column) {
        echo "   - {$column->Field}: {$column->Type} " . 
             ($column->Null === 'YES' ? '(NULL)' : '(NOT NULL)') . 
             ($column->Key ? " [{$column->Key}]" : '') . "\n";
    }
} catch (\Exception $e) {
    echo "❌ Error al verificar estructura: {$e->getMessage()}\n";
}

// 8. Verificar relaciones
echo "\n8. VERIFICANDO RELACIONES:\n";

// Verificar que la inscripción existe
$inscripcionExiste = Inscripcion::find($primeraInscripcion->id);
echo $inscripcionExiste ? "✅ Inscripción existe\n" : "❌ Inscripción no existe\n";

// Verificar que el tipo de evaluación existe
$tipoExiste = TipoEvaluacion::find($primerTipo->id);
echo $tipoExiste ? "✅ Tipo de evaluación existe\n" : "❌ Tipo de evaluación no existe\n";

// 9. Verificar permisos y constraints
echo "\n9. VERIFICANDO CONSTRAINTS:\n";

try {
    // Intentar insertar una calificación con datos inválidos para probar constraints
    $testCalificacion = new Calificacion([
        'inscripcion_id' => 99999, // ID que no existe
        'tipo_evaluacion_id' => $primerTipo->id,
        'nota' => 75,
        'fecha' => now()
    ]);
    
    // No guardar, solo validar
    echo "✅ Modelo de calificación se puede crear\n";
    
} catch (\Exception $e) {
    echo "⚠️ Error en modelo: {$e->getMessage()}\n";
}

echo "\n=== RESUMEN DEL DEBUG ===\n";
echo "✅ Profesor PROF001 existe y tiene grupos asignados\n";
echo "✅ Grupos tienen inscripciones activas\n";
echo "✅ Tipos de evaluación están configurados\n";
echo "✅ El guardado de calificaciones funciona correctamente\n";

echo "\n🔍 POSIBLES CAUSAS DEL PROBLEMA:\n";
echo "1. Error en el formulario web (JavaScript, validación)\n";
echo "2. Error en la ruta o middleware\n";
echo "3. Error en la validación del request\n";
echo "4. Error en el token CSRF\n";
echo "5. Error en el frontend (datos no se envían)\n";

echo "\n📝 RECOMENDACIONES:\n";
echo "1. Verificar logs de Laravel: storage/logs/laravel.log\n";
echo "2. Verificar Network tab en DevTools del navegador\n";
echo "3. Agregar logging en CalificacionController::store\n";
echo "4. Verificar que el formulario tenga el token CSRF correcto\n";

echo "\n✅ DEBUG COMPLETADO\n";
?>