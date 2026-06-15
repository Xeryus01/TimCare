<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Define permissions
        $perms = [
            'view tickets',
            'create tickets',
            'update tickets',
            'assign tickets',
            'comment tickets',
            'view assets',
            'manage assets',
            'view reservations',
            'create reservations',
            'manage reservations',
            'view dashboard',
            'view maintenance',
            'manage maintenance',
        ];

        foreach ($perms as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Roles
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->syncPermissions($perms);

        $tech = Role::firstOrCreate(['name' => 'Teknisi']);
        $tech->syncPermissions([
            'view tickets',
            'update tickets',
            'assign tickets',
            'comment tickets',
            'view assets',
            'manage assets',
            'view dashboard',
            'view maintenance',
        ]);

        $ulp = Role::firstOrCreate(['name' => 'ULP']);
        $ulp->syncPermissions([
            'view tickets',
            'create tickets',
            'comment tickets',
            'view dashboard',
            'view assets',
            'view reservations',
            'create reservations',
            'view maintenance',
            'manage maintenance',
        ]);

        $user = Role::firstOrCreate(['name' => 'User']);
        $user->syncPermissions([
            'view tickets',
            'create tickets',
            'comment tickets',
            'view dashboard',
            'view assets',
            'view reservations',
            'create reservations',
        ]);
    }
}
