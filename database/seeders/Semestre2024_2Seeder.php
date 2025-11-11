<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Profesor;
use App\Models\Grupo;
use App\Models\CargaAcademica;
use App\Models\Horario;
use App\Models\AsistenciaDocente;
use Carbon\Carbon;

class Semestre2024_2Seeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('📅 Creando datos del semestre 2024-2...');
        
        $periodo = '2024-2';
        
        // Limpiar datos existentes del período 2024-2
        $this->command->info('🧹 Limpiando datos existentes del período 2024-2...');
        $cargasExistentes = CargaAcademica::where('periodo', $periodo)->get();
        foreach ($cargasExistentes as $carga) {
            // Eliminar asistencias de los horarios de esta carga
            $horarioIds = $carga->horarios->pluck('id');
            AsistenciaDocente::whereIn('horario_id', $horarioIds)->delete();
            // Eliminar horarios
            Horario::whereIn('id', $horarioIds)->delete();
        }
        // Eliminar cargas
        CargaAcademica::where('periodo', $periodo)->delete();
        
        // Obtener profesores y grupos existentes
        $profesores = Profesor::limit(5)->get();
        $grupos = Grupo::limit(10)->get();
        
        if ($profesores->isEmpty() || $grupos->isEmpty()) {
            $this->command->error('❌ No hay profesores o grupos. Ejecuta primero FICCTCompletaSeeder');
            return;
        }
        
        $cargasCreadas = 0;
        $horariosCreados = 0;
        $asistenciasCreadas = 0;
        
        // Crear cargas académicas para el período 2024-2
        foreach ($profesores as $index => $profesor) {
            // Cada profesor tiene 2-3 grupos
            $gruposProfesor = $grupos->slice($index * 2, 2);
            
            foreach ($gruposProfesor as $grupo) {
                // Crear carga académica
                $carga = CargaAcademica::create([
                    'profesor_id' => $profesor->id,
                    'grupo_id' => $grupo->id,
                    'periodo' => $periodo,
                    'periodo_academico' => $periodo,
                    'estado' => 'asignado'
                ]);
                $cargasCreadas++;
                
                // Crear 2 horarios por carga (Lunes-Miércoles y Martes-Jueves)
                $horariosData = [
                    [
                        'dias' => ['lunes', 'miercoles'],
                        'inicio' => '08:00',
                        'fin' => '10:00',
                        'duracion' => 4 // 2 horas x 2 días = 4 horas semanales
                    ],
                    [
                        'dias' => ['martes', 'jueves'],
                        'inicio' => '14:00',
                        'fin' => '16:00',
                        'duracion' => 4
                    ]
                ];
                
                foreach ($horariosData as $horarioData) {
                    $horario = Horario::create([
                        'carga_academica_id' => $carga->id,
                        'aula_id' => rand(1, 10), // Aulas aleatorias
                        'dias_semana' => $horarioData['dias'],
                        'hora_inicio' => $horarioData['inicio'],
                        'hora_fin' => $horarioData['fin'],
                        'duracion_horas' => $horarioData['duracion'],
                        'tipo_clase' => 'teorica',
                        'periodo_academico' => $periodo,
                        'estado' => 'activo'
                    ]);
                    $horariosCreados++;
                    
                    // Crear asistencias para las últimas 8 semanas (simulando un semestre)
                    $fechaInicio = Carbon::parse('2024-08-01'); // Inicio del semestre 2024-2
                    $fechaFin = Carbon::parse('2024-09-30'); // 2 meses de clases
                    
                    $fechaActual = $fechaInicio->copy();
                    
                    while ($fechaActual <= $fechaFin) {
                        // Verificar si es un día de clase
                        $diaSemana = strtolower($this->getDiaNombre($fechaActual->dayOfWeek));
                        
                        if (in_array($diaSemana, $horarioData['dias'])) {
                            // 85% de probabilidad de asistencia
                            $asiste = rand(1, 100) <= 85;
                            
                            if ($asiste) {
                                // Calcular hora de entrada (puede llegar hasta 15 min tarde)
                                $minutosRetraso = rand(0, 15);
                                $horaEntrada = Carbon::parse($fechaActual->format('Y-m-d') . ' ' . $horarioData['inicio'])
                                    ->addMinutes($minutosRetraso);
                                
                                // Calcular hora de salida (normalmente a tiempo)
                                $horaSalida = Carbon::parse($fechaActual->format('Y-m-d') . ' ' . $horarioData['fin']);
                                
                                // Calcular duración en minutos
                                $duracionMinutos = $horaEntrada->diffInMinutes($horaSalida);
                                
                                // Determinar estado
                                $estado = $minutosRetraso > 10 ? 'tardanza' : 'presente';
                                
                                AsistenciaDocente::create([
                                    'profesor_id' => $profesor->id,
                                    'horario_id' => $horario->id,
                                    'fecha' => $fechaActual->format('Y-m-d'),
                                    'hora_entrada' => $horaEntrada->format('H:i:s'),
                                    'hora_salida' => $horaSalida->format('H:i:s'),
                                    'estado' => $estado,
                                    'validado_en_horario' => true,
                                    'observaciones' => $estado === 'tardanza' ? 'Llegó ' . $minutosRetraso . ' minutos tarde' : null
                                ]);
                                $asistenciasCreadas++;
                            } else {
                                // 15% de faltas (algunas justificadas)
                                $justificada = rand(1, 100) <= 30; // 30% de las faltas están justificadas
                                
                                AsistenciaDocente::create([
                                    'profesor_id' => $profesor->id,
                                    'horario_id' => $horario->id,
                                    'fecha' => $fechaActual->format('Y-m-d'),
                                    'estado' => $justificada ? 'justificado' : 'ausente',
                                    'justificacion' => $justificada ? 'Motivo personal' : null,
                                    'tipo_justificacion' => $justificada ? 'personal' : null
                                ]);
                                $asistenciasCreadas++;
                            }
                        }
                        
                        $fechaActual->addDay();
                    }
                }
            }
        }
        
        $this->command->info("✅ Semestre 2024-2 creado:");
        $this->command->info("   - Cargas académicas: $cargasCreadas");
        $this->command->info("   - Horarios: $horariosCreados");
        $this->command->info("   - Asistencias: $asistenciasCreadas");
    }
    
    private function getDiaNombre($numeroDia)
    {
        $dias = [
            0 => 'domingo',
            1 => 'lunes',
            2 => 'martes',
            3 => 'miercoles',
            4 => 'jueves',
            5 => 'viernes',
            6 => 'sabado'
        ];
        
        return $dias[$numeroDia] ?? 'lunes';
    }
}
