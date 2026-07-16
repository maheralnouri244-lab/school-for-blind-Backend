<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-4">

        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold m-0" style="color: var(--text-main);">الدورات الوزارية السابقة</h2>

            
            <a href="<?php echo e(route('dashboard.past-exams.create')); ?>" class="btn px-4 rounded-pill fw-bold shadow-sm-hover"
                style="background-color: var(--accent-color); color: #111827; border: none; transition: transform 0.2s;">
                <i class="fa-solid fa-plus me-2"></i> إضافة دورة جديدة
            </a>
        </div>

        
        <div class="custom-card mb-4 p-3 border" style="border-color: var(--border-color) !important;">
            <form action="<?php echo e(route('dashboard.past-exams.index')); ?>" method="GET" class="row g-3 align-items-center">

                <div class="col-md-4">
                    <input type="text" name="search" class="form-control search-input rounded-pill bg-transparent"
                        style="color: var(--text-main); border: 1px solid var(--border-color);"
                        placeholder="ابحث باسم الدورة..." value="<?php echo e(request('search')); ?>">
                </div>

                <div class="col-md-3">
                    <select name="subject_id" class="form-select search-input rounded-pill bg-transparent"
                        style="color: var(--text-main); border: 1px solid var(--border-color);">
                        <option value="">كل المواد</option>
                        <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($subject->id); ?>" <?php echo e(request('subject_id') == $subject->id ? 'selected' : ''); ?>>
                                <?php echo e($subject->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="year" class="form-select search-input rounded-pill bg-transparent"
                        style="color: var(--text-main); border: 1px solid var(--border-color);">
                        <option value="">كل السنوات</option>
                        <?php for($i = date('Y'); $i >= 2010; $i--): ?>
                            <option value="<?php echo e($i); ?>" <?php echo e(request('year') == $i ? 'selected' : ''); ?>><?php echo e($i); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    
                    <button type="submit" class="btn w-100 rounded-pill fw-bold shadow-sm-hover"
                        style="background-color: rgba(59, 130, 246, 0.12); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.5); transition: transform 0.2s;">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> تصفية
                    </button>
                </div>
            </form>
        </div>

        
        <div class="custom-card p-0 overflow-hidden rounded-4 border"
            style="border-color: var(--border-color) !important; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
            <div class="table-responsive">
                <table class="table table-hover-custom mb-0 text-center"
                    style="color: var(--text-main); border-color: var(--border-color);">
                    <thead style="background-color: var(--hover-bg);">
                        <tr>
                            <th class="py-3 px-3 border-0">#</th>
                            <th class="py-3 px-4 text-start border-0">عنوان الدورة</th>
                            <th class="py-3 px-3 border-0">المادة</th>
                            <th class="py-3 px-3 border-0">السنة والدورة</th>
                            <th class="py-3 px-3 border-0">الحالة</th>
                            <th class="py-3 px-4 border-0">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $pastExams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td class="align-middle px-3"><?php echo e($loop->iteration); ?></td>

                                
                                <td class="align-middle px-4 text-start fw-bold">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="p-2 rounded-circle bg-soft-info d-flex align-items-center justify-content-center"
                                            style="width: 38px; height: 38px;">
                                            <i class="fa-solid fa-book-open text-info" style="font-size: 1rem;"></i>
                                        </div>
                                        <span><?php echo e($exam->title); ?></span>
                                    </div>
                                </td>

                                <td class="align-middle px-3"><?php echo e($exam->subject->name ?? 'غير محدد'); ?></td>

                                <td class="align-middle px-3 fw-bold" style="color: var(--text-muted);" dir="rtl">
                                    <?php echo e($exam->year); ?> -
                                    <?php if($exam->session == 'first'): ?> الأولى
                                    <?php elseif($exam->session == 'second'): ?> الثانية
                                    <?php else: ?> تكميلية <?php endif; ?>
                                </td>

                                
                                <td class="align-middle px-3">
                                    <?php if($exam->is_published): ?>
                                        <span class="badge px-3 py-2 rounded-pill fw-normal"
                                            style="background-color: rgba(163, 230, 53, 0.15); color: var(--accent-color); border: 1px solid var(--accent-color);">
                                            <i class="fa-solid fa-earth-americas me-1"></i> منشورة
                                        </span>
                                    <?php else: ?>
                                        <span class="badge px-3 py-2 rounded-pill fw-normal"
                                            style="background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid #f59e0b;">
                                            <i class="fa-solid fa-lock me-1"></i> مسودة
                                        </span>
                                    <?php endif; ?>
                                </td>

                                
                                <td class="align-middle px-4">
                                    <div class="d-flex justify-content-center gap-2">

                                        
                                        <a href="<?php echo e(route('dashboard.past-exams.show', $exam->id)); ?>"
                                            class="btn btn-sm shadow-sm-hover d-flex align-items-center justify-content-center"
                                            style="width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #9ca3af, var(--accent-color)); box-shadow: 0 0 10px rgba(163, 230, 53, 0.3); color: #111827; border: none; transition: all 0.2s;"
                                            title="عرض وإدارة الأسئلة">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>

                                        
                                        <a href="<?php echo e(route('dashboard.past-exams.edit', $exam->id)); ?>"
                                            class="btn btn-sm text-white shadow-sm-hover d-flex align-items-center justify-content-center"
                                            style="width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #9ca3af, #3b82f6); box-shadow: 0 0 10px rgba(59, 130, 246, 0.3); border: none; transition: all 0.2s;"
                                            title="تعديل">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        
                                        <form action="<?php echo e(route('dashboard.past-exams.destroy', $exam->id)); ?>" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('هل أنت متأكد من حذف هذه الدورة بشكل نهائي؟');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit"
                                                class="btn btn-sm text-white shadow-sm-hover d-flex align-items-center justify-content-center"
                                                style="width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #9ca3af, #ef4444); box-shadow: 0 0 10px rgba(239, 68, 68, 0.3); border: none; transition: all 0.2s;"
                                                title="حذف">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-folder-open fs-1 d-block mb-3 opacity-50"></i>
                                    لا يوجد دورات سابقة مضافة حالياً.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <?php if($pastExams->hasPages()): ?>
                <div class="d-flex justify-content-center p-3 border-top" style="border-color: var(--border-color) !important;"
                    dir="ltr">
                    <?php echo e($pastExams->links()); ?>

                </div>
            <?php endif; ?>
        </div>

    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USER\Desktop\school-for-blind-Backend\school_for_blind\resources\views/pages/past_exams/index.blade.php ENDPATH**/ ?>