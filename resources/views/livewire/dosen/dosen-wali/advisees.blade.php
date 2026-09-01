<div>
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-neutral-800">Mahasiswa Bimbingan</h1>
        <a href="{{ route('dosen.dashboard') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">&larr; Dashboard</a>
    </div>

    <div class="mt-6 overflow-hidden rounded-xl border border-neutral-200 bg-white">
        <table class="min-w-full divide-y divide-neutral-100 text-sm">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-neutral-500">NIM</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-neutral-500">Nama</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-neutral-500">Prodi</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-neutral-500">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                @forelse ($advisees as $student)
                    <tr>
                        <td class="px-4 py-3 text-neutral-700">{{ $student->nim }}</td>
                        <td class="px-4 py-3 text-neutral-700">{{ $student->user->name }}</td>
                        <td class="px-4 py-3 text-neutral-500">{{ $student->studyProgram->name }}</td>
                        <td class="px-4 py-3 text-neutral-500">{{ $student->student_status }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('dosen.wali.show', $student) }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">Bimbingan &rarr;</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-sm text-neutral-500">Belum ada mahasiswa bimbingan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
