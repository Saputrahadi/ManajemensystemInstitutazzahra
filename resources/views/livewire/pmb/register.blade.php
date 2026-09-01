<div class="flex min-h-screen items-center justify-center px-4 py-10">
    @if ($registrationNumber)
        <div class="w-full max-w-md rounded-2xl border border-brand-100 bg-white p-8 text-center shadow-sm">
            <h1 class="text-lg font-semibold text-brand-700">Pendaftaran Berhasil</h1>
            <p class="mt-2 text-sm text-neutral-600">Simpan nomor pendaftaran Anda:</p>
            <p class="mt-3 rounded-lg bg-brand-50 px-4 py-3 font-mono text-xl font-semibold text-brand-700">
                {{ $registrationNumber }}
            </p>
            <p class="mt-4 text-sm text-neutral-500">
                Tahap berikutnya (verifikasi berkas & pembayaran) akan diinformasikan lewat kontak yang Anda daftarkan.
            </p>
        </div>
    @else
        <div class="w-full max-w-2xl rounded-2xl border border-brand-100 bg-white p-8 shadow-sm">
            <div class="mb-6 text-center">
                <h1 class="text-lg font-semibold text-brand-700">Az-Zahra Digital Campus</h1>
                <p class="mt-1 text-sm text-neutral-500">Formulir Pendaftaran Mahasiswa Baru</p>
            </div>

            <form wire:submit="register" class="space-y-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label for="admission_period_id" class="block text-sm font-medium text-neutral-700">Periode</label>
                        <select wire:model="admission_period_id" id="admission_period_id"
                            class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                            <option value="">Pilih periode</option>
                            @foreach ($periods as $period)
                                <option value="{{ $period->id }}">{{ $period->name }}</option>
                            @endforeach
                        </select>
                        @error('admission_period_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="admission_path_id" class="block text-sm font-medium text-neutral-700">Jalur</label>
                        <select wire:model="admission_path_id" id="admission_path_id"
                            class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                            <option value="">Pilih jalur</option>
                            @foreach ($paths as $path)
                                <option value="{{ $path->id }}">{{ $path->name }}</option>
                            @endforeach
                        </select>
                        @error('admission_path_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="study_program_id" class="block text-sm font-medium text-neutral-700">Program Studi</label>
                        <select wire:model="study_program_id" id="study_program_id"
                            class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                            <option value="">Pilih prodi</option>
                            @foreach ($studyPrograms as $program)
                                <option value="{{ $program->id }}">{{ $program->name }}</option>
                            @endforeach
                        </select>
                        @error('study_program_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="border-t border-neutral-100 pt-6">
                    <p class="mb-3 text-sm font-medium text-neutral-700">Data Pribadi</p>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="name" class="block text-sm font-medium text-neutral-700">Nama Lengkap</label>
                            <input wire:model="name" id="name" type="text"
                                class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="nik" class="block text-sm font-medium text-neutral-700">NIK</label>
                            <input wire:model="nik" id="nik" type="text"
                                class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                            @error('nik') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="gender" class="block text-sm font-medium text-neutral-700">Jenis Kelamin</label>
                            <select wire:model="gender" id="gender"
                                class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                                <option value="">Pilih</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            @error('gender') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="birth_place" class="block text-sm font-medium text-neutral-700">Tempat Lahir</label>
                            <input wire:model="birth_place" id="birth_place" type="text"
                                class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                            @error('birth_place') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="birth_date" class="block text-sm font-medium text-neutral-700">Tanggal Lahir</label>
                            <input wire:model="birth_date" id="birth_date" type="date"
                                class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                            @error('birth_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="address" class="block text-sm font-medium text-neutral-700">Alamat</label>
                            <textarea wire:model="address" id="address" rows="2"
                                class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500"></textarea>
                            @error('address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="border-t border-neutral-100 pt-6">
                    <p class="mb-3 text-sm font-medium text-neutral-700">Kontak & Orang Tua</p>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-neutral-700">No. HP</label>
                            <input wire:model="phone" id="phone" type="text"
                                class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                            @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-neutral-700">Email</label>
                            <input wire:model="email" id="email" type="email"
                                class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="parent_name" class="block text-sm font-medium text-neutral-700">Nama Orang Tua/Wali</label>
                            <input wire:model="parent_name" id="parent_name" type="text"
                                class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                            @error('parent_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="parent_phone" class="block text-sm font-medium text-neutral-700">No. HP Orang Tua/Wali</label>
                            <input wire:model="parent_phone" id="parent_phone" type="text"
                                class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                            @error('parent_phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <button type="submit"
                    class="w-full rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-700"
                    wire:loading.attr="disabled" wire:target="register">
                    <span wire:loading.remove wire:target="register">Daftar</span>
                    <span wire:loading wire:target="register">Memproses…</span>
                </button>
            </form>
        </div>
    @endif
</div>
