<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoriaBien extends Model
{
    use HasFactory;

    protected $table = 'categorias_bienes';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function bienes()
    {
        return $this->hasMany(BienNacional::class, 'categoria_bien_id');
    }
}