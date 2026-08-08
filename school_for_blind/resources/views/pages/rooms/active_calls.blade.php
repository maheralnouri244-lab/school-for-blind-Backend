@extends('layouts.app')

@section('content')
 <meta name="csrf-token" content="{{ csrf_token() }}">
 <div class="container-fluid py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
   <div>
    <h3 class="fw-bold mb-1" style="color: var(--text-main);">الدروس والمكالمات الجارية</h3>
    <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0;">مراقبة مباشرة للحصص الافتراضية النشطة على
     السيرفر حالياً</p>
   </div>
   <div class="d-flex align-items-center gap-3">
    <span class="badge px-4 py-2 rounded-pill fw-bold"
     style="background-color: rgba(59, 130, 246, 0.12); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.5); font-size: 0.9rem;">
     عدد الدروس النشطة: {{ $activeCalls->count() }}
    </span>
    <a href="{{ route('rooms.history') }}"
     class="btn px-4 rounded-pill fw-bold shadow-sm-hover d-flex align-items-center gap-2"
     style="background-color: var(--hover-bg); color: var(--text-main); border: 1px solid var(--border-color); transition: transform 0.2s;">
     <i class="fa-solid fa-clock-rotate-left text-muted"></i>
     سجل المكالمات السابقة
    </a>
   </div>
  </div>

  <div class="custom-card p-0 overflow-hidden rounded-4 border"
   style="border-color: var(--border-color) !important; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
   @if($activeCalls->isEmpty())
    <div class="text-center py-5">
     <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
      style="width: 80px; height: 80px; background-color: var(--hover-bg);">
      <i class="fa-solid fa-video-slash fs-1 text-muted opacity-75"></i>
     </div>
     <h5 class="fw-bold" style="color: var(--text-main);">لا توجد مكالمات أو دروس نشطة حالياً</h5>
     <p class="text-muted">لا يوجد أي أساتذة يقومون بالبث في الوقت الحالي.</p>
    </div>
   @else
    <div class="table-responsive">
     <table class="table table-hover-custom mb-0 text-center align-middle"
      style="color: var(--text-main); border-color: var(--border-color);">
      <thead style="background-color: var(--hover-bg);">
       <tr>
        <th class="py-3 px-3 border-0">اسم الدرس / الغرفة</th>
        <th class="py-3 px-3 border-0">الأستاذ المنشئ</th>
        <th class="py-3 px-3 border-0">الشعبة المستهدفة</th>
        <th class="py-3 px-3 border-0">المادة</th>
        <th class="py-3 px-3 border-0">وقت البدء</th>
        <th class="py-3 px-3 border-0 text-center">الإجراءات</th>
       </tr>
      </thead>
      <tbody>
       @foreach($activeCalls as $call)
        <tr style="border-bottom: 1px solid var(--border-color);">
         <td class="px-3 fw-bold">
          <div class="d-flex align-items-center justify-content-center gap-2">
           <span class="spinner-grow spinner-grow-sm text-success" role="status"
            style="--bs-spinner-width: 0.6rem; --bs-spinner-height: 0.6rem;"></span>
           {{ $call->room_name }}
          </div>
         </td>

         <td class="px-3">
          <div class="d-flex align-items-center justify-content-center gap-2">
           <div class="p-2 rounded-circle bg-soft-info d-flex align-items-center justify-content-center"
            style="width: 32px; height: 32px;">
            <i class="fa-solid fa-user-tie text-info" style="font-size: 0.85rem;"></i>
           </div>
           <span class="fw-bold">{{ $call->creator->full_name ?? $call->creator->name ?? 'غير معروف' }}</span>
          </div>
         </td>

         <td class="px-3">
          <span class="badge px-3 py-2 rounded-pill fw-normal"
           style="background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid #3b82f6;">
           <i class="fa-regular fa-folder-open me-1"></i>
           {{ $call->schoolClass->name ?? 'شعبة ' . $call->class_id }}
          </span>
         </td>

         <td class="px-3">
          @if(auth('admin')->check() && in_array(auth('admin')->user()->role, ['Super Admin', 'Academic Manager']))
           <select class="form-select form-select-sm subject-select rounded-pill mx-auto fw-bold"
            data-room-id="{{ $call->id }}"
            style="background-color: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color); min-width: 130px; text-align: center;">
            <option value="">-- حدد المادة --</option>
            @if($call->creator && method_exists($call->creator, 'subjects'))
             @foreach($call->creator->subjects()->get() as $subj)
              <option value="{{ $subj->id }}" {{ $call->subject_id == $subj->id ? 'selected' : '' }}>
               {{ $subj->full_name ?? $subj->name }}
              </option>
             @endforeach
            @endif
           </select>
          @else
           <span class="badge px-3 py-2 rounded-pill fw-normal"
            style="background-color: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color);">
            {{ $call->subject->name ?? 'لم يتم التحديد' }}
           </span>
          @endif
         </td>

         <td class="px-3" style="font-size: 0.85rem;" dir="ltr">
          <div class="d-flex align-items-center justify-content-center gap-1 fw-bold" style="color: var(--text-main);">
           <i class="fa-solid fa-clock text-success" style="font-size: 0.8rem;"></i>
           <span>{{ $call->started_at ? \Carbon\Carbon::parse($call->started_at)->diffForHumans() : 'غير معروف' }}</span>
          </div>
         </td>

         <td class="px-3 text-center">
          <a href="{{ route('rooms.join', $call->room_name) }}"
           class="btn px-4 py-1.5 rounded-pill d-inline-flex align-items-center gap-2 fw-bold text-white shadow-sm shadow-sm-hover"
           style="background-color: var(--accent-color); border: none; font-size: 0.9rem; transition: transform 0.2s;">
           <i class="fa-solid fa-eye"></i>
           دخول
          </a>
         </td>
        </tr>
       @endforeach
      </tbody>
     </table>
    </div>
   @endif
  </div>
 </div>

 @if(auth('admin')->check() && in_array(auth('admin')->user()->role, ['Super Admin', 'Academic Manager']))
  <script>
   document.addEventListener('DOMContentLoaded', function () {
    const selects = document.querySelectorAll('.subject-select');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    selects.forEach(select => {
     select.addEventListener('change', async function () {
      const roomId = this.getAttribute('data-room-id');
      const subjectId = this.value;

      if (!subjectId) return;

      this.disabled = true;

      try {
       const response = await fetch("{{ route('rooms.assign_subject') }}", {
        method: 'POST',
        headers: {
         "Content-Type": "application/json",
         "X-CSRF-TOKEN": csrfToken
        },
        body: JSON.stringify({ room_id: roomId, subject_id: subjectId })
       });

       const data = await response.json();

       if (!response.ok) {
        alert(data.message || 'حدث خطأ');
       }
      } catch (error) {
       alert('فشل الاتصال بالخادم');
      } finally {
       this.disabled = false;
      }
     });
    });
   });
  </script>
 @endif
@endsection