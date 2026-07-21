@extends('layouts.app')

@section('content')
 <div class="container-fluid p-0">
  <div class="d-flex justify-content-between align-items-center mb-4">
   <h4 class="fw-bold" style="color: var(--text-main);">سجل العقوبات النشطة</h4>
   <a href="{{ route('content.monitor') }}" class="btn btn-outline-secondary">
    <i class="fa-solid fa-arrow-right me-2"></i> رجوع
   </a>
  </div>

  @if(session('success'))
   <div class="alert alert-success bg-soft-success border-0 text-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
   </div>
  @endif

  <div class="custom-card">
   <div class="table-responsive">
    <table class="table table-hover-custom align-middle mb-0" style="color: var(--text-main);">
     <thead>
      <tr style="border-bottom: 2px solid var(--border-color);">
       <th class="pb-3 text-muted fw-normal">المستخدم المعاقب</th>
       <th class="pb-3 text-muted fw-normal">نوع العقوبة</th>
       <th class="pb-3 text-muted fw-normal">بواسطة الأدمن</th>
       <th class="pb-3 text-muted fw-normal text-center">تاريخ الانتهاء</th>
       <th class="pb-3 text-muted fw-normal text-start">إجراء</th>
      </tr>
     </thead>
     <tbody>
      @forelse($activePunishments as $record)
       <tr style="border-bottom: 1px solid var(--border-color);">
        <td class="py-3">
         <span
          class="fw-bold text-danger">{{ $record->punishable->role ?? $record->punishable->fullname ?? $record->punishable->full_name ?? 'غير معروف' }}</span>
         <small
          class="text-muted d-block">{{ class_basename($record->punishable_type) == 'Student' ? 'طالب' 
                          : (class_basename($record->punishable_type) == 'Teacher' ? 'أستاذ' : 
                          (class_basename($record->punishable_type) == 'Admin' ? 'أدمن' :  'اهل')) }}</small>
        </td>
        <td class="py-3">
         <span class="badge-status bg-soft-warning text-warning">{{ $record->punishment->name }}</span>
        </td>
        <td class="py-3 text-muted">
         {{ $record->admin->role ?? 'أدمن محذوف' }}
        </td>
        <td class="py-3 text-center">
         @if($record->expires_at)
          <span dir="ltr">{{ $record->expires_at->format('Y-m-d H:i') }}</span>
          <small class="d-block text-danger mt-1">({{ $record->expires_at->diffForHumans() }})</small>
         @else
          <span class="text-danger fw-bold">عقوبة دائمة</span>
         @endif
        </td>
        <td class="py-3 text-start">
         <form action="{{ route('punishments.revoke', $record->id) }}" method="POST"
          onsubmit="return confirm('هل أنت متأكد من إلغاء هذه العقوبة ورفع التقييد؟')">
          @csrf
          <button type="submit" class="btn btn-sm btn-outline-success">
           <i class="fa-solid fa-unlock me-1"></i> فك العقوبة
          </button>
         </form>
        </td>
       </tr>
      @empty
       <tr>
        <td colspan="5" class="text-center py-4 text-muted">لا يوجد أي مستخدمين معاقبين حالياً.</td>
       </tr>
      @endforelse
     </tbody>
    </table>
   </div>
   <div class="mt-4">
    {{ $activePunishments->links() }}
   </div>
  </div>
 </div>
@endsection