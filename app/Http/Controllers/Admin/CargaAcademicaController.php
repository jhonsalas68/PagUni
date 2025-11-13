<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CargaAcademica;
use App\Models\Profesor;
use App\Models\Grupo;
use App\Models\Carrera;
use App\Models\Facultad;
use Illuminate\Http\Request;

class CargaAcademicaController extends Controller
{
    public function index(Request $request)
    {
        $query = CargaAcademica::with(['profesor', 'grupo.materia.carrera.facultad']);
        
        // Filtro por facultad
        if ($request->filled('facultad_id')) {
            $query->whereHas('grupo.materia.carrera.facultad', function($q) use ($request) {
                $q->where('id', $request->facultad_id);
            });
        }
        
        // Filtro por carrera
        if ($request->filled('carrera_id')) {
            $query->whereHas('grupo.materia.carrera', function($q) use ($request) {
                $q->where('id', $request->carrera_id);
            });
        }
        
        // Filtro por período
        if ($request->filled('periodo')) {
            $query->where('periodo', $request->periodo);
        }
        
        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        
        // Filtro por profesor
        if ($request->filled('profesor_id')) {
            $query->where('profesor_id', $request->profesor_id);
        }
        
        $cargasAcademicas = $query->orderBy('periodo', 'desc')->paginate(10);
        
        // Obtener datos para los filtros
        $facultades = Facultad::orderBy('nombre')->get();
        $carreras = Carrera::orderBy('nombre')->get();
        $profesores = Profesor::where('estado', 'activo')->orderBy('nombre')->get();
        $periodos = CargaAcademica::select('periodo')->distinct()->orderBy('periodo', 'desc')->pluck('periodo');
        
        return view('admin.cargas-academicas.index', compact('cargasAcademicas', 'facultades', 'carreras', 'profesores', 'periodos'));
    }

    public function create(Request $request)
    {
        $profesores = Profesor::where('estado', 'activo')->orderBy('nombre')->get();
        $grupos = Grupo::with('materia')->where('estado', 'activo')->orderBy('identificador')->get();
        $materiaId = $request->input('materia_id');
        
        // Si se especifica una materia, filtrar grupos de esa materia
        if ($materiaId) {
            $grupos = $grupos->where('materia_id', $materiaId);
        }
        
        return view('admin.cargas-academicas.create', compact('profesores', 'grupos', 'materiaId'));
    }

    public function store(Request $request)
    {
$request->validate([
            'profesor_id' => 'required|exists:profesores,id',
            'grupo_id' => 'required|exists:grupos,id',
            'periodo' => 'required|string|max:20',
            'estado' => 'required|in:asignado,pendiente,completado,cancelado',
        ]);

        // Verificar que no exista ya una carga académica para el mismo profesor, grupo y período
        $existeCarga = CargaAcademica::where('profesor_id', $request->profesor_id)
                                   ->where('grupo_id', $request->grupo_id)
                                   ->where('periodo', $request->periodo)
                                   ->exists();

        if ($existeCarga) {
            return back()->withErrors(['error' => 'Ya existe una carga académica para este profesor, grupo y período.'])
                        ->withInput();
        }

        CargaAcademica::create([
            'profesor_id' => $request->profesor_id,
            'grupo_id' => $request->grupo_id,
            'periodo' => $request->periodo,
            'periodo_academico' => $request->periodo, // Agregar este campo también
            'estado' => $request->estado,
        ]);

        return redirect()->route('admin.cargas-academicas.index')
            ->with('success', 'Carga académica registrada exitosamente.');
    }

    public function show(CargaAcademica $cargaAcademica)
    {
$cargaAcademica->load(['profesor', 'grupo.materia', 'horarios.aula']);
        return view('admin.cargas-academicas.show', compact('cargaAcademica'));
    }

    public function edit(CargaAcademica $cargaAcademica)
    {
$profesores = Profesor::where('estado', 'activo')->orderBy('nombre')->get();
        $grupos = Grupo::with('materia')->where('estado', 'activo')->orderBy('identificador')->get();
        
        return view('admin.cargas-academicas.edit', compact('cargaAcademica', 'profesores', 'grupos'));
    }

    public function update(Request $request, CargaAcademica $cargaAcademica)
    {
$request->validate([
            'profesor_id' => 'required|exists:profesores,id',
            'grupo_id' => 'required|exists:grupos,id',
            'periodo' => 'required|string|max:20',
            'estado' => 'required|in:asignado,pendiente,completado,cancelado',
        ]);

        // Verificar que no exista ya una carga académica para el mismo profesor, grupo y período (excluyendo la actual)
        $existeCarga = CargaAcademica::where('profesor_id', $request->profesor_id)
                                   ->where('grupo_id', $request->grupo_id)
                                   ->where('periodo', $request->periodo)
                                   ->where('id', '!=', $cargaAcademica->id)
                                   ->exists();

        if ($existeCarga) {
            return back()->withErrors(['error' => 'Ya existe una carga académica para este profesor, grupo y período.'])
                        ->withInput();
        }

        $cargaAcademica->update([
            'profesor_id' => $request->profesor_id,
            'grupo_id' => $request->grupo_id,
            'periodo' => $request->periodo,
            'periodo_academico' => $request->periodo, // Agregar este campo también
            'estado' => $request->estado,
        ]);

        return redirect()->route('admin.cargas-academicas.index')
            ->with('success', 'Carga académica actualizada exitosamente.');
    }

    public function destroy(CargaAcademica $cargaAcademica)
    {
$cargaAcademica->delete();

        return redirect()->route('admin.cargas-academicas.index')
            ->with('success', 'Carga académica eliminada exitosamente.');
    }
}
