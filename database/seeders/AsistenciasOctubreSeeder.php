<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AsistenciaDocente;
use App\Models\Horario;
use App\Models\CargaAcademica;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AsistenciasOctubreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar asistencias existentes
        DB::table('asistencia_docente')->truncate();

        $this->command->info('Generando asistencias desde octubre 2024...');

        // Obtener todos los horarios activos del periodo 2024-2
        $horarios = Horario::with(['cargaAcademica.profesor', 'cargaAcademica.grupo.materia'])
            ->where('periodo_academico', '2024-2')
            ->get();

        if ($horarios->isEmpty()) {
            $this->command->warn('No hay horarios para el periodo 2024-2');
            return;
        }

        // Fecha de inicio: 1 de octubre 2024
        $fechaInicio = Carbon::create(2024, 10, 1);
        $fechaFin = Carbon::now(); // Hasta hoy

        $totalAsistencias = 0;

        foreach ($horarios as $horario) {
            $diasSemana = $horario->dias_semana ?? [];
            
            if (empty($diasSemana)) {
                continue;
            }

            // Mapeo de días en español a números de Carbon
            $mapaDias = [
                'lunes' => Carbon::MONDAY,
                'martes' => Carbon::TUESDAY,
                'miércoles' => Carbon::WEDNESDAY,
                'miercoles' => Carbon::WEDNESDAY,
                'jueves' => Carbon::THURSDAY,
                'viernes' => Carbon::FRIDAY,
                'sábado' => Carbon::SATURDAY,
                'sabado' => Carbon::SATURDAY,
                'domingo' => Carbon::SUNDAY,
            ];

            // Determinar patrón de asistencia para este docente/materia
            $profesor = $horario->cargaAcademica->profesor;
            $materia = $horario->cargaAcademica->grupo->materia;
            
            // Crear diferentes patrones de asistencia
            $patronAsistencia = $this->determinarPatronAsistencia($profesor->id, $materia->id);

            $fechaActual = $fechaInicio->copy();

            while ($fechaActual->lte($fechaFin)) {
                // Verificar si es un día de clase
                $diaActual = strtolower($fechaActual->locale('es')->dayName);
                
                if (in_array($diaActual, $diasSemana)) {
                    // Generar asistencia según el patrón
                    $asistencia = $this->generarAsistencia(
                        $horario,
                        $fechaActual->copy(),
                        $patronAsistencia
                    );

                    if ($asistencia) {
                        $totalAsistencias++;
                    }
                }

                $fechaActual->addDay();
            }
        }

        $this->command->info("✓ Se generaron {$totalAsistencias} registros de asistencia");
    }

    /**
     * Determinar patrón de asistencia basado en profesor y materia
     */
    private function determinarPatronAsistencia($profesorId, $materiaId): array
    {
        // Usar el ID para crear patrones consistentes pero variados
        $seed = $profesorId + $materiaId;
        
        // Diferentes perfiles de docentes
        $perfiles = [
            'excelente' => ['presente' => 95, 'tardanza' => 3, 'falta' => 2],
            'bueno' => ['presente' => 85, 'tardanza' => 8, 'falta' => 7],
            'regular' => ['presente' => 75, 'tardanza' => 10, 'falta' => 15],
            'irregular' => ['presente' => 65, 'tardanza' => 15, 'falta' => 20],
        ];

        $perfilKeys = array_keys($perfiles);
        $perfilSeleccionado = $perfilKeys[$seed % count($perfilKeys)];

        return $perfiles[$perfilSeleccionado];
    }

    /**
     * Generar un registro de asistencia
     */
    private function generarAsistencia(Horario $horario, Carbon $fecha, array $patron): ?AsistenciaDocente
    {
        // Determinar estado según el patrón
        $random = rand(1, 100);
        
        if ($random <= $patron['presente']) {
            $estado = 'presente';
        } elseif ($random <= $patron['presente'] + $patron['tardanza']) {
            $estado = 'tardanza';
        } else {
            $estado = 'ausente';
        }

        // Parsear horas del horario
        $horaInicio = Carbon::parse($horario->hora_inicio);
        $horaFin = Carbon::parse($horario->hora_fin);

        // Crear fecha y hora de entrada
        $horaEntrada = $fecha->copy()
            ->setHour($horaInicio->hour)
            ->setMinute($horaInicio->minute);

        // Crear fecha y hora de salida
        $horaSalida = $fecha->copy()
            ->setHour($horaFin->hour)
            ->setMinute($horaFin->minute);

        // Ajustar según el estado
        if ($estado === 'tardanza') {
            // Llegar 5-20 minutos tarde
            $minutosRetraso = rand(5, 20);
            $horaEntrada->addMinutes($minutosRetraso);
        } elseif ($estado === 'ausente') {
            // No registrar entrada/salida para ausencias
            $horaEntrada = null;
            $horaSalida = null;
        }

        // Crear el registro
        return AsistenciaDocente::create([
            'profesor_id' => $horario->cargaAcademica->profesor_id,
            'horario_id' => $horario->id,
            'fecha' => $fecha->format('Y-m-d'),
            'hora_entrada' => $horaEntrada?->format('H:i:s'),
            'hora_salida' => $horaSalida?->format('H:i:s'),
            'estado' => $estado,
            'modalidad' => rand(1, 10) > 8 ? 'virtual' : 'presencial', // 20% virtual
            'observaciones' => $estado === 'ausente' ? 'Ausencia sin justificar' : null,
            'created_at' => $fecha->copy()->setTime(rand(6, 8), rand(0, 59)),
            'updated_at' => $fecha->copy()->setTime(rand(6, 8), rand(0, 59)),
        ]);
    }
}
