<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Estado;
use App\Models\Sede;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $dc = Estado::where('nombre', 'Distrito Capital')->first();
        $sedeCentral = Sede::where('nombre_sede', 'Sede Central CONAPDIS')->first();

        User::firstOrCreate(
            ['email' => 'admin@conapdis.gob.ve'],
            [
                'name' => 'Administrador CONAPDIS',
                'password' => Hash::make('Conapdis2024!'),
                'email_verified_at' => now(),
                'estado_id' => $dc->id,
                'sede_id' => $sedeCentral->id,
            ]
        )->assignRole('Administrador');
    }
}