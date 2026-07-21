

<?php $__env->startSection('content'); ?>
  <div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="fw-bold m-0" style="color: var(--text-main);">مركز طلبات الدعم الفني</h2>
        <p class="text-muted mt-1 mb-0">إدارة، تصنيف وتوجيه بلاغات ومشاكل المستخدمين</p>
      </div>
    </div>

    <?php if(session('success')): ?>
      <div class="alert alert-success rounded-3 mb-4 fw-bold">
        <i class="fa-solid fa-circle-check me-2"></i> <?php echo e(session('success')); ?>

      </div>
    <?php endif; ?>

    <?php
      $selectedStatuses = request('statuses', []);
      $statusLabels = ['open' => 'جديدة', 'in_progress' => 'قيد المعالجة', 'resolved' => 'محلولة', 'closed' => 'مغلقة'];
      $currentStatusText = count($selectedStatuses) > 0 && count($selectedStatuses) < 4
        ? implode('، ', array_intersect_key($statusLabels, array_flip($selectedStatuses)))
        : (count($selectedStatuses) === 4 ? 'جميع الحالات' : 'كل الحالات (اضغط للاختيار)');

      $selectedPriorities = request('priorities', []);
      $priorityLabels = ['low' => 'منخفضة', 'medium' => 'متوسطة', 'high' => 'عالية', 'urgent' => 'عاجلة جداً'];
      $currentPriorityText = count($selectedPriorities) > 0 && count($selectedPriorities) < 4
        ? implode('، ', array_intersect_key($priorityLabels, array_flip($selectedPriorities)))
        : (count($selectedPriorities) === 4 ? 'جميع الأولويات' : 'كل الأولويات (اضغط للاختيار)');

      $selectedDepartments = request('departments', []);
      $departmentLabels = [
        'unassigned' => 'بانتظار الفرز',
        'Super Admin' => 'المدير العام',
        'Academic Manager' => 'الموجه الأكاديمي',
        'Moderator' => 'مراقب المحتوى',
        'Financial Manager' => 'المدير المالي',
        'Data Entry' => 'مدخل البيانات'
      ];
      $currentDepartmentText = count($selectedDepartments) > 0 && count($selectedDepartments) < 6
        ? implode('، ', array_intersect_key($departmentLabels, array_flip($selectedDepartments)))
        : (count($selectedDepartments) === 6 ? 'جميع الأقسام' : 'كل الأقسام (اضغط للاختيار)');
    ?>

    <div class="custom-card mb-4 p-3 border" style="border-color: var(--border-color) !important;">
      <form action="<?php echo e(route('dashboard.support.index')); ?>" method="GET" class="row g-3 align-items-end">

        <div class="col-md-3">
          <label class="form-label small fw-bold mb-1" style="color: var(--text-main);">حالة الطلب</label>
          <div class="dropdown" dir="rtl">
            <button
              class="btn w-100 text-start d-flex justify-content-between align-items-center rounded-pill bg-transparent p-2 px-3"
              type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"
              style="color: var(--text-main); border: 1px solid var(--border-color); font-size: 0.85rem; text-align: right !important;">
              <span class="text-truncate me-2" style="max-width: 85%;"><?php echo e($currentStatusText); ?></span>
              <i class="fa-solid fa-chevron-down opacity-50 small"></i>
            </button>
            <div class="dropdown-menu p-3 rounded-3 shadow text-end border w-100"
              style="background-color: var(--bg-main); border-color: var(--border-color) !important; min-width: 220px;">

              <?php $__currentLoopData = $statusLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="form-check mb-2 d-flex align-items-center gap-2 justify-content-start">
                  <input class="form-check-input m-0" type="checkbox" name="statuses[]" value="<?php echo e($key); ?>"
                    id="status_<?php echo e($key); ?>" <?php echo e(in_array($key, $selectedStatuses) ? 'checked' : ''); ?>

                    style="cursor: pointer;">
                  <label class="form-check-label small fw-bold mb-0" for="status_<?php echo e($key); ?>"
                    style="color: var(--text-main); cursor: pointer;"><?php echo e($label); ?></label>
                </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </div>
          </div>
        </div>

        <div class="col-md-3">
          <label class="form-label small fw-bold mb-1" style="color: var(--text-main);">الأولوية</label>
          <div class="dropdown" dir="rtl">
            <button
              class="btn w-100 text-start d-flex justify-content-between align-items-center rounded-pill bg-transparent p-2 px-3"
              type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"
              style="color: var(--text-main); border: 1px solid var(--border-color); font-size: 0.85rem; text-align: right !important;">
              <span class="text-truncate me-2" style="max-width: 85%;"><?php echo e($currentPriorityText); ?></span>
              <i class="fa-solid fa-chevron-down opacity-50 small"></i>
            </button>
            <div class="dropdown-menu p-3 rounded-3 shadow text-end border w-100"
              style="background-color: var(--bg-main); border-color: var(--border-color) !important; min-width: 220px;">

              <?php $__currentLoopData = $priorityLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="form-check mb-2 d-flex align-items-center gap-2 justify-content-start">
                  <input class="form-check-input m-0" type="checkbox" name="priorities[]" value="<?php echo e($key); ?>"
                    id="priority_<?php echo e($key); ?>" <?php echo e(in_array($key, $selectedPriorities) ? 'checked' : ''); ?>

                    style="cursor: pointer;">
                  <label class="form-check-label small fw-bold mb-0" for="priority_<?php echo e($key); ?>"
                    style="color: var(--text-main); cursor: pointer;"><?php echo e($label); ?></label>
                </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </div>
          </div>
        </div>

        <?php if(isset($isSuperOrSupport) && $isSuperOrSupport): ?>
          <div class="col-md-3">
            <label class="form-label small fw-bold mb-1" style="color: var(--text-main);">القسم الموجه إليه</label>
            <div class="dropdown" dir="rtl">
              <button
                class="btn w-100 text-start d-flex justify-content-between align-items-center rounded-pill bg-transparent p-2 px-3"
                type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"
                style="color: var(--text-main); border: 1px solid var(--border-color); font-size: 0.85rem; text-align: right !important;">
                <span class="text-truncate me-2" style="max-width: 85%;"><?php echo e($currentDepartmentText); ?></span>
                <i class="fa-solid fa-chevron-down opacity-50 small"></i>
              </button>
              <div class="dropdown-menu p-3 rounded-3 shadow text-end border w-100"
                style="background-color: var(--bg-main); border-color: var(--border-color) !important; min-width: 240px; max-height: 280px; overflow-y: auto;">

                <?php $__currentLoopData = $departmentLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <div class="form-check mb-2 d-flex align-items-center gap-2 justify-content-start">
                    <input class="form-check-input m-0" type="checkbox" name="departments[]" value="<?php echo e($key); ?>"
                      id="dept_<?php echo e(str_replace(' ', '_', $key)); ?>" <?php echo e(in_array($key, $selectedDepartments) ? 'checked' : ''); ?>

                      style="cursor: pointer;">
                    <label class="form-check-label small fw-bold mb-0 <?php echo e($key === 'unassigned' ? 'text-warning' : ''); ?>"
                      for="dept_<?php echo e(str_replace(' ', '_', $key)); ?>"
                      style="color: var(--text-main); cursor: pointer;"><?php echo e($label); ?></label>
                  </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

              </div>
            </div>
          </div>
        <?php endif; ?>

        <div class="col-md-2">
          <label class="form-label small fw-bold mb-1" style="color: var(--text-main);">الترتيب الأولوّي</label>
          <select name="sort_by_priority" class="form-select search-input rounded-pill bg-transparent p-2 px-3"
            style="color: var(--text-main); border: 1px solid var(--border-color); font-size: 0.85rem;">
            <option value="">بدون ترتيب أولوي</option>
            <option value="desc" <?php echo e(request('sort_by_priority') == 'desc' ? 'selected' : ''); ?>>الأعلى أولاً</option>
            <option value="asc" <?php echo e(request('sort_by_priority') == 'asc' ? 'selected' : ''); ?>>الأقل أولاً</option>
          </select>
        </div>

        <div class="col-md-1 text-end">
          <button type="submit"
            class="btn rounded-circle d-flex align-items-center justify-content-center shadow-sm-hover"
            style="width: 38px; height: 38px; background-color: rgba(59, 130, 246, 0.12); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.5); transition: transform 0.2s;"
            title="تطبيق الفلاتر">
            <i class="fa-solid fa-filter"></i>
          </button>
        </div>
      </form>

      <?php if(count($selectedStatuses) > 0 || count($selectedPriorities) > 0 || count($selectedDepartments) > 0 || request('sort_by_priority')): ?>
        <div class="mt-2 text-start px-2">
          <a href="<?php echo e(route('dashboard.support.index')); ?>" class="text-danger small text-decoration-none fw-bold"><i
              class="fa-solid fa-trash-can me-1"></i> إعادة تعيين وإلغاء كافة الفلاتر</a>
        </div>
      <?php endif; ?>
    </div>

    <div class="custom-card p-0 overflow-hidden rounded-4 border"
      style="border-color: var(--border-color) !important; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
      <div class="table-responsive">
        <table class="table table-hover-custom mb-0 text-center"
          style="color: var(--text-main); border-color: var(--border-color);">
          <thead style="background-color: var(--hover-bg);">
            <tr>
              <th class="py-3 px-3 border-0">#</th>
              <th class="py-3 px-3 text-start border-0">صاحب الطلب</th>
              <th class="py-3 px-4 text-start border-0">نص الرسالة والمشكلة</th>
              <th class="py-3 px-3 border-0">الأولوية</th>
              <th class="py-3 px-3 border-0">القسم الموجه إليه</th>
              <th class="py-3 px-3 border-0">الحالة</th>
              <th class="py-3 px-4 border-0">الإجراءات</th>
            </tr>
          </thead>
          <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <tr style="border-bottom: 1px solid var(--border-color);">
                <td class="align-middle px-3"><?php echo e($loop->iteration); ?></td>

                <td class="align-middle px-3 text-start">
                  <div class="fw-bold mb-1">
                    <?php echo e($ticket->sender->fullname ?? $ticket->sender->full_name ?? 'مستخدم غير معروف'); ?>

                  </div>
                  <?php if($ticket->sender_type === 'student' || $ticket->sender_type === 'App\Models\Student'): ?>
                    <span class="badge px-2 py-1 rounded-pill small bg-soft-info text-info">طالب</span>
                  <?php elseif($ticket->sender_type === 'teacher' || $ticket->sender_type === 'App\Models\Teacher'): ?>
                    <span class="badge px-2 py-1 rounded-pill small"
                      style="background-color: rgba(163, 230, 53, 0.15); color: var(--accent-color);">أستاذ</span>
                  <?php else: ?>
                    <span class="badge px-2 py-1 rounded-pill small bg-soft-warning text-warning">ولي أمر</span>
                  <?php endif; ?>
                </td>

                <td class="align-middle px-4 text-start small" style="max-width: 300px;">
                  <div class="text-truncate" title="<?php echo e($ticket->message); ?>"><?php echo e($ticket->message); ?></div>
                  <?php if($ticket->attachment_path): ?>
                    <a href="<?php echo e(asset('storage/' . $ticket->attachment_path)); ?>" target="_blank"
                      class="btn btn-sm mt-2 rounded-pill fw-bold small d-inline-flex align-items-center gap-1"
                      style="background-color: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid #10b981; text-decoration: none;">
                      <i class="fa-solid fa-image"></i> عرض المرفق
                    </a>
                  <?php endif; ?>
                </td>

                <td class="align-middle px-3">
                  <?php if($ticket->priority === 'urgent'): ?>
                    <span class="badge px-3 py-1.5 rounded-pill fw-bold text-white bg-danger animate-pulse">عاجل جداً
                      🚨</span>
                  <?php elseif($ticket->priority === 'high'): ?>
                    <span class="badge px-3 py-1.5 rounded-pill fw-normal"
                      style="background-color: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid #ef4444;">عالية</span>
                  <?php elseif($ticket->priority === 'medium'): ?>
                    <span class="badge px-3 py-1.5 rounded-pill fw-normal"
                      style="background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid #f59e0b;">متوسطة</span>
                  <?php else: ?>
                    <span class="badge px-3 py-1.5 rounded-pill fw-normal"
                      style="background-color: rgba(156, 163, 175, 0.1); color: #9ca3af; border: 1px solid #9ca3af;">منخفضة</span>
                  <?php endif; ?>
                </td>

                <td class="align-middle px-3 fw-bold small">
                  <?php if($ticket->assigned_department === 'Super Admin'): ?> المدير العام
                  <?php elseif($ticket->assigned_department === 'Academic Manager'): ?> الموجه الأكاديمي
                  <?php elseif($ticket->assigned_department === 'Moderator'): ?> مراقب المحتوى
                  <?php elseif($ticket->assigned_department === 'Financial Manager'): ?> المدير المالي
                  <?php elseif($ticket->assigned_department === 'Data Entry'): ?> مدخل البيانات
                  <?php else: ?>
                    <span class="text-muted fw-normal fst-italic">بانتظار الفرز</span>
                  <?php endif; ?>
                </td>

                <td class="align-middle px-3">
                  <?php if($ticket->status === 'resolved'): ?>
                    <span class="badge px-3 py-2 rounded-pill fw-normal"
                      style="background-color: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid #10b981;">محلولة</span>
                  <?php elseif($ticket->status === 'in_progress'): ?>
                    <span class="badge px-3 py-2 rounded-pill fw-normal"
                      style="background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid #3b82f6;">قيد
                      المعالجة</span>
                  <?php elseif($ticket->status === 'closed'): ?>
                    <span class="badge px-3 py-2 rounded-pill fw-normal"
                      style="background-color: rgba(156, 163, 175, 0.1); color: #9ca3af; border: 1px solid #9ca3af;">مغلقة</span>
                  <?php else: ?>
                    <span class="badge px-3 py-2 rounded-pill fw-normal"
                      style="background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid #f59e0b;">جديدة</span>
                  <?php endif; ?>
                </td>

                <td class="align-middle px-4">
                  <div class="d-flex justify-content-center gap-2">

                    <button type="button"
                      class="btn btn-sm text-white shadow-sm-hover d-flex align-items-center justify-content-center"
                      data-bs-toggle="modal" data-bs-target="#ticketModal<?php echo e($ticket->id); ?>"
                      style="width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #9ca3af, #3b82f6); box-shadow: 0 0 10px rgba(59, 130, 246, 0.2); border: none; transition: all 0.2s;">
                      <i class="fa-solid fa-folder-open"></i>
                    </button>

                    <?php if($ticket->status !== 'resolved' && $ticket->status !== 'closed'): ?>
                      <form action="<?php echo e(route('dashboard.support.update-status', $ticket->id)); ?>" method="POST"
                        class="d-inline">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="status" value="resolved">
                        <button type="submit"
                          class="btn btn-sm shadow-sm-hover d-flex align-items-center justify-content-center"
                          title="تحديد كمحلولة"
                          style="width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #9ca3af, var(--accent-color)); box-shadow: 0 0 10px rgba(163, 230, 53, 0.2); color: #111827; border: none; transition: all 0.2s;">
                          <i class="fa-solid fa-check-double"></i>
                        </button>
                      </form>
                    <?php endif; ?>

                  </div>
                </td>
              </tr>

              <div class="modal fade" id="ticketModal<?php echo e($ticket->id); ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                  <div class="modal-content glass-modal text-end" dir="rtl"
                    style="border: 1px solid var(--border-color); color: var(--text-main); border-radius: 16px; overflow: hidden;">

                    <div class="modal-header d-flex justify-content-between align-items-center"
                      style="border-bottom: 1px solid var(--border-color); background-color: rgba(59, 130, 246, 0.03);">
                      <div class="d-flex align-items-center gap-3">
                        <div class="p-2 rounded-circle bg-soft-info d-flex align-items-center justify-content-center"
                          style="width: 40px; height: 40px;">
                          <i class="fa-solid fa-headset text-info fs-5"></i>
                        </div>
                        <div>
                          <h5 class="modal-title fw-bold mb-0" style="color: var(--text-main);">تفاصيل تذكرة الدعم الفني
                          </h5>
                          <small class="text-muted">المرسل:
                            <?php echo e($ticket->sender->fullname ?? $ticket->sender->full_name ?? 'غير معروف'); ?></small>
                        </div>
                      </div>
                      <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4">
                      <div class="row g-3">

                        <div class="col-12">
                          <div class="p-3 rounded-4"
                            style="background-color: var(--hover-bg); border: 1px solid var(--border-color); text-align: right;">
                            <p class="mb-0 fs-5 style-main" style="white-space: pre-line; color: var(--text-main);">
                              <?php echo e($ticket->message); ?>

                            </p>
                          </div>
                        </div>

                        <?php if($ticket->attachment_path): ?>
                          <div class="col-12 mt-2">
                            <label class="form-label small fw-bold mb-2">المرفقات المرسلة:</label>
                            <div class="p-2 rounded-4 text-center border"
                              style="background-color: var(--bg-main); border-color: var(--border-color) !important; max-height: 350px; overflow: hidden;">
                              <a href="<?php echo e(asset('storage/' . $ticket->attachment_path)); ?>" target="_blank"
                                title="اضغط لعرض الحجم الكامل">
                                <img src="<?php echo e(asset('storage/' . $ticket->attachment_path)); ?>" class="img-fluid rounded-3"
                                  style="max-height: 330px; object-fit: contain;">
                              </a>
                            </div>
                          </div>
                        <?php endif; ?>

                        <?php if(isset($isSuperOrSupport) && $isSuperOrSupport): ?>
                          <div class="col-12 mt-4">
                            <hr style="border-color: var(--border-color);">
                            <h6 class="fw-bold mb-3">فرز وتصنيف الطلب (للإدارة فقط):</h6>
                            <form action="<?php echo e(route('dashboard.support.assign', $ticket->id)); ?>" method="POST"
                              class="p-3 rounded-4"
                              style="background-color: var(--bg-main); border: 1px dashed var(--border-color);">
                              <?php echo csrf_field(); ?>

                              <div class="row g-3">
                                <div class="col-md-4 text-start" dir="rtl">
                                  <label class="form-label small text-muted">القسم الموجه إليه</label>
                                  <select name="assigned_department" class="form-select bg-transparent rounded-3"
                                    style="color: var(--text-main); border-color: var(--border-color);" required>
                                    <option value="" <?php echo e(is_null($ticket->assigned_department) ? 'selected' : ''); ?>>اختر
                                      القسم...</option>
                                    <option value="Super Admin" <?php echo e($ticket->assigned_department == 'Super Admin' ? 'selected' : ''); ?>>المدير العام</option>
                                    <option value="Academic Manager" <?php echo e($ticket->assigned_department == 'Academic Manager' ? 'selected' : ''); ?>>الموجّه الأكاديمي</option>
                                    <option value="Moderator" <?php echo e($ticket->assigned_department == 'Moderator' ? 'selected' : ''); ?>>مراقب المحتوى</option>
                                    <option value="Financial Manager" <?php echo e($ticket->assigned_department == 'Financial Manager' ? 'selected' : ''); ?>>المدير المالي</option>
                                    <option value="Data Entry" <?php echo e($ticket->assigned_department == 'Data Entry' ? 'selected' : ''); ?>>مدخل البيانات</option>
                                  </select>
                                </div>

                                <div class="col-md-4 text-start" dir="rtl">
                                  <label class="form-label small text-muted">درجة الأولوية</label>
                                  <select name="priority" class="form-select bg-transparent rounded-3"
                                    style="color: var(--text-main); border-color: var(--border-color);" required>
                                    <option value="low" <?php echo e($ticket->priority == 'low' ? 'selected' : ''); ?>>منخفضة</option>
                                    <option value="medium" <?php echo e($ticket->priority == 'medium' ? 'selected' : ''); ?>>متوسطة</option>
                                    <option value="high" <?php echo e($ticket->priority == 'high' ? 'selected' : ''); ?>>عالية</option>
                                    <option value="urgent" <?php echo e($ticket->priority == 'urgent' ? 'selected' : ''); ?>>عاجلة جداً
                                    </option>
                                  </select>
                                </div>

                                <div class="col-md-4 text-start" dir="rtl">
                                  <label class="form-label small text-muted">حالة الطلب</label>
                                  <select name="status" class="form-select bg-transparent rounded-3"
                                    style="color: var(--text-main); border-color: var(--border-color);" required>
                                    <option value="open" <?php echo e($ticket->status == 'open' ? 'selected' : ''); ?>>جديدة</option>
                                    <option value="in_progress" <?php echo e($ticket->status == 'in_progress' ? 'selected' : ''); ?>>قيد
                                      المعالجة</option>
                                    <option value="resolved" <?php echo e($ticket->status == 'resolved' ? 'selected' : ''); ?>>محلولة
                                    </option>
                                    <option value="closed" <?php echo e($ticket->status == 'closed' ? 'selected' : ''); ?>>مغلقة</option>
                                  </select>
                                </div>
                              </div>

                              <button type="submit" class="btn w-100 mt-3 rounded-pill fw-bold shadow-sm-hover"
                                style="background-color: var(--accent-color); color: #111827; border: none;">
                                اعتماد وحفظ التعديلات
                              </button>
                            </form>
                          </div>
                        <?php else: ?>
                          <div class="col-12 mt-3 text-start">
                            <hr style="border-color: var(--border-color);">
                            <form action="<?php echo e(route('dashboard.support.update-status', $ticket->id)); ?>" method="POST"
                              class="p-3 rounded-4 d-flex flex-column gap-3"
                              style="background-color: var(--bg-main); border: 1px dashed var(--border-color);">
                              <?php echo csrf_field(); ?>

                              <div class="text-start" dir="rtl">
                                <label class="form-label small text-muted">تحديث حالة الطلب:</label>
                                <select name="status" class="form-select bg-transparent rounded-3 mb-2"
                                  style="color: var(--text-main); border-color: var(--border-color);" required>
                                  <option value="open" <?php echo e($ticket->status == 'open' ? 'selected' : ''); ?>>جديدة</option>
                                  <option value="in_progress" <?php echo e($ticket->status == 'in_progress' ? 'selected' : ''); ?>>قيد
                                    المعالجة</option>
                                  <option value="resolved" <?php echo e($ticket->status == 'resolved' ? 'selected' : ''); ?>>محلولة</option>
                                  <option value="closed" <?php echo e($ticket->status == 'closed' ? 'selected' : ''); ?>>مغلقة</option>
                                </select>
                              </div>

                              <button type="submit" class="btn rounded-pill px-4 fw-bold shadow-sm-hover w-100"
                                style="background-color: var(--accent-color); color: #111827; border: none;">
                                <i class="fa-solid fa-check-circle me-1"></i> تحديث حالة الطلب
                              </button>
                            </form>
                          </div>
                        <?php endif; ?>

                      </div>
                    </div>

                  </div>
                </div>
              </div>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                  <i class="fa-solid fa-inbox fs-1 mb-3 opacity-50"></i>
                  <br>
                  لا توجد تذاكر أو طلبات دعم فني ضمن هذا الفلتر حالياً.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php if($tickets->hasPages()): ?>
        <div class="d-flex justify-content-center p-3 border-top" style="border-color: var(--border-color) !important;"
          dir="ltr">
          <?php echo e($tickets->links()); ?>

        </div>
      <?php endif; ?>
    </div>

  </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\project\laravel\school-for-blind-Backend\school_for_blind\resources\views/pages/support/index.blade.php ENDPATH**/ ?>