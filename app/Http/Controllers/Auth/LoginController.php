<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\BitacoraService;

class LoginController extends Controller
{
    /**
     * Muestra el formulario de login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Procesa el inicio de sesión.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            BitacoraService::accion(
                'auth',
                'login',
                'Inicio de sesión: ' . Auth::user()->name . ' (' . Auth::user()->email . ')',
                Auth::user(),
                ['email' => Auth::user()->email],
                Auth::user()->email
            );

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * Cierra la sesión del usuario.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            BitacoraService::accion(
                'auth',
                'logout',
                'Cierre de sesión: ' . $user->name . ' (' . $user->email . ')',
                $user,
                ['email' => $user->email],
                $user->email
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}