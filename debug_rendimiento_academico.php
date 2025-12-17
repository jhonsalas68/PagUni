<?php
require_once 'vendor/autoload.php';

// Cargar configuración de Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TipoEvaluacion;
use App\Models\Calificacion;
use App\Models\Inscripcion;
use App\Models\Grupo;
use App\Models\Materia;

echo "🔍 DEBUG REPORTE DE RENDIMIENTO ACADÉMICO\n";
echo "=========================================\n\n";

// 1. Verificar tipos de evaluación
$tiposEvaluacion = TipoEvaluacion::all();
echo "📊 TIPOS DE EVALUACIÓN ({$tiposEvaluacion->count()}):\n";
foreach ($tiposEvaluacion as $tipo) {
    echo "   - {$tipo->nombre}: {$tipo->ponderacion}%\n";
}
echo "\n";

// 2. Verificar calificaciones
$calificaciones = Calificacion::count();
echo "📝 CALIFICACIONES EN BD: {$calificaciones}\n\n";

// 3. Verificar inscripciones activas
$inscripciones = Inscripcion::where('estado', 'activo')->count();
echo "👥 INSCRIPCIONES ACTIVAS: {$inscripciones}\n\n";

// 4. Verificar materias con grupos
$materias = Materia::with('grupos')->get();
echo "📚 MATERIAS CON GRUPOS:\n";
foreach ($materias as $materia) {
    echo "   - {$materia->nombre} ({$materia->codigo}): {$materia->grupos->count()} grupos\n";
    foreach ($materia->grupos as $grupo) {
        $inscripcionesGrupo = Inscripcion::where('grupo_id', $grupo->id)->where('estado', 'activo')->count();
        echo "     * Grupo {$grupo->identificador}: {$inscripcionesGrupo} estudiantes\n";
    }
}
echo "\n";

// 5. Probar una consulta específica como la del controlador
echo "🧪 PROBANDO CONSULTA DEL CONTROLADOR:\n";
$grupoId = Grupo::first()->id ?? null;

if ($grupoId) {
    echo "Probando con Grupo ID: {$grupoId}\n";
    
    try {
        $grupoResult = Grupo::with(['inscripciones.estudiante', 'inscripciones.calificaciones.tipoEvaluacion'])
            ->find($grupoId);
            
        if ($grupoResult) {
            $materiaNombre = $grupoResult->materia ? $grupoResult->materia->nombre : 'N/A';
            echo "✅ Grupo encontrado: {$materiaNombre}\n";
            echo "   Inscripciones: {$grupoResult->inscripciones->count()}\n";
            
            foreach ($grupoResult->inscripciones as $inscripcion) {
                if ($inscripcion->estado !== 'activo') continue;
                if (!$inscripcion->estudiante) continue;
                
                echo "   - Estudiante: {$inscripcion->estudiante->nombre} {$inscripcion->estudiante->apellido}\n";
                echo "     Calificaciones: {$inscripcion->calificaciones->count()}\n";
                
                foreach ($inscripcion->calificaciones as $cal) {
                    $tipoNombre = $cal->tipoEvaluacion ? $cal->tipoEvaluacion->nombre : 'N/A';
                    echo "       * {$tipoNombre}: {$cal->nota}\n";
                }
            }
        } else {
            echo "❌ Grupo no encontrado\n";
        }
    } catch (Exception $e) {
        echo "❌ ERROR: " . $e->getMessage() . "\n";
        echo "Línea: " . $e->getLine() . "\n";
        echo "Archivo: " . $e->getFile() . "\n";
    }
} else {
    echo "❌ No hay grupos en la base de datos\n";
}

echo "\n🎯 RECOMENDACIONES:\n";
if ($tiposEvaluacion->count() == 0) {
    echo "1. Crear tipos de evaluación (Parcial 1, Parcial 2, Final, etc.)\n";
}
if ($calificaciones == 0) {
    echo "2. Crear calificaciones de prueba para los estudiantes\n";
}
echo "3. Verificar que las relaciones estén correctamente definidas\n";
echo "4. Probar el reporte con datos reales\n";