<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ImportacionService
{
    protected $importados = 0;
    protected $fallidos = 0;
    protected $errores = [];
    protected $modulo = '';

    public function __construct($modulo)
    {
        $this->modulo = $modulo;
    }

    public function registrarError($fila, $mensaje, $datos = [])
    {
        $this->fallidos++;
        $this->errores[] = [
            'fila' => $fila,
            'mensaje' => $mensaje,
            'datos' => $datos,
        ];
    }

    public function registrarExito()
    {
        $this->importados++;
    }

    public function generarResumen()
    {
        $resumen = [
            'modulo' => $this->modulo,
            'total_procesados' => $this->importados + $this->fallidos,
            'importados' => $this->importados,
            'fallidos' => $this->fallidos,
            'tiene_errores' => $this->fallidos > 0,
            'archivo_errores' => null,
        ];

        if ($this->fallidos > 0) {
            $nombreArchivo = 'errores_' . $this->modulo . '_' . date('YmdHis') . '.txt';
            $contenido = $this->generarContenidoTXT();
            Storage::disk('public')->put('importaciones/' . $nombreArchivo, $contenido);
            $resumen['archivo_errores'] = $nombreArchivo;
        }

        return $resumen;
    }

    protected function generarContenidoTXT()
    {
        $contenido = "========================================\n";
        $contenido .= "REPORTE DE ERRORES DE IMPORTACIÓN\n";
        $contenido .= "Módulo: " . strtoupper($this->modulo) . "\n";
        $contenido .= "Fecha: " . date('d/m/Y H:i:s') . "\n";
        $contenido .= "========================================\n\n";
        $contenido .= "Total procesados: " . ($this->importados + $this->fallidos) . "\n";
        $contenido .= "Importados correctamente: {$this->importados}\n";
        $contenido .= "Fallidos: {$this->fallidos}\n\n";
        $contenido .= "========================================\n";
        $contenido .= "DETALLE DE ERRORES\n";
        $contenido .= "========================================\n\n";

        foreach ($this->errores as $error) {
            $contenido .= "Fila {$error['fila']}: {$error['mensaje']}\n";
            if (!empty($error['datos'])) {
                $contenido .= "  Datos: " . json_encode($error['datos'], JSON_UNESCAPED_UNICODE) . "\n";
            }
            $contenido .= "\n";
        }

        return $contenido;
    }
}