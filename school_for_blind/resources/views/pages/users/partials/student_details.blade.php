<div class="row g-4 text-end" dir="rtl">
 <div class="col-md-6">
  <label class="text-muted small d-block">الاسم الكامل</label>
  <h6 class="fw-bold">{{ $user->fullname }}</h6>
 </div>
 <div class="col-md-6">
  <label class="text-muted small d-block">اسم الأب</label>
  <h6 class="fw-bold">{{ $user->fathersname ?? '-' }}</h6>
 </div>
 <div class="col-md-6">
  <label class="text-muted small d-block">رقم هاتف الطالب</label>
  <h6 class="fw-bold" dir="ltr">{{ $user->phone }}</h6>
 </div>
 <div class="col-md-6">
  <label class="text-muted small d-block">رقم ولي الأمر</label>
  <h6 class="fw-bold" dir="ltr">{{ $user->parent_phone }}</h6>
 </div>
 <div class="col-md-6">
  <label class="text-muted small d-block">المستوى الدراسي</label>
  <span class="badge bg-primary">{{ $user->level == 'twelfth' ? 'بكالوريا' : 'تاسع' }}</span>
 </div>
 <div class="col-md-6">
  <label class="text-muted small d-block">الشعبة</label>
  <span class="badge bg-secondary">{{ $user->class->name ?? 'غير محدد' }}</span>
 </div>
 <div class="col-md-6">
  <label class="text-muted small d-block">النقاط الحالية</label>
  <h6 class="text-success fw-bold">{{ $user->points }} نقطة</h6>
 </div>
 <div class="col-md-6">
  <label class="text-muted small d-block">إجمالي النقاط المكتسبة</label>
  <h6 class="text-success fw-bold">{{ $user->total_earned_points }} نقطة</h6>
 </div>
 <div class="col-12">
  <label class="text-muted small d-block mb-2">الوثائق الثبوتية</label>
  @if($user->DocumentaryEvidence)
   <a href="{{ asset('storage/' . $user->DocumentaryEvidence) }}" target="_blank"
    class="btn btn-sm btn-outline-info w-100">
    عرض الوثيقة المرفقة
   </a>
  @else
   <span class="text-muted">لا يوجد</span>
  @endif
 </div>
 {{-- الإجراءات (تظهر فقط إذا تم القبول أو الرفض) --}}
 <div class="col-12 mt-3 pt-3 border-top d-flex flex-column gap-2">
  @if($user->status !== 'pending')
   <button type="button" class="btn btn-primary w-100 fw-bold" data-id="{{ $user->id }}"
    data-fullname="{{ $user->fullname }}" data-fathersname="{{ $user->fathersname ?? '' }}"
    data-phone="{{ $user->phone }}" data-parent="{{ $user->parent_phone }}" data-level="{{ $user->level }}"
    data-class="{{ $user->class_id }}" onclick="openEditStudentModal(this)">
    <i class="fa-solid fa-pen-to-square ms-2"></i> تعديل بيانات الطالب
   </button>

   <button type="button" class="btn btn-outline-danger w-100 fw-bold"
    onclick="fetchUserPunishments('student', {{ $user->id }})">
    <i class="fa-solid fa-gavel me-2"></i>عرض سجل العقوبات
   </button>
  @endif
 </div>

 <input type="hidden" id="current_user_status" value="{{ $user->status }}">
</div>