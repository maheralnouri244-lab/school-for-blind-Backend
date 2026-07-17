

<?php $__env->startSection('content'); ?>
  <div class="container-fluid py-4">

    
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold m-0" style="color: var(--text-main);">إضافة دورة وزارية جديدة</h2>
      <a href="<?php echo e(route('dashboard.past-exams.index')); ?>" class="btn px-4 rounded-pill fw-bold shadow-sm-hover"
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

          <form action="<?php echo e(route('dashboard.past-exams.store')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>

            
            <div class="mb-4 text-start" dir="rtl">
              <label for="title" class="form-label fw-bold text-muted mb-2">عنوان الدورة <span
                  class="text-danger">*</span></label>
              <input type="text" name="title" id="title" class="form-control bg-transparent rounded-3" required
                style="color: var(--text-main); border: 1px solid var(--border-color);"
                placeholder="مثال: دورة 2023 - رياضيات" value="<?php echo e(old('title')); ?>">
            </div>

            <div class="row mb-4 g-4">
              
              <div class="col-md-6 text-start" dir="rtl">
                <label for="subject_id" class="form-label fw-bold text-muted mb-2">المادة <span
                    class="text-danger">*</span></label>
                <select name="subject_id" id="subject_id" class="form-select bg-transparent rounded-3" required
                  style="color: var(--text-main); border: 1px solid var(--border-color);">
                  <option value="" selected disabled>-- اختر المادة --</option>
                  <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($subject->id); ?>" <?php echo e(old('subject_id') == $subject->id ? 'selected' : ''); ?>>
                      <?php echo e($subject->name); ?>

                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>

              
              <div class="col-md-6 text-start" dir="rtl">
                <label for="year" class="form-label fw-bold text-muted mb-2">السنة الدراسية <span
                    class="text-danger">*</span></label>
                <input type="number" name="year" id="year" class="form-control bg-transparent rounded-3" required
                  min="2000" style="color: var(--text-main); border: 1px solid var(--border-color);"
                  max="<?php echo e(date('Y') + 1); ?>" value="<?php echo e(old('year', date('Y'))); ?>">
              </div>
            </div>

            <div class="row mb-4 g-4">
              
              <div class="col-md-6 text-start" dir="rtl">
                <label for="session" class="form-label fw-bold text-muted mb-2">الدورة <span
                    class="text-danger">*</span></label>
                <select name="session" id="session" class="form-select bg-transparent rounded-3" required
                  style="color: var(--text-main); border: 1px solid var(--border-color);">
                  <option value="first" <?php echo e(old('session') == 'first' ? 'selected' : ''); ?>>الأولى</option>
                  <option value="second" <?php echo e(old('session') == 'second' ? 'selected' : ''); ?>>الثانية</option>
                  <option value="complementary" <?php echo e(old('session') == 'complementary' ? 'selected' : ''); ?>>تكميلية</option>
                </select>
              </div>

              
              <div class="col-md-6 text-start" dir="rtl">
                <label for="timelimit" class="form-label fw-bold text-muted mb-2">مدة الامتحان (بالدقائق) <span
                    class="text-danger">*</span></label>
                <input type="number" name="timelimit" id="timelimit" class="form-control bg-transparent rounded-3"
                  required min="1" max="300" style="color: var(--text-main); border: 1px solid var(--border-color);"
                  placeholder="مثال: 120" value="<?php echo e(old('timelimit', 120)); ?>">
              </div>
            </div>

            
            <div class="mb-4 text-start" dir="rtl">
              <label for="voice_solution" class="form-label fw-bold text-muted mb-2">ملف الحل الصوتي الشامل
                (اختياري)</label>
              <input type="file" name="voice_solution" id="voice_solution" class="form-control bg-transparent rounded-3"
                accept="audio/*" style="color: var(--text-main); border: 1px solid var(--border-color);">
              <small class="text-muted d-block mt-1">الملفات المدعومة: mp3, wav, aac (الحد الأقصى: 20MB)</small>
            </div>

            
            <div class="d-flex justify-content-between align-items-center pt-4 border-top"
              style="border-color: var(--border-color) !important;">
              <div>
                <span class="badge px-3 py-2 rounded-pill fw-normal"
                  style="background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid #f59e0b;">
                  <i class="fa-solid fa-lock me-1"></i> سيتم حفظ الدورة كمسودة أولاً
                </span>
              </div>
              <button type="submit" class="btn px-5 py-2 rounded-pill fw-bold shadow-sm-hover"
                style="background-color: var(--accent-color); color: #111827; border: none; transition: transform 0.2s;">
                <i class="fa-solid fa-save me-2"></i> حفظ كمسودة
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\project\laravel\school-for-blind-Backend\school_for_blind\resources\views/pages/past_exams/create.blade.php ENDPATH**/ ?>