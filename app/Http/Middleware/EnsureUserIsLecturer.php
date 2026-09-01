<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate portal self-service Dosen (Prioritas 4) — beda dari middleware
 * `admin`/`permission:` (RBAC berbasis Permission untuk panel Filament),
 * ini cuma cek role `dosen` DAN punya baris `Lecturer` terpaut (akun bisa
 * saja diberi role dosen tapi datanya belum lengkap).
 */
class EnsureUserIsLecturer
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless($user, 401);
        abort_unless($user->hasRole('dosen') && $user->lecturer, 403, 'Halaman ini khusus untuk akun Dosen.');

        return $next($request);
    }
}
