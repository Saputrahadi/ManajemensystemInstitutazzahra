<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Core\AuditLog;
use App\Services\Core\DashboardStatsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    public function __invoke(Request $request, DashboardStatsService $stats)
    {
        $user = $request->user()->load('unit', 'roles');

        $recentAuditLogs = Gate::allows('viewAny', AuditLog::class)
            ? AuditLog::with('user')->latest('created_at')->limit(8)->get()
            : collect();

        return view('core.dashboard', [
            'user' => $user,
            'stats' => $stats->forUser($user),
            'recentAuditLogs' => $recentAuditLogs,
        ]);
    }
}
