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
        <a href="{{ route('dashboard.schedules.create') }}" class="btn px-4 py-2 fw-bold"
          style="background-color: var(--accent-color); color: #000; border-radius: 8px;">
          <i class="fa-solid fa-plus ms-2"></i> إنشاء جدول جديد
        </a>
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
@endsection

@push('scripts')
  <script>
    const daysArr = ["الأحد", "الإثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة", "السبت"];
    const dayKeys = ["sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"];

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
  </script>
@endpush