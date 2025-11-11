<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primero, crear una tabla temporal para guardar los datos existentes
        DB::statement('CREATE TABLE horarios_temp AS SELECT * FROM horarios');
        
        // Eliminar restricciones de clave foránea que dependen de horarios
        Schema::table('asistencia_docente', function (Blueprint $table) {
            $table->dropForeign(['horario_id']);
        });
        
        // Eliminar la tabla original
        Schema::dropIfExists('horarios');
        
        // Recrear la tabla con la nueva estructura
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carga_academica_id')->constrained('carga_academica')->onDelete('cascade');
            $table->foreignId('aula_id')->constrained('aulas')->onDelete('cascade');
            
            // CAMBIO PRINCIPAL: dias_semana ahora es JSON para múltiples días
            $table->json('dias_semana')->comment('Array de días: ["lunes", "martes", "miercoles"]');
            
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->decimal('duracion_horas', 4, 2)->default(2.0);
            $table->string('tipo_clase')->nullable()->comment('teorica, practica, laboratorio');
            $table->string('periodo_academico', 10)->comment('Ej: 2024-1, 2024-2, 2025-1');
            $table->boolean('es_semestral')->default(true);
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->integer('semanas_duracion')->default(16);
            $table->enum('tipo_asignacion', ['manual', 'automatica'])->default('manual');
            $table->enum('estado', ['activo', 'cancelado'])->default('activo');
            $table->text('observaciones')->nullable();
            $table->boolean('usar_configuracion_por_dia')->default(false);
            $table->json('configuracion_dias')->nullable()->comment('Configuración específica por día');
            $table->timestamps();
            
            // Índices para mejorar rendimiento
            $table->index('carga_academica_id');
            $table->index('aula_id');
            $table->index('periodo_academico');
            $table->index('estado');
        });
        
        // Migrar datos existentes: convertir dia_semana único a array
        $horariosAntiguos = DB::table('horarios_temp')->get();
        
        foreach ($horariosAntiguos as $horario) {
            // Convertir el día único a un array JSON
            $diasArray = [$horario->dia_semana];
            
            DB::table('horarios')->insert([
                'id' => $horario->id,
                'carga_academica_id' => $horario->carga_academica_id,
                'aula_id' => $horario->aula_id,
                'dias_semana' => json_encode($diasArray), // Convertir a JSON
                'hora_inicio' => $horario->hora_inicio,
                'hora_fin' => $horario->hora_fin,
                'duracion_horas' => $horario->duracion_horas ?? 2.0,
                'tipo_clase' => $horario->tipo_clase ?? null,
                'periodo_academico' => $horario->periodo ?? $horario->periodo_academico ?? '2025-1',
                'es_semestral' => $horario->es_semestral ?? true,
                'fecha_inicio' => $horario->fecha_inicio ?? null,
                'fecha_fin' => $horario->fecha_fin ?? null,
                'semanas_duracion' => $horario->semanas_duracion ?? 16,
                'tipo_asignacion' => $horario->tipo_asignacion ?? 'manual',
                'estado' => $horario->estado ?? 'activo',
                'observaciones' => $horario->observaciones ?? null,
                'usar_configuracion_por_dia' => $horario->usar_configuracion_por_dia ?? false,
                'configuracion_dias' => $horario->configuracion_dias ?? null,
                'created_at' => $horario->created_at ?? now(),
                'updated_at' => $horario->updated_at ?? now(),
            ]);
        }
        
        // Eliminar tabla temporal
        Schema::dropIfExists('horarios_temp');
        
        // Resetear el autoincrement
        $maxId = DB::table('horarios')->max('id');
        if ($maxId) {
            DB::statement("ALTER SEQUENCE horarios_id_seq RESTART WITH " . ($maxId + 1));
        }
        
        // Restaurar la restricción de clave foránea en asistencia_docente
        Schema::table('asistencia_docente', function (Blueprint $table) {
            $table->foreign('horario_id')->references('id')->on('horarios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Crear tabla temporal
        DB::statement('CREATE TABLE horarios_temp AS SELECT * FROM horarios');
        
        // Eliminar tabla actual
        Schema::dropIfExists('horarios');
        
        // Recrear con estructura antigua
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carga_academica_id')->constrained('carga_academica')->onDelete('cascade');
            $table->foreignId('aula_id')->constrained('aulas')->onDelete('cascade');
            $table->enum('dia_semana', ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado']);
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->decimal('duracion_horas', 4, 2)->default(2.0);
            $table->string('tipo_clase')->nullable();
            $table->string('periodo_academico', 10);
            $table->boolean('es_semestral')->default(true);
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->integer('semanas_duracion')->default(16);
            $table->enum('tipo_asignacion', ['manual', 'automatica'])->default('manual');
            $table->enum('estado', ['activo', 'cancelado'])->default('activo');
            $table->text('observaciones')->nullable();
            $table->boolean('usar_configuracion_por_dia')->default(false);
            $table->json('configuracion_dias')->nullable();
            $table->timestamps();
        });
        
        // Migrar datos de vuelta: tomar el primer día del array
        $horariosNuevos = DB::table('horarios_temp')->get();
        
        foreach ($horariosNuevos as $horario) {
            $diasArray = json_decode($horario->dias_semana, true);
            $primerDia = $diasArray[0] ?? 'lunes';
            
            DB::table('horarios')->insert([
                'id' => $horario->id,
                'carga_academica_id' => $horario->carga_academica_id,
                'aula_id' => $horario->aula_id,
                'dia_semana' => $primerDia,
                'hora_inicio' => $horario->hora_inicio,
                'hora_fin' => $horario->hora_fin,
                'duracion_horas' => $horario->duracion_horas,
                'tipo_clase' => $horario->tipo_clase,
                'periodo_academico' => $horario->periodo_academico,
                'es_semestral' => $horario->es_semestral,
                'fecha_inicio' => $horario->fecha_inicio,
                'fecha_fin' => $horario->fecha_fin,
                'semanas_duracion' => $horario->semanas_duracion,
                'tipo_asignacion' => $horario->tipo_asignacion,
                'estado' => $horario->estado,
                'observaciones' => $horario->observaciones,
                'usar_configuracion_por_dia' => $horario->usar_configuracion_por_dia,
                'configuracion_dias' => $horario->configuracion_dias,
                'created_at' => $horario->created_at,
                'updated_at' => $horario->updated_at,
            ]);
        }
        
        Schema::dropIfExists('horarios_temp');
    }
};
