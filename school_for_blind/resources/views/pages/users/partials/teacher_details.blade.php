<div class="row g-4 text-end" dir="rtl">
 <div class="col-12">
  <h6 class="fw-bold mb-3" style="color: var(--accent-color);"><i class="fa-solid fa-address-card me-2"></i>المعلومات
   الأساسية</h6>
  <div class="row g-3 p-4 shadow-sm"
   style="background-color: var(--bg-main); border: 1px solid var(--border-color); border-radius: 16px;">
   <div class="col-md-6">
    <label class="text-muted fw-bold small d-block mb-1">الاسم الكامل</label>
    <h6 class="fw-bold m-0" style="color: var(--text-main);">{{ $user->full_name }}</h6>
   </div>
   <div class="col-md-6">
    <label class="text-muted fw-bold small d-block mb-1">رقم الهاتف</label>
    <h6 class="fw-bold m-0" dir="ltr" style="color: var(--text-main);">{{ $user->phone }}</h6>
   </div>
   <div class="col-md-6 mt-4">
    <label class="text-muted fw-bold small d-block mb-2">المستوى المستهدف</label>
    <span
     class="badge {{ $user->level == 'twelfth' ? 'bg-danger' : 'bg-info' }} rounded-pill px-3 py-2 shadow-sm fw-bold">
     {{ $user->level == 'twelfth' ? 'بكالوريا' : 'تاسع' }}
    </span>
   </div>
   <div class="col-md-6 mt-4">
    <label class="text-muted fw-bold small d-block mb-2">حالة الحساب</label>
    @php
     $statusColors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger', 'suspended' => 'danger'];
     $color = $statusColors[$user->status] ?? 'secondary';
     $statusNames = ['pending' => 'قيد الانتظار', 'approved' => 'مقبول', 'rejected' => 'مرفوض', 'suspended' => 'موقوف'];
    @endphp
    <span
     class="badge-status bg-soft-{{ $color }} text-{{ $color }} rounded-pill px-3 py-2 shadow-sm fw-bold d-inline-flex align-items-center gap-2">
     <span style="width: 6px; height: 6px; border-radius: 50%; background-color: currentColor;"></span>
     {{ $statusNames[$user->status] ?? $user->status }}
    </span>
   </div>
  </div>
 </div>

 <div class="col-12">
  <h6 class="fw-bold mb-3" style="color: var(--accent-color);"><i class="fa-solid fa-book-open-reader me-2"></i>الشعب
   والمواد المسندة</h6>

  <div class="mb-4 p-4 shadow-sm"
   style="background-color: var(--bg-main); border: 1px solid var(--border-color); border-radius: 16px;">
   <label class="text-muted fw-bold small d-block mb-3">الشعب التي يدرسها:</label>
   <div class="d-flex flex-wrap gap-2">
    @forelse($user->classes as $class)
     <span class="px-3 py-2 rounded-pill fw-bold shadow-sm"
      style="font-size: 0.85rem; background-color: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-main);">{{ $class->name }}</span>
    @empty
     <span class="text-muted small fw-bold px-3 py-2 rounded-pill"
      style="background-color: var(--bg-card); border: 1px dashed var(--border-color);">لا يوجد شعب مرتبطة</span>
    @endforelse
   </div>
  </div>

  <div class="p-4 shadow-sm"
   style="background-color: var(--bg-main); border: 1px solid var(--border-color); border-radius: 16px;">
   <label class="text-muted fw-bold small d-block mb-3">المواد وتكلفة الحصة:</label>
   @php
    $teacherSubjects = $user->subjects()->get();
   @endphp
   @if($teacherSubjects->count() > 0)
    <div class="table-responsive shadow-sm"
     style="border-radius: 12px; border: 1px solid var(--border-color); overflow: hidden; background-color: var(--bg-card);">
     <table class="table table-hover-custom text-center align-middle mb-0" style="color: var(--text-main);">
      <thead style="background-color: var(--hover-bg);">
       <tr>
        <th class="py-3 border-0 text-muted fw-bold">المادة</th>
        <th class="py-3 border-0 text-muted fw-bold">سعر الحصة</th>
       </tr>
      </thead>
      <tbody>
       @foreach($teacherSubjects as $subject)
        <tr style="border-bottom: 1px solid var(--border-color);">
         <td class="fw-bold py-3">{{ $subject->name }}</td>
         <td class="text-success fw-bold py-3">{{ number_format($subject->pivot->price_for_lesson ?? 0) }} ل.س</td>
        </tr>
       @endforeach
      </tbody>
     </table>
    </div>
   @else
    <span class="text-muted small fw-bold px-3 py-2 rounded-pill"
     style="background-color: var(--bg-card); border: 1px dashed var(--border-color);">لا يوجد مواد مرتبطة</span>
   @endif
  </div>
 </div>

 <div class="col-12">
  <h6 class="fw-bold mb-3" style="color: var(--accent-color);"><i class="fa-regular fa-calendar-check me-2"></i>أوقات
   التفرغ (الحصص الفارغة)
  </h6>
  <div class="p-4 shadow-sm"
   style="background-color: var(--bg-main); border: 1px solid var(--border-color); border-radius: 16px;">
   @php
    $days = [
     1 => 'الأحد',
     2 => 'الإثنين',
     3 => 'الثلاثاء',
     4 => 'الأربعاء',
     5 => 'الخميس',
     6 => 'الجمعة',
     7 => 'السبت'
    ];
    $availabilities = $user->availabilities ?? collect();
    $groupedAvail = $availabilities->groupBy('day_of_week');
   @endphp

   @if($availabilities->count() > 0)
    <div class="d-flex flex-column gap-3">
     @foreach($days as $dayNum => $dayName)
      @if(isset($groupedAvail[$dayNum]))
       <div class="d-flex align-items-center gap-3 p-3 rounded-3 shadow-sm"
        style="background-color: var(--bg-card); border: 1px solid var(--border-color);">
        <span class="fw-bold" style="min-width: 70px; color: var(--text-main);">{{ $dayName }}:</span>
        <div class="d-flex flex-wrap gap-2">
         @foreach($groupedAvail[$dayNum]->sortBy('period_number') as $avail)
          <span
           class="badge-status bg-soft-success text-success px-3 py-2 rounded-pill fw-bold shadow-sm border border-success border-opacity-25">
           الحصة {{ $avail->period_number }}
          </span>
         @endforeach
        </div>
       </div>
      @endif
     @endforeach
    </div>
   @else
    <span class="text-muted small fw-bold px-3 py-2 rounded-pill"
     style="background-color: var(--bg-card); border: 1px dashed var(--border-color);">لم يتم تحديد أوقات فراغ لهذا
     المعلم.</span>
   @endif
  </div>
 </div>

 <div class="col-12 mt-4 pt-4 border-top d-flex flex-column gap-3"
  style="border-color: var(--border-color) !important;">
  <div class="d-flex gap-3">
   @if($user->cv_path)
    <a href="{{ asset('storage/' . $user->cv_path) }}" target="_blank"
     class="btn btn-outline-custom flex-grow-1 py-2 fw-bold shadow-sm rounded-3">
     <i class="fa-solid fa-file-arrow-down me-2"></i>تحميل السيرة الذاتية (CV)
    </a>
   @endif

   @if($user->status !== 'pending')
    <a href="{{ route('dashboard.users.teacher.setup', $user->id) }}"
     class="btn btn-primary flex-grow-1 py-2 fw-bold shadow-sm rounded-3">
     <i class="fa-solid fa-pen-to-square me-2"></i>تعديل بيانات وإعدادات المعلم
    </a>
   @endif
  </div>

  <button type="button" class="btn btn-reject w-100 fw-bold py-2 shadow-sm rounded-3"
   onclick="fetchUserPunishments('teacher', {{ $user->id }})">
   <i class="fa-solid fa-user-shield me-2"></i>عرض سجل العقوبات
  </button>
 </div>

 <input type="hidden" id="current_user_status" value="{{ $user->status }}">
</div>