<div>
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-neutral-800">Kelas Saya</h1>
        <a href="{{ route('dosen.dashboard') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">&larr; Dashboard</a>
    </div>

    <div class="mt-6 overflow-hidden rounded-xl border border-neutral-200 bg-white">
        <table class="min-w-full divide-y divide-neutral-100 text-sm">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-neutral-500">Mata Kuliah</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-neutral-500">Kelas</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-neutral-500">Tahun Akademik</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-neutral-500">Peran</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                @forelse ($classes as $class)
                    <tr>
                        <td class="px-4 py-3 text-neutral-700">{{ $class->courseOffering->course->name }}</td>
                        <td class="px-4 py-3 text-neutral-700">{{ $class->code }}</td>
                        <td class="px-4 py-3 text-neutral-500">{{ $class->courseOffering->academic_year }} ({{ $class->courseOffering->term }})</td>
                        <td class="px-4 py-3 text-neutral-500">{{ $class->pivot->role }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('dosen.kelas.show', $class) }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">Kelola &rarr;</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-sm text-neutral-500">Belum ada kelas yang diampu.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
