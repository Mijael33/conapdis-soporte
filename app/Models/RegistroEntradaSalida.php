<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RegistroEntradaSalida extends Model
{
    use HasFactory;

    protected $table = 'registros_entrada_salida';

    protected $fillable = [
        'tipo',
        'bien_tipo',
        'bien_id',
        'sede_id',
        'fecha_hora_salida',
        'fecha_hora_entrada',
        'autorizado_por_nombre',
        'autorizado_por_cedula',
        'autorizado_por_cargo',
        'persona_retira_nombre',
        'persona_retira_cedula',
        'persona_retira_cargo',
        'motivo',
        'destino',
        'seguridad_salida_nombre',
        'seguridad_salida_cedula',
        'seguridad_entrada_nombre',
        'seguridad_entrada_cedula',
        'estado_salida',
        'estado_entrada',
        'observaciones_salida',
        'observaciones_entrada',
        'usuario_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha_hora_salida' => 'datetime',
            'fecha_hora_entrada' => 'datetime',
        ];
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Obtener el bien asociado según el tipo.
     */
    public function getBienAttribute()
    {
        return match($this->bien_tipo) {
            'Tecnologia' => Equipo::find($this->bien_id),
            'BienNacional' => BienNacional::find($this->bien_id),
            'Vehiculo' => Vehiculo::find($this->bien_id),
            'Sonido' => EquipoSonido::find($this->bien_id),
            default => null,
        };
    }

    /**
     * Obtener el código de inventario del bien.
     */
    public function getCodigoInventarioAttribute()
    {
        $bien = $this->bien;
        if (!$bien) return 'N/A';

        return match($this->bien_tipo) {
            'Tecnologia' => $bien->codigo_inventario_institucional,
            'BienNacional' => $bien->codigo_inventario,
            'Vehiculo' => $bien->codigo_inventario,
            'Sonido' => $bien->codigo_inventario,
            default => 'N/A',
        };
    }

    /**
     * Obtener la descripción del bien.
     */
    public function getDescripcionBienAttribute()
    {
        $bien = $this->bien;
        if (!$bien) return 'N/A';

        return match($this->bien_tipo) {
            'Tecnologia' => $bien->marca . ' ' . $bien->modelo,
            'BienNacional' => $bien->descripcion,
            'Vehiculo' => $bien->marca . ' ' . $bien->modelo . ' (' . $bien->placa . ')',
            'Sonido' => $bien->marca . ' ' . $bien->modelo,
            default => 'N/A',
        };
    }
}