<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Estado;
use App\Models\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsuarioController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasRole('Administrador')) {
            abort(403, 'No autorizado');
        }
        $usuarios = User::with('roles')->orderBy('name')->paginate(15);
        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        if (!auth()->user()->hasRole('Administrador')) {
            abort(403, 'No autorizado');
        }
        $roles = Role::orderBy('name')->get();
        $estados = Estado::orderBy('nombre')->get();
        $sedes = Sede::orderBy('nombre_sede')->get();
        return view('admin.usuarios.create', compact('roles', 'estados', 'sedes'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('Administrador')) {
            abort(403, 'No autorizado');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'estado_id' => 'nullable|exists:estados,id',
            'sede_id' => 'nullable|exists:sedes,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'estado_id' => $validated['estado_id'] ?? null,
            'sede_id' => $validated['sede_id'] ?? null,
        ]);

        if (!empty($validated['roles'])) {
            $roles = Role::whereIn('id', $validated['roles'])->get();
            $user->syncRoles($roles);
        }

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario creado.');
    }

    public function show($id)
    {
        $usuario = User::findOrFail($id);
        if (!auth()->user()->hasRole('Administrador') && auth()->id() != $usuario->id) {
            abort(403);
        }
        return view('admin.usuarios.show', compact('usuario'));
    }

    public function edit($id)
    {
        $usuario = User::findOrFail($id);
        if (!auth()->user()->hasRole('Administrador') && auth()->id() != $usuario->id) {
            abort(403);
        }

        $esAdmin = auth()->user()->hasRole('Administrador');
        $roles = $esAdmin ? Role::orderBy('name')->get() : collect([]);
        $userRoles = $usuario->roles->pluck('id')->toArray();
        $estados = $esAdmin ? Estado::orderBy('nombre')->get() : collect([]);
        $sedes = $esAdmin ? Sede::orderBy('nombre_sede')->get() : collect([]);

        return view('admin.usuarios.edit', compact('usuario', 'roles', 'userRoles', 'estados', 'sedes', 'esAdmin'));
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);
        $esAdmin = auth()->user()->hasRole('Administrador');

        if (!$esAdmin && auth()->id() != $usuario->id) {
            abort(403);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $usuario->id,
            'password' => 'nullable|string|min:8|confirmed',
        ];

        if ($esAdmin) {
            $rules['roles'] = 'nullable|array';
            $rules['roles.*'] = 'exists:roles,id';
            $rules['estado_id'] = 'nullable|exists:estados,id';
            $rules['sede_id'] = 'nullable|exists:sedes,id';
        }

        $validated = $request->validate($rules);

        $data = ['name' => $validated['name'], 'email' => $validated['email']];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        if ($esAdmin) {
            $data['estado_id'] = $validated['estado_id'] ?? $usuario->estado_id;
            $data['sede_id'] = $validated['sede_id'] ?? $usuario->sede_id;
        }

        $usuario->update($data);

        if ($esAdmin && isset($validated['roles'])) {
            $roles = Role::whereIn('id', $validated['roles'])->get();
            $usuario->syncRoles($roles);
        }

        $redirect = $esAdmin ? 'admin.usuarios.index' : 'admin.dashboard';
        return redirect()->route($redirect)->with('success', 'Perfil actualizado.');
    }

    public function destroy($id)
    {
        if (!auth()->user()->hasRole('Administrador')) {
            abort(403);
        }
        $usuario = User::findOrFail($id);
        $usuario->delete();
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado.');
    }
}