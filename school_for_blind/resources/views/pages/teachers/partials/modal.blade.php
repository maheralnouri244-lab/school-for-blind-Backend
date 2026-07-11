<div class="modal fade" id="teacherModal{{ $teacher->id }}" tabindex="-1" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered modal-lg">
  <div class="modal-content glass-modal text-end" dir="rtl"
   style="border: 1px solid var(--border-color); color: var(--text-main); border-radius: 16px; overflow: hidden;">

   {{-- ترويسة المودال (مع لمسة فسفورية للمعلم) --}}
   <div class="modal-header d-flex justify-content-between align-items-center"
    style="border-bottom: 1px solid var(--border-color); background-color: rgba(163, 230, 53, 0.05);">
    <div class="d-flex align-items-center gap-3">
     <div class="p-2 rounded-circle d-flex align-items-center justify-content-center"
      style="width: 40px; height: 40px; background-color: rgba(163, 230, 53, 0.15);">
      <i class="fa-solid fa-person-chalkboard fs-5" style="color: var(--accent-color);"></i>
     </div>
     <h5 class="modal-title fw-bold mb-0" style="color: var(--text-main);">الملف الشخصي للمعلم</h5>
    </div>
    <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
   </div>

   <div class="modal-body p-4">
    <div class="row g-3 mb-4">

     {{-- بطاقة الحالة والمستوى --}}
     <div class="col-12 mb-2">
      <div class="d-flex flex-wrap gap-2 align-items-center p-3 rounded"
       style="background-color: var(--bg-main); border: 1px dashed var(--border-color);">
       <span class="text-muted fw-bold me-auto">المستوى والحالة:</span>
       <span class="badge bg-secondary px-3 py-2 rounded-pill">
        تدريس {{ $teacher->level === 'ninth' ? 'التاسع' : 'البكالوريا' }}
       </span>
       @php
        $statusColors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger', 'suspended' => 'danger'];
        $color = $statusColors[$teacher->status] ?? 'secondary';
        $statusNames = ['pending' => 'قيد الانتظار', 'approved' => 'مقبول النشاط', 'rejected' => 'مرفوض', 'suspended' => 'موقوف'];
       @endphp
       <span class="badge-status bg-soft-{{ $color }} text-{{ $color }} px-3 py-2 rounded-pill">
        حساب {{ $statusNames[$teacher->status] ?? $teacher->status }}
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
       <strong style="color: var(--text-main); font-size: 1.1rem;">{{ $teacher->full_name }}</strong>
      </div>
     </div>
     <div class="col-md-6">
      <div class="p-3 rounded shadow-sm-hover"
       style="background-color: var(--hover-bg); border: 1px solid var(--border-color); transition: 0.2s;">
       <div class="d-flex align-items-center gap-2 mb-2 text-muted">
        <i class="fa-solid fa-mobile-screen"></i> <small>رقم الهاتف</small>
       </div>
       <strong style="color: var(--text-main); font-size: 1.1rem;" dir="ltr">{{ $teacher->phone }}</strong>
      </div>
     </div>

     {{-- المواد والصفوف --}}
     <div class="col-12">
      <div class="p-3 rounded shadow-sm-hover"
       style="background-color: var(--hover-bg); border: 1px solid var(--border-color); transition: 0.2s;">
       <div class="d-flex align-items-center gap-2 mb-3 text-muted">
        <i class="fa-solid fa-book-open"></i> <small>المواد التي يدرسها</small>
       </div>
       <div class="d-flex flex-wrap gap-2">
        @forelse($teacher->subjects()->get() as $subject)
         <span class="badge px-3 py-2"
          style="background-color: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color);">
          {{ $subject->name ?? 'مادة' }} <span class="ms-1 text-success">({{ $subject->pivot->price_for_lesson ?? 0 }}
           ل.س)</span>
         </span>
        @empty
         <span class="text-muted">لا يوجد مواد مرتبطة</span>
        @endforelse
       </div>
      </div>
     </div>

     <div class="col-12">
      <div class="p-3 rounded shadow-sm-hover"
       style="background-color: var(--hover-bg); border: 1px solid var(--border-color); transition: 0.2s;">
       <div class="d-flex align-items-center gap-2 mb-3 text-muted">
        <i class="fa-solid fa-users-rectangle"></i> <small>الصفوف المرتبطة</small>
       </div>
       <div class="d-flex flex-wrap gap-2">
        @forelse($teacher->classes as $class)
         <span class="badge bg-secondary px-3 py-2 rounded-pill">{{ $class->name ?? 'صف' }}</span>
        @empty
         <span class="text-muted">لا يوجد صفوف مرتبطة</span>
        @endforelse
       </div>
      </div>
     </div>

     {{-- زر السيرة الذاتية --}}
     @if($teacher->cv_path)
      <div class="col-12 mt-3">
       <a href="{{ asset('storage/' . $teacher->cv_path) }}" target="_blank"
        class="btn btn-outline-success w-100 py-2 fw-bold" style="border-radius: 8px;">
        <i class="fa-solid fa-file-arrow-down me-2"></i> تحميل وعرض السيرة الذاتية
       </a>
      </div>
     @endif

     {{-- قسم العقوبات --}}
     <div class="col-12 mt-4">
      <hr style="border-color: var(--border-color);">
      <button class="btn btn-outline-danger w-100 py-2 d-flex justify-content-center align-items-center gap-2 fw-bold"
       type="button" data-bs-toggle="collapse" data-bs-target="#punishStudent{{ $teacher->id }}" aria-expanded="false"
       style="border-radius: 8px;">
       <i class="fa-solid fa-gavel"></i> <span>إدارة العقوبات</span>
      </button>

      <div class="collapse mt-3" id="punishTeacher{{ $teacher->id }}">
       <div class="p-4 rounded" style="background-color: var(--bg-main); border: 1px solid var(--border-color);">
        <form action="{{ route('punishments.apply') }}" method="POST">
         @csrf
         <input type="hidden" name="punishable_id" value="{{ $teacher->id }}">
         <input type="hidden" name="punishable_type" value="App\Models\Teacher">

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