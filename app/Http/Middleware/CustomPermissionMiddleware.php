<?php

namespace App\Http\Middleware;

use Closure;
use Spatie\Permission\Middleware\PermissionMiddleware as SpatiePermissionMiddleware;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CustomPermissionMiddleware
{
    public function handle($request, Closure $next, $permission, $guard = null)
    {
        try {
            return (new SpatiePermissionMiddleware())->handle($request, $next, $permission, $guard);
        } catch (UnauthorizedException $exception) {
            $permissions = $exception->getRequiredPermissions();

            if (! empty($permissions)) {
                throw new HttpException(403, 'Akses tidak sesuai. Hanya role dengan permission ' . implode(', ', $permissions) . ' yang bisa mengakses halaman ini.');
            }

            throw new HttpException(403, 'Akses tidak sesuai. Anda harus masuk dengan izin yang sesuai.');
        }
    }
}
