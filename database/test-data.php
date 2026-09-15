<?php

/**
 * DATOS DE PRUEBA - SISTEMA CONAPDIS
 * 
 * Para ejecutar:
 * php artisan tinker < database/test-data.php
 * 
 * O copiar y pegar bloque por bloque en:
 * php artisan tinker
 */

// ====================================================================
// BLOQUE 1: CATÁLOGOS (Tipos de Equipos y Categorías)
// ====================================================================
echo "Creando catálogos...\n";

use App\Models\TipoEquipo;
use App\Models\CategoriaComponente;

TipoEquipo::firstOrCreate(['nombre' => 'PC de Escritorio'], ['descripcion' => 'Computadora de escritorio estándar']);
TipoEquipo::firstOrCreate(['nombre' => 'Laptop'], ['descripcion' => 'Computadora portátil']);
TipoEquipo::firstOrCreate(['nombre' => 'Impresora'], ['descripcion' => 'Impresora láser o de inyección de tinta']);
TipoEquipo::firstOrCreate(['nombre' => 'Switch de Red'], ['descripcion' => 'Switch para infraestructura de red']);
TipoEquipo::firstOrCreate(['nombre' => 'Servidor'], ['descripcion' => 'Servidor para aplicaciones o almacenamiento']);
TipoEquipo::firstOrCreate(['nombre' => 'UPS'], ['descripcion' => 'Sistema de alimentación ininterrumpida']);
TipoEquipo::firstOrCreate(['nombre' => 'Monitor'], ['descripcion' => 'Pantalla o monitor']);
TipoEquipo::firstOrCreate(['nombre' => 'Scanner'], ['descripcion' => 'Escáner de documentos']);

CategoriaComponente::firstOrCreate(['nombre' => 'CPU'], ['descripcion' => 'Procesador central']);
CategoriaComponente::firstOrCreate(['nombre' => 'GPU'], ['descripcion' => 'Tarjeta gráfica o de video']);
CategoriaComponente::firstOrCreate(['nombre' => 'RAM'], ['descripcion' => 'Memoria RAM']);
CategoriaComponente::firstOrCreate(['nombre' => 'Almacenamiento'], ['descripcion' => 'Disco duro, SSD, NVMe']);
CategoriaComponente::firstOrCreate(['nombre' => 'Placa Madre'], ['descripcion' => 'Motherboard o placa base']);
CategoriaComponente::firstOrCreate(['nombre' => 'Fuente de Poder'], ['descripcion' => 'Fuente de alimentación']);
CategoriaComponente::firstOrCreate(['nombre' => 'Ventilación'], ['descripcion' => 'Ventiladores y sistemas de enfriamiento']);
CategoriaComponente::firstOrCreate(['nombre' => 'Tarjeta de Red'], ['descripcion' => 'NIC, WiFi, Bluetooth']);
CategoriaComponente::firstOrCreate(['nombre' => 'Periférico'], ['descripcion' => 'Teclado, mouse, webcam']);

echo "✓ Catálogos creados.\n";

// ====================================================================
// BLOQUE 2: USUARIOS DE PRUEBA POR SEDE
// ====================================================================
echo "Creando usuarios de prueba...\n";

use App\Models\User;
use App\Models\Estado;
use App\Models\Sede;
use Illuminate\Support\Facades\Hash;

// Coordinador Carabobo
$carabobo = Estado::where('nombre', 'Carabobo')->first();
$sedeCarabobo = Sede::where('nombre_sede', 'Oficina Regional Carabobo')->first();
User::firstOrCreate(['email' => 'coordinador@conapdis.gob.ve'], [
    'name' => 'Coordinador Carabobo',
    'password' => Hash::make('Conapdis2024!'),
    'email_verified_at' => now(),
    'estado_id' => $carabobo->id,
    'sede_id' => $sedeCarabobo->id,
])->assignRole('Coordinador de Soporte');

// Técnico Zulia
$zulia = Estado::where('nombre', 'Zulia')->first();
$sedeZulia = Sede::where('nombre_sede', 'Oficina Regional Zulia')->first();
User::firstOrCreate(['email' => 'tecnico@conapdis.gob.ve'], [
    'name' => 'Técnico Zulia',
    'password' => Hash::make('Conapdis2024!'),
    'email_verified_at' => now(),
    'estado_id' => $zulia->id,
    'sede_id' => $sedeZulia->id,
])->assignRole('Técnico');

// Técnico Lara
$lara = Estado::where('nombre', 'Lara')->first();
$sedeLara = Sede::where('nombre_sede', 'Oficina Regional Lara')->first();
User::firstOrCreate(['email' => 'tecnico.lara@conapdis.gob.ve'], [
    'name' => 'Técnico Lara',
    'password' => Hash::make('Conapdis2024!'),
    'email_verified_at' => now(),
    'estado_id' => $lara->id,
    'sede_id' => $sedeLara->id,
])->assignRole('Técnico');

// Auditor Nacional
$dc = Estado::where('nombre', 'Distrito Capital')->first();
$sedeCentral = Sede::where('nombre_sede', 'Sede Central CONAPDIS')->first();
User::firstOrCreate(['email' => 'auditor@conapdis.gob.ve'], [
    'name' => 'Auditor Nacional',
    'password' => Hash::make('Conapdis2024!'),
    'email_verified_at' => now(),
    'estado_id' => $dc->id,
    'sede_id' => $sedeCentral->id,
])->assignRole('Auditor');

echo "✓ Usuarios creados.\n";

// ====================================================================
// BLOQUE 3: COMPONENTES DE PRUEBA POR SEDE
// ====================================================================
echo "Creando componentes de prueba...\n";

use App\Models\Componente;
use App\Models\Departamento;

// --- SEDE CENTRAL (Distrito Capital) ---
$cpu1 = Componente::firstOrCreate(['serial_unico' => 'CPU-INTEL-i9-001'], [
    'categoria_componente_id' => 1, 'marca' => 'Intel', 'modelo' => 'Core i9-14900K',
    'estatus' => 'Disponible', 'sede_id' => $sedeCentral->id,
    'caracteristicas_tecnicas' => ['nucleos' => '24', 'hilos' => '32', 'frecuencia' => '6.0GHz', 'socket' => 'LGA1700'],
]);
$cpu2 = Componente::firstOrCreate(['serial_unico' => 'CPU-INTEL-i7-002'], [
    'categoria_componente_id' => 1, 'marca' => 'Intel', 'modelo' => 'Core i7-14700K',
    'estatus' => 'Disponible', 'sede_id' => $sedeCentral->id,
    'caracteristicas_tecnicas' => ['nucleos' => '20', 'hilos' => '28', 'frecuencia' => '5.6GHz', 'socket' => 'LGA1700'],
]);
$cpu3 = Componente::firstOrCreate(['serial_unico' => 'CPU-INTEL-i5-003'], [
    'categoria_componente_id' => 1, 'marca' => 'Intel', 'modelo' => 'Core i5-14600K',
    'estatus' => 'En Revisión', 'sede_id' => $sedeCentral->id,
    'caracteristicas_tecnicas' => ['nucleos' => '14', 'hilos' => '20', 'frecuencia' => '5.3GHz', 'socket' => 'LGA1700'],
    'observaciones' => 'Presenta temperaturas elevadas. En diagnóstico.',
]);

$ram1 = Componente::firstOrCreate(['serial_unico' => 'RAM-COR-DDR5-001'], [
    'categoria_componente_id' => 3, 'marca' => 'Corsair', 'modelo' => 'Vengeance DDR5',
    'estatus' => 'Disponible', 'sede_id' => $sedeCentral->id,
    'caracteristicas_tecnicas' => ['capacidad' => '64GB', 'tipo' => 'DDR5', 'frecuencia' => '6000MHz', 'modulos' => '2x32GB'],
]);
$ram2 = Componente::firstOrCreate(['serial_unico' => 'RAM-KING-DDR5-002'], [
    'categoria_componente_id' => 3, 'marca' => 'Kingston', 'modelo' => 'Fury Renegade DDR5',
    'estatus' => 'Disponible', 'sede_id' => $sedeCentral->id,
    'caracteristicas_tecnicas' => ['capacidad' => '32GB', 'tipo' => 'DDR5', 'frecuencia' => '7200MHz', 'modulos' => '2x16GB'],
]);

$ssd1 = Componente::firstOrCreate(['serial_unico' => 'SSD-SAM-990PRO-001'], [
    'categoria_componente_id' => 4, 'marca' => 'Samsung', 'modelo' => '990 Pro',
    'estatus' => 'Disponible', 'sede_id' => $sedeCentral->id,
    'caracteristicas_tecnicas' => ['capacidad' => '2TB', 'tipo' => 'NVMe M.2', 'interfaz' => 'PCIe 4.0 x4', 'lectura' => '7450MB/s'],
]);
$ssd2 = Componente::firstOrCreate(['serial_unico' => 'SSD-WD-SN850X-002'], [
    'categoria_componente_id' => 4, 'marca' => 'Western Digital', 'modelo' => 'WD Black SN850X',
    'estatus' => 'Disponible', 'sede_id' => $sedeCentral->id,
    'caracteristicas_tecnicas' => ['capacidad' => '4TB', 'tipo' => 'NVMe M.2', 'interfaz' => 'PCIe 4.0 x4', 'lectura' => '7300MB/s'],
]);

$gpu1 = Componente::firstOrCreate(['serial_unico' => 'GPU-NV-RTX4090-001'], [
    'categoria_componente_id' => 2, 'marca' => 'NVIDIA', 'modelo' => 'RTX 4090',
    'estatus' => 'Disponible', 'sede_id' => $sedeCentral->id,
    'caracteristicas_tecnicas' => ['vram' => '24GB GDDR6X', 'cuda_cores' => '16384', 'tdp' => '450W'],
]);
$gpu2 = Componente::firstOrCreate(['serial_unico' => 'GPU-NV-RTX4070-002'], [
    'categoria_componente_id' => 2, 'marca' => 'NVIDIA', 'modelo' => 'RTX 4070 Ti',
    'estatus' => 'Disponible', 'sede_id' => $sedeCentral->id,
    'caracteristicas_tecnicas' => ['vram' => '12GB GDDR6X', 'cuda_cores' => '7680', 'tdp' => '285W'],
]);

$mobo1 = Componente::firstOrCreate(['serial_unico' => 'MB-ASUS-Z790-001'], [
    'categoria_componente_id' => 5, 'marca' => 'ASUS', 'modelo' => 'ROG Maximus Z790 Hero',
    'estatus' => 'Disponible', 'sede_id' => $sedeCentral->id,
    'caracteristicas_tecnicas' => ['socket' => 'LGA1700', 'chipset' => 'Z790', 'formato' => 'ATX', 'ram_max' => '128GB'],
]);

// --- SEDE CARABOBO ---
$cpu4 = Componente::firstOrCreate(['serial_unico' => 'CPU-AMD-RYZEN9-001'], [
    'categoria_componente_id' => 1, 'marca' => 'AMD', 'modelo' => 'Ryzen 9 7950X',
    'estatus' => 'Disponible', 'sede_id' => $sedeCarabobo->id,
    'caracteristicas_tecnicas' => ['nucleos' => '16', 'hilos' => '32', 'frecuencia' => '5.7GHz', 'socket' => 'AM5'],
]);
$cpu5 = Componente::firstOrCreate(['serial_unico' => 'CPU-AMD-RYZEN7-002'], [
    'categoria_componente_id' => 1, 'marca' => 'AMD', 'modelo' => 'Ryzen 7 7800X3D',
    'estatus' => 'Disponible', 'sede_id' => $sedeCarabobo->id,
    'caracteristicas_tecnicas' => ['nucleos' => '8', 'hilos' => '16', 'frecuencia' => '5.0GHz', 'socket' => 'AM5', 'cache_l3' => '96MB'],
]);

$ram3 = Componente::firstOrCreate(['serial_unico' => 'RAM-GSKILL-DDR5-003'], [
    'categoria_componente_id' => 3, 'marca' => 'G.Skill', 'modelo' => 'Trident Z5 DDR5',
    'estatus' => 'Disponible', 'sede_id' => $sedeCarabobo->id,
    'caracteristicas_tecnicas' => ['capacidad' => '32GB', 'tipo' => 'DDR5', 'frecuencia' => '6400MHz', 'modulos' => '2x16GB'],
]);
$ram4 = Componente::firstOrCreate(['serial_unico' => 'RAM-COR-DDR5-004'], [
    'categoria_componente_id' => 3, 'marca' => 'Corsair', 'modelo' => 'Dominator Platinum DDR5',
    'estatus' => 'En Revisión', 'sede_id' => $sedeCarabobo->id,
    'caracteristicas_tecnicas' => ['capacidad' => '32GB', 'tipo' => 'DDR5', 'frecuencia' => '6600MHz'],
    'observaciones' => 'Posible falla en slot 2. En pruebas.',
]);

$ssd3 = Componente::firstOrCreate(['serial_unico' => 'SSD-KING-KC3000-003'], [
    'categoria_componente_id' => 4, 'marca' => 'Kingston', 'modelo' => 'KC3000',
    'estatus' => 'Disponible', 'sede_id' => $sedeCarabobo->id,
    'caracteristicas_tecnicas' => ['capacidad' => '1TB', 'tipo' => 'NVMe M.2', 'interfaz' => 'PCIe 4.0 x4', 'lectura' => '7000MB/s'],
]);
$ssd4 = Componente::firstOrCreate(['serial_unico' => 'SSD-CRUC-T700-004'], [
    'categoria_componente_id' => 4, 'marca' => 'Crucial', 'modelo' => 'T700',
    'estatus' => 'Disponible', 'sede_id' => $sedeCarabobo->id,
    'caracteristicas_tecnicas' => ['capacidad' => '2TB', 'tipo' => 'NVMe M.2', 'interfaz' => 'PCIe 5.0 x4', 'lectura' => '12400MB/s'],
]);

// --- SEDE ZULIA ---
$cpu6 = Componente::firstOrCreate(['serial_unico' => 'CPU-INTEL-i3-005'], [
    'categoria_componente_id' => 1, 'marca' => 'Intel', 'modelo' => 'Core i3-14100',
    'estatus' => 'Disponible', 'sede_id' => $sedeZulia->id,
    'caracteristicas_tecnicas' => ['nucleos' => '4', 'hilos' => '8', 'frecuencia' => '4.7GHz', 'socket' => 'LGA1700'],
]);

$ram5 = Componente::firstOrCreate(['serial_unico' => 'RAM-CRUCIAL-DDR4-005'], [
    'categoria_componente_id' => 3, 'marca' => 'Crucial', 'modelo' => 'DDR4',
    'estatus' => 'Disponible', 'sede_id' => $sedeZulia->id,
    'caracteristicas_tecnicas' => ['capacidad' => '16GB', 'tipo' => 'DDR4', 'frecuencia' => '3200MHz', 'modulos' => '1x16GB'],
]);
$ram6 = Componente::firstOrCreate(['serial_unico' => 'RAM-KING-DDR4-006'], [
    'categoria_componente_id' => 3, 'marca' => 'Kingston', 'modelo' => 'Fury DDR4',
    'estatus' => 'Disponible', 'sede_id' => $sedeZulia->id,
    'caracteristicas_tecnicas' => ['capacidad' => '8GB', 'tipo' => 'DDR4', 'frecuencia' => '3200MHz'],
]);

$ssd5 = Componente::firstOrCreate(['serial_unico' => 'SSD-SAM-870EVO-005'], [
    'categoria_componente_id' => 4, 'marca' => 'Samsung', 'modelo' => '870 EVO',
    'estatus' => 'Disponible', 'sede_id' => $sedeZulia->id,
    'caracteristicas_tecnicas' => ['capacidad' => '1TB', 'tipo' => 'SSD SATA III', 'lectura' => '560MB/s', 'escritura' => '530MB/s'],
]);
$ssd6 = Componente::firstOrCreate(['serial_unico' => 'HDD-WD-BLUE-006'], [
    'categoria_componente_id' => 4, 'marca' => 'Western Digital', 'modelo' => 'WD Blue',
    'estatus' => 'Disponible', 'sede_id' => $sedeZulia->id,
    'caracteristicas_tecnicas' => ['capacidad' => '2TB', 'tipo' => 'HDD SATA III', 'rpm' => '7200', 'cache' => '256MB'],
]);

$fuente1 = Componente::firstOrCreate(['serial_unico' => 'PSU-COR-RM850-001'], [
    'categoria_componente_id' => 6, 'marca' => 'Corsair', 'modelo' => 'RM850x',
    'estatus' => 'Disponible', 'sede_id' => $sedeZulia->id,
    'caracteristicas_tecnicas' => ['potencia' => '850W', 'certificacion' => '80 Plus Gold', 'modular' => 'Full'],
]);

// --- SEDE LARA ---
$cpu7 = Componente::firstOrCreate(['serial_unico' => 'CPU-AMD-RYZEN5-007'], [
    'categoria_componente_id' => 1, 'marca' => 'AMD', 'modelo' => 'Ryzen 5 7600X',
    'estatus' => 'Disponible', 'sede_id' => $sedeLara->id,
    'caracteristicas_tecnicas' => ['nucleos' => '6', 'hilos' => '12', 'frecuencia' => '5.3GHz', 'socket' => 'AM5'],
]);

$ram7 = Componente::firstOrCreate(['serial_unico' => 'RAM-ADATA-DDR5-007'], [
    'categoria_componente_id' => 3, 'marca' => 'ADATA', 'modelo' => 'XPG Lancer DDR5',
    'estatus' => 'Disponible', 'sede_id' => $sedeLara->id,
    'caracteristicas_tecnicas' => ['capacidad' => '16GB', 'tipo' => 'DDR5', 'frecuencia' => '5200MHz'],
]);

$periferico1 = Componente::firstOrCreate(['serial_unico' => 'PERF-LOGITECH-KB-001'], [
    'categoria_componente_id' => 9, 'marca' => 'Logitech', 'modelo' => 'MX Keys',
    'estatus' => 'Disponible', 'sede_id' => $sedeLara->id,
    'caracteristicas_tecnicas' => ['tipo' => 'Teclado', 'conexion' => 'Bluetooth/USB-C', 'layout' => 'Español'],
]);
$periferico2 = Componente::firstOrCreate(['serial_unico' => 'PERF-LOGITECH-MOUSE-002'], [
    'categoria_componente_id' => 9, 'marca' => 'Logitech', 'modelo' => 'MX Master 3S',
    'estatus' => 'Disponible', 'sede_id' => $sedeLara->id,
    'caracteristicas_tecnicas' => ['tipo' => 'Mouse', 'dpi' => '8000', 'conexion' => 'Bluetooth/USB-C'],
]);

echo "✓ Componentes creados.\n";

// ====================================================================
// BLOQUE 4: EQUIPOS DE PRUEBA CON COMPONENTES ASIGNADOS
// ====================================================================
echo "Creando equipos y asignando componentes...\n";

use App\Models\Equipo;
use App\Models\TipoEquipo;
use Illuminate\Support\Facades\DB;

$tipoPC = TipoEquipo::where('nombre', 'PC de Escritorio')->first();
$tipoLaptop = TipoEquipo::where('nombre', 'Laptop')->first();
$tipoServidor = TipoEquipo::where('nombre', 'Servidor')->first();
$tipoImpresora = TipoEquipo::where('nombre', 'Impresora')->first();

// --- EQUIPOS SEDE CENTRAL ---
$deptoTecCentral = Departamento::where('nombre_departamento', 'Dirección de Tecnología')->where('sede_id', $sedeCentral->id)->first();
$deptoRrhhCentral = Departamento::where('nombre_departamento', 'Recursos Humanos')->where('sede_id', $sedeCentral->id)->first();
$deptoAdminCentral = Departamento::where('nombre_departamento', 'Administración')->where('sede_id', $sedeCentral->id)->first();

// PC-001 - Sede Central - Dirección de Tecnología (OPERATIVO - con componentes)
$equipo1 = Equipo::firstOrCreate(['codigo_inventario_institucional' => 'CONAPDIS-PC-001'], [
    'serial_chasis' => 'CHS-DELL-OPTIPLEX-001',
    'tipo_equipo_id' => $tipoPC->id,
    'departamento_id' => $deptoTecCentral->id,
    'marca' => 'Dell', 'modelo' => 'OptiPlex 7080',
    'estatus_general' => 'Operativo',
    'usuario_asignado_nombre' => 'Carlos Mendoza',
    'usuario_asignado_cedula' => 'V-12345678',
    'usuario_asignado_cargo' => 'Director de Tecnología',
]);
// Asignar componentes y cambiar estatus a Instalado
if (!$equipo1->componentes()->where('componente_id', $cpu1->id)->exists()) {
    DB::beginTransaction();
    $equipo1->componentes()->attach($cpu1->id, ['fecha_instalacion' => now(), 'activo' => true]); $cpu1->update(['estatus' => 'Instalado']);
    $equipo1->componentes()->attach($ram1->id, ['fecha_instalacion' => now(), 'activo' => true]); $ram1->update(['estatus' => 'Instalado']);
    $equipo1->componentes()->attach($ssd1->id, ['fecha_instalacion' => now(), 'activo' => true]); $ssd1->update(['estatus' => 'Instalado']);
    $equipo1->componentes()->attach($gpu1->id, ['fecha_instalacion' => now(), 'activo' => true]); $gpu1->update(['estatus' => 'Instalado']);
    $equipo1->componentes()->attach($mobo1->id, ['fecha_instalacion' => now(), 'activo' => true]); $mobo1->update(['estatus' => 'Instalado']);
    DB::commit();
    echo "   - CONAPDIS-PC-001: CPU i9 + 64GB RAM + 2TB SSD + RTX 4090\n";
}

// PC-002 - Sede Central - Recursos Humanos (OPERATIVO)
$equipo2 = Equipo::firstOrCreate(['codigo_inventario_institucional' => 'CONAPDIS-PC-002'], [
    'serial_chasis' => 'CHS-HP-ELITEDESK-001',
    'tipo_equipo_id' => $tipoPC->id,
    'departamento_id' => $deptoRrhhCentral->id,
    'marca' => 'HP', 'modelo' => 'EliteDesk 800 G6',
    'estatus_general' => 'Operativo',
    'usuario_asignado_nombre' => 'María González',
    'usuario_asignado_cedula' => 'V-23456789',
    'usuario_asignado_cargo' => 'Coordinadora de RRHH',
]);
if (!$equipo2->componentes()->where('componente_id', $cpu2->id)->exists()) {
    DB::beginTransaction();
    $equipo2->componentes()->attach($cpu2->id, ['fecha_instalacion' => now(), 'activo' => true]); $cpu2->update(['estatus' => 'Instalado']);
    $equipo2->componentes()->attach($ram2->id, ['fecha_instalacion' => now(), 'activo' => true]); $ram2->update(['estatus' => 'Instalado']);
    $equipo2->componentes()->attach($ssd2->id, ['fecha_instalacion' => now(), 'activo' => true]); $ssd2->update(['estatus' => 'Instalado']);
    $equipo2->componentes()->attach($gpu2->id, ['fecha_instalacion' => now(), 'activo' => true]); $gpu2->update(['estatus' => 'Instalado']);
    DB::commit();
    echo "   - CONAPDIS-PC-002: CPU i7 + 32GB RAM + 4TB SSD + RTX 4070 Ti\n";
}

// SRV-001 - Sede Central - Servidor (OPERATIVO)
$equipo3 = Equipo::firstOrCreate(['codigo_inventario_institucional' => 'CONAPDIS-SRV-001'], [
    'serial_chasis' => 'CHS-DELL-POWEREDGE-001',
    'tipo_equipo_id' => $tipoServidor->id,
    'departamento_id' => $deptoTecCentral->id,
    'marca' => 'Dell', 'modelo' => 'PowerEdge R750',
    'estatus_general' => 'Operativo',
    'usuario_asignado_nombre' => 'Sistema',
    'usuario_asignado_cedula' => 'N/A',
    'usuario_asignado_cargo' => 'Servidor de Aplicaciones',
]);
echo "   - CONAPDIS-SRV-001: Servidor (sin componentes asignados)\n";

// PC-003 - Sede Central - Administración (EN MANTENIMIENTO)
$equipo4 = Equipo::firstOrCreate(['codigo_inventario_institucional' => 'CONAPDIS-PC-003'], [
    'serial_chasis' => 'CHS-LEN-IDEACENTRE-001',
    'tipo_equipo_id' => $tipoPC->id,
    'departamento_id' => $deptoAdminCentral->id,
    'marca' => 'Lenovo', 'modelo' => 'IdeaCentre 5',
    'estatus_general' => 'En Mantenimiento',
    'usuario_asignado_nombre' => 'Pedro Ramírez',
    'usuario_asignado_cedula' => 'V-34567890',
    'usuario_asignado_cargo' => 'Jefe de Administración',
]);
if (!$equipo4->componentes()->where('componente_id', $cpu3->id)->exists()) {
    DB::beginTransaction();
    $equipo4->componentes()->attach($cpu3->id, ['fecha_instalacion' => now(), 'activo' => true]); $cpu3->update(['estatus' => 'Instalado']);
    DB::commit();
    echo "   - CONAPDIS-PC-003: CPU i5 (En Revisión) - EN MANTENIMIENTO\n";
}

// --- EQUIPOS SEDE CARABOBO ---
$deptoTecCarabobo = Departamento::where('nombre_departamento', 'Dirección de Tecnología')->where('sede_id', $sedeCarabobo->id)->first();
$deptoRrhhCarabobo = Departamento::where('nombre_departamento', 'Recursos Humanos')->where('sede_id', $sedeCarabobo->id)->first();

// PC-004 - Carabobo - Tecnología
$equipo5 = Equipo::firstOrCreate(['codigo_inventario_institucional' => 'CONAPDIS-PC-004'], [
    'serial_chasis' => 'CHS-ASUS-ROG-001',
    'tipo_equipo_id' => $tipoPC->id,
    'departamento_id' => $deptoTecCarabobo->id,
    'marca' => 'ASUS', 'modelo' => 'ROG Strix G16',
    'estatus_general' => 'Operativo',
    'usuario_asignado_nombre' => 'Ana López',
    'usuario_asignado_cedula' => 'V-45678901',
    'usuario_asignado_cargo' => 'Analista de Sistemas',
]);
if (!$equipo5->componentes()->where('componente_id', $cpu4->id)->exists()) {
    DB::beginTransaction();
    $equipo5->componentes()->attach($cpu4->id, ['fecha_instalacion' => now(), 'activo' => true]); $cpu4->update(['estatus' => 'Instalado']);
    $equipo5->componentes()->attach($ram3->id, ['fecha_instalacion' => now(), 'activo' => true]); $ram3->update(['estatus' => 'Instalado']);
    $equipo5->componentes()->attach($ssd3->id, ['fecha_instalacion' => now(), 'activo' => true]); $ssd3->update(['estatus' => 'Instalado']);
    DB::commit();
    echo "   - CONAPDIS-PC-004: Ryzen 9 + 32GB RAM + 1TB SSD\n";
}

// LAP-001 - Carabobo - RRHH (OPERATIVO)
$equipo6 = Equipo::firstOrCreate(['codigo_inventario_institucional' => 'CONAPDIS-LAP-001'], [
    'serial_chasis' => 'CHS-LEN-THINKPAD-001',
    'tipo_equipo_id' => $tipoLaptop->id,
    'departamento_id' => $deptoRrhhCarabobo->id,
    'marca' => 'Lenovo', 'modelo' => 'ThinkPad X1 Carbon',
    'estatus_general' => 'Operativo',
    'usuario_asignado_nombre' => 'Laura Martínez',
    'usuario_asignado_cedula' => 'V-56789012',
    'usuario_asignado_cargo' => 'Coordinadora de RRHH',
]);
if (!$equipo6->componentes()->where('componente_id', $cpu5->id)->exists()) {
    DB::beginTransaction();
    $equipo6->componentes()->attach($cpu5->id, ['fecha_instalacion' => now(), 'activo' => true]); $cpu5->update(['estatus' => 'Instalado']);
    $equipo6->componentes()->attach($ssd4->id, ['fecha_instalacion' => now(), 'activo' => true]); $ssd4->update(['estatus' => 'Instalado']);
    DB::commit();
    echo "   - CONAPDIS-LAP-001: Ryzen 7 7800X3D + 2TB SSD PCIe 5.0\n";
}

// PC-005 - Carabobo - Tecnología (EN MANTENIMIENTO - RAM en revisión)
$equipo7 = Equipo::firstOrCreate(['codigo_inventario_institucional' => 'CONAPDIS-PC-005'], [
    'serial_chasis' => 'CHS-DELL-VOSTRO-001',
    'tipo_equipo_id' => $tipoPC->id,
    'departamento_id' => $deptoTecCarabobo->id,
    'marca' => 'Dell', 'modelo' => 'Vostro 3910',
    'estatus_general' => 'En Mantenimiento',
    'usuario_asignado_nombre' => 'José Herrera',
    'usuario_asignado_cedula' => 'V-67890123',
    'usuario_asignado_cargo' => 'Soporte Técnico',
]);
if (!$equipo7->componentes()->where('componente_id', $ram4->id)->exists()) {
    DB::beginTransaction();
    $equipo7->componentes()->attach($ram4->id, ['fecha_instalacion' => now(), 'activo' => true]); $ram4->update(['estatus' => 'En Revisión']);
    DB::commit();
    echo "   - CONAPDIS-PC-005: RAM en revisión - EN MANTENIMIENTO\n";
}

// --- EQUIPOS SEDE ZULIA ---
$deptoAdminZulia = Departamento::where('nombre_departamento', 'Administración')->where('sede_id', $sedeZulia->id)->first();
$deptoAtencionZulia = Departamento::where('nombre_departamento', 'Atención al Ciudadano')->where('sede_id', $sedeZulia->id)->first();

// PC-006 - Zulia - Administración
$equipo8 = Equipo::firstOrCreate(['codigo_inventario_institucional' => 'CONAPDIS-PC-006'], [
    'serial_chasis' => 'CHS-HP-PAVILION-001',
    'tipo_equipo_id' => $tipoPC->id,
    'departamento_id' => $deptoAdminZulia->id,
    'marca' => 'HP', 'modelo' => 'Pavilion TP01',
    'estatus_general' => 'Operativo',
    'usuario_asignado_nombre' => 'Rosa Díaz',
    'usuario_asignado_cedula' => 'V-78901234',
    'usuario_asignado_cargo' => 'Administradora',
]);
if (!$equipo8->componentes()->where('componente_id', $cpu6->id)->exists()) {
    DB::beginTransaction();
    $equipo8->componentes()->attach($cpu6->id, ['fecha_instalacion' => now(), 'activo' => true]); $cpu6->update(['estatus' => 'Instalado']);
    $equipo8->componentes()->attach($ram5->id, ['fecha_instalacion' => now(), 'activo' => true]); $ram5->update(['estatus' => 'Instalado']);
    $equipo8->componentes()->attach($ssd5->id, ['fecha_instalacion' => now(), 'activo' => true]); $ssd5->update(['estatus' => 'Instalado']);
    $equipo8->componentes()->attach($fuente1->id, ['fecha_instalacion' => now(), 'activo' => true]); $fuente1->update(['estatus' => 'Instalado']);
    DB::commit();
    echo "   - CONAPDIS-PC-006: i3 + 16GB DDR4 + 1TB SSD + 850W PSU\n";
}

// IMP-001 - Zulia - Atención al Ciudadano
$equipo9 = Equipo::firstOrCreate(['codigo_inventario_institucional' => 'CONAPDIS-IMP-001'], [
    'serial_chasis' => 'CHS-HP-LASERJET-001',
    'tipo_equipo_id' => $tipoImpresora->id,
    'departamento_id' => $deptoAtencionZulia->id,
    'marca' => 'HP', 'modelo' => 'LaserJet Pro M404dn',
    'estatus_general' => 'Operativo',
    'usuario_asignado_nombre' => 'Oficina Atención',
    'usuario_asignado_cedula' => 'N/A',
    'usuario_asignado_cargo' => 'Uso Compartido',
]);
echo "   - CONAPDIS-IMP-001: Impresora HP LaserJet\n";

// PC-007 - Zulia - Atención al Ciudadano (INOPERATIVO)
$equipo10 = Equipo::firstOrCreate(['codigo_inventario_institucional' => 'CONAPDIS-PC-007'], [
    'serial_chasis' => 'CHS-OLD-DELL-001',
    'tipo_equipo_id' => $tipoPC->id,
    'departamento_id' => $deptoAtencionZulia->id,
    'marca' => 'Dell', 'modelo' => 'OptiPlex 3020',
    'estatus_general' => 'Inoperativo',
    'usuario_asignado_nombre' => 'Sin asignar',
    'usuario_asignado_cedula' => 'N/A',
    'usuario_asignado_cargo' => 'Equipo de respaldo',
]);
if (!$equipo10->componentes()->where('componente_id', $ssd6->id)->exists()) {
    DB::beginTransaction();
    $equipo10->componentes()->attach($ssd6->id, ['fecha_instalacion' => now()->subMonths(3), 'activo' => false, 'fecha_desinstalacion' => now()]); 
    // El SSD queda Disponible porque se desinstaló
    DB::commit();
    echo "   - CONAPDIS-PC-007: INOPERATIVO (SSD removido)\n";
}

// --- EQUIPOS SEDE LARA ---
$deptoTecLara = Departamento::where('nombre_departamento', 'Dirección de Tecnología')->where('sede_id', $sedeLara->id)->first();

// PC-008 - Lara - Tecnología
$equipo11 = Equipo::firstOrCreate(['codigo_inventario_institucional' => 'CONAPDIS-PC-008'], [
    'serial_chasis' => 'CHS-ACER-ASPIRE-001',
    'tipo_equipo_id' => $tipoPC->id,
    'departamento_id' => $deptoTecLara->id,
    'marca' => 'Acer', 'modelo' => 'Aspire TC-1780',
    'estatus_general' => 'Operativo',
    'usuario_asignado_nombre' => 'Fernando Rojas',
    'usuario_asignado_cedula' => 'V-89012345',
    'usuario_asignado_cargo' => 'Técnico de Soporte',
]);
if (!$equipo11->componentes()->where('componente_id', $cpu7->id)->exists()) {
    DB::beginTransaction();
    $equipo11->componentes()->attach($cpu7->id, ['fecha_instalacion' => now(), 'activo' => true]); $cpu7->update(['estatus' => 'Instalado']);
    $equipo11->componentes()->attach($ram7->id, ['fecha_instalacion' => now(), 'activo' => true]); $ram7->update(['estatus' => 'Instalado']);
    $equipo11->componentes()->attach($periferico1->id, ['fecha_instalacion' => now(), 'activo' => true]); $periferico1->update(['estatus' => 'Instalado']);
    $equipo11->componentes()->attach($periferico2->id, ['fecha_instalacion' => now(), 'activo' => true]); $periferico2->update(['estatus' => 'Instalado']);
    DB::commit();
    echo "   - CONAPDIS-PC-008: Ryzen 5 + 16GB DDR5 + Teclado/Mouse MX\n";
}

echo "\n✅ DATOS DE PRUEBA CREADOS EXITOSAMENTE\n";
echo "========================================\n";
echo "Credenciales:\n";
echo "  Admin:      admin@conapdis.gob.ve / Conapdis2024!\n";
echo "  Coordinador: coordinador@conapdis.gob.ve / Conapdis2024!\n";
echo "  Técnico:    tecnico@conapdis.gob.ve / Conapdis2024!\n";
echo "  Auditor:    auditor@conapdis.gob.ve / Conapdis2024!\n";
echo "========================================\n";