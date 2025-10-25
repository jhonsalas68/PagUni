<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facultad;
use App\Models\Carrera;
use App\Models\Materia;
use App\Models\Profesor;
use App\Models\Estudiante;
use App\Models\Administrador;

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