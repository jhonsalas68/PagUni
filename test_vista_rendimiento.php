<?php
require_once 'vendor/autoload.php';

// Cargar configuración de Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Materia;
use App\Models\Profesor;
use App\Models\Grupo;

echo "🧪 PROBANDO DATOS PARA LA VISTA DE RENDIMIENTO\n";
echo "==============================================\n\n";

try {
    // Simular los datos que se pasan a la vista
    $materias = Materia::orderBy('nombre')->get();
    $profesores = Profesor::orderBy('nombre')->get();
    
    echo "📚 MATERIAS DISPONIBLES ({$materias->count()}):\n";
    foreach ($materias as $materia) {
        echo "   - ID: {$materia->id} | {$materia->nombre} ({$materia->codigo})\n";
    }
    echo "\n";
    
    $materiaId = 4; // Programación I (ISC-101)
    $grupoId = null;
    $profesorId = null;
    
    $estudiantes = [];
    $estadisticas = [
        'total' => 0,
        'aprobados' => 0,
        'reprobados' => 0,
        'promedio_general' => 0
    ];
    
    // Cargar grupos dependientes de la materia
    $grupos = collect([]);
    if ($materiaId) {
        $grupos = Grupo::where('materia_id', $materiaId)
            ->with('cargaAcademica.profesor')
            ->get();
            
        echo "👥 GRUPOS PARA MATERIA ID {$materiaId} ({$grupos->count()}):\n";
        foreach ($grupos as $grupo) {
            $profesor = $grupo->cargaAcademica->first() ? $grupo->cargaAcademica->first()->profesor : null;
            $profesorNombre = $profesor ? $profesor->nombre . ' ' . $profesor->apellido : 'Sin Docente';
            echo "   - ID: {$grupo->id} | Grupo {$grupo->identificador} - {$profesorNombre}\n";
        }
        echo "\n";
    }
    
    // Probar con un grupo específico
    $grupoId = 5; // Grupo con estudiantes
    
    if ($grupoId) {
        echo "🎯 PROBANDO CON GRUPO ID: {$grupoId}\n";
        
        $grupoResult = Grupo::with(['inscripciones.estudiante', 'inscripciones.calificaciones.tipoEvaluacion'])
            ->find($grupoId);
            
        if ($grupoResult) {
            $totalNotas = 0;
            
            foreach ($grupoResult->inscripciones as $inscripcion) {
                if ($inscripcion->estado !== 'activo') continue;
                if (!$inscripcion->estudiante) continue;
                
                $notaFinal = 0;
                $acumulado = 0;
                
                foreach ($inscripcion->calificaciones as $cal) {
                    if (!$cal->tipoEvaluacion) continue;
                    
                    $ponderacion = $cal->tipoEvaluacion->ponderacion;
                    $nota = $cal->nota;
                    
                    $puntos = ($nota / 20) * $ponderacion;
                    $acumulado += $puntos;
                }
                
                $notaFinal = round($acumulado, 2);
                $estado = $notaFinal >= 51 ? 'Aprobado' : 'Reprobado';
                
                $estudiantes[] = [
                    'nombres' => $inscripcion->estudiante->nombre . ' ' . $inscripcion->estudiante->apellido,
                    'codigo' => $inscripcion->estudiante->codigo_estudiante,
                    'nota_final' => $notaFinal,
                    'estado' => $estado
                ];
                
                if ($estado === 'Aprobado') $estadisticas['aprobados']++;
                else $estadisticas['reprobados']++;
                
                $totalNotas += $notaFinal;
            }
            
            $estadisticas['total'] = count($estudiantes);
            if ($estadisticas['total'] > 0) {
                $estadisticas['promedio_general'] = round($totalNotas / $estadisticas['total'], 2);
            }
        }
    }
    
    echo "📊 DATOS FINALES PARA LA VISTA:\n";
    echo "   materias: {$materias->count()} elementos\n";
    echo "   profesores: {$profesores->count()} elementos\n";
    echo "   grupos: {$grupos->count()} elementos\n";
    echo "   estudiantes: " . count($estudiantes) . " elementos\n";
    echo "   estadisticas: " . json_encode($estadisticas) . "\n";
    echo "   materiaId: {$materiaId}\n";
    echo "   grupoId: {$grupoId}\n\n";
    
    echo "✅ TODOS LOS DATOS ESTÁN DISPONIBLES\n";
    echo "La vista debería renderizar correctamente.\n\n";
    
    echo "🔍 POSIBLE PROBLEMA:\n";
    echo "1. Verificar que la ruta esté correctamente definida\n";
    echo "2. Verificar middleware de autenticación\n";
    echo "3. Revisar logs de Laravel para el error específico\n";
    echo "4. Probar accediendo directamente a la URL\n";
    
} catch (Exception $e) {
    echo "❌ ERROR AL PREPARAR DATOS:\n";
    echo "Mensaje: " . $e->getMessage() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
}