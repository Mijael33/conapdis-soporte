<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Crypt;

class EquipoSistemaOperativo extends Model
{
    use HasFactory;

    protected $table = 'equipo_sistemas_operativos';

    protected $fillable = [
        'equipo_id',
        'nombre',
        'arquitectura',
        'password_encriptada',
        'tiene_contrasena',
        'notas',
    ];

    protected $hidden = [
        'password_encriptada',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */
    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function accesosContrasena()
    {
        return $this->hasMany(BitacoraAccesoContrasena::class, 'equipo_so_id');
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTODOS PARA CONTRASEÑA
    |--------------------------------------------------------------------------
    */
    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password_encriptada'] = Crypt::encryptString($value);
            $this->attributes['tiene_contrasena'] = true;
        } else {
            $this->attributes['password_encriptada'] = null;
            $this->attributes['tiene_contrasena'] = false;
        }
    }

    public function getPasswordAttribute()
    {
        if ($this->password_encriptada) {
            return Crypt::decryptString($this->password_encriptada);
        }
        return null;
    }
}