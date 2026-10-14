<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BitacoraService;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    protected function nombreModulo($moduloKey)
    {
        return [
            'dashboard' => 'Dashboard',
            'tecnologia' => 'Tecnología',
            'bienes' => 'Bienes Nacionales',
            'vehiculos' => 'Vehículos',
            'sonido' => 'Equipos de Sonido',
            'entrada_salida' => 'Entrada/Salida',
            'valorizacion' => 'Valorización',
            'bitacora' => 'Bitácora',
            'reportes' => 'Reportes',
            'estados' => 'Catálogos Base',
            'usuarios' => 'Usuarios',
            'roles' => 'Roles y Permisos',
            'acceso_nacional' => 'Acceso Nacional',
        ][$moduloKey] ?? ucfirst(str_replace('_', ' ', $moduloKey));
    }

    protected function nombreSubmodulo($subKey)
    {
        return [
            'equipos' => 'Equipos',
            'componentes' => 'Componentes',
            'tipos-equipos' => 'Tipos de Equipos',
            'categorias-componentes' => 'Categorías de Componentes',
            'ordenes' => 'Órdenes de Servicio',
            'bienes' => 'Bienes',
            'categorias' => 'Categorías',
            'vehiculos' => 'Vehículos',
            'sonido' => 'Equipos de Sonido',
            'entrada-salida' => 'Registros de Entrada/Salida',
            'dashboard' => 'General',
            'valorizacion' => 'Valorización de Inventario',
            'bitacora' => 'Bitácora Global',
            'reportes' => 'Reportes',
            'estados' => 'Estados y Sedes',
            'usuarios' => 'Usuarios',
            'roles' => 'Roles',
            'acceso_nacional' => 'Acceso Nacional',
        ][$subKey] ?? ucfirst(str_replace('-', ' ', $subKey));
    }

    /**
     * Agrupa los permisos por módulo y submódulo, EXCLUYENDO los permisos
     * especiales (como acceso.nacional) que se manejan por separado.
     */
    protected function agruparPermisos()
    {
        $permisos = Permission::where('name', '!=', 'acceso.nacional')
            ->orderBy('name')
            ->get();

        $ordenModulos = [
            'dashboard',
            'tecnologia',
            'bienes',
            'vehiculos',
            'sonido',
            'entrada_salida',
            'valorizacion',
            'bitacora',
            'reportes',
            'estados',
            'usuarios',
            'roles',
        ];

        $estructura = [];

        foreach ($permisos as $permiso) {
            $moduloKey = $permiso->modulo ?? $this->extraerModuloDelNombre($permiso->name);
            $subKey = $this->extraerSubmoduloDelNombre($permiso->name, $moduloKey);

            if (!isset($estructura[$moduloKey])) {
                $estructura[$moduloKey] = [
                    'nombre' => $this->nombreModulo($moduloKey),
                    'submodulos' => [],
                ];
            }

            if (!isset($estructura[$moduloKey]['submodulos'][$subKey])) {
                $estructura[$moduloKey]['submodulos'][$subKey] = [
                    'nombre' => $this->nombreSubmodulo($subKey),
                    'permisos' => collect(),
                ];
            }

            $estructura[$moduloKey]['submodulos'][$subKey]['permisos']->push($permiso);
        }

        $estructuraOrdenada = [];
        foreach ($ordenModulos as $key) {
            if (isset($estructura[$key])) {
                $estructuraOrdenada[$key] = $estructura[$key];
            }
        }
        foreach ($estructura as $key => $data) {
            if (!isset($estructuraOrdenada[$key])) {
                $estructuraOrdenada[$key] = $data;
            }
        }

        return $estructuraOrdenada;
    }

    protected function extraerModuloDelNombre($nombrePermiso)
    {
        $partes = explode('.', $nombrePermiso);
        return $partes[0] ?? 'otros';
    }

    protected function extraerSubmoduloDelNombre($nombrePermiso, $moduloKey)
    {
        $partes = explode('.', $nombrePermiso);
        if (count($partes) >= 3) {
            return $partes[1];
        }
        return $moduloKey;
    }

    /**
     * Determina si un rol debe considerarse "Administrador Total".
     * Criterio:
     *  - Su nombre está en config('institucional.roles_admin'), O
     *  - Tiene TODOS los permisos actuales del sistema.
     */
    protected function rolEsAdmin($role): bool
    {
        $rolesAdmin = config('institucional.roles_admin', ['Administrador']);

        if (!empty($rolesAdmin) && in_array($role->name, $rolesAdmin, true)) {
            return true;
        }

        $totalPermisos = Permission::count();
        if ($totalPermisos === 0) {
            return false;
        }

        return $role->permissions->count() >= $totalPermisos;
    }

    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->paginate(15);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $estructura = $this->agruparPermisos();
        $permisoNacional = Permission::where('name', 'acceso.nacional')->first();
        return view('admin.roles.create', compact('estructura', 'permisoNacional'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permisos,id',
            'es_admin' => 'nullable|in:0,1',
            'acceso_nacional' => 'nullable|in:0,1',
        ]);

        $role = Role::create(['name' => $validated['name'], 'guard_name' => 'web']);

        $permisoNacional = Permission::where('name', 'acceso.nacional')->first();

        // Determinar si es admin:
        //  1) Por check "es_admin" del formulario, O
        //  2) Porque el nombre está en la lista config de roles_admin
        $rolesAdminConfig = config('institucional.roles_admin', ['Administrador']);
        $esAdminPorNombre = in_array($validated['name'], $rolesAdminConfig, true);
        $esAdminForm = $request->input('es_admin') == '1';

        if ($esAdminForm || $esAdminPorNombre) {
            // Admin: TODOS los permisos + acceso.nacional garantizado
            $role->syncPermissions(Permission::all());
        } else {
            $permisosIds = $validated['permissions'] ?? [];

            // Agregar permiso acceso.nacional si está marcado
            if ($request->input('acceso_nacional') == '1' && $permisoNacional) {
                $permisosIds[] = $permisoNacional->id;
            }

            if (!empty($permisosIds)) {
                $permisos = Permission::whereIn('id', array_unique($permisosIds))->get();
                $role->syncPermissions($permisos);
            }
        }

        BitacoraService::crear('roles', $role, 'Rol creado: ' . $role->name, $role->name);

        return redirect()->route('admin.roles.index')->with('success', 'Rol creado exitosamente.');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $estructura = $this->agruparPermisos();
        $permisoNacional = Permission::where('name', 'acceso.nacional')->first();

        $rolePermissions = $role->permissions->pluck('id')->toArray();
        $tieneAccesoNacional = $permisoNacional && in_array($permisoNacional->id, $rolePermissions);

        // ¿Este rol es admin por config (nombre en lista) o por tener todos los permisos?
        $esAdmin = $this->rolEsAdmin($role);

        return view('admin.roles.edit', compact(
            'role',
            'estructura',
            'permisoNacional',
            'rolePermissions',
            'tieneAccesoNacional',
            'esAdmin'
        ));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        if ($role->name === 'Administrador') {
            return redirect()->route('admin.roles.index')->with('error', 'El rol Administrador no puede ser modificado.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permisos,id',
            'es_admin' => 'nullable|in:0,1',
            'acceso_nacional' => 'nullable|in:0,1',
        ]);

        $datosAnteriores = $role->toArray();

        $role->update(['name' => $validated['name']]);

        $permisoNacional = Permission::where('name', 'acceso.nacional')->first();

        $rolesAdminConfig = config('institucional.roles_admin', ['Administrador']);
        $esAdminPorNombre = in_array($validated['name'], $rolesAdminConfig, true);
        $esAdminForm = $request->input('es_admin') == '1';

        if ($esAdminForm || $esAdminPorNombre) {
            $role->syncPermissions(Permission::all());
        } else {
            $permisosIds = $validated['permissions'] ?? [];

            if ($request->input('acceso_nacional') == '1' && $permisoNacional) {
                $permisosIds[] = $permisoNacional->id;
            }

            $permisos = Permission::whereIn('id', array_unique($permisosIds))->get();
            $role->syncPermissions($permisos);
        }

        BitacoraService::editar('roles', $role, $datosAnteriores, 'Rol actualizado: ' . $role->name, $role->name);

        return redirect()->route('admin.roles.index')->with('success', 'Rol actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        if ($role->name === 'Administrador') {
            return redirect()->route('admin.roles.index')->with('error', 'El rol Administrador no puede ser eliminado.');
        }

        // Proteger roles admin (por nombre config o por tener todos los permisos)
        if ($this->rolEsAdmin($role)) {
            return redirect()->route('admin.roles.index')->with('error', 'No se puede eliminar un rol con acceso total.');
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')->with('error', 'No se puede eliminar el rol "' . $role->name . '" porque tiene usuarios asignados.');
        }

        BitacoraService::eliminar('roles', $role, 'Rol eliminado: ' . $role->name, $role->name);

        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Rol eliminado exitosamente.');
    }
}