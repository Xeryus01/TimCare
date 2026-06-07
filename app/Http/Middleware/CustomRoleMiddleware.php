<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Middleware\RoleMiddleware as SpatieRoleMiddleware;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CustomRoleMiddleware
{
    public function handle($request, Closure $next, $role, $guard = null)
    {
        try {
            return (new SpatieRoleMiddleware())->handle($request, $next, $role, $guard);
        } catch (UnauthorizedException $exception) {
            $roles = $exception->getRequiredRoles();

            if (! empty($roles)) {
                throw new HttpException(403, 'Akses tidak sesuai. Hanya role ' . implode(', ', $roles) . ' yang bisa mengakses halaman ini.');
            }

            throw new HttpException(403, 'Akses tidak sesuai. Anda harus masuk dengan role yang sesuai.');
        }
    }
}
