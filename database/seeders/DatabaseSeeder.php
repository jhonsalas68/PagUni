<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            AdminSeeder::class,
        AsistenciasDocenteSeeder::class,
        AsistenciasOctubreSeeder::class,
        DatosEjemploSeeder::class,
        EstudiantesTestSeeder::class,
        FICCTCompletaSeeder::class,
        GruposYCargasSeeder::class,
        HorarioTestSeeder::class,
        IngenieriaSistemasSeeder::class,
        InscripcionesTestSeeder::class,
        MateriasCompletasSeeder::class,
        PeriodosAcademicosSeeder::class,
        Semestre2024_2Seeder::class,
        UniversidadSeeder::class,
        ]);
    }
}