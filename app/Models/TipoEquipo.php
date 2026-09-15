<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TipoEquipo extends Model
{
    use HasFactory;

    protected $table = 'tipos_equipos';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    /**
     * Equipos de este tipo.
     */
    public function equipos()
    {
        return $this->hasMany(Equipo::class, 'tipo_equipo_id');
    }
}