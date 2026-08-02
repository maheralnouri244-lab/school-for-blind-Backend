@extends('layouts.app')

@section('content')
 <meta name="csrf-token" content="{{ csrf_token() }}">
 <div class="container-fluid py-4">
  <div class="d-flex align-items-center justify-content-between mb-4">
   <div>
    <h3 class="fw-bold mb-1" style="color: var(--text-main);">سجل المكالمات السابقة</h3>
    <p style="color: var(--text-muted); font-size: 0.9rem;">تاريخ الحصص المنتهية مع تفاصيل المدة والأجر</p>
   </div>
   <div class="d-flex align-items-center gap-3">
    <a href="{{ route('admin.active-calls') }}"
     class="btn px-3 py-2 d-inline-flex align-items-center gap-2 fw-bold shadow-sm"
     style="background-color: var(--bg-card); color: var(--text-main); border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.9rem;">
     <i class="fa-solid fa-video text-muted"></i>
     الدروس الجارية حالياً
    </a>
    <span class="badge-status bg-soft-info">
     إجمالي المكالمات: {{ $pastCalls->count() }}
    </span>
   </div>
  </div>

  <div class="custom-card p-4">
   @if($pastCalls->isEmpty())
    <div class="text-center py-5">
     <i class="fa-solid fa-clock-rotate-left fs-1 text-muted mb-3"></i>
     <h5 style="color: var(--text-main);">لا توجد مكالمات منتهية في السجل</h5>
    </div>
   @else
    <div class="table-responsive">
     <table class="table table-hover-custom align-middle" style="color: var(--text-main);">
      <thead>
       <tr style="color: var(--text-muted); border-bottom: 2px solid var(--border-color);">
        <th>اسم الغرفة</th>
        <th>الأستاذ</th>
        <th>الشعبة</th>
        <th>المادة</th>
        <th>توقيت المكالمة</th>
        <th>المدة</th>
        <th>الأجر المالي</th>
       </tr>
      </thead>
      <tbody>
       @foreach($pastCalls as $call)
        <tr style="border-bottom: 1px solid var(--border-color);">
         <td class="fw-bold">{{ $call->room_name }}</td>
         <td>
          <div class="d-flex align-items-center gap-2">
           <i class="fa-solid fa-user-tie text-muted"></i>
           <span>{{ $call->creator->full_name ?? $call->creator->name ?? 'غير معروف' }}</span>
          </div>
         </td>
         <td>
          <span class="badge bg-soft-info px-2 py-1 w-auto" style="font-size: 0.8rem;">
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
           <span class="badge bg-secondary px-2 py-1 w-auto" style="font-size: 0.8rem;">
            {{ $call->subject->name ?? 'لم تحدد المادة' }}
           </span>
          @endif
         </td>
         <td style="font-size: 0.85rem;">
          <div style="color: var(--text-main);">بدء: {{ \Carbon\Carbon::parse($call->started_at)->format('Y-m-d H:i') }}
          </div>
          <div style="color: var(--text-muted);">إنهاء:
           {{ $call->ended_at ? \Carbon\Carbon::parse($call->ended_at)->format('Y-m-d H:i') : '-' }}
          </div>
         </td>
         <td>
          @php
           $duration = 0;
           if ($call->started_at && $call->ended_at) {
            $duration = \Carbon\Carbon::parse($call->started_at)->diffInMinutes($call->ended_at);
           }
          @endphp
          <span class="fw-bold {{ $duration >= 30 ? 'text-success' : 'text-danger' }}">
           {{ number_format($duration, 0) }} دقيقة
          </span>
         </td>
         <td>
          @if(auth('admin')->check() && auth('admin')->user()->role === 'Super Admin')
           <button class="btn btn-sm fw-bold payment-toggle-btn w-100" data-room-id="{{ $call->id }}"
            data-status="{{ $call->is_paid ? '1' : '0' }}"
            style="background-color: {{ $call->is_paid ? '#10b981' : '#ef4444' }}; color: white; border-radius: 6px;">
            {{ $call->is_paid ? 'مستحق الأجر' : 'مخصوم الأجر' }}
           </button>
          @else
           <span class="badge {{ $call->is_paid ? 'bg-success' : 'bg-danger' }} px-3 py-2 w-100 text-center rounded">
            {{ $call->is_paid ? 'مستحق الأجر' : 'مخصوم الأجر' }}
           </span>
          @endif
         </td>
        </tr>
       @endforeach
      </tbody>
     </table>
    </div>
   @endif
  </div>
 </div>

 <script>
  document.addEventListener('DOMContentLoaded', function () {
   const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

   @if(auth('admin')->check() && in_array(auth('admin')->user()->role, ['Super Admin', 'Academic Manager']))
    const selects = document.querySelectorAll('.subject-select');
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
       if (!response.ok) alert(data.message || 'حدث خطأ');
      } catch (error) {
       alert('فشل الاتصال بالخادم');
      } finally {
       this.disabled = false;
      }
     });
    });
   @endif

    @if(auth('admin')->check() && auth('admin')->user()->role === 'Super Admin')
     const toggleBtns = document.querySelectorAll('.payment-toggle-btn');
     toggleBtns.forEach(btn => {
      btn.addEventListener('click', async function () {
       const roomId = this.getAttribute('data-room-id');
       let currentStatus = this.getAttribute('data-status');
       let newStatus = currentStatus === '1' ? 0 : 1;

       this.disabled = true;

       try {
        const response = await fetch(`/rooms/${roomId}/toggle-payment`, {
         method: 'POST',
         headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": csrfToken
         },
         body: JSON.stringify({ is_paid: newStatus })
        });

        if (response.ok) {
         this.setAttribute('data-status', newStatus.toString());
         if (newStatus === 1) {
          this.style.backgroundColor = '#10b981';
          this.innerText = 'مستحق الأجر';
         } else {
          this.style.backgroundColor = '#ef4444';
          this.innerText = 'مخصوم الأجر';
         }
        } else {
         alert('حدث خطأ أثناء تعديل حالة الأجر');
        }
       } catch (error) {
        alert('فشل الاتصال بالخادم');
       } finally {
        this.disabled = false;
       }
      });
     });
    @endif
     });
 </script>
@endsection