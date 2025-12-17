<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatosBasicosNeon extends Seeder
{
    /**
     * Seed básico para poblar Neon con datos esenciales
     */
    public function run(): void
    {
        $this->command->info('🚀 Poblando base de datos Neon...');
        
        // 1. FACULTADES
        $this->command->info('📚 Creando Facultades...');
        DB::table('facultades')->insert([
            [
                'nombre' => 'Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones',
                'codigo' => 'FICCT',
                'descripcion' => 'Facultad especializada en tecnología e innovación',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // 2. CARRERAS
        $this->command->info('🎓 Creando Carreras...');
        $facultadId = DB::table('facultades')->where('codigo', 'FICCT')->first()->id;
        
        DB::table('carreras')->insert([
            [
                'nombre' => 'Ingeniería de Sistemas',
                'codigo' => 'ING-SIS',
                'facultad_id' => $facultadId,
                'duracion_semestres' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Ingeniería en Telecomunicaciones',
                'codigo' => 'ING-TEL',
                'facultad_id' => $facultadId,
                'duracion_semestres' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // 3. MATERIAS
        $this->command->info('📖 Creando Materias...');
        $carreraId = DB::table('carreras')->where('codigo', 'ING-SIS')->first()->id;
        
        DB::table('materias')->insert([
            [
                'nombre' => 'Programación I',
                'codigo' => 'SIS-101',
                'carrera_id' => $carreraId,
                'semestre' => 1,
                'creditos' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Matemática Discreta',
                'codigo' => 'SIS-102',
                'carrera_id' => $carreraId,
                'semestre' => 1,
                'creditos' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Base de Datos I',
                'codigo' => 'SIS-201',
                'carrera_id' => $carreraId,
                'semestre' => 3,
                'creditos' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // 4. PROFESORES
        $this->command->info('👨‍🏫 Creando Profesores...');
        DB::table('profesores')->insert([
            [
                'nombre' => 'Juan Carlos',
                'apellido' => 'Pérez López',
                'email' => 'prof001@ficct.edu.bo',
                'telefono' => '3-1234567',
                'cedula' => '12345678',
                'especialidad' => 'Ingeniería de Software',
                'tipo_contrato' => 'tiempo_completo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'María Elena',
                'apellido' => 'García Morales',
                'email' => 'prof002@ficct.edu.bo',
                'telefono' => '3-2345678',
                'cedula' => '23456789',
                'especialidad' => 'Base de Datos',
                'tipo_contrato' => 'tiempo_completo',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // 5. ESTUDIANTES
        $this->command->info('👨‍🎓 Creando Estudiantes...');
        DB::table('estudiantes')->insert([
            [
                'nombre' => 'Pedro',
                'apellido' => 'Ramírez Silva',
                'email' => 'est001@ficct.edu.bo',
                'telefono' => '7-1234567',
                'cedula' => '34567890',
                'codigo_estudiante' => 'EST001',
                'carrera_id' => $carreraId,
                'semestre_actual' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Ana',
                'apellido' => 'Martínez Rojas',
                'email' => 'est002@ficct.edu.bo',
                'telefono' => '7-2345678',
                'cedula' => '45678901',
                'codigo_estudiante' => 'EST002',
                'carrera_id' => $carreraId,
                'semestre_actual' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // 6. AULAS
        $this->command->info('🏫 Creando Aulas...');
        DB::table('aulas')->insert([
            [
                'nombre' => 'Aula 101',
                'capacidad' => 30,
                'tipo' => 'aula',
                'ubicacion' => 'Edificio A - Primer Piso',
                'equipamiento' => 'Proyector, Pizarra Digital',
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Lab Sistemas',
                'capacidad' => 25,
                'tipo' => 'laboratorio',
                'ubicacion' => 'Edificio B - Segundo Piso',
                'equipamiento' => '25 Computadoras, Proyector',
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        $this->command->info('✅ Datos básicos creados exitosamente!');
        $this->command->newLine();
        
        // Mostrar resumen
        $this->command->info('📊 RESUMEN DE DATOS CREADOS:');
        $this->command->table(
            ['Tabla', 'Registros'],
            [
                ['Administradores', DB::table('administradores')->count()],
                ['Facultades', DB::table('facultades')->count()],
                ['Carreras', DB::table('carreras')->count()],
                ['Materias', DB::table('materias')->count()],
                ['Profesores', DB::table('profesores')->count()],
                ['Estudiantes', DB::table('estudiantes')->count()],
                ['Aulas', DB::table('aulas')->count()],
            ]
        );
    }
}