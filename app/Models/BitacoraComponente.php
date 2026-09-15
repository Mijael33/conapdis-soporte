<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BitacoraComponente extends Model
{
    use HasFactory;

    protected $table = 'bitacora_componentes';

    public $timestamps = false;

    protected $fillable = [
        'componente_id',
        'usuario_id',
        'accion',
        'descripcion_detallada',
        'datos_anteriores',
        'datos_nuevos',
        'fecha_registro',
    ];

    protected function casts(): array
    {
        return [
            'datos_anteriores' => 'array',
            'datos_nuevos' => 'array',
            'fecha_registro' => 'datetime',
        ];
    }

    public function componente()
    {
        return $this->belongsTo(Componente::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}