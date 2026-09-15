<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Módulos con sus nombres amigables para la vista.
     */
    protected function obtenerModulos()
    {
        return [
            'dashboard' => 'Dashboard',
            'tecnologia' => 'Tecnología',
            'bienes' => 'Bienes Nacionales',
            'vehiculos' => 'Vehículos',
            'sonido' => 'Equipos de Sonido',
            'entrada_salida' => 'Entrada/Salida',
            'bitacora' => 'Bitácora',
            'reportes' => 'Reportes',
            'usuarios' => 'Usuarios',
            'roles' => 'Roles',
        ];
    }

    /**
     * Acciones estándar para los módulos.
     */
    protected function obtenerAcciones()
    {
        return ['ver', 'crear', 'editar', 'eliminar', 'importar', 'exportar'];
    }

    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->paginate(15);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $modulos = $this->obtenerModulos();
        $acciones = $this->obtenerAcciones();

        // Agrupar permisos por módulo
        $permisosPorModulo = [];
        foreach ($modulos as $key => $nombre) {
            $permisosPorModulo[$key] = Permission::where('modulo', $key)->orderBy('name')->get();
        }

        return view('admin.roles.create', compact('modulos', 'acciones', 'permisosPorModulo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permisos,id',
        ]);

        $role = Role::create(['name' => $validated['name'], 'guard_name' => 'web']);

        if (!empty($validated['permissions'])) {
            $permisos = Permission::whereIn('id', $validated['permissions'])->get();
            $role->syncPermissions($permisos);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Rol creado exitosamente.');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $modulos = $this->obtenerModulos();
        $acciones = $this->obtenerAcciones();

        // Agrupar permisos por módulo
        $permisosPorModulo = [];
        foreach ($modulos as $key => $nombre) {
            $permisosPorModulo[$key] = Permission::where('modulo', $key)->orderBy('name')->get();
        }

        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'modulos', 'acciones', 'permisosPorModulo', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permisos,id',
        ]);

        $role->update(['name' => $validated['name']]);

        if (isset($validated['permissions'])) {
            $permisos = Permission::whereIn('id', $validated['permissions'])->get();
            $role->syncPermissions($permisos);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Rol actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Rol eliminado exitosamente.');
    }
}