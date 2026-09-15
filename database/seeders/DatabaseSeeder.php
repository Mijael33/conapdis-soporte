<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            EstructuraOrganizacionalSeeder::class,
            RolePermissionSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}