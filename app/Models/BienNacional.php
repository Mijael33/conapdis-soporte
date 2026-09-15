<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BienNacional extends Model
{
    use HasFactory;

    protected $table = 'bienes_nacionales';

    protected $fillable = [
        'codigo_inventario',
        'categoria_bien_id',
        'sede_id',
        'descripcion',
        'marca',
        'modelo',
        'serial',
        'color',
        'material',
        'estatus',
        'usuario_asignado_nombre',
        'usuario_asignado_cedula',
        'usuario_asignado_cargo',
        'valor_adquisicion',
        'fecha_adquisicion',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'valor_adquisicion' => 'decimal:2',
            'fecha_adquisicion' => 'date',
        ];
    }

    public function categoria()
    {
        return $this->belongsTo(CategoriaBien::class, 'categoria_bien_id');
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }
}