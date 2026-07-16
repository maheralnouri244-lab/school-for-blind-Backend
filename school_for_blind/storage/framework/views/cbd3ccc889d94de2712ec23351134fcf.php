<?php $__env->startSection('content'); ?>
 <div class="container-fluid p-0">
  <div class="d-flex justify-content-between align-items-center mb-4">
   <h4 class="fw-bold" style="color: var(--text-main);">أنواع العقوبات المسجلة في النظام</h4>
   <a href="<?php echo e(route('content.monitor')); ?>" class="btn btn-outline-secondary">
    <i class="fa-solid fa-arrow-right me-2"></i> رجوع
   </a>
  </div>

  <div class="row g-4">
   
   <div class="col-12">
    <div class="custom-card h-100">
     <div class="table-responsive">
      <table class="table table-hover-custom align-middle mb-0" style="color: var(--text-main);">
       <thead>
        <tr style="border-bottom: 2px solid var(--border-color);">
         <th class="pb-3 text-muted fw-normal">اسم العقوبة</th>
         <th class="pb-3 text-muted fw-normal text-center">المستوى</th>
         <th class="pb-3 text-muted fw-normal text-center">المدة الافتراضية</th>
         <th class="pb-3 text-muted fw-normal">الوصف</th>
        </tr>
       </thead>
       <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $punishments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $punishment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
         <tr style="border-bottom: 1px solid var(--border-color);">
          <td class="py-3 fw-bold"><?php echo e($punishment->name); ?></td>
          <td class="py-3 text-center">
           <span class="badge-status bg-soft-info text-info"><?php echo e($punishment->level); ?></span>
          </td>
          <td class="py-3 text-center text-muted">
           <?php echo e($punishment->duration_minutes ? $punishment->duration_minutes . ' دقيقة' : 'غير محدد'); ?>

          </td>
          <td class="py-3 text-muted"
           style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
           <?php echo e($punishment->description ?? '-'); ?>

          </td>
         </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
         <tr>
          <td colspan="4" class="text-center py-4 text-muted">لا توجد عقوبات مسجلة في النظام حالياً.</td>
         </tr>
        <?php endif; ?>
       </tbody>
      </table>
     </div>
    </div>
   </div>
  </div>
 </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USER\Desktop\school-for-blind-Backend\school_for_blind\resources\views/pages/punishments/types/index.blade.php ENDPATH**/ ?>