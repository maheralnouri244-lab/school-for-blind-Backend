@forelse($excuses as $excuse)
 <div class="card mb-3 border-0 shadow-sm" style="background-color: var(--bg-main);">
  <div class="card-body">
   <div class="d-flex justify-content-between align-items-center mb-2">
    <h6 class="fw-bold mb-0" style="color: var(--text-main);">
     <i class="fa-solid fa-headset text-primary me-1"></i>
     مكالمة: {{ $excuse->room->name ?? 'غير محدد' }}
    </h6>
    <small class="text-muted" dir="ltr">{{ $excuse->created_at->format('Y-m-d H:i') }}</small>
   </div>

   <p class="mb-2" style="color: var(--text-muted);">
    <strong>السبب:</strong> {{ $excuse->reason ?? 'لم يتم كتابة سبب.' }}
   </p>

   <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top"
    style="border-color: var(--border-color) !important;">

    {{-- عرض حالة الطلب بناءً على الحالات الـ 3 الجديدة --}}
    <div>
     @if($excuse->status == 'pending')
      <span class="badge bg-soft-warning text-warning">بانتظار الإدارة</span>
     @elseif($excuse->status == 'approved')
      <span class="badge bg-soft-success text-success">مقبول إدارياً</span>
     @elseif($excuse->status == 'rejected')
      <span class="badge bg-soft-danger text-danger">مرفوض إدارياً</span>
     @endif
    </div>

    {{-- أزرار التحكم تظهر فقط إذا كان الطلب قيد الانتظار pending --}}
    @if($excuse->status == 'pending')
     <div class="d-flex gap-2">
      {{-- تم تعديل الحالة المرسلة لتكون approved --}}
      <button class="btn btn-sm btn-success fw-bold px-3"
       onclick="updateExcuseStatus({{ $excuse->id }}, 'approved', {{ $excuse->student_id }})">
       <i class="fa-solid fa-check"></i> قبول
      </button>

      {{-- تم تعديل الحالة المرسلة لتكون rejected --}}
      <button class="btn btn-sm btn-danger fw-bold px-3"
       onclick="updateExcuseStatus({{ $excuse->id }}, 'rejected', {{ $excuse->student_id }})">
       <i class="fa-solid fa-xmark"></i> رفض
      </button>
     </div>
    @endif

   </div>
  </div>
 </div>
@empty
 <div class="text-center py-4">
  <i class="fa-solid fa-box-open fs-1 text-muted mb-3 opacity-50"></i>
  <h6 class="text-muted">لا يوجد تذاكر غياب لهذا الطالب.</h6>
 </div>
@endforelse