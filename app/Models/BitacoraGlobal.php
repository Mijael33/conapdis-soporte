<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BitacoraGlobal extends Model
{
    use HasFactory;

    protected $table = 'bitacora_global';

    public $timestamps = false;

    protected $fillable = [
        'modulo',
        'accion',
        'modelo_tipo',
        'modelo_id',
        'modelo_codigo',
        'usuario_id',
        'usuario_nombre_snapshot',
        'ip_address',
        'user_agent',
        'descripcion',
        'datos_anteriores',
        'datos_nuevos',
        'datos_extra',
        'fecha_registro',
        'zona_horaria',
    ];

    protected function casts(): array
    {
        return [
            'datos_anteriores' => 'array',
            'datos_nuevos' => 'array',
            'datos_extra' => 'array',
            'fecha_registro' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeDelModulo($query, $modulo)
    {
        return $query->where('modulo', $modulo);
    }

    public function scopeDeAccion($query, $accion)
    {
        return $query->where('accion', $accion);
    }

    public function scopeDelUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Badge de color según la acción
     */
    public function getColorAccionAttribute()
    {
        return match ($this->accion) {
            'crear', 'creado' => 'success',
            'editar', 'actualizado' => 'warning',
            'eliminar', 'eliminado' => 'danger',
            'cambiar_estatus' => 'info',
            'instalar', 'instalado' => 'primary',
            'remover', 'removido' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Badge de color según el módulo
     */
    public function getColorModuloAttribute()
    {
        return match ($this->modulo) {
            'equipos' => '#2563eb',
            'componentes' => '#059669',
            'bienes' => '#db2777',
            'vehiculos' => '#7c3aed',
            'sonido' => '#0891b2',
            'entrada-salida' => '#f59e0b',
            'usuarios' => '#4f46e5',
            'roles' => '#9333ea',
            default => '#64748b',
        };
    }
}