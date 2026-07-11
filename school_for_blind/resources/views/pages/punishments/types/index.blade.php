@extends('layouts.app')

@section('content')
 <div class="container-fluid p-0">
  <div class="d-flex justify-content-between align-items-center mb-4">
   <h4 class="fw-bold" style="color: var(--text-main);">أنواع العقوبات المسجلة في النظام</h4>
   <a href="{{ route('content.monitor') }}" class="btn btn-outline-secondary">
    <i class="fa-solid fa-arrow-right me-2"></i> رجوع
   </a>
  </div>

  <div class="row g-4">
   {{-- جدول عرض العقوبات المتاحة (أصبح يأخذ العرض كاملاً) --}}
   <div class="col-12">
    <div class="custom-card h-100">
     <div class="table-responsive">
      <table class="table table-hover-custom align-middle mb-0" style="color: var(--text-main);">
       <thead>
        <tr style="border-bottom: 2px solid var(--border-color);">
         <th class="pb-3 text-muted fw-normal">اسم العقوبة</th>
         <th class="pb-3 text-muted fw-normal text-center">المستوى</th>
         <th class="pb-3 text-muted fw-normal text-center">المدة الافتراضية</th>
         <th class="pb-3 text-muted fw-normal">الوصف</th>
        </tr>
       </thead>
       <tbody>
        @forelse($punishments as $punishment)
         <tr style="border-bottom: 1px solid var(--border-color);">
          <td class="py-3 fw-bold">{{ $punishment->name }}</td>
          <td class="py-3 text-center">
           <span class="badge-status bg-soft-info text-info">{{ $punishment->level }}</span>
          </td>
          <td class="py-3 text-center text-muted">
           {{ $punishment->duration_minutes ? $punishment->duration_minutes . ' دقيقة' : 'غير محدد' }}
          </td>
          <td class="py-3 text-muted"
           style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
           {{ $punishment->description ?? '-' }}
          </td>
         </tr>
        @empty
         <tr>
          <td colspan="4" class="text-center py-4 text-muted">لا توجد عقوبات مسجلة في النظام حالياً.</td>
         </tr>
        @endforelse
       </tbody>
      </table>
     </div>
    </div>
   </div>
  </div>
 </div>
@endsection