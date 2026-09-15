<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrdenServicio extends Model
{
    use HasFactory;

    protected $table = 'ordenes_servicio';

    protected $fillable = [
        'codigo_ticket',
        'equipo_id',
        'tecnico_id',
        'problema_reportado_usuario',
        'diagnostico_tecnico',
        'acciones_realizadas',
        'estatus_final',
        'fecha_inicio',
        'fecha_cierre',
    ];

    /**
     * Casts para fechas.
     */
    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'datetime',
            'fecha_cierre' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */
    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }

    public function tecnico()
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }
}