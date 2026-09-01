<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Dashboard']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Dashboard')]); ?>
    <h1 class="text-xl font-semibold text-neutral-800">Selamat datang, <?php echo e($user->name); ?></h1>
    <p class="mt-1 text-sm text-neutral-500">
        Unit: <?php echo e($user->unit?->name ?? '—'); ?> ·
        Role: <?php echo e($user->roles->pluck('name')->join(', ') ?: '—'); ?>

    </p>

    <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-xl border border-neutral-200 bg-white p-4">
            <p class="text-xs text-neutral-500">Total Pengguna</p>
            <p class="mt-1 text-2xl font-semibold text-brand-700"><?php echo e($stats['total_users']); ?></p>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-4">
            <p class="text-xs text-neutral-500">Pengguna Aktif</p>
            <p class="mt-1 text-2xl font-semibold text-brand-700"><?php echo e($stats['active_users']); ?></p>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-4">
            <p class="text-xs text-neutral-500">Unit Terlihat</p>
            <p class="mt-1 text-2xl font-semibold text-brand-700"><?php echo e($stats['total_units']); ?></p>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-4">
            <p class="text-xs text-neutral-500">Total Role</p>
            <p class="mt-1 text-2xl font-semibold text-brand-700"><?php echo e($stats['total_roles']); ?></p>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($recentAuditLogs->isNotEmpty()): ?>
        <div class="mt-6 rounded-xl border border-neutral-200 bg-white p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-neutral-800">Aktivitas Terbaru</h2>
                <a href="/admin/audit-logs" class="text-xs font-medium text-brand-600 hover:text-brand-700">Lihat semua →</a>
            </div>
            <ul class="mt-4 divide-y divide-neutral-100">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $recentAuditLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="flex items-center justify-between gap-4 py-2.5 text-sm">
                        <div>
                            <p class="text-neutral-700"><?php echo e($log->description); ?></p>
                            <p class="text-xs text-neutral-400"><?php echo e($log->user?->name ?? 'Sistem'); ?> · <?php echo e($log->created_at->diffForHumans()); ?></p>
                        </div>
                        <span class="shrink-0 rounded-full bg-brand-50 px-2 py-0.5 text-xs font-medium text-brand-700"><?php echo e($log->action); ?></span>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </ul>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="mt-6 rounded-xl border border-neutral-200 bg-white p-6">
        <p class="text-sm text-neutral-600">
            Ini adalah dasbor Core System (Fase 0). Modul akademik, keuangan, dan lainnya
            akan tampil di sini bertahap sesuai Urutan Pembangunan pada Master Blueprint.
        </p>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', \App\Models\Core\AuditLog::class)): ?>
            <a href="/admin" class="mt-4 inline-block text-sm font-medium text-brand-600 hover:text-brand-700">
                Buka Panel Admin →
            </a>
        <?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->hasRole('dosen') && $user->lecturer): ?>
            <a href="<?php echo e(route('dosen.dashboard')); ?>" class="mt-4 block text-sm font-medium text-brand-600 hover:text-brand-700">
                Buka Portal Dosen →
            </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $attributes = $__attributesOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__attributesOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $component = $__componentOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__componentOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php /**PATH C:\az-zahra-digital-campus-fase0\az-zahra-digital-campus\resources\views/core/dashboard.blade.php ENDPATH**/ ?>