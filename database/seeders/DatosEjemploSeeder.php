<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Facultad;
use App\Models\Carrera;
use App\Models\Materia;
use App\Models\Profesor;
use App\Models\Grupo;
use App\Models\CargaAcademica;
use App\Models\Aula;
use App\Models\Estudiante;
use Illuminate\Support\Facades\Hash;

class DatosEjemploSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear Facultades
        $facultades = [
            [
                'codigo' => 'FING',
                'nombre' => 'Facultad de Ingeniería',
                'descripcion' => 'Facultad dedicada a las carreras de ingeniería'
            ],
            [
                'codigo' => 'FMED',
                'nombre' => 'Facultad de Medicina',
                'descripcion' => 'Facultad de ciencias médicas'
            ],
            [
                'codigo' => 'FADM',
                'nombre' => 'Facultad de Administración',
                'descripcion' => 'Facultad de ciencias administrativas y económicas'
            ]
        ];

        foreach ($facultades as $facultadData) {
            Facultad::create($facultadData);
        }

        // Crear Carreras
        $carreras = [
            [
                'codigo' => 'ING-SIS',
                'nombre' => 'Ingeniería de Sistemas',
                'facultad_id' => 1,
                'duracion_semestres' => 10,
                'descripcion' => 'Carrera enfocada en desarrollo de software y sistemas'
            ],
            [
                'codigo' => 'ING-IND',
                'nombre' => 'Ingeniería Industrial',
                'facultad_id' => 1,
                'duracion_semestres' => 10,
                'descripcion' => 'Carrera enfocada en optimización de procesos industriales'
            ],
            [
                'codigo' => 'MED-GEN',
                'nombre' => 'Medicina General',
                'facultad_id' => 2,
                'duracion_semestres' => 12,
                'descripcion' => 'Carrera de medicina general'
            ],
            [
                'codigo' => 'ADM-EMP',
                'nombre' => 'Administración de Empresas',
                'facultad_id' => 3,
                'duracion_semestres' => 8,
                'descripcion' => 'Carrera enfocada en gestión empresarial'
            ]
        ];

        foreach ($carreras as $carreraData) {
            Carrera::create($carreraData);
        }

        // Crear Materias
        $materias = [
            // Ingeniería de Sistemas
            [
                'codigo' => 'SIS-101',
                'nombre' => 'Programación I',
                'carrera_id' => 1,
                'semestre' => 1,
                'creditos' => 4,
                'horas_teoricas' => 3,
                'horas_practicas' => 2,
                'descripcion' => 'Introducción a la programación'
            ],
            [
                'codigo' => 'SIS-102',
                'nombre' => 'Matemáticas Discretas',
                'carrera_id' => 1,
                'semestre' => 1,
                'creditos' => 3,
                'horas_teoricas' => 4,
                'horas_practicas' => 0,
                'descripcion' => 'Fundamentos matemáticos para sistemas'
            ],
            [
                'codigo' => 'SIS-201',
                'nombre' => 'Programación II',
                'carrera_id' => 1,
                'semestre' => 2,
                'creditos' => 4,
                'horas_teoricas' => 3,
                'horas_practicas' => 2,
                'descripcion' => 'Programación orientada a objetos'
            ],
            // Ingeniería Industrial
            [
                'codigo' => 'IND-101',
                'nombre' => 'Introducción a la Ingeniería Industrial',
                'carrera_id' => 2,
                'semestre' => 1,
                'creditos' => 3,
                'horas_teoricas' => 3,
                'horas_practicas' => 1,
                'descripcion' => 'Conceptos básicos de ingeniería industrial'
            ],
            [
                'codigo' => 'IND-102',
                'nombre' => 'Estadística I',
                'carrera_id' => 2,
                'semestre' => 1,
                'creditos' => 4,
                'horas_teoricas' => 3,
                'horas_practicas' => 2,
                'descripcion' => 'Fundamentos de estadística'
            ]
        ];

        foreach ($materias as $materiaData) {
            Materia::create($materiaData);
        }

        // Crear Profesores
        $profesores = [
            [
                'codigo_docente' => 'PROF001',
                'nombre' => 'Juan Carlos',
                'apellido' => 'Rodríguez',
                'email' => 'juan.rodriguez@universidad.edu',
                'telefono' => '555-0001',
                'cedula' => '12345678',
                'especialidad' => 'Programación',
                'tipo_contrato' => 'tiempo_completo',
                'password' => Hash::make('password123'),
                'estado' => 'activo'
            ],
            [
                'codigo_docente' => 'PROF002',
                'nombre' => 'María Elena',
                'apellido' => 'García',
                'email' => 'maria.garcia@universidad.edu',
                'telefono' => '555-0002',
                'cedula' => '87654321',
                'especialidad' => 'Matemáticas',
                'tipo_contrato' => 'tiempo_completo',
                'password' => Hash::make('password123'),
                'estado' => 'activo'
            ],
            [
                'codigo_docente' => 'PROF003',
                'nombre' => 'Carlos Alberto',
                'apellido' => 'Mendoza',
                'email' => 'carlos.mendoza@universidad.edu',
                'telefono' => '555-0003',
                'cedula' => '11223344',
                'especialidad' => 'Ingeniería Industrial',
                'tipo_contrato' => 'catedra',
                'password' => Hash::make('password123'),
                'estado' => 'activo'
            ],
            [
                'codigo_docente' => 'PROF004',
                'nombre' => 'Ana Sofía',
                'apellido' => 'López',
                'email' => 'ana.lopez@universidad.edu',
                'telefono' => '555-0004',
                'cedula' => '44332211',
                'especialidad' => 'Estadística',
                'tipo_contrato' => 'tiempo_completo',
                'password' => Hash::make('password123'),
                'estado' => 'activo'
            ]
        ];

        foreach ($profesores as $profesorData) {
            Profesor::create($profesorData);
        }

        // Crear Aulas
        $aulas = [
            [
                'codigo_aula' => 'A101',
                'nombre' => 'Aula de Sistemas 1',
                'tipo_aula' => 'aula',
                'edificio' => 'Edificio A',
                'piso' => 1,
                'capacidad' => 30,
                'descripcion' => 'Aula equipada con computadores',
                'equipamiento' => ['proyector', 'computadores', 'aire_acondicionado'],
                'estado' => 'disponible',
                'tiene_aire_acondicionado' => true,
                'tiene_proyector' => true,
                'tiene_computadoras' => true,
                'acceso_discapacitados' => true
            ],
            [
                'codigo_aula' => 'A102',
                'nombre' => 'Aula de Matemáticas',
                'tipo_aula' => 'aula',
                'edificio' => 'Edificio A',
                'piso' => 1,
                'capacidad' => 40,
                'descripcion' => 'Aula para clases teóricas',
                'equipamiento' => ['proyector', 'pizarra'],
                'estado' => 'disponible',
                'tiene_aire_acondicionado' => true,
                'tiene_proyector' => true,
                'tiene_computadoras' => false,
                'acceso_discapacitados' => true
            ],
            [
                'codigo_aula' => 'LAB01',
                'nombre' => 'Laboratorio de Programación',
                'tipo_aula' => 'laboratorio',
                'edificio' => 'Edificio B',
                'piso' => 2,
                'capacidad' => 25,
                'descripcion' => 'Laboratorio especializado en programación',
                'equipamiento' => ['computadores', 'proyector', 'servidor'],
                'estado' => 'disponible',
                'tiene_aire_acondicionado' => true,
                'tiene_proyector' => true,
                'tiene_computadoras' => true,
                'acceso_discapacitados' => false
            ]
        ];

        foreach ($aulas as $aulaData) {
            Aula::create($aulaData);
        }

        // Crear Grupos
        $grupos = [
            [
                'identificador' => 'A',
                'materia_id' => 1, // Programación I
                'capacidad_maxima' => 30,
                'estado' => 'activo'
            ],
            [
                'identificador' => 'B',
                'materia_id' => 1, // Programación I
                'capacidad_maxima' => 30,
                'estado' => 'activo'
            ],
            [
                'identificador' => 'A',
                'materia_id' => 2, // Matemáticas Discretas
                'capacidad_maxima' => 40,
                'estado' => 'activo'
            ],
            [
                'identificador' => 'A',
                'materia_id' => 3, // Programación II
                'capacidad_maxima' => 25,
                'estado' => 'activo'
            ],
            [
                'identificador' => 'A',
                'materia_id' => 4, // Introducción a la Ingeniería Industrial
                'capacidad_maxima' => 35,
                'estado' => 'activo'
            ],
            [
                'identificador' => 'A',
                'materia_id' => 5, // Estadística I
                'capacidad_maxima' => 30,
                'estado' => 'activo'
            ]
        ];

        foreach ($grupos as $grupoData) {
            Grupo::create($grupoData);
        }

        // Crear Cargas Académicas
        $cargasAcademicas = [
            [
                'profesor_id' => 1, // Juan Carlos Rodríguez
                'grupo_id' => 1, // Programación I - Grupo A
                'periodo' => '2024-2',
                'estado' => 'asignado'
            ],
            [
                'profesor_id' => 1, // Juan Carlos Rodríguez
                'grupo_id' => 2, // Programación I - Grupo B
                'periodo' => '2024-2',
                'estado' => 'asignado'
            ],
            [
                'profesor_id' => 2, // María Elena García
                'grupo_id' => 3, // Matemáticas Discretas - Grupo A
                'periodo' => '2024-2',
                'estado' => 'asignado'
            ],
            [
                'profesor_id' => 1, // Juan Carlos Rodríguez
                'grupo_id' => 4, // Programación II - Grupo A
                'periodo' => '2024-2',
                'estado' => 'asignado'
            ],
            [
                'profesor_id' => 3, // Carlos Alberto Mendoza
                'grupo_id' => 5, // Introducción a la Ingeniería Industrial - Grupo A
                'periodo' => '2024-2',
                'estado' => 'asignado'
            ],
            [
                'profesor_id' => 4, // Ana Sofía López
                'grupo_id' => 6, // Estadística I - Grupo A
                'periodo' => '2024-2',
                'estado' => 'asignado'
            ]
        ];

        foreach ($cargasAcademicas as $cargaData) {
            CargaAcademica::create($cargaData);
        }

        // Crear algunos estudiantes de ejemplo
        $estudiantes = [
            [
                'codigo_estudiante' => 'EST001',
                'nombre' => 'Pedro',
                'apellido' => 'Martínez',
                'email' => 'pedro.martinez@estudiante.edu',
                'telefono' => '555-1001',
                'cedula' => '20001001',
                'fecha_nacimiento' => '2000-05-15',
                'direccion' => 'Calle 123 #45-67',
                'carrera_id' => 1,
                'semestre_actual' => 1,
                'password' => Hash::make('password123'),
                'estado' => 'activo'
            ],
            [
                'codigo_estudiante' => 'EST002',
                'nombre' => 'Laura',
                'apellido' => 'González',
                'email' => 'laura.gonzalez@estudiante.edu',
                'telefono' => '555-1002',
                'cedula' => '20001002',
                'fecha_nacimiento' => '2001-03-22',
                'direccion' => 'Carrera 45 #12-34',
                'carrera_id' => 1,
                'semestre_actual' => 2,
                'password' => Hash::make('password123'),
                'estado' => 'activo'
            ],
            [
                'codigo_estudiante' => 'EST003',
                'nombre' => 'Miguel',
                'apellido' => 'Hernández',
                'email' => 'miguel.hernandez@estudiante.edu',
                'telefono' => '555-1003',
                'cedula' => '20001003',
                'fecha_nacimiento' => '1999-11-08',
                'direccion' => 'Avenida 67 #89-12',
                'carrera_id' => 2,
                'semestre_actual' => 1,
                'password' => Hash::make('password123'),
                'estado' => 'activo'
            ]
        ];

        foreach ($estudiantes as $estudianteData) {
            Estudiante::create($estudianteData);
        }

        echo "✅ Datos de ejemplo creados exitosamente:\n";
        echo "- 3 Facultades\n";
        echo "- 4 Carreras\n";
        echo "- 5 Materias\n";
        echo "- 4 Profesores\n";
        echo "- 3 Aulas\n";
        echo "- 6 Grupos\n";
        echo "- 6 Cargas Académicas\n";
        echo "- 3 Estudiantes\n";
    }
}
