<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Equipo extends Model
{
    use HasFactory;

    protected $table = 'equipos';

    protected $fillable = [
        'codigo_inventario_institucional',
        'serial_chasis',
        'tipo_equipo_id',
        'departamento_id',
        'marca',
        'modelo',
        'estatus_general',
        'usuario_asignado_nombre',
        'usuario_asignado_cedula',
        'usuario_asignado_cargo',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */
    public function tipoEquipo()
    {
        return $this->belongsTo(TipoEquipo::class, 'tipo_equipo_id');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function componentes()
    {
        return $this->belongsToMany(Componente::class, 'equipo_componente')
                    ->withPivot('fecha_instalacion', 'fecha_desinstalacion', 'activo')
                    ->wherePivot('activo', true)
                    ->withTimestamps();
    }

    public function historialComponentes()
    {
        return $this->belongsToMany(Componente::class, 'equipo_componente')
                    ->withPivot('fecha_instalacion', 'fecha_desinstalacion', 'activo')
                    ->withTimestamps();
    }

    public function sistemasOperativos()
    {
        return $this->hasMany(EquipoSistemaOperativo::class);
    }

    public function ordenesServicio()
    {
        return $this->hasMany(OrdenServicio::class, 'equipo_id');
    }

    public function bitacoras()
    {
        return $this->hasMany(BitacoraEquipo::class, 'equipo_id');
    }
}