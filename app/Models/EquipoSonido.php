<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EquipoSonido extends Model
{
    use HasFactory;

    protected $table = 'equipos_sonido';

    protected $fillable = [
        'codigo_inventario',
        'categoria_sonido_id',
        'sede_id',
        'marca',
        'modelo',
        'serial',
        'potencia',
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
        return $this->belongsTo(CategoriaSonido::class, 'categoria_sonido_id');
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }
}