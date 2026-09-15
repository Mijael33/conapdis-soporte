<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | PERMISOS ORGANIZADOS POR MÓDULO
        |--------------------------------------------------------------------------
        */
        $modulos = [
            'dashboard' => [
                'dashboard.ver',
            ],
            'tecnologia' => [
                'tecnologia.equipos.ver',
                'tecnologia.equipos.crear',
                'tecnologia.equipos.editar',
                'tecnologia.equipos.eliminar',
                'tecnologia.equipos.importar',
                'tecnologia.equipos.exportar',
                'tecnologia.componentes.ver',
                'tecnologia.componentes.crear',
                'tecnologia.componentes.editar',
                'tecnologia.componentes.eliminar',
                'tecnologia.componentes.importar',
                'tecnologia.componentes.exportar',
                'tecnologia.tipos-equipos.ver',
                'tecnologia.tipos-equipos.crear',
                'tecnologia.tipos-equipos.editar',
                'tecnologia.tipos-equipos.eliminar',
                'tecnologia.categorias-componentes.ver',
                'tecnologia.categorias-componentes.crear',
                'tecnologia.categorias-componentes.editar',
                'tecnologia.categorias-componentes.eliminar',
                'tecnologia.ordenes.ver',
                'tecnologia.ordenes.crear',
                'tecnologia.ordenes.editar',
                'tecnologia.ordenes.eliminar',
            ],
            'bienes' => [
                'bienes.ver',
                'bienes.crear',
                'bienes.editar',
                'bienes.eliminar',
                'bienes.importar',
                'bienes.exportar',
                'bienes.categorias.ver',
                'bienes.categorias.crear',
                'bienes.categorias.editar',
                'bienes.categorias.eliminar',
            ],
            'vehiculos' => [
                'vehiculos.ver',
                'vehiculos.crear',
                'vehiculos.editar',
                'vehiculos.eliminar',
                'vehiculos.importar',
                'vehiculos.exportar',
                'vehiculos.categorias.ver',
                'vehiculos.categorias.crear',
                'vehiculos.categorias.editar',
                'vehiculos.categorias.eliminar',
            ],
            'sonido' => [
                'sonido.ver',
                'sonido.crear',
                'sonido.editar',
                'sonido.eliminar',
                'sonido.importar',
                'sonido.exportar',
                'sonido.categorias.ver',
                'sonido.categorias.crear',
                'sonido.categorias.editar',
                'sonido.categorias.eliminar',
            ],
            'entrada_salida' => [
                'entrada-salida.ver',
                'entrada-salida.crear',
                'entrada-salida.editar',
                'entrada-salida.eliminar',
                'entrada-salida.exportar',
            ],
            'bitacora' => [
                'bitacora.ver',
            ],
            'reportes' => [
                'reportes.generar',
                'reportes.exportar',
            ],
            'usuarios' => [
                'usuarios.ver',
                'usuarios.crear',
                'usuarios.editar',
                'usuarios.eliminar',
            ],
            'roles' => [
                'roles.ver',
                'roles.crear',
                'roles.editar',
                'roles.eliminar',
            ],
        ];

        foreach ($modulos as $modulo => $permisos) {
            foreach ($permisos as $permiso) {
                Permission::firstOrCreate(
                    ['name' => $permiso, 'guard_name' => 'web'],
                    ['modulo' => $modulo]
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | ROL ADMINISTRADOR (ÚNICO PRECARGADO)
        |--------------------------------------------------------------------------
        | El admin tiene TODOS los permisos. Los demás roles se crean por CRUD.
        */
        $admin = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());
    }
}