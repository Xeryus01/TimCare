<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Teknisi']);
        Role::firstOrCreate(['name' => 'ULP']);
        Role::firstOrCreate(['name' => 'User']);
    }
}
