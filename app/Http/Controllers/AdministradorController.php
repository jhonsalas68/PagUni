<?php

namespace App\Http\Controllers;

use App\Models\Administrador;
use Illuminate\Http\Request;

class AdministradorController extends Controller
{
    public function index()
    {
        $administradores = Administrador::all();
        return response()->json($administradores);
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo_admin' => 'required|string|max:20|unique:administradores',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:administradores',
            'cedula' => 'required|string|max:20|unique:administradores',
            'password' => 'required|string|min:6',
            'telefono' => 'nullable|string|max:20',
            'nivel_acceso' => 'required|in:super_admin,admin',
        ]);

        $administrador = Administrador::create($request->all());
        return response()->json($administrador, 201);
    }

    public function show(Administrador $administrador)
    {
        return response()->json($administrador);
    }

    public function update(Request $request, Administrador $administrador)
    {
        $request->validate([
            'codigo_admin' => 'required|string|max:20|unique:administradores,codigo_admin,' . $administrador->id,
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:administradores,email,' . $administrador->id,
            'cedula' => 'required|string|max:20|unique:administradores,cedula,' . $administrador->id,
            'telefono' => 'nullable|string|max:20',
            'nivel_acceso' => 'required|in:super_admin,admin',
        ]);

        // Solo actualizar password si se proporciona
        $data = $request->except('password');
        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $administrador->update($data);
        return response()->json($administrador);
    }

    public function destroy(Administrador $administrador)
    {
        $administrador->delete();
        return response()->json(null, 204);
    }
}