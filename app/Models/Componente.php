<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Componente extends Model
{
    use HasFactory;

    protected $table = 'componentes';

    protected $fillable = [
        'categoria_componente_id',
        'marca',
        'modelo',
        'serial_unico',
        'estatus',
        'sede_id',
        'caracteristicas_tecnicas',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'caracteristicas_tecnicas' => 'array',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */
    public function categoria()
    {
        return $this->belongsTo(CategoriaComponente::class, 'categoria_componente_id');
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function equipos()
    {
        return $this->belongsToMany(Equipo::class, 'equipo_componente')
                    ->withPivot('fecha_instalacion', 'fecha_desinstalacion', 'activo')
                    ->withTimestamps();
    }

    public function equipoActual()
    {
        return $this->belongsToMany(Equipo::class, 'equipo_componente')
                    ->withPivot('fecha_instalacion', 'fecha_desinstalacion', 'activo')
                    ->wherePivot('activo', true)
                    ->withTimestamps();
    }

    /**
     * Bitácora de cambios del componente (independiente del equipo).
     */
    public function bitacoras()
    {
        return $this->hasMany(BitacoraComponente::class, 'componente_id');
    }
}