<?php

namespace App\Http\Controllers\Core\Api;

use App\Http\Controllers\Controller;
use App\Models\Core\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $units = Unit::query()
            ->when($request->boolean('active_only', true), fn ($q) => $q->where('is_active', true))
            ->orderBy('order_index')
            ->paginate($request->integer('per_page', 25));

        return response()->json([
            'success' => true,
            'data' => $units->items(),
            'meta' => [
                'current_page' => $units->currentPage(),
                'last_page' => $units->lastPage(),
                'total' => $units->total(),
            ],
        ]);
    }

    public function show(Unit $unit)
    {
        return response()->json([
            'success' => true,
            'data' => $unit->load('parent', 'children'),
        ]);
    }
}
