<?php
require_once 'vendor/autoload.php';

// Cargar configuración de Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Profesor;
use App\Models\CargaAcademica;
use App\Models\Horario;

echo "🔍 DEBUG HORARIOS PROF001\n";
echo "========================\n\n";

$profesor = Profesor::where('codigo_docente', 'PROF001')->first();
echo "👨‍🏫 Profesor: {$profesor->nombre} {$profesor->apellido} (ID: {$profesor->id})\n\n";

$cargas = CargaAcademica::where('profesor_id', $profesor->id)->get();
echo "📚 Cargas académicas encontradas: {$cargas->count()}\n";

foreach ($cargas as $carga) {
    echo "   - Carga ID: {$carga->id}\n";
    echo "     Grupo ID: {$carga->grupo_id}\n";
    echo "     Periodo: {$carga->periodo}\n";
    
    // Buscar horarios para esta carga
    $horarios = Horario::where('carga_academica_id', $carga->id)->get();
    echo "     Horarios: {$horarios->count()}\n";
    
    foreach ($horarios as $horario) {
        echo "       * Horario ID: {$horario->id}\n";
        echo "         Días: {$horario->dias_semana}\n";
        echo "         Hora: {$horario->hora_inicio} - {$horario->hora_fin}\n";
        echo "         Aula ID: {$horario->aula_id}\n";
    }
    echo "\n";
}

// Verificar todos los horarios en la base de datos
$todosLosHorarios = Horario::all();
echo "🕐 Total de horarios en BD: {$todosLosHorarios->count()}\n";

if ($todosLosHorarios->count() > 0) {
    echo "Primeros 5 horarios:\n";
    foreach ($todosLosHorarios->take(5) as $h) {
        echo "   - ID: {$h->id}, Carga: {$h->carga_academica_id}, Días: {$h->dias_semana}\n";
    }
}

echo "\n🔍 Verificando cargas con horarios:\n";
$cargasConHorarios = CargaAcademica::whereHas('horarios')->where('profesor_id', $profesor->id)->get();
echo "Cargas con horarios: {$cargasConHorarios->count()}\n";

foreach ($cargasConHorarios as $carga) {
    $horarios = $carga->horarios;
    echo "   - Carga {$carga->id}: {$horarios->count()} horarios\n";
}