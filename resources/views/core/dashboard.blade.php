<x-layouts.app :title="'Dashboard'">
    <h1 class="text-xl font-semibold text-neutral-800">Selamat datang, {{ $user->name }}</h1>
    <p class="mt-1 text-sm text-neutral-500">
        Unit: {{ $user->unit?->name ?? '—' }} ·
        Role: {{ $user->roles->pluck('name')->join(', ') ?: '—' }}
    </p>

    <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-xl border border-neutral-200 bg-white p-4">
            <p class="text-xs text-neutral-500">Total Pengguna</p>
            <p class="mt-1 text-2xl font-semibold text-brand-700">{{ $stats['total_users'] }}</p>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-4">
            <p class="text-xs text-neutral-500">Pengguna Aktif</p>
            <p class="mt-1 text-2xl font-semibold text-brand-700">{{ $stats['active_users'] }}</p>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-4">
            <p class="text-xs text-neutral-500">Unit Terlihat</p>
            <p class="mt-1 text-2xl font-semibold text-brand-700">{{ $stats['total_units'] }}</p>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-4">
            <p class="text-xs text-neutral-500">Total Role</p>
            <p class="mt-1 text-2xl font-semibold text-brand-700">{{ $stats['total_roles'] }}</p>
        </div>
    </div>

    @if ($recentAuditLogs->isNotEmpty())
        <div class="mt-6 rounded-xl border border-neutral-200 bg-white p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-neutral-800">Aktivitas Terbaru</h2>
                <a href="/admin/audit-logs" class="text-xs font-medium text-brand-600 hover:text-brand-700">Lihat semua →</a>
            </div>
            <ul class="mt-4 divide-y divide-neutral-100">
                @foreach ($recentAuditLogs as $log)
                    <li class="flex items-center justify-between gap-4 py-2.5 text-sm">
                        <div>
                            <p class="text-neutral-700">{{ $log->description }}</p>
                            <p class="text-xs text-neutral-400">{{ $log->user?->name ?? 'Sistem' }} · {{ $log->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="shrink-0 rounded-full bg-brand-50 px-2 py-0.5 text-xs font-medium text-brand-700">{{ $log->action }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-6 rounded-xl border border-neutral-200 bg-white p-6">
        <p class="text-sm text-neutral-600">
            Ini adalah dasbor Core System (Fase 0). Modul akademik, keuangan, dan lainnya
            akan tampil di sini bertahap sesuai Urutan Pembangunan pada Master Blueprint.
        </p>
        @can('viewAny', \App\Models\Core\AuditLog::class)
            <a href="/admin" class="mt-4 inline-block text-sm font-medium text-brand-600 hover:text-brand-700">
                Buka Panel Admin →
            </a>
        @endcan
        @if ($user->hasRole('dosen') && $user->lecturer)
            <a href="{{ route('dosen.dashboard') }}" class="mt-4 block text-sm font-medium text-brand-600 hover:text-brand-700">
                Buka Portal Dosen →
            </a>
        @endif
    </div>
</x-layouts.app>
