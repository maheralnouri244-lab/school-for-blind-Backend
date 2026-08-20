@extends('layouts.app')

@section('content')
 <div class="container-fluid p-0">
  <div class="d-flex justify-content-between align-items-center mb-4">
   <h4 class="fw-bold" style="color: var(--text-main);">إدارة الصفوف والشعب</h4>

   {{-- زر إضافة شعبة جديدة --}}
   <button type="button" class="btn btn-primary fw-bold px-4 shadow-sm" data-bs-toggle="modal"
    data-bs-target="#addClassModal">
    <i class="fa-solid fa-plus me-2"></i> إضافة شعبة
   </button>
  </div>

  <div class="row g-4">
   @forelse($classes as $class)
    <div class="col-lg-4 col-md-6">
     <div class="custom-card h-100 d-flex flex-column justify-content-between shadow-sm-hover transition-transform"
      style="transform: translateY(0); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'"
      onmouseout="this.style.transform='translateY(0)'">

      {{-- الهيدر تبع الكرت --}}
      <div class="d-flex justify-content-between align-items-start mb-4">
       <div>
        <h5 class="fw-bold mb-1" style="color: var(--text-main);">
         {{ $class->name }}
        </h5>
        <span class="badge bg-soft-info text-info px-2 py-1">
         {{ $class->level == 'twelfth' ? 'البكالوريا' : 'التاسع' }}
        </span>
       </div>
       <div class="p-3 rounded d-flex align-items-center justify-content-center bg-soft-success"
        style="width: 50px; height: 50px;">
        <i class="fa-solid fa-chalkboard-user fs-4 text-success"></i>
       </div>
      </div>

      {{-- إحصائيات الشعبة --}}
      <div class="d-flex justify-content-between mb-4 border-top pt-3"
       style="border-color: var(--border-color) !important;">
       <div class="text-center">
        <span class="text-muted d-block small mb-1">الطلاب</span>
        <h6 class="fw-bold mb-0" style="color: var(--text-main);">{{ $class->students_count }}</h6>
       </div>
       <div class="text-center border-start border-end px-3" style="border-color: var(--border-color) !important;">
        <span class="text-muted d-block small mb-1">الأساتذة</span>
        <h6 class="fw-bold mb-0" style="color: var(--text-main);">{{ $class->teachers_count }}</h6>
       </div>
       <div class="text-center">
        <span class="text-muted d-block small mb-1">رقم الشعبة</span>
        <h6 class="fw-bold mb-0" style="color: var(--text-main);">{{ $class->number }}</h6>
       </div>
      </div>

      {{-- الأزرار --}}
      <div class="d-flex gap-2 mt-auto">
       <a href="{{ route('classes.show', $class->id) }}"
        class="btn flex-grow-1 py-2 fw-bold d-flex align-items-center justify-content-center gap-2 transition-all shadow-sm"
        style="background-color: var(--accent-color); color: #111827; border: none; border-radius: 8px; font-size: 0.9rem;">
        <i class="fa-regular fa-folder-open"></i> التفاصيل
       </a>

       {{-- زر الحذف مع التحقق من عدد الطلاب --}}
       @if($class->students_count == 0)
        <form action="{{ route('classes.destroy', $class->id) }}" method="POST" class="m-0"
         onsubmit="return confirm('هل أنت متأكد من حذف هذه الشعبة نهائياً؟ سيتم فك ارتباط الأساتذة بها.');">
         @csrf
         @method('DELETE')
         <button type="submit" class="btn btn-outline-danger py-2 px-3 h-100" style="border-radius: 8px;"
          title="حذف الشعبة">
          <i class="fa-solid fa-trash"></i>
         </button>
        </form>
       @else
        {{-- زر معطل لأن الشعبة تحتوي على طلاب --}}
        <button type="button" class="btn btn-outline-secondary py-2 px-3 h-100 opacity-50" style="border-radius: 8px;"
         title="لا يمكن الحذف - الشعبة تحتوي على طلاب" disabled>
         <i class="fa-solid fa-trash"></i>
        </button>
       @endif

      </div>

     </div>
    </div>
   @empty
    <div class="col-12 text-center py-5">
     <i class="fa-solid fa-folder-open fs-1 text-muted mb-3 d-block" style="opacity: 0.5;"></i>
     <h5 class="text-muted">لا يوجد شعب مضافة حالياً.</h5>
    </div>
   @endforelse
  </div>
 </div>

 {{-- مودال إضافة شعبة جديدة --}}
 <div class="modal fade glass-modal" id="addClassModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered text-end" dir="rtl">
   <div class="modal-content border-0 shadow-lg rounded-4">
    <div class="modal-header border-bottom pb-3" style="border-color: var(--border-color) !important;">
     <div class="d-flex align-items-center gap-2">
      <div class="p-2 rounded-circle bg-soft-primary d-flex align-items-center justify-content-center"
       style="width: 40px; height: 40px;">
       <i class="fa-solid fa-layer-group text-primary fs-5"></i>
      </div>
      <div>
       <h5 class="modal-title fw-bold mb-0" style="color: var(--text-main);">إضافة شعبة جديدة</h5>
      </div>
     </div>
     <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <form action="{{ route('classes.store') }}" method="POST">
     @csrf
     <div class="modal-body py-4">

      <div class="mb-3">
       <label class="form-label text-muted fw-bold">المستوى الدراسي</label>
       <select class="form-select" name="level" required
        style="background-color: var(--bg-main); color: var(--text-main);">
        <option value="" disabled selected>-- اختر المستوى --</option>
        <option value="ninth">التاسع</option>
        <option value="twelfth">البكالوريا</option>
       </select>
      </div>

      <div class="mb-3">
       <label class="form-label text-muted fw-bold">اسم الشعبة</label>
       <input type="text" class="form-control" name="name" placeholder="مثال: الشعبة الأولى" required
        style="background-color: var(--bg-main); color: var(--text-main);">
      </div>

      <div class="mb-3">
       <label class="form-label text-muted fw-bold">رقم الشعبة</label>
       <input type="number" class="form-control" name="number" min="1" placeholder="مثال: 1" required
        style="background-color: var(--bg-main); color: var(--text-main);">
      </div>

     </div>

     <div class="modal-footer border-top d-flex justify-content-between"
      style="border-color: var(--border-color) !important;">
      <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">إلغاء</button>
      <button type="submit" class="btn btn-primary px-4 fw-bold text-white shadow-sm">
       <i class="fa-solid fa-check me-2"></i> حفظ الشعبة
      </button>
     </div>
    </form>
   </div>
  </div>
 </div>
@endsection