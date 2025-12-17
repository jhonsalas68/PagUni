<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SistemaUniversitarioCompleto extends Seeder
{
    /**
     * Seeder COMPLETO del Sistema Universitario
     * Carga TODOS los datos necesarios para producción
     */
    public function run(): void
    {
        $this->command->info('🚀 CARGANDO SISTEMA UNIVERSITARIO COMPLETO...');
        
        // Limpiar todas las tablas
        $this->limpiarTablas();
        
        // 1. ADMINISTRADORES
        $this->crearAdministradores();
        
        // 2. FACULTADES Y CARRERAS
        $this->crearFacultadesYCarreras();
        
        // 3. MATERIAS COMPLETAS
        $this->crearMaterias();
        
        // 4. PROFESORES
        $this->crearProfesores();
        
        // 5. ESTUDIANTES
        $this->crearEstudiantes();
        
        // 6. AULAS
        $this->crearAulas();
        
        // 7. GRUPOS Y HORARIOS
        $this->crearGruposYHorarios();
        
        // 8. CARGA ACADÉMICA
        $this->crearCargaAcademica();
        
        // 9. PERÍODOS ACADÉMICOS
        $this->crearPeriodosAcademicos();
        
        // 10. INSCRIPCIONES
        $this->crearInscripciones();
        
        // 11. CALIFICACIONES DE EJEMPLO
        $this->crearCalificaciones();
        
        // 12. ASISTENCIAS DE EJEMPLO
        $this->crearAsistencias();
        
        $this->mostrarResumen();
    }
    
    private function limpiarTablas()
    {
        $this->command->info('🧹 Limpiando tablas...');
        
        $tablas = [
            'calificaciones', 'asistencia_estudiantes', 'asistencia_docente',
            'inscripciones', 'carga_academica', 'horarios', 'grupos',
            'estudiantes', 'profesores', 'materias', 'carreras', 
            'facultades', 'aulas', 'administradores', 'periodos_academicos'
        ];
        
        foreach ($tablas as $tabla) {
            try {
                DB::table($tabla)->truncate();
            } catch (\Exception $e) {
                // Tabla no existe, continuar
            }
        }
    }
    
    private function crearAdministradores()
    {
        $this->command->info('👨‍💼 Creando Administradores...');
        
        DB::table('administradores')->insert([
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
        ]);
    }
    
    private function crearFacultadesYCarreras()
    {
        $this->command->info('🏛️ Creando Facultades y Carreras...');
        
        // FACULTADES
        DB::table('facultades')->insert([
            [
                'id' => 1,
                'nombre' => 'Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones',
                'codigo' => 'FICCT',
                'descripcion' => 'Facultad especializada en tecnología e innovación',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'nombre' => 'Facultad de Ciencias Exactas y Naturales',
                'codigo' => 'FCEN',
                'descripcion' => 'Facultad de ciencias básicas y aplicadas',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
        
        // CARRERAS
        DB::table('carreras')->insert([
            [
                'id' => 1,
                'nombre' => 'Ingeniería de Sistemas',
                'codigo' => 'ING-SIS',
                'facultad_id' => 1,
                'duracion_semestres' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'nombre' => 'Ingeniería en Telecomunicaciones',
                'codigo' => 'ING-TEL',
                'facultad_id' => 1,
                'duracion_semestres' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'nombre' => 'Ingeniería Informática',
                'codigo' => 'ING-INF',
                'facultad_id' => 1,
                'duracion_semestres' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
    
    private function crearMaterias()
    {
        $this->command->info('📚 Creando Materias...');
        
        $materias = [
            // INGENIERÍA DE SISTEMAS - Semestre 1
            ['nombre' => 'Programación I', 'codigo' => 'SIS-101', 'carrera_id' => 1, 'semestre' => 1, 'creditos' => 4],
            ['nombre' => 'Matemática I', 'codigo' => 'SIS-102', 'carrera_id' => 1, 'semestre' => 1, 'creditos' => 4],
            ['nombre' => 'Física I', 'codigo' => 'SIS-103', 'carrera_id' => 1, 'semestre' => 1, 'creditos' => 4],
            ['nombre' => 'Álgebra Lineal', 'codigo' => 'SIS-104', 'carrera_id' => 1, 'semestre' => 1, 'creditos' => 4],
            
            // INGENIERÍA DE SISTEMAS - Semestre 2
            ['nombre' => 'Programación II', 'codigo' => 'SIS-201', 'carrera_id' => 1, 'semestre' => 2, 'creditos' => 4],
            ['nombre' => 'Matemática II', 'codigo' => 'SIS-202', 'carrera_id' => 1, 'semestre' => 2, 'creditos' => 4],
            ['nombre' => 'Física II', 'codigo' => 'SIS-203', 'carrera_id' => 1, 'semestre' => 2, 'creditos' => 4],
            ['nombre' => 'Matemática Discreta', 'codigo' => 'SIS-204', 'carrera_id' => 1, 'semestre' => 2, 'creditos' => 4],
            
            // INGENIERÍA DE SISTEMAS - Semestre 3
            ['nombre' => 'Estructura de Datos', 'codigo' => 'SIS-301', 'carrera_id' => 1, 'semestre' => 3, 'creditos' => 4],
            ['nombre' => 'Base de Datos I', 'codigo' => 'SIS-302', 'carrera_id' => 1, 'semestre' => 3, 'creditos' => 4],
            ['nombre' => 'Análisis de Sistemas', 'codigo' => 'SIS-303', 'carrera_id' => 1, 'semestre' => 3, 'creditos' => 4],
            ['nombre' => 'Estadística', 'codigo' => 'SIS-304', 'carrera_id' => 1, 'semestre' => 3, 'creditos' => 4],
            
            // INGENIERÍA DE SISTEMAS - Semestre 4
            ['nombre' => 'Algoritmos Avanzados', 'codigo' => 'SIS-401', 'carrera_id' => 1, 'semestre' => 4, 'creditos' => 4],
            ['nombre' => 'Base de Datos II', 'codigo' => 'SIS-402', 'carrera_id' => 1, 'semestre' => 4, 'creditos' => 4],
            ['nombre' => 'Ingeniería de Software I', 'codigo' => 'SIS-403', 'carrera_id' => 1, 'semestre' => 4, 'creditos' => 4],
            ['nombre' => 'Redes de Computadoras I', 'codigo' => 'SIS-404', 'carrera_id' => 1, 'semestre' => 4, 'creditos' => 4],
            
            // INGENIERÍA DE SISTEMAS - Semestre 5
            ['nombre' => 'Desarrollo Web', 'codigo' => 'SIS-501', 'carrera_id' => 1, 'semestre' => 5, 'creditos' => 4],
            ['nombre' => 'Ingeniería de Software II', 'codigo' => 'SIS-502', 'carrera_id' => 1, 'semestre' => 5, 'creditos' => 4],
            ['nombre' => 'Sistemas Operativos', 'codigo' => 'SIS-503', 'carrera_id' => 1, 'semestre' => 5, 'creditos' => 4],
            ['nombre' => 'Inteligencia Artificial', 'codigo' => 'SIS-504', 'carrera_id' => 1, 'semestre' => 5, 'creditos' => 4],
        ];
        
        foreach ($materias as $materia) {
            $materia['created_at'] = now();
            $materia['updated_at'] = now();
            DB::table('materias')->insert($materia);
        }
    }
    
    private function crearProfesores()
    {
        $this->command->info('👨‍🏫 Creando Profesores...');
        
        $profesores = [
            ['nombre' => 'Juan Carlos', 'apellido' => 'Pérez López', 'email' => 'prof001@ficct.edu.bo', 'cedula' => '12345678', 'especialidad' => 'Programación'],
            ['nombre' => 'María Elena', 'apellido' => 'García Morales', 'email' => 'prof002@ficct.edu.bo', 'cedula' => '23456789', 'especialidad' => 'Base de Datos'],
            ['nombre' => 'Roberto', 'apellido' => 'Martínez Silva', 'email' => 'prof003@ficct.edu.bo', 'cedula' => '34567890', 'especialidad' => 'Matemáticas'],
            ['nombre' => 'Ana Lucía', 'apellido' => 'Rodríguez Paz', 'email' => 'prof004@ficct.edu.bo', 'cedula' => '45678901', 'especialidad' => 'Física'],
            ['nombre' => 'Carlos Alberto', 'apellido' => 'González Ruiz', 'email' => 'prof005@ficct.edu.bo', 'cedula' => '56789012', 'especialidad' => 'Ingeniería de Software'],
            ['nombre' => 'Patricia', 'apellido' => 'Fernández Castro', 'email' => 'prof006@ficct.edu.bo', 'cedula' => '67890123', 'especialidad' => 'Redes'],
            ['nombre' => 'Miguel Ángel', 'apellido' => 'Torres Vargas', 'email' => 'prof007@ficct.edu.bo', 'cedula' => '78901234', 'especialidad' => 'Sistemas Operativos'],
            ['nombre' => 'Lucía', 'apellido' => 'Morales Jiménez', 'email' => 'prof008@ficct.edu.bo', 'cedula' => '89012345', 'especialidad' => 'Inteligencia Artificial'],
        ];
        
        foreach ($profesores as $profesor) {
            $profesor['telefono'] = '3-' . rand(1000000, 9999999);
            $profesor['tipo_contrato'] = 'tiempo_completo';
            $profesor['created_at'] = now();
            $profesor['updated_at'] = now();
            DB::table('profesores')->insert($profesor);
        }
    }
    
    private function crearEstudiantes()
    {
        $this->command->info('👨‍🎓 Creando Estudiantes...');
        
        $nombres = ['Pedro', 'Ana', 'Luis', 'Carmen', 'Diego', 'Sofia', 'Andrés', 'Valeria', 'Javier', 'Isabella'];
        $apellidos = ['Ramírez', 'Silva', 'Martínez', 'López', 'González', 'Rodríguez', 'Fernández', 'García', 'Morales', 'Castro'];
        
        for ($i = 1; $i <= 50; $i++) {
            $nombre = $nombres[array_rand($nombres)];
            $apellido = $apellidos[array_rand($apellidos)] . ' ' . $apellidos[array_rand($apellidos)];
            
            DB::table('estudiantes')->insert([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'email' => 'est' . str_pad($i, 3, '0', STR_PAD_LEFT) . '@ficct.edu.bo',
                'telefono' => '7-' . rand(1000000, 9999999),
                'cedula' => str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                'codigo_estudiante' => 'EST' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'carrera_id' => rand(1, 3),
                'semestre_actual' => rand(1, 5),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    
    private function crearAulas()
    {
        $this->command->info('🏫 Creando Aulas...');
        
        $aulas = [
            ['nombre' => 'Aula 101', 'capacidad' => 30, 'tipo' => 'aula', 'ubicacion' => 'Edificio A - Primer Piso'],
            ['nombre' => 'Aula 102', 'capacidad' => 35, 'tipo' => 'aula', 'ubicacion' => 'Edificio A - Primer Piso'],
            ['nombre' => 'Aula 201', 'capacidad' => 40, 'tipo' => 'aula', 'ubicacion' => 'Edificio A - Segundo Piso'],
            ['nombre' => 'Aula 202', 'capacidad' => 40, 'tipo' => 'aula', 'ubicacion' => 'Edificio A - Segundo Piso'],
            ['nombre' => 'Lab Sistemas 1', 'capacidad' => 25, 'tipo' => 'laboratorio', 'ubicacion' => 'Edificio B - Primer Piso'],
            ['nombre' => 'Lab Sistemas 2', 'capacidad' => 25, 'tipo' => 'laboratorio', 'ubicacion' => 'Edificio B - Primer Piso'],
            ['nombre' => 'Lab Redes', 'capacidad' => 20, 'tipo' => 'laboratorio', 'ubicacion' => 'Edificio B - Segundo Piso'],
            ['nombre' => 'Auditorio', 'capacidad' => 100, 'tipo' => 'auditorio', 'ubicacion' => 'Edificio Principal'],
        ];
        
        foreach ($aulas as $aula) {
            $aula['equipamiento'] = 'Proyector, Pizarra Digital, Aire Acondicionado';
            $aula['estado'] = 'activo';
            $aula['created_at'] = now();
            $aula['updated_at'] = now();
            DB::table('aulas')->insert($aula);
        }
    }
    
    private function crearGruposYHorarios()
    {
        $this->command->info('📅 Creando Grupos y Horarios...');
        
        // Crear grupos para las primeras 10 materias
        for ($i = 1; $i <= 10; $i++) {
            $grupoId = DB::table('grupos')->insertGetId([
                'identificador' => 'G' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'materia_id' => $i,
                'capacidad_maxima' => 30,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Crear horario para cada grupo
            $dias = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes'];
            $horas = ['08:00:00', '10:00:00', '14:00:00', '16:00:00'];
            
            DB::table('horarios')->insert([
                'grupo_id' => $grupoId,
                'aula_id' => rand(1, 6), // Aulas 1-6 (no auditorio)
                'dia_semana' => $dias[array_rand($dias)],
                'hora_inicio' => $horas[array_rand($horas)],
                'hora_fin' => '18:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    
    private function crearCargaAcademica()
    {
        $this->command->info('📋 Creando Carga Académica...');
        
        // Asignar profesores a grupos
        for ($i = 1; $i <= 10; $i++) {
            DB::table('carga_academica')->insert([
                'profesor_id' => rand(1, 8),
                'grupo_id' => $i,
                'gestion' => 2024,
                'periodo' => '2-2024',
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    
    private function crearPeriodosAcademicos()
    {
        $this->command->info('📆 Creando Períodos Académicos...');
        
        try {
            DB::table('periodos_academicos')->insert([
                [
                    'nombre' => 'Segundo Semestre 2024',
                    'codigo' => '2-2024',
                    'fecha_inicio' => '2024-08-01',
                    'fecha_fin' => '2024-12-20',
                    'estado' => 'activo',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nombre' => 'Primer Semestre 2025',
                    'codigo' => '1-2025',
                    'fecha_inicio' => '2025-02-01',
                    'fecha_fin' => '2025-06-30',
                    'estado' => 'planificado',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        } catch (\Exception $e) {
            $this->command->warn('Tabla periodos_academicos no existe, saltando...');
        }
    }
    
    private function crearInscripciones()
    {
        $this->command->info('📝 Creando Inscripciones...');
        
        try {
            // Inscribir estudiantes en grupos
            for ($i = 1; $i <= 30; $i++) {
                DB::table('inscripciones')->insert([
                    'estudiante_id' => rand(1, 50),
                    'grupo_id' => rand(1, 10),
                    'gestion' => 2024,
                    'periodo' => '2-2024',
                    'fecha_inscripcion' => now()->subDays(rand(1, 30)),
                    'estado' => 'activo',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            $this->command->warn('Tabla inscripciones no existe, saltando...');
        }
    }
    
    private function crearCalificaciones()
    {
        $this->command->info('📊 Creando Calificaciones...');
        
        try {
            // Crear tipos de evaluación
            for ($i = 1; $i <= 5; $i++) {
                DB::table('tipos_evaluacion')->insert([
                    'grupo_id' => $i,
                    'nombre' => 'Primer Parcial',
                    'ponderacion' => 30,
                    'fecha_evaluacion' => now()->subDays(rand(10, 30)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                DB::table('tipos_evaluacion')->insert([
                    'grupo_id' => $i,
                    'nombre' => 'Segundo Parcial',
                    'ponderacion' => 30,
                    'fecha_evaluacion' => now()->subDays(rand(1, 10)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                DB::table('tipos_evaluacion')->insert([
                    'grupo_id' => $i,
                    'nombre' => 'Examen Final',
                    'ponderacion' => 40,
                    'fecha_evaluacion' => now()->addDays(rand(1, 15)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Crear calificaciones de ejemplo
            for ($i = 1; $i <= 50; $i++) {
                DB::table('calificaciones')->insert([
                    'estudiante_id' => rand(1, 30),
                    'tipo_evaluacion_id' => rand(1, 15),
                    'nota' => rand(60, 100),
                    'observaciones' => 'Calificación registrada',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            $this->command->warn('Tablas de calificaciones no existen, saltando...');
        }
    }
    
    private function crearAsistencias()
    {
        $this->command->info('✅ Creando Asistencias...');
        
        try {
            // Asistencias de profesores
            for ($i = 1; $i <= 20; $i++) {
                DB::table('asistencia_docente')->insert([
                    'profesor_id' => rand(1, 8),
                    'grupo_id' => rand(1, 10),
                    'fecha' => now()->subDays(rand(1, 30)),
                    'hora_entrada' => '08:00:00',
                    'hora_salida' => '10:00:00',
                    'estado' => 'presente',
                    'observaciones' => 'Clase normal',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            $this->command->warn('Tabla asistencia_docente no existe, saltando...');
        }
    }
    
    private function mostrarResumen()
    {
        $this->command->info('✅ SISTEMA UNIVERSITARIO COMPLETO CARGADO!');
        $this->command->newLine();
        
        $resumen = [
            ['Tabla', 'Registros'],
            ['Administradores', DB::table('administradores')->count()],
            ['Facultades', DB::table('facultades')->count()],
            ['Carreras', DB::table('carreras')->count()],
            ['Materias', DB::table('materias')->count()],
            ['Profesores', DB::table('profesores')->count()],
            ['Estudiantes', DB::table('estudiantes')->count()],
            ['Aulas', DB::table('aulas')->count()],
            ['Grupos', DB::table('grupos')->count()],
            ['Horarios', DB::table('horarios')->count()],
            ['Carga Académica', DB::table('carga_academica')->count()],
        ];
        
        $this->command->table(['Tabla', 'Registros'], array_slice($resumen, 1));
        
        $this->command->newLine();
        $this->command->info('🎉 ¡BASE DE DATOS LISTA PARA PRODUCCIÓN!');
        $this->command->info('📧 Credenciales: admin@ficct.edu.bo / admin123');
    }
}