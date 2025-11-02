<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Materia;
use App\Models\Carrera;

class MateriasCompletasSeeder extends Seeder
{
    public function run(): void
    {
        $carreras = Carrera::all();

        foreach ($carreras as $carrera) {
            $this->crearMateriasPorCarrera($carrera);
        }
    }

    private function crearMateriasPorCarrera($carrera)
    {
        switch ($carrera->nombre) {
            case 'Ingeniería de Sistemas':
                $this->crearMateriasIngenieriaSistemas($carrera->id);
                break;
            case 'Ingeniería Industrial':
                $this->crearMateriasIngenieriaIndustrial($carrera->id);
                break;
            case 'Medicina General':
                $this->crearMateriasMedicina($carrera->id);
                break;
            case 'Administración de Empresas':
                $this->crearMateriasAdministracion($carrera->id);
                break;
            case 'Licenciatura en Ma':
                $this->crearMateriasMatematicas($carrera->i);
                break;
            default:
                // Para carreras no especificadas, crear materiasas
                $this-);
         ;
     
}

    p
    {
        $materias = [
            // Primer Semestre
            ['codigo' => 'ISI-101', 'nombre' => 'Fundamentos de Programación', 'semestre' => 1, 'creditos' => 4, 'horas_teoricas' => 2, 'horas_prac
            ['codigo' => 'ISI-102', 'nombre' => 'Matemáticas para Ingeniería', 'semestre' => 1, 'creditos' => 4, 'horas_teoricas' => 4, 'horas_practicas' => 2],
            ['codigo' => 'ISI-103', 'nombre' => 'Lógica Computacional', 'semestre' => 1, 'creditos' => 3, 'horas_teoricas' => 2, 'horas_practicas' => 2],
            ['codigo' => 'ISI-104', 'nombre' => 'Introducción a la Ingeniería', 'semestre' => 1, 'creditos' => 2, 'horas_teoricas' => 2, 'horas_practicas' => 1],
            
            tre
            ['codigo' => 'ISI
            ['codigo' => 'ISI-202', 'nombre' => 'Base de Datos', 'semestre' => 2, 'creditos' => 4, 'horas_teoricas' => 3, 'horas_practicas' => 3],
            ['codigo' => 'ISI-203', 'nombre' => 'Estructuras de Datos', 'semestre' => 2, 'creditos' => 4, 'horas_teoricas' => 2, 'horas_practicas' => 4],
            ['codigo' => 'ISI-204', 'nombre' => 'Cálculo Diferencial', 'semestre' => 2, 'creditos' => 4, 'horas_teoricas' => 4, 'horas_practicas' => 2],
            
            // Tercer Semestre
           => 4],
2],
            ['codigo' => 'ISI-303', 'nombre' => 'Sistem' => 3],
     

        $this->insertarMaterias($materias, $carreraId);
    }

    private function crearMateId)
    {
        $materias = [
            // Primer Semestre
            ['codigo' => 'IIN-101', 'nombre' => 'Introducción a la Ingeniería Industrial', 'semestre' => 1, 'creditos' => 3, 'horas_teoricas' => 3, ,
            ['codigo' => 'IIN-102', 'nombre' => 'Matemáticas Aplicadas', 'semestre' => 1, 'creditos' => 4, 'horas_teoricas' => 4, 'horas_practicas' => 2],
            ],
            ['codigo' => 'IIN-13],
            
            // Segundo Semestre
            ['codigo' => 'IIN-201', 'nombre' => 'Procesos Industriales', 'semestre' => 2, 'creditos' => 4, 'horas_teoricas' => 2, 'horas_pract
            ['codigo' => 'IIN-202', 'nombre' => 'Control de Calidad', 'semestre' => 2, 'creditos' => 3, 'horas_teoricas' => 2, 'horas_practicas' => 2],
            ['codigo' => 'IIN-203', 'nombre' => 'Estadística Industrial', 'semestre' => 2, 'creditos' => 3, 'horas_teoricas' => 2, 'horas_practicas' => 2],
            ],
            
            // Tercer Semestre
            ['codigo' => 'IIN-301', 'nombre' => 'Investigación de Operaciones', 'semestre' => 3, 'creditos' => 4, 'horas_teoricas' => 3, 'horas_practicas' =3],
            ['codigo' => 'IIN-302', 'nombre' => 'Gestión de Producción', 'semestre' => 3, 'creditos' => 4, 'horas_teoricas' => 3, 'horas_practicas' => ,
        ];

        $taId);
  }

    praId)

        $materias = [
     estre
            ['codigo'> 6],
            ['codigo' => 'MED-102', 'nombre' => 'Biología Celu 3],
            ['codigo' => 'MED-1],
            ['codigo' => 'MED-104', 'nombre' => 'Histología', 'semestre' => 1, 'creditos' => 4, 'horas_teoricas' => 2, 'horas_practicas' => 4],
            
            // Segundo Semestre
            ['codigo' => 'MED-201', 'nombre' => 'Fisiología Humana', 'semestre' => 2, 'creditos' => 5, 'horas_teoricas' => 4, 'horas_practicas' => 4],
            ['codigo' => 'MED-202', 'nombre' => 'Anatomía Humana II', 'semestre' => 2, 'creditos' => 5, 'horas_teoricas' => 3, 'horas_practicas' => 5],
            3],
            ['codigo' => 'MED-=> 2],
            
            // Tercer Semestre
            ['codigo' => 'MED-301', 'nombre' => 'Patología General', 'semestre' => 3, 'creditos' => 5, 'horas_teoricas' => 4, 'horas_practicas' => 4],
            ['codigo' => 'MED-302', 'nombre' => 'Farmacología Básica', 'semestre' => 3, 'creditos' => 4, 'horas_teoricas' => 3, 'horas_practicas' 
            ['codigo' => 'MED-303', 'nombre' => 'Microbiología', 'semestre' => 3, 'creditos' => 4, 'horas_teoricas' => 2, 'horas_practicas' => 4],
        ];

        $this->insertarMaterias($materias, $carreraId);
    }

    private function crearMateriasAdministracion($carr
    {
        $materias = [
            // Primer Semestre
            ['codigo' => 'ADM-101', 'nombre' => 'Fundamentos de Administración', 'semestre' => 1, 'creditos' => 4, 'horas_teoricas' => 3, 'horas_pract> 2],
            ['codigo' => 'ADM-102', 'nombre' => 'Contabilidad General', 'semestre' => 1, 'creditos' => 4, 'horas_teoricas' => 3, 'horas_practicas' =>
            ['codigo' => 'ADM-103', 'nombre' => 'Matemáticas Financieras', 'semestre' => 1, 'creditos' => 3, 'horas_teoricas' => 2, 'horas_practicas'2],
            ['codigo' => 'ADM-104', 'nombre' => 'Comunicación Empresarial', 'semestre' => 1, 'creditos' => 3, 'horas_teoricas' => 2, 'horas_pract=> 2],
            
            emestre
            ['codigo' => 'ADM-2 => 2],
            ['codigo' => 'ADM-202', 'nombre' => 'Marketing Estratégico', 'semestre' => 2, 'creditos' => 4, 'horas_teoricas' => 3, 'horas_practicas' => 2],
            ['codigo' => 'ADM-203', 'nombre' => 'Finanzas Corporativas', 'semestre' => 2, 'creditos' => 4, 'horas_teoricas' => 3, 'horas_practica2],
            ['codigo' => 'ADM-204', 'nombre' => 'Estadística Empresarial', 'semestre' => 2, 'creditos' => 3, 'horas_teoricas' => 2, 'horas_practic],
        ];

        $thireraId);
    }

    private function crearMateriasMatematicas($carreraId)
    {
        $materias = [
            // Primer Semestre
          
,
            ['codigo' => 'MAT-103', 'nombre' => 'Geomet],
           
 Semestre
            ['codigo' => 'MAT-201', 'nombre' => 'Cálculo Mul> 3],
      2],
            ['codigo'> 2],
        ];

        $this->insertarMaterias($materias, $carreraId);
    }

    private function crearMateriasGenericas($carreraId)
    {
        $materias = [
            ['codigo' => 'GEN-101', 'nombre' => 'Introducción a la Carrera', 'semestre' => 1, 'creditos' => 3, 'horas_teoricas' => 2, 'horas_practicas' => 2],
            ['codigo' => 'GEN-102', 'nombre' => 'Metodología de la Investigación', 'semestre' => 1, 'creditos' => 3, 'horas_teoricas' => 2, 'horas_practicas 2],
            ['codigo' => 'GEN-201', 'nombre' => 'Ética Profesional', 'semestre' => 2, 'creditos' => 2, 'horas_teoricas' => 2, 'horas_practicas' =>1],
        ];

        $thiaId);
    }

    private function insertarMaterias($materias, $carreraId)
    {
        foreach ($materias as $materia) {
            // Verificar si ya existe la materia
          digo'])
raId)
                            ->exists();
      

                Materia::create([
     go'],
                    'nombre' => $materia[],
                    'carrera_id' => $carreraId,
                    'semestre' => $materia['semestre'],
            itos'],
                    'horas_s'],
                    '
                ]);
                
                echo "✅ Creada: {$materia['codigo']} - 
            } else {
                echo "⚠️  Ya existe: {$materia['codigo']}\n";
            }
        }
    }
}           } else {
                echo "⏭️  Saltando: {$materia['codigo']} - {$materia['nombre']} (ya existe)\n";
            }
        }
    }
}