<div>
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-neutral-800">Mahasiswa Bimbingan</h1>
        <a href="<?php echo e(route('dosen.dashboard')); ?>" class="text-sm font-medium text-brand-600 hover:text-brand-700">&larr; Dashboard</a>
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
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $advisees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-4 py-3 text-neutral-700"><?php echo e($student->nim); ?></td>
                        <td class="px-4 py-3 text-neutral-700"><?php echo e($student->user->name); ?></td>
                        <td class="px-4 py-3 text-neutral-500"><?php echo e($student->studyProgram->name); ?></td>
                        <td class="px-4 py-3 text-neutral-500"><?php echo e($student->student_status); ?></td>
                        <td class="px-4 py-3 text-right">
                            <a href="<?php echo e(route('dosen.wali.show', $student)); ?>" class="text-sm font-medium text-brand-600 hover:text-brand-700">Bimbingan &rarr;</a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-sm text-neutral-500">Belum ada mahasiswa bimbingan.</td>
                    </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php /**PATH C:\az-zahra-digital-campus-fase0\az-zahra-digital-campus\resources\views/livewire/dosen/dosen-wali/advisees.blade.php ENDPATH**/ ?>