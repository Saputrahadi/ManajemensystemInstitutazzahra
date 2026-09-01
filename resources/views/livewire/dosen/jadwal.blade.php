<div>
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-neutral-800">Jadwal Mengajar</h1>
        <a href="{{ route('dosen.dashboard') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">&larr; Dashboard</a>
    </div>

    <div class="mt-6 space-y-4">
        @forelse ($days as $dayNumber => $dayName)
            @if ($schedulesByDay->has($dayNumber))
                <div class="rounded-xl border border-neutral-200 bg-white p-6">
                    <h2 class="text-sm font-semibold text-neutral-800">{{ $dayName }}</h2>
                    <ul class="mt-3 divide-y divide-neutral-100">
                        @foreach ($schedulesByDay[$dayNumber] as $schedule)
                            <li class="flex items-center justify-between gap-4 py-2.5 text-sm">
                                <div>
                                    <p class="text-neutral-700">{{ $schedule->courseClass->courseOffering->course->name }} — Kelas {{ $schedule->courseClass->code }}</p>
                                    <p class="text-xs text-neutral-400">{{ $schedule->room?->name ?? 'Tanpa ruang' }}</p>
                                </div>
                                <span class="shrink-0 rounded-full bg-brand-50 px-2 py-0.5 text-xs font-medium text-brand-700">
                                    {{ \Illuminate\Support\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Illuminate\Support\Carbon::parse($schedule->end_time)->format('H:i') }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @empty
        @endforelse

        @if ($schedulesByDay->isEmpty())
            <div class="rounded-xl border border-neutral-200 bg-white p-6">
                <p class="text-sm text-neutral-500">Belum ada jadwal mengajar.</p>
            </div>
        @endif
    </div>
</div>
