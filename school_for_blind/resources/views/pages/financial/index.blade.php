@extends('layouts.app')

@section('content')
 <div class="container-fluid p-0">

  {{-- ترويسة الصفحة --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
   <h4 class="fw-bold" style="color: var(--text-main);">لوحة الإدارة المالية</h4>
   <button class="btn btn-sm fw-bold px-3 py-2 shadow-sm-hover"
    style="background-color: var(--accent-color); color: #fff; border-radius: 8px; transition: transform 0.2s;">
    <i class="fa-solid fa-file-excel me-2"></i> تصدير تقرير (Excel)
   </button>
  </div>

  {{-- 1. بطاقات الإحصائيات العلوية (KPIs) --}}
  <div class="row g-4 mb-4">

   {{-- بطاقة: رصيد محفظة المدرسة --}}
   <div class="col-lg-3 col-md-6">
    <div class="custom-card h-100 d-flex flex-column justify-content-between p-4">
     <div class="d-flex justify-content-between align-items-start mb-2">
      <div>
       <p class="text-muted mb-1 fs-6 fw-bold">رصيد المدرسة المتاح</p>
       <h3 class="fw-bold mb-0" style="color: var(--text-main);">€{{ number_format($walletBalance ?? 0, 2) }}</h3>
      </div>
      <div class="p-3 rounded d-flex align-items-center justify-content-center bg-soft-success"
       style="width: 55px; height: 55px;">
       <i class="fa-solid fa-wallet fs-3 text-success"></i>
      </div>
     </div>
     <div class="mt-auto pt-3">
      <span class="badge-status bg-soft-success"><i class="fa-solid fa-check-circle me-1"></i> جاهز للتحويل</span>
     </div>
    </div>
   </div>

   {{-- بطاقة: إجمالي التبرعات --}}
   <div class="col-lg-3 col-md-6">
    <div class="custom-card h-100 d-flex flex-column justify-content-between p-4">
     <div class="d-flex justify-content-between align-items-start mb-2">
      <div>
       <p class="text-muted mb-1 fs-6 fw-bold">تبرعات الشهر</p>
       <h3 class="fw-bold mb-0" style="color: var(--text-main);">€{{ number_format($totalDonations ?? 0, 2) }}</h3>
      </div>
      <div class="p-3 rounded d-flex align-items-center justify-content-center bg-soft-info"
       style="width: 55px; height: 55px;">
       <i class="fa-solid fa-hand-holding-dollar fs-3 text-info"></i>
      </div>
     </div>
     <a href="{{ route('financial.donations') }}" class="mt-auto pt-3 text-decoration-none fw-bold"
      style="color: #3b82f6; font-size: 0.9rem;">
      عرض التبرعات <i class="fa-solid fa-arrow-left fa-sm ms-1"></i>
     </a>
    </div>
   </div>

   {{-- بطاقة: الرواتب المدفوعة --}}
   <div class="col-lg-3 col-md-6">
    <div class="custom-card h-100 d-flex flex-column justify-content-between p-4">
     <div class="d-flex justify-content-between align-items-start mb-2">
      <div>
       <p class="text-muted mb-1 fs-6 fw-bold">الرواتب المدفوعة</p>
       <h3 class="fw-bold mb-0" style="color: var(--text-main);">€{{ number_format($paidSalaries ?? 0, 2) }}</h3>
      </div>
      <div class="p-3 rounded d-flex align-items-center justify-content-center bg-soft-warning"
       style="width: 55px; height: 55px;">
       <i class="fa-solid fa-money-check-dollar fs-3 text-warning"></i>
      </div>
     </div>
     <a href="{{ route('financial.salaries') }}" class="mt-auto pt-3 text-decoration-none fw-bold text-warning"
      style="font-size: 0.9rem;">
      إدارة الرواتب <i class="fa-solid fa-arrow-left fa-sm ms-1"></i>
     </a>
    </div>
   </div>

   {{-- بطاقة: طلبات المكافآت (استبدال النقاط) --}}
   <div class="col-lg-3 col-md-6">
    <div class="custom-card h-100 d-flex flex-column justify-content-between p-4">
     <div class="d-flex justify-content-between align-items-start mb-2">
      <div>
       <p class="text-muted mb-1 fs-6 fw-bold">طلبات المكافآت</p>
       <h3 class="fw-bold mb-0" style="color: var(--text-main);">{{ $pendingRewards ?? 0 }}</h3>
      </div>
      <div class="p-3 rounded d-flex align-items-center justify-content-center bg-soft-danger"
       style="width: 55px; height: 55px;">
       <i class="fa-solid fa-gift fs-3 text-danger"></i>
      </div>
     </div>
     <a href="{{ route('financial.rewards') }}" class="mt-auto pt-3 text-decoration-none fw-bold text-danger"
      style="font-size: 0.9rem;">
      مراجعة الطلبات <i class="fa-solid fa-arrow-left fa-sm ms-1"></i>
     </a>
    </div>
   </div>

  </div>

  {{-- 2. قسم الرسم البياني ورواتب الانتظار --}}
  <div class="row g-4 mb-4">

   {{-- رسم بياني للتدفق المالي (تم حصر الارتفاع لمنع التضخم اللانهائي) --}}
   <div class="col-lg-8">
    <div class="custom-card h-100 p-4">
     <h5 class="fw-bold mb-4 text-center" style="color: var(--text-main);">التدفق المالي (تبرعات مقابل مصاريف)</h5>

     {{-- غلاف ثابت الارتفاع يمنع التمدد اللانهائي --}}
     <div style="position: relative; height: 280px; width: 100%;">
      <canvas id="cashflowChart"></canvas>
     </div>
    </div>
   </div>

   {{-- قائمة سريعة: رواتب بانتظار التحويل --}}
   <div class="col-lg-4">
    <div class="custom-card h-100 p-4 d-flex flex-column">
     <div class="d-flex justify-content-between align-items-center mb-4">
      <h6 class="fw-bold mb-0" style="color: var(--text-main);">رواتب مستحقة (Pending)</h6>
      <span class="badge rounded-pill bg-soft-warning text-warning">{{ count($pendingTeachers ?? []) }}</span>
     </div>

     <div class="d-flex flex-column gap-3 overflow-auto" style="max-height: 260px;">
      @forelse($pendingTeachers ?? [] as $teacher)
       <div class="d-flex justify-content-between align-items-center p-3 rounded"
        style="border: 1px solid var(--border-color); background-color: var(--hover-bg);">
        <div>
         <span class="d-block fw-bold"
          style="color: var(--text-main); font-size: 0.95rem;">{{ $teacher->full_name }}</span>
         <small class="text-muted">المبلغ: €{{ number_format($teacher->salary, 2) }}</small>
        </div>
        <form action="{{ route('financial.paySalary') }}" method="POST" class="m-0">
         @csrf
         <input type="hidden" name="teacher_id" value="{{ $teacher->id }}">
         <input type="hidden" name="amount" value="{{ $teacher->salary }}">
         <button type="submit" class="btn btn-sm fw-bold px-3"
          style="background-color: rgba(16, 185, 129, 0.1); color: #10b981; border: none; border-radius: 6px;">
          دفع
         </button>
        </form>
       </div>
      @empty
       <div class="text-center text-muted my-auto py-4">
        <i class="fa-solid fa-check-double fs-3 mb-2 text-success opacity-50"></i>
        <p class="mb-0">لا توجد رواتب مستحقة حالياً.</p>
       </div>
      @endforelse
     </div>
    </div>
   </div>

  </div>

  {{-- 3. جدول آخر العمليات المالية --}}
  <div class="row g-4 mb-4">
   <div class="col-12">
    <div class="custom-card p-4">
     <div class="d-flex justify-content-between align-items-center mb-4">
      <h5 class="fw-bold mb-0" style="color: var(--text-main);">أحدث الحركات المالية</h5>
      <a href="{{ route('financial.transactions') }}" class="btn btn-sm px-3"
       style="background-color: var(--hover-bg); color: var(--text-main); border: 1px solid var(--border-color); border-radius: 6px;">
       عرض السجل الكامل
      </a>
     </div>

     <div class="table-responsive">
      <table class="table table-hover-custom align-middle mb-0" style="color: var(--text-main);">
       <thead>
        <tr style="border-bottom: 2px solid var(--border-color);">
         <th scope="col" class="pb-3 text-muted fw-normal">رقم العملية</th>
         <th scope="col" class="pb-3 text-muted fw-normal">النوع</th>
         <th scope="col" class="pb-3 text-muted fw-normal">المبلغ</th>
         <th scope="col" class="pb-3 text-muted fw-normal">الوصف</th>
         <th scope="col" class="pb-3 text-muted fw-normal text-start">التاريخ</th>
        </tr>
       </thead>
       <tbody>
        @forelse($recentTransactions ?? [] as $tx)
         <tr style="border-bottom: 1px solid var(--border-color);">
          <td class="py-3 fw-bold">#{{ $tx->id }}</td>

          <td class="py-3">
           @if($tx->type === 'deposit')
            <span class="badge-status bg-soft-success text-success"><i class="fa-solid fa-arrow-down me-1"></i>
             إيداع</span>
           @else
            <span class="badge-status bg-soft-warning text-warning"><i class="fa-solid fa-arrow-up me-1"></i> سحب</span>
           @endif
          </td>

          <td class="py-3 fw-bold {{ $tx->type === 'deposit' ? 'text-success' : 'text-warning' }}" dir="ltr">
           {{ $tx->type === 'deposit' ? '+' : '-' }}€{{ number_format($tx->amount, 2) }}
          </td>

          <td class="py-3 text-muted"
           style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
           {{ $tx->description ?? 'بدون وصف' }}
          </td>

          <td class="py-3 text-muted text-start">
           {{ \Carbon\Carbon::parse($tx->created_at)->format('Y-m-d H:i') }}
          </td>
         </tr>
        @empty
         <tr>
          <td colspan="5" class="text-center py-5 text-muted">
           لا توجد حركات مالية مسجلة حتى الآن.
          </td>
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

@push('scripts')
 <script>
  Chart.defaults.color = '#9ca3af';
  Chart.defaults.borderColor = 'rgba(107, 114, 128, 0.2)';
  Chart.defaults.font.family = "'Amiri', system-ui, sans-serif";

  const cashflowCtx = document.getElementById('cashflowChart');
  if (cashflowCtx) {
   new Chart(cashflowCtx.getContext('2d'), {
    type: 'bar',
    data: {
     labels: @json($chartLabels),
     datasets: [
      {
       label: 'التبرعات (إيداع)',
       data: @json($chartDeposits),
       backgroundColor: 'rgba(16, 185, 129, 0.8)',
       borderRadius: 4,
       barPercentage: 0.5
      },
      {
       label: 'المصاريف (سحب)',
       data: @json($chartWithdrawals),
       backgroundColor: 'rgba(245, 158, 11, 0.8)',
       borderRadius: 4,
       barPercentage: 0.5
      }
     ]
    },
    options: {
     responsive: true,
     maintainAspectRatio: false, // لمنع الرسم البياني من التمدد العمودي اللانهائي
     plugins: {
      legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8 } }
     },
     scales: {
      y: { beginAtZero: true, grid: { drawBorder: false } },
      x: { grid: { display: false } }
     }
    }
   });
  }
 </script>
@endpush