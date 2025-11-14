<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Administrador;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Seed de Administradores del Sistema
     * Ejecutar con: php artisan db:seed --class=AdminSeeder
     */
    public function run(): void
    {
        // Limpiar tabla de administradores (opcional)
        // DB::table('administradores')->truncate();
        
        // Administrador Principal - Super Admin
        Administrador::create([
            'codigo_admin' => 'ADMIN001',
            'nombre' => 'Administrador',
            'apellido' => 'Principal',
            'email' => 'admin@uagrm.edu.bo',
            'cedula' => '12345678',
            'telefono' => '3-1234567',
            'password' => 'admin2024!',
            'nivel_acceso' => 'super_admin'
        ]);

        // Administrador Académico
        Administrador::create([
            'codigo_admin' => 'ADMIN002',
            'nombre' => 'Carlos',
            'apellido' => 'Rodríguez Pérez',
            'email' => 'academico@uagrm.edu.bo',
            'cedula' => '87654321',
            'telefono' => '3-7654321',
            'password' => 'Academico2024!',
            'nivel_acceso' => 'admin'
        ]);

        // Administrador de Sistemas
        Administrador::create([
            'codigo_admin' => 'ADMIN003',
            'nombre' => 'María',
            'apellido' => 'González Silva',
            'email' => 'sistemas@uagrm.edu.bo',
            'cedula' => '11223344',
            'telefono' => '3-1122334',
            'password' => 'Sistemas2024!',
            'nivel_acceso' => 'admin'
        ]);

        // Administrador de Recursos Humanos
        Administrador::create([
            'codigo_admin' => 'ADMIN004',
            'nombre' => 'Juan',
            'apellido' => 'Martínez López',
            'email' => 'rrhh@uagrm.edu.bo',
            'cedula' => '55667788',
            'telefono' => '3-5566778',
            'password' => 'RRHH2024!',
            'nivel_acceso' => 'admin'
        ]);

        // Administrador de Pruebas (para desarrollo)
        Administrador::create([
            'codigo_admin' => 'ADMIN999',
            'nombre' => 'Test',
            'apellido' => 'Administrator',
            'email' => 'test@uagrm.edu.bo',
            'cedula' => '99999999',
            'telefono' => '3-9999999',
            'password' => 'test123',
            'nivel_acceso' => 'super_admin'
        ]);

        $this->command->info('✅ Administradores creados exitosamente!');
        $this->command->newLine();
        $this->command->info('📋 CREDENCIALES DE ACCESO:');
        $this->command->newLine();
        
        $this->command->table(
            ['Rol', 'Email', 'Password', 'Nivel'],
            [
                ['Super Admin', 'admin@uagrm.edu.bo', 'Admin2024!', 'super_admin'],
                ['Admin Académico', 'academico@uagrm.edu.bo', 'Academico2024!', 'admin'],
                ['Admin Sistemas', 'sistemas@uagrm.edu.bo', 'Sistemas2024!', 'admin'],
                ['Admin RRHH', 'rrhh@uagrm.edu.bo', 'RRHH2024!', 'admin'],
                ['Admin Test', 'test@uagrm.edu.bo', 'test123', 'super_admin'],
            ]
        );
        
        $this->command->newLine();
        $this->command->info('🌐 URL de acceso: http://localhost/login');
    }
}
