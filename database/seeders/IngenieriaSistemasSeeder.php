<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Materia;
use App\Models\Carrera;

class IngenieriaSistemasSeeder extends Seeder
{
    public function run(): void
    {
        $carrera = Carrera::where('nombre', 'Ingeniería de Sistemas')->first();
        
        if (!$carrera) {
            echo "❌ No se encontró la carrera 'Ingeniería de Sistemas'\n";
            return;
        }

        $materias = [
            // Primer Semestre
            ['codigo' => 'INGS-101', 'nombre' => 'Fundamentos de Sistemas', 'semestre' => 1, 'creditos' => 4, 'horas_teoricas' => 3, 'horas_practicas' => 2],
            ['codigo' => 'INGS-102', 'nombre' => 'Matemáticas I', 'semestre' => 1, 'creditos' => 4, 'horas_teoricas' => 4, 'horas_practicas' => 2],
            ['codigo' => 'INGS-103', 'nombre' => 'Física Aplicada', 'semestre' => 1, 'creditos' => 4, 'horas_teoricas' => 3, 'horas_practicas' => 2],
            ['codigo' => 'INGS-104', 'nombre' => 'Introducción a la Programación', 'semestre' => 1, 'creditos' => 4, 'horas_teoricas' => 2, 'horas_practicas' => 4],
            ['codigo' => 'INGS-105', 'nombre' => 'Comunicación Técnica', 'semestre' => 1, 'creditos' => 2, 'horas_teoricas' => 2, 'horas_practicas' => 1],
            
            // Segundo Semestre
            ['codigo' => 'INGS-201', 'nombre' => 'Análisis de Sistemas', 'semestre' => 2, 'creditos' => 4, 'horas_teoricas' => 3, 'horas_practicas' => 3],
            ['codigo' => 'INGS-202', 'nombre' => 'Matemáticas II', 'semestre' => 2, 'creditos' => 4, 'horas_teoricas' => 4, 'horas_practicas' => 2],
            ['codigo' => 'INGS-203', 'nombre' => 'Programación Avanzada', 'semestre' => 2, 'creditos' => 4, 'horas_teoricas' => 2, 'horas_practicas' => 4],
            ['codigo' => 'INGS-204', 'nombre' => 'Estadística Aplicada', 'semestre' => 2, 'creditos' => 3, 'horas_teoricas' => 3, 'horas_practicas' => 1],
            ['codigo' => 'INGS-205', 'nombre' => 'Metodología de Investigación', 'semestre' => 2, 'creditos' => 2, 'horas_teoricas' => 2, 'horas_practicas' => 1],
            
            // Tercer Semestre
            ['codigo' => 'INGS-301', 'nombre' => 'Diseño de Sistemas', 'semestre' => 3, 'creditos' => 4, 'horas_teoricas' => 3, 'horas_practicas' => 3],
            ['codigo' => 'INGS-302', 'nombre' => 'Base de Datos', 'semestre' => 3, 'creditos' => 4, 'horas_teoricas' => 3, 'horas_practicas' => 3],
            ['codigo' => 'INGS-303', 'nombre' => 'Redes y Comunicaciones', 'semestre' => 3, 'creditos' => 4, 'horas_teoricas' => 3, 'horas_practicas' => 2],
            ['codigo' => 'INGS-304', 'nombre' => 'Ingeniería de Software', 'semestre' => 3, 'creditos' => 4, 'horas_teoricas' => 3, 'horas_practicas' => 3],
            ['codigo' => 'INGS-305', 'nombre' => 'Sistemas Operativos', 'semestre' => 3, 'creditos' => 3, 'horas_teoricas' => 2, 'horas_practicas' => 2],
            
            // Cuarto Semestre
            ['codigo' => 'INGS-401', 'nombre' => 'Arquitectura de Sistemas', 'semestre' => 4, 'creditos' => 4, 'horas_teoricas' => 3, 'horas_practicas' => 3],
            ['codigo' => 'INGS-402', 'nombre' => 'Gestión de Proyectos', 'semestre' => 4, 'creditos' => 4, 'horas_teoricas' => 3, 'horas_practicas' => 2],
            ['codigo' => 'INGS-403', 'nombre' => 'Seguridad de Sistemas', 'semestre' => 4, 'creditos' => 3, 'horas_teoricas' => 2, 'horas_practicas' => 2],
            ['codigo' => 'INGS-404', 'nombre' => 'Inteligencia de Negocios', 'semestre' => 4, 'creditos' => 4, 'horas_teoricas' => 3, 'horas_practicas' => 3],
            ['codigo' => 'INGS-405', 'nombre' => 'Ética Profesional', 'semestre' => 4, 'creditos' => 2, 'horas_teoricas' => 2, 'horas_practicas' => 0],
        ];

        foreach ($materias as $materia) {
            $existe = Materia::where('codigo', $materia['codigo'])->exists();
            
            if (!$existe) {
                try {
                    Materia::create([
                        'codigo' => $materia['codigo'],
                        'nombre' => $materia['nombre'],
                        'carrera_id' => $carrera->id,
                        'semestre' => $materia['semestre'],
                        'creditos' => $materia['creditos'],
                        'horas_teoricas' => $materia['horas_teoricas'],
                        'horas_practicas' => $materia['horas_practicas'],
                    ]);
                    echo "✅ Creada: {$materia['codigo']} - {$materia['nombre']}\n";
                } catch (\Exception $e) {
                    echo "⚠️  Error: {$materia['codigo']} - {$e->getMessage()}\n";
                }
            } else {
                echo "⏭️  Ya existe: {$materia['codigo']} - {$materia['nombre']}\n";
            }
        }
        
        echo "\n🎓 Materias agregadas a Ingeniería de Sistemas\n";
    }
}