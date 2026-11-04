<?php

namespace App\Providers;

use App\Models\Reservation;
use App\Models\Ticket;
use App\Observers\ReservationObserver;
use App\Observers\TicketObserver;
use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\URL;
use Throwable;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Ticket::observe(TicketObserver::class);
        Reservation::observe(ReservationObserver::class);

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
        
        Event::listen(MigrationsEnded::class, function () {
            $this->syncDefaultRolesAndPermissions();
        });

        $this->syncDefaultRolesAndPermissions();
    }

    private function syncDefaultRolesAndPermissions(): void
    {
        try {
            if (! Schema::hasTable('roles')) {
                return;
            }
        } catch (Throwable $e) {
            return;
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $roles = [];

        foreach (['Admin', 'Teknisi', 'User'] as $roleName) {
            $roles[$roleName] = Role::findOrCreate($roleName, 'web');
        }

        if (! Schema::hasTable('permissions') || ! Schema::hasTable('role_has_permissions')) {
            return;
        }

        $permissions = [
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
            'approve reservations',
            'view dashboard',
        ];

        foreach ($permissions as $permissionName) {
            Permission::findOrCreate($permissionName, 'web');
        }

        $roles['Admin']->syncPermissions($permissions);
        $roles['Teknisi']->syncPermissions([
            'view tickets',
            'update tickets',
            'comment tickets',
            'view assets',
            'manage assets',
            'view reservations',
            'approve reservations',
            'view dashboard',
        ]);
        $roles['User']->syncPermissions([
            'view tickets',
            'create tickets',
            'comment tickets',
            'view assets',
            'view reservations',
            'create reservations',
            'view dashboard',
        ]);
    }
}
