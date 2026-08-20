@extends('layouts.app')

@section('content')
  <style>
    .step-badge {
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.1rem;
    }

    .teacher-info-card,
    .student-info-card {
      background-color: var(--bg-main);
      border: 1px solid var(--border-color);
      border-radius: 12px;
      transition: all 0.3s ease;
    }

    .icon-wrapper {
      width: 50px;
      height: 50px;
    }

    .icon-wrapper.teacher {
      background-color: rgba(132, 204, 22, 0.1);
      color: var(--accent-color);
    }

    .icon-wrapper.student {
      background-color: rgba(59, 130, 246, 0.1);
      color: #3b82f6;
    }

    .summary-list li {
      background: transparent;
      border-color: var(--border-color);
      padding-inline-start: 0;
      color: var(--text-main);
    }

    .custom-pills .nav-link {
      color: var(--text-muted);
      background: var(--bg-main);
      border: 1px solid var(--border-color);
      margin-left: 10px;
      transition: all 0.3s;
    }

    .custom-pills .nav-link.active {
      color: #fff;
      background: var(--info-color);
      border-color: var(--info-color);
    }
  </style>

  <div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="fw-bold m-0" style="color: var(--text-main);">مساحتي</h3>
    </div>

    @include('pages.my_space.partials.notes')
    @include('pages.my_space.partials.profile_info')

    <div class="row mb-5 g-4">
      {{-- <div class="col-lg-6">
        <div class="card h-100 border-primary shadow-sm rounded-4"
          style="border-width: 2px !important; background: rgba(59, 130, 246, 0.02);">
          <div class="card-body p-4 d-flex flex-column justify-content-between">
            <div class="d-flex align-items-start gap-3 mb-4">
              <div class="p-3 bg-soft-info text-primary rounded-circle position-relative">
                <i class="fa-solid fa-award fs-4"></i>
                @if(isset($pendingSuggestions) && $pendingSuggestions->count() > 0)
                <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-danger">
                  {{ $pendingSuggestions->count() }}
                </span>
                @endif
              </div>
              <div>
                <h5 class="fw-bold text-primary mb-1">إجراءات تحفيزية للطلاب</h5>
                <p class="text-muted mb-0" style="font-size: 0.95rem;">مراجعة طلبات النقاط أو منح نقاط إضافية يدوياً.</p>
              </div>
            </div>
            <button type="button"
              class="btn btn-primary w-100 py-2 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2"
              data-bs-toggle="modal" data-bs-target="#grantPointsModal">
              <i class="fa-solid fa-star"></i> إدارة النقاط والمكافآت
            </button>
          </div>
        </div>
      </div> --}}

      <div class="col-lg-6">
        <div class="card h-100 border-warning shadow-sm rounded-4"
          style="border-width: 2px !important; background: rgba(245, 158, 11, 0.02);">
          <div class="card-body p-4 d-flex flex-column justify-content-between">
            <div class="d-flex align-items-start gap-3 mb-4">
              <div class="p-3 bg-soft-warning text-warning rounded-circle">
                <i class="fa-solid fa-users-gear fs-4"></i>
              </div>
              <div>
                <h5 class="fw-bold text-warning mb-1">إدارة مدراء النظام</h5>
                <p class="text-muted mb-0" style="font-size: 0.95rem;">إضافة وحذف الحسابات، وتعديل الصلاحيات وكلمات
                  المرور.</p>
              </div>
            </div>
            <button type="button"
              class="btn btn-warning w-100 py-2 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2"
              data-bs-toggle="modal" data-bs-target="#adminManagementModal">
              <i class="fa-solid fa-user-shield"></i> إدارة المدراء
            </button>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card h-100 border-danger shadow-sm rounded-4"
          style="border-width: 2px !important; background: rgba(220, 53, 69, 0.02);">
          <div class="card-body p-4 d-flex flex-column justify-content-between">
            <div class="d-flex align-items-start gap-3 mb-4">
              <div class="p-3 bg-soft-danger text-danger rounded-circle">
                <i class="fa-solid fa-triangle-exclamation fs-4"></i>
              </div>
              <div>
                <h5 class="fw-bold text-danger mb-1">إجراءات إدارية حساسة</h5>
                <p class="text-muted mb-0" style="font-size: 0.95rem;">إدارة الحسابات وفصل المعلمين نهائياً ونقل
                  صلاحياتهم.</p>
              </div>
            </div>
            <button type="button"
              class="btn btn-danger w-100 py-2 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2"
              data-bs-toggle="modal" data-bs-target="#dismissTeacherStep1Modal">
              <i class="fa-solid fa-user-slash"></i> فصل معلم ونقل مهامه
            </button>
          </div>
        </div>
      </div>

      <!-- مودال منح النقاط -->
      <div class="modal fade glass-modal" id="grantPointsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
              <h5 class="modal-title fw-bold text-primary d-flex align-items-center gap-2">
                <i class="fa-solid fa-gift"></i> إدارة منح النقاط
              </h5>
              <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
              <ul class="nav nav-pills mb-4 custom-pills" id="points-tab" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active fw-bold px-4 rounded-pill" id="suggestions-tab" data-bs-toggle="pill"
                    data-bs-target="#suggestions-pane" type="button" role="tab">طلبات النقاط المقترحة</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link fw-bold px-4 rounded-pill" id="manual-tab" data-bs-toggle="pill"
                    data-bs-target="#manual-pane" type="button" role="tab">منح حر (يدوي)</button>
                </li>
              </ul>

              <div class="tab-content" id="points-tabContent">
                <div class="tab-pane fade show active" id="suggestions-pane" role="tabpanel" tabindex="0">
                  <div class="table-responsive rounded-3" style="border: 1px solid var(--border-color);">
                    <table class="table table-hover-custom align-middle mb-0">
                      <thead style="background-color: var(--hover-bg);">
                        <tr>
                          <th class="py-3 px-3 text-muted fw-normal">الطالب</th>
                          <th class="py-3 px-3 text-muted fw-normal">السبب</th>
                          <th class="py-3 px-3 text-muted fw-normal text-center" width="130">النقاط</th>
                          <th class="py-3 px-3 text-muted fw-normal text-center" width="100">إجراء</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($pendingSuggestions ?? [] as $suggestion)
                          <tr style="border-bottom: 1px solid var(--border-color);">
                            <td class="px-3 py-3 fw-bold" style="color: var(--text-main);">
                              {{ $suggestion->student->fullname ?? $suggestion->student->name ?? 'غير معروف' }}
                            </td>
                            <td class="px-3 py-3">
                              <span class="text-muted d-block text-wrap"
                                style="font-size: 0.9rem; max-width: 250px;">{{ $suggestion->reason }}</span>
                            </td>
                            <td class="px-3 py-3 text-center">
                              <form action="{{ route('reward.grant') }}" method="POST"
                                id="form-suggestion-{{ $suggestion->id }}" class="m-0">
                                @csrf
                                <input type="hidden" name="target_type" value="student_suggestion">
                                <input type="hidden" name="target_id" value="{{ $suggestion->id }}">
                                <input type="number" name="points_amount" class="form-control text-center mx-auto"
                                  value="{{ $suggestion->suggested_points ?? 50 }}" min="1" required
                                  style="width: 90px; padding: 4px 8px;">
                              </form>
                            </td>
                            <td class="px-3 py-3 text-center">
                              <button type="submit" form="form-suggestion-{{ $suggestion->id }}"
                                class="btn btn-sm btn-success fw-bold px-3 shadow-sm">
                                منح
                              </button>
                            </td>
                          </tr>
                        @empty
                          <tr>
                            <td colspan="4" class="text-center py-5 text-muted">لا توجد طلبات منح نقاط معلقة حالياً.</td>
                          </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>

                <div class="tab-pane fade" id="manual-pane" role="tabpanel" tabindex="0">
                  <form action="{{ route('reward.grant') }}" method="POST">
                    @csrf
                    <input type="hidden" name="target_type" value="manual_student">
                    <input type="hidden" name="target_id" id="student_target_id">

                    <div class="mb-4">
                      <label class="form-label text-muted fw-bold">البحث عن طالب</label>
                      <div class="input-group input-group-lg shadow-sm rounded-3">
                        <span class="input-group-text border-end-0"
                          style="background-color: var(--bg-main); border-color: var(--border-color);"><i
                            class="fa-solid fa-phone text-muted"></i></span>
                        <input type="text" id="student-phone-input" class="form-control border-start-0 ps-0"
                          placeholder="أدخل رقم هاتف الطالب...">
                        <button class="btn btn-primary px-4 fw-bold" type="button" id="search-student-btn">بحث</button>
                      </div>
                    </div>

                    <div id="student-info-container" class="student-info-card p-3 mb-4 d-none"></div>

                    <div id="points-amount-container" class="d-none mb-4">
                      <label class="form-label text-muted fw-bold">عدد النقاط المراد منحها</label>
                      <div class="input-group input-group-lg shadow-sm rounded-3">
                        <span class="input-group-text border-end-0 text-warning"
                          style="background-color: var(--bg-main); border-color: var(--border-color);"><i
                            class="fa-solid fa-star"></i></span>
                        <input type="number" name="points_amount" class="form-control border-start-0 ps-0" value="50"
                          min="1" required>
                      </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top"
                      style="border-color: var(--border-color) !important;">
                      <button type="button" class="btn btn-outline-custom px-4 fw-bold"
                        data-bs-dismiss="modal">إلغاء</button>
                      <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm" id="submit-points-btn"
                        disabled>
                        منح النقاط
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- مودال فصل المعلم (الخطوات) -->
      <div class="modal fade glass-modal" id="dismissTeacherStep1Modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
              <h5 class="modal-title fw-bold text-danger d-flex align-items-center gap-2">
                <i class="fa-solid fa-magnifying-glass"></i> معالج نقل وفصل المعلمين
              </h5>
              <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
              <div id="step-old-teacher" class="mb-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                  <span class="badge bg-danger rounded-circle step-badge shadow-sm">1</span>
                  <h6 class="mb-0 fw-bold" style="color: var(--text-main);">تحديد المعلم المُراد فصله (القديم)</h6>
                </div>
                <div class="input-group input-group-lg mb-3 shadow-sm rounded-3">
                  <span class="input-group-text border-end-0"
                    style="background-color: var(--bg-main); border-color: var(--border-color);"><i
                      class="fa-solid fa-phone text-muted"></i></span>
                  <input type="text" id="old-phone-input" class="form-control border-start-0 ps-0"
                    placeholder="أدخل رقم هاتف المعلم بدقة...">
                  <button class="btn btn-primary px-4 fw-bold" type="button" id="search-old-btn">بحث</button>
                </div>
                <div id="old-teacher-info" class="teacher-info-card p-3 d-none"></div>
              </div>

              <div id="step-new-teacher" class="d-none mt-4 pt-4 border-top"
                style="border-color: var(--border-color) !important;">
                <div class="d-flex align-items-center gap-2 mb-3">
                  <span class="badge bg-success rounded-circle step-badge shadow-sm">2</span>
                  <h6 class="mb-0 fw-bold" style="color: var(--text-main);">تحديد المعلم البديل (الجديد)</h6>
                </div>
                <div class="input-group input-group-lg mb-3 shadow-sm rounded-3">
                  <span class="input-group-text border-end-0"
                    style="background-color: var(--bg-main); border-color: var(--border-color);"><i
                      class="fa-solid fa-phone text-muted"></i></span>
                  <input type="text" id="new-phone-input" class="form-control border-start-0 ps-0"
                    placeholder="أدخل رقم هاتف المعلم البديل بدقة...">
                  <button class="btn btn-success px-4 fw-bold" type="button" id="search-new-btn">بحث</button>
                </div>
                <div id="new-teacher-info" class="teacher-info-card p-3 d-none"></div>
              </div>
            </div>

            <div class="modal-footer border-top-0 px-4 pb-4 pt-0 d-none justify-content-between" id="review-footer">
              <span class="text-muted small"><i class="fa-solid fa-circle-info text-primary"></i> تأكد من البيانات قبل
                الانتقال للمراجعة النهائية.</span>
              <button type="button" class="btn btn-warning px-5 py-2 fw-bold shadow-sm d-flex align-items-center gap-2"
                id="open-final-modal-btn">
                مراجعة الإجراء النهائي <i class="fa-solid fa-arrow-left"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- مودال التأكيد النهائي -->
      <div class="modal fade glass-modal" id="finalConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="bg-danger py-4 text-center">
              <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                style="width: 70px; height: 70px;">
                <i class="fa-solid fa-triangle-exclamation text-danger fs-1"></i>
              </div>
              <h4 class="fw-bold text-white mb-0">تحذير: إجراء لا يمكن التراجع عنه</h4>
            </div>

            <form action="{{ route('teacher.dismiss_and_transfer') }}" method="POST" id="transfer-form">
              @csrf
              <input type="hidden" name="old_teacher_id" id="hidden_old_id">
              <input type="hidden" name="new_teacher_id" id="hidden_new_id">

              <div class="modal-body p-4">
                <p class="fw-bold mb-3 fs-5" style="color: var(--text-main);">ملخص العملية التي سيتم تنفيذها حالاً:</p>

                <div class="card border-0 rounded-3 mb-4" style="background-color: var(--hover-bg);">
                  <ul class="list-group list-group-flush summary-list p-3">
                    <li class="list-group-item d-flex align-items-start gap-3">
                      <i class="fa-solid fa-user-slash text-danger mt-1 fs-5"></i>
                      <div>
                        <strong class="text-danger">فصل نهائي:</strong> سيتم تغيير حالة الأستاذ <strong
                          id="summary-old-name" class="text-danger text-decoration-underline"></strong> إلى (مفصول -
                        Dismissed) وطرده من النظام.
                      </div>
                    </li>
                    <li class="list-group-item d-flex align-items-start gap-3">
                      <i class="fa-solid fa-people-arrows text-success mt-1 fs-5"></i>
                      <div>
                        <strong class="text-success">نقل المهام:</strong> سيتم تسليم كافة الشعب الصفية وبنك الأسئلة
                        والكويزات للأستاذ <strong id="summary-new-name"
                          class="text-success text-decoration-underline"></strong>.
                      </div>
                    </li>
                    <li class="list-group-item d-flex align-items-start gap-3">
                      <i class="fa-solid fa-link-slash text-warning mt-1 fs-5"></i>
                      <div>
                        <strong class="text-warning">فك الارتباط:</strong> سيتم فك ارتباط المدرس المفصول بجميع مواده،
                        وسيتم
                        اقتطاع راتبه لهذا الشهر إن كان هناك أموال مستحقة له.
                      </div>
                    </li>
                    <li class="list-group-item d-flex align-items-start gap-3 border-bottom-0 pb-0">
                      <i class="fa-solid fa-file-shield text-info mt-1 fs-5"></i>
                      <div>
                        <strong class="text-info">الامتحانات:</strong> الامتحانات لن تتأثر بهذا الإجراء وستبقى كما هي.
                      </div>
                    </li>
                  </ul>
                </div>

                <div class="p-3 border rounded-3 border-danger"
                  style="background-color: var(--bg-main); border-style: dashed !important;">
                  <label class="form-label text-muted fw-bold mb-2">يرجى كتابة كلمة <span
                      class="badge bg-danger fs-6">تأكيد</span> في الحقل أدناه للمتابعة</label>
                  <input type="text" name="confirmation" id="confirmation-input"
                    class="form-control form-control-lg text-center fw-bold"
                    style="color: var(--danger-color) !important;" placeholder="تأكيد" required autocomplete="off">
                </div>
              </div>

              <div class="modal-footer px-4 py-3 border-top-0 d-flex justify-content-between"
                style="background-color: var(--hover-bg);">
                <button type="button" class="btn btn-outline-secondary px-4 fw-bold" data-bs-dismiss="modal">تراجع
                  وإلغاء</button>
                <button type="submit" class="btn btn-danger px-5 fw-bold shadow-sm d-flex align-items-center gap-2"
                  id="final-submit-btn" disabled>
                  <i class="fa-solid fa-check"></i> تنفيذ الفصل والنقل
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- مودال إدارة المدراء -->
    <div class="modal fade glass-modal" id="adminManagementModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-xl"> <!-- استخدمنا modal-xl ليكون عريض ويستوعب الجدول -->
        <div class="modal-content border-0 shadow-lg rounded-4">
          <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
            <h5 class="modal-title fw-bold text-warning d-flex align-items-center gap-2">
              <i class="fa-solid fa-user-shield"></i> لوحة تحكم المدراء
            </h5>
            <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body p-4">
            <!-- التبويبات -->
            <ul class="nav nav-pills mb-4 custom-pills" id="admins-tab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold px-4 rounded-pill" id="list-admins-tab" data-bs-toggle="pill"
                  data-bs-target="#list-admins-pane" type="button" role="tab"><i class="fa-solid fa-list me-2"></i> قائمة
                  المدراء</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold px-4 rounded-pill" id="add-admin-tab" data-bs-toggle="pill"
                  data-bs-target="#add-admin-pane" type="button" role="tab"><i class="fa-solid fa-user-plus me-2"></i>
                  إضافة مدير جديد</button>
              </li>
            </ul>

            <div class="tab-content" id="admins-tabContent">
              <!-- تبويب قائمة المدراء -->
              <div class="tab-pane fade show active" id="list-admins-pane" role="tabpanel" tabindex="0">
                <div class="table-responsive rounded-3" style="border: 1px solid var(--border-color);">
                  <table class="table table-hover-custom align-middle mb-0">
                    <thead style="background-color: var(--hover-bg);">
                      <tr>
                        <th class="py-3 px-3 text-muted fw-normal">البريد الإلكتروني</th>
                        <th class="py-3 px-3 text-muted fw-normal">الصلاحية (Role)</th>
                        <th class="py-3 px-3 text-muted fw-normal">تاريخ الإضافة</th>
                        <th class="py-3 px-3 text-muted fw-normal text-center" width="200">إجراءات</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- قراءة المدراء من الداتا بيز -->
                      @forelse($admins as $admin)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                          <td class="px-3 py-3 fw-bold" style="color: var(--text-main);">{{ $admin->email }}</td>
                          <td class="px-3 py-3">
                            <!-- تلوين الصلاحية حسب نوعها -->
                            @if($admin->role === 'Super Admin')
                              <span class="badge bg-soft-danger text-danger border border-danger">{{ $admin->role }}</span>
                            @elseif($admin->role === 'Academic Manager')
                              <span class="badge bg-soft-info text-info border border-info">{{ $admin->role }}</span>
                            @else
                              <span class="badge bg-soft-warning text-warning border border-warning">{{ $admin->role }}</span>
                            @endif
                          </td>
                          <td class="px-3 py-3 text-muted small">
                            {{ $admin->created_at ? $admin->created_at->format('Y-m-d') : 'غير محدد' }}
                          </td>
                          <td class="px-3 py-3 text-center">
                            <div class="d-flex gap-2 justify-content-center">
                              <button type="button" class="btn btn-sm btn-outline-custom" title="تعديل"
                                onclick="openEditAdminModal({{ $admin->id }}, '{{ $admin->email }}', '{{ $admin->role }}')">
                                <i class="fa-solid fa-pen-to-square"></i>
                              </button>
                              <form action="{{ route('admins.destroy', $admin->id) }}" method="POST" class="m-0"
                                onsubmit="return confirm('هل أنت متأكد أنك تريد حذف هذا المدير نهائياً؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-reject" title="حذف">
                                  <i class="fa-solid fa-trash"></i>
                                </button>
                              </form>
                            </div>
                          </td>
                        </tr>
                      @empty
                        <tr>
                          <td colspan="4" class="text-center py-4 text-muted">لا يوجد مدراء في النظام حالياً</td>
                        </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- تبويب إضافة مدير جديد -->
              <div class="tab-pane fade" id="add-admin-pane" role="tabpanel" tabindex="0">
                <!-- أضفنا route و method و csrf للفورم -->
                <form action="{{ route('admins.store') }}" method="POST" id="add-admin-form" class="p-3 border rounded-3"
                  style="background-color: var(--hover-bg); border-color: var(--border-color) !important;">
                  @csrf
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label text-muted fw-bold">البريد الإلكتروني</label>
                      <input type="email" class="form-control" name="email" placeholder="example@app.com" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label text-muted fw-bold">كلمة المرور</label>
                      <input type="password" class="form-control" name="password" placeholder="أدخل كلمة المرور..."
                        minlength="6" required>
                    </div>
                    <div class="col-md-12">
                      <label class="form-label text-muted fw-bold">تحديد الصلاحية (Role)</label>
                      <select class="form-select" name="role" required>
                        <option value="" selected disabled>اختر الصلاحية المناسبة...</option>
                        <option value="Super Admin">Super Admin (مدير عام)</option>
                        <option value="Academic Manager">Academic Manager (مدير أكاديمي)</option>
                        <option value="Moderator">Moderator (مشرف)</option>
                        <option value="Support Agent">Support Agent (دعم فني)</option>
                        <option value="Data Entry">Data Entry (مدخل بيانات)</option>
                        <option value="Financial Manager">Financial Manager (مدير مالي)</option>
                      </select>
                    </div>
                  </div>

                  <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="reset" class="btn btn-outline-custom px-4 fw-bold">تفريغ</button>
                    <button type="submit" class="btn btn-warning px-5 fw-bold shadow-sm text-dark">
                      <i class="fa-solid fa-plus me-1"></i> إنشاء الحساب
                    </button>
                  </div>
                </form>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

    <!-- مودال تعديل بيانات المدير -->
    <div class="modal fade glass-modal" id="editAdminModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
          <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
            <h5 class="modal-title fw-bold text-info d-flex align-items-center gap-2">
              <i class="fa-solid fa-pen-to-square"></i> تعديل صلاحيات المدير
            </h5>
            <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <form id="edit-admin-form" method="POST">
              @csrf
              @method('PUT') <!-- مهم جداً لتحديث البيانات في لارافيل -->

              <div class="mb-3">
                <label class="form-label text-muted fw-bold">البريد الإلكتروني</label>
                <!-- الإيميل للقراءة فقط، ما بصير يتعدل -->
                <input type="email" class="form-control" id="edit-email" readonly disabled>
              </div>

              <div class="mb-3">
                <label class="form-label text-muted fw-bold">الصلاحية (Role)</label>
                <select class="form-select" name="role" id="edit-role" required>
                  <option value="Super Admin">Super Admin</option>
                  <option value="Academic Manager">Academic Manager</option>
                  <option value="Moderator">Moderator</option>
                  <option value="Support Agent">Support Agent</option>
                  <option value="Data Entry">Data Entry</option>
                  <option value="Financial Manager">Financial Manager</option>
                </select>
              </div>

              <div class="mb-4">
                <label class="form-label text-muted fw-bold">تغيير كلمة المرور (اختياري)</label>
                <input type="password" class="form-control" name="password"
                  placeholder="اتركه فارغاً إذا لم ترد تغييره..." minlength="6">
              </div>

              <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-custom px-4 fw-bold" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-info px-4 fw-bold text-white shadow-sm">حفظ التعديلات</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
@endsection

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      let oldTeacherData = null;
      let newTeacherData = null;

      function toggleButtonLoading(btnId, isLoading) {
        const btn = document.getElementById(btnId);
        if (isLoading) {
          btn.dataset.originalText = btn.innerHTML;
          btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
          btn.disabled = true;
        } else {
          btn.innerHTML = btn.dataset.originalText;
          btn.disabled = false;
        }
      }

      function buildTeacherCardHTML(data, type) {
        let statsHtml = '';
        if (type === 'old') {
          statsHtml = `
        <div class="d-flex gap-3 mt-2">
          <span class="badge border" style="background-color: var(--bg-main); color: var(--text-main); border-color: var(--border-color) !important;"><i class="fa-solid fa-users me-1"></i> ${data.classes_count || 0} شُعب</span>
          <span class="badge border" style="background-color: var(--bg-main); color: var(--text-main); border-color: var(--border-color) !important;"><i class="fa-solid fa-file-signature me-1"></i> ${data.quizzes_count || 0} كويزات</span>
        </div>
      `;
        }
        return `
      <div class="d-flex align-items-center gap-3">
        <div class="icon-wrapper teacher rounded-circle d-flex align-items-center justify-content-center shadow-sm">
          <i class="fa-solid fa-chalkboard-user fs-4"></i>
        </div>
        <div>
          <h6 class="fw-bold mb-1" style="color: var(--text-main);">${data.name || data.full_name}</h6>
          <span class="text-muted small" dir="ltr">${data.phone}</span>
          ${statsHtml}
        </div>
      </div>
    `;
      }

      function buildStudentCardHTML(data) {
        return `
      <div class="d-flex align-items-center gap-3">
        <div class="icon-wrapper student rounded-circle d-flex align-items-center justify-content-center shadow-sm">
          <i class="fa-solid fa-user-graduate fs-4"></i>
        </div>
        <div>
          <h6 class="fw-bold mb-1" style="color: var(--text-main);">${data.name}</h6>
          <span class="text-muted small" dir="ltr">${data.phone}</span>
        </div>
      </div>
    `;
      }

      function fetchTeacher(phone, type) {
        const btnId = type === 'old' ? 'search-old-btn' : 'search-new-btn';
        toggleButtonLoading(btnId, true);

        fetch(`/dashboard/teacher/search-by-phone?phone=${phone}`)
          .then(response => response.json())
          .then(data => {
            toggleButtonLoading(btnId, false);
            if (data.status === 'success') {
              if (type === 'old') {
                oldTeacherData = data.data;
                document.getElementById('old-teacher-info').innerHTML = buildTeacherCardHTML(data.data, 'old');
                document.getElementById('old-teacher-info').classList.remove('d-none');
                document.getElementById('step-new-teacher').classList.remove('d-none');
              } else {
                newTeacherData = data.data;
                document.getElementById('new-teacher-info').innerHTML = buildTeacherCardHTML(data.data, 'new');
                document.getElementById('new-teacher-info').classList.remove('d-none');
                document.getElementById('review-footer').classList.remove('d-none');
                document.getElementById('review-footer').classList.add('d-flex');
              }
            } else {
              alert(data.message || "حدث خطأ غير معروف.");
            }
          }).catch(error => {
            toggleButtonLoading(btnId, false);
            alert('حدث خطأ في الاتصال بالخادم. يرجى التأكد من اتصالك بالإنترنت.');
          });
      }

      document.getElementById('search-student-btn').addEventListener('click', () => {
        let phone = document.getElementById('student-phone-input').value.trim();
        if (!phone) return;

        toggleButtonLoading('search-student-btn', true);
        fetch(`/dashboard/reward/search-user?phone=${phone}&type=manual_student`)
          .then(response => response.json())
          .then(data => {
            toggleButtonLoading('search-student-btn', false);
            if (data.status === 'success') {
              document.getElementById('student_target_id').value = data.data.id;
              document.getElementById('student-info-container').innerHTML = buildStudentCardHTML(data.data);
              document.getElementById('student-info-container').classList.remove('d-none');
              document.getElementById('points-amount-container').classList.remove('d-none');
              document.getElementById('submit-points-btn').removeAttribute('disabled');
            } else {
              alert(data.message || "حدث خطأ غير معروف.");
              document.getElementById('student-info-container').classList.add('d-none');
              document.getElementById('points-amount-container').classList.add('d-none');
              document.getElementById('submit-points-btn').setAttribute('disabled', 'true');
            }
          }).catch(error => {
            toggleButtonLoading('search-student-btn', false);
            alert('حدث خطأ في الاتصال بالخادم.');
          });
      });

      document.getElementById('student-phone-input').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          document.getElementById('search-student-btn').click();
        }
      });

      document.getElementById('search-old-btn').addEventListener('click', () => {
        let phone = document.getElementById('old-phone-input').value.trim();
        if (phone) fetchTeacher(phone, 'old');
      });

      document.getElementById('search-new-btn').addEventListener('click', () => {
        let phone = document.getElementById('new-phone-input').value.trim();
        if (phone) fetchTeacher(phone, 'new');
      });

      document.getElementById('old-phone-input').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          document.getElementById('search-old-btn').click();
        }
      });

      document.getElementById('new-phone-input').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          document.getElementById('search-new-btn').click();
        }
      });

      document.getElementById('open-final-modal-btn').addEventListener('click', () => {
        if (oldTeacherData && newTeacherData) {
          if (oldTeacherData.id === newTeacherData.id) {
            alert("تنبيه: لا يمكن أن يكون الأستاذ البديل هو نفسه الأستاذ المُراد فصله!");
            return;
          }
          document.getElementById('hidden_old_id').value = oldTeacherData.id;
          document.getElementById('hidden_new_id').value = newTeacherData.id;
          document.getElementById('summary-old-name').innerText = oldTeacherData.name || oldTeacherData.full_name;
          document.getElementById('summary-new-name').innerText = newTeacherData.name || newTeacherData.full_name;
          document.getElementById('confirmation-input').value = '';
          document.getElementById('final-submit-btn').setAttribute('disabled', 'true');
          bootstrap.Modal.getInstance(document.getElementById('dismissTeacherStep1Modal')).hide();
          new bootstrap.Modal(document.getElementById('finalConfirmModal')).show();
        }
      });

      document.getElementById('confirmation-input').addEventListener('input', function (e) {
        let submitBtn = document.getElementById('final-submit-btn');
        if (e.target.value.trim() === 'تأكيد') {
          submitBtn.removeAttribute('disabled');
        } else {
          submitBtn.setAttribute('disabled', 'true');
        }
      });

      document.getElementById('transfer-form').addEventListener('submit', function (e) {
        if (document.getElementById('confirmation-input').value.trim() !== 'تأكيد') {
          e.preventDefault();
        } else {
          toggleButtonLoading('final-submit-btn', true);
        }
      });
    });
    function openEditAdminModal(id, email, role) {
      bootstrap.Modal.getInstance(document.getElementById('adminManagementModal')).hide();
      document.getElementById('edit-email').value = email;
      document.getElementById('edit-role').value = role;
      document.getElementById('edit-admin-form').action = `/my-space/admins/${id}`;
      new bootstrap.Modal(document.getElementById('editAdminModal')).show();
    }
  </script>