<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AsistenciaDocente;
use App\Models\Horario;
use App\Models\Profesor;
use Carbon\Carbon;

class AsistenciasDocenteSeeder extends Seeder
{
    public function run()
    {
        echo "🔄 Generando asistencias de docentes...\n";

        // Obtener todos los horarios con sus cargas académicas
        $horarios = Horario::with('cargaAcademica')->get();

        if ($horarios->isEmpty()) {
            echo "❌ No hay horarios disponibles. Ejecuta primero los seeders de horarios.\n";
            return;
        }

        // Generar asistencias para los últimos 2 meses
        $fechaInicio = Carbon::now()->subMonths(2)->startOfMonth();
        $fechaFin = Carbon::now();

        $totalAsistencias = 0;

        foreach ($horarios as $horario) {
            if (!$horario->cargaAcademica) {
                continue;
            }

            $profesorId = $horario->cargaAcademica->profesor_id;
            
            // Obtener los días de la semana de este horario
            $diasSemana = $horario->dias_semana ?? [];
            
            if (empty($diasSemana)) {
                continue;
            }

            // Mapeo de días
            $mapaDias = [
                'lunes' => 1,
                'martes' => 2,
                'miercoles' => 3,
                'jueves' => 4,
                'viernes' => 5,
                'sabado' => 6,
                'domingo' => 7
            ];

            // Convertir días a números
            $numeroDias = [];
            foreach ($diasSemana as $dia) {
                if (isset($mapaDias[$dia])) {
                    $numeroDias[] = $mapaDias[$dia];
                }
            }

            // Generar asistencias para cada día que corresponda
            $fecha = $fechaInicio->copy();
            while ($fecha <= $fechaFin) {
                $diaSemana = $fecha->dayOfWeek;
                $diaSemana = $diaSemana === 0 ? 7 : $diaSemana; // Domingo = 7

                // Si este día corresponde a una clase
                if (in_array($diaSemana, $numeroDias)) {
                    // 85% de probabilidad de asistencia
                    $asiste = rand(1, 100) <= 85;

                    if ($asiste) {
                        // Calcular hora de entrada (puede ser puntual o con tardanza)
                        $horaInicio = Carbon::parse($horario->hora_inicio);
                        $esTardanza = rand(1, 100) <= 15; // 15% de tardanzas

                        if ($esTardanza) {
                            // Tardanza de 5 a 20 minutos
                            $minutosRetraso = rand(5, 20);
                            $horaEntrada = $horaInicio->copy()->addMinutes($minutosRetraso);
                            $estado = 'tardanza';
                            $validadoEnHorario = false;
                        } else {
                            // Puntual (puede llegar hasta 10 minutos antes)
                            $minutosAntes = rand(-10, 5);
                            $horaEntrada = $horaInicio->copy()->addMinutes($minutosAntes);
                            $estado = 'presente';
                            $validadoEnHorario = true;
                        }

                        // Verificar si ya existe
                        $existe = AsistenciaDocente::where([
                            'profesor_id' => $profesorId,
                            'horario_id' => $horario->id,
                            'fecha' => $fecha->format('Y-m-d'),
                            'numero_sesion' => 1
                        ])->exists();

                        if (!$existe) {
                            // Crear asistencia
                            AsistenciaDocente::create([
                                'profesor_id' => $profesorId,
                                'horario_id' => $horario->id,
                                'fecha' => $fecha->format('Y-m-d'),
                                'hora_entrada' => $horaEntrada->format('H:i:s'),
                                'estado' => $estado,
                                'validado_en_horario' => $validadoEnHorario,
                                'modalidad' => rand(1, 100) <= 80 ? 'presencial' : 'virtual', // 80% presencial
                                'numero_sesion' => 1,
                                'created_at' => $fecha->copy()->setTime($horaEntrada->hour, $horaEntrada->minute),
                                'updated_at' => $fecha->copy()->setTime($horaEntrada->hour, $horaEntrada->minute),
                            ]);

                            $totalAsistencias++;
                        }
                    }
                }

                $fecha->addDay();
            }
        }

        echo "✅ Se generaron {$totalAsistencias} asistencias de docentes\n";
        echo "   Período: {$fechaInicio->format('d/m/Y')} - {$fechaFin->format('d/m/Y')}\n";
    }
}
