<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profesor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DocenteController extends Controller
{
    public function __construct()
    {
        // No usar middleware aquí, se manejará en cada método
    }

    // CU-01: Mostrar lista de docentes
    public function index()
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }
        
        $docentes = Profesor::orderBy('codigo_docente')->get();
        return view('admin.docentes.index', compact('docentes'));
    }

    // CU-01: Mostrar formulario de registro
    public function create()
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }
        
        return view('admin.docentes.create');
    }

    // CU-01: Registrar nuevo docente
    public function store(Request $request)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }
        $request->validate([
            'codigo_docente' => 'required|string|max:20|unique:profesores,codigo_docente',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:profesores,email',
            'cedula' => 'required|string|max:20|unique:profesores,cedula',
            'telefono' => 'nullable|string|max:20',
            'especialidad' => 'required|string|max:255',
            'tipo_contrato' => 'required|in:tiempo_completo,medio_tiempo,catedra',
            'password' => 'required|string|min:6',
        ], [
            'codigo_docente.unique' => 'Error: El Código de Docente ya existe, ingrese uno diferente.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'cedula.unique' => 'La cédula ya está registrada.',
        ]);

        try {
            Profesor::create([
                'codigo_docente' => $request->codigo_docente,
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'email' => $request->email,
                'cedula' => $request->cedula,
                'telefono' => $request->telefono,
                'especialidad' => $request->especialidad,
                'tipo_contrato' => $request->tipo_contrato,
                'password' => $request->password,
                'estado' => 'activo'
            ]);

            return redirect()->route('admin.docentes.index')
                ->with('success', 'Registro Exitoso del Docente y cuenta de usuario creada.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al registrar el docente. Intente nuevamente.');
        }
    }

    // CU-02: Mostrar formulario de edición
    public function edit(Profesor $docente)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }
        
        return view('admin.docentes.edit', compact('docente'));
    }

    // CU-02: Actualizar datos del docente
    public function update(Request $request, Profesor $docente)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }
        $request->validate([
            'codigo_docente' => 'required|string|max:20|unique:profesores,codigo_docente,' . $docente->id,
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:profesores,email,' . $docente->id,
            'cedula' => 'required|string|max:20|unique:profesores,cedula,' . $docente->id,
            'telefono' => 'nullable|string|max:20',
            'especialidad' => 'required|string|max:255',
            'tipo_contrato' => 'required|in:tiempo_completo,medio_tiempo,catedra',
            'password' => 'nullable|string|min:6',
        ], [
            'codigo_docente.unique' => 'Error: Los datos modificados son inválidos o el código ya pertenece a otro docente.',
            'email.unique' => 'El correo electrónico ya está registrado por otro docente.',
            'cedula.unique' => 'La cédula ya está registrada por otro docente.',
        ]);

        try {
            $data = $request->except('password');
            
            // Solo actualizar contraseña si se proporciona
            if ($request->filled('password')) {
                $data['password'] = $request->password;
            }

            $docente->update($data);

            return redirect()->route('admin.docentes.index')
                ->with('success', 'Modificación Exitosa.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error: Los datos modificados son inválidos o el código ya pertenece a otro docente.');
        }
    }

    // CU-03: Desactivar docente (desactivación lógica)
    public function destroy(Profesor $docente)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }
        try {
            $docente->desactivar();
            
            return redirect()->route('admin.docentes.index')
                ->with('success', 'Docente desactivado y cuenta inhabilitada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al desactivar el docente. Intente nuevamente.');
        }
    }

    // Activar docente
    public function activate(Profesor $docente)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }
        try {
            $docente->activar();
            
            return redirect()->route('admin.docentes.index')
                ->with('success', 'Docente activado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al activar el docente. Intente nuevamente.');
        }
    }

    // Búsqueda de docentes
    public function search(Request $request)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }
        $query = $request->get('q');
        
        $docentes = Profesor::where('codigo_docente', 'LIKE', "%{$query}%")
            ->orWhere('nombre', 'LIKE', "%{$query}%")
            ->orWhere('apellido', 'LIKE', "%{$query}%")
            ->orderBy('codigo_docente')
            ->get();

        return view('admin.docentes.index', compact('docentes', 'query'));
    }
}