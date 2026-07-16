<?php $__env->startSection('content'); ?>
  <div class="container-fluid p-0">

    <div class="row g-4 mb-4">

      
      <div class="col-lg-4">
        <div class="custom-card h-100 d-flex flex-column justify-content-between">

          
          <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
              <p class="text-muted mb-1 fs-6 fw-bold">إجمالي الطلاب المسجلين</p>
              <h2 class="fw-bold mb-0" style="color: var(--text-main);"><?php echo e($studentsCount); ?></h2>
            </div>
            <div class="p-3 rounded d-flex align-items-center justify-content-center bg-soft-info"
              style="width: 55px; height: 55px;">
              <i class="fa-solid fa-graduation-cap fs-3 text-info"></i>
            </div>
          </div>

          
          <div class="d-flex gap-2 mt-auto">
            <a href="<?php echo e(route('students.index')); ?>" class="btn btn-sm flex-grow-1 py-2 fw-bold"
              style="background-color: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color); border-radius: 8px;">
              عرض الكل
            </a>
            <a href="<?php echo e(route('requests.view', 'student')); ?>"
              class="btn btn-sm flex-grow-1 py-2 fw-bold d-flex align-items-center justify-content-center gap-2"
              style="background-color: rgba(59, 130, 246, 0.12); color: #3b82f6; border: none; border-radius: 8px;">
              <span>طلبات الانضمام</span>
              <span class="badge rounded-pill px-2 py-1"
                style="background-color: rgba(59, 130, 246, 0.25); color: #3b82f6; font-size: 0.85rem;">
                <?php echo e($pendingstudentsCount); ?>

              </span>
            </a>
          </div>

        </div>
      </div>

      
      <div class="col-lg-4">
        <div class="custom-card h-100 d-flex flex-column justify-content-between">

          
          <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
              <p class="text-muted mb-1 fs-6 fw-bold">إجمالي المعلمين المسجلين</p>
              <h2 class="fw-bold mb-0" style="color: var(--text-main);"><?php echo e($teachersCount); ?></h2>
            </div>
            <div class="p-3 rounded d-flex align-items-center justify-content-center"
              style="width: 55px; height: 55px; background-color: rgba(163, 230, 53, 0.15);">
              <i class="fa-solid fa-person-chalkboard fs-3" style="color: var(--accent-color);"></i>
            </div>
          </div>

          
          <div class="d-flex gap-2 mt-auto">
            <a href="<?php echo e(route('teachers.index')); ?>" class="btn btn-sm flex-grow-1 py-2 fw-bold"
              style="background-color: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color); border-radius: 8px;">
              عرض الكل
            </a>
            <a href="<?php echo e(route('requests.view', 'teacher')); ?>"
              class="btn btn-sm flex-grow-1 py-2 fw-bold d-flex align-items-center justify-content-center gap-2"
              style="background-color: rgba(163, 230, 53, 0.12); color: var(--accent-color); border: none; border-radius: 8px;">
              <span>طلبات الانضمام</span>
              <span class="badge rounded-pill px-2 py-1"
                style="background-color: rgba(163, 230, 53, 0.25); color: var(--accent-color); font-size: 0.85rem;">
                <?php echo e($pendingteachersCount); ?>

              </span>
            </a>
          </div>

        </div>
      </div>

      
      <div class="col-lg-4">
        <a href="<?php echo e(route('content.monitor')); ?>" class="text-decoration-none d-block h-100">
          <div class="custom-card h-100 shadow-sm-hover" style="transition: transform 0.2s; cursor: pointer;">
            <div class="d-flex justify-content-between mb-4">
              <h5 class="fw-bold mb-0" style="color: var(--text-main);">مراقب المحتوى</h5>
              <div class="p-1 rounded d-flex align-items-center justify-content-center"
                style="width: 30px; height: 30px; background-color: rgba(163, 230, 53, 0.15);">
                <i class="fa-solid fa-shield-halved" style="color: var(--accent-color);"></i>
              </div>
            </div>

            <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
              
              <li class="d-flex align-items-center justify-content-between p-2 rounded shadow-sm-hover"
                style="transition: all 0.2s;">
                <div class="d-flex align-items-center">
                  <span class="p-2 rounded-circle me-3"
                    style="background: linear-gradient(135deg, #9ca3af, var(--accent-color)); box-shadow: 0 0 10px rgba(163, 230, 53, 0.3); width: 16px; height: 16px; display: inline-block;"></span>
                  <span style="color: var(--text-main); font-weight: 500;">البلاغات</span>
                </div>
                <i class="fa-solid fa-chevron-left text-muted fs-6"></i>
              </li>

              
              <li class="d-flex align-items-center justify-content-between p-2 rounded shadow-sm-hover"
                style="transition: all 0.2s;">
                <div class="d-flex align-items-center">
                  <span class="p-2 rounded-circle me-3"
                    style="background: linear-gradient(135deg, #9ca3af, #3b82f6); box-shadow: 0 0 10px rgba(59, 130, 246, 0.3); width: 16px; height: 16px; display: inline-block;"></span>
                  <span style="color: var(--text-main); font-weight: 500;">مشاكل تقنية</span>
                </div>
                <i class="fa-solid fa-chevron-left text-muted fs-6"></i>
              </li>

              
              <li class="d-flex align-items-center justify-content-between p-2 rounded shadow-sm-hover"
                style="transition: all 0.2s;">
                <div class="d-flex align-items-center">
                  <span class="p-2 rounded-circle me-3"
                    style="background: linear-gradient(135deg, #9ca3af, var(--accent-color)); box-shadow: 0 0 10px rgba(163, 230, 53, 0.3); width: 16px; height: 16px; display: inline-block;"></span>
                  <span style="color: var(--text-main); font-weight: 500;">المحتوى</span>
                </div>
                <i class="fa-solid fa-chevron-left text-muted fs-6"></i>
              </li>

              
              <li class="d-flex align-items-center justify-content-between p-2 rounded shadow-sm-hover"
                style="transition: all 0.2s;">
                <div class="d-flex align-items-center">
                  <span class="p-2 rounded-circle me-3"
                    style="background: linear-gradient(135deg, #9ca3af, #3b82f6); box-shadow: 0 0 10px rgba(59, 130, 246, 0.3); width: 16px; height: 16px; display: inline-block;"></span>
                  <span style="color: var(--text-main); font-weight: 500;">المحادثات</span>
                </div>
                <i class="fa-solid fa-chevron-left text-muted fs-6"></i>
              </li>
            </ul>
          </div>
        </a>
      </div>

    </div>

    <div class="row g-4 mb-4">

      <div class="col-lg-8">
        <div class="custom-card h-100 d-flex flex-column justify-content-center">
          <h5 class="fw-bold mb-4 text-center" style="color: var(--text-main);">تسجيل الطلاب حسب الشهر</h5>
          <canvas id="barChart" height="300"></canvas>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="custom-card h-100 d-flex flex-column justify-content-center">
          <h5 class="fw-bold mb-4 text-center" style="color: var(--text-main);">حالة الطلبات</h5>
          <canvas id="donutChart" height="200"></canvas>
        </div>
      </div>

    </div>

    <div class="row g-4 mb-4">
      <div class="col-12">
        <div class="custom-card">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0" style="color: var(--text-main);">سجل العمليات</h5>
            <a href="<?php echo e(route('logs.index')); ?>" class="btn btn-sm px-3"
              style="background-color: var(--hover-bg); color: var(--text-main); border: 1px solid var(--border-color); text-decoration: none;">
              عرض الكل
            </a>
          </div>

          <div class="table-responsive">
            <table class="table table-hover-custom align-middle mb-0" style="color: var(--text-main);">
              <thead>
                <tr style="border-bottom: 2px solid var(--border-color);">
                  <th scope="col" class="pb-3 text-muted fw-normal">الأنشطة</th>
                  <th scope="col" class="pb-3 text-muted fw-normal">القسم</th>
                  <th scope="col" class="pb-3 text-muted fw-normal">الحالة</th>
                  <th scope="col" class="pb-3 text-muted fw-normal text-start">التاريخ</th>
                </tr>
              </thead>
              <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                  <tr style="border-bottom: 1px solid var(--border-color);">
                    <td class="py-3">
                      <div class="d-flex align-items-center">
                        <div class="rounded-circle p-2 me-2 d-flex align-items-center justify-content-center"
                          style="width: 35px; height: 35px; background-color: var(--bg-main);">
                          <?php if($log->event === 'created'): ?>
                            <i class="fa-solid fa-plus text-success fs-6"></i>
                          <?php elseif($log->event === 'updated'): ?>
                            <i class="fa-solid fa-pen text-warning fs-6"></i>
                          <?php elseif($log->event === 'deleted'): ?>
                            <i class="fa-solid fa-trash text-danger fs-6"></i>
                          <?php else: ?>
                            <i class="fa-solid fa-file-signature text-info fs-6"></i>
                          <?php endif; ?>
                        </div>
                        
                      </div>
                    </td>

                    <td class="py-3 text-muted">
                      <?php echo e(class_basename($log->subject_type)); ?>

                    </td>

                    <td class="py-3">
                      <?php
                        $eventColors = ['created' => 'success', 'updated' => 'warning', 'deleted' => 'danger'];
                        $eventNames = ['created' => 'إنشاء', 'updated' => 'تعديل', 'deleted' => 'حذف'];
                        $color = $eventColors[$log->event] ?? 'info';
                        $name = $eventNames[$log->event] ?? $log->event;
                      ?>
                      <span class="badge-status bg-soft-<?php echo e($color); ?> text-<?php echo e($color); ?> px-2 py-1 rounded">
                        <?php echo e($name); ?>

                      </span>
                    </td>

                    <td class="py-3 text-muted text-start">
                      
                      <div class="d-flex justify-content-between align-items-center">
                        <span><?php echo e($log->created_at->format('Y-m-d')); ?></span>

                        

                    <button type="button" class="btn btn-sm btn-outline-secondary ms-2" data-bs-toggle="modal"
                      data-bs-target="#logModal<?php echo e($log->id); ?>">
                      <i class="fa-solid fa-eye"></i>
                    </button>
                    </td>
                  </tr>

                  <div class="modal fade" id="logModal<?php echo e($log->id); ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content text-end" dir="rtl">
                        <div class="modal-header d-flex justify-content-between">
                          <h5 class="modal-title">تفاصيل العملية (<?php echo e($name); ?>)</h5>
                          <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-start" dir="ltr">
                          <?php if(isset($log->properties['custom_info']) && is_array($log->properties['custom_info'])): ?>
                            <div class="mb-4 p-3 rounded"
                              style="background-color: var(--hover-bg); border: 1px solid var(--border-color);">
                              <h6 class="text-end fw-bold mb-3" style="color: var(--text-main);">:معلومات إضافية</h6>
                              <div class="row text-end" dir="rtl">
                                <?php $__currentLoopData = $log->properties['custom_info']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                  <div class="col-md-4 col-6 mb-3">
                                    <small class="text-muted d-block"><?php echo e(ucfirst(str_replace('_', ' ', $key))); ?></small>
                                    <strong style="color: var(--text-main);">
                                      <?php echo e(is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : ($value ?? '-')); ?>

                                    </strong>
                                  </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                              </div>
                            </div>
                          <?php endif; ?>

                          <?php if(isset($log->properties['attributes'])): ?>
                            <h6 class="text-success text-end">:القيم (الجديدة)</h6>
                            <pre
                              class="p-2 rounded"><code><?php echo e(json_encode($log->properties['attributes'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></code></pre>
                          <?php endif; ?>

                          <?php if(isset($log->properties['old'])): ?>
                            <h6 class="text-danger text-end mt-3">:القيم (القديمة)</h6>
                            <pre
                              class="p-2 rounded"><code><?php echo e(json_encode($log->properties['old'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></code></pre>
                          <?php endif; ?>

                          <?php if(!isset($log->properties['attributes']) && !isset($log->properties['old'])): ?>
                            <p class="text-muted text-end">لا توجد تفاصيل إضافية مسجلة لهذه العملية.</p>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                  <tr>
                    <td colspan="4" class="text-center py-4 text-muted">لا توجد سجلات.</td>
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

<?php $__env->startPush('scripts'); ?>
  <script>
    Chart.defaults.color = '#9ca3af';
    Chart.defaults.borderColor = '#2d3748';


    const barCtx = document.getElementById('barChart').getContext('2d');
    new Chart(barCtx, {
      type: 'bar',
      data: {
        labels: <?php echo json_encode($chartLabels, 15, 512) ?>,
        datasets: [
          {
            label: 'الطلاب',
            data: <?php echo json_encode($studentsChartData, 15, 512) ?>,
            backgroundColor: 'rgba(59, 130, 246, 0.6)',
            borderColor: '#3b82f6',
            borderWidth: 1,
            borderRadius: 4,
            barPercentage: 0.6
          },
          {
            label: 'المعلمون',
            data: <?php echo json_encode($teachersChartData, 15, 512) ?>,
            backgroundColor: 'rgba(132, 204, 22, 0.6)',
            borderColor: '#84cc16',
            borderWidth: 1,
            borderRadius: 4,
            barPercentage: 0.6
          }
        ]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: true,
            position: 'top',
            labels: {
              usePointStyle: true
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1
            }
          }
        }
      }
    });

    const donutCtx = document.getElementById('donutChart').getContext('2d');
    new Chart(donutCtx, {
      type: 'doughnut',
      data: {
        labels: ['الطلاب', 'المعلمين'],
        datasets: [{
          data: [<?php echo e($pendingstudentsCount ?? 0); ?>, <?php echo e($pendingteachersCount ?? 0); ?>],
          backgroundColor: [
            'rgba(59, 130, 246, 0.7)',
            'rgba(132, 204, 22, 0.7)'
          ],
          borderWidth: 0,
          hoverOffset: 4
        }]
      },
      options: {
        responsive: true,
        cutout: '75%',
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              padding: 20,
              usePointStyle: true
            }
          }
        }
      }
    });
  </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\project\laravel\school-for-blind-Backend\school_for_blind\resources\views/dashboard.blade.php ENDPATH**/ ?>