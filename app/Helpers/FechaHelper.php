<?php

namespace App\Helpers;

use Carbon\Carbon;

class FechaHelper
{
    /**
     * Formatear fecha completa en español.
     * Ej: 15/09/2026 03:45 PM
     */
    public static function formatear($fecha)
    {
        if (!$fecha) {
            return 'N/A';
        }

        $carbon = $fecha instanceof Carbon ? $fecha : Carbon::parse($fecha);

        return $carbon->format('d/m/Y') . ' ' . self::horaNormal($carbon);
    }

    /**
     * Formatear hora en formato normal (12h con AM/PM).
     */
    public static function horaNormal($fecha)
    {
        if (!$fecha) {
            return 'N/A';
        }

        $carbon = $fecha instanceof Carbon ? $fecha : Carbon::parse($fecha);

        return $carbon->format('h:i A');
    }

    /**
     * Solo fecha: 15/09/2026
     */
    public static function soloFecha($fecha)
    {
        if (!$fecha) {
            return 'N/A';
        }

        $carbon = $fecha instanceof Carbon ? $fecha : Carbon::parse($fecha);

        return $carbon->format('d/m/Y');
    }

    /**
     * Fecha larga: 15 de Septiembre de 2026
     */
    public static function fechaLarga($fecha)
    {
        if (!$fecha) {
            return 'N/A';
        }

        $carbon = $fecha instanceof Carbon ? $fecha : Carbon::parse($fecha);

        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        $mes = $meses[(int) $carbon->format('n')];

        return $carbon->format('d') . ' de ' . $mes . ' de ' . $carbon->format('Y');
    }
}