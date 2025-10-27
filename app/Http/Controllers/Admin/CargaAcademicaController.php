<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CargaAcademica;
use App\Models\Profesor;
use App\Models\Grupo;
use Illuminate\Http\Request;

class CargaAcademicaController extends Controller
{
    public function index()
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }
        
        $cargasAcademicas = CargaAcademica::with(['profesor', 'grupo.materia'])->orderBy('periodo', 'desc')->get();
        return view('admin.cargas-academicas.index', compact('cargasAcademicas'));
    }

    public function create()
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }
        
        $profesores = Profesor::where('estado', 'activo')->orderBy('nombre')->get();
        $grupos = Grupo::with('materia')->where('estado', 'activo')->orderBy('identificador')->get();
        
        return view('admin.cargas-academicas.create', compact('profesores', 'grupos'));
    }

    public function store(Request $request)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }

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

        CargaAcademica::create($request->all());

        return redirect()->route('admin.cargas-academicas.index')
            ->with('success', 'Carga académica registrada exitosamente.');
    }

    public function show(CargaAcademica $cargaAcademica)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }
        
        $cargaAcademica->load(['profesor', 'grupo.materia', 'horarios.aula']);
        return view('admin.cargas-academicas.show', compact('cargaAcademica'));
    }

    public function edit(CargaAcademica $cargaAcademica)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }
        
        $profesores = Profesor::where('estado', 'activo')->orderBy('nombre')->get();
        $grupos = Grupo::with('materia')->where('estado', 'activo')->orderBy('identificador')->get();
        
        return view('admin.cargas-academicas.edit', compact('cargaAcademica', 'profesores', 'grupos'));
    }

    public function update(Request $request, CargaAcademica $cargaAcademica)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }

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

        $cargaAcademica->update($request->all());

        return redirect()->route('admin.cargas-academicas.index')
            ->with('success', 'Carga académica actualizada exitosamente.');
    }

    public function destroy(CargaAcademica $cargaAcademica)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }

        $cargaAcademica->delete();

        return redirect()->route('admin.cargas-academicas.index')
            ->with('success', 'Carga académica eliminada exitosamente.');
    }
}
