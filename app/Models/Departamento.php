<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Departamento extends Model
{
    use HasFactory;

    protected $table = 'departamentos';

    protected $fillable = [
        'sede_id',
        'nombre_departamento',
        'piso',
        'extension_telefonica',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    /**
     * Sede a la que pertenece este departamento.
     */
    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    /**
     * Equipos asignados a este departamento.
     */
    public function equipos()
    {
        return $this->hasMany(Equipo::class);
    }
}