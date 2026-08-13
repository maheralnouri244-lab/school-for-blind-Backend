<div class="row g-4 text-end" dir="rtl">

 <div class="col-12">
  <h6 class="fw-bold mb-3" style="color: #3b82f6;"><i class="fa-solid fa-address-card me-2"></i>بيانات الطالب الأساسية
  </h6>
  <div class="row g-3 p-4 shadow-sm"
   style="background-color: var(--bg-main); border: 1px solid var(--border-color); border-radius: 16px;">
   <div class="col-md-6">
    <label class="text-muted fw-bold small d-block mb-1">الاسم الكامل</label>
    <h6 class="fw-bold m-0" style="color: var(--text-main);">{{ $user->fullname }}</h6>
   </div>
   <div class="col-md-6">
    <label class="text-muted fw-bold small d-block mb-1">اسم الأب</label>
    <h6 class="fw-bold m-0" style="color: var(--text-main);">{{ $user->fathersname ?? '-' }}</h6>
   </div>
   <div class="col-md-6 mt-4">
    <label class="text-muted fw-bold small d-block mb-1">رقم هاتف الطالب</label>
    <h6 class="fw-bold m-0" dir="ltr" style="color: var(--text-main);">{{ $user->phone }}</h6>
   </div>
   <div class="col-md-6 mt-4">
    <label class="text-muted fw-bold small d-block mb-1">رقم ولي الأمر</label>
    <h6 class="fw-bold m-0" dir="ltr" style="color: var(--text-main);">{{ $user->parent_phone }}</h6>
   </div>
   <div class="col-md-6 mt-4">
    <label class="text-muted fw-bold small d-block mb-2">المستوى الدراسي</label>
    <span class="badge bg-soft-info text-info rounded-pill px-3 py-2 shadow-sm fw-bold">
     {{ $user->level == 'twelfth' ? 'بكالوريا' : 'تاسع' }}
    </span>
   </div>
   <div class="col-md-6 mt-4">
    <label class="text-muted fw-bold small d-block mb-2">الشعبة</label>
    <span class="badge px-3 py-2 rounded-pill fw-bold shadow-sm"
     style="background-color: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-main);">
     {{ $user->class->name ?? 'غير محدد' }}
    </span>
   </div>
  </div>
 </div>

 <div class="col-12">
  <h6 class="fw-bold mb-3" style="color: #3b82f6;"><i class="fa-solid fa-star me-2"></i>النقاط والمكافآت</h6>
  <div class="row g-3 p-4 shadow-sm"
   style="background-color: var(--bg-main); border: 1px solid var(--border-color); border-radius: 16px;">
   <div class="col-md-6">
    <label class="text-muted fw-bold small d-block mb-2">النقاط الحالية</label>
    <h5 class="fw-bold m-0 text-info">{{ $user->points }} <span class="fs-6 text-muted">نقطة</span></h5>
   </div>
   <div class="col-md-6">
    <label class="text-muted fw-bold small d-block mb-2">إجمالي النقاط المكتسبة</label>
    <h5 class="fw-bold m-0 text-info">{{ $user->total_earned_points }} <span class="fs-6 text-muted">نقطة</span></h5>
   </div>
  </div>
 </div>

 <div class="col-12">
  <h6 class="fw-bold mb-3" style="color: #3b82f6;"><i class="fa-solid fa-file-lines me-2"></i>الوثائق الثبوتية</h6>
  <div class="p-4 shadow-sm"
   style="background-color: var(--bg-main); border: 1px solid var(--border-color); border-radius: 16px;">
   @if($user->DocumentaryEvidence)
    <a href="{{ asset('storage/' . $user->DocumentaryEvidence) }}" target="_blank"
     class="btn btn-outline-custom w-100 py-2 fw-bold shadow-sm rounded-3 d-flex align-items-center justify-content-center gap-2">
     <i class="fa-solid fa-file-arrow-down text-info"></i>
     عرض الوثيقة المرفقة
    </a>
   @else
    <span class="text-muted small fw-bold px-3 py-2 rounded-pill d-block text-center"
     style="background-color: var(--bg-card); border: 1px dashed var(--border-color);">لا يوجد وثائق مرفقة</span>
   @endif
  </div>
 </div>

 <div class="col-12 mt-4 pt-4 border-top d-flex flex-column gap-3"
  style="border-color: var(--border-color) !important;">
  @if($user->status !== 'pending')
   <button type="button"
    class="btn btn-primary w-100 fw-bold py-2 shadow-sm rounded-3 d-flex align-items-center justify-content-center gap-2"
    data-id="{{ $user->id }}" data-fullname="{{ $user->fullname }}" data-fathersname="{{ $user->fathersname ?? '' }}"
    data-phone="{{ $user->phone }}" data-parent="{{ $user->parent_phone }}" data-level="{{ $user->level }}"
    data-class="{{ $user->class_id }}" onclick="openEditStudentModal(this)">
    <i class="fa-solid fa-pen-to-square"></i>
    تعديل بيانات الطالب
   </button>
  @endif

  <button type="button"
   class="btn btn-reject w-100 fw-bold py-2 shadow-sm rounded-3 d-flex align-items-center justify-content-center gap-2"
   onclick="fetchUserPunishments('student', {{ $user->id }})">
   <i class="fa-solid fa-user-shield"></i>
   عرض سجل العقوبات
  </button>
 </div>

 <input type="hidden" id="current_user_status" value="{{ $user->status }}">
</div>