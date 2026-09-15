<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoriaSonido extends Model
{
    use HasFactory;

    protected $table = 'categorias_sonido';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function equiposSonido()
    {
        return $this->hasMany(EquipoSonido::class, 'categoria_sonido_id');
    }
}