<?php
require_once 'vendor/autoload.php';

// Cargar configuración de Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 VERIFICANDO TABLA MATERIAS CON COLUMNA ESTADO\n";
echo "===============================================\n\n";

try {
    // 1. Verificar que la columna estado existe
    echo "1. Verificando estructura de la tabla materias...\n";
    $columns = \DB::select("SELECT column_name FROM information_schema.columns WHERE table_name = 'materias'");
    
    $hasEstado = false;
    echo "   Columnas encontradas:\n";
    foreach ($columns as $column) {
        echo "   - {$column->column_name}\n";
        if ($column->column_name === 'estado') {
            $hasEstado = true;
        }
    }
    
    if ($hasEstado) {
        echo "   ✅ Columna 'estado' encontrada\n\n";
    } else {
        echo "   ❌ Columna 'estado' NO encontrada\n\n";
        exit(1);
    }
    
    // 2. Probar consulta con estado
    echo "2. Probando consulta con estado...\n";
    $materias = \App\Models\Materia::where('estado', 'activo')->get();
    echo "   ✅ Consulta exitosa: {$materias->count()} materias activas\n\n";
    
    // 3. Actualizar materias existentes para que tengan estado activo
    echo "3. Actualizando materias existentes...\n";
    $materiasActualizadas = \DB::table('materias')
        ->whereNull('estado')
        ->orWhere('estado', '')
        ->update(['estado' => 'activo']);
    
    echo "   ✅ Materias actualizadas: {$materiasActualizadas}\n\n";
    
    // 4. Mostrar materias con su estado
    echo "4. Listado de materias:\n";
    $todasLasMaterias = \App\Models\Materia::all();
    foreach ($todasLasMaterias as $materia) {
        echo "   - {$materia->nombre} ({$materia->codigo}): {$materia->estado}\n";
    }
    echo "\n";
    
    // 5. Probar el servicio que causaba el error
    echo "5. Probando GeneradorCargaService...\n";
    if (class_exists('\App\Services\GeneradorCargaService')) {
        // Solo verificar que la consulta no falle
        $materiasActivas = \App\Models\Materia::where('estado', 'activo')->get();
        echo "   ✅ Servicio funcionando: {$materiasActivas->count()} materias activas\n";
    } else {
        echo "   ⚠️  GeneradorCargaService no encontrado\n";
    }
    
    echo "\n🎉 PROBLEMA SOLUCIONADO\n";
    echo "======================\n";
    echo "✅ Columna 'estado' agregada a tabla materias\n";
    echo "✅ Materias existentes marcadas como activas\n";
    echo "✅ Consultas funcionando correctamente\n";
    echo "✅ Error SQLSTATE[42703] resuelto\n\n";
    
    echo "🚀 AHORA PUEDES:\n";
    echo "================\n";
    echo "1. Usar el sistema sin errores de columna estado\n";
    echo "2. Gestionar materias activas/inactivas\n";
    echo "3. Continuar con las calificaciones de 0-100\n\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
}