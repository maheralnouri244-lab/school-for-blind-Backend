@extends('layouts.app')

@section('content')
  <div class="container-fluid p-0">

    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <div class="row mb-4">
      <div class="col-12 d-flex justify-content-between align-items-center">
        <h3 class="fw-bold m-0" style="color: var(--text-main);">إدارة الجداول الدراسية والامتحانات</h3>
        <div class="d-flex gap-2">
          <button type="button" class="btn px-4 py-2 fw-bold shadow-sm"
            style="background-color: #3b82f6; color: #fff; border-radius: 8px;" data-bs-toggle="modal"
            data-bs-target="#quickAnnouncementModal">
            <i class="fa-solid fa-bullhorn ms-2"></i> تعميم الجداول
          </button>

          <a href="{{ route('dashboard.schedules.create') }}" class="btn px-4 py-2 fw-bold shadow-sm"
            style="background-color: var(--accent-color); color: #000; border-radius: 8px;">
            <i class="fa-solid fa-plus ms-2"></i> إنشاء جدول جديد
          </a>
        </div>
      </div>
    </div>

    <ul class="nav nav-pills mb-4 gap-2" id="schedulesTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active fw-bold px-4 py-2" id="classes-tab" data-bs-toggle="tab"
          data-bs-target="#classes-panel" type="button" role="tab">
          <i class="fa-solid fa-door-open ms-2"></i> أحدث جداول الشعب
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold px-4 py-2" id="teachers-tab" data-bs-toggle="tab" data-bs-target="#teachers-panel"
          type="button" role="tab">
          <i class="fa-solid fa-chalkboard-user ms-2"></i> جداول الأساتذة
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold px-4 py-2" id="exams-tab" data-bs-toggle="tab" data-bs-target="#exams-panel"
          type="button" role="tab">
          <i class="fa-solid fa-file-signature ms-2"></i> جداول الامتحانات
        </button>
      </li>
    </ul>

    <div class="tab-content" id="schedulesTabsContent">

      <div class="tab-pane fade show active" id="classes-panel" role="tabpanel">
        <div class="custom-card">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0" style="color: var(--text-main);">أحدث جدول إداري لكل شعبة</h5>
          </div>

          <div class="table-responsive">
            <table class="table table-hover-custom align-middle mb-0" style="color: var(--text-main);">
              <thead>
                <tr style="border-bottom: 2px solid var(--border-color);">
                  <th scope="col" class="pb-3 text-muted fw-normal">الشعبة / الصف</th>
                  <th scope="col" class="pb-3 text-muted fw-normal">تاريخ آخر تحديث</th>
                  <th scope="col" class="pb-3 text-muted fw-normal text-start">إجراءات</th>
                </tr>
              </thead>
              <tbody>
                @forelse($classes as $class)
                  <tr style="border-bottom: 1px solid var(--border-color);">
                    <td class="py-3 fw-bold">{{ $class->name }} (شعبة {{ $class->number }})</td>
                    <td class="py-3 text-muted">{{ \Carbon\Carbon::parse($class->schedule_date)->format('Y-m-d H:i') }}</td>
                    <td class="py-3 text-start">
                      <button type="button" class="btn btn-sm btn-outline-primary ms-1"
                        data-title="جدول الدوام لشعبة: {{ $class->name }} ({{ $class->number }})"
                        data-schedules="{{ json_encode($class->latest_schedules) }}" onclick="handleViewSchedule(this)">
                        <i class="fa-solid fa-eye me-1"></i> عرض
                      </button>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="3" class="text-center py-4 text-muted">لا توجد جداول منشورة حالياً للشعب.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="tab-pane fade" id="teachers-panel" role="tabpanel">
        <div class="custom-card">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0" style="color: var(--text-main);">برامج الدوام الخاصة بالأساتذة</h5>
          </div>

          <div class="table-responsive">
            <table class="table table-hover-custom align-middle mb-0" style="color: var(--text-main);">
              <thead>
                <tr style="border-bottom: 2px solid var(--border-color);">
                  <th scope="col" class="pb-3 text-muted fw-normal">اسم الأستاذ</th>
                  <th scope="col" class="pb-3 text-muted fw-normal">المرحلة</th>
                  <th scope="col" class="pb-3 text-muted fw-normal text-start">إجراءات</th>
                </tr>
              </thead>
              <tbody>
                @forelse($teachers as $teacher)
                  <tr style="border-bottom: 1px solid var(--border-color);">
                    <td class="py-3 fw-bold">{{ $teacher->full_name }}</td>
                    <td class="py-3 text-muted">{{ $teacher->level === 'ninth' ? 'التاسع' : 'البكالوريا' }}</td>
                    <td class="py-3 text-start">
                      <button type="button" class="btn btn-sm btn-outline-success"
                        data-title="جدول الدوام للأستاذ: {{ $teacher->full_name }}"
                        data-schedules="{{ json_encode($teacher->latest_schedules) }}"
                        onclick="handleViewTeacherSchedule(this)">
                        <i class="fa-solid fa-calendar-week me-1"></i> عرض جدول الأستاذ
                      </button>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="3" class="text-center py-4 text-muted">لا يوجد أساتذة مسجلون أو ليس لديهم جداول.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!-- لوحة جداول الامتحانات -->
      <div class="tab-pane fade" id="exams-panel" role="tabpanel">
        <div class="custom-card">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0" style="color: var(--text-main);">أحدث جداول الامتحانات المنشورة</h5>
          </div>

          <div class="table-responsive">
            <table class="table table-hover-custom align-middle mb-0" style="color: var(--text-main);">
              <thead>
                <tr style="border-bottom: 2px solid var(--border-color);">
                  <th scope="col" class="pb-3 text-muted fw-normal">المرحلة الدراسية</th>
                  <th scope="col" class="pb-3 text-muted fw-normal">عنوان الجدول</th>
                  <th scope="col" class="pb-3 text-muted fw-normal">تاريخ النشر</th>
                  <th scope="col" class="pb-3 text-muted fw-normal text-start">إجراءات</th>
                </tr>
              </thead>
              <tbody>
                @php
                  // نجلب أحدث إعلان امتحان لكل مرحلة (هذا الكود يفضل أن يكون في الكونترولر، لكن وضعناه هنا للتسهيل بناءً على طلبك بالتركيز على الواجهة)
                  $latestNinthExam = \App\Models\Announcement::where('type', 'exam_schedule')->where('level', 'ninth')->orderBy('created_at', 'desc')->first();
                  $latestTwelfthExam = \App\Models\Announcement::where('type', 'exam_schedule')->where('level', 'twelfth')->orderBy('created_at', 'desc')->first();
                  $examsList = array_filter([$latestNinthExam, $latestTwelfthExam]);
                @endphp

                @forelse($examsList as $exam)
                  <tr style="border-bottom: 1px solid var(--border-color);">
                    <td class="py-3 fw-bold">
                      <span
                        class="badge {{ $exam->level == 'ninth' ? 'bg-soft-info text-info' : 'bg-soft-warning text-warning' }}">
                        {{ $exam->level == 'ninth' ? 'الصف التاسع' : 'البكالوريا' }}
                      </span>
                    </td>
                    <td class="py-3 fw-bold">{{ $exam->title ?? 'برنامج امتحانات' }}</td>
                    <td class="py-3 text-muted">{{ $exam->created_at->format('Y-m-d H:i') }}</td>
                    <td class="py-3 text-start">
                      <button type="button" class="btn btn-sm btn-outline-primary ms-1"
                        data-title="{{ $exam->title ?? 'برنامج امتحانات ' . ($exam->level == 'ninth' ? 'التاسع' : 'البكالوريا') }}"
                        data-content="{{ $exam->content }}" onclick="handleViewExamSchedule(this)">
                        <i class="fa-solid fa-eye me-1"></i> عرض الجدول
                      </button>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center py-4 text-muted">لا توجد جداول امتحانات منشورة حالياً.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="viewScheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content glass-modal">
        <div class="modal-header">
          <h5 class="modal-title fw-bold" id="modalScheduleTitle">عرض الجدول</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="table-responsive" id="modalScheduleContainer"></div>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal: نشر إعلان الجداول السريع -->
  <div class="modal fade" id="quickAnnouncementModal" tabindex="-1" aria-hidden="true" dir="rtl">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content glass-modal">
        <div class="modal-header border-bottom">
          <h5 class="modal-title fw-bold" style="color: var(--text-main);">إرسال تعميم بخصوص الجداول</h5>
          <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="quickAnnouncementForm">
          @csrf
          <div class="modal-body text-start">
            <p class="text-muted small mb-4">هذا التعميم سيتم إرساله فوراً إلى جميع الطلاب والمعلمين وأولياء الأمور
              كإشعار.</p>

            <div class="mb-3">
              <label class="form-label fw-bold" style="color: var(--text-main);">نوع الجدول</label>
              <select name="type" id="quickType" class="form-select"
                style="background-color: var(--bg-main); color: var(--text-main); border-color: var(--border-color);">
                <option value="normal">جدول دوام جديد</option>
                <option value="exam_schedule">جدول امتحانات</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold" style="color: var(--text-main);">الرسالة (اختياري)</label>
              <textarea id="quickContent" class="form-control" rows="3"
                placeholder="اتركه فارغاً وسيتم إرسال: تم اصدار جداول جديدة..."
                style="background-color: var(--bg-main); color: var(--text-main); border-color: var(--border-color);"></textarea>
              <small class="text-muted mt-1 d-block">سيتم ملء هذه الخانة تلقائياً إذا تركتها فارغة.</small>
            </div>

            <div id="quickAlert" class="alert d-none mt-2 mb-0"></div>
          </div>
          <div class="modal-footer border-top">
            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">إلغاء</button>
            <button type="submit" class="btn px-4" style="background-color: #3b82f6; color: #fff;" id="quickSubmitBtn">
              <span id="quickBtnText">إرسال التعميم الآن</span>
              <span id="quickBtnLoader" class="spinner-border spinner-border-sm d-none"></span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    const daysArr = ["الأحد", "الإثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة", "السبت"];
    const dayKeys = ["1", "2", "3", "4", "5", "6", "7"];
    function handleViewSchedule(btn) {
      const schedules = JSON.parse(btn.getAttribute('data-schedules'));
      const title = btn.getAttribute('data-title');
      viewSchedule(schedules, title);
    }

    function handleViewTeacherSchedule(btn) {
      const schedules = JSON.parse(btn.getAttribute('data-schedules'));
      const title = btn.getAttribute('data-title');
      viewTeacherSchedule(schedules, title);
    }

    function viewSchedule(schedules, title) {
      document.getElementById('modalScheduleTitle').innerText = title;

      let grid = {};
      schedules.forEach(s => {
        if (!grid[s.day_of_week]) grid[s.day_of_week] = {};
        grid[s.day_of_week][s.period_number] = `${s.subject?.name || '-'} <br><small class="text-muted">(${s.teacher?.full_name || s.teacher?.first_name || '-'})</small>`;
      });

      renderTable(grid);
    }

    function viewTeacherSchedule(schedules, title) {
      document.getElementById('modalScheduleTitle').innerText = title;

      let grid = {};
      schedules.forEach(s => {
        if (!grid[s.day_of_week]) grid[s.day_of_week] = {};
        let className = s.student_class ? `${s.student_class.name} (${s.student_class.number})` : '-';
        grid[s.day_of_week][s.period_number] = `${s.subject?.name || '-'} <br><small class="text-primary">${className}</small>`;
      });

      renderTable(grid);
    }

    function renderTable(grid) {
      let html = '<table class="table table-bordered text-center align-middle" style="color: var(--text-main); font-size:0.85rem;"><thead><tr class="table-dark"><th>اليوم / الحصة</th>';
      for (let p = 1; p <= 8; p++) {
        html += `<th>الحصة ${p}</th>`;
      }
      html += '</tr></thead><tbody>';

      dayKeys.forEach((dKey, idx) => {
        html += `<tr><td class="fw-bold bg-soft-secondary">${daysArr[idx]}</td>`;
        for (let p = 1; p <= 8; p++) {
          let cellContent = (grid[dKey] && grid[dKey][p]) ? grid[dKey][p] : '-';
          html += `<td>${cellContent}</td>`;
        }
        html += '</tr>';
      });

      html += '</tbody></table>';

      document.getElementById('modalScheduleContainer').innerHTML = html;
      new bootstrap.Modal(document.getElementById('viewScheduleModal')).show();
    }

    document.addEventListener('DOMContentLoaded', function () {
      const quickForm = document.getElementById('quickAnnouncementForm');

      if (quickForm) {
        quickForm.addEventListener('submit', function (e) {
          e.preventDefault();

          let typeSelect = document.getElementById('quickType');
          let contentField = document.getElementById('quickContent');

          let finalContent = contentField.value.trim();
          if (finalContent === '') {
            if (typeSelect.value === 'exam_schedule') {
              finalContent = "تم إصدار جداول امتحانات جديدة، يرجى الاطلاع عليها من قسم الجداول.";
            } else {
              finalContent = "تم إصدار جداول دوام أسبوعية جديدة، يرجى الاطلاع عليها للالتزام بالمواعيد.";
            }
          }

          let payload = {
            _token: document.querySelector('input[name="_token"]').value,
            title: 'تحديث هام بخصوص الجداول',
            content: finalContent,
            type: typeSelect.value,
            target_audience: ['all'],
            level: ['all']
          };

          let submitBtn = document.getElementById('quickSubmitBtn');
          let btnText = document.getElementById('quickBtnText');
          let btnLoader = document.getElementById('quickBtnLoader');
          let alertBox = document.getElementById('quickAlert');

          submitBtn.disabled = true;
          btnText.classList.add('d-none');
          btnLoader.classList.remove('d-none');
          alertBox.classList.add('d-none');

          fetch('{{ route("dashboard.announcements.store") }}', {
            method: 'POST',
            body: JSON.stringify(payload),
            headers: {
              'Content-Type': 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json'
            }
          })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(res => {
              if (res.status === 201 || res.status === 200) {
                alertBox.className = 'alert alert-success mt-2 mb-0';
                alertBox.innerHTML = '<i class="fa-solid fa-check me-2"></i> تم إرسال التعميم للمدرسة بنجاح!';
                alertBox.classList.remove('d-none');

                quickForm.reset();
                contentField.value = '';

                setTimeout(() => {
                  let modalInstance = bootstrap.Modal.getInstance(document.getElementById('quickAnnouncementModal'));
                  modalInstance.hide();
                  alertBox.classList.add('d-none');
                }, 2000);
              } else {
                let errorTxt = res.body.message || 'حدث خطأ أثناء الإرسال';
                if (res.body.errors) {
                  errorTxt = Object.values(res.body.errors).map(err => err.join('<br>')).join('<br>');
                }

                alertBox.className = 'alert alert-danger mt-2 mb-0';
                alertBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-2"></i> ' + errorTxt;
                alertBox.classList.remove('d-none');
              }
            })
            .catch(error => {
              alertBox.className = 'alert alert-danger mt-2 mb-0';
              alertBox.innerHTML = 'حدث خطأ في الاتصال بالسيرفر.';
              alertBox.classList.remove('d-none');
            })
            .finally(() => {
              submitBtn.disabled = false;
              btnText.classList.remove('d-none');
              btnLoader.classList.add('d-none');
            });
        });
      }
    });
    function handleViewExamSchedule(btn) {
      let contentStr = btn.getAttribute('data-content');
      let title = btn.getAttribute('data-title');

      try {
        let contentObj = JSON.parse(contentStr);
        if (typeof contentObj === 'string') {
          contentObj = JSON.parse(contentObj);
        }
        viewExamSchedule(contentObj, title);
      } catch (e) {
        console.error("خطأ في قراءة بيانات الامتحان:", e);
        alert("تعذر قراءة بيانات الجدول. قد يكون التنسيق غير مدعوم.");
      }
    }

    function viewExamSchedule(examData, title) {
      document.getElementById('modalScheduleTitle').innerText = title;

      if (!examData || !examData.columns || !examData.rows) {
        document.getElementById('modalScheduleContainer').innerHTML = '<div class="alert alert-warning text-center">لا توجد بيانات صحيحة لعرضها.</div>';
        new bootstrap.Modal(document.getElementById('viewScheduleModal')).show();
        return;
      }

      let html = '<table class="table table-bordered table-striped text-center align-middle" style="color: var(--text-main); font-size:0.9rem;">';

      html += '<thead class="table-dark"><tr>';
      examData.columns.forEach(col => {
        html += `<th>${col}</th>`;
      });
      html += '</tr></thead><tbody>';

      if (examData.rows.length === 0) {
        html += `<tr><td colspan="${examData.columns.length}" class="text-muted py-4">الجدول فارغ</td></tr>`;
      } else {
        examData.rows.forEach(row => {
          html += '<tr>';
          html += `<td class="fw-bold">${row.date}</td>`;
          html += `<td class="fw-bold text-primary">${row.subject}</td>`;
          html += `<td><span class="badge" style="background-color: var(--hover-bg); color: var(--text-main); border: 1px solid var(--border-color); font-size:0.85rem;">${row.time}</span></td>`;
          html += '</tr>';
        });
      }

      html += '</tbody></table>';

      document.getElementById('modalScheduleContainer').innerHTML = html;
      new bootstrap.Modal(document.getElementById('viewScheduleModal')).show();
    }
  </script>
@endpush