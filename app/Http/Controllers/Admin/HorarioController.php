<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Horario;
use App\Models\CargaAcademica;
use App\Models\Aula;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    public function index()
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }
        
        $horarios = Horario::with(['cargaAcademica.profesor', 'cargaAcademica.grupo.materia', 'aula'])
                          ->orderBy('dia_semana')
                          ->orderBy('hora_inicio')
                          ->get();
        
        return view('admin.horarios.index', compact('horarios'));
    }

    public function create()
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }
        
        $cargasAcademicas = CargaAcademica::with(['profesor', 'grupo.materia'])->get();
        $aulas = Aula::where('estado', 'disponible')->orderBy('codigo_aula')->get();
        
        // Obtener horarios existentes para mostrar en el formulario
        $horariosExistentes = Horario::with(['cargaAcademica.profesor', 'cargaAcademica.grupo.materia', 'aula'])
                                   ->orderBy('periodo_academico', 'desc')
                                   ->orderBy('dia_semana')
                                   ->orderBy('hora_inicio')
                                   ->get();
        
        return view('admin.horarios.create', compact('cargasAcademicas', 'aulas', 'horariosExistentes'));
    }

    public function store(Request $request)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }

        $request->validate([
            'carga_academica_id' => 'required|exists:carga_academica,id',
            'aula_id' => 'required|exists:aulas,id',
            'dias_semana' => 'required|array|min:1',
            'dias_semana.*' => 'integer|min:1|max:7',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'duracion_horas' => 'required|numeric|min:0.1',
            'tipo_clase' => 'required|in:teorica,practica,laboratorio',
            'periodo_academico' => 'required|string|max:20',
            'es_semestral' => 'boolean',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'semanas_duracion' => 'nullable|integer|min:1|max:20',
        ]);

        $horariosCreados = 0;
        $conflictos = [];

        // Crear horario para cada día seleccionado
        foreach ($request->dias_semana as $dia) {
            // Usar el método mejorado de validación de conflictos
            $validacion = Horario::validarConflictos(
                $request->carga_academica_id,
                $request->aula_id,
                $dia,
                $request->hora_inicio,
                $request->hora_fin,
                $request->periodo_academico
            );

            if (!$validacion['disponible']) {
                $diasNombres = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                $tipoConflicto = '';
                if ($validacion['conflicto_profesor']) {
                    $tipoConflicto .= 'Profesor ocupado';
                }
                if ($validacion['conflicto_aula']) {
                    $tipoConflicto .= ($tipoConflicto ? ' y ' : '') . 'Aula ocupada';
                }
                $conflictos[] = $diasNombres[$dia] . ' (' . $tipoConflicto . ')';
            } else {
                // Crear el horario para este día
                Horario::create([
                    'carga_academica_id' => $request->carga_academica_id,
                    'aula_id' => $request->aula_id,
                    'dia_semana' => $dia,
                    'hora_inicio' => $request->hora_inicio,
                    'hora_fin' => $request->hora_fin,
                    'duracion_horas' => $request->duracion_horas,
                    'tipo_clase' => $request->tipo_clase,
                    'periodo_academico' => $request->periodo_academico,
                    'es_semestral' => $request->has('es_semestral'),
                    'fecha_inicio' => $request->fecha_inicio,
                    'fecha_fin' => $request->fecha_fin,
                    'semanas_duracion' => $request->semanas_duracion ?? 16,
                ]);
                $horariosCreados++;
            }
        }

        // Preparar mensaje de respuesta
        $mensaje = '';
        if ($horariosCreados > 0) {
            $mensaje = "Se crearon {$horariosCreados} horario(s) exitosamente.";
        }
        
        if (!empty($conflictos)) {
            $diasConflicto = implode(', ', $conflictos);
            if ($horariosCreados > 0) {
                $mensaje .= " Conflictos encontrados en: {$diasConflicto}.";
            } else {
                return back()->withErrors(['error' => "Conflictos de horario encontrados en: {$diasConflicto}"])
                            ->withInput();
            }
        }

        return redirect()->route('admin.horarios.index')
            ->with('success', $mensaje);
    }

    public function show(Horario $horario)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }
        
        $horario->load(['cargaAcademica.profesor', 'cargaAcademica.grupo.materia', 'aula']);
        return view('admin.horarios.show', compact('horario'));
    }

    public function edit(Horario $horario)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }
        
        $cargasAcademicas = CargaAcademica::with(['profesor', 'grupo.materia'])->get();
        $aulas = Aula::where('estado', 'disponible')->orderBy('codigo_aula')->get();
        
        // Obtener horarios existentes excluyendo el actual
        $horariosExistentes = Horario::with(['cargaAcademica.profesor', 'cargaAcademica.grupo.materia', 'aula'])
                                   ->where('id', '!=', $horario->id)
                                   ->orderBy('periodo_academico', 'desc')
                                   ->orderBy('dia_semana')
                                   ->orderBy('hora_inicio')
                                   ->get();
        
        return view('admin.horarios.edit', compact('horario', 'cargasAcademicas', 'aulas', 'horariosExistentes'));
    }

    public function update(Request $request, Horario $horario)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }

        $request->validate([
            'carga_academica_id' => 'required|exists:carga_academica,id',
            'aula_id' => 'required|exists:aulas,id',
            'dia_semana' => 'required|integer|min:1|max:7',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'duracion_horas' => 'required|numeric|min:0.1',
            'tipo_clase' => 'required|in:teorica,practica,laboratorio',
        ]);

        // Verificar conflictos de horario (excluyendo el horario actual)
        $conflicto = Horario::where('aula_id', $request->aula_id)
                           ->where('dia_semana', $request->dia_semana)
                           ->where('id', '!=', $horario->id)
                           ->where(function($query) use ($request) {
                               $query->whereBetween('hora_inicio', [$request->hora_inicio, $request->hora_fin])
                                     ->orWhereBetween('hora_fin', [$request->hora_inicio, $request->hora_fin])
                                     ->orWhere(function($q) use ($request) {
                                         $q->where('hora_inicio', '<=', $request->hora_inicio)
                                           ->where('hora_fin', '>=', $request->hora_fin);
                                     });
                           })
                           ->exists();

        if ($conflicto) {
            return back()->withErrors(['error' => 'Ya existe un horario en esa aula para ese día y hora.'])
                        ->withInput();
        }

        $horario->update([
            'carga_academica_id' => $request->carga_academica_id,
            'aula_id' => $request->aula_id,
            'dia_semana' => $request->dia_semana,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'duracion_horas' => $request->duracion_horas,
            'tipo_clase' => $request->tipo_clase,
        ]);

        return redirect()->route('admin.horarios.index')
            ->with('success', 'Horario actualizado exitosamente.');
    }

    public function destroy(Horario $horario)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }

        $horario->delete();

        return redirect()->route('admin.horarios.index')
            ->with('success', 'Horario eliminado exitosamente.');
    }
}