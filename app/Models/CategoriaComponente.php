<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoriaComponente extends Model
{
    use HasFactory;

    protected $table = 'categorias_componentes';

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
     * Componentes que pertenecen a esta categoría.
     */
    public function componentes()
    {
        return $this->hasMany(Componente::class, 'categoria_componente_id');
    }
}