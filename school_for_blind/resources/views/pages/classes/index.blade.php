@extends('layouts.app')

@section('content')
 <div class="container-fluid p-0">
  <div class="d-flex justify-content-between align-items-center mb-4">
   <h4 class="fw-bold" style="color: var(--text-main);">إدارة الصفوف والشعب</h4>
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
@endsection