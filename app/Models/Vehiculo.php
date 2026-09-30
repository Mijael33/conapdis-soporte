<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehiculo extends Model
{
    use HasFactory;

    protected $table = 'vehiculos';

    protected $fillable = [
        'codigo_inventario',
        'categoria_vehiculo_id',
        'sede_id',
        'placa',
        'marca',
        'modelo',
        'anio',
        'color',
        'serial_motor',
        'serial_carroceria',
        'kilometraje',
        'estatus',
        'usuario_asignado_nombre',
        'usuario_asignado_cedula',
        'usuario_asignado_cargo',
        'valor_prudencial',
        'valor_adquisicion',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'valor_prudencial' => 'decimal:2',
            'valor_adquisicion' => 'decimal:2',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function categoria()
    {
        return $this->belongsTo(CategoriaVehiculo::class, 'categoria_vehiculo_id');
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }
}