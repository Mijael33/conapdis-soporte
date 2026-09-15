<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BitacoraAccesoContrasena extends Model
{
    use HasFactory;

    protected $table = 'bitacora_acceso_contrasenas';

    public $timestamps = false;

    protected $fillable = [
        'equipo_so_id',
        'usuario_id',
        'motivo',
        'ip_address',
        'fecha_acceso',
    ];

    protected function casts(): array
    {
        return [
            'fecha_acceso' => 'datetime',
        ];
    }

    public function sistemaOperativo()
    {
        return $this->belongsTo(EquipoSistemaOperativo::class, 'equipo_so_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}