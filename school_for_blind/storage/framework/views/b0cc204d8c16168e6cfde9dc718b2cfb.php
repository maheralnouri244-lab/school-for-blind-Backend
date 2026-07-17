

<?php $__env->startSection('content'); ?>
  <div class="container-fluid py-4">

    
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold m-0" style="color: var(--text-main);">تعديل بيانات الامتحان</h2>
      <a href="<?php echo e(route('dashboard.exams.index')); ?>" class="btn px-4 rounded-pill fw-bold shadow-sm-hover"
        style="background-color: var(--hover-bg); color: var(--text-main); border: 1px solid var(--border-color); transition: transform 0.2s;">
        <i class="fa-solid fa-arrow-right me-2"></i> عودة للقائمة
      </a>
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-8">
        
        <div class="custom-card p-4 shadow-sm-hover border"
          style="border-color: var(--border-color) !important; border-radius: 16px;">

          
          <?php if($errors->any()): ?>
            <div class="alert alert-danger rounded-3 fw-bold mb-4">
              <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </ul>
            </div>
          <?php endif; ?>

          <form action="<?php echo e(route('dashboard.exams.update', $exam->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="mb-4 text-start" dir="rtl">
              <label for="title" class="form-label fw-bold text-muted mb-2">عنوان الامتحان <span
                  class="text-danger">*</span></label>
              <input type="text" name="title" id="title" class="form-control bg-transparent rounded-3" required
                style="color: var(--text-main); border: 1px solid var(--border-color);"
                value="<?php echo e(old('title', $exam->title)); ?>">
            </div>

            <div class="mb-4 text-start" dir="rtl">
              <label for="description" class="form-label fw-bold text-muted mb-2">تفاصيل / نطاق الامتحان</label>
              <textarea name="description" id="description" class="form-control bg-transparent rounded-3"
                style="color: var(--text-main); border: 1px solid var(--border-color);"
                rows="3"><?php echo e(old('description', $exam->description)); ?></textarea>
            </div>

            <div class="row mb-4">
              <div class="col-md-12 text-start" dir="rtl">
                <label for="subject_id" class="form-label fw-bold text-muted mb-2">المادة <span
                    class="text-danger">*</span></label>
                <select name="subject_id" id="subject_id" class="form-select bg-transparent rounded-3" required
                  style="color: var(--text-main); border: 1px solid var(--border-color);">
                  <option value="" disabled>-- اختر المادة --</option>
                  <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($subject->id); ?>" <?php echo e(old('subject_id', $exam->subject_id) == $subject->id ? 'selected' : ''); ?>>
                      <?php echo e($subject->name); ?>

                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
            </div>

            <div class="row mb-5 g-4">
              <<div class="col-md-6 text-start" dir="rtl">
                <label for="exam_date" class="form-label fw-bold text-muted mb-2">موعد الامتحان (التاريخ والوقت)</label>
                <input type="datetime-local" name="exam_date" id="exam_date" class="form-control bg-transparent rounded-3"
                  style="color: var(--text-main); border: 1px solid var(--border-color);"
                  value="<?php echo e($exam->exam_date ? \Carbon\Carbon::parse($exam->exam_date)->format('Y-m-d\TH:i') : ''); ?>">
                <small class="text-muted">يمكنك تركه فارغاً إذا كان الامتحان مسودة</small>
            </div>

            <div class="col-md-6 text-start" dir="rtl">
              <label for="duration_minutes" class="form-label fw-bold text-muted mb-2">المدة (بالدقائق) <span
                  class="text-danger">*</span></label>
              <input type="number" name="duration_minutes" id="duration_minutes"
                class="form-control bg-transparent rounded-3" required
                style="color: var(--text-main); border: 1px solid var(--border-color);" min="5" max="300"
                value="<?php echo e(old('duration_minutes', $exam->duration_minutes)); ?>">
            </div>
        </div>

        
        <div class="d-flex justify-content-between align-items-center pt-4 border-top"
          style="border-color: var(--border-color) !important;">
          <div>
            <?php if(!$exam->is_published): ?>
              <span class="badge px-3 py-2 rounded-pill fw-normal"
                style="background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid #f59e0b;">
                <i class="fa-solid fa-lock me-1"></i> الحالة: مسودة
              </span>
            <?php else: ?>
              <span class="badge px-3 py-2 rounded-pill fw-normal"
                style="background-color: rgba(163, 230, 53, 0.15); color: var(--accent-color); border: 1px solid var(--accent-color);">
                <i class="fa-solid fa-earth-americas me-1"></i> الحالة: منشور للطلاب
              </span>
            <?php endif; ?>
          </div>
          <button type="submit" class="btn px-5 py-2 rounded-pill fw-bold shadow-sm-hover"
            style="background-color: var(--accent-color); color: #111827; border: none; transition: transform 0.2s;">
            <i class="fa-solid fa-circle-check me-2"></i> تحديث البيانات
          </button>
        </div>

        </form>
      </div>
    </div>
  </div>
  </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\project\laravel\school-for-blind-Backend\school_for_blind\resources\views/pages/exams/edit.blade.php ENDPATH**/ ?>