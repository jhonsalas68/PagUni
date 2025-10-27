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

class UniversidadSeeder extends Seeder
{
    public function run(): void
    {
        // Crear Facultades
        $facultadIngenieria = Facultad::create([
            'nombre' => 'Facultad de Ingeniería',
            'codigo' => 'ING',
            'descripcion' => 'Facultad dedicada a las ciencias de la ingeniería'
        ]);

        $facultadCiencias = Facultad::create([
            'nombre' => 'Facultad de Ciencias',
            'codigo' => 'CIE',
            'descripcion' => 'Facultad de ciencias básicas y aplicadas'
        ]);

        // Crear Carreras
        $carreraISC = Carrera::create([
            'nombre' => 'Ingeniería en Sistemas Computacionales',
            'codigo' => 'ISC',
            'duracion_semestres' => 9,
            'facultad_id' => $facultadIngenieria->id,
            'descripcion' => 'Carrera enfocada en el desarrollo de sistemas de información'
        ]);

        $carreraMate = Carrera::create([
            'nombre' => 'Licenciatura en Matemáticas',
            'codigo' => 'MATE',
            'duracion_semestres' => 8,
            'facultad_id' => $facultadCiencias->id,
            'descripcion' => 'Carrera enfocada en matemáticas puras y aplicadas'
        ]);

        // Crear Materias para ISC
        Materia::create([
            'nombre' => 'Programación I',
            'codigo' => 'PROG1',
            'creditos' => 6,
            'semestre' => 1,
            'carrera_id' => $carreraISC->id,
            'descripcion' => 'Fundamentos de programación'
        ]);

        Materia::create([
            'nombre' => 'Base de Datos',
            'codigo' => 'BD',
            'creditos' => 5,
            'semestre' => 3,
            'carrera_id' => $carreraISC->id,
            'descripcion' => 'Diseño y administración de bases de datos'
        ]);

        // Crear Materias para Matemáticas
        Materia::create([
            'nombre' => 'Cálculo Diferencial',
            'codigo' => 'CALDIF',
            'creditos' => 6,
            'semestre' => 1,
            'carrera_id' => $carreraMate->id,
            'descripcion' => 'Fundamentos del cálculo diferencial'
        ]);

        // Crear Profesores
        Profesor::create([
            'codigo_docente' => 'PROF001',
            'nombre' => 'Juan Carlos',
            'apellido' => 'Pérez García',
            'email' => 'juan.perez@universidad.edu',
            'cedula' => '12345678',
            'telefono' => '555-0001',
            'especialidad' => 'Ingeniería de Software',
            'tipo_contrato' => 'tiempo_completo',
            'password' => 'password123'
        ]);

        Profesor::create([
            'codigo_docente' => 'PROF002',
            'nombre' => 'María Elena',
            'apellido' => 'Rodríguez López',
            'email' => 'maria.rodriguez@universidad.edu',
            'cedula' => '87654321',
            'telefono' => '555-0002',
            'especialidad' => 'Matemáticas Aplicadas',
            'tipo_contrato' => 'tiempo_completo',
            'password' => 'password123'
        ]);

        // Crear Estudiantes
        Estudiante::create([
            'nombre' => 'Ana',
            'apellido' => 'González Martínez',
            'email' => 'ana.gonzalez@estudiante.edu',
            'cedula' => '11111111',
            'codigo_estudiante' => 'ISC2024001',
            'fecha_nacimiento' => '2000-05-15',
            'telefono' => '555-1001',
            'direccion' => 'Calle Principal 123',
            'password' => 'student123',
            'carrera_id' => $carreraISC->id,
            'semestre_actual' => 3,
            'estado' => 'activo'
        ]);

        Estudiante::create([
            'nombre' => 'Carlos',
            'apellido' => 'Hernández Silva',
            'email' => 'carlos.hernandez@estudiante.edu',
            'cedula' => '22222222',
            'codigo_estudiante' => 'MATE2024001',
            'fecha_nacimiento' => '1999-08-22',
            'telefono' => '555-1002',
            'direccion' => 'Avenida Central 456',
            'password' => 'student123',
            'carrera_id' => $carreraMate->id,
            'semestre_actual' => 2,
            'estado' => 'activo'
        ]);
    }
}
    
    // Crear Administradores
        Administrador::create([
            'codigo_admin' => 'ADM001',
            'nombre' => 'Super',
            'apellido' => 'Administrador',
            'email' => 'admin@universidad.edu',
            'cedula' => '99999999',
            'telefono' => '555-9999',
            'password' => 'admin123',
            'nivel_acceso' => 'super_admin'
        ]);

        Administrador::create([
            'codigo_admin' => 'ADM002',
            'nombre' => 'Administrador',
            'apellido' => 'Académico',
            'email' => 'academico@universidad.edu',
            'cedula' => '88888888',
            'telefono' => '555-8888',
            'password' => 'admin123',
            'nivel_acceso' => 'admin'
        ]);       
 // Crear Aulas
        Aula::create([
            'codigo_aula' => 'A101',
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

        Aula::create([
            'codigo_aula' => 'LAB-B205',
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

        Aula::create([
            'codigo_aula' => 'AUD-C301',
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

        Aula::create([
            'codigo_aula' => 'LAB-A102',
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

        Aula::create([
            'codigo_aula' => 'B201',
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

        Aula::create([
            'codigo_aula' => 'CONF-C201',
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
 // Crear Grupos para las materias
        $materia1 = Materia::find(1); // Programación I
        $materia2 = Materia::find(2); // Base de Datos
        $materia3 = Materia::find(3); // Cálculo Diferencial

        if ($materia1) {
            Grupo::create([
                'identificador' => 'A',
                'materia_id' => $materia1->id,
                'capacidad_maxima' => 30,
                'estado' => 'activo'
            ]);

            Grupo::create([
                'identificador' => 'B',
                'materia_id' => $materia1->id,
                'capacidad_maxima' => 25,
                'estado' => 'activo'
            ]);
        }

        if ($materia2) {
            Grupo::create([
                'identificador' => 'A',
                'materia_id' => $materia2->id,
                'capacidad_maxima' => 20,
                'estado' => 'activo'
            ]);
        }

        if ($materia3) {
            Grupo::create([
                'identificador' => 'A',
                'materia_id' => $materia3->id,
                'capacidad_maxima' => 35,
                'estado' => 'activo'
            ]);
        }

        // Crear algunas asignaciones de carga académica
        $profesor1 = Profesor::find(1);
        $profesor2 = Profesor::find(2);

        if ($profesor1) {
            // Profesor 1 tiene Programación I - Grupo A
            CargaAcademica::create([
                'profesor_id' => $profesor1->id,
                'grupo_id' => 1, // Programación I - Grupo A
                'periodo' => '2024-2',
                'estado' => 'asignado'
            ]);

            // Profesor 1 tiene Base de Datos - Grupo A
            CargaAcademica::create([
                'profesor_id' => $profesor1->id,
                'grupo_id' => 3, // Base de Datos - Grupo A
                'periodo' => '2024-2',
                'estado' => 'asignado'
            ]);
        }

        if ($profesor2) {
            // Profesor 2 tiene Cálculo Diferencial - Grupo A
            CargaAcademica::create([
                'profesor_id' => $profesor2->id,
                'grupo_id' => 4, // Cálculo Diferencial - Grupo A
                'periodo' => '2024-2',
                'estado' => 'asignado'
            ]);

            // Profesor 2 tiene Programación I - Grupo B
            CargaAcademica::create([
                'profesor_id' => $profesor2->id,
                'grupo_id' => 2, // Programación I - Grupo B
                'periodo' => '2024-2',
                'estado' => 'asignado'
            ]);
        }