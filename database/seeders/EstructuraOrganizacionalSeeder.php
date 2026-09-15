<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Estado;
use App\Models\Sede;
use App\Models\Departamento;

class EstructuraOrganizacionalSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 24 ESTADOS DE VENEZUELA
        |--------------------------------------------------------------------------
        */
        $estadosData = [
            ['nombre' => 'Amazonas', 'region' => 'Guayana'],
            ['nombre' => 'Anzoátegui', 'region' => 'Oriental'],
            ['nombre' => 'Apure', 'region' => 'Los Llanos'],
            ['nombre' => 'Aragua', 'region' => 'Central'],
            ['nombre' => 'Barinas', 'region' => 'Los Llanos'],
            ['nombre' => 'Bolívar', 'region' => 'Guayana'],
            ['nombre' => 'Carabobo', 'region' => 'Central'],
            ['nombre' => 'Cojedes', 'region' => 'Central'],
            ['nombre' => 'Delta Amacuro', 'region' => 'Oriental'],
            ['nombre' => 'Distrito Capital', 'region' => 'Capital'],
            ['nombre' => 'Falcón', 'region' => 'Occidental'],
            ['nombre' => 'Guárico', 'region' => 'Los Llanos'],
            ['nombre' => 'La Guaira', 'region' => 'Capital'],
            ['nombre' => 'Lara', 'region' => 'Occidental'],
            ['nombre' => 'Mérida', 'region' => 'Occidental'],
            ['nombre' => 'Miranda', 'region' => 'Capital'],
            ['nombre' => 'Monagas', 'region' => 'Oriental'],
            ['nombre' => 'Nueva Esparta', 'region' => 'Oriental'],
            ['nombre' => 'Portuguesa', 'region' => 'Occidental'],
            ['nombre' => 'Sucre', 'region' => 'Oriental'],
            ['nombre' => 'Táchira', 'region' => 'Occidental'],
            ['nombre' => 'Trujillo', 'region' => 'Occidental'],
            ['nombre' => 'Yaracuy', 'region' => 'Occidental'],
            ['nombre' => 'Zulia', 'region' => 'Occidental'],
        ];

        $estados = [];
        foreach ($estadosData as $data) {
            $estados[$data['nombre']] = Estado::create($data);
        }

        /*
        |--------------------------------------------------------------------------
        | 24 SEDES - UNA POR CADA ESTADO
        |--------------------------------------------------------------------------
        */
        $sedesData = [
            'Amazonas' => ['nombre_sede' => 'Oficina Regional Amazonas', 'direccion' => 'Calle Principal, Puerto Ayacucho', 'codigo_postal' => '7101'],
            'Anzoátegui' => ['nombre_sede' => 'Oficina Regional Anzoátegui', 'direccion' => 'Av. Principal, Barcelona', 'codigo_postal' => '6001'],
            'Apure' => ['nombre_sede' => 'Oficina Regional Apure', 'direccion' => 'Calle Bolívar, San Fernando de Apure', 'codigo_postal' => '7001'],
            'Aragua' => ['nombre_sede' => 'Oficina Regional Aragua', 'direccion' => 'Av. Bolívar, Maracay', 'codigo_postal' => '2101'],
            'Barinas' => ['nombre_sede' => 'Oficina Regional Barinas', 'direccion' => 'Calle Comercio, Barinas', 'codigo_postal' => '5201'],
            'Bolívar' => ['nombre_sede' => 'Oficina Regional Bolívar', 'direccion' => 'Av. Guayana, Ciudad Guayana', 'codigo_postal' => '8001'],
            'Carabobo' => ['nombre_sede' => 'Oficina Regional Carabobo', 'direccion' => 'Av. Bolívar Norte, Valencia', 'codigo_postal' => '2001'],
            'Cojedes' => ['nombre_sede' => 'Oficina Regional Cojedes', 'direccion' => 'Calle Principal, San Carlos', 'codigo_postal' => '2201'],
            'Delta Amacuro' => ['nombre_sede' => 'Oficina Regional Delta Amacuro', 'direccion' => 'Av. Orinoco, Tucupita', 'codigo_postal' => '6401'],
            'Distrito Capital' => ['nombre_sede' => 'Sede Central CONAPDIS', 'direccion' => 'Av. Francisco de Miranda, Torre CONAPDIS, Caracas', 'codigo_postal' => '1060'],
            'Falcón' => ['nombre_sede' => 'Oficina Regional Falcón', 'direccion' => 'Calle Falcón, Coro', 'codigo_postal' => '4101'],
            'Guárico' => ['nombre_sede' => 'Oficina Regional Guárico', 'direccion' => 'Av. Los Llanos, San Juan de los Morros', 'codigo_postal' => '2301'],
            'La Guaira' => ['nombre_sede' => 'Oficina Regional La Guaira', 'direccion' => 'Av. Principal, La Guaira', 'codigo_postal' => '1160'],
            'Lara' => ['nombre_sede' => 'Oficina Regional Lara', 'direccion' => 'Carrera 19, Barquisimeto', 'codigo_postal' => '3001'],
            'Mérida' => ['nombre_sede' => 'Oficina Regional Mérida', 'direccion' => 'Av. Los Andes, Mérida', 'codigo_postal' => '5101'],
            'Miranda' => ['nombre_sede' => 'Oficina Regional Miranda', 'direccion' => 'Calle Bolívar, Los Teques', 'codigo_postal' => '1201'],
            'Monagas' => ['nombre_sede' => 'Oficina Regional Monagas', 'direccion' => 'Av. Principal, Maturín', 'codigo_postal' => '6201'],
            'Nueva Esparta' => ['nombre_sede' => 'Oficina Regional Nueva Esparta', 'direccion' => 'Av. Santiago Mariño, Porlamar', 'codigo_postal' => '6301'],
            'Portuguesa' => ['nombre_sede' => 'Oficina Regional Portuguesa', 'direccion' => 'Calle Principal, Guanare', 'codigo_postal' => '3301'],
            'Sucre' => ['nombre_sede' => 'Oficina Regional Sucre', 'direccion' => 'Av. Principal, Cumaná', 'codigo_postal' => '6101'],
            'Táchira' => ['nombre_sede' => 'Oficina Regional Táchira', 'direccion' => 'Carrera 7, San Cristóbal', 'codigo_postal' => '5001'],
            'Trujillo' => ['nombre_sede' => 'Oficina Regional Trujillo', 'direccion' => 'Av. Principal, Trujillo', 'codigo_postal' => '3101'],
            'Yaracuy' => ['nombre_sede' => 'Oficina Regional Yaracuy', 'direccion' => 'Calle Principal, San Felipe', 'codigo_postal' => '3201'],
            'Zulia' => ['nombre_sede' => 'Oficina Regional Zulia', 'direccion' => 'Av. 5 de Julio, Maracaibo', 'codigo_postal' => '4001'],
        ];

        $sedes = [];
        foreach ($sedesData as $estadoNombre => $sedeData) {
            $sedes[$estadoNombre] = Sede::create([
                'estado_id' => $estados[$estadoNombre]->id,
                'nombre_sede' => $sedeData['nombre_sede'],
                'direccion' => $sedeData['direccion'],
                'codigo_postal' => $sedeData['codigo_postal'],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | DEPARTAMENTOS ESTÁNDAR POR SEDE
        |--------------------------------------------------------------------------
        */
        $deptos = ['Dirección de Tecnología', 'Recursos Humanos', 'Administración', 'Atención al Ciudadano'];

        foreach ($sedes as $sede) {
            foreach ($deptos as $i => $nombre) {
                Departamento::create([
                    'sede_id' => $sede->id,
                    'nombre_departamento' => $nombre,
                    'piso' => $i + 1,
                    'extension_telefonica' => '10' . ($i + 1) . '0',
                ]);
            }
        }
    }
}