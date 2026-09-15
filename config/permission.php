<?php

use Spatie\Permission\DefaultTeamResolver;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return [

    'models' => [
        'permission' => Permission::class,
        'role' => Role::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | NOMBRES DE TABLAS PERSONALIZADOS PARA CONAPDIS
    |--------------------------------------------------------------------------
    | Cambiamos los nombres por defecto de Spatie para que coincidan
    | con nuestras migraciones en español.
    */
    'table_names' => [
        'roles' => 'roles',
        'permissions' => 'permisos',
        'model_has_permissions' => 'user_has_permissions',
        'model_has_roles' => 'user_has_roles',
        'role_has_permissions' => 'role_has_permissions',
    ],

    'column_names' => [
        'role_pivot_key' => null,
        'permission_pivot_key' => null,
        'model_morph_key' => 'model_id',
        'team_foreign_key' => 'team_id',
    ],

    'register_permission_check_method' => true,
    'register_octane_reset_listener' => false,
    'events_enabled' => false,
    'teams' => false,
    'team_resolver' => DefaultTeamResolver::class,
    'use_passport_client_credentials' => false,
    'display_permission_in_exception' => false,
    'display_role_in_exception' => false,
    'enable_wildcard_permission' => false,

    'cache' => [
        'expiration_time' => \DateInterval::createFromDateString('24 hours'),
        'key' => 'spatie.permission.cache',
        'store' => 'default',
    ],
];