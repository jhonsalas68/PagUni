<?php

use App\Models\Profesor;
use App\Models\CargaAcademica;
use App\Models\Materia;
use App\Models\Grupo;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$profesor = Profesor::where('codigo_docente', 'PROF001')->first();

if (!$profesor) {
    echo "Profesor PROF001 NOT FOUND.\n";
    exit;
}

echo "Profesor found: " . $profesor->nombre_completo . " (ID: " . $profesor->id . ")\n";

$cargas = CargaAcademica::where('profesor_id', $profesor->id)->get();

echo "Cargas Academicas found: " . $cargas->count() . "\n";

foreach ($cargas as $carga) {
    echo "- Grupo ID: " . $carga->grupo_id . " | Periodo: " . $carga->periodo . " | Estado: " . $carga->estado . "\n";
    $grupo = Grupo::find($carga->grupo_id);
    if ($grupo) {
        $materia = Materia::find($grupo->materia_id);
        echo "  -> Materia: " . ($materia ? $materia->nombre : 'NULL') . "\n";
    }
}

$materias = Materia::whereIn('sigla', ['MAT101', 'FIS101', 'INF110'])->get();
echo "Materias created: " . $materias->count() . "\n";
foreach($materias as $m) {
    echo " - " . $m->nombre . " (ID: " . $m->id . ")\n";
}
