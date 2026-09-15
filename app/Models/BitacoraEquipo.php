<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BitacoraEquipo extends Model
{
    use HasFactory;

    protected $table = 'bitacora_equipos';

    /**
     * La bitácora es de solo lectura.
     */
    public $timestamps = false;

    protected $fillable = [
        'equipo_id',
        'usuario_id',
        'accion',
        'descripcion_detallada',
        'datos_anteriores',
        'datos_nuevos',
        'fecha_registro',
    ];

    /**
     * Casts para atributos JSONB.
     */
    protected function casts(): array
    {
        return [
            'datos_anteriores' => 'array',
            'datos_nuevos' => 'array',
            'fecha_registro' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    /**
     * Equipo sobre el que se registró la acción.
     */
    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    /**
     * Usuario que realizó la acción.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}