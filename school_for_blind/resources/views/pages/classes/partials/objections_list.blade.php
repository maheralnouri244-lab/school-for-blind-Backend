@forelse($objections as $objection)
 <div class="card mb-3 border-0 shadow-sm" style="background-color: var(--bg-main);">
  <div class="card-body">
   <div class="d-flex justify-content-between align-items-center mb-2">
    <h6 class="fw-bold mb-0" style="color: var(--text-main);">
     <i class="fa-solid fa-shield-halved text-warning me-1"></i>
     عقوبة: {{ $objection->punishableRecord->punishment->name ?? 'غير محدد' }}
    </h6>
    <small class="text-muted" dir="ltr">{{ $objection->created_at->format('Y-m-d H:i') }}</small>
   </div>

   <p class="mb-2" style="color: var(--text-muted);">
    <strong>سبب الاعتراض:</strong> {{ $objection->reason ?? 'لم يتم كتابة سبب.' }}
   </p>

   <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top"
    style="border-color: var(--border-color) !important;">

    <div>
     @if($objection->status == 'pending')
      <span class="badge bg-soft-warning text-warning">بانتظار الإدارة</span>
     @elseif($objection->status == 'approved')
      <span class="badge bg-soft-success text-success">مقبول (مُعفى)</span>
     @elseif($objection->status == 'rejected')
      <span class="badge bg-soft-danger text-danger">مرفوض (العقوبة سارية)</span>
     @endif
    </div>

    @if($objection->status == 'pending')
     <div class="d-flex gap-2">
      <button class="btn btn-sm btn-success fw-bold px-3"
       onclick="updateObjectionStatus({{ $objection->id }}, 'approved', {{ $objection->student_id }})">
       <i class="fa-solid fa-check"></i> قبول
      </button>

      <button class="btn btn-sm btn-danger fw-bold px-3"
       onclick="updateObjectionStatus({{ $objection->id }}, 'rejected', {{ $objection->student_id }})">
       <i class="fa-solid fa-xmark"></i> رفض
      </button>
     </div>
    @endif

   </div>
  </div>
 </div>
@empty
 <div class="text-center py-4">
  <i class="fa-solid fa-shield-blank fs-1 text-muted mb-3 opacity-50"></i>
  <h6 class="text-muted">لا يوجد اعتراضات مسجلة لهذا الطالب.</h6>
 </div>
@endforelse