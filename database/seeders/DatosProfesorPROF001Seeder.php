<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facultad;
use App\Models\Carrera;
use App\Models\Materia;
use App\Models\Profesor;
use App\Models\Estudiante;
use App\Models\Grupo;
use App\Models\CargaAcademica;
use App\Models\Horario;
use App\Models\Aula;
use App\Models\Inscripcion;
use App\Models\PeriodoInscripcion;
use App\Models\PeriodoAcademico;
use Illuminate\Support\Facades\DB;

class DatosProfesorPROF001Seeder extends Seeder
{
    /**
     * Seeder para crear datos completos del Profesor PROF001
     * Incluye: Materias, Estudiantes, Grupos, Cargas, Horarios, Inscripciones
     */
    public function run(): void
    {
        $this->command->info('🎓 Creando datos para Profesor PROF001 (Juan Carlos Pérez García)');
        $this->command->newLine();

        // Buscar el profesor PROF001
        $profesor = Profesor::where('codigo_docente', 'PROF001')->first();
        if (!$profesor) {
            $this->command->error('❌ Profesor PROF001 no encontrado. Ejecuta primero UniversidadSeeder.');
            return;
        }

        // Buscar o crear facultad y carrera
        $facultad = Facultad::firstOrCreate([
            'codigo' => 'FICCT'
        ], [
            'nombre' => 'Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones',
            'descripcion' => 'Facultad especializada en tecnología e ingeniería'
        ]);

        $carrera = Carrera::firstOrCreate([
            'codigo' => 'ISC'
        ], [
            'nombre' => 'Ingeniería en Sistemas Computacionales',
            'duracion_semestres' => 10,
            'facultad_id' => $facultad->id,
            'descripcion' => 'Carrera enfocada en desarrollo de software y sistemas'
        ]);

        // Crear materias para el profesor
        $materias = [
            [
                'nombre' => 'Programación I',
                'codigo' => 'ISC-101',
                'creditos' => 6,
                'semestre' => 1,
                'descripcion' => 'Fundamentos de programación con Python'
            ],
            [
                'nombre' => 'Estructura de Datos',
                'codigo' => 'ISC-201',
                'creditos' => 5,
                'semestre' => 3,
                'descripcion' => 'Algoritmos y estructuras de datos fundamentales'
            ],
            [
                'nombre' => 'Ingeniería de Software I',
                'codigo' => 'ISC-301',
                'creditos' => 4,
                'semestre' => 5,
                'descripcion' => 'Metodologías de desarrollo de software'
            ]
        ];

        $materiasCreadas = [];
        foreach ($materias as $materiaData) {
            $materia = Materia::firstOrCreate([
                'codigo' => $materiaData['codigo']
            ], array_merge($materiaData, [
                'carrera_id' => $carrera->id
            ]));
            $materiasCreadas[] = $materia;
            $this->command->info("✅ Materia creada: {$materia->nombre} ({$materia->codigo})");
        }

        // Crear aulas si no existen
        $aulas = [
            [
                'codigo_aula' => 'LAB-A101',
                'nombre' => 'Laboratorio de Programación A101',
                'tipo_aula' => 'laboratorio',
                'edificio' => 'Edificio A',
                'piso' => 1,
                'capacidad' => 30
            ],
            [
                'codigo_aula' => 'AULA-B201',
                'nombre' => 'Aula Teórica B201',
                'tipo_aula' => 'aula',
                'edificio' => 'Edificio B',
                'piso' => 2,
                'capacidad' => 40
            ],
            [
                'codigo_aula' => 'LAB-C301',
                'nombre' => 'Laboratorio de Software C301',
                'tipo_aula' => 'laboratorio',
                'edificio' => 'Edificio C',
                'piso' => 3,
                'capacidad' => 25
            ]
        ];

        $aulasCreadas = [];
        foreach ($aulas as $aulaData) {
            $aula = Aula::firstOrCreate([
                'codigo_aula' => $aulaData['codigo_aula']
            ], array_merge($aulaData, [
                'descripcion' => $aulaData['nombre'],
                'equipamiento' => ['computadoras', 'proyector', 'pizarra'],
                'estado' => 'disponible',
                'tiene_aire_acondicionado' => true,
                'tiene_proyector' => true,
                'tiene_computadoras' => $aulaData['tipo_aula'] === 'laboratorio',
                'acceso_discapacitados' => true
            ]));
            $aulasCreadas[] = $aula;
        }

        // Crear grupos para cada materia
        $gruposCreados = [];
        foreach ($materiasCreadas as $index => $materia) {
            $grupo = Grupo::firstOrCreate([
                'materia_id' => $materia->id,
                'identificador' => 'A'
            ], [
                'capacidad_maxima' => 30,
                'estado' => 'activo'
            ]);
            $gruposCreados[] = $grupo;
            $this->command->info("✅ Grupo creado: {$materia->nombre} - Grupo A");
        }

        // Crear cargas académicas (asignar profesor a grupos)
        $cargasCreadas = [];
        foreach ($gruposCreados as $grupo) {
            $carga = CargaAcademica::firstOrCreate([
                'profesor_id' => $profesor->id,
                'grupo_id' => $grupo->id,
                'periodo' => '2024-2'
            ], [
                'estado' => 'asignado',
                'periodo_academico' => '2024-2'
            ]);
            $cargasCreadas[] = $carga;
            $this->command->info("✅ Carga académica asignada: {$grupo->materia->nombre}");
        }

        // Crear horarios para cada carga académica
        $horarios = [
            ['dias_semana' => ['lunes'], 'hora_inicio' => '08:00', 'hora_fin' => '10:00'],
            ['dias_semana' => ['miercoles'], 'hora_inicio' => '10:00', 'hora_fin' => '12:00'],
            ['dias_semana' => ['viernes'], 'hora_inicio' => '14:00', 'hora_fin' => '16:00']
        ];

        foreach ($cargasCreadas as $index => $carga) {
            $horarioData = $horarios[$index];
            $aula = $aulasCreadas[$index];
            
            Horario::firstOrCreate([
                'carga_academica_id' => $carga->id,
                'aula_id' => $aula->id,
                'periodo_academico' => '2024-2'
            ], [
                'dias_semana' => json_encode($horarioData['dias_semana']),
                'hora_inicio' => $horarioData['hora_inicio'],
                'hora_fin' => $horarioData['hora_fin'],
                'duracion_horas' => 2.0,
                'tipo_clase' => 'teorica',
                'es_semestral' => true,
                'semanas_duracion' => 16,
                'tipo_asignacion' => 'manual',
                'estado' => 'activo'
            ]);
            $this->command->info("✅ Horario creado: {$carga->grupo->materia->nombre} - {$horarioData['dias_semana'][0]} {$horarioData['hora_inicio']}-{$horarioData['hora_fin']}");
        }

        // Crear estudiantes
        $estudiantes = [
            [
                'nombre' => 'María José',
                'apellido' => 'Rodríguez Pérez',
                'email' => 'maria.rodriguez@estudiante.uagrm.edu.bo',
                'cedula' => '12345101',
                'codigo_estudiante' => 'ISC2024101',
                'fecha_nacimiento' => '2002-03-15',
                'telefono' => '3-1234101',
                'direccion' => 'Av. Santos Dumont #123'
            ],
            [
                'nombre' => 'Carlos Alberto',
                'apellido' => 'Mendoza Silva',
                'email' => 'carlos.mendoza@estudiante.uagrm.edu.bo',
                'cedula' => '12345102',
                'codigo_estudiante' => 'ISC2024102',
                'fecha_nacimiento' => '2001-07-22',
                'telefono' => '3-1234102',
                'direccion' => 'Calle Libertad #456'
            ],
            [
                'nombre' => 'Ana Lucía',
                'apellido' => 'García López',
                'email' => 'ana.garcia@estudiante.uagrm.edu.bo',
                'cedula' => '12345103',
                'codigo_estudiante' => 'ISC2024103',
                'fecha_nacimiento' => '2002-11-08',
                'telefono' => '3-1234103',
                'direccion' => 'Barrio Equipetrol #789'
            ],
            [
                'nombre' => 'Luis Fernando',
                'apellido' => 'Vargas Morales',
                'email' => 'luis.vargas@estudiante.uagrm.edu.bo',
                'cedula' => '12345104',
                'codigo_estudiante' => 'ISC2024104',
                'fecha_nacimiento' => '2001-12-03',
                'telefono' => '3-1234104',
                'direccion' => 'Av. Roca y Coronado #321'
            ],
            [
                'nombre' => 'Sofía Alejandra',
                'apellido' => 'Herrera Castro',
                'email' => 'sofia.herrera@estudiante.uagrm.edu.bo',
                'cedula' => '12345105',
                'codigo_estudiante' => 'ISC2024105',
                'fecha_nacimiento' => '2002-05-17',
                'telefono' => '3-1234105',
                'direccion' => 'Calle Sucre #654'
            ],
            [
                'nombre' => 'Diego Andrés',
                'apellido' => 'Flores Ríos',
                'email' => 'diego.flores@estudiante.uagrm.edu.bo',
                'cedula' => '12345106',
                'codigo_estudiante' => 'ISC2024106',
                'fecha_nacimiento' => '2001-09-25',
                'telefono' => '3-1234106',
                'direccion' => 'Barrio Las Palmas #987'
            ],
            [
                'nombre' => 'Valentina',
                'apellido' => 'Jiménez Vega',
                'email' => 'valentina.jimenez@estudiante.uagrm.edu.bo',
                'cedula' => '12345107',
                'codigo_estudiante' => 'ISC2024107',
                'fecha_nacimiento' => '2002-01-14',
                'telefono' => '3-1234107',
                'direccion' => 'Av. Alemana #147'
            ],
            [
                'nombre' => 'Sebastián',
                'apellido' => 'Torres Aguilar',
                'email' => 'sebastian.torres@estudiante.uagrm.edu.bo',
                'cedula' => '12345108',
                'codigo_estudiante' => 'ISC2024108',
                'fecha_nacimiento' => '2001-06-30',
                'telefono' => '3-1234108',
                'direccion' => 'Calle Warnes #258'
            ]
        ];

        $estudiantesCreados = [];
        foreach ($estudiantes as $estudianteData) {
            $estudiante = Estudiante::firstOrCreate([
                'cedula' => $estudianteData['cedula']
            ], array_merge($estudianteData, [
                'carrera_id' => $carrera->id,
                'semestre_actual' => rand(1, 5),
                'estado' => 'activo',
                'password' => 'student123'
            ]));
            $estudiantesCreados[] = $estudiante;
            $this->command->info("✅ Estudiante creado: {$estudiante->nombre} {$estudiante->apellido} ({$estudiante->codigo_estudiante})");
        }

        // Crear periodo de inscripción activo
        $periodoInscripcion = PeriodoInscripcion::firstOrCreate([
            'nombre' => 'Inscripciones Segundo Semestre 2024'
        ], [
            'periodo_academico' => '2024-2',
            'fecha_inicio' => '2024-11-01',
            'fecha_fin' => '2024-12-15',
            'activo' => true,
            'descripcion' => 'Periodo de inscripciones para el segundo semestre 2024'
        ]);

        // Crear periodo académico activo
        $periodoAcademico = PeriodoAcademico::firstOrCreate([
            'codigo' => '2024-2'
        ], [
            'nombre' => 'Segundo Semestre 2024',
            'anio' => 2024,
            'semestre' => 2,
            'fecha_inicio' => '2024-11-15',
            'fecha_fin' => '2025-03-15',
            'estado' => 'activo',
            'es_actual' => true,
            'observaciones' => 'Periodo académico actual'
        ]);

        // Inscribir estudiantes a las materias del profesor
        $inscripcionesCreadas = 0;
        foreach ($estudiantesCreados as $estudiante) {
            // Inscribir a 2-3 materias aleatorias
            $materiasAInscribir = collect($gruposCreados)->random(rand(2, 3));
            
            foreach ($materiasAInscribir as $grupo) {
                $inscripcion = Inscripcion::firstOrCreate([
                    'estudiante_id' => $estudiante->id,
                    'grupo_id' => $grupo->id,
                    'periodo_academico' => '2024-2'
                ], [
                    'fecha_inscripcion' => now(),
                    'estado' => 'activo'
                ]);
                
                if ($inscripcion->wasRecentlyCreated) {
                    $inscripcionesCreadas++;
                }
            }
        }

        $this->command->info("✅ Inscripciones creadas: {$inscripcionesCreadas}");

        // Resumen final
        $this->command->newLine();
        $this->command->info('🎉 DATOS CREADOS EXITOSAMENTE PARA PROF001');
        $this->command->newLine();
        
        $this->command->table(
            ['Elemento', 'Cantidad', 'Detalles'],
            [
                ['Materias', count($materiasCreadas), 'Programación I, Estructura de Datos, Ing. Software I'],
                ['Grupos', count($gruposCreados), 'Un grupo A por cada materia'],
                ['Estudiantes', count($estudiantesCreados), 'Estudiantes de ISC con códigos ISC2024001-008'],
                ['Aulas', count($aulasCreadas), 'Laboratorios y aulas teóricas'],
                ['Horarios', count($cargasCreadas), 'Lunes, Miércoles, Viernes'],
                ['Inscripciones', $inscripcionesCreadas, 'Estudiantes inscritos en materias'],
            ]
        );

        $this->command->newLine();
        $this->command->info('📋 CREDENCIALES DE ESTUDIANTES CREADOS:');
        $this->command->newLine();
        
        foreach ($estudiantesCreados as $estudiante) {
            $this->command->line("👤 {$estudiante->nombre} {$estudiante->apellido}");
            $this->command->line("   Email: {$estudiante->email}");
            $this->command->line("   Password: student123");
            $this->command->line("   Código: {$estudiante->codigo_estudiante}");
            $this->command->line("   --------------------------------");
        }

        $this->command->newLine();
        $this->command->info('🎯 PARA PROBAR:');
        $this->command->info('1. Login como PROF001: juan.perez@universidad.edu / password123');
        $this->command->info('2. Ver "Mi Horario" - debe mostrar las 3 materias');
        $this->command->info('3. Generar QR para marcar asistencia');
        $this->command->info('4. Login como estudiante y marcar asistencia');
        $this->command->info('5. Ver reportes de asistencia');
        $this->command->newLine();
    }
}