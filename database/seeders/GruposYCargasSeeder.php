<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Materia;
use App\Models\Profesor;
use App\Models\Grupo;
use App\Models\CargaAcademica;

class GruposYCargasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener las materias existentes
        $materias = Materia::all();
        
        if ($materias->count() == 0) {
            echo "❌ No hay materias en la base de datos. Crea materias primero.\n";
            return;
        }

        // Crear grupos para cada materia
        foreach ($materias as $materia) {
            // Crear 2 grupos por materia (A y B)
            $grupos = ['A', 'B'];
            
            foreach ($grupos as $identificador) {
                // Verificar si ya existe el grupo
                $existeGrupo = Grupo::where('materia_id', $materia->id)
                                  ->where('identificador', $identificador)
                                  ->exists();
                
                if (!$existeGrupo) {
                    Grupo::create([
                        'identificador' => $identificador,
                        'materia_id' => $materia->id,
                        'capacidad_maxima' => 30,
                        'estado' => 'activo'
                    ]);
                    echo "✅ Grupo {$identificador} creado para {$materia->nombre}\n";
                }
            }
        }

        // Obtener profesores y grupos para crear cargas académicas
        $profesores = Profesor::where('estado', 'activo')->get();
        $grupos = Grupo::where('estado', 'activo')->get();

        if ($profesores->count() == 0) {
            echo "❌ No hay profesores activos. Crea profesores primero.\n";
            return;
        }

        // Crear cargas académicas asignando profesores a grupos
        $profesorIndex = 0;
        foreach ($grupos as $grupo) {
            // Verificar si ya existe una carga académica para este grupo
            $existeCarga = CargaAcademica::where('grupo_id', $grupo->id)
                                       ->where('periodo', '2024-2')
                                       ->exists();
            
            if (!$existeCarga) {
                // Asignar profesor de forma rotativa
                $profesor = $profesores[$profesorIndex % $profesores->count()];
                
                CargaAcademica::create([
                    'profesor_id' => $profesor->id,
                    'grupo_id' => $grupo->id,
                    'periodo' => '2024-2',
                    'estado' => 'asignado'
                ]);
                
                echo "✅ Carga académica creada: {$profesor->nombre_completo} -> {$grupo->materia->nombre} Grupo {$grupo->identificador}\n";
                
                $profesorIndex++;
            }
        }

        echo "\n🎉 Proceso completado!\n";
        echo "Grupos creados: " . Grupo::count() . "\n";
        echo "Cargas académicas creadas: " . CargaAcademica::count() . "\n";
    }
}
