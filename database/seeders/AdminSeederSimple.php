<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeederSimple extends Seeder
{
    /**
     * Seed básico de Administradores para Neon
     * Ejecutar con: php artisan db:seed --class=AdminSeederSimple
     */
    public function run(): void
    {
        // Limpiar tabla
        DB::table('administradores')->truncate();
        
        // Insertar administradores con solo las columnas que existen
        $admins = [
            [
                'codigo_admin' => 'ADMIN001',
                'nombre' => 'Administrador',
                'apellido' => 'Principal',
                'email' => 'admin@ficct.edu.bo',
                'password' => Hash::make('admin123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo_admin' => 'ADMIN002',
                'nombre' => 'Carlos',
                'apellido' => 'Rodríguez',
                'email' => 'academico@ficct.edu.bo',
                'password' => Hash::make('admin123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo_admin' => 'ADMIN003',
                'nombre' => 'María',
                'apellido' => 'González',
                'email' => 'sistemas@ficct.edu.bo',
                'password' => Hash::make('admin123'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('administradores')->insert($admins);

        $this->command->info('✅ Administradores creados exitosamente en Neon!');
        $this->command->newLine();
        $this->command->info('📋 CREDENCIALES DE ACCESO:');
        $this->command->newLine();
        
        $this->command->table(
            ['Email', 'Password'],
            [
                ['admin@ficct.edu.bo', 'admin123'],
                ['academico@ficct.edu.bo', 'admin123'],
                ['sistemas@ficct.edu.bo', 'admin123'],
            ]
        );
    }
}