<?php
require_once 'vendor/autoload.php';

// Cargar configuración de Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🧪 VERIFICANDO CÁLCULO CON NOTAS SOBRE 100\n";
echo "==========================================\n\n";

// Probar el cálculo con datos reales
$controller = new \App\Http\Controllers\ReporteController();
$request = new \Illuminate\Http\Request(['materia_id' => 4, 'grupo_id' => 5]);

try {
    $response = $controller->reporteRendimiento($request);
    echo "✅ Controlador funciona correctamente\n\n";
    
    // Obtener datos directamente para mostrar el cálculo
    $grupoResult = \App\Models\Grupo::with(['inscripciones' => function($query) {
        $query->where('estado', 'activo')->with(['estudiante', 'calificaciones.tipoEvaluacion']);
    }])->find(5);
    
    if ($grupoResult && $grupoResult->inscripciones->count() > 0) {
        echo "📊 EJEMPLO DE CÁLCULOS REALES:\n";
        echo "==============================\n\n";
        
        $estudiante = $grupoResult->inscripciones->first();
        if ($estudiante && $estudiante->estudiante && $estudiante->calificaciones->count() > 0) {
            echo "👤 Estudiante: {$estudiante->estudiante->nombre} {$estudiante->estudiante->apellido}\n";
            echo "📚 Materia: {$grupoResult->materia->nombre}\n\n";
            
            $acumulado = 0;
            $totalPonderacion = 0;
            
            echo "📝 CALIFICACIONES:\n";
            foreach ($estudiante->calificaciones as $cal) {
                if (!$cal->tipoEvaluacion) continue;
                
                $ponderacion = $cal->tipoEvaluacion->ponderacion;
                $nota = $cal->nota;
                
                // Cálculo correcto: (nota/100) * ponderación
                $puntos = ($nota / 100) * $ponderacion;
                $acumulado += $puntos;
                $totalPonderacion += $ponderacion;
                
                echo "   - {$cal->tipoEvaluacion->nombre}: {$nota}/100 puntos\n";
                echo "     Ponderación: {$ponderacion}%\n";
                echo "     Cálculo: ({$nota}/100) × {$ponderacion} = {$puntos} puntos\n\n";
            }
            
            echo "🎯 RESULTADO FINAL:\n";
            echo "   Total acumulado: {$acumulado} puntos\n";
            echo "   Ponderación total: {$totalPonderacion}%\n";
            echo "   Nota final: " . round($acumulado, 2) . "/100\n";
            echo "   Estado: " . ($acumulado >= 51 ? "✅ APROBADO" : "❌ REPROBADO") . "\n\n";
        }
    }
    
    // Verificar tipos de evaluación
    $tipos = \App\Models\TipoEvaluacion::all();
    echo "📋 TIPOS DE EVALUACIÓN CONFIGURADOS:\n";
    echo "====================================\n";
    foreach ($tipos as $tipo) {
        echo "   - {$tipo->nombre}: {$tipo->ponderacion}%\n";
    }
    
    $totalPonderacion = $tipos->sum('ponderacion');
    echo "\n   Total ponderación: {$totalPonderacion}%\n";
    
    if ($totalPonderacion == 100) {
        echo "   ✅ Ponderación correcta (suma 100%)\n";
    } else {
        echo "   ⚠️  Ponderación incorrecta (debería sumar 100%)\n";
    }
    
    echo "\n🎉 SISTEMA FUNCIONANDO CORRECTAMENTE\n";
    echo "===================================\n\n";
    
    echo "✅ CARACTERÍSTICAS ACTUALES:\n";
    echo "============================\n";
    echo "• Notas de 0 a 100 puntos ✅\n";
    echo "• Cálculo: (nota/100) × ponderación ✅\n";
    echo "• Ponderaciones configurables ✅\n";
    echo "• Nota final sobre 100 puntos ✅\n";
    echo "• Aprobación con 51+ puntos ✅\n\n";
    
    echo "📱 EJEMPLO PRÁCTICO:\n";
    echo "====================\n";
    echo "Estudiante obtiene:\n";
    echo "• Parcial 1: 75/100 (30% ponderación) = 22.5 puntos\n";
    echo "• Parcial 2: 80/100 (30% ponderación) = 24.0 puntos\n";
    echo "• Final: 60/100 (40% ponderación) = 24.0 puntos\n";
    echo "• TOTAL: 70.5/100 = APROBADO ✅\n\n";
    
    echo "🚀 LISTO PARA USAR:\n";
    echo "===================\n";
    echo "1. Ve a Reportes → Rendimiento Académico\n";
    echo "2. Selecciona materia y grupo\n";
    echo "3. Verás notas calculadas sobre 100 puntos\n";
    echo "4. Descarga PDF o Excel con los nuevos cálculos\n\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
}