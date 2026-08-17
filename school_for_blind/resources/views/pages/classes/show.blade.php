@extends('layouts.app')

@section('content')
  <div class="container-fluid p-0">

    {{-- الهيدر العلوي وزر العودة --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div class="d-flex align-items-center gap-3">
        <a href="{{ route('classes') }}"
          class="btn btn-outline-secondary btn-sm rounded-circle p-2 d-flex align-items-center justify-content-center"
          style="width: 38px; height: 38px;">
          <i class="fa-solid fa-arrow-right"></i>
        </a>
        <div>
          <h4 class="fw-bold mb-0" style="color: var(--text-main);">تفاصيل {{ $class->name }}</h4>
          <small class="text-muted">المستوى: {{ $class->level == 'twelfth' ? 'البكالوريا' : 'التاسع' }} | الشعبة رقم
            {{ $class->number }}</small>
        </div>
      </div>

      {{-- زر عرض جدول الدوام --}}
      <a href="{{ route('dashboard.schedules.index', ['class_id' => $class->id]) }}"
        class="btn btn-sm px-3 fw-bold d-flex align-items-center gap-2"
        style="background-color: var(--hover-bg); color: var(--text-main); border: 1px solid var(--border-color);">
        <i class="fa-regular fa-calendar-days text-muted"></i>
        <span>جدول الدوام</span>
      </a>
    </div>

    {{-- القوائم المزدوجة --}}
    <div class="row g-4">

      {{-- 1. قائمة الطلاب (الجهة اليمنى) --}}
      <div class="col-lg-7">
        <div class="custom-card h-100 p-4">
          <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom"
            style="border-color: var(--border-color) !important;">
            <div class="d-flex align-items-center gap-2">
              <i class="fa-solid fa-user-graduate fs-5 text-info"></i>
              <h5 class="fw-bold mb-0" style="color: var(--text-main);">طلاب الشعبة</h5>
              <span class="badge bg-soft-info text-info rounded-pill ms-2">{{ $class->students->count() }}</span>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-hover-custom align-middle mb-0" style="color: var(--text-main);">
              <thead>
                <tr style="border-bottom: 2px solid var(--border-color);">
                  <th scope="col" class="pb-3 text-muted fw-bold">الطالب</th>
                  <th scope="col" class="pb-3 text-muted fw-bold text-center">رقم الهاتف</th>
                  {{-- <th scope="col" class="pb-3 text-muted fw-bold text-center">النقاط</th> --}}
                  <th scope="col" class="pb-3 text-muted fw-bold text-start">الإجراءات</th>
                </tr>
              </thead>
              <tbody>
                @forelse($class->students as $student)
                  <tr style="border-bottom: 1px solid var(--border-color);">
                    <td class="py-3">
                      <span class="fw-bold cursor-pointer text-decoration-underline-hover"
                        onclick="openUserDetails('student', {{ $student->id }})">
                        {{ $student->fullname }}
                      </span>
                    </td>
                    <td class="py-3 text-center text-muted" dir="ltr">{{ $student->phone }}</td>
                    {{-- <td class="py-3 text-center">
                      <span class="badge bg-soft-success text-success px-2 py-1">{{ $student->points }} نقطة</span>
                    </td> --}}
                    <td class="py-3 text-start">
                      <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                          data-bs-toggle="dropdown">
                          خيارات
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                          <li>
                            <a class="dropdown-menu-item dropdown-item cursor-pointer text-warning"
                              onclick="openTransferModal({{ $student->id }}, '{{ $student->fullname }}')">
                              <i class="fa-solid fa-right-left me-2"></i>نقل لشعبة أخرى
                            </a>
                          </li>
                          {{-- <li>
                            <a class="dropdown-menu-item dropdown-item cursor-pointer text-success"
                              onclick="openRewardSuggestModal({{ $student->id }}, '{{ $student->fullname }}')">
                              <i class="fa-solid fa-award me-2"></i>اقتراح نقاط مكافأة
                            </a> --}}
                          </li>
                          <li>
                            <a class="dropdown-menu-item dropdown-item cursor-pointer text-danger"
                              onclick="fetchUserPunishments('student', {{ $student->id }})">
                              <i class="fa-solid fa-gavel me-2"></i>سجل العقوبات
                            </a>
                          </li>
                          <li>
                            <a class="dropdown-menu-item dropdown-item cursor-pointer text-info"
                              onclick="openExcusesModal({{ $student->id }}, '{{ $student->fullname }}')">
                              <i class="fa-solid fa-envelope-open-text me-2"></i>تذاكر الغياب
                            </a>
                          </li>
                          <li>
                            <a href="{{ route('students.absences', $student->id) }}"
                              class="dropdown-menu-item dropdown-item cursor-pointer text-info">
                              <i class="fa-solid fa-calendar-xmark me-2"></i>سجل غيابات الطالب
                            </a>
                          </li>
                          <li>
                            <a href="{{ route('students.reports', $student->id) }}"
                              class="dropdown-menu-item dropdown-item cursor-pointer text-primary">
                              <i class="fa-solid fa-chart-pie me-2"></i>تقارير الطالب
                            </a>
                          </li>
                        </ul>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center py-4 text-muted">لا يوجد طلاب مسجلون في هذه الشعبة.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- 2. قائمة الأساتذة (الجهة اليسرى) --}}
      {{-- 2. قائمة الأساتذة (الجهة اليسرى) --}}
      <div class="col-lg-5">
        <div class="custom-card h-100 p-4">
          <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom"
            style="border-color: var(--border-color) !important;">
            <div class="d-flex align-items-center gap-2">
              <i class="fa-solid fa-chalkboard-user fs-5 text-success"></i>
              <h5 class="fw-bold mb-0" style="color: var(--text-main);">أساتذة الشعبة</h5>
              <span class="badge bg-soft-success text-success rounded-pill ms-2">{{ $class->teachers->count() }}</span>
            </div>

            {{-- زر إضافة أستاذ لشعبة --}}
            <button type="button" class="btn btn-sm btn-success fw-bold d-flex align-items-center gap-1"
              data-bs-toggle="modal" data-bs-target="#addTeacherModal">
              <i class="fa-solid fa-plus"></i> إضافة أستاذ
            </button>
          </div>

          <div class="table-responsive">
            <table class="table table-hover-custom align-middle mb-0" style="color: var(--text-main);">
              <thead>
                <tr style="border-bottom: 2px solid var(--border-color);">
                  <th scope="col" class="pb-3 text-muted fw-bold">الأستاذ</th>
                  <th scope="col" class="pb-3 text-muted fw-bold text-center">المواد</th>
                  <th scope="col" class="pb-3 text-muted fw-bold text-start">الإجراءات</th>
                </tr>
              </thead>
              <tbody>
                @forelse($class->teachers as $teacher)
                  <tr style="border-bottom: 1px solid var(--border-color);">
                    <td class="py-3 fw-bold">{{ $teacher->full_name }}</td>
                    <td class="py-3 text-center">
                      {{-- جلب وعرض أسماء المواد المرتبطة بهاد الأستاذ --}}
                      @forelse($teacher->subjects()->get() as $subject)
                        <span class="badge bg-soft-info text-info mb-1">{{ $subject->name }}</span>
                      @empty
                        <span class="text-muted small">غير محدد</span>
                      @endforelse
                    </td>
                    <td class="py-3 text-start">
                      <button type="button" class="btn btn-sm btn-outline-danger"
                        onclick="confirmDetachTeacher({{ $teacher->id }}, '{{ $teacher->full_name }}')"
                        title="إلغاء ربط من الشعبة">
                        <i class="fa-solid fa-user-minus"></i>
                      </button>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="3" class="text-center py-4 text-muted">لا يوجد أساتذة مسندون لهذه الشعبة.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
  {{-- Modal إضافة أستاذ للشعبة - تصميم عصري --}}
  <div class="modal fade glass-modal" id="addTeacherModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg text-end" dir="rtl">
      <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">

        {{-- هيدر المودال --}}
        <div class="modal-header border-bottom pb-3" style="border-color: var(--border-color) !important;">
          <div class="d-flex align-items-center gap-2">
            <div class="p-2 rounded-circle bg-soft-success d-flex align-items-center justify-content-center"
              style="width: 40px; height: 40px;">
              <i class="fa-solid fa-user-plus text-success fs-5"></i>
            </div>
            <div>
              <h5 class="modal-title fw-bold mb-0" style="color: var(--text-main);">إضافة أستاذ للشعبة</h5>
              <small class="text-muted">اختر الأستاذ من القائمة لإسناده لهذه الشعبة</small>
            </div>
          </div>
          <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <form action="{{ route('classes.teachers.attach', $class->id) }}" method="POST" id="attachTeacherForm">
          @csrf
          <input type="hidden" name="teacher_id" id="selected_teacher_id" required>

          <div class="modal-body py-4">

            <div class="mb-3 position-relative">
              <i class="fa-solid fa-magnifying-glass position-absolute top-50 translate-middle-y text-muted"
                style="right: 15px;"></i>
              <input type="text" id="teacherSearchInput" class="form-control search-input"
                placeholder="ابحث باسم الأستاذ أو المادة..."
                style="border-radius: 10px; padding-top: 10px; padding-bottom: 10px; padding-right: 45px;">
            </div>

            {{-- قائمة الأساتذة التفاعلية --}}
            <div class="teacher-select-list d-flex flex-column gap-2" id="teachersListContainer"
              style="max-height: 320px; overflow-y: auto; padding-left: 5px;">
              @forelse($availableTeachers as $teacher)
                <div
                  class="teacher-select-card p-3 rounded-3 cursor-pointer d-flex align-items-center justify-content-between transition-all"
                  data-teacher-id="{{ $teacher->id }}"
                  data-search-text="{{ mb_strtolower($teacher->full_name . ' ' . $teacher->subjects()->get()->pluck('name')->join(' ')) }}"
                  onclick="selectTeacherCard(this, {{ $teacher->id }})"
                  style="background-color: var(--bg-main); border: 2px solid var(--border-color); transition: all 0.2s ease;">

                  <div class="d-flex align-items-center gap-3">
                    {{-- أيقونة الأستاذ --}}
                    <div class="p-2 rounded-circle bg-soft-info d-flex align-items-center justify-content-center"
                      style="width: 45px; height: 45px; min-width: 45px;">
                      <i class="fa-solid fa-chalkboard-user text-info fs-5"></i>
                    </div>

                    <div>
                      <h6 class="fw-bold mb-1" style="color: var(--text-main);">{{ $teacher->full_name }}</h6>
                      <div class="d-flex flex-wrap gap-1 align-items-center">
                        @forelse($teacher->subjects()->get() as $subject)
                          <span class="badge bg-soft-info text-info font-normal"
                            style="font-size: 0.75rem;">{{ $subject->name }}</span>
                        @empty
                          <span class="text-muted small" style="font-size: 0.75rem;">بدون مادة محددة</span>
                        @endforelse
                      </div>
                    </div>
                  </div>

                  {{-- دائرة الاختيار (Radio UI) --}}
                  <div class="check-indicator rounded-circle d-flex align-items-center justify-content-center"
                    style="width: 26px; height: 26px; border: 2px solid var(--border-color); background-color: var(--bg-card);">
                    <i class="fa-solid fa-check text-white fs-6 opacity-0 check-icon"></i>
                  </div>
                </div>
              @empty
                <div class="text-center py-4 text-muted">
                  <i class="fa-solid fa-user-slash fs-2 mb-2 opacity-50"></i>
                  <p class="mb-0">جميع الأساتذة المتاحين مضافون بالفعل إلى هذه الشعبة.</p>
                </div>
              @endforelse
            </div>

          </div>

          {{-- فوتر المودال --}}
          <div class="modal-header border-top pt-3 d-flex justify-content-between"
            style="border-color: var(--border-color) !important;">
            <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal"
              style="border-radius: 8px;">إلغاء</button>
            <button type="submit" class="btn btn-success px-4 fw-bold d-flex align-items-center gap-2"
              id="submitAttachBtn" disabled style="border-radius: 8px;">
              <i class="fa-solid fa-plus"></i>
              <span>إضافة للشعبة</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade glass-modal" id="transferStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered text-end" dir="rtl">
      <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
        <div class="modal-header border-bottom pb-3" style="border-color: var(--border-color) !important;">
          <div class="d-flex align-items-center gap-2">
            <div class="p-2 rounded-circle bg-soft-warning d-flex align-items-center justify-content-center"
              style="width: 40px; height: 40px;">
              <i class="fa-solid fa-right-left text-warning fs-5"></i>
            </div>
            <div>
              <h5 class="modal-title fw-bold mb-0" style="color: var(--text-main);">نقل طالب إلى شعبة أخرى</h5>
              <small class="text-muted">نقل الطالب <span id="transferStudentNameDisplay" class="fw-bold"></span></small>
            </div>
          </div>
          <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <form action="{{ route('classes.students.transfer', $class->id) }}" method="POST">
          @csrf
          <input type="hidden" name="student_id" id="transfer_student_id" required>

          <div class="modal-body py-4">
            <div class="mb-3">
              <label for="new_class_id" class="form-label text-muted">اختر الشعبة الجديدة</label>
              <select class="form-select" id="new_class_id" name="new_class_id" required
                style="background-color: var(--bg-main); color: var(--text-main); border-color: var(--border-color);">
                <option value="" disabled selected>-- اختر الشعبة --</option>
                @forelse($allClasses as $availableClass)
                  <option value="{{ $availableClass->id }}">
                    {{ $availableClass->name }} (الشعبة {{ $availableClass->number }})
                  </option>
                @empty
                  <option value="" disabled>لا توجد شعب أخرى متاحة من نفس المستوى الدراسي</option>
                @endforelse
              </select>
            </div>
            <div class="alert bg-soft-warning border-warning text-warning d-flex align-items-center gap-2 mb-0"
              role="alert">
              <i class="fa-solid fa-triangle-exclamation"></i>
              <small>سيتم نقل الطالب فوراً إلى الشعبة المحددة وتحديث بياناته.</small>
            </div>
          </div>

          <div class="modal-header border-top pt-3 d-flex justify-content-between"
            style="border-color: var(--border-color) !important;">
            <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal"
              style="border-radius: 8px;">إلغاء</button>
            <button type="submit" class="btn btn-warning px-4 fw-bold text-dark d-flex align-items-center gap-2"
              style="border-radius: 8px;">
              <i class="fa-solid fa-right-left"></i>
              <span>تأكيد النقل</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="modal fade glass-modal" id="rewardSuggestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered text-end" dir="rtl">
      <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
        <div class="modal-header border-bottom pb-3" style="border-color: var(--border-color) !important;">
          <div class="d-flex align-items-center gap-2">
            <div class="p-2 rounded-circle bg-soft-success d-flex align-items-center justify-content-center"
              style="width: 40px; height: 40px;">
              <i class="fa-solid fa-award text-success fs-5"></i>
            </div>
            <div>
              <h5 class="modal-title fw-bold mb-0" style="color: var(--text-main);">اقتراح نقاط مكافأة</h5>
              <small class="text-muted">الطالب: <span id="rewardStudentNameDisplay" class="fw-bold"></span></small>
            </div>
          </div>
          <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <form action="{{ route('classes.students.suggest_points', $class->id) }}" method="POST">
          @csrf
          <input type="hidden" name="student_id" id="reward_student_id" required>

          <div class="modal-body py-4">
            <div class="mb-3">
              <label for="reason" class="form-label text-muted">سبب المكافأة</label>
              <textarea class="form-control" id="reason" name="reason" rows="3" required
                placeholder="مثال: التفوق في اختبار الرياضيات..."
                style="background-color: var(--bg-main); color: var(--text-main); border-color: var(--border-color);"></textarea>
            </div>
            <div class="mb-3">
              <label for="suggested_points" class="form-label text-muted">النقاط المقترحة (اختياري)</label>
              <input type="number" class="form-control" id="suggested_points" name="suggested_points" min="1"
                placeholder="مثال: 50"
                style="background-color: var(--bg-main); color: var(--text-main); border-color: var(--border-color);">
              <small class="text-muted mt-1 d-block">يمكن ترك هذا الحقل فارغاً لتحدده الإدارة لاحقاً.</small>
            </div>
          </div>

          <div class="modal-header border-top pt-3 d-flex justify-content-between"
            style="border-color: var(--border-color) !important;">
            <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal"
              style="border-radius: 8px;">إلغاء</button>
            <button type="submit" class="btn btn-success px-4 fw-bold d-flex align-items-center gap-2"
              style="border-radius: 8px;">
              <i class="fa-solid fa-paper-plane"></i>
              <span>إرسال المقترح</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="modal fade glass-modal" id="excusesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable text-end" dir="rtl">
      <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
        <div class="modal-header border-bottom pb-3" style="border-color: var(--border-color) !important;">
          <div class="d-flex align-items-center gap-2">
            <div class="p-2 rounded-circle bg-soft-info d-flex align-items-center justify-content-center"
              style="width: 40px; height: 40px;">
              <i class="fa-solid fa-envelope-open-text text-info fs-5"></i>
            </div>
            <div>
              <h5 class="modal-title fw-bold mb-0" style="color: var(--text-main);">تذاكر تبرير الغياب</h5>
              <small class="text-muted">الطالب: <span id="excusesStudentNameDisplay" class="fw-bold"></span></small>
            </div>
          </div>
          <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body py-4" id="excusesModalBody" style="min-height: 200px;">
          <div class="d-flex justify-content-center align-items-center h-100">
            <div class="spinner-border text-info" role="status">
              <span class="visually-hidden">جاري التحميل...</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  {{-- Modal سجل العقوبات --}}
  <div class="modal fade" id="userPunishmentsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content glass-modal text-end" dir="rtl"
        style="border: 1px solid var(--border-color); color: var(--text-main);">
        <div class="modal-header border-0 p-4">
          <h5 class="fw-bold mb-0 text-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i>سجل عقوبات المستخدم
          </h5>
          <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4" id="punishments-modal-body">
          <div class="text-center py-4"><i class="fa-solid fa-circle-notch fa-spin fs-2 text-muted"></i></div>
        </div>
      </div>
    </div>
  </div>
@endsection
@push('scripts')
  <script>

    // دالة جلب وعرض سجل العقوبات
    window.fetchUserPunishments = function (type, id) {
      const punModalElement = document.getElementById('userPunishmentsModal');
      let punModal = bootstrap.Modal.getInstance(punModalElement);
      if (!punModal) {
        punModal = new bootstrap.Modal(punModalElement);
      }

      const body = document.getElementById('punishments-modal-body');
      body.innerHTML = '<div class="text-center py-4"><i class="fa-solid fa-circle-notch fa-spin fs-2 text-muted"></i></div>';

      punModal.show();

      // هنا نستخدم نفس الراوت الخاص بالـ UserManagerController والذي عدلناه ليبحث بالرقم
      fetch(`/dashboard/users/${type}/${id}/punishments`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            body.innerHTML = data.html;
          } else {
            console.error("Server Error:", data);
            body.innerHTML = `<div class="text-center py-4 text-danger"><i class="fa-solid fa-triangle-exclamation mb-2 fs-1"></i><br>حدث خطأ بالسيرفر.</div>`;
          }
        })
        .catch(error => {
          body.innerHTML = '<div class="text-center py-4 text-danger">حدث خطأ أثناء الاتصال بالسيرفر.</div>';
          console.error("Fetch Error:", error);
        });
    };

    function confirmDetachTeacher(teacherId, teacherName) {
      if (confirm(`هل أنت متأكد أنك تريد إلغاء ربط الأستاذ ${teacherName} من هذه الشعبة؟`)) {
        let form = document.createElement('form');
        form.method = 'POST';
        let url = "{{ route('classes.teachers.detach', ['class_id' => $class->id, 'teacher_id' => ':teacher_id']) }}";
        url = url.replace(':teacher_id', teacherId);
        form.action = url;
        form.innerHTML = `
                                    @csrf
                                    @method('DELETE')
                                `;
        document.body.appendChild(form);
        form.submit();
      }
    }

    // تحديد أستاذ من الكروت
    function selectTeacherCard(cardElement, teacherId) {
      // إلغاء التحديد عن باقي الكروت
      document.querySelectorAll('.teacher-select-card').forEach(card => {
        card.style.borderColor = 'var(--border-color)';
        card.style.backgroundColor = 'var(--bg-main)';
        const check = card.querySelector('.check-indicator');
        check.style.backgroundColor = 'var(--bg-card)';
        check.style.borderColor = 'var(--border-color)';
        card.querySelector('.check-icon').classList.add('opacity-0');
      });

      // تحديد الكرت المختار
      cardElement.style.borderColor = 'var(--accent-color)';
      cardElement.style.backgroundColor = 'var(--hover-bg)';

      const indicator = cardElement.querySelector('.check-indicator');
      indicator.style.backgroundColor = 'var(--accent-color)';
      indicator.style.borderColor = 'var(--accent-color)';
      cardElement.querySelector('.check-icon').classList.remove('opacity-0');

      // تعبئة قيمة الـ Input وتفعيل زر الحفظ
      document.getElementById('selected_teacher_id').value = teacherId;
      document.getElementById('submitAttachBtn').disabled = false;
    }

    // البحث اللحظي داخل القائمة
    document.getElementById('teacherSearchInput')?.addEventListener('input', function (e) {
      const term = e.target.value.toLowerCase().trim();
      const cards = document.querySelectorAll('.teacher-select-card');

      cards.forEach(card => {
        const searchText = card.getAttribute('data-search-text');
        if (searchText.includes(term)) {
          card.style.setProperty('display', 'flex', 'important');
        } else {
          card.style.setProperty('display', 'none', 'important');
        }
      });
    });
    function openTransferModal(studentId, studentName) {
      document.getElementById('transfer_student_id').value = studentId;
      document.getElementById('transferStudentNameDisplay').textContent = studentName;

      var transferModal = new bootstrap.Modal(document.getElementById('transferStudentModal'));
      transferModal.show();
    }
    function openRewardSuggestModal(studentId, studentName) {
      document.getElementById('reward_student_id').value = studentId;
      document.getElementById('rewardStudentNameDisplay').textContent = studentName;

      var rewardModal = new bootstrap.Modal(document.getElementById('rewardSuggestModal'));
      rewardModal.show();
    }
    function openExcusesModal(studentId, studentName) {
      document.getElementById('excusesStudentNameDisplay').textContent = studentName;
      document.getElementById('excusesModalBody').innerHTML = `
                   <div class="d-flex justify-content-center align-items-center" style="height: 150px;">
                       <div class="spinner-border text-info" role="status"></div>
                   </div>`;

      var excusesModal = new bootstrap.Modal(document.getElementById('excusesModal'));
      excusesModal.show();

      fetch(`/students/${studentId}/excuses`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            document.getElementById('excusesModalBody').innerHTML = data.html;
          }
        })
        .catch(error => console.error('Error fetching excuses:', error));
    }

    function updateExcuseStatus(excuseId, status, studentId) {
      if (confirm('هل أنت متأكد من هذا الإجراء؟')) {
        fetch(`/excuses/${excuseId}/status`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({ status: status })
        })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              let studentName = document.getElementById('excusesStudentNameDisplay').textContent;
              openExcusesModal(studentId, studentName);
            }
          })
          .catch(error => console.error('Error updating excuse:', error));
      }
    }
  </script>
@endpush