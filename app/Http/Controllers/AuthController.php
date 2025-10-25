<?php

namespace App\Http\Controllers;

use App\Models\Administrador;
use App\Models\Profesor;
use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string',
            'password' => 'required|string',
        ]);

        $codigo = $request->codigo;
        $password = $request->password;

        // Detectar tipo de usuario por el formato del código
        $user = null;
        $userType = null;

        // Administrador: ADM###
        if (preg_match('/^ADM\d+$/', $codigo)) {
            $user = Administrador::where('codigo_admin', $codigo)->first();
            $userType = 'administrador';
        }
        // Profesor: PROF###
        elseif (preg_match('/^PROF\d+$/', $codigo)) {
            $user = Profesor::where('codigo_docente', $codigo)->first();
            $userType = 'profesor';
        }
        // Estudiante: formato [CARRERA][AÑO][NUMERO] como ISC2024001, MATE2024001
        else {
            $user = Estudiante::where('codigo_estudiante', $codigo)->first();
            $userType = 'estudiante';
        }

        // Verificar si el usuario existe y la contraseña es correcta
        if ($user && Hash::check($password, $user->password)) {
            // Guardar información del usuario en la sesión
            Session::put('user_id', $user->id);
            Session::put('user_type', $userType);
            Session::put('user_codigo', $codigo);
            Session::put('user_name', $user->nombre_completo);

            // Redirigir según el tipo de usuario
            switch ($userType) {
                case 'administrador':
                    return redirect()->route('admin.dashboard');
                case 'profesor':
                    return redirect()->route('profesor.dashboard');
                case 'estudiante':
                    return redirect()->route('estudiante.dashboard');
            }
        }

        return back()->withErrors([
            'codigo' => 'Las credenciales proporcionadas no son correctas.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Session::flush();
        return redirect()->route('login')->with('success', 'Has cerrado sesión correctamente.');
    }

    // Middleware helper para verificar autenticación
    public static function getAuthenticatedUser()
    {
        if (!Session::has('user_id')) {
            return null;
        }

        $userType = Session::get('user_type');
        $userId = Session::get('user_id');

        switch ($userType) {
            case 'administrador':
                return Administrador::find($userId);
            case 'profesor':
                return Profesor::find($userId);
            case 'estudiante':
                return Estudiante::find($userId);
        }

        return null;
    }

    public static function getUserType()
    {
        return Session::get('user_type');
    }
}