<div class="modal fade" id="studentModal{{ $student->id }}" tabindex="-1" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered modal-lg">
  <div class="modal-content glass-modal text-end" dir="rtl"
   style="border: 1px solid var(--border-color); color: var(--text-main); border-radius: 16px; overflow: hidden;">

   {{-- ترويسة المودال (مع لمسة زرقاء للطالب) --}}
   <div class="modal-header d-flex justify-content-between align-items-center"
    style="border-bottom: 1px solid var(--border-color); background-color: rgba(59, 130, 246, 0.05);">
    <div class="d-flex align-items-center gap-3">
     <div class="p-2 rounded-circle bg-soft-info d-flex align-items-center justify-content-center"
      style="width: 40px; height: 40px;">
      <i class="fa-solid fa-user-graduate text-info fs-5"></i>
     </div>
     <h5 class="modal-title fw-bold mb-0" style="color: var(--text-main);">الملف الشخصي للطالب</h5>
    </div>
    <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
   </div>

   <div class="modal-body p-4">
    <div class="row g-3 mb-4">

     {{-- بطاقة الحالة والمستوى (معلومات أساسية ومهمة) --}}
     <div class="col-12 mb-2">
      <div class="d-flex flex-wrap gap-2 align-items-center p-3 rounded"
       style="background-color: var(--bg-main); border: 1px dashed var(--border-color);">
       <span class="text-muted fw-bold me-auto">الحالة الأكاديمية:</span>
       <span class="badge bg-secondary px-3 py-2 rounded-pill">
        {{ $student->level === 'ninth' ? 'الصف التاسع' : 'البكالوريا' }}
       </span>
       <span class="badge bg-soft-info text-info px-3 py-2 rounded-pill">
        الشعبة: {{ $student->class->name ?? 'غير محددة' }}
       </span>
       @php
        $statusColors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
        $color = $statusColors[$student->status] ?? 'secondary';
        $statusNames = ['pending' => 'قيد الانتظار', 'approved' => 'مقبول', 'rejected' => 'مرفوض'];
       @endphp
       <span class="badge-status bg-soft-{{ $color }} text-{{ $color }} px-3 py-2 rounded-pill">
        حساب {{ $statusNames[$student->status] ?? $student->status }}
       </span>
      </div>
     </div>

     {{-- المعلومات الشخصية --}}
     <div class="col-md-6">
      <div class="p-3 rounded shadow-sm-hover"
       style="background-color: var(--hover-bg); border: 1px solid var(--border-color); transition: 0.2s;">
       <div class="d-flex align-items-center gap-2 mb-2 text-muted">
        <i class="fa-regular fa-id-card"></i> <small>الاسم الكامل</small>
       </div>
       <strong style="color: var(--text-main); font-size: 1.1rem;">{{ $student->fullname }}</strong>
      </div>
     </div>
     <div class="col-md-6">
      <div class="p-3 rounded shadow-sm-hover"
       style="background-color: var(--hover-bg); border: 1px solid var(--border-color); transition: 0.2s;">
       <div class="d-flex align-items-center gap-2 mb-2 text-muted">
        <i class="fa-solid fa-user-tie"></i> <small>اسم الأب</small>
       </div>
       <strong style="color: var(--text-main); font-size: 1.1rem;">{{ $student->fathersname ?? '-' }}</strong>
      </div>
     </div>

     {{-- معلومات التواصل --}}
     <div class="col-md-6">
      <div class="p-3 rounded shadow-sm-hover"
       style="background-color: var(--hover-bg); border: 1px solid var(--border-color); transition: 0.2s;">
       <div class="d-flex align-items-center gap-2 mb-2 text-muted">
        <i class="fa-solid fa-mobile-screen"></i> <small>رقم هاتف الطالب</small>
       </div>
       <strong style="color: var(--text-main); font-size: 1.1rem;" dir="ltr">{{ $student->phone }}</strong>
      </div>
     </div>
     <div class="col-md-6">
      <div class="p-3 rounded shadow-sm-hover"
       style="background-color: var(--hover-bg); border: 1px solid var(--border-color); transition: 0.2s;">
       <div class="d-flex align-items-center gap-2 mb-2 text-muted">
        <i class="fa-solid fa-phone-flip"></i> <small>رقم ولي الأمر</small>
       </div>
       <strong style="color: var(--text-main); font-size: 1.1rem;" dir="ltr">{{ $student->parent_phone }}</strong>
      </div>
     </div>

     {{-- النقاط --}}
     {{-- <div class="col-md-6">
      <div class="p-3 rounded shadow-sm-hover"
       style="background-color: var(--hover-bg); border: 1px solid var(--border-color); transition: 0.2s;">
       <div class="d-flex align-items-center gap-2 mb-2 text-muted">
        <i class="fa-solid fa-star text-warning"></i> <small>النقاط الحالية</small>
       </div>
       <strong style="color: var(--text-main); font-size: 1.1rem;">{{ $student->points }} نقطة</strong>
      </div>
     </div>
     <div class="col-md-6">
      <div class="p-3 rounded shadow-sm-hover"
       style="background-color: var(--hover-bg); border: 1px solid var(--border-color); transition: 0.2s;">
       <div class="d-flex align-items-center gap-2 mb-2 text-muted">
        <i class="fa-solid fa-trophy text-success"></i> <small>إجمالي النقاط المكتسبة</small>
       </div>
       <strong style="color: var(--text-main); font-size: 1.1rem;">{{ $student->total_earned_points }} نقطة</strong>
      </div>
     </div> --}}

     {{-- زر عرض المستند الثبوتي --}}
     @if($student->DocumentaryEvidence)
      <div class="col-12 mt-3">
       <a href="{{ $student->DocumentaryEvidence }}" target="_blank" class="btn btn-outline-info w-100 py-2 fw-bold"
        style="border-radius: 8px;">
        <i class="fa-solid fa-file-image me-2"></i> عرض المستند الثبوتي
       </a>
      </div>
     @endif

     {{-- قسم العقوبات --}}
     <div class="col-12 mt-4">
      <hr style="border-color: var(--border-color);">
      <button class="btn btn-outline-danger w-100 py-2 d-flex justify-content-center align-items-center gap-2 fw-bold"
       type="button" data-bs-toggle="collapse" data-bs-target="#punishStudent{{ $student->id }}" aria-expanded="false"
       style="border-radius: 8px;">
       <i class="fa-solid fa-gavel"></i> <span>إدارة العقوبات</span>
      </button>

      <div class="collapse mt-3" id="punishStudent{{ $student->id }}">
       <div class="p-4 rounded" style="background-color: var(--bg-main); border: 1px solid var(--border-color);">
        <form action="{{ route('punishments.apply') }}" method="POST">
         @csrf
         <input type="hidden" name="punishable_id" value="{{ $student->id }}">
         <input type="hidden" name="punishable_type" value="App\Models\Student">

         <h6 class="fw-bold mb-3 text-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i>تطبيق عقوبة جديدة
         </h6>

         <div class="mb-3 text-start" dir="rtl">
          <label class="form-label text-muted">اختر نوع العقوبة</label>
          <select name="punishment_id" class="form-select bg-transparent text-main border-color" required>
           <option value="">-- اختر من القائمة --</option>
           @foreach($punishments as $punishment)
            <option value="{{ $punishment->id }}">{{ $punishment->description }}</option>
           @endforeach
          </select>
         </div>

         <div class="mb-3 text-start" dir="rtl">
          <label class="form-label text-muted">المدة بالدقائق (اختياري)</label>
          <input type="number" name="duration_minutes" class="form-control bg-transparent text-main border-color"
           placeholder="اتركه فارغاً لاعتماد المدة الافتراضية">
         </div>

         <button type="submit" class="btn btn-danger w-100 fw-bold" style="border-radius: 8px;">تأكيد وفرض
          العقوبة</button>
        </form>
       </div>
      </div>
     </div>

    </div>
   </div>
  </div>
 </div>
</div>