<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware RBAC — pasang di route/group lewat alias 'permission:<code>'
 * (didaftarkan di bootstrap/app.php). Least Privilege: default-deny, hanya
 * lolos jika user login DAN memiliki permission persis yang diminta.
 */
class EnsureUserHasPermission
{
    public function handle(Request $request, Closure $next, string $permissionCode): Response
    {
        $user = $request->user();

        abort_unless($user, 401, 'Belum terautentikasi.');
        abort_unless($user->hasPermission($permissionCode), 403, "Anda tidak memiliki wewenang: {$permissionCode}");

        return $next($request);
    }
}
