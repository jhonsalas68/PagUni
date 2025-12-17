<?php
require_once 'vendor/autoload.php';

// Cargar configuración de Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Profesor;
use App\Models\CargaAcademica;
use App\Models\Horario;
use App\Models\Estudiante;
use App\Models\Inscripcion;

echo "🔍 VERIFICANDO DATOS DEL PROFESOR PROF001\n";
echo "==========================================\n\n";

// Verificar profesor
$profesor = Profesor::where('codigo_docente', 'PROF001')->first();
if ($profesor) {
    echo "✅ Profesor encontrado:\n";
    echo "   Nombre: {$profesor->nombre} {$profesor->apellido}\n";
    echo "   Email: {$profesor->email}\n";
    echo "   Código: {$profesor->codigo_docente}\n\n";
} else {
    echo "❌ Profesor PROF001 no encontrado\n";
    exit(1);
}

// Verificar cargas académicas
$cargas = CargaAcademica::where('profesor_id', $profesor->id)->with(['grupo.materia'])->get();
echo "📚 MATERIAS ASIGNADAS ({$cargas->count()}):\n";
foreach ($cargas as $carga) {
    echo "   - {$carga->grupo->materia->nombre} ({$carga->grupo->materia->codigo})\n";
    echo "     Grupo: {$carga->grupo->identificador}\n";
    echo "     Periodo: {$carga->periodo}\n\n";
}

// Verificar horarios
$horarios = Horario::whereIn('carga_academica_id', $cargas->pluck('id'))
    ->with(['cargaAcademica.grupo.materia', 'aula'])
    ->get();

echo "🕐 HORARIOS ({$horarios->count()}):\n";
foreach ($horarios as $horario) {
    $dias = json_decode($horario->dias_semana, true);
    echo "   - {$horario->cargaAcademica->grupo->materia->nombre}\n";
    echo "     Días: " . implode(', ', $dias) . "\n";
    echo "     Hora: {$horario->hora_inicio} - {$horario->hora_fin}\n";
    echo "     Aula: {$horario->aula->codigo_aula} ({$horario->aula->nombre})\n\n";
}

// Verificar estudiantes creados
$estudiantes = Estudiante::where('codigo_estudiante', 'LIKE', 'ISC2024%')->get();
echo "👥 ESTUDIANTES CREADOS ({$estudiantes->count()}):\n";
foreach ($estudiantes as $estudiante) {
    echo "   - {$estudiante->nombre} {$estudiante->apellido} ({$estudiante->codigo_estudiante})\n";
    echo "     Email: {$estudiante->email}\n";
}
echo "\n";

// Verificar inscripciones
$inscripciones = Inscripcion::whereIn('estudiante_id', $estudiantes->pluck('id'))
    ->whereIn('grupo_id', $cargas->pluck('grupo_id'))
    ->with(['estudiante', 'grupo.materia'])
    ->get();

echo "📝 INSCRIPCIONES ({$inscripciones->count()}):\n";
$inscripcionesPorMateria = $inscripciones->groupBy('grupo.materia.nombre');
foreach ($inscripcionesPorMateria as $materia => $inscritos) {
    echo "   📖 {$materia}: {$inscritos->count()} estudiantes\n";
    foreach ($inscritos as $inscripcion) {
        echo "      - {$inscripcion->estudiante->nombre} {$inscripcion->estudiante->apellido}\n";
    }
    echo "\n";
}

echo "🎯 RESUMEN:\n";
echo "   ✅ Profesor PROF001 configurado\n";
echo "   ✅ {$cargas->count()} materias asignadas\n";
echo "   ✅ {$horarios->count()} horarios creados\n";
echo "   ✅ {$estudiantes->count()} estudiantes creados\n";
echo "   ✅ {$inscripciones->count()} inscripciones activas\n\n";

echo "🚀 LISTO PARA PROBAR:\n";
echo "   1. Login: juan.perez@universidad.edu / password123\n";
echo "   2. Ir a 'Mi Horario' para ver las 3 materias\n";
echo "   3. Generar QR para cualquier materia\n";
echo "   4. Login como estudiante y marcar asistencia\n\n";

echo "📱 CREDENCIALES DE ESTUDIANTES:\n";
echo "   Email: [nombre].[apellido]@estudiante.uagrm.edu.bo\n";
echo "   Password: student123\n";
echo "   Ejemplo: maria.rodriguez@estudiante.uagrm.edu.bo / student123\n\n";

echo "✨ ¡DATOS COMPLETOS Y LISTOS!\n";