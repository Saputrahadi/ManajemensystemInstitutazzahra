<div>
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-neutral-800">Bimbingan Akademik</h1>
            <p class="mt-1 text-sm text-neutral-500"><?php echo e($student->nim); ?> — <?php echo e($student->user->name); ?></p>
        </div>
        <a href="<?php echo e(route('dosen.wali.index')); ?>" class="text-sm font-medium text-brand-600 hover:text-brand-700">&larr; Mahasiswa Bimbingan</a>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status')): ?>
        <div class="mt-4 rounded-lg bg-brand-50 px-4 py-2.5 text-sm text-brand-700"><?php echo e(session('status')); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-xl border border-neutral-200 bg-white p-6">
            <h2 class="text-sm font-semibold text-neutral-800">Catatan Baru</h2>
            <form wire:submit="save" class="mt-3 space-y-3">
                <div>
                    <label class="block text-xs font-medium text-neutral-700">Tanggal</label>
                    <input type="date" wire:model="advisingDate" class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['advisingDate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div>
                    <label class="block text-xs font-medium text-neutral-700">Topik</label>
                    <input type="text" wire:model="topic" class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['topic'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li class="py-3 text-sm">
                        <div class="flex items-center justify-between">
                            <p class="font-medium text-neutral-700"><?php echo e($entry->topic); ?></p>
                            <span class="text-xs text-neutral-400"><?php echo e($entry->advising_date->format('d M Y')); ?></span>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entry->notes): ?>
                            <p class="mt-1 text-xs text-neutral-500"><?php echo e($entry->notes); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li class="py-3 text-sm text-neutral-500">Belum ada catatan bimbingan.</li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </ul>
        </div>
    </div>
</div>
<?php /**PATH C:\az-zahra-digital-campus-fase0\az-zahra-digital-campus\resources\views/livewire/dosen/dosen-wali/bimbingan.blade.php ENDPATH**/ ?>