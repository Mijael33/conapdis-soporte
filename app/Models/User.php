<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'estado_id',
        'sede_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */
    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function ordenesServicio()
    {
        return $this->hasMany(OrdenServicio::class, 'tecnico_id');
    }

    public function bitacoras()
    {
        return $this->hasMany(BitacoraEquipo::class, 'usuario_id');
    }
}