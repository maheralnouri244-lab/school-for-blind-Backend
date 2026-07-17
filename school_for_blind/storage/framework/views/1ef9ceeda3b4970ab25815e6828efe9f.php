<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-4">

        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold m-0" style="color: var(--text-main);">أوراق الطلاب والتسليمات</h2>
                <p class="text-muted mt-1 mb-0">امتحان: <span class="fw-bold"
                        style="color: var(--accent-color);"><?php echo e($exam->title); ?></span></p>
            </div>
            <a href="<?php echo e(route('dashboard.exams.index')); ?>" class="btn px-4 rounded-pill fw-bold shadow-sm-hover"
               style="background-color: var(--hover-bg); color: var(--text-main); border: 1px solid var(--border-color); transition: transform 0.2s;">
                <i class="fa-solid fa-arrow-right me-2"></i> عودة للامتحانات
            </a>
        </div>

        
        <?php if(session('success')): ?>
            <div class="alert alert-success rounded-3 mb-4 fw-bold">
                <i class="fa-solid fa-circle-check me-2"></i> <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>
        <?php if(session('info')): ?>
            <div class="alert alert-info rounded-3 mb-4 fw-bold">
                <i class="fa-solid fa-circle-info me-2"></i> <?php echo e(session('info')); ?>

            </div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="alert alert-danger rounded-3 mb-4 fw-bold">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        
        <div class="custom-card mb-4 p-3 border" style="border-color: var(--border-color) !important;">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                <form action="<?php echo e(route('dashboard.exams.submissions', $exam->id)); ?>" method="GET"
                    class="d-flex gap-2 align-items-center">
                    <select name="status" class="form-select search-input rounded-pill bg-transparent" 
                            style="min-width: 220px; color: var(--text-main); border: 1px solid var(--border-color);">
                        <option value="">كل الحالات (عرض الجميع)</option>
                        <option value="pending_grading" <?php echo e(request('status') == 'pending_grading' ? 'selected' : ''); ?>>قيد تصحيح الأستاذ</option>
                        <option value="pending_approval" <?php echo e(request('status') == 'pending_approval' ? 'selected' : ''); ?>>بانتظار الختم الإداري</option>
                        <option value="approved" <?php echo e(request('status') == 'approved' ? 'selected' : ''); ?>>معتمدة للطلاب</option>
                    </select>
                    <button type="submit" class="btn rounded-pill px-4 fw-bold shadow-sm-hover"
                            style="background-color: rgba(59, 130, 246, 0.12); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.5); transition: transform 0.2s;">
                        <i class="fa-solid fa-filter me-1"></i> تصفية
                    </button>
                </form>

                <form action="<?php echo e(route('dashboard.exams.approve-all', $exam->id)); ?>" method="POST"
                    onsubmit="return confirm('هل أنت متأكد من اعتماد جميع الأوراق المعلقة؟ سيتمكن الطلاب من رؤية نتائجهم.');">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn px-4 rounded-pill fw-bold shadow-sm-hover"
                            style="background-color: var(--accent-color); color: #111827; border: none; transition: transform 0.2s;">
                        <i class="fa-solid fa-check-double me-2"></i> اعتماد كل الأوراق المعلقة
                    </button>
                </form>

            </div>
        </div>

        
        <div class="custom-card p-0 overflow-hidden rounded-4 border"
            style="border-color: var(--border-color) !important; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
            <div class="table-responsive">
                <table class="table table-hover-custom mb-0 text-center"
                    style="color: var(--text-main); border-color: var(--border-color);">
                    <thead style="background-color: var(--hover-bg);">
                        <tr>
                            <th class="py-3 px-3 border-0">#</th>
                            <th class="py-3 px-4 text-start border-0">اسم الطالب</th>
                            <th class="py-3 px-3 border-0">المجموع النهائي</th>
                            <th class="py-3 px-3 border-0">الحالة</th>
                            <th class="py-3 px-3 border-0">تاريخ التسليم</th>
                            <th class="py-3 px-4 text-center border-0">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $submissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $submission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td class="align-middle px-3"><?php echo e($loop->iteration); ?></td>

                                
                                <td class="align-middle px-4 text-start fw-bold">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="p-2 rounded-circle bg-soft-info d-flex align-items-center justify-content-center"
                                            style="width: 38px; height: 38px;">
                                            <i class="fa-solid fa-user text-info" style="font-size: 1rem;"></i>
                                        </div>
                                        <span><?php echo e($submission->student->fullname ?? $submission->student->name ?? 'طالب غير معروف'); ?></span>
                                    </div>
                                </td>

                                <td class="align-middle fw-bold fs-5 px-3" style="color: var(--accent-color);">
                                    <?php echo e($submission->score); ?>

                                </td>

                                <td class="align-middle px-3">
                                    <?php if($submission->status === 'approved'): ?>
                                        <span class="badge px-3 py-2 rounded-pill fw-normal" style="background-color: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid #10b981;">تم الاعتماد</span>
                                    <?php elseif($submission->status === 'pending_approval'): ?>
                                        <span class="badge px-3 py-2 rounded-pill fw-normal" style="background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid #f59e0b;">بانتظار الاعتماد</span>
                                    <?php elseif($submission->status === 'pending_grading'): ?>
                                        <span class="badge px-3 py-2 rounded-pill fw-normal" style="background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid #3b82f6;">قيد تصحيح الأستاذ</span>
                                    <?php else: ?>
                                        <span class="badge px-3 py-2 rounded-pill fw-normal" style="background-color: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid #ef4444;">مرفوضة</span>
                                    <?php endif; ?>
                                </td>

                                <td class="align-middle px-3" dir="ltr" style="color: var(--text-muted);">
                                    <?php echo e(\Carbon\Carbon::parse($submission->created_at)->format('Y-m-d H:i')); ?>

                                </td>

                                <td class="align-middle px-4 text-center">
                                    <div class="d-flex justify-content-center gap-2">

                                        
                                        <button type="button"
                                            class="btn btn-sm text-white shadow-sm-hover d-flex align-items-center justify-content-center"
                                            data-bs-toggle="modal" data-bs-target="#submissionModal<?php echo e($submission->id); ?>"
                                            style="width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #9ca3af, #3b82f6); box-shadow: 0 0 10px rgba(59, 130, 246, 0.3); border: none; transition: all 0.2s;" title="عرض التفاصيل">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>

                                        
                                        <?php if($submission->status === 'pending_approval'): ?>
                                            <form action="<?php echo e(route('dashboard.exams.submissions.approve', $submission->id)); ?>"
                                                method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit"
                                                    class="btn btn-sm shadow-sm-hover d-flex align-items-center justify-content-center"
                                                    style="width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #9ca3af, var(--accent-color)); box-shadow: 0 0 10px rgba(163, 230, 53, 0.3); color: #111827; border: none; transition: all 0.2s;" title="اعتماد الورقة">
                                                    <i class="fa-solid fa-check"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                    </div>
                                </td>
                            </tr>

                            
                            <div class="modal fade" id="submissionModal<?php echo e($submission->id); ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                                    <div class="modal-content glass-modal text-end" dir="rtl"
                                        style="border: 1px solid var(--border-color); color: var(--text-main); border-radius: 16px; overflow: hidden;">

                                        <div class="modal-header d-flex justify-content-between align-items-center"
                                            style="border-bottom: 1px solid var(--border-color); background-color: rgba(59, 130, 246, 0.03);">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="p-2 rounded-circle bg-soft-info d-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px;">
                                                    <i class="fa-solid fa-file-lines text-info fs-5"></i>
                                                </div>
                                                <div>
                                                    <h5 class="modal-title fw-bold mb-0" style="color: var(--text-main);">مراجعة ورقة إجابة الطالب</h5>
                                                    <small class="text-muted"><?php echo e($submission->student->fullname ?? 'غير معروف'); ?></small>
                                                </div>
                                            </div>
                                            <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body p-4">
                                            <div class="d-flex justify-content-between align-items-center p-3 rounded mb-4"
                                                style="background-color: var(--bg-main); border: 1px dashed var(--border-color);">
                                                <span class="fw-bold fs-5 text-muted">المجموع النهائي الحاصل عليه:</span>
                                                <div class="px-4 py-2 rounded-pill fw-bold fs-4"
                                                    style="background-color: rgba(163, 230, 53, 0.12); color: var(--accent-color);">
                                                    <?php echo e($submission->score); ?> درجة
                                                </div>
                                            </div>

                                            <h6 class="fw-bold mb-3" style="color: var(--text-main);"><i class="fa-solid fa-list-check me-2 text-info"></i>تفاصيل الأسئلة والإجابات:</h6>

                                            <div class="d-flex flex-column gap-3">
                                                <?php $__empty_2 = true; $__currentLoopData = $submission->answers ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $answer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                                    <div class="p-3 rounded-4 shadow-sm-hover"
                                                        style="background-color: var(--hover-bg); border: 1px solid var(--border-color); transition: all 0.2s;">

                                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                                            <strong style="color: var(--text-main); max-width: 75%; text-align: right;">
                                                                <span class="text-info me-1"><?php echo e($index + 1); ?>.</span>
                                                                <?php echo e($answer->question->description ?? 'نص السؤال غير متوفر'); ?>

                                                            </strong>
                                                            <span class="badge px-3 py-2 rounded-pill d-flex align-items-center gap-1"
                                                                style="background-color: <?php echo e($answer->is_correct ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)'); ?>; 
                                                                       color: <?php echo e($answer->is_correct ? '#10b981' : '#ef4444'); ?>; 
                                                                       border: 1px solid <?php echo e($answer->is_correct ? '#10b981' : '#ef4444'); ?>; font-size: 0.85rem;">
                                                                <span><?php echo e($answer->points_earned ?? 0); ?></span>
                                                                <small class="opacity-75">نقطة</small>
                                                            </span>
                                                        </div>

                                                        <div class="row g-2">
                                                            <div class="col-md-6">
                                                                <div class="p-3 rounded-3 h-100"
                                                                    style="background-color: var(--bg-main); border: 1px solid var(--border-color); text-align: right;">
                                                                    <small class="text-muted d-block mb-2"><i class="fa-solid fa-pen text-secondary me-1"></i> إجابة الطالب الحالية:</small>

                                                                    <?php if($answer->choice_id && $answer->choice): ?>
                                                                        <p class="mb-0 fw-bold <?php echo e($answer->is_correct ? 'text-success' : 'text-danger'); ?>">
                                                                            <i class="fa-regular fa-circle-dot me-1"></i> <?php echo e($answer->choice->choice_text ?? 'خيار غير معروف'); ?>

                                                                        </p>
                                                                    <?php elseif($answer->text_answer): ?>
                                                                        <p class="mb-0" style="color: var(--text-main); white-space: pre-line;"><?php echo e($answer->text_answer); ?></p>
                                                                    <?php else: ?>
                                                                        <p class="mb-0 text-muted fst-italic"><i class="fa-solid fa-xmark me-1 text-danger"></i> لم يقم بالإجابة.</p>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="p-3 rounded-3 h-100"
                                                                    style="background-color: rgba(16, 185, 129, 0.03); border: 1px dashed rgba(16, 185, 129, 0.3); text-align: right;">
                                                                    <small class="text-success d-block mb-2"><i class="fa-solid fa-circle-check me-1"></i> الإجابة الصحيحة النموذجية:</small>

                                                                    <?php if($answer->question && $answer->question->choices && $answer->question->choices->count() > 0): ?>
                                                                        <?php
                                                                            $correctChoice = $answer->question->choices->where('is_correct', 1)->first();
                                                                        ?>
                                                                        <?php if($correctChoice): ?>
                                                                            <p class="mb-0 fw-bold text-success"><i class="fa-solid fa-check-double me-1"></i> <?php echo e($correctChoice->choice_text); ?></p>
                                                                        <?php else: ?>
                                                                            <span class="text-muted small">لم يحدد الأستاذ خياراً صحيحاً</span>
                                                                        <?php endif; ?>
                                                                    <?php elseif($answer->question && $answer->question->correct_answer): ?>
                                                                        <p class="mb-0 text-success" style="white-space: pre-line;"><?php echo e($answer->question->correct_answer); ?></p>
                                                                    <?php else: ?>
                                                                        <span class="text-muted small">سؤال مقالي (يصحح يدوياً)</span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                                    <div class="text-center py-4 text-muted">
                                                        <i class="fa-solid fa-file-circle-xmark fs-2 mb-2 opacity-50"></i>
                                                        <p class="mb-0">لا توجد تفاصيل إجابات مسجلة لهذه الورقة بالكامل.</p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-folder-open fs-1 d-block mb-3 opacity-50"></i>
                                    لا توجد أوراق طلاب مرتبطة بهذا الامتحان أو تناسب الفلتر المختار حالياً.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if($submissions->hasPages()): ?>
                <div class="d-flex justify-content-center p-3 border-top" style="border-color: var(--border-color) !important;" dir="ltr">
                    <?php echo e($submissions->links()); ?>

                </div>
            <?php endif; ?>
        </div>

    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USER\Desktop\school-for-blind-Backend\school_for_blind\resources\views/pages/exams/submissions.blade.php ENDPATH**/ ?>