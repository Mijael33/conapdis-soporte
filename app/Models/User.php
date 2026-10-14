<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'estado_id',
        'sede_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS DE ROLES
    |--------------------------------------------------------------------------
    */

    /**
     * Determina si el usuario es "Administrador Total".
     *
     * Considera admin si cumple CUALQUIERA de estas condiciones:
     *  1. Tiene algún rol cuyo nombre esté en config('institucional.roles_admin').
     *  2. Tiene TODOS los permisos del sistema asignados (fallback).
     *
     * El criterio por nombre en config es el principal y más estable:
     * no se rompe si mañana se agrega un permiso nuevo al sistema.
     */
    public function esAdmin(): bool
    {
        // Condición 1: rol admin nativo (por lista configurable)
        $rolesAdmin = config('institucional.roles_admin', ['Administrador']);

        if (!empty($rolesAdmin) && $this->hasAnyRole($rolesAdmin)) {
            return true;
        }

        // Condición 2 (fallback): tiene todos los permisos actuales
        $totalPermisos = \Spatie\Permission\Models\Permission::count();

        if ($totalPermisos === 0) {
            return false;
        }

        return $this->getAllPermissions()->count() >= $totalPermisos;
    }

    /**
     * Determina si el usuario puede ver información a nivel nacional
     * (usar el filtro global de estado/sede sin restricciones).
     *
     * Condiciones (cualquiera es suficiente):
     *  - Es Administrador Total (por lista config o por todos los permisos).
     *  - Tiene el rol 'Auditor'.
     *  - Tiene el permiso especial 'acceso.nacional'.
     */
    public function puedeVerNacional(): bool
    {
        if ($this->esAdmin()) {
            return true;
        }

        if ($this->hasRole('Auditor')) {
            return true;
        }

        return $this->hasPermissionTo('acceso.nacional');
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */
    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function ordenesServicio()
    {
        return $this->hasMany(OrdenServicio::class, 'tecnico_id');
    }

    public function bitacoras()
    {
        return $this->hasMany(BitacoraEquipo::class, 'usuario_id');
    }
}