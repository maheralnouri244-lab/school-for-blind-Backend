<?php $__env->startSection('content'); ?>
 <div class="container-fluid py-4">
  <div class="d-flex align-items-center justify-content-between mb-4">
   <div>
    <h3 class="fw-bold mb-1" style="color: var(--text-main);">الدروس والمكالمات الجارية</h3>
    <p style="color: var(--text-muted); font-size: 0.9rem;">مراقبة مباشرة للحصص الافتراضية النشطة على السيرفر حالياً</p>
   </div>
   <span class="badge-status bg-soft-success">
    عدد الدروس النشطة: <?php echo e($activeCalls->count()); ?>

   </span>
  </div>

  <div class="custom-card p-4">
   <?php if($activeCalls->isEmpty()): ?>
    <div class="text-center py-5">
     <i class="fa-solid fa-video-slash fs-1 text-muted mb-3"></i>
     <h5 style="color: var(--text-main);">لا توجد مكالمات أو دروس نشطة حالياً</h5>
     <p style="color: var(--text-muted); font-size: 0.9rem;">عندما يبدأ أي أستاذ درساً جديداً، سيظهر هنا فوراً.</p>
    </div>
   <?php else: ?>
    <div class="table-responsive">
     <table class="table table-hover-custom align-middle" style="color: var(--text-main);">
      <thead>
       <tr style="color: var(--text-muted); border-bottom: 2px solid var(--border-color);">
        <th>اسم الدرس / الغرفة</th>
        <th>الأستاذ المنشئ</th>
        <th>الشعبة المستهدفة</th>
        <th>وقت البدء</th>
        <th class="text-center">الإجراءات</th>
       </tr>
      </thead>
      <tbody>
       <?php $__currentLoopData = $activeCalls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $call): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr style="border-bottom: 1px solid var(--border-color);">
         <td class="fw-bold">
          <span style="color: var(--accent-color); font-size: 1.1rem; margin-left: 5px;">●</span>
          <?php echo e($call->room_name); ?>

         </td>
         <td>
          <div class="d-flex align-items-center gap-2">
           <i class="fa-solid fa-user-tie text-muted"></i>
           <span><?php echo e($call->creator->full_name ?? $call->creator->name ?? 'غير معروف'); ?></span>
          </div>
         </td>
         <td>
          <span class="badge bg-soft-info px-2 py-1" style="font-size: 0.85rem;">
           <i class="fa-regular fa-folder-open me-1"></i>
           <?php echo e($call->class->name ?? 'شعبة ' . $call->class_id); ?>

          </span>
         </td>
         <td style="color: var(--text-muted); font-size: 0.9rem;">
          <?php echo e($call->created_at->diffForHumans()); ?>

         </td>
         <td class="text-center">
          <a href="<?php echo e(route('rooms.join', $call->room_name)); ?>"
           class="btn px-3 py-1.5 d-inline-flex align-items-center gap-2 fw-bold text-white shadow-sm"
           style="background-color: var(--accent-color); border: none; border-radius: 8px; font-size: 0.9rem;">
           <i class="fa-solid fa-eye"></i>
           دخول ومراقبة الدرس
          </a>
         </td>
        </tr>
       <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
     </table>
    </div>
   <?php endif; ?>
  </div>
 </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Ghalia_\Downloads\newbackend\school-for-blind-Backend\school_for_blind\resources\views/pages/rooms/active_calls.blade.php ENDPATH**/ ?>