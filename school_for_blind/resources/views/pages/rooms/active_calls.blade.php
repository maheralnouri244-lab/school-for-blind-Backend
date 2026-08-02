@extends('layouts.app')

@section('content')
 <meta name="csrf-token" content="{{ csrf_token() }}">
 <div class="container-fluid py-4">
  <div class="d-flex align-items-center justify-content-between mb-4">
   <div>
    <h3 class="fw-bold mb-1" style="color: var(--text-main);">الدروس والمكالمات الجارية</h3>
    <p style="color: var(--text-muted); font-size: 0.9rem;">مراقبة مباشرة للحصص الافتراضية النشطة على السيرفر حالياً</p>
   </div>
   <div class="d-flex align-items-center gap-3">
    <a href="{{ route('rooms.history') }}" class="btn px-3 py-2 d-inline-flex align-items-center gap-2 fw-bold shadow-sm"
     style="background-color: var(--bg-card); color: var(--text-main); border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.9rem;">
     <i class="fa-solid fa-clock-rotate-left text-muted"></i>
     سجل المكالمات السابقة
    </a>
    <span class="badge-status bg-soft-success">
     عدد الدروس النشطة: {{ $activeCalls->count() }}
    </span>
   </div>
  </div>

  <div class="custom-card p-4">
   @if($activeCalls->isEmpty())
    <div class="text-center py-5">
     <i class="fa-solid fa-video-slash fs-1 text-muted mb-3"></i>
     <h5 style="color: var(--text-main);">لا توجد مكالمات أو دروس نشطة حالياً</h5>
    </div>
   @else
    <div class="table-responsive">
     <table class="table table-hover-custom align-middle" style="color: var(--text-main);">
      <thead>
       <tr style="color: var(--text-muted); border-bottom: 2px solid var(--border-color);">
        <th>اسم الدرس / الغرفة</th>
        <th>الأستاذ المنشئ</th>
        <th>الشعبة المستهدفة</th>
        <th>المادة</th>
        <th>وقت البدء</th>
        <th class="text-center">الإجراءات</th>
       </tr>
      </thead>
      <tbody>
       @foreach($activeCalls as $call)
        <tr style="border-bottom: 1px solid var(--border-color);">
         <td class="fw-bold">
          <span style="color: var(--accent-color); font-size: 1.1rem; margin-left: 5px;">●</span>
          {{ $call->room_name }}
         </td>
         <td>
          <div class="d-flex align-items-center gap-2">
           <i class="fa-solid fa-user-tie text-muted"></i>
           <span>{{ $call->creator->full_name ?? $call->creator->name ?? 'غير معروف' }}</span>
          </div>
         </td>
         <td>
          <span class="badge bg-soft-info px-2 py-1" style="font-size: 0.85rem;">
           <i class="fa-regular fa-folder-open me-1"></i>
           {{ $call->schoolClass->name ?? 'شعبة ' . $call->class_id }}
          </span>
         </td>
         <td>
          @if(auth('admin')->check() && in_array(auth('admin')->user()->role, ['Super Admin', 'Academic Manager']))
           <select class="form-select form-select-sm subject-select" data-room-id="{{ $call->id }}"
            style="background-color: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color); border-radius: 6px;">
            <option value="">-- حدد المادة --</option>
            @if($call->creator && method_exists($call->creator, 'subjects'))
             @foreach($call->creator->subjects()->get() as $subj)
              <option value="{{ $subj->id }}" {{ $call->subject_id == $subj->id ? 'selected' : '' }}>
               {{ $subj->name }}
              </option>
             @endforeach
            @endif
           </select>
          @else
           <span class="badge bg-secondary px-2 py-1">
            {{ $call->subject->name ?? 'لم يتم التحديد' }}
           </span>
          @endif
         </td>
         <td style="color: var(--text-muted); font-size: 0.9rem;">
          {{ $call->started_at ? \Carbon\Carbon::parse($call->started_at)->diffForHumans() : 'غير معروف' }}
         </td>
         <td class="text-center">
          <a href="{{ route('rooms.join', $call->room_name) }}"
           class="btn px-3 py-1.5 d-inline-flex align-items-center gap-2 fw-bold text-white shadow-sm"
           style="background-color: var(--accent-color); border: none; border-radius: 8px; font-size: 0.9rem;">
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