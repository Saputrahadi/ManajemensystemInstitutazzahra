<div>
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-neutral-800">Bimbingan Akademik</h1>
            <p class="mt-1 text-sm text-neutral-500">{{ $student->nim }} — {{ $student->user->name }}</p>
        </div>
        <a href="{{ route('dosen.wali.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">&larr; Mahasiswa Bimbingan</a>
    </div>

    @if (session('status'))
        <div class="mt-4 rounded-lg bg-brand-50 px-4 py-2.5 text-sm text-brand-700">{{ session('status') }}</div>
    @endif

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-xl border border-neutral-200 bg-white p-6">
            <h2 class="text-sm font-semibold text-neutral-800">Catatan Baru</h2>
            <form wire:submit="save" class="mt-3 space-y-3">
                <div>
                    <label class="block text-xs font-medium text-neutral-700">Tanggal</label>
                    <input type="date" wire:model="advisingDate" class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm">
                    @error('advisingDate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-neutral-700">Topik</label>
                    <input type="text" wire:model="topic" class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm">
                    @error('topic') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-neutral-700">Catatan</label>
                    <textarea wire:model="notes" rows="4" class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm"></textarea>
                </div>
                <button type="submit" class="w-full rounded-lg bg-brand-600 px-3 py-2 text-sm font-medium text-white hover:bg-brand-700">Simpan</button>
            </form>
        </div>

        <div class="lg:col-span-2 rounded-xl border border-neutral-200 bg-white p-6">
            <h2 class="text-sm font-semibold text-neutral-800">Riwayat Bimbingan</h2>
            <ul class="mt-3 divide-y divide-neutral-100">
                @forelse ($history as $entry)
                    <li class="py-3 text-sm">
                        <div class="flex items-center justify-between">
                            <p class="font-medium text-neutral-700">{{ $entry->topic }}</p>
                            <span class="text-xs text-neutral-400">{{ $entry->advising_date->format('d M Y') }}</span>
                        </div>
                        @if ($entry->notes)
                            <p class="mt-1 text-xs text-neutral-500">{{ $entry->notes }}</p>
                        @endif
                    </li>
                @empty
                    <li class="py-3 text-sm text-neutral-500">Belum ada catatan bimbingan.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
