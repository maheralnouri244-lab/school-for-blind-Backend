<div class="modal-header d-flex justify-content-between align-items-center" style="border-bottom: 1px solid var(--border-color); background-color: rgba(59, 130, 246, 0.05);">
    <div class="d-flex align-items-center gap-3">
        <div class="p-2 rounded-circle bg-soft-info d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="fa-solid fa-user-graduate text-info fs-5"></i>
        </div>
        <h5 class="modal-title fw-bold mb-0" style="color: var(--text-main);">الملف الشخصي للطالب</h5>
    </div>
    <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-4 text-start" dir="rtl">
    <div class="d-flex flex-column gap-2 mb-4 p-3 rounded-3" style="background-color: var(--hover-bg);">
        <div>
            <h5 class="fw-bold mb-2"><?php echo e($user->fullname); ?></h5>
            <div class="text-muted mb-1"><i class="fa-solid fa-user-tie me-2"></i>اسم الأب: <?php echo e($user->fathersname); ?></div>
            <div class="text-muted mb-1"><i class="fa-solid fa-phone me-2"></i><?php echo e($user->phone); ?></div>
            <div class="text-muted mb-1"><i class="fa-solid fa-graduation-cap me-2"></i>المرحلة: <?php echo e($user->level === 'ninth' ? 'التاسع' : 'البكالوريا'); ?></div>
            <div class="text-muted"><i class="fa-solid fa-star me-2 text-warning"></i>النقاط: <?php echo e($user->points); ?></div>
        </div>
    </div>

    <form action="<?php echo e(route('punishments.apply')); ?>" method="POST" id="apply-punishment-form">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="punishable_id" value="<?php echo e($user->id); ?>">
        <input type="hidden" name="punishable_type" value="App\Models\Student">

        <h6 class="fw-bold mb-3 text-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i>تطبيق عقوبة</h6>
        <div class="mb-3 text-start">
            <label class="form-label text-muted">اختر نوع العقوبة</label>
            <select name="punishment_id" class="form-select bg-transparent border-color" style="color: var(--text-main);" required>
                <option value="" selected disabled>-- اختر من القائمة --</option>
                <?php if(isset($punishments) && $punishments->count() > 0): ?>
                    <?php $__currentLoopData = $punishments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $punishment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($punishment->id); ?>">
                            <?php echo e($punishment->name); ?> (مستوى <?php echo e($punishment->level); ?>) 
                            <?php echo e($punishment->duration_minutes ? '- مدة: '.$punishment->duration_minutes.' دقيقة' : ''); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <option value="" disabled>لا توجد عقوبات مسجلة في النظام</option>
                <?php endif; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-danger w-100 fw-bold">تأكيد تطبيق العقوبة</button>
    </form>
</div><?php /**PATH D:\project\laravel\school-for-blind-Backend\school_for_blind\resources\views/pages/conversations/partials/student_profile_modal.blade.php ENDPATH**/ ?>