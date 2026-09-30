<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BitacoraService;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Nombre legible de cada módulo (guardado en columna `modulo` de la BD).
     */
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
        ][$moduloKey] ?? ucfirst(str_replace('_', ' ', $moduloKey));
    }

    /**
     * Nombre legible de cada submódulo (segunda parte del nombre del permiso).
     * Ej: "tecnologia.equipos.ver" → submódulo = "equipos"
     */
    protected function nombreSubmodulo($subKey)
    {
        return [
            // Tecnología
            'equipos' => 'Equipos',
            'componentes' => 'Componentes',
            'tipos-equipos' => 'Tipos de Equipos',
            'categorias-componentes' => 'Categorías de Componentes',
            'ordenes' => 'Órdenes de Servicio',

            // Bienes
            'bienes' => 'Bienes',
            'categorias' => 'Categorías',

            // Vehículos
            'vehiculos' => 'Vehículos',

            // Sonido
            'sonido' => 'Equipos de Sonido',

            // Entrada/Salida
            'entrada-salida' => 'Registros de Entrada/Salida',

            // Simple (sin submódulo real)
            'dashboard' => 'General',
            'valorizacion' => 'Valorización de Inventario',
            'bitacora' => 'Bitácora Global',
            'reportes' => 'Reportes',
            'estados' => 'Estados y Sedes',
            'usuarios' => 'Usuarios',
            'roles' => 'Roles',
        ][$subKey] ?? ucfirst(str_replace('-', ' ', $subKey));
    }

    /**
     * Agrupa los permisos por módulo y submódulo, usando el campo `modulo`
     * de la BD y la estructura del nombre (modulo.submodulo.accion).
     *
     * @return array
     */
    protected function agruparPermisos()
    {
        // Traemos TODOS los permisos ordenados por nombre
        $permisos = Permission::orderBy('name')->get();

        // Orden en el que queremos mostrar los módulos
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

        // Estructura a devolver: [moduloKey => ['nombre' => ..., 'submodulos' => [subKey => ['nombre' => ..., 'permisos' => Collection]]]]
        $estructura = [];

        foreach ($permisos as $permiso) {
            // Determinar el módulo (usando el campo `modulo` de la BD)
            $moduloKey = $permiso->modulo ?? $this->extraerModuloDelNombre($permiso->name);

            // Determinar el submódulo (segunda parte del nombre)
            $subKey = $this->extraerSubmoduloDelNombre($permiso->name, $moduloKey);

            // Inicializar el módulo si no existe
            if (!isset($estructura[$moduloKey])) {
                $estructura[$moduloKey] = [
                    'nombre' => $this->nombreModulo($moduloKey),
                    'submodulos' => [],
                ];
            }

            // Inicializar el submódulo si no existe
            if (!isset($estructura[$moduloKey]['submodulos'][$subKey])) {
                $estructura[$moduloKey]['submodulos'][$subKey] = [
                    'nombre' => $this->nombreSubmodulo($subKey),
                    'permisos' => collect(),
                ];
            }

            // Agregar el permiso
            $estructura[$moduloKey]['submodulos'][$subKey]['permisos']->push($permiso);
        }

        // Reordenar según $ordenModulos
        $estructuraOrdenada = [];
        foreach ($ordenModulos as $key) {
            if (isset($estructura[$key])) {
                $estructuraOrdenada[$key] = $estructura[$key];
            }
        }
        // Agregar cualquier módulo que no esté en el orden (por si acaso)
        foreach ($estructura as $key => $data) {
            if (!isset($estructuraOrdenada[$key])) {
                $estructuraOrdenada[$key] = $data;
            }
        }

        return $estructuraOrdenada;
    }

    /**
     * Extrae el módulo del nombre del permiso si no hay campo `modulo`.
     * Ej: "tecnologia.equipos.ver" → "tecnologia"
     */
    protected function extraerModuloDelNombre($nombrePermiso)
    {
        $partes = explode('.', $nombrePermiso);
        return $partes[0] ?? 'otros';
    }

    /**
     * Extrae el submódulo del nombre del permiso.
     * - 3 partes: "tecnologia.equipos.ver" → "equipos"
     * - 2 partes: "bitacora.ver" → "bitacora"
     */
    protected function extraerSubmoduloDelNombre($nombrePermiso, $moduloKey)
    {
        $partes = explode('.', $nombrePermiso);

        // Caso 3+ partes: modulo.submodulo.accion
        if (count($partes) >= 3) {
            return $partes[1];
        }

        // Caso 2 partes: modulo.accion → el submódulo es el propio módulo
        return $moduloKey;
    }

    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->paginate(15);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $estructura = $this->agruparPermisos();
        return view('admin.roles.create', compact('estructura'));
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

        BitacoraService::crear('roles', $role, 'Rol creado: ' . $role->name, $role->name);

        return redirect()->route('admin.roles.index')->with('success', 'Rol creado exitosamente.');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $estructura = $this->agruparPermisos();
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        return view('admin.roles.edit', compact('role', 'estructura', 'rolePermissions'));
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
        ]);

        $datosAnteriores = $role->toArray();

        $role->update(['name' => $validated['name']]);

        if (isset($validated['permissions'])) {
            $permisos = Permission::whereIn('id', $validated['permissions'])->get();
            $role->syncPermissions($permisos);
        } else {
            $role->syncPermissions([]);
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

        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')->with('error', 'No se puede eliminar el rol "' . $role->name . '" porque tiene usuarios asignados.');
        }

        BitacoraService::eliminar('roles', $role, 'Rol eliminado: ' . $role->name, $role->name);

        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Rol eliminado exitosamente.');
    }
}