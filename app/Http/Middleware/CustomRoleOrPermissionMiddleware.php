<?php

namespace App\Http\Middleware;

use Closure;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware as SpatieRoleOrPermissionMiddleware;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CustomRoleOrPermissionMiddleware
{
    public function handle($request, Closure $next, $rolesOrPermissions, $guard = null)
    {
        try {
            return (new SpatieRoleOrPermissionMiddleware())->handle($request, $next, $rolesOrPermissions, $guard);
        } catch (UnauthorizedException $exception) {
            $values = $exception->getRequiredPermissions();

            if (! empty($values)) {
                throw new HttpException(403, 'Akses tidak sesuai. Hanya role atau izin berikut yang bisa mengakses halaman ini: ' . implode(', ', $values) . '.');
            }

            throw new HttpException(403, 'Akses tidak sesuai. Anda harus masuk dengan role atau izin yang sesuai.');
        }
    }
}
