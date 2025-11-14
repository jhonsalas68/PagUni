<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facultad;
use App\Models\Carrera;
use App\Models\Materia;
use App\Models\Profesor;
use App\Models\Estudiante;
use App\Models\Administrador;
use App\Models\Aula;
use App\Models\Grupo;
use App\Models\CargaAcademica;
use Illuminate\Support\Facades\Hash; // ¡Asegúrate de importar Hash!

class UniversidadSeeder extends Seeder
{
    public function run(): void
    {
        // ------------------------------------
        // NOTA IMPORTANTE: Para evitar el error 'Unique violation'
        // se está utilizando el método 'firstOrCreate' o 'updateOrCreate' 
        // en los registros con claves únicas (como Administrador).
        // ------------------------------------

        // Crear Facultades
        $facultadIngenieria = Facultad::firstOrCreate(
            ['codigo' => 'ING'],
            ['nombre' => 'Facultad de Ingeniería', 'descripcion' => 'Facultad dedicada a las ciencias de la ingeniería']
        );

        $facultadCiencias = Facultad::firstOrCreate(
            ['codigo' => 'CIE'],
            ['nombre' => 'Facultad de Ciencias', 'descripcion' => 'Facultad de ciencias básicas y aplicadas']
        );

        // Crear Carreras
        $carreraISC = Carrera::firstOrCreate(
            ['codigo' => 'ISC'],
            [
                'nombre' => 'Ingeniería en Sistemas Computacionales',
                'duracion_semestres' => 9,
                'facultad_id' => $facultadIngenieria->id,
                'descripcion' => 'Carrera enfocada en el desarrollo de sistemas de información'
            ]
        );

        $carreraMate = Carrera::firstOrCreate(
            ['codigo' => 'MATE'],
            [
                'nombre' => 'Licenciatura en Matemáticas',
                'duracion_semestres' => 8,
                'facultad_id' => $facultadCiencias->id,
                'descripcion' => 'Carrera enfocada en matemáticas puras y aplicadas'
            ]
        );

        // Crear Materias para ISC
        Materia::firstOrCreate(['codigo' => 'PROG1'], [
            'nombre' => 'Programación I',
            'creditos' => 6,
            'semestre' => 1,
            'carrera_id' => $carreraISC->id,
            'descripcion' => 'Fundamentos de programación'
        ]);

        Materia::firstOrCreate(['codigo' => 'BD'], [
            'nombre' => 'Base de Datos',
            'creditos' => 5,
            'semestre' => 3,
            'carrera_id' => $carreraISC->id,
            'descripcion' => 'Diseño y administración de bases de datos'
        ]);

        // Crear Materias para Matemáticas
        Materia::firstOrCreate(['codigo' => 'CALDIF'], [
            'nombre' => 'Cálculo Diferencial',
            'creditos' => 6,
            'semestre' => 1,
            'carrera_id' => $carreraMate->id,
            'descripcion' => 'Fundamentos del cálculo diferencial'
        ]);

        // Crear Profesores
        Profesor::firstOrCreate(['codigo_docente' => 'PROF001'], [
            'nombre' => 'Juan Carlos',
            'apellido' => 'Pérez García',
            'email' => 'juan.perez@universidad.edu',
            'cedula' => '12345678',
            'telefono' => '555-0001',
            'especialidad' => 'Ingeniería de Software',
            'tipo_contrato' => 'tiempo_completo',
            'password' => Hash::make('password123') // Siempre hashea contraseñas
        ]);

        $profesor2 = Profesor::firstOrCreate(['codigo_docente' => 'PROF002'], [
            'nombre' => 'María Elena',
            'apellido' => 'Rodríguez López',
            'email' => 'maria.rodriguez@universidad.edu',
            'cedula' => '87654321',
            'telefono' => '555-0002',
            'especialidad' => 'Matemáticas Aplicadas',
            'tipo_contrato' => 'tiempo_completo',
            'password' => Hash::make('password123') // Siempre hashea contraseñas
        ]);
        
        // Crear Estudiantes
        Estudiante::firstOrCreate(['codigo_estudiante' => 'ISC2024001'], [
            'nombre' => 'Ana',
            'apellido' => 'González Martínez',
            'email' => 'ana.gonzalez@estudiante.edu',
            'cedula' => '11111111',
            'fecha_nacimiento' => '2000-05-15',
            'telefono' => '555-1001',
            'direccion' => 'Calle Principal 123',
            'password' => Hash::make('student123'), // Siempre hashea contraseñas
            'carrera_id' => $carreraISC->id,
            'semestre_actual' => 3,
            'estado' => 'activo'
        ]);

        Estudiante::firstOrCreate(['codigo_estudiante' => 'MATE2024001'], [
            'nombre' => 'Carlos',
            'apellido' => 'Hernández Silva',
            'email' => 'carlos.hernandez@estudiante.edu',
            'cedula' => '22222222',
            'fecha_nacimiento' => '1999-08-22',
            'telefono' => '555-1002',
            'direccion' => 'Avenida Central 456',
            'password' => Hash::make('student123'), // Siempre hashea contraseñas
            'carrera_id' => $carreraMate->id,
            'semestre_actual' => 2,
            'estado' => 'activo'
        ]);
        
        // Crear Administradores (CAUSA DEL ERROR ANTERIOR - CORREGIDO CON firstOrCreate)
        Administrador::firstOrCreate(['codigo_admin' => 'ADM001'], [
            'nombre' => 'Super',
            'apellido' => 'Administrador',
            'email' => 'admin@universidad.edu',
            'cedula' => '99999999',
            'telefono' => '555-9999',
            'password' => Hash::make('admin123'), // Siempre hashea contraseñas
            'nivel_acceso' => 'super_admin'
        ]);

        Administrador::firstOrCreate(['codigo_admin' => 'ADM002'], [
            'nombre' => 'Administrador',
            'apellido' => 'Académico',
            'email' => 'academico@universidad.edu',
            'cedula' => '88888888',
            'telefono' => '555-8888',
            'password' => Hash::make('admin123'), // Siempre hashea contraseñas
            'nivel_acceso' => 'admin'
        ]); 

        // Crear Aulas
        Aula::firstOrCreate(['codigo_aula' => 'A101'], [
            'nombre' => 'Aula Magna A101',
            'tipo_aula' => 'aula',
            'edificio' => 'Edificio A',
            'piso' => 1,
            'capacidad' => 40,
            'descripcion' => 'Aula regular para clases teóricas',
            'equipamiento' => ['pizarra', 'proyector', 'aire_acondicionado'],
            'estado' => 'disponible',
            'tiene_aire_acondicionado' => true,
            'tiene_proyector' => true,
            'tiene_computadoras' => false,
            'acceso_discapacitados' => true
        ]);

        Aula::firstOrCreate(['codigo_aula' => 'LAB-B205'], [
            'nombre' => 'Laboratorio de Computación',
            'tipo_aula' => 'laboratorio',
            'edificio' => 'Edificio B',
            'piso' => 2,
            'capacidad' => 25,
            'descripcion' => 'Laboratorio equipado con 25 computadoras',
            'equipamiento' => ['computadoras', 'proyector', 'aire_acondicionado', 'red_internet'],
            'estado' => 'disponible',
            'tiene_aire_acondicionado' => true,
            'tiene_proyector' => true,
            'tiene_computadoras' => true,
            'acceso_discapacitados' => false
        ]);

        Aula::firstOrCreate(['codigo_aula' => 'AUD-C301'], [
            'nombre' => 'Auditorio Principal',
            'tipo_aula' => 'auditorio',
            'edificio' => 'Edificio C',
            'piso' => 3,
            'capacidad' => 150,
            'descripcion' => 'Auditorio para eventos y conferencias',
            'equipamiento' => ['sistema_audio', 'proyector', 'microfono', 'aire_acondicionado'],
            'estado' => 'disponible',
            'tiene_aire_acondicionado' => true,
            'tiene_proyector' => true,
            'tiene_computadoras' => false,
            'acceso_discapacitados' => true
        ]);

        Aula::firstOrCreate(['codigo_aula' => 'LAB-A102'], [
            'nombre' => 'Laboratorio de Física',
            'tipo_aula' => 'laboratorio',
            'edificio' => 'Edificio A',
            'piso' => 1,
            'capacidad' => 20,
            'descripcion' => 'Laboratorio para prácticas de física',
            'equipamiento' => ['equipos_laboratorio', 'mesas_trabajo', 'ventilacion'],
            'estado' => 'disponible',
            'tiene_aire_acondicionado' => false,
            'tiene_proyector' => false,
            'tiene_computadoras' => false,
            'acceso_discapacitados' => true
        ]);

        Aula::firstOrCreate(['codigo_aula' => 'B201'], [
            'nombre' => 'Aula B201',
            'tipo_aula' => 'aula',
            'edificio' => 'Edificio B',
            'piso' => 2,
            'capacidad' => 35,
            'descripcion' => 'Aula regular con capacidad media',
            'equipamiento' => ['pizarra', 'ventiladores'],
            'estado' => 'disponible',
            'tiene_aire_acondicionado' => false,
            'tiene_proyector' => false,
            'tiene_computadoras' => false,
            'acceso_discapacitados' => false
        ]);

        Aula::firstOrCreate(['codigo_aula' => 'CONF-C201'], [
            'nombre' => 'Sala de Conferencias',
            'tipo_aula' => 'sala_conferencias',
            'edificio' => 'Edificio C',
            'piso' => 2,
            'capacidad' => 30,
            'descripcion' => 'Sala para reuniones y conferencias pequeñas',
            'equipamiento' => ['mesa_conferencia', 'proyector', 'sistema_audio', 'aire_acondicionado'],
            'estado' => 'disponible',
            'tiene_aire_acondicionado' => true,
            'tiene_proyector' => true,
            'tiene_computadoras' => false,
            'acceso_discapacitados' => true
        ]);

        // --- LÓGICA DE GRUPOS Y CARGA ACADÉMICA (Depende de los IDs creados arriba) ---
        
        // Obtener IDs de Materias (usando el código para mayor seguridad)
        $materia1 = Materia::where('codigo', 'PROG1')->first(); // Programación I
        $materia2 = Materia::where('codigo', 'BD')->first(); // Base de Datos
        $materia3 = Materia::where('codigo', 'CALDIF')->first(); // Cálculo Diferencial

        if ($materia1) {
            Grupo::firstOrCreate(
                ['materia_id' => $materia1->id, 'identificador' => 'A'], 
                ['capacidad_maxima' => 30, 'estado' => 'activo']
            );

            Grupo::firstOrCreate(
                ['materia_id' => $materia1->id, 'identificador' => 'B'], 
                ['capacidad_maxima' => 25, 'estado' => 'activo']
            );
        }

        if ($materia2) {
            Grupo::firstOrCreate(
                ['materia_id' => $materia2->id, 'identificador' => 'A'], 
                ['capacidad_maxima' => 20, 'estado' => 'activo']
            );
        }

        if ($materia3) {
            Grupo::firstOrCreate(
                ['materia_id' => $materia3->id, 'identificador' => 'A'], 
                ['capacidad_maxima' => 35, 'estado' => 'activo']
            );
        }

        // Crear algunas asignaciones de carga académica
        // Obtener IDs de profesores (usando el código para mayor seguridad)
        $profesor1 = Profesor::where('codigo_docente', 'PROF001')->first();
        $profesor2 = Profesor::where('codigo_docente', 'PROF002')->first();

        // Obtener IDs de Grupos (usando la materia y el identificador)
        $grupo1A = Grupo::where('materia_id', $materia1->id)->where('identificador', 'A')->first();
        $grupo1B = Grupo::where('materia_id', $materia1->id)->where('identificador', 'B')->first();
        $grupo3A = Grupo::where('materia_id', $materia2->id)->where('identificador', 'A')->first();
        $grupo4A = Grupo::where('materia_id', $materia3->id)->where('identificador', 'A')->first();


        // Verificar y crear Cargas Académicas usando firstOrCreate en base a profesor, grupo y periodo
        if ($profesor1 && $grupo1A) {
            CargaAcademica::firstOrCreate(
                ['profesor_id' => $profesor1->id, 'grupo_id' => $grupo1A->id, 'periodo' => '2024-2'],
                ['estado' => 'asignado']
            );
        }
        
        if ($profesor1 && $grupo3A) {
            CargaAcademica::firstOrCreate(
                ['profesor_id' => $profesor1->id, 'grupo_id' => $grupo3A->id, 'periodo' => '2024-2'],
                ['estado' => 'asignado']
            );
        }

        if ($profesor2 && $grupo4A) {
            CargaAcademica::firstOrCreate(
                ['profesor_id' => $profesor2->id, 'grupo_id' => $grupo4A->id, 'periodo' => '2024-2'],
                ['estado' => 'asignado']
            );
        }

        if ($profesor2 && $grupo1B) {
            CargaAcademica::firstOrCreate(
                ['profesor_id' => $profesor2->id, 'grupo_id' => $grupo1B->id, 'periodo' => '2024-2'],
                ['estado' => 'asignado']
            );
        }
    }
}