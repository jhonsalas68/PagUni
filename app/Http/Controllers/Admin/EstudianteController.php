<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use App\Models\Carrera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EstudianteController extends Controller
{
    public function index()
    {
$estudiantes = Estudiante::with('carrera.facultad')->orderBy('codigo_estudiante')->get();
        return view('admin.estudiantes.index', compact('estudiantes'));
    }

    public function create()
    {
$carreras = Carrera::with('facultad')->orderBy('nombre')->get();
        return view('admin.estudiantes.create', compact('carreras'));
    }

    public function store(Request $request)
    {
$request->validate([
            'codigo_estudiante' => 'required|string|max:15|unique:estudiantes,codigo_estudiante',
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'required|email|unique:estudiantes,email',
            'telefono' => 'nullable|string|max:20',
            'cedula' => 'required|string|max:20|unique:estudiantes,cedula',
            'direccion' => 'nullable|string',
            'fecha_nacimiento' => 'required|date',
            'carrera_id' => 'required|exists:carreras,id',
            'semestre_actual' => 'required|integer|min:1',
            'password' => 'required|string|min:6',
            'estado' => 'required|in:activo,inactivo,graduado,retirado',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($request->password);

        Estudiante::create($data);

        return redirect()->route('admin.estudiantes.index')
            ->with('success', 'Estudiante registrado exitosamente.');
    }

    public function show(Estudiante $estudiante)
    {
$estudiante->load('carrera.facultad');
        return view('admin.estudiantes.show', compact('estudiante'));
    }

    public function edit(Estudiante $estudiante)
    {
$carreras = Carrera::with('facultad')->orderBy('nombre')->get();
        return view('admin.estudiantes.edit', compact('estudiante', 'carreras'));
    }

    public function update(Request $request, Estudiante $estudiante)
    {
$request->validate([
            'codigo_estudiante' => 'required|string|max:15|unique:estudiantes,codigo_estudiante,' . $estudiante->id,
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'required|email|unique:estudiantes,email,' . $estudiante->id,
            'telefono' => 'nullable|string|max:20',
            'cedula' => 'required|string|max:20|unique:estudiantes,cedula,' . $estudiante->id,
            'direccion' => 'nullable|string',
            'fecha_nacimiento' => 'required|date',
            'carrera_id' => 'required|exists:carreras,id',
            'semestre_actual' => 'required|integer|min:1',
            'password' => 'nullable|string|min:6',
            'estado' => 'required|in:activo,inactivo,graduado,retirado',
        ]);

        $data = $request->except('password');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $estudiante->update($data);

        return redirect()->route('admin.estudiantes.index')
            ->with('success', 'Estudiante actualizado exitosamente.');
    }

    public function destroy(Estudiante $estudiante)
    {
$estudiante->delete();

        return redirect()->route('admin.estudiantes.index')
            ->with('success', 'Estudiante eliminado exitosamente.');
    }
}