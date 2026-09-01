<?php

namespace App\Http\Controllers\Core\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user()->load('unit', 'roles');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'status' => $user->status,
                'unit' => $user->unit?->only(['id', 'code', 'name']),
                'roles' => $user->roles->map(fn ($r) => [
                    'code' => $r->code,
                    'name' => $r->name,
                    'unit_id' => $r->pivot->unit_id,
                ]),
            ],
        ]);
    }
}
