<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class RegistroEntradaSalida extends Model
{
    use HasFactory;

    protected $table = 'registros_entrada_salida';

    protected $fillable = [
        'bien_tipo',
        'bien_id',
        'bien_codigo',
        'bien_descripcion',
        'sede_id',

        // Salida
        'fecha_hora_salida',
        'salida_autoriza_nombre',
        'salida_autoriza_cedula',
        'salida_autoriza_cargo',
        'salida_retira_nombre',
        'salida_retira_cedula',
        'salida_retira_cargo',
        'salida_motivo',
        'salida_destino',
        'salida_seguridad_nombre',
        'salida_seguridad_cedula',
        'salida_estado_bien',
        'salida_observaciones',
        'salida_usuario_id',
        'salida_ip',

        // Entrada
        'fecha_hora_entrada',
        'entrada_recibe_nombre',
        'entrada_recibe_cedula',
        'entrada_seguridad_nombre',
        'entrada_seguridad_cedula',
        'entrada_estado_bien',
        'entrada_observaciones',
        'entrada_usuario_id',
        'entrada_ip',

        // Control
        'estatus',
        'zona_horaria',
    ];

    protected function casts(): array
    {
        return [
            'fecha_hora_salida' => 'datetime',
            'fecha_hora_entrada' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function salidaUsuario()
    {
        return $this->belongsTo(User::class, 'salida_usuario_id');
    }

    public function entradaUsuario()
    {
        return $this->belongsTo(User::class, 'entrada_usuario_id');
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTODOS HELPER
    |--------------------------------------------------------------------------
    */

    /**
     * Obtener el bien asociado según su tipo (Tecnología, Bien Nacional, etc.)
     */
    public function getBienAttribute()
    {
        return match ($this->bien_tipo) {
            'Tecnologia' => Equipo::find($this->bien_id),
            'BienNacional' => BienNacional::find($this->bien_id),
            'Vehiculo' => Vehiculo::find($this->bien_id),
            'Sonido' => EquipoSonido::find($this->bien_id),
            default => null,
        };
    }

    /**
     * ¿Está pendiente de entrada?
     */
    public function getEsSalidaPendienteAttribute()
    {
        return $this->estatus === 'Pendiente';
    }

    /**
     * ¿Está completado?
     */
    public function getEsCompletadoAttribute()
    {
        return $this->estatus === 'Completado';
    }

    /**
     * Duración del movimiento (de salida a entrada)
     */
    public function getDuracionAttribute()
    {
        if (!$this->fecha_hora_salida || !$this->fecha_hora_entrada) {
            return null;
        }

        $inicio = Carbon::parse($this->fecha_hora_salida);
        $fin = Carbon::parse($this->fecha_hora_entrada);

        return $inicio->diffForHumans($fin, true);
    }

    /**
     * Generar número de comprobante legible
     */
    public function getNumeroComprobanteAttribute()
    {
        return 'MOV-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopePendientes($query)
    {
        return $query->where('estatus', 'Pendiente');
    }

    public function scopeCompletados($query)
    {
        return $query->where('estatus', 'Completado');
    }

    public function scopeDelTipoBien($query, $tipo)
    {
        return $query->where('bien_tipo', $tipo);
    }
}