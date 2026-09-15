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
     * Departamentos que pertenecen a esta sede.
     */
    public function departamentos()
    {
        return $this->hasMany(Departamento::class);
    }
}