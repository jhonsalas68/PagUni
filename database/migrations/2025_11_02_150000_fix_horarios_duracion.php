<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Actualizar horarios que tienen duracion_horas vacía o nula
        $horarios = DB::table('horarios')
            ->where(function($query) {
                $query->whereNull('duracion_horas')
                      ->orWhere('duracion_horas', 0);
            })
            ->get();

        foreach ($horarios as $horario) {
            try {
                if (!empty($horario->hora_inicio) && !empty($horario->hora_fin)) {
                    // Calcular duración desde las horas
                    $inicio = Carbon::createFromFormat('H:i', $horario->hora_inicio);
                    $fin = Carbon::createFromFormat('H:i', $horario->hora_fin);
                    $duracionHoras = $fin->diffInMinutes($inicio) / 60;
                    
                    DB::table('horarios')
                        ->where('id', $horario->id)
                        ->update(['duracion_horas' => $duracionHoras]);
                        
                    echo "Horario ID {$horario->id}: Calculada duración de {$duracionHoras} horas\n";
                }
            } catch (\Exception $e) {
                // Si no se puede calcular, asignar 2 horas por defecto
                DB::table('horarios')
                    ->where('id', $horario->id)
                    ->update(['duracion_horas' => 2.0]);
                    
                echo "Horario ID {$horario->id}: Asignada duración por defecto de 2 horas\n";
            }
        }

        // Asegurar que la columna no permita nulos
        Schema::table('horarios', function (Blueprint $table) {
            $table->decimal('duracion_horas', 4, 2)->default(2.0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('horarios', function (Blueprint $table) {
            $table->decimal('duracion_horas', 4, 2)->nullable()->change();
        });
    }
};