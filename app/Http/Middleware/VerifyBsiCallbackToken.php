<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Autentikasi callback masuk dari BSI lewat token statis di header
 * `X-Callback-Token` (bukan Sanctum — ini panggilan server-ke-server dari
 * bank, bukan sesi pengguna). Token dikonfigurasi lewat BSI_CALLBACK_TOKEN
 * di .env, dicocokkan lewat hash_equals untuk menghindari timing attack.
 */
class VerifyBsiCallbackToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = config('bsi.callback_token');
        $given = $request->header('X-Callback-Token', '');

        abort_if(empty($expected), 500, 'BSI_CALLBACK_TOKEN belum dikonfigurasi.');
        abort_unless(hash_equals($expected, $given), 401, 'Token callback tidak valid.');

        return $next($request);
    }
}
