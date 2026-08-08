<div class="row g-4 text-end" dir="rtl">
 {{-- القسم الأول: المعلومات الأساسية --}}
 <div class="col-12">
  <h6 class="fw-bold mb-3 text-primary"><i class="fa-solid fa-address-card me-2"></i>المعلومات الأساسية</h6>
  <div class="row g-3 p-3 rounded" style="background-color: var(--hover-bg); border: 1px solid var(--border-color);">
   <div class="col-md-6">
    <label class="text-muted small d-block">الاسم الكامل</label>
    <h6 class="fw-bold m-0" style="color: var(--text-main);">{{ $user->full_name }}</h6>
   </div>
   <div class="col-md-6">
    <label class="text-muted small d-block">رقم الهاتف</label>
    <h6 class="fw-bold m-0" dir="ltr" style="color: var(--text-main);">{{ $user->phone }}</h6>
   </div>
   <div class="col-md-6 mt-3">
    <label class="text-muted small d-block">المستوى المستهدف</label>
    <span class="badge {{ $user->level == 'twelfth' ? 'bg-danger' : 'bg-info' }}">
     {{ $user->level == 'twelfth' ? 'بكالوريا' : 'تاسع' }}
    </span>
   </div>
   <div class="col-md-6 mt-3">
    <label class="text-muted small d-block">حالة الحساب</label>
    @php
     $statusColors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger', 'suspended' => 'danger'];
     $color = $statusColors[$user->status] ?? 'secondary';
     $statusNames = ['pending' => 'قيد الانتظار', 'approved' => 'مقبول', 'rejected' => 'مرفوض', 'suspended' => 'موقوف'];
    @endphp
    <span class="badge bg-soft-{{ $color }} text-{{ $color }}">
     {{ $statusNames[$user->status] ?? $user->status }}
    </span>
   </div>
  </div>
 </div>

 {{-- القسم الثاني: الشعب والمواد --}}
 <div class="col-12">
  <h6 class="fw-bold mb-3 text-primary"><i class="fa-solid fa-book-open-reader me-2"></i>الشعب والمواد المسندة</h6>

  <div class="mb-3">
   <label class="text-muted small d-block mb-2">الشعب التي يدرسها:</label>
   <div class="d-flex flex-wrap gap-2">
    @forelse($user->classes as $class)
     <span class="badge bg-secondary px-3 py-2 rounded-pill">{{ $class->name }}</span>
    @empty
     <span class="text-muted small">لا يوجد شعب مرتبطة</span>
    @endforelse
   </div>
  </div>

  <div>
   <label class="text-muted small d-block mb-2">المواد وتكلفة الحصة:</label>
   @php
    $teacherSubjects = $user->subjects()->get();
   @endphp
   @if($teacherSubjects->count() > 0)
    <div class="table-responsive rounded border" style="border-color: var(--border-color) !important;">
     <table class="table table-sm text-center align-middle mb-0" style="color: var(--text-main);">
      <thead style="background-color: var(--hover-bg);">
       <tr>
        <th>المادة</th>
        <th>سعر الحصة</th>
       </tr>
      </thead>
      <tbody>
       @foreach($teacherSubjects as $subject)
        <tr>
         <td class="fw-bold">{{ $subject->name }}</td>
         <td class="text-success fw-bold">{{ number_format($subject->pivot->price_for_lesson ?? 0) }} ل.س</td>
        </tr>
       @endforeach
      </tbody>
     </table>
    </div>
   @else
    <span class="text-muted small">لا يوجد مواد مرتبطة</span>
   @endif
  </div>
 </div>

 {{-- القسم الثالث: أوقات الفراغ --}}
 <div class="col-12">
  <h6 class="fw-bold mb-3 text-primary"><i class="fa-regular fa-calendar-check me-2"></i>أوقات التفرغ (الحصص الفارغة)
  </h6>
  <div class="p-3 rounded" style="background-color: var(--hover-bg); border: 1px solid var(--border-color);">
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
       <div class="d-flex align-items-center gap-3">
        <span class="fw-bold" style="min-width: 70px;">{{ $dayName }}:</span>
        <div class="d-flex flex-wrap gap-1">
         @foreach($groupedAvail[$dayNum]->sortBy('period_number') as $avail)
          <span class="badge bg-soft-success text-success border border-success">
           الحصة {{ $avail->period_number }}
          </span>
         @endforeach
        </div>
       </div>
      @endif
     @endforeach
    </div>
   @else
    <span class="text-muted small">لم يتم تحديد أوقات فراغ لهذا المعلم.</span>
   @endif
  </div>
 </div>

 {{-- القسم الرابع: الإجراءات --}}
 <div class="col-12 mt-4 pt-3 border-top d-flex flex-column gap-2">
  <div class="d-flex gap-2">
   @if($user->cv_path)
    <a href="{{ asset('storage/' . $user->cv_path) }}" target="_blank" class="btn btn-outline-info flex-grow-1">
     <i class="fa-solid fa-file-arrow-down me-2"></i>تحميل السيرة الذاتية (CV)
    </a>
   @endif

   {{-- إظهار زر التعديل فقط إذا لم يكن الطلب قيد الانتظار --}}
   @if($user->status !== 'pending')
    <a href="{{ route('dashboard.users.teacher.setup', $user->id) }}" class="btn btn-primary flex-grow-1 fw-bold">
     <i class="fa-solid fa-pen-to-square me-2"></i>تعديل بيانات وإعدادات المعلم
    </a>
   @endif
  </div>

  {{-- إظهار سجل العقوبات فقط إذا لم يكن الطلب قيد الانتظار --}}
  {{-- تم إزالة شرط الـ pending لتمكين الإدارة من رؤية ماضي المعلم قبل قبوله --}}
  <button type="button" class="btn btn-outline-danger w-100 fw-bold mt-2"
   onclick="fetchUserPunishments('teacher', {{ $user->id }})">
   <i class="fa-solid fa-gavel me-2"></i>عرض سجل العقوبات
  </button>
 </div>

 <input type="hidden" id="current_user_status" value="{{ $user->status }}">
</div>