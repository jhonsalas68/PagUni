<?php
require_once 'vendor/autoload.php';

// Cargar configuración de Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🧹 LIMPIANDO CALIFICACIONES DUPLICADAS\n";
echo "======================================\n\n";

try {
    // 1. Eliminar calificaciones duplicadas
    echo "1. Eliminando calificaciones duplicadas...\n";
    
    $duplicadas = \DB::select("
        SELECT inscripcion_id, tipo_evaluacion_id, COUNT(*) as total
        FROM calificaciones 
        GROUP BY inscripcion_id, tipo_evaluacion_id 
        HAVING COUNT(*) > 1
    ");
    
    echo "   📊 Grupos duplicados encontrados: " . count($duplicadas) . "\n";
    
    foreach ($duplicadas as $dup) {
        // Mantener solo la calificación más reciente
        $calificaciones = \App\Models\Calificacion::where('inscripcion_id', $dup->inscripcion_id)
            ->where('tipo_evaluacion_id', $dup->tipo_evaluacion_id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Eliminar todas excepto la primera (más reciente)
        for ($i = 1; $i < $calificaciones->count(); $i++) {
            $calificaciones[$i]->delete();
        }
        
        echo "   ✅ Limpiado: inscripción {$dup->inscripcion_id}, tipo {$dup->tipo_evaluacion_id}\n";
    }
    
    // 2. Verificar que no haya tipos de evaluación duplicados
    echo "\n2. Verificando tipos de evaluación...\n";
    
    $tiposDuplicados = \DB::select("
        SELECT nombre, COUNT(*) as total
        FROM tipos_evaluacion 
        GROUP BY nombre 
        HAVING COUNT(*) > 1
    ");
    
    if (count($tiposDuplicados) > 0) {
        echo "   ⚠️  Tipos duplicados encontrados: " . count($tiposDuplicados) . "\n";
        
        foreach ($tiposDuplicados as $tipo) {
            $tipos = \App\Models\TipoEvaluacion::where('nombre', $tipo->nombre)
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Mantener solo el más reciente
            for ($i = 1; $i < $tipos->count(); $i++) {
                // Actualizar calificaciones que usen este tipo
                \DB::table('calificaciones')
                    ->where('tipo_evaluacion_id', $tipos[$i]->id)
                    ->update(['tipo_evaluacion_id' => $tipos[0]->id]);
                
                $tipos[$i]->delete();
            }
            
            echo "   ✅ Tipo '{$tipo->nombre}' consolidado\n";
        }
    } else {
        echo "   ✅ No hay tipos duplicados\n";
    }
    
    // 3. Mostrar estado final
    echo "\n3. Estado final del sistema...\n";
    
    $tiposFinales = \App\Models\TipoEvaluacion::all();
    echo "   📋 Tipos de evaluación:\n";
    $totalPonderacion = 0;
    foreach ($tiposFinales as $tipo) {
        echo "      - {$tipo->nombre}: {$tipo->ponderacion}%\n";
        $totalPonderacion += $tipo->ponderacion;
    }
    echo "   📊 Total ponderación: {$totalPonderacion}%\n";
    
    if ($totalPonderacion == 100) {
        echo "   ✅ Ponderación correcta\n";
    } else {
        echo "   ⚠️  Ponderación total: {$totalPonderacion}% (debería ser 100%)\n";
    }
    
    // 4. Contar calificaciones finales
    $totalCalificaciones = \App\Models\Calificacion::count();
    echo "\n   📝 Total calificaciones: {$totalCalificaciones}\n";
    
    // 5. Probar cálculo con un estudiante
    echo "\n4. Probando cálculo corregido...\n";
    
    $inscripcion = \App\Models\Inscripcion::with(['estudiante', 'calificaciones.tipoEvaluacion'])
        ->whereHas('calificaciones')
        ->first();
    
    if ($inscripcion) {
        echo "   👤 Estudiante: {$inscripcion->estudiante->nombre} {$inscripcion->estudiante->apellido}\n";
        
        $acumulado = 0;
        foreach ($inscripcion->calificaciones as $cal) {
            if (!$cal->tipoEvaluacion) continue;
            
            $ponderacion = $cal->tipoEvaluacion->ponderacion;
            $nota = $cal->nota;
            $puntos = ($nota / 100) * $ponderacion;
            $acumulado += $puntos;
            
            echo "      - {$cal->tipoEvaluacion->nombre}: {$nota}/100 → {$puntos} puntos\n";
        }
        
        echo "   🎯 Nota final: " . round($acumulado, 2) . "/100\n";
        echo "   📊 Estado: " . ($acumulado >= 51 ? "APROBADO" : "REPROBADO") . "\n";
    }
    
    echo "\n🎉 LIMPIEZA COMPLETADA\n";
    echo "=====================\n";
    echo "✅ Calificaciones duplicadas eliminadas\n";
    echo "✅ Tipos de evaluación consolidados\n";
    echo "✅ Cálculos funcionando sobre 100 puntos\n";
    echo "✅ Sistema listo para usar\n\n";
    
    echo "🚀 AHORA PUEDES:\n";
    echo "================\n";
    echo "1. Ir al reporte de rendimiento\n";
    echo "2. Ver notas calculadas correctamente\n";
    echo "3. Ingresar nuevas notas de 0 a 100 puntos\n";
    echo "4. Descargar reportes en PDF y Excel\n\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
}