

<?php $__env->startSection('content'); ?>
 <div class="container-fluid p-0">
  <div class="d-flex justify-content-between align-items-center mb-4">
   <h4 class="fw-bold" style="color: var(--text-main);">لوحة مراقب المحتوى</h4>
  </div>

  <div class="row g-4">
   
   <div class="col-lg-4 col-md-6">
    <a href="<?php echo e(route('reports.index')); ?>" class="text-decoration-none d-block">
     <div class="custom-card d-flex align-items-center justify-content-between shadow-sm-hover transition-transform"
      style="transform: translateY(0); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'"
      onmouseout="this.style.transform='translateY(0)'">
      <div>
       <h5 class="fw-bold mb-2" style="color: var(--text-main);">إدارة البلاغات</h5>
       <p class="text-muted mb-0">مراجعة ومعالجة البلاغات الجديدة</p>
      </div>
      <div class="p-3 rounded-circle bg-soft-warning">
       <i class="fa-solid fa-flag text-warning fs-3"></i>
      </div>
     </div>
    </a>
   </div>

   
   <div class="col-lg-4 col-md-6">
    <a href="<?php echo e(route('punishments.active')); ?>" class="text-decoration-none d-block">
     <div class="custom-card d-flex align-items-center justify-content-between shadow-sm-hover transition-transform"
      style="transform: translateY(0); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'"
      onmouseout="this.style.transform='translateY(0)'">
      <div>
       <h5 class="fw-bold mb-2" style="color: var(--text-main);">سجل العقوبات</h5>
       <p class="text-muted mb-0">عرض المستخدمين المعاقبين حالياً</p>
      </div>
      <div class="p-3 rounded-circle bg-soft-danger">
       <i class="fa-solid fa-ban text-danger fs-3"></i>
      </div>
     </div>
    </a>
   </div>

   
   <div class="col-lg-4 col-md-6">
    <a href="<?php echo e(route('punishments.types.index')); ?>" class="text-decoration-none d-block">
     <div class="custom-card d-flex align-items-center justify-content-between shadow-sm-hover transition-transform"
      style="transform: translateY(0); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'"
      onmouseout="this.style.transform='translateY(0)'">
      <div>
       <h5 class="fw-bold mb-2" style="color: var(--text-main);">أنواع العقوبات</h5>
       <p class="text-muted mb-0">عرض العقوبات المتاحة </p>
      </div>
      <div class="p-3 rounded-circle bg-soft-info">
       <i class="fa-solid fa-gavel text-info fs-3"></i>
      </div>
     </div>
    </a>
   </div>

   
   <div class="col-lg-4 col-md-6">
    <a href="<?php echo e(route('dashboard.conversations.index')); ?>" class="text-decoration-none d-block">
     <div class="custom-card d-flex align-items-center justify-content-between shadow-sm-hover transition-transform"
      style="transform: translateY(0); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'"
      onmouseout="this.style.transform='translateY(0)'">
      <div>
       <h5 class="fw-bold mb-2" style="color: var(--text-main);">مراقبة المحادثات</h5>
       <p class="text-muted mb-0">مراقبة قنوات الأساتذة ومجموعات النقاش</p>
      </div>
      <div class="p-3 rounded-circle bg-soft-info">
       <i class="fa-solid fa-comments text-info fs-3"></i>
      </div>
     </div>
    </a>
   </div>
  </div>
 </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\project\laravel\school-for-blind-Backend\school_for_blind\resources\views/pages/content-monitor/index.blade.php ENDPATH**/ ?>