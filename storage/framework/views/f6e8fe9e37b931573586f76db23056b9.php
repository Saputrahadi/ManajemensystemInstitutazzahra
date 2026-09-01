<div
    <?php echo e($attributes
            ->merge([
                'id' => $getId(),
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)); ?>

>
    <?php echo e($getChildComponentContainer()); ?>

</div>
<?php /**PATH C:\az-zahra-digital-campus-fase0\az-zahra-digital-campus\vendor\filament\forms\resources\views/components/group.blade.php ENDPATH**/ ?>