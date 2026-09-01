<div>
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-neutral-800"><?php echo e($class->courseOffering->course->name); ?> — Kelas <?php echo e($class->code); ?></h1>
            <p class="mt-1 text-sm text-neutral-500"><?php echo e($class->courseOffering->academic_year); ?> (<?php echo e($class->courseOffering->term); ?>)</p>
        </div>
        <a href="<?php echo e(route('dosen.kelas.index')); ?>" class="text-sm font-medium text-brand-600 hover:text-brand-700">&larr; Kelas Saya</a>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status')): ?>
        <div class="mt-4 rounded-lg bg-brand-50 px-4 py-2.5 text-sm text-brand-700"><?php echo e(session('status')); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="mt-6 flex gap-1 border-b border-neutral-200">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['presensi' => 'Presensi', 'materi' => 'Materi', 'tugas' => 'Tugas', 'nilai' => 'Nilai']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tab => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <button wire:click="$set('activeTab', '<?php echo e($tab); ?>')"
                class="px-4 py-2 text-sm font-medium <?php echo e($activeTab === $tab ? 'border-b-2 border-brand-600 text-brand-700' : 'text-neutral-500 hover:text-brand-600'); ?>">
                <?php echo e($label); ?>

            </button>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'presensi'): ?>
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
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <button wire:click="selectSession(<?php echo e($session->id); ?>)"
                                class="w-full py-2 text-left text-sm <?php echo e($selectedSessionId === $session->id ? 'font-semibold text-brand-700' : 'text-neutral-600'); ?>">
                                Pertemuan <?php echo e($session->meeting_number); ?> — <?php echo e($session->session_date->format('d M Y')); ?>

                            </button>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </ul>
            </div>

            <div class="lg:col-span-2 rounded-xl border border-neutral-200 bg-white p-6">
                <h2 class="text-sm font-semibold text-neutral-800">Presensi Mahasiswa</h2>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $selectedSessionId): ?>
                    <p class="mt-3 text-sm text-neutral-500">Pilih atau buat pertemuan dulu.</p>
                <?php else: ?>
                    <div class="mt-3 space-y-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->roster; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center justify-between gap-4 border-b border-neutral-100 py-2 text-sm">
                                <span class="text-neutral-700"><?php echo e($student->nim); ?> — <?php echo e($student->user->name); ?></span>
                                <select wire:model="attendanceStatuses.<?php echo e($student->id); ?>" class="rounded-lg border border-neutral-300 px-2 py-1 text-sm">
                                    <option value="hadir">Hadir</option>
                                    <option value="izin">Izin</option>
                                    <option value="sakit">Sakit</option>
                                    <option value="alpa">Alpa</option>
                                </select>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->roster->isEmpty()): ?>
                            <p class="text-sm text-neutral-500">Belum ada mahasiswa yang KRS kelas ini.</p>
                        <?php else: ?>
                            <button wire:click="saveAttendance" class="mt-3 rounded-lg bg-brand-600 px-3 py-2 text-sm font-medium text-white hover:bg-brand-700">Simpan Presensi</button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'materi'): ?>
        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-xl border border-neutral-200 bg-white p-6">
                <h2 class="text-sm font-semibold text-neutral-800">Unggah Materi</h2>
                <form wire:submit="addMaterial" class="mt-3 space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-neutral-700">Pertemuan</label>
                        <select wire:model="materialSessionId" class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm">
                            <option value="">Pilih pertemuan</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($session->id); ?>">Pertemuan <?php echo e($session->meeting_number); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['materialSessionId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-neutral-700">Judul</label>
                        <input type="text" wire:model="materialTitle" class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['materialTitle'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-neutral-700">Deskripsi</label>
                        <textarea wire:model="materialDescription" rows="2" class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-neutral-700">File (opsional)</label>
                        <input type="file" wire:model="materialFile" class="mt-1 block w-full text-sm">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['materialFile'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-brand-600 px-3 py-2 text-sm font-medium text-white hover:bg-brand-700">Tambah Materi</button>
                </form>
            </div>

            <div class="lg:col-span-2 rounded-xl border border-neutral-200 bg-white p-6">
                <h2 class="text-sm font-semibold text-neutral-800">Materi Diunggah</h2>
                <ul class="mt-3 divide-y divide-neutral-100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li class="py-2.5 text-sm">
                            <p class="text-neutral-700"><?php echo e($material->title); ?> <span class="text-xs text-neutral-400">(Pertemuan <?php echo e($material->classSession->meeting_number); ?>)</span></p>
                            <p class="text-xs text-neutral-500"><?php echo e($material->description); ?></p>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="py-2.5 text-sm text-neutral-500">Belum ada materi.</li>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'tugas'): ?>
        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-xl border border-neutral-200 bg-white p-6">
                <h2 class="text-sm font-semibold text-neutral-800">Buat Tugas</h2>
                <form wire:submit="addAssignment" class="mt-3 space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-neutral-700">Judul</label>
                        <input type="text" wire:model="assignmentTitle" class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['assignmentTitle'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <button wire:click="selectAssignment(<?php echo e($assignment->id); ?>)"
                                class="w-full py-2 text-left text-sm <?php echo e($selectedAssignmentId === $assignment->id ? 'font-semibold text-brand-700' : 'text-neutral-600'); ?>">
                                <?php echo e($assignment->title); ?>

                            </button>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </ul>
            </div>

            <div class="lg:col-span-2 rounded-xl border border-neutral-200 bg-white p-6">
                <h2 class="text-sm font-semibold text-neutral-800">Nilai Pengumpulan</h2>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $selectedAssignmentId): ?>
                    <p class="mt-3 text-sm text-neutral-500">Pilih tugas dulu.</p>
                <?php else: ?>
                    <div class="mt-3 space-y-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->roster; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center gap-3 border-b border-neutral-100 py-2 text-sm">
                                <span class="w-40 shrink-0 text-neutral-700"><?php echo e($student->nim); ?></span>
                                <input type="number" wire:model="submissionScores.<?php echo e($student->id); ?>" placeholder="Skor" class="w-24 rounded-lg border border-neutral-300 px-2 py-1 text-sm">
                                <input type="text" wire:model="submissionFeedback.<?php echo e($student->id); ?>" placeholder="Umpan balik" class="flex-1 rounded-lg border border-neutral-300 px-2 py-1 text-sm">
                                <button wire:click="saveSubmissionGrade(<?php echo e($student->id); ?>)" class="rounded-lg bg-brand-600 px-3 py-1 text-xs font-medium text-white hover:bg-brand-700">Simpan</button>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'nilai'): ?>
        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-xl border border-neutral-200 bg-white p-6">
                <h2 class="text-sm font-semibold text-neutral-800">Komponen Nilai</h2>
                <form wire:submit="addComponent" class="mt-3 space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-neutral-700">Nama Komponen</label>
                        <input type="text" wire:model="componentName" placeholder="mis. Tugas, UTS, UAS" class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['componentName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-neutral-700">Bobot (%)</label>
                        <input type="number" wire:model="componentWeight" class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm">
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-brand-600 px-3 py-2 text-sm font-medium text-white hover:bg-brand-700">Tambah Komponen</button>
                </form>

                <ul class="mt-4 divide-y divide-neutral-100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $gradeComponents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $component): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <button wire:click="selectComponent(<?php echo e($component->id); ?>)"
                                class="w-full py-2 text-left text-sm <?php echo e($selectedComponentId === $component->id ? 'font-semibold text-brand-700' : 'text-neutral-600'); ?>">
                                <?php echo e($component->name); ?> (<?php echo e($component->weight); ?>%)
                            </button>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </ul>
            </div>

            <div class="lg:col-span-2 rounded-xl border border-neutral-200 bg-white p-6">
                <h2 class="text-sm font-semibold text-neutral-800">Input Nilai</h2>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $selectedComponentId): ?>
                    <p class="mt-3 text-sm text-neutral-500">Pilih komponen nilai dulu.</p>
                <?php else: ?>
                    <div class="mt-3 space-y-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->roster; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center gap-3 border-b border-neutral-100 py-2 text-sm">
                                <span class="w-40 shrink-0 text-neutral-700"><?php echo e($student->nim); ?> — <?php echo e($student->user->name); ?></span>
                                <input type="number" wire:model="gradeScores.<?php echo e($student->id); ?>" placeholder="Skor" class="w-24 rounded-lg border border-neutral-300 px-2 py-1 text-sm">
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->roster->isNotEmpty()): ?>
                            <button wire:click="saveGrades" class="mt-3 rounded-lg bg-brand-600 px-3 py-2 text-sm font-medium text-white hover:bg-brand-700">Simpan Nilai</button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <h2 class="mt-6 text-sm font-semibold text-neutral-800">Nilai Akhir</h2>
                <div class="mt-3 space-y-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->roster; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center justify-between gap-3 border-b border-neutral-100 py-2 text-sm">
                            <span class="text-neutral-700"><?php echo e($student->nim); ?> — <?php echo e($student->user->name); ?></span>
                            <div class="flex items-center gap-3">
                                <span class="text-neutral-500"><?php echo e($finalizations[$student->id] ?? '—'); ?></span>
                                <button wire:click="finalizeGrade(<?php echo e($student->id); ?>)" class="rounded-lg border border-brand-600 px-3 py-1 text-xs font-medium text-brand-600 hover:bg-brand-50">Finalisasi</button>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\az-zahra-digital-campus-fase0\az-zahra-digital-campus\resources\views/livewire/dosen/kelas/show.blade.php ENDPATH**/ ?>