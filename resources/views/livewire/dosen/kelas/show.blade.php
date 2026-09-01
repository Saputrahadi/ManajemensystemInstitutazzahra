<div>
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-neutral-800">{{ $class->courseOffering->course->name }} — Kelas {{ $class->code }}</h1>
            <p class="mt-1 text-sm text-neutral-500">{{ $class->courseOffering->academic_year }} ({{ $class->courseOffering->term }})</p>
        </div>
        <a href="{{ route('dosen.kelas.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">&larr; Kelas Saya</a>
    </div>

    @if (session('status'))
        <div class="mt-4 rounded-lg bg-brand-50 px-4 py-2.5 text-sm text-brand-700">{{ session('status') }}</div>
    @endif

    <div class="mt-6 flex gap-1 border-b border-neutral-200">
        @foreach (['presensi' => 'Presensi', 'materi' => 'Materi', 'tugas' => 'Tugas', 'nilai' => 'Nilai'] as $tab => $label)
            <button wire:click="$set('activeTab', '{{ $tab }}')"
                class="px-4 py-2 text-sm font-medium {{ $activeTab === $tab ? 'border-b-2 border-brand-600 text-brand-700' : 'text-neutral-500 hover:text-brand-600' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- PRESENSI --}}
    @if ($activeTab === 'presensi')
        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-xl border border-neutral-200 bg-white p-6">
                <h2 class="text-sm font-semibold text-neutral-800">Pertemuan</h2>
                <form wire:submit="createSession" class="mt-3 space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-neutral-700">Pertemuan Ke-</label>
                        <input type="number" wire:model="meetingNumber" class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-neutral-700">Topik</label>
                        <input type="text" wire:model="topic" class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-neutral-700">Tanggal</label>
                        <input type="date" wire:model="sessionDate" class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-brand-600 px-3 py-2 text-sm font-medium text-white hover:bg-brand-700">Buat Pertemuan</button>
                </form>

                <ul class="mt-4 divide-y divide-neutral-100">
                    @foreach ($sessions as $session)
                        <li>
                            <button wire:click="selectSession({{ $session->id }})"
                                class="w-full py-2 text-left text-sm {{ $selectedSessionId === $session->id ? 'font-semibold text-brand-700' : 'text-neutral-600' }}">
                                Pertemuan {{ $session->meeting_number }} — {{ $session->session_date->format('d M Y') }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="lg:col-span-2 rounded-xl border border-neutral-200 bg-white p-6">
                <h2 class="text-sm font-semibold text-neutral-800">Presensi Mahasiswa</h2>
                @if (! $selectedSessionId)
                    <p class="mt-3 text-sm text-neutral-500">Pilih atau buat pertemuan dulu.</p>
                @else
                    <div class="mt-3 space-y-2">
                        @foreach ($this->roster as $student)
                            <div class="flex items-center justify-between gap-4 border-b border-neutral-100 py-2 text-sm">
                                <span class="text-neutral-700">{{ $student->nim }} — {{ $student->user->name }}</span>
                                <select wire:model="attendanceStatuses.{{ $student->id }}" class="rounded-lg border border-neutral-300 px-2 py-1 text-sm">
                                    <option value="hadir">Hadir</option>
                                    <option value="izin">Izin</option>
                                    <option value="sakit">Sakit</option>
                                    <option value="alpa">Alpa</option>
                                </select>
                            </div>
                        @endforeach
                        @if ($this->roster->isEmpty())
                            <p class="text-sm text-neutral-500">Belum ada mahasiswa yang KRS kelas ini.</p>
                        @else
                            <button wire:click="saveAttendance" class="mt-3 rounded-lg bg-brand-600 px-3 py-2 text-sm font-medium text-white hover:bg-brand-700">Simpan Presensi</button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- MATERI --}}
    @if ($activeTab === 'materi')
        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-xl border border-neutral-200 bg-white p-6">
                <h2 class="text-sm font-semibold text-neutral-800">Unggah Materi</h2>
                <form wire:submit="addMaterial" class="mt-3 space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-neutral-700">Pertemuan</label>
                        <select wire:model="materialSessionId" class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm">
                            <option value="">Pilih pertemuan</option>
                            @foreach ($sessions as $session)
                                <option value="{{ $session->id }}">Pertemuan {{ $session->meeting_number }}</option>
                            @endforeach
                        </select>
                        @error('materialSessionId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-neutral-700">Judul</label>
                        <input type="text" wire:model="materialTitle" class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm">
                        @error('materialTitle') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-neutral-700">Deskripsi</label>
                        <textarea wire:model="materialDescription" rows="2" class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-neutral-700">File (opsional)</label>
                        <input type="file" wire:model="materialFile" class="mt-1 block w-full text-sm">
                        @error('materialFile') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-brand-600 px-3 py-2 text-sm font-medium text-white hover:bg-brand-700">Tambah Materi</button>
                </form>
            </div>

            <div class="lg:col-span-2 rounded-xl border border-neutral-200 bg-white p-6">
                <h2 class="text-sm font-semibold text-neutral-800">Materi Diunggah</h2>
                <ul class="mt-3 divide-y divide-neutral-100">
                    @forelse ($materials as $material)
                        <li class="py-2.5 text-sm">
                            <p class="text-neutral-700">{{ $material->title }} <span class="text-xs text-neutral-400">(Pertemuan {{ $material->classSession->meeting_number }})</span></p>
                            <p class="text-xs text-neutral-500">{{ $material->description }}</p>
                        </li>
                    @empty
                        <li class="py-2.5 text-sm text-neutral-500">Belum ada materi.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    @endif

    {{-- TUGAS --}}
    @if ($activeTab === 'tugas')
        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-xl border border-neutral-200 bg-white p-6">
                <h2 class="text-sm font-semibold text-neutral-800">Buat Tugas</h2>
                <form wire:submit="addAssignment" class="mt-3 space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-neutral-700">Judul</label>
                        <input type="text" wire:model="assignmentTitle" class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm">
                        @error('assignmentTitle') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-neutral-700">Deskripsi</label>
                        <textarea wire:model="assignmentDescription" rows="2" class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-neutral-700">Tenggat</label>
                        <input type="datetime-local" wire:model="assignmentDueDate" class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-neutral-700">Skor Maksimal</label>
                        <input type="number" wire:model="maxScore" class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm">
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-brand-600 px-3 py-2 text-sm font-medium text-white hover:bg-brand-700">Buat Tugas</button>
                </form>

                <ul class="mt-4 divide-y divide-neutral-100">
                    @foreach ($assignments as $assignment)
                        <li>
                            <button wire:click="selectAssignment({{ $assignment->id }})"
                                class="w-full py-2 text-left text-sm {{ $selectedAssignmentId === $assignment->id ? 'font-semibold text-brand-700' : 'text-neutral-600' }}">
                                {{ $assignment->title }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="lg:col-span-2 rounded-xl border border-neutral-200 bg-white p-6">
                <h2 class="text-sm font-semibold text-neutral-800">Nilai Pengumpulan</h2>
                @if (! $selectedAssignmentId)
                    <p class="mt-3 text-sm text-neutral-500">Pilih tugas dulu.</p>
                @else
                    <div class="mt-3 space-y-2">
                        @foreach ($this->roster as $student)
                            <div class="flex items-center gap-3 border-b border-neutral-100 py-2 text-sm">
                                <span class="w-40 shrink-0 text-neutral-700">{{ $student->nim }}</span>
                                <input type="number" wire:model="submissionScores.{{ $student->id }}" placeholder="Skor" class="w-24 rounded-lg border border-neutral-300 px-2 py-1 text-sm">
                                <input type="text" wire:model="submissionFeedback.{{ $student->id }}" placeholder="Umpan balik" class="flex-1 rounded-lg border border-neutral-300 px-2 py-1 text-sm">
                                <button wire:click="saveSubmissionGrade({{ $student->id }})" class="rounded-lg bg-brand-600 px-3 py-1 text-xs font-medium text-white hover:bg-brand-700">Simpan</button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- NILAI --}}
    @if ($activeTab === 'nilai')
        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-xl border border-neutral-200 bg-white p-6">
                <h2 class="text-sm font-semibold text-neutral-800">Komponen Nilai</h2>
                <form wire:submit="addComponent" class="mt-3 space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-neutral-700">Nama Komponen</label>
                        <input type="text" wire:model="componentName" placeholder="mis. Tugas, UTS, UAS" class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm">
                        @error('componentName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-neutral-700">Bobot (%)</label>
                        <input type="number" wire:model="componentWeight" class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm">
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-brand-600 px-3 py-2 text-sm font-medium text-white hover:bg-brand-700">Tambah Komponen</button>
                </form>

                <ul class="mt-4 divide-y divide-neutral-100">
                    @foreach ($gradeComponents as $component)
                        <li>
                            <button wire:click="selectComponent({{ $component->id }})"
                                class="w-full py-2 text-left text-sm {{ $selectedComponentId === $component->id ? 'font-semibold text-brand-700' : 'text-neutral-600' }}">
                                {{ $component->name }} ({{ $component->weight }}%)
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="lg:col-span-2 rounded-xl border border-neutral-200 bg-white p-6">
                <h2 class="text-sm font-semibold text-neutral-800">Input Nilai</h2>
                @if (! $selectedComponentId)
                    <p class="mt-3 text-sm text-neutral-500">Pilih komponen nilai dulu.</p>
                @else
                    <div class="mt-3 space-y-2">
                        @foreach ($this->roster as $student)
                            <div class="flex items-center gap-3 border-b border-neutral-100 py-2 text-sm">
                                <span class="w-40 shrink-0 text-neutral-700">{{ $student->nim }} — {{ $student->user->name }}</span>
                                <input type="number" wire:model="gradeScores.{{ $student->id }}" placeholder="Skor" class="w-24 rounded-lg border border-neutral-300 px-2 py-1 text-sm">
                            </div>
                        @endforeach
                        @if ($this->roster->isNotEmpty())
                            <button wire:click="saveGrades" class="mt-3 rounded-lg bg-brand-600 px-3 py-2 text-sm font-medium text-white hover:bg-brand-700">Simpan Nilai</button>
                        @endif
                    </div>
                @endif

                <h2 class="mt-6 text-sm font-semibold text-neutral-800">Nilai Akhir</h2>
                <div class="mt-3 space-y-2">
                    @foreach ($this->roster as $student)
                        <div class="flex items-center justify-between gap-3 border-b border-neutral-100 py-2 text-sm">
                            <span class="text-neutral-700">{{ $student->nim }} — {{ $student->user->name }}</span>
                            <div class="flex items-center gap-3">
                                <span class="text-neutral-500">{{ $finalizations[$student->id] ?? '—' }}</span>
                                <button wire:click="finalizeGrade({{ $student->id }})" class="rounded-lg border border-brand-600 px-3 py-1 text-xs font-medium text-brand-600 hover:bg-brand-50">Finalisasi</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
