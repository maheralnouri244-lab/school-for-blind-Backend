

<?php $__env->startSection('content'); ?>
  <div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="fw-bold" style="color: var(--text-main);">المحادثات والقنوات</h4>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="<?php echo e(route('content.monitor')); ?>" class="text-decoration-none"
              style="color: var(--accent-color);">مراقب المحتوى</a></li>
          <li class="breadcrumb-item active" aria-current="page" style="color: var(--text-muted);">المحادثات</li>
        </ol>
      </nav>
    </div>

    <div class="custom-card">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0" style="color: var(--text-main);">القائمة الحالية</h5>
        <div class="input-group" style="width: 250px;">
          <span class="input-group-text border-0" style="background-color: var(--bg-main); color: var(--text-muted);">
            <i class="fa-solid fa-magnifying-glass"></i>
          </span>
          <input type="text" class="form-control border-0 shadow-none search-input" placeholder="بحث عن أستاذ أو مادة...">
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover-custom align-middle mb-0" style="color: var(--text-main);">
          <thead>
            <tr style="border-bottom: 2px solid var(--border-color);">
              <th scope="col" class="pb-3 text-muted fw-normal">اسم المحادثة</th>
              <th scope="col" class="pb-3 text-muted fw-normal">النوع</th>
              <th scope="col" class="pb-3 text-muted fw-normal">الأستاذ المرتبط</th>
              <th scope="col" class="pb-3 text-muted fw-normal">تاريخ الإنشاء</th>
              <th scope="col" class="pb-3 text-muted fw-normal text-start">الإجراءات</th>
            </tr>
          </thead>
          <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $conversations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <tr style="border-bottom: 1px solid var(--border-color);">
                <td class="py-3 fw-bold"><?php echo e($conv->name ?? 'محادثة بدلاً من اسم'); ?></td>
                <td class="py-3">
                  <?php if($conv->type === 'channel'): ?>
                    <span class="badge-status bg-soft-info text-info px-2 py-1 rounded">قناة</span>
                  <?php elseif($conv->type === 'discussion'): ?>
                    <span class="badge-status bg-soft-warning text-warning px-2 py-1 rounded">مجموعة نقاش</span>
                  <?php else: ?>
                    <span class="badge-status bg-soft-success text-success px-2 py-1 rounded">محادثة إدارية</span>
                  <?php endif; ?>
                </td>
                <td class="py-3 text-muted"><?php echo e($conv->teacher->full_name ?? 'غير محدد'); ?></td>
                <td class="py-3 text-muted"><?php echo e($conv->created_at->format('Y-m-d')); ?></td>
                <td class="py-3 text-start">
                  <a href="<?php echo e(route('dashboard.conversations.show', $conv->id)); ?>" class="btn btn-sm"
                    style="background-color: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color);">
                    عرض المحادثة <i class="fa-solid fa-arrow-left ms-1"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr>
                <td colspan="5" class="text-center py-4 text-muted">لا توجد محادثات حالياً.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="mt-4 d-flex justify-content-center">
        <?php echo e($conversations->links()); ?>

      </div>
    </div>
  </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\project\laravel\school-for-blind-Backend\school_for_blind\resources\views/pages/conversations/index.blade.php ENDPATH**/ ?>