<?php

use App\Models\Profesor;
use App\Models\Materia;
use App\Models\Grupo;
use App\Models\CargaAcademica;
use App\Models\Estudiante;
use App\Models\Inscripcion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

Config::set('database.default', 'mysql');

$profesor = Profesor::where('codigo_docente', 'PROF001')->firstOrFail();
$periodoActual = '1-2025';

$materias = [
    ['sigla' => 'MAT101', 'nombre' => 'Calculo I', 'nivel' => 1],
    ['sigla' => 'FIS101', 'nombre' => 'Fisica I', 'nivel' => 1],
    ['sigla' => 'INF110', 'nombre' => 'Introduccion a la Informatica', 'nivel' => 1],
];

foreach ($materias as $m) {
    echo "Processing " . $m['nombre'] . "\n";
    $materia = Materia::firstOrCreate(
        ['sigla' => $m['sigla']],
        [
            'nombre' => $m['nombre'],
            'nivel' => $m['nivel'],
            'descripcion' => $m['nombre'],
            'estado' => 'activo'
        ]
    );

    $grupo = Grupo::firstOrCreate(
        ['identificador' => 'A', 'materia_id' => $materia->id],
        [
            'capacidad_maxima' => 50, 
            'estado' => 'activo'
        ] // Reduced attributes to avoid mass assignment errors if column missing
    );

    // Manual update if needed for missing columns in fillable
    $grupo->cupo_maximo = 50;
    if (!$grupo->cupo_actual) $grupo->cupo_actual = 0;
    $grupo->permite_inscripcion = true;
    $grupo->save();

    CargaAcademica::updateOrCreate(
        [
            'profesor_id' => $profesor->id,
            'grupo_id' => $grupo->id,
            'periodo' => $periodoActual
        ],
        ['estado' => 'asignado']
    );

    $estudiantes = Estudiante::all();
    foreach ($estudiantes as $estudiante) {
        $inscripcion = Inscripcion::where('estudiante_id', $estudiante->id)
            ->where('grupo_id', $grupo->id)
            ->first();
            
        if (!$inscripcion) {
            $inscripcion = new Inscripcion();
            $inscripcion->estudiante_id = $estudiante->id;
            $inscripcion->grupo_id = $grupo->id;
            $inscripcion->fecha_inscripcion = Carbon::now();
            $inscripcion->periodo_academico = $periodoActual;
            $inscripcion->estado = 'activo';
            $inscripcion->save();
            
            $grupo->increment('cupo_actual');
        }
    }
}
echo "Done.\n";
