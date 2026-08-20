@extends('layouts.app')

@section('content')
 <div class="container-fluid p-0">

  {{-- ترويسة الصفحة والأزرار الإدارية --}}
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
   <div>
    <h4 class="fw-bold mb-1" style="color: var(--text-main);">سجل التبرعات الواردة</h4>
    <p class="text-muted mb-0 small">متابعة وإدارة جميع عمليات التبرع المقدمة لدعم مدرسة المكفوفين</p>
   </div>
   <div class="d-flex gap-2">
    {{-- <button class="btn btn-sm fw-bold px-3 py-2 shadow-sm-hover"
     style="background-color: var(--hover-bg); color: var(--text-main); border: 1px solid var(--border-color); border-radius: 8px;">
     <i class="fa-solid fa-arrows-rotate me-1"></i> تحديث البيانات
    </button>
    <button class="btn btn-sm fw-bold px-3 py-2 shadow-sm-hover"
     style="background-color: var(--accent-color); color: #fff; border-radius: 8px;">
     <i class="fa-solid fa-file-excel me-1"></i> تصدير التبرعات
    </button> --}}
   </div>
  </div>

  {{-- 1. بطاقات الإحصائيات السريعة --}}
  <div class="row g-4 mb-4">

   {{-- إجمالي التبرعات المكتملة --}}
   <div class="col-lg-3 col-md-6">
    <div class="custom-card p-4 d-flex align-items-center justify-content-between">
     <div>
      <p class="text-muted mb-1 small fw-bold">التبرعات المكتملة</p>
      <h3 class="fw-bold mb-0" style="color: var(--text-main);">€{{ number_format($completedAmount ?? 0, 2) }}</h3>
     </div>
     <div class="p-3 rounded-circle bg-soft-success d-flex align-items-center justify-content-center"
      style="width: 50px; height: 50px;">
      <i class="fa-solid fa-circle-check fs-4 text-success"></i>
     </div>
    </div>
   </div>

   {{-- تبرعات قيد المعالجة --}}
   <div class="col-lg-3 col-md-6">
    <div class="custom-card p-4 d-flex align-items-center justify-content-between">
     <div>
      <p class="text-muted mb-1 small fw-bold">قيد الانتظار (Pending)</p>
      <h3 class="fw-bold mb-0" style="color: var(--text-main);">€{{ number_format($pendingAmount ?? 0, 2) }}</h3>
     </div>
     <div class="p-3 rounded-circle bg-soft-warning d-flex align-items-center justify-content-center"
      style="width: 50px; height: 50px;">
      <i class="fa-solid fa-clock fs-4 text-warning"></i>
     </div>
    </div>
   </div>

   {{-- إجمالي المتبرعين --}}
   <div class="col-lg-3 col-md-6">
    <div class="custom-card p-4 d-flex align-items-center justify-content-between">
     <div>
      <p class="text-muted mb-1 small fw-bold">إجمالي المتبرعين</p>
      <h3 class="fw-bold mb-0" style="color: var(--text-main);">{{ $donorsCount ?? 0 }}</h3>
     </div>
     <div class="p-3 rounded-circle bg-soft-info d-flex align-items-center justify-content-center"
      style="width: 50px; height: 50px;">
      <i class="fa-solid fa-users fs-4 text-info"></i>
     </div>
    </div>
   </div>

   {{-- متوسط قيمة التبرع --}}
   <div class="col-lg-3 col-md-6">
    <div class="custom-card p-4 d-flex align-items-center justify-content-between">
     <div>
      <p class="text-muted mb-1 small fw-bold">متوسط التبرع</p>
      <h3 class="fw-bold mb-0" style="color: var(--text-main);">€{{ number_format($avgDonation ?? 0, 2) }}</h3>
     </div>
     <div class="p-3 rounded-circle bg-soft-danger d-flex align-items-center justify-content-center"
      style="width: 50px; height: 50px;">
      <i class="fa-solid fa-chart-line fs-4 text-danger"></i>
     </div>
    </div>
   </div>

  </div>

  {{-- 2. الفلاتر ومحرك البحث --}}
  <div class="custom-card p-4 mb-4">
   <form action="{{ route('financial.donations') ?? '#' }}" method="GET" class="row g-3">

    {{-- بحث باسم المتبرع --}}
    <div class="col-md-4">
     <div class="input-group">
      <span class="input-group-text border-end-0"
       style="background-color: var(--bg-main); border-color: var(--border-color); color: var(--text-muted);">
       <i class="fa-solid fa-magnifying-glass"></i>
      </span>
      <input type="text" name="search" class="form-control search-input border-start-0"
       placeholder="ابحث باسم المتبرع..." value="{{ request('search') }}">
     </div>
    </div>

    {{-- فلترة حسب الحالة --}}
    <div class="col-md-3">
     <select name="status" class="form-select search-input" style="border-color: var(--border-color);">
      <option value="">جميع الحالات</option>
      <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة (Completed)</option>
      <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد المعالجة (Pending)</option>
     </select>
    </div>

    {{-- فلترة حسب الهدف / المستفيد --}}
    <div class="col-md-3">
     <select name="target" class="form-select search-input" style="border-color: var(--border-color);">
      <option value="">جميع الوجهات</option>
      <option value="general" {{ request('target') == 'general' ? 'selected' : '' }}>عام (المدرسة)</option>
      <option value="student" {{ request('target') == 'student' ? 'selected' : '' }}>دعم الطلاب</option>
      <option value="teacher" {{ request('target') == 'teacher' ? 'selected' : '' }}>دعم المعلمين</option>
      <option value="parent" {{ request('target') == 'parent' ? 'selected' : '' }}>دعم الأهل</option>
     </select>
    </div>

    {{-- زر التطبيق --}}
    <div class="col-md-2 d-flex gap-2">
     <button type="submit" class="btn fw-bold w-100"
      style="background-color: var(--hover-bg); color: var(--text-main); border: 1px solid var(--border-color);">
      تصفية
     </button>
    </div>

   </form>
  </div>

  {{-- 3. جدول عرض التبرعات --}}
  <div class="custom-card p-4">
   <div class="table-responsive">
    <table class="table table-hover-custom align-middle mb-0" style="color: var(--text-main);">
     <thead>
      <tr style="border-bottom: 2px solid var(--border-color);">
       <th scope="col" class="pb-3 text-muted fw-normal">#</th>
       <th scope="col" class="pb-3 text-muted fw-normal">اسم المتبرع</th>
       <th scope="col" class="pb-3 text-muted fw-normal">المبلغ</th>
       <th scope="col" class="pb-3 text-muted fw-normal">الوجهة</th>
       <th scope="col" class="pb-3 text-muted fw-normal">حالة العملية</th>
       <th scope="col" class="pb-3 text-muted fw-normal">التاريخ</th>
       <th scope="col" class="pb-3 text-muted fw-normal text-start">التفاصيل</th>
      </tr>
     </thead>
     <tbody>
      @forelse($donations ?? [] as $donation)
       <tr style="border-bottom: 1px solid var(--border-color);">
        <td class="py-3 fw-bold">#{{ $donation->id }}</td>

        <td class="py-3">
         <div class="d-flex align-items-center">
          <div class="rounded-circle p-2 me-2 d-flex align-items-center justify-content-center bg-soft-info"
           style="width: 35px; height: 35px;">
           <i class="fa-solid fa-user-heart text-info"></i>
          </div>
          <span class="fw-bold">{{ $donation->donor_name ?? 'فاعل خير' }}</span>
         </div>
        </td>

        <td class="py-3 fw-bold text-success" dir="ltr">
         €{{ number_format($donation->amount, 2) }}
        </td>

        <td class="py-3">
         @php
          $targets = [
           'general' => ['label' => 'عام للمدرسة', 'class' => 'bg-soft-info text-info'],
           'student' => ['label' => 'دعم طالب', 'class' => 'bg-soft-success text-success'],
           'teacher' => ['label' => 'دعم مدرس', 'class' => 'bg-soft-warning text-warning'],
           'parent' => ['label' => 'دعم الأهل', 'class' => 'bg-soft-danger text-danger'],
          ];
          $targetInfo = $targets[$donation->donation_target] ?? ['label' => $donation->donation_target, 'class' => 'bg-soft-info text-info'];
         @endphp
         <span class="badge-status {{ $targetInfo['class'] }}">
          {{ $targetInfo['label'] }}
         </span>
        </td>

        <td class="py-3">
         @if($donation->status === 'completed')
          <span class="badge-status bg-soft-success text-success">
           <i class="fa-solid fa-circle-check me-1"></i> مكتملة
          </span>
         @elseif($donation->status === 'pending')
          <span class="badge-status bg-soft-warning text-warning">
           <i class="fa-solid fa-hourglass-half me-1"></i> قيد المعالجة
          </span>
         @else
          <span class="badge-status bg-soft-danger text-danger">
           <i class="fa-solid fa-circle-xmark me-1"></i> {{ $donation->status }}
          </span>
         @endif
        </td>

        <td class="py-3 text-muted">
         {{ \Carbon\Carbon::parse($donation->created_at)->format('Y-m-d H:i') }}
        </td>

        <td class="py-3 text-start">
         <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
          data-bs-target="#donationModal{{ $donation->id }}">
          <i class="fa-solid fa-eye me-1"></i> عرض
         </button>
        </td>
       </tr>

       {{-- Modal تفاصيل التبرع --}}
       <div class="modal fade glass-modal" id="donationModal{{ $donation->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content text-end" dir="rtl">
          <div class="modal-header d-flex justify-content-between">
           <h5 class="modal-title fw-bold" style="color: var(--text-main);">تفاصيل عملية التبرع #{{ $donation->id }}</h5>
           <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
           <ul class="list-group list-group-flush p-0">
            <li class="list-group-item bg-transparent d-flex justify-content-between py-2"
             style="border-color: var(--border-color); color: var(--text-main);">
             <span class="text-muted">اسم المتبرع:</span>
             <strong>{{ $donation->donor_name ?? 'فاعل خير' }}</strong>
            </li>
            <li class="list-group-item bg-transparent d-flex justify-content-between py-2"
             style="border-color: var(--border-color); color: var(--text-main);">
             <span class="text-muted">المبلغ:</span>
             <strong class="text-success">€{{ number_format($donation->amount, 2) }}
              {{ strtoupper($donation->currency ?? 'EUR') }}</strong>
            </li>
            <li class="list-group-item bg-transparent d-flex justify-content-between py-2"
             style="border-color: var(--border-color); color: var(--text-main);">
             <span class="text-muted">الجهة المستهدفة:</span>
             <strong>{{ $targetInfo['label'] }}</strong>
            </li>
            <li class="list-group-item bg-transparent d-flex justify-content-between py-2"
             style="border-color: var(--border-color); color: var(--text-main);">
             <span class="text-muted">Stripe Intent ID:</span>
             <small class="text-primary" dir="ltr">{{ $donation->stripe_session_id }}</small>
            </li>
            <li class="list-group-item bg-transparent d-flex justify-content-between py-2"
             style="border-color: var(--border-color); color: var(--text-main);">
             <span class="text-muted">تاريخ العملية:</span>
             <strong>{{ \Carbon\Carbon::parse($donation->created_at)->format('Y-m-d - H:i:s') }}</strong>
            </li>
           </ul>
          </div>
         </div>
        </div>
       </div>

      @empty
       <tr>
        <td colspan="7" class="text-center py-5 text-muted">
         <i class="fa-solid fa-inbox fs-2 mb-2 d-block opacity-50"></i>
         لا توجد سجلات تبرع مطابقة لشروط البحث.
        </td>
       </tr>
      @endforelse
     </tbody>
    </table>
   </div>

   {{-- الترقيم السريع (Pagination) --}}
   @if(isset($donations) && method_exists($donations, 'links'))
    <div class="mt-4 d-flex justify-content-end">
     {{ $donations->links() }}
    </div>
   @endif

  </div>

 </div>
@endsection