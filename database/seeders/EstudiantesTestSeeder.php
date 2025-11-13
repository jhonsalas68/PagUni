<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Estudiante;
use App\Models\Carrera;
use Illuminate\Support\Facades\Hash;

class EstudiantesTestSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creando estudiantes de prueba...');

        // Obtener la primera carrera disponible
        $carrera = Carrera::first();
        
        if (!$carrera) {
            $this->command->warn('No se encontró ninguna carrera');
            return;
        }
        
        $this->command->info("Usando carrera: {$carrera->nombre}");

        $estudiantes = [
            [
                'codigo_estudiante' => 'INGS2024001',
                'cedula' => '12345001',
                'nombre' => 'Juan',
                'apellido' => 'Pérez García',
                'email' => 'juan.perez@estudiante.edu',
                'telefono' => '70000001',
                'direccion' => 'Av. Principal #123',
            ],
            [
                'codigo_estudiante' => 'INGS2024002',
                'cedula' => '12345002',
                'nombre' => 'María',
                'apellido' => 'López Rodríguez',
                'email' => 'maria.lopez@estudiante.edu',
                'telefono' => '70000002',
                'direccion' => 'Calle Secundaria #456',
            ],
            [
                'codigo_estudiante' => 'INGS2024003',
                'cedula' => '12345003',
                'nombre' => 'Carlos',
                'apellido' => 'Martínez Sánchez',
                'email' => 'carlos.martinez@estudiante.edu',
                'telefono' => '70000003',
                'direccion' => 'Av. Universitaria #789',
            ],
            [
                'codigo_estudiante' => 'INGS2024004',
                'cedula' => '12345004',
                'nombre' => 'Ana',
                'apellido' => 'González Fernández',
                'email' => 'ana.gonzalez@estudiante.edu',
                'telefono' => '70000004',
                'direccion' => 'Calle Central #321',
            ],
            [
                'codigo_estudiante' => 'INGS2024005',
                'cedula' => '12345005',
                'nombre' => 'Luis',
                'apellido' => 'Ramírez Torres',
                'email' => 'luis.ramirez@estudiante.edu',
                'telefono' => '70000005',
                'direccion' => 'Av. Libertad #654',
            ],
        ];

        foreach ($estudiantes as $data) {
            Estudiante::create([
                'codigo_estudiante' => $data['codigo_estudiante'],
                'cedula' => $data['cedula'],
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'email' => $data['email'],
                'telefono' => $data['telefono'],
                'direccion' => $data['direccion'],
                'fecha_nacimiento' => '2000-01-01',
                'carrera_id' => $carrera->id,
                'password' => Hash::make('password'),
            ]);
            
            $this->command->info("✓ Creado: {$data['codigo_estudiante']} - {$data['nombre']} {$data['apellido']}");
        }

        $this->command->info("\n✅ Se crearon " . count($estudiantes) . " estudiantes de prueba");
        $this->command->info("📝 Usuario: INGS2024001 (o cualquier código)");
        $this->command->info("🔑 Contraseña: password\n");
    }
}
