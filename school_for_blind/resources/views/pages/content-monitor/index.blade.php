@extends('layouts.app')

@section('content')
 <div class="container-fluid p-0">
  <div class="d-flex justify-content-between align-items-center mb-4">
   <h4 class="fw-bold" style="color: var(--text-main);">لوحة مراقب المحتوى</h4>
  </div>

  <div class="row g-4">
   {{-- بطاقة إدارة البلاغات --}}
   <div class="col-lg-4 col-md-6">
    <a href="{{ route('reports.index') }}" class="text-decoration-none d-block">
     <div class="custom-card d-flex align-items-center justify-content-between shadow-sm-hover transition-transform"
      style="transform: translateY(0); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'"
      onmouseout="this.style.transform='translateY(0)'">
      <div>
       <h5 class="fw-bold mb-2" style="color: var(--text-main);">إدارة البلاغات</h5>
       <p class="text-muted mb-0">مراجعة ومعالجة البلاغات الجديدة</p>
      </div>
      <div class="p-3 rounded-circle bg-soft-warning">
       <i class="fa-solid fa-flag text-warning fs-3"></i>
      </div>
     </div>
    </a>
   </div>

   {{-- بطاقة سجل العقوبات النشطة --}}
   <div class="col-lg-4 col-md-6">
    <a href="{{ route('punishments.active') }}" class="text-decoration-none d-block">
     <div class="custom-card d-flex align-items-center justify-content-between shadow-sm-hover transition-transform"
      style="transform: translateY(0); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'"
      onmouseout="this.style.transform='translateY(0)'">
      <div>
       <h5 class="fw-bold mb-2" style="color: var(--text-main);">سجل العقوبات</h5>
       <p class="text-muted mb-0">عرض المستخدمين المعاقبين حالياً</p>
      </div>
      <div class="p-3 rounded-circle bg-soft-danger">
       <i class="fa-solid fa-ban text-danger fs-3"></i>
      </div>
     </div>
    </a>
   </div>

   {{-- بطاقة أنواع العقوبات --}}
   <div class="col-lg-4 col-md-6">
    <a href="{{ route('punishments.types.index') }}" class="text-decoration-none d-block">
     <div class="custom-card d-flex align-items-center justify-content-between shadow-sm-hover transition-transform"
      style="transform: translateY(0); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'"
      onmouseout="this.style.transform='translateY(0)'">
      <div>
       <h5 class="fw-bold mb-2" style="color: var(--text-main);">أنواع العقوبات</h5>
       <p class="text-muted mb-0">عرض العقوبات المتاحة </p>
      </div>
      <div class="p-3 rounded-circle bg-soft-info">
       <i class="fa-solid fa-gavel text-info fs-3"></i>
      </div>
     </div>
    </a>
   </div>

   {{-- بطاقة مراقبة المحادثات --}}
   <div class="col-lg-4 col-md-6">
    <a href="{{ route('dashboard.conversations.index') }}" class="text-decoration-none d-block">
     <div class="custom-card d-flex align-items-center justify-content-between shadow-sm-hover transition-transform"
      style="transform: translateY(0); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'"
      onmouseout="this.style.transform='translateY(0)'">
      <div>
       <h5 class="fw-bold mb-2" style="color: var(--text-main);">مراقبة المحادثات</h5>
       <p class="text-muted mb-0">مراقبة قنوات الأساتذة ومجموعات النقاش</p>
      </div>
      <div class="p-3 rounded-circle bg-soft-info">
       <i class="fa-solid fa-comments text-info fs-3"></i>
      </div>
     </div>
    </a>
   </div>

   {{-- بطاقة إدارة الكويزات --}}
   <div class="col-lg-4 col-md-6">
    <a href="{{ route('dashboard.quizzes.index') }}" class="text-decoration-none d-block">
     <div class="custom-card d-flex align-items-center justify-content-between shadow-sm-hover transition-transform"
      style="transform: translateY(0); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'"
      onmouseout="this.style.transform='translateY(0)'">
      <div>
       <h5 class="fw-bold mb-2" style="color: var(--text-main);">إدارة الكويزات</h5>
       <p class="text-muted mb-0">استعراض وتعديل وإعادة تصحيح الكويزات</p>
      </div>
      <div class="p-3 rounded-circle" style="background-color: rgba(163, 230, 53, 0.15);">
       <i class="fa-solid fa-clipboard-question fs-3" style="color: var(--accent-color);"></i>
      </div>
     </div>
    </a>
   </div>
  </div>
 </div>


@endsection