@extends('layouts.app')

@section('content')
 <div class="container-fluid p-0">

  {{-- هيدر الواجهة وزر العودة --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
   <div class="d-flex align-items-center gap-3">
    <a href="{{ route('classes.show', $student->class_id ?? 1) }}"
     class="btn btn-outline-secondary btn-sm rounded-circle p-2 d-flex align-items-center justify-content-center"
     style="width: 38px; height: 38px;">
     <i class="fa-solid fa-arrow-right"></i>
    </a>
    <div>
     <h4 class="fw-bold mb-0" style="color: var(--text-main);">سجل غيابات الطالب: {{ $student->fullname }}</h4>
     <small class="text-muted">الصف والشعبة: {{ $student->class->name ?? 'غير محدد' }}</small>
    </div>
   </div>
  </div>

  {{-- كرت الفلاتر --}}
  <div class="custom-card p-4 mb-4">
   <form method="GET" action="{{ route('students.absences', $student->id) }}" class="row g-3 align-items-end">

    <div class="col-md-3">
     <label for="from_date" class="form-label text-muted small fw-bold">من تاريخ</label>
     <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date') }}"
      style="background-color: var(--bg-main); color: var(--text-main); border-color: var(--border-color);">
    </div>

    <div class="col-md-3">
     <label for="to_date" class="form-label text-muted small fw-bold">إلى تاريخ</label>
     <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}"
      style="background-color: var(--bg-main); color: var(--text-main); border-color: var(--border-color);">
    </div>

    <div class="col-md-3">
    <label for="status" class="form-label text-muted small fw-bold">حالة التبرير</label>
    <select name="status" id="status" class="form-select"
            style="background-color: var(--bg-main); color: var(--text-main); border-color: var(--border-color);">
        <option value="">جميع الحالات</option>
        <option value="waiting_caregiver" {{ request('status') == 'waiting_caregiver' ? 'selected' : '' }}>بانتظار الأهل (لا يوجد طلب)</option>
        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>بانتظار الإدارة</option>
        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>مبرر (مقبول إدارياً)</option>
        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>غير مبرر (مرفوض إدارياً)</option>
    </select>
</div>

    <div class="col-md-3 d-flex gap-2">
     <button type="submit" class="btn btn-primary flex-grow-1 fw-bold">
      <i class="fa-solid fa-filter me-1"></i> تصفية
     </button>
     <a href="{{ route('students.absences', $student->id) }}" class="btn btn-outline-secondary fw-bold"
      title="إعادة ضبط">
      <i class="fa-solid fa-rotate-right"></i>
     </a>
    </div>
   </form>
  </div>

  {{-- جدول الغيابات --}}
  <div class="custom-card p-4">
   <div class="table-responsive">
    <table class="table table-hover-custom align-middle mb-0" style="color: var(--text-main);">
     <thead>
      <tr style="border-bottom: 2px solid var(--border-color);">
       <th scope="col" class="pb-3 text-muted fw-bold">التاريخ</th>
       <th scope="col" class="pb-3 text-muted fw-bold text-center">المكالمة / القاعة</th>
       <th scope="col" class="pb-3 text-muted fw-bold text-center">السبب المذكور</th>
       <th scope="col" class="pb-3 text-muted fw-bold text-center">حالة التبرير</th>
       <th scope="col" class="pb-3 text-muted fw-bold text-start">الإجراءات</th>
      </tr>
     </thead>
     <tbody>
                    @forelse($absences as $absence)
                        @php
                            $excuse = $absence->excuse;
                        @endphp
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            
                            {{-- تاريخ الغياب --}}
                            <td class="py-3 fw-bold" dir="ltr">
                                {{ \Carbon\Carbon::parse($absence->date)->format('Y-m-d') }}
                            </td>

                            {{-- القاعة / الغرفة (مع زر التفاصيل) --}}
                            <td class="py-3 text-center">
                                <span class="d-block text-muted mb-1">{{ $absence->room->room_name ?? 'غير محدد' }}</span>
                                @if($absence->room)
                                    <button class="btn btn-sm btn-outline-info py-0 px-2" style="font-size: 0.8rem;"
                                            data-room="{{ json_encode($absence->room) }}"
                                            onclick="openRoomModal(this)">
                                        <i class="fa-solid fa-circle-info"></i> تفاصيل
                                    </button>
                                @endif
                            </td>

                            {{-- السبب المذكور --}}
                            <td class="py-3 text-center text-muted">
                                {{ $excuse->reason ?? 'لا يوجد سبب' }}
                            </td>

                            {{-- حالة التبرير (تم توضيحها حسب الشروط الجديدة) --}}
                            <td class="py-3 text-center">
                                @if(!$excuse)
                                    <span class="badge bg-soft-secondary text-secondary px-3 py-2">
                                        <i class="fa-solid fa-hourglass-half me-1"></i> بانتظار الأهل (لا يوجد طلب)
                                    </span>
                                @elseif($excuse->status == 'pending')
                                    <span class="badge bg-soft-warning text-warning px-3 py-2">
                                        <i class="fa-solid fa-clock me-1"></i> بانتظار الإدارة
                                    </span>
                                @elseif($excuse->status == 'approved')
                                    <span class="badge bg-soft-success text-success px-3 py-2">
                                        <i class="fa-solid fa-circle-check me-1"></i> مبرر (مقبول إدارياً)
                                    </span>
                                @elseif($excuse->status == 'rejected')
                                    <span class="badge bg-soft-danger text-danger px-3 py-2">
                                        <i class="fa-solid fa-circle-xmark me-1"></i> غير مبرر (مرفوض إدارياً)
                                    </span>
                                @endif
                            </td>

                            {{-- الإجراءات (تغيير الحالة أو القبول والرفض) --}}
                            <td class="py-3 text-start">
                                @if(!$excuse)
                                    <span class="text-muted small">- لا يوجد طلب للتحكم به -</span>
                                @else
                                    <div class="d-flex gap-1 justify-content-end">
                                        {{-- زر القبول (يظهر بشكل باهت وغير مفعل إذا كان الطلب مقبولاً بالفعل) --}}
                                        <button class="btn btn-sm {{ $excuse->status == 'approved' ? 'btn-success opacity-50' : 'btn-outline-success' }} fw-bold px-2"
                                                onclick="updateExcuseStatus({{ $excuse->id }}, 'approved', {{ $student->id }})"
                                                {{ $excuse->status == 'approved' ? 'disabled' : '' }}
                                                title="قبول الطلب">
                                            <i class="fa-solid fa-check"></i> قبول
                                        </button>

                                        {{-- زر الرفض (يظهر بشكل باهت وغير مفعل إذا كان الطلب مرفوضاً بالفعل) --}}
                                        <button class="btn btn-sm {{ $excuse->status == 'rejected' ? 'btn-danger opacity-50' : 'btn-outline-danger' }} fw-bold px-2"
                                                onclick="updateExcuseStatus({{ $excuse->id }}, 'rejected', {{ $student->id }})"
                                                {{ $excuse->status == 'rejected' ? 'disabled' : '' }}
                                                title="رفض الطلب">
                                            <i class="fa-solid fa-xmark"></i> رفض
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
       <tr>
        <td colspan="5" class="text-center py-5 text-muted">
         <i class="fa-solid fa-calendar-check fs-1 mb-3 d-block opacity-50"></i>
         <h5>لا يوجد سجلات غياب تطابق خيارات البحث.</h5>
        </td>
       </tr>

      @endforelse
     </tbody>
    </table>
   </div>

   {{-- الترقيم الصفحي (Pagination) --}}
   @if($absences->hasPages())
    <div class="d-flex justify-content-center mt-4">
     {{ $absences->links() }}
    </div>
   @endif
  </div>
 </div>
 {{-- مودال عرض تفاصيل الغرفة --}}
<div class="modal fade glass-modal" id="roomDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered text-end" dir="rtl">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom pb-3" style="border-color: var(--border-color) !important;">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 rounded-circle bg-soft-info d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fa-solid fa-headset text-info fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" style="color: var(--text-main);">تفاصيل الغرفة / المكالمة</h5>
                    </div>
                </div>
                <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body py-4">
                <ul class="list-group list-group-flush" style="background-color: transparent;">
                    <li class="list-group-item d-flex justify-content-between align-items-center border-bottom-0 mb-2 rounded" style="background-color: var(--bg-main); color: var(--text-main);">
                        <span class="text-muted fw-bold">اسم الغرفة:</span>
                        <span id="modal_room_name" class="fw-bold text-primary"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center border-bottom-0 mb-2 rounded" style="background-color: var(--bg-main); color: var(--text-main);">
                        <span class="text-muted fw-bold">المادة:</span>
                        <span id="modal_room_subject" class="fw-bold"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center border-bottom-0 mb-2 rounded" style="background-color: var(--bg-main); color: var(--text-main);">
                        <span class="text-muted fw-bold">حالة الغرفة:</span>
                        <span id="modal_room_status" class="badge"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center border-bottom-0 mb-2 rounded" style="background-color: var(--bg-main); color: var(--text-main);">
                        <span class="text-muted fw-bold">حالة الدفع:</span>
                        <span id="modal_room_payment"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center border-bottom-0 mb-2 rounded" style="background-color: var(--bg-main); color: var(--text-main);">
                        <span class="text-muted fw-bold">وقت البدء:</span>
                        <span id="modal_room_start" dir="ltr" class="small"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center border-bottom-0 rounded" style="background-color: var(--bg-main); color: var(--text-main);">
                        <span class="text-muted fw-bold">وقت الانتهاء:</span>
                        <span id="modal_room_end" dir="ltr" class="small"></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    // دالة فتح مودال الغرفة وتعبئة البيانات فيه
    function openRoomModal(buttonElement) {
        // جلب بيانات الغرفة من الزر
        let roomData = JSON.parse(buttonElement.getAttribute('data-room'));

        // تعبئة البيانات في المودال
        document.getElementById('modal_room_name').innerText = roomData.room_name || 'غير محدد';
        
        // تعبئة اسم المادة (نتأكد أولاً أن علاقة subject موجودة)
       let subjectName = 'غير محدد / لا يوجد';
if (roomData.subject) {
    subjectName = roomData.subject.full_name || roomData.subject.name;
}
document.getElementById('modal_room_subject').innerText = subjectName;
        // حالة الغرفة
        let statusElement = document.getElementById('modal_room_status');
        if(roomData.status === 'active') {
            statusElement.innerText = 'نشطة';
            statusElement.className = 'badge bg-soft-success text-success px-2 py-1';
        } else {
            statusElement.innerText = 'منتهية';
            statusElement.className = 'badge bg-soft-danger text-danger px-2 py-1';
        }

        // حالة الدفع
        let paymentStatus = roomData.payment_status;
        let paymentText = paymentStatus === 'paid' ? 'مدفوع' : (paymentStatus === 'deducted' ? 'مخصوم' : 'غير مدفوع');
        document.getElementById('modal_room_payment').innerText = paymentText;

        // الأوقات
        document.getElementById('modal_room_start').innerText = roomData.started_at || 'غير محدد';
        document.getElementById('modal_room_end').innerText = roomData.ended_at || 'قيد التشغيل...';

        // عرض المودال
        var roomModal = new bootstrap.Modal(document.getElementById('roomDetailsModal'));
        roomModal.show();
    }

    // دالة تحديث حالة طلب التبرير (تعمل في كل الحالات الآن)
    function updateExcuseStatus(excuseId, status, studentId) {
        if (confirm('هل أنت متأكد من تغيير حالة طلب التبرير؟')) {
            fetch(`/excuses/${excuseId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // تحديث الصفحة لرؤية التغيير
                }
            })
            .catch(error => console.error('Error updating excuse:', error));
        }
    }
</script>
@endpush