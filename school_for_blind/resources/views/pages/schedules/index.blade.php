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

    {{-- تبويبات الاختيار بين جداول الشعب وجداول الأساتذة --}}
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

      {{-- التبويب الأول: أحدث جداول الشعب --}}
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
                  <th scope="col" class="pb-3 text-muted fw-normal">عنوان الجدول</th>
                  <th scope="col" class="pb-3 text-muted fw-normal">النوع</th>
                  <th scope="col" class="pb-3 text-muted fw-normal">تاريخ النشر</th>
                  <th scope="col" class="pb-3 text-muted fw-normal text-start">إجراءات</th>
                </tr>
              </thead>
              <tbody>
                @forelse($classSchedules as $schedule)
                  @php
                    $contentData = is_string($schedule->content) ? json_decode($schedule->content, true) : $schedule->content;
                  @endphp
                  <tr style="border-bottom: 1px solid var(--border-color);">
                    <td class="py-3 fw-bold">
                      @if($schedule->class)
                        {{ $schedule->class->name }} (شعبة {{ $schedule->class->number }})
                      @else
                        عام / غير محدد
                      @endif
                    </td>

                    <td class="py-3 text-muted">{{ $schedule->title }}</td>

                    <td class="py-3">
                      @if($schedule->type === 'school_timetable')
                        <span class="badge-status bg-soft-info px-2 py-1 rounded">برنامج دوام</span>
                      @else
                        <span class="badge-status bg-soft-warning px-2 py-1 rounded">برنامج امتحانات</span>
                      @endif
                    </td>

                    <td class="py-3 text-muted">{{ $schedule->created_at->format('Y-m-d H:i') }}</td>

                    <td class="py-3 text-start">
                      <button type="button" class="btn btn-sm btn-outline-primary ms-1"
                        onclick="viewClassSchedule('{{ json_encode($contentData) }}', '{{ $schedule->title }}')">
                        <i class="fa-solid fa-eye me-1"></i> عرض
                      </button>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center py-4 text-muted">لا توجد جداول منشورة حالياً.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- التبويب الثاني: جداول الأساتذة --}}
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
                        onclick="viewTeacherSchedule('{{ $teacher->full_name }}')">
                        <i class="fa-solid fa-calendar-week me-1"></i> عرض جدول الأستاذ
                      </button>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="3" class="text-center py-4 text-muted">لا يوجد أساتذة مسجلون.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>

  {{-- Modal عرض جدول الشعبة --}}
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
    const allSchedulesData = @json($allLatestSchedules);

    function viewClassSchedule(contentJson, title) {
      let content = typeof contentJson === 'string' ? JSON.parse(contentJson) : contentJson;
      document.getElementById('modalScheduleTitle').innerText = title;

      let html = '<table class="table table-bordered text-center align-middle" style="color: var(--text-main); font-size:0.85rem;"><thead><tr class="table-dark">';

      if (content && content.columns) {
        content.columns.forEach(col => {
          html += `<th>${col}</th>`;
        });
        html += '</tr></thead><tbody>';

        if (content.rows) {
          content.rows.forEach(row => {
            html += '<tr>';
            html += `<td class="fw-bold bg-soft-secondary">${row.period || '-'}</td>`;

            let dayKeys = ["sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"];
            dayKeys.forEach(key => {
              let val = row[key] || '-';
              html += `<td>${val}</td>`;
            });
            html += '</tr>';
          });
        }
        html += 'tbody></table>';
      } else {
        html = '<p class="text-center text-muted">لا توجد بيانات متاحة لهذا الجدول.</p>';
      }

      document.getElementById('modalScheduleContainer').innerHTML = html;
      new bootstrap.Modal(document.getElementById('viewScheduleModal')).show();
    }

    function viewTeacherSchedule(teacherName) {
      document.getElementById('modalScheduleTitle').innerText = `جدول الدوام للأستاذ: ${teacherName}`;

      let days = ["الأحد", "الإثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة", "السبت"];
      let dayKeys = ["sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"];

      let teacherGrid = {};

      allSchedulesData.forEach(sched => {
        let content = sched.content;
        if (content && content.rows) {
          content.rows.forEach((row, rowIndex) => {
            let periodNum = rowIndex + 1;
            dayKeys.forEach(dKey => {
              let slotVal = row[dKey];
              if (slotVal && slotVal.includes(`(${teacherName})`)) {
                let subject = slotVal.replace(`(${teacherName})`, '').trim();
                if (!teacherGrid[dKey]) teacherGrid[dKey] = {};
                teacherGrid[dKey][periodNum] = `${subject} <br><small class="text-primary">${sched.class_name}</small>`;
              }
            });
          });
        }
      });

      let html = '<table class="table table-bordered text-center align-middle" style="color: var(--text-main); font-size:0.85rem;"><thead><tr class="table-dark"><th>اليوم / الحصة</th>';
      for (let p = 1; p <= 8; p++) {
        html += `<th>الحصة ${p}</th>`;
      }
      html += '</tr></thead><tbody>';

      dayKeys.forEach((dKey, idx) => {
        html += `<tr><td class="fw-bold bg-soft-secondary">${days[idx]}</td>`;
        for (let p = 1; p <= 8; p++) {
          let cellContent = (teacherGrid[dKey] && teacherGrid[dKey][p]) ? teacherGrid[dKey][p] : '-';
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