<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facultad;
use App\Models\Carrera;
use App\Models\Materia;
use App\Models\Aula;
use App\Models\Grupo;
use App\Models\Profesor;
use App\Models\CargaAcademica;
use App\Models\Horario;
use Illuminate\Support\Facades\DB;

class FICCTCompletaSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();
        
        try {
            // Limpiar datos existentes
            $this->limpiarDatos();
            
            // Crear estructura base
            $facultad = $this->crearFacultad();
            $carrera = $this->crearCarrera($facultad);
            $aulas = $this->crearAulas();
            $materias = $this->crearMaterias($carrera);
            $profesores = $this->crearProfesores();
            
            // Crear grupos, cargas y horarios
            $this->crearHorarios($materias, $aulas, $profesores, $carrera);
            
            DB::commit();
            $this->command->info('✅ Datos de FICCT cargados exitosamente');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function limpiarDatos(): void
    {
        $this->command->info('🧹 Limpiando datos existentes...');
        
        // Eliminar en orden correcto por dependencias
        Horario::truncate();
        CargaAcademica::truncate();
        Grupo::truncate();
        Profesor::truncate();
        Materia::truncate();
        Aula::truncate();
        Carrera::truncate();
        Facultad::truncate();
        
        $this->command->info('✓ Datos limpiados');
    }

    private function crearFacultad(): Facultad
    {
        $this->command->info('🏛️ Creando Facultad...');
        
        return Facultad::create([
            'nombre' => 'Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones',
            'codigo' => 'FICCT',
            'descripcion' => 'Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones - UAGRM'
        ]);
    }

    private function crearCarrera(Facultad $facultad): Carrera
    {
        $this->command->info('🎓 Creando Carreras...');
        
        // Carrera principal con malla curricular completa
        $carreraInformatica = Carrera::create([
            'facultad_id' => $facultad->id,
            'nombre' => 'Ingeniería Informática',
            'codigo' => 'INF',
            'duracion_semestres' => 10,
            'descripcion' => 'Carrera de Ingeniería Informática - Malla curricular completa'
        ]);
        
        // Otras carreras de la FICCT (sin malla curricular por ahora)
        Carrera::create([
            'facultad_id' => $facultad->id,
            'nombre' => 'Ingeniería de Sistemas',
            'codigo' => 'ISC',
            'duracion_semestres' => 10,
            'descripcion' => 'Carrera de Ingeniería de Sistemas'
        ]);
        
        Carrera::create([
            'facultad_id' => $facultad->id,
            'nombre' => 'Ingeniería en Redes y Telecomunicaciones',
            'codigo' => 'IRT',
            'duracion_semestres' => 10,
            'descripcion' => 'Carrera de Ingeniería en Redes y Telecomunicaciones'
        ]);
        
        $this->command->info('✓ 3 carreras creadas (1 con malla curricular)');
        
        return $carreraInformatica;
    }

    private function crearAulas(): array
    {
        $this->command->info('🚪 Creando Aulas del Módulo 236...');
        
        $aulasData = [
            // Aulas Normales
            ['codigo' => 'Aula 10', 'nombre' => 'Aula 10', 'capacidad' => 40, 'tipo' => 'aula'],
            ['codigo' => 'Aula 11', 'nombre' => 'Aula 11', 'capacidad' => 40, 'tipo' => 'aula'],
            ['codigo' => 'Aula 12', 'nombre' => 'Aula 12', 'capacidad' => 40, 'tipo' => 'aula'],
            ['codigo' => 'Aula 13', 'nombre' => 'Aula 13', 'capacidad' => 40, 'tipo' => 'aula'],
            ['codigo' => 'Aula 14', 'nombre' => 'Aula 14', 'capacidad' => 40, 'tipo' => 'aula'],
            ['codigo' => 'Aula 15', 'nombre' => 'Aula 15', 'capacidad' => 40, 'tipo' => 'aula'],
            ['codigo' => 'Aula 16', 'nombre' => 'Aula 16', 'capacidad' => 40, 'tipo' => 'aula'],
            ['codigo' => 'Aula 20', 'nombre' => 'Aula 20', 'capacidad' => 40, 'tipo' => 'aula'],
            ['codigo' => 'Aula 21', 'nombre' => 'Aula 21', 'capacidad' => 40, 'tipo' => 'aula'],
            ['codigo' => 'Aula 22', 'nombre' => 'Aula 22', 'capacidad' => 40, 'tipo' => 'aula'],
            ['codigo' => 'Aula 23', 'nombre' => 'Aula 23', 'capacidad' => 40, 'tipo' => 'aula'],
            ['codigo' => 'Aula 24', 'nombre' => 'Aula 24', 'capacidad' => 40, 'tipo' => 'aula'],
            ['codigo' => 'Aula 25', 'nombre' => 'Aula 25', 'capacidad' => 40, 'tipo' => 'aula'],
            ['codigo' => 'Aula 26', 'nombre' => 'Aula 26', 'capacidad' => 40, 'tipo' => 'aula'],
            ['codigo' => 'Aula 30', 'nombre' => 'Aula 30', 'capacidad' => 40, 'tipo' => 'aula'],
            ['codigo' => 'Aula 31', 'nombre' => 'Aula 31', 'capacidad' => 40, 'tipo' => 'aula'],
            ['codigo' => 'Aula 32', 'nombre' => 'Aula 32', 'capacidad' => 40, 'tipo' => 'aula'],
            ['codigo' => 'Aula 33', 'nombre' => 'Aula 33', 'capacidad' => 40, 'tipo' => 'aula'],
            ['codigo' => 'Aula 34', 'nombre' => 'Aula 34', 'capacidad' => 40, 'tipo' => 'aula'],
            ['codigo' => 'Aula 35', 'nombre' => 'Aula 35', 'capacidad' => 40, 'tipo' => 'aula'],
            ['codigo' => 'Aula 36', 'nombre' => 'Aula 36', 'capacidad' => 40, 'tipo' => 'aula'],
            
            // Laboratorios
            ['codigo' => 'Lab 40', 'nombre' => 'Laboratorio 40', 'capacidad' => 30, 'tipo' => 'laboratorio'],
            ['codigo' => 'Lab 41', 'nombre' => 'Laboratorio 41', 'capacidad' => 30, 'tipo' => 'laboratorio'],
            ['codigo' => 'Lab 42', 'nombre' => 'Laboratorio 42', 'capacidad' => 30, 'tipo' => 'laboratorio'],
            ['codigo' => 'Lab 43', 'nombre' => 'Laboratorio 43', 'capacidad' => 30, 'tipo' => 'laboratorio'],
            ['codigo' => 'Lab 44', 'nombre' => 'Laboratorio 44', 'capacidad' => 30, 'tipo' => 'laboratorio'],
            ['codigo' => 'Lab 45', 'nombre' => 'Laboratorio 45', 'capacidad' => 30, 'tipo' => 'laboratorio'],
            ['codigo' => 'Lab 46', 'nombre' => 'Laboratorio 46', 'capacidad' => 30, 'tipo' => 'laboratorio'],
            
            // Auditorio
            ['codigo' => 'Auditorio (99)', 'nombre' => 'Auditorio Principal', 'capacidad' => 150, 'tipo' => 'auditorio'],
        ];

        $aulas = [];
        foreach ($aulasData as $data) {
            $aulas[$data['codigo']] = Aula::create([
                'codigo_aula' => $data['codigo'],
                'nombre' => $data['nombre'],
                'capacidad' => $data['capacidad'],
                'tipo_aula' => $data['tipo'],
                'edificio' => 'Módulo 236',
                'piso' => '1',
                'estado' => 'disponible'
            ]);
        }

        return $aulas;
    }

    private function crearMaterias(Carrera $carrera): array
    {
        $this->command->info('📚 Creando Materias...');
        
        // Obtener todas las carreras para asignar materias compartidas
        $todasLasCarreras = Carrera::all();
        
        $materiasData = [
            // Primer Semestre
            ['codigo' => 'MAT101', 'nombre' => 'Cálculo I', 'semestre' => 1, 'horas' => 4],
            ['codigo' => 'INF119', 'nombre' => 'Estructuras Discretas', 'semestre' => 1, 'horas' => 4],
            ['codigo' => 'INF110', 'nombre' => 'Introducción a la Informática', 'semestre' => 1, 'horas' => 4],
            ['codigo' => 'MAT103', 'nombre' => 'Álgebra Lineal', 'semestre' => 1, 'horas' => 4],
            ['codigo' => 'FIS100', 'nombre' => 'Física I', 'semestre' => 1, 'horas' => 4],
            ['codigo' => 'LIN100', 'nombre' => 'Inglés Técnico I', 'semestre' => 1, 'horas' => 3],
            
            // Segundo Semestre
            ['codigo' => 'INF210', 'nombre' => 'Programación II', 'semestre' => 2, 'horas' => 4],
            ['codigo' => 'MAT202', 'nombre' => 'Cálculo II', 'semestre' => 2, 'horas' => 4],
            ['codigo' => 'MAT207', 'nombre' => 'Ecuaciones Diferenciales', 'semestre' => 2, 'horas' => 4],
            ['codigo' => 'MAT205', 'nombre' => 'Métodos Numéricos', 'semestre' => 2, 'horas' => 4],
            ['codigo' => 'INF220', 'nombre' => 'Estructura de Datos I', 'semestre' => 2, 'horas' => 4],
            ['codigo' => 'INF211', 'nombre' => 'Arquitectura de Computadoras', 'semestre' => 2, 'horas' => 4],
            ['codigo' => 'INF221', 'nombre' => 'Programación Ensamblador', 'semestre' => 2, 'horas' => 4],
            ['codigo' => 'FIS102', 'nombre' => 'Física II', 'semestre' => 2, 'horas' => 4],
            ['codigo' => 'LIN101', 'nombre' => 'Inglés Técnico II', 'semestre' => 2, 'horas' => 2],
            ['codigo' => 'ADM100', 'nombre' => 'Administración', 'semestre' => 2, 'horas' => 4],
            
            // Tercer Semestre
            ['codigo' => 'MAT302', 'nombre' => 'Probabilidad y Estadística I', 'semestre' => 3, 'horas' => 4],
            ['codigo' => 'INF318', 'nombre' => 'Programación Lógica y Funcional', 'semestre' => 3, 'horas' => 4],
            ['codigo' => 'INF310', 'nombre' => 'Estructura de Datos II', 'semestre' => 3, 'horas' => 4],
            ['codigo' => 'INF312', 'nombre' => 'Bases de Datos I', 'semestre' => 3, 'horas' => 4],
            ['codigo' => 'INF319', 'nombre' => 'Lenguajes Formales', 'semestre' => 3, 'horas' => 4],
            ['codigo' => 'ADM200', 'nombre' => 'Contabilidad', 'semestre' => 3, 'horas' => 4],
            
            // Cuarto Semestre
            ['codigo' => 'MAT329', 'nombre' => 'Probabilidad y Estadística II', 'semestre' => 4, 'horas' => 4],
            ['codigo' => 'INF342', 'nombre' => 'Sistemas de Información I', 'semestre' => 4, 'horas' => 4],
            ['codigo' => 'INF323', 'nombre' => 'Sistemas Operativos I', 'semestre' => 4, 'horas' => 4],
            ['codigo' => 'INF322', 'nombre' => 'Bases de Datos II', 'semestre' => 4, 'horas' => 4],
            ['codigo' => 'INF329', 'nombre' => 'Compiladores', 'semestre' => 4, 'horas' => 4],
            ['codigo' => 'FIS200', 'nombre' => 'Física III', 'semestre' => 4, 'horas' => 4],
            ['codigo' => 'INF412', 'nombre' => 'Sistemas de Información', 'semestre' => 4, 'horas' => 4],
            
            // Quinto Semestre
            ['codigo' => 'MAT419', 'nombre' => 'Investigación Operativa I', 'semestre' => 5, 'horas' => 4],
            ['codigo' => 'INF418', 'nombre' => 'Inteligencia Artificial', 'semestre' => 5, 'horas' => 4],
            ['codigo' => 'INF413', 'nombre' => 'Sistemas Operativos II', 'semestre' => 5, 'horas' => 4],
            ['codigo' => 'INF433', 'nombre' => 'Redes I', 'semestre' => 5, 'horas' => 4],
            ['codigo' => 'INF422', 'nombre' => 'Ingeniería de Software I', 'semestre' => 5, 'horas' => 4],

            // Sexto Semestre
            ['codigo' => 'MAT429', 'nombre' => 'Investigación Operativa II', 'semestre' => 6, 'horas' => 4],
            ['codigo' => 'INF428', 'nombre' => 'Sistemas Expertos', 'semestre' => 6, 'horas' => 4],
            ['codigo' => 'INF442', 'nombre' => 'Sistemas de Información Geográfica', 'semestre' => 6, 'horas' => 4],
            ['codigo' => 'INF423', 'nombre' => 'Redes II', 'semestre' => 6, 'horas' => 4],
            ['codigo' => 'INF552', 'nombre' => 'Arquitectura de Software', 'semestre' => 6, 'horas' => 4],
            ['codigo' => 'INF513', 'nombre' => 'Tecnología Web', 'semestre' => 6, 'horas' => 4],
            
            // Séptimo Semestre
            ['codigo' => 'INF528', 'nombre' => 'Sistemas Distribuidos', 'semestre' => 7, 'horas' => 4],
            ['codigo' => 'INF533', 'nombre' => 'Minería de Datos', 'semestre' => 7, 'horas' => 4],
            ['codigo' => 'INF539', 'nombre' => 'Comercio Electrónico', 'semestre' => 7, 'horas' => 4],
            ['codigo' => 'INF542', 'nombre' => 'Seguridad Informática I', 'semestre' => 7, 'horas' => 4],
            ['codigo' => 'ADM400', 'nombre' => 'Administración de Proyectos', 'semestre' => 7, 'horas' => 4],
            ['codigo' => 'FIL401', 'nombre' => 'Ética Profesional', 'semestre' => 7, 'horas' => 2],
            
            // Octavo Semestre
            ['codigo' => 'INF628', 'nombre' => 'Taller de Base de Datos', 'semestre' => 8, 'horas' => 4],
            ['codigo' => 'INF633', 'nombre' => 'Auditoría Informática', 'semestre' => 8, 'horas' => 4],
            ['codigo' => 'INF639', 'nombre' => 'Simulación de Sistemas', 'semestre' => 8, 'horas' => 4],
            ['codigo' => 'INF642', 'nombre' => 'Seguridad Informática II', 'semestre' => 8, 'horas' => 4],
            ['codigo' => 'ADM500', 'nombre' => 'Formulación y Evaluación de Proyectos', 'semestre' => 8, 'horas' => 4],
            ['codigo' => 'DER501', 'nombre' => 'Derecho Informático', 'semestre' => 8, 'horas' => 2],
            
            // Noveno Semestre
            ['codigo' => 'TG601', 'nombre' => 'Taller de Grado I', 'semestre' => 9, 'horas' => 4],
            ['codigo' => 'INF728', 'nombre' => 'Desarrollo Web Avanzado', 'semestre' => 9, 'horas' => 4],
            ['codigo' => 'INF733', 'nombre' => 'Teletrabajo y Outsourcing', 'semestre' => 9, 'horas' => 4],
            ['codigo' => 'INF739', 'nombre' => 'Ingeniería de Requisitos', 'semestre' => 9, 'horas' => 4],
            ['codigo' => 'INF742', 'nombre' => 'Mantenimiento y Calidad de Sistemas', 'semestre' => 9, 'horas' => 4],
            
            // Décimo Semestre
            ['codigo' => 'TG701', 'nombre' => 'Taller de Grado II', 'semestre' => 10, 'horas' => 4],
            ['codigo' => 'PP701', 'nombre' => 'Prácticas Pre-Profesionales', 'semestre' => 10, 'horas' => 4],
            ['codigo' => 'EMP701', 'nombre' => 'Cátedra de Emprendimiento', 'semestre' => 10, 'horas' => 4],
        ];

        $materias = [];
        foreach ($materiasData as $data) {
            // Las materias de los primeros 4 semestres son compartidas entre las 3 carreras
            if ($data['semestre'] <= 4) {
                // Crear la materia para cada carrera con código único
                foreach ($todasLasCarreras as $index => $carreraItem) {
                    // Agregar sufijo al código para hacerlo único: MAT101-INF, MAT101-ISC, MAT101-IRT
                    $codigoUnico = $data['codigo'] . '-' . $carreraItem->codigo;
                    
                    $materia = Materia::create([
                        'carrera_id' => $carreraItem->id,
                        'codigo' => $codigoUnico,
                        'nombre' => $data['nombre'],
                        'semestre' => $data['semestre'],
                        'horas_teoricas' => $data['horas'],
                        'horas_practicas' => 0,
                        'creditos' => ceil($data['horas'] / 2),
                        'descripcion' => 'Materia compartida - ' . $data['nombre'] . ' (' . $carreraItem->nombre . ')'
                    ]);
                    
                    // Guardar solo la de Ingeniería Informática para los horarios
                    if ($carreraItem->id === $carrera->id) {
                        $materias[$data['codigo']] = $materia;
                    }
                }
            } else {
                // Materias del 5to semestre en adelante solo para Ingeniería Informática
                $materias[$data['codigo']] = Materia::create([
                    'carrera_id' => $carrera->id,
                    'codigo' => $data['codigo'] . '-INF',
                    'nombre' => $data['nombre'],
                    'semestre' => $data['semestre'],
                    'horas_teoricas' => $data['horas'],
                    'horas_practicas' => 0,
                    'creditos' => ceil($data['horas'] / 2),
                    'descripcion' => 'Materia de ' . $data['nombre']
                ]);
            }
        }

        $this->command->info('✓ Materias compartidas (semestres 1-4) creadas para las 3 carreras');
        $this->command->info('✓ Materias específicas (semestres 5-10) solo para Ingeniería Informática');

        return $materias;
    }

    private function crearProfesores(): array
    {
        $this->command->info('👨‍🏫 Creando Profesores...');
        
        $profesoresData = [
            ['codigo' => 'PROF001', 'nombre' => 'Juan Carlos', 'apellido' => 'Pérez García', 'email' => 'jperez@uagrm.edu.bo'],
            ['codigo' => 'PROF002', 'nombre' => 'María Elena', 'apellido' => 'González Rojas', 'email' => 'mgonzalez@uagrm.edu.bo'],
            ['codigo' => 'PROF003', 'nombre' => 'Roberto', 'apellido' => 'Sánchez Méndez', 'email' => 'rsanchez@uagrm.edu.bo'],
            ['codigo' => 'PROF004', 'nombre' => 'Ana Patricia', 'apellido' => 'Martínez López', 'email' => 'amartinez@uagrm.edu.bo'],
            ['codigo' => 'PROF005', 'nombre' => 'Carlos Alberto', 'apellido' => 'López Fernández', 'email' => 'clopez@uagrm.edu.bo'],
            ['codigo' => 'PROF006', 'nombre' => 'Laura Beatriz', 'apellido' => 'Fernández Torres', 'email' => 'lfernandez@uagrm.edu.bo'],
            ['codigo' => 'PROF007', 'nombre' => 'Pedro Antonio', 'apellido' => 'Ramírez Silva', 'email' => 'pramirez@uagrm.edu.bo'],
            ['codigo' => 'PROF008', 'nombre' => 'Sofia Isabel', 'apellido' => 'Torres Vargas', 'email' => 'storres@uagrm.edu.bo'],
            ['codigo' => 'PROF009', 'nombre' => 'Miguel Ángel', 'apellido' => 'Ruiz Moreno', 'email' => 'mruiz@uagrm.edu.bo'],
            ['codigo' => 'PROF010', 'nombre' => 'Patricia Andrea', 'apellido' => 'Morales Castro', 'email' => 'pmorales@uagrm.edu.bo'],
        ];

        $profesores = [];
        foreach ($profesoresData as $data) {
            $profesores[$data['codigo']] = Profesor::create([
                'codigo_docente' => $data['codigo'],
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'email' => $data['email'],
                'telefono' => '7' . rand(1000000, 9999999),
                'cedula' => rand(1000000, 9999999),
                'especialidad' => 'Ingeniería de Sistemas',
                'tipo_contrato' => 'tiempo_completo',
                'estado' => 'activo',
                'password' => bcrypt('password123')
            ]);
        }

        return $profesores;
    }

    private function crearHorarios($materias, $aulas, $profesores, $carrera): void
    {
        $this->command->info('📅 Creando Horarios...');
        
        $periodo = '2025-1';
        $profesoresArray = array_values($profesores);
        
        $horariosData = [
            // Cálculo I - MAT101
            ['materia' => 'MAT101', 'grupo' => 'CA', 'dias' => ['lunes', 'miercoles', 'viernes'], 'inicio' => '07:00', 'fin' => '08:30', 'aula' => 'Auditorio (99)'],
            ['materia' => 'MAT101', 'grupo' => 'SZ', 'dias' => ['martes', 'jueves'], 'inicio' => '09:15', 'fin' => '10:45', 'aula' => 'Aula 10'],
            ['materia' => 'MAT101', 'grupo' => 'SC', 'dias' => ['lunes', 'miercoles'], 'inicio' => '14:00', 'fin' => '15:30', 'aula' => 'Aula 11'],
            
            // Estructuras Discretas - INF119
            ['materia' => 'INF119', 'grupo' => 'A', 'dias' => ['martes', 'jueves'], 'inicio' => '07:00', 'fin' => '08:30', 'aula' => 'Aula 12'],
            ['materia' => 'INF119', 'grupo' => 'B', 'dias' => ['lunes', 'miercoles'], 'inicio' => '11:30', 'fin' => '13:00', 'aula' => 'Aula 13'],
            
            // Introducción a la Informática - INF110
            ['materia' => 'INF110', 'grupo' => 'TA', 'dias' => ['lunes', 'miercoles'], 'inicio' => '09:15', 'fin' => '11:15', 'aula' => 'Lab 40'],
            ['materia' => 'INF110', 'grupo' => 'TB', 'dias' => ['martes', 'jueves'], 'inicio' => '11:30', 'fin' => '13:30', 'aula' => 'Lab 41'],
            
            // Álgebra Lineal - MAT103
            ['materia' => 'MAT103', 'grupo' => 'FA', 'dias' => ['lunes', 'miercoles', 'viernes'], 'inicio' => '07:00', 'fin' => '08:30', 'aula' => 'Aula 14'],
            ['materia' => 'MAT103', 'grupo' => 'FB', 'dias' => ['martes', 'jueves'], 'inicio' => '14:00', 'fin' => '15:30', 'aula' => 'Aula 15'],
            
            // Física I - FIS100
            ['materia' => 'FIS100', 'grupo' => 'X', 'dias' => ['lunes', 'miercoles'], 'inicio' => '18:30', 'fin' => '20:30', 'aula' => 'Aula 16'],
            ['materia' => 'FIS100', 'grupo' => 'Y', 'dias' => ['martes', 'jueves'], 'inicio' => '18:30', 'fin' => '20:30', 'aula' => 'Aula 20'],
            
            // Inglés Técnico I - LIN100
            ['materia' => 'LIN100', 'grupo' => 'A', 'dias' => ['sabado'], 'inicio' => '09:00', 'fin' => '12:00', 'aula' => 'Aula 21'],
            
            // SEGUNDO SEMESTRE
            // Programación II - INF210
            ['materia' => 'INF210', 'grupo' => 'TA', 'dias' => ['lunes', 'miercoles'], 'inicio' => '11:30', 'fin' => '13:30', 'aula' => 'Lab 43'],
            ['materia' => 'INF210', 'grupo' => 'TB', 'dias' => ['martes', 'jueves'], 'inicio' => '07:00', 'fin' => '09:00', 'aula' => 'Lab 44'],
            
            // Cálculo II - MAT202
            ['materia' => 'MAT202', 'grupo' => 'A', 'dias' => ['lunes', 'miercoles', 'viernes'], 'inicio' => '09:15', 'fin' => '10:45', 'aula' => 'Aula 22'],
            ['materia' => 'MAT202', 'grupo' => 'B', 'dias' => ['martes', 'jueves'], 'inicio' => '16:15', 'fin' => '17:45', 'aula' => 'Aula 23'],
            
            // Ecuaciones Diferenciales - MAT207
            ['materia' => 'MAT207', 'grupo' => 'A', 'dias' => ['lunes', 'miercoles'], 'inicio' => '14:00', 'fin' => '15:30', 'aula' => 'Aula 24'],
            
            // Métodos Numéricos - MAT205
            ['materia' => 'MAT205', 'grupo' => 'A', 'dias' => ['martes', 'jueves'], 'inicio' => '14:00', 'fin' => '15:30', 'aula' => 'Aula 25'],
            
            // Estructura de Datos I - INF220
            ['materia' => 'INF220', 'grupo' => 'A', 'dias' => ['martes', 'jueves'], 'inicio' => '09:15', 'fin' => '11:15', 'aula' => 'Lab 45'],
            
            // Arquitectura de Computadoras - INF211
            ['materia' => 'INF211', 'grupo' => 'A', 'dias' => ['lunes', 'viernes'], 'inicio' => '16:15', 'fin' => '17:45', 'aula' => 'Aula 30'],
            
            // Programación Ensamblador - INF221
            ['materia' => 'INF221', 'grupo' => 'A', 'dias' => ['lunes', 'jueves'], 'inicio' => '18:30', 'fin' => '20:30', 'aula' => 'Lab 46'],
            
            // Física II - FIS102
            ['materia' => 'FIS102', 'grupo' => 'A', 'dias' => ['lunes', 'miercoles'], 'inicio' => '18:30', 'fin' => '20:30', 'aula' => 'Aula 31'],
            
            // Inglés Técnico II - LIN101
            ['materia' => 'LIN101', 'grupo' => 'A', 'dias' => ['sabado'], 'inicio' => '07:00', 'fin' => '09:00', 'aula' => 'Aula 32'],
            
            // Administración - ADM100
            ['materia' => 'ADM100', 'grupo' => 'A', 'dias' => ['miercoles', 'viernes'], 'inicio' => '11:30', 'fin' => '13:00', 'aula' => 'Aula 33'],

            // TERCER SEMESTRE
            // Probabilidad y Estadística I - MAT302
            ['materia' => 'MAT302', 'grupo' => 'A', 'dias' => ['lunes', 'miercoles', 'viernes'], 'inicio' => '07:00', 'fin' => '08:30', 'aula' => 'Aula 10'],
            ['materia' => 'MAT302', 'grupo' => 'B', 'dias' => ['martes', 'jueves'], 'inicio' => '14:00', 'fin' => '15:30', 'aula' => 'Aula 14'],
            
            // Programación Lógica y Funcional - INF318
            ['materia' => 'INF318', 'grupo' => 'A', 'dias' => ['lunes', 'miercoles'], 'inicio' => '09:15', 'fin' => '11:15', 'aula' => 'Lab 40'],
            
            // Estructura de Datos II - INF310
            ['materia' => 'INF310', 'grupo' => 'A', 'dias' => ['martes', 'jueves'], 'inicio' => '09:15', 'fin' => '11:15', 'aula' => 'Lab 41'],
            
            // Bases de Datos I - INF312
            ['materia' => 'INF312', 'grupo' => 'A', 'dias' => ['lunes', 'miercoles'], 'inicio' => '14:00', 'fin' => '16:00', 'aula' => 'Lab 43'],
            
            // Lenguajes Formales - INF319
            ['materia' => 'INF319', 'grupo' => 'A', 'dias' => ['martes', 'jueves'], 'inicio' => '11:30', 'fin' => '13:00', 'aula' => 'Aula 15'],
            
            // Contabilidad - ADM200
            ['materia' => 'ADM200', 'grupo' => 'A', 'dias' => ['miercoles', 'viernes'], 'inicio' => '07:00', 'fin' => '08:30', 'aula' => 'Aula 16'],
            
            // CUARTO SEMESTRE
            // Probabilidad y Estadística II - MAT329
            ['materia' => 'MAT329', 'grupo' => 'A', 'dias' => ['martes', 'jueves'], 'inicio' => '07:00', 'fin' => '08:30', 'aula' => 'Aula 22'],
            
            // Sistemas de Información I - INF342
            ['materia' => 'INF342', 'grupo' => 'A', 'dias' => ['lunes', 'miercoles'], 'inicio' => '09:15', 'fin' => '10:45', 'aula' => 'Aula 23'],
            ['materia' => 'INF342', 'grupo' => 'B', 'dias' => ['martes', 'jueves'], 'inicio' => '14:00', 'fin' => '15:30', 'aula' => 'Aula 24'],
            
            // Sistemas Operativos I - INF323
            ['materia' => 'INF323', 'grupo' => 'A', 'dias' => ['lunes', 'miercoles'], 'inicio' => '11:30', 'fin' => '13:30', 'aula' => 'Lab 41'],
            ['materia' => 'INF323', 'grupo' => 'B', 'dias' => ['martes', 'jueves'], 'inicio' => '11:30', 'fin' => '13:30', 'aula' => 'Lab 42'],
            
            // Bases de Datos II - INF322
            ['materia' => 'INF322', 'grupo' => 'A', 'dias' => ['lunes', 'miercoles'], 'inicio' => '14:00', 'fin' => '16:00', 'aula' => 'Lab 44'],
            
            // Compiladores - INF329
            ['materia' => 'INF329', 'grupo' => 'A', 'dias' => ['martes', 'jueves'], 'inicio' => '18:30', 'fin' => '20:30', 'aula' => 'Lab 45'],
            
            // Física III - FIS200
            ['materia' => 'FIS200', 'grupo' => 'A', 'dias' => ['lunes', 'miercoles'], 'inicio' => '18:30', 'fin' => '20:30', 'aula' => 'Aula 25'],
            
            // Sistemas de Información - INF412 (4to semestre)
            ['materia' => 'INF412', 'grupo' => 'A', 'dias' => ['viernes', 'sabado'], 'inicio' => '18:30', 'fin' => '20:30', 'aula' => 'Aula 26'],
            
            // QUINTO SEMESTRE
            // Investigación Operativa I - MAT419
            ['materia' => 'MAT419', 'grupo' => 'A', 'dias' => ['lunes', 'miercoles', 'viernes'], 'inicio' => '07:00', 'fin' => '08:30', 'aula' => 'Aula 30'],
            
            // Inteligencia Artificial - INF418
            ['materia' => 'INF418', 'grupo' => 'A', 'dias' => ['martes', 'jueves'], 'inicio' => '09:15', 'fin' => '11:15', 'aula' => 'Lab 46'],
            
            // Sistemas Operativos II - INF413
            ['materia' => 'INF413', 'grupo' => 'A', 'dias' => ['lunes', 'miercoles'], 'inicio' => '09:15', 'fin' => '11:15', 'aula' => 'Lab 40'],
            
            // Redes I - INF433
            ['materia' => 'INF433', 'grupo' => 'A', 'dias' => ['martes', 'jueves'], 'inicio' => '16:15', 'fin' => '18:15', 'aula' => 'Lab 43'],
            
            // Ingeniería de Software I - INF422
            ['materia' => 'INF422', 'grupo' => 'A', 'dias' => ['martes', 'jueves'], 'inicio' => '14:00', 'fin' => '15:30', 'aula' => 'Aula 32'],

            // SEXTO SEMESTRE
            // Investigación Operativa II - MAT429
            ['materia' => 'MAT429', 'grupo' => 'A', 'dias' => ['lunes', 'miercoles', 'viernes'], 'inicio' => '07:00', 'fin' => '08:30', 'aula' => 'Aula 33'],
            
            // Sistemas Expertos - INF428
            ['materia' => 'INF428', 'grupo' => 'A', 'dias' => ['martes', 'jueves'], 'inicio' => '09:15', 'fin' => '11:15', 'aula' => 'Lab 41'],
            
            // Sistemas de Información Geográfica - INF442
            ['materia' => 'INF442', 'grupo' => 'A', 'dias' => ['lunes', 'miercoles'], 'inicio' => '09:15', 'fin' => '11:15', 'aula' => 'Lab 42'],
            
            // Redes II - INF423
            ['materia' => 'INF423', 'grupo' => 'A', 'dias' => ['lunes', 'miercoles'], 'inicio' => '16:15', 'fin' => '18:15', 'aula' => 'Lab 44'],
            
            // Arquitectura de Software - INF552
            ['materia' => 'INF552', 'grupo' => 'A', 'dias' => ['martes', 'jueves'], 'inicio' => '14:00', 'fin' => '15:30', 'aula' => 'Aula 34'],
            
            // Tecnología Web - INF513
            ['materia' => 'INF513', 'grupo' => 'A', 'dias' => ['lunes', 'miercoles'], 'inicio' => '14:00', 'fin' => '16:00', 'aula' => 'Aula 35'],
            
            // SÉPTIMO SEMESTRE
            // Sistemas Distribuidos - INF528
            ['materia' => 'INF528', 'grupo' => 'A', 'dias' => ['lunes', 'miercoles'], 'inicio' => '07:00', 'fin' => '09:00', 'aula' => 'Lab 40'],
            
            // Minería de Datos - INF533
            ['materia' => 'INF533', 'grupo' => 'A', 'dias' => ['martes', 'jueves'], 'inicio' => '07:00', 'fin' => '09:00', 'aula' => 'Lab 43'],
            
            // Comercio Electrónico - INF539
            ['materia' => 'INF539', 'grupo' => 'A', 'dias' => ['lunes', 'miercoles'], 'inicio' => '09:15', 'fin' => '10:45', 'aula' => 'Aula 36'],
            
            // Seguridad Informática I - INF542
            ['materia' => 'INF542', 'grupo' => 'A', 'dias' => ['martes', 'jueves'], 'inicio' => '09:15', 'fin' => '11:15', 'aula' => 'Lab 45'],
            
            // Administración de Proyectos - ADM400
            ['materia' => 'ADM400', 'grupo' => 'A', 'dias' => ['lunes', 'miercoles'], 'inicio' => '11:30', 'fin' => '13:00', 'aula' => 'Aula 10'],
            
            // Ética Profesional - FIL401
            ['materia' => 'FIL401', 'grupo' => 'A', 'dias' => ['viernes'], 'inicio' => '18:30', 'fin' => '20:30', 'aula' => 'Aula 11'],
            
            // OCTAVO SEMESTRE
            // Taller de Base de Datos - INF628
            ['materia' => 'INF628', 'grupo' => 'A', 'dias' => ['lunes', 'miercoles'], 'inicio' => '09:15', 'fin' => '11:15', 'aula' => 'Lab 41'],
            
            // Auditoría Informática - INF633
            ['materia' => 'INF633', 'grupo' => 'A', 'dias' => ['martes', 'jueves'], 'inicio' => '09:15', 'fin' => '11:15', 'aula' => 'Aula 12'],
            
            // Simulación de Sistemas - INF639
            ['materia' => 'INF639', 'grupo' => 'A', 'dias' => ['lunes', 'miercoles'], 'inicio' => '14:00', 'fin' => '15:30', 'aula' => 'Aula 13'],
            
            // Seguridad Informática II - INF642
            ['materia' => 'INF642', 'grupo' => 'A', 'dias' => ['martes', 'jueves'], 'inicio' => '14:00', 'fin' => '16:00', 'aula' => 'Lab 46'],
            
            // Formulación y Evaluación de Proyectos - ADM500
            ['materia' => 'ADM500', 'grupo' => 'A', 'dias' => ['lunes', 'miercoles'], 'inicio' => '16:15', 'fin' => '17:45', 'aula' => 'Aula 14'],
            
            // Derecho Informático - DER501
            ['materia' => 'DER501', 'grupo' => 'A', 'dias' => ['viernes'], 'inicio' => '14:00', 'fin' => '16:00', 'aula' => 'Aula 15'],
            
            // NOVENO SEMESTRE
            // Taller de Grado I - TG601
            ['materia' => 'TG601', 'grupo' => 'A', 'dias' => ['lunes', 'miercoles'], 'inicio' => '07:00', 'fin' => '09:00', 'aula' => 'Lab 42'],
            
            // Desarrollo Web Avanzado - INF728
            ['materia' => 'INF728', 'grupo' => 'A', 'dias' => ['martes', 'jueves'], 'inicio' => '07:00', 'fin' => '09:00', 'aula' => 'Lab 44'],
            
            // Teletrabajo y Outsourcing - INF733
            ['materia' => 'INF733', 'grupo' => 'A', 'dias' => ['lunes', 'miercoles'], 'inicio' => '09:15', 'fin' => '10:45', 'aula' => 'Aula 16'],
            
            // Ingeniería de Requisitos - INF739
            ['materia' => 'INF739', 'grupo' => 'A', 'dias' => ['martes', 'jueves'], 'inicio' => '09:15', 'fin' => '11:15', 'aula' => 'Aula 20'],
            
            // Mantenimiento y Calidad de Sistemas - INF742
            ['materia' => 'INF742', 'grupo' => 'A', 'dias' => ['lunes', 'miercoles'], 'inicio' => '14:00', 'fin' => '15:30', 'aula' => 'Aula 21'],
            
            // DÉCIMO SEMESTRE
            // Taller de Grado II - TG701
            ['materia' => 'TG701', 'grupo' => 'A', 'dias' => ['lunes', 'miercoles'], 'inicio' => '09:15', 'fin' => '11:15', 'aula' => 'Lab 43'],
            
            // Prácticas Pre-Profesionales - PP701
            ['materia' => 'PP701', 'grupo' => 'A', 'dias' => ['martes', 'jueves'], 'inicio' => '09:15', 'fin' => '11:15', 'aula' => 'Aula 22'],
            
            // Cátedra de Emprendimiento - EMP701
            ['materia' => 'EMP701', 'grupo' => 'A', 'dias' => ['miercoles', 'viernes'], 'inicio' => '14:00', 'fin' => '15:30', 'aula' => 'Aula 23'],
        ];

        foreach ($horariosData as $index => $data) {
            $materia = $materias[$data['materia']];
            $aula = $aulas[$data['aula']];
            $profesor = $profesoresArray[$index % count($profesoresArray)];
            
            // Crear grupo
            $grupo = Grupo::create([
                'materia_id' => $materia->id,
                'identificador' => $data['grupo'],
                'capacidad_maxima' => 40,
                'estado' => 'activo'
            ]);
            
            // Crear carga académica
            $carga = CargaAcademica::create([
                'profesor_id' => $profesor->id,
                'grupo_id' => $grupo->id,
                'periodo' => $periodo,
                'periodo_academico' => $periodo,
                'estado' => 'asignado'
            ]);
            
            // Calcular duración en horas
            $inicio = \Carbon\Carbon::createFromFormat('H:i', $data['inicio']);
            $fin = \Carbon\Carbon::createFromFormat('H:i', $data['fin']);
            $duracion = $fin->diffInMinutes($inicio) / 60;
            
            // Crear horario con múltiples días
            Horario::create([
                'carga_academica_id' => $carga->id,
                'aula_id' => $aula->id,
                'dias_semana' => $data['dias'],
                'hora_inicio' => $data['inicio'],
                'hora_fin' => $data['fin'],
                'duracion_horas' => $duracion,
                'tipo_clase' => in_array($aula->tipo_aula, ['laboratorio']) ? 'practica' : 'teorica',
                'periodo_academico' => $periodo,
                'es_semestral' => true,
                'semanas_duracion' => 16,
                'tipo_asignacion' => 'manual',
                'estado' => 'activo'
            ]);
        }
        
        $this->command->info('✓ ' . count($horariosData) . ' horarios creados');
    }
}
