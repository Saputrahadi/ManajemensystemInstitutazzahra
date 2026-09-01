<div>
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-neutral-800">Dashboard Dosen</h1>
            <p class="mt-1 text-sm text-neutral-500"><?php echo e($lecturer->user->name); ?> &middot; <?php echo e($lecturer->nidn_nidk); ?></p>
        </div>
        <nav class="flex gap-4 text-sm font-medium text-brand-600">
            <a href="<?php echo e(route('dosen.jadwal')); ?>" class="hover:text-brand-700">Jadwal</a>
            <a href="<?php echo e(route('dosen.kelas.index')); ?>" class="hover:text-brand-700">Kelas</a>
            <a href="<?php echo e(route('dosen.wali.index')); ?>" class="hover:text-brand-700">Dosen Wali</a>
        </nav>
    </div>

    <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-2">
        <div class="rounded-xl border border-neutral-200 bg-white p-4">
            <p class="text-xs text-neutral-500">Kelas Diampu</p>
            <p class="mt-1 text-2xl font-semibold text-brand-700"><?php echo e($totalClasses); ?></p>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-4">
            <p class="text-xs text-neutral-500">Mahasiswa Bimbingan</p>
            <p class="mt-1 text-2xl font-semibold text-brand-700"><?php echo e($totalAdvisees); ?></p>
        </div>
    </div>

    <div class="mt-6 rounded-xl border border-neutral-200 bg-white p-6">
        <h2 class="text-sm font-semibold text-neutral-800">Jadwal Hari Ini</h2>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($todaySchedules->isEmpty()): ?>
            <p class="mt-3 text-sm text-neutral-500">Tidak ada jadwal mengajar hari ini.</p>
        <?php else: ?>
            <ul class="mt-4 divide-y divide-neutral-100">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $todaySchedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="flex items-center justify-between gap-4 py-2.5 text-sm">
                        <div>
                            <p class="text-neutral-700"><?php echo e($schedule->courseClass->courseOffering->course->name); ?> — Kelas <?php echo e($schedule->courseClass->code); ?></p>
                            <p class="text-xs text-neutral-400"><?php echo e($schedule->room?->name ?? 'Tanpa ruang'); ?></p>
                        </div>
                        <span class="shrink-0 rounded-full bg-brand-50 px-2 py-0.5 text-xs font-medium text-brand-700">
                            <?php echo e(\Illuminate\Support\Carbon::parse($schedule->start_time)->format('H:i')); ?> - <?php echo e(\Illuminate\Support\Carbon::parse($schedule->end_time)->format('H:i')); ?>

                        </span>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </ul>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH C:\az-zahra-digital-campus-fase0\az-zahra-digital-campus\resources\views/livewire/dosen/dashboard.blade.php ENDPATH**/ ?>