<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoriaVehiculo extends Model
{
    use HasFactory;

    protected $table = 'categorias_vehiculos';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'categoria_vehiculo_id');
    }
}