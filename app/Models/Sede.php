<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sede extends Model
{
    use HasFactory;

    protected $table = 'sedes';

    protected $fillable = [
        'estado_id',
        'nombre_sede',
        'direccion',
        'codigo_postal',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    /**
     * Estado al que pertenece esta sede.
     */
    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    /**
     * Equipos asignados a esta sede.
     */
    public function equipos()
    {
        return $this->hasMany(Equipo::class);
    }

    public function componentes()
    {
        return $this->hasMany(Componente::class);
    }

    public function bienes()
    {
        return $this->hasMany(BienNacional::class);
    }

    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class);
    }

    public function equiposSonido()
    {
        return $this->hasMany(EquipoSonido::class);
    }

    public function movimientos()
    {
        return $this->hasMany(RegistroEntradaSalida::class, 'sede_id');
    }

    public function usuarios()
    {
        return $this->hasMany(User::class);
    }
}