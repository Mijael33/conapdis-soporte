<?php

namespace App\Services;

use App\Models\BitacoraGlobal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class BitacoraService
{
    /**
     * Registrar una acción en la bitácora.
     */
    public static function registrar(
        string $modulo,
        string $accion,
        string $descripcion,
        ?array $datosAnteriores = null,
        ?array $datosNuevos = null,
        ?array $datosExtra = null,
        $modelo = null,
        ?string $modeloCodigo = null
    ): BitacoraGlobal {
        $user = Auth::user();

        return BitacoraGlobal::create([
            'modulo' => $modulo,
            'accion' => $accion,
            'modelo_tipo' => $modelo ? get_class($modelo) : null,
            'modelo_id' => $modelo ? $modelo->id : null,
            'modelo_codigo' => $modeloCodigo,
            'usuario_id' => $user?->id,
            'usuario_nombre_snapshot' => $user?->name,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'descripcion' => $descripcion,
            'datos_anteriores' => $datosAnteriores,
            'datos_nuevos' => $datosNuevos,
            'datos_extra' => $datosExtra,
            'fecha_registro' => now(),
            'zona_horaria' => config('app.timezone', 'America/Caracas'),
        ]);
    }

    /**
     * Registrar creación de un modelo.
     */
    public static function crear(string $modulo, $modelo, string $descripcion, ?string $codigo = null)
    {
        return self::registrar(
            $modulo,
            'crear',
            $descripcion,
            null,
            $modelo->toArray(),
            null,
            $modelo,
            $codigo
        );
    }

    /**
     * Registrar edición de un modelo.
     * Si se pasan $datosNuevos, se usan; si no, se obtienen del modelo actualizado.
     */
    public static function editar(
        string $modulo,
        $modelo,
        array $datosAnteriores,
        string $descripcion,
        ?string $codigo = null,
        ?array $datosNuevos = null
    ) {
        if ($datosNuevos === null) {
            try {
                $datosNuevos = $modelo->refresh()->toArray();
            } catch (\Exception $e) {
                $datosNuevos = $modelo->toArray();
            }
        }

        return self::registrar(
            $modulo,
            'editar',
            $descripcion,
            $datosAnteriores,
            $datosNuevos,
            null,
            $modelo,
            $codigo
        );
    }

    /**
     * Registrar eliminación de un modelo.
     */
    public static function eliminar(string $modulo, $modelo, string $descripcion, ?string $codigo = null)
    {
        return self::registrar(
            $modulo,
            'eliminar',
            $descripcion,
            $modelo->toArray(),
            null,
            null,
            $modelo,
            $codigo
        );
    }

    /**
     * Registrar cambio de estatus.
     */
    public static function cambiarEstatus(string $modulo, $modelo, string $estatusAnterior, string $estatusNuevo, ?string $codigo = null)
    {
        return self::registrar(
            $modulo,
            'cambiar_estatus',
            "Estatus cambiado de '{$estatusAnterior}' a '{$estatusNuevo}'",
            ['estatus' => $estatusAnterior],
            ['estatus' => $estatusNuevo],
            null,
            $modelo,
            $codigo
        );
    }

    /**
     * Registrar una acción personalizada.
     */
    public static function accion(string $modulo, string $accion, string $descripcion, $modelo = null, ?array $extra = null, ?string $codigo = null)
    {
        return self::registrar(
            $modulo,
            $accion,
            $descripcion,
            null,
            null,
            $extra,
            $modelo,
            $codigo
        );
    }
}