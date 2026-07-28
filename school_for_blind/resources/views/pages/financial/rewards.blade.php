@extends('layouts.app')

@section('content')
 <div class="container-fluid p-0">

  {{-- ترويسة الصفحة والأزرار الإدارية --}}
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
   <div>
    <h4 class="fw-bold mb-1" style="color: var(--text-main);">إدارة طلبات المكافآت واستبدال النقاط</h4>
    <p class="text-muted mb-0 small">مراجعة طلبات الطلاب لاستبدال النقاط المكتسبة بمكافآت حقيقية ممولة من التبرعات</p>
   </div>
   <div class="d-flex gap-2">
    <button class="btn btn-sm fw-bold px-3 py-2 shadow-sm-hover"
     style="background-color: var(--hover-bg); color: var(--text-main); border: 1px solid var(--border-color); border-radius: 8px;">
     <i class="fa-solid fa-sliders me-1"></i> إعدادات قيمة النقاط
    </button>
   </div>
  </div>

  {{-- 1. بطاقات الإحصائيات --}}
  <div class="row g-4 mb-4">

   {{-- الطلبات قيد الانتظار --}}
   <div class="col-lg-3 col-md-6">
    <div class="custom-card p-4 d-flex align-items-center justify-content-between">
     <div>
      <p class="text-muted mb-1 small fw-bold">طلبات بانتظار الموافقة</p>
      <h3 class="fw-bold mb-0 text-warning">{{ $pendingRequestsCount ?? 0 }}</h3>
     </div>
     <div class="p-3 rounded-circle bg-soft-warning d-flex align-items-center justify-content-center"
      style="width: 50px; height: 50px;">
      <i class="fa-solid fa-gift fs-4 text-warning"></i>
     </div>
    </div>
   </div>

   {{-- المكافآت المعتمدة هذا الشهر --}}
   <div class="col-lg-3 col-md-6">
    <div class="custom-card p-4 d-flex align-items-center justify-content-between">
     <div>
      <p class="text-muted mb-1 small fw-bold">طلبات تمت الموافقة عليها</p>
      <h3 class="fw-bold mb-0 text-success">{{ $approvedRequestsCount ?? 0 }}</h3>
     </div>
     <div class="p-3 rounded-circle bg-soft-success d-flex align-items-center justify-content-center"
      style="width: 50px; height: 50px;">
      <i class="fa-solid fa-circle-check fs-4 text-success"></i>
     </div>
    </div>
   </div>

   {{-- مجموع النقاط المستبدلة --}}
   <div class="col-lg-3 col-md-6">
    <div class="custom-card p-4 d-flex align-items-center justify-content-between">
     <div>
      <p class="text-muted mb-1 small fw-bold">إجمالي النقاط المستبدلة</p>
      <h3 class="fw-bold mb-0" style="color: var(--text-main);">{{ number_format($totalPointsRedeemed ?? 0) }} pts</h3>
     </div>
     <div class="p-3 rounded-circle bg-soft-info d-flex align-items-center justify-content-center"
      style="width: 50px; height: 50px;">
      <i class="fa-solid fa-star fs-4 text-info"></i>
     </div>
    </div>
   </div>

   {{-- التكلفة الإجمالية للمكافآت --}}
   <div class="col-lg-3 col-md-6">
    <div class="custom-card p-4 d-flex align-items-center justify-content-between">
     <div>
      <p class="text-muted mb-1 small fw-bold">إجمالي التكلفة الحالية</p>
      <h3 class="fw-bold mb-0 text-danger">€{{ number_format($totalRewardsCost ?? 0, 2) }}</h3>
     </div>
     <div class="p-3 rounded-circle bg-soft-danger d-flex align-items-center justify-content-center"
      style="width: 50px; height: 50px;">
      <i class="fa-solid fa-coins fs-4 text-danger"></i>
     </div>
    </div>
   </div>

  </div>

  {{-- 2. الفلاتر ومحرك البحث --}}
  <div class="custom-card p-4 mb-4">
   <form action="{{ route('financial.rewards') ?? '#' }}" method="GET" class="row g-3">

    {{-- بحث باسم الطالب --}}
    <div class="col-md-5">
     <div class="input-group">
      <span class="input-group-text border-end-0"
       style="background-color: var(--bg-main); border-color: var(--border-color); color: var(--text-muted);">
       <i class="fa-solid fa-magnifying-glass"></i>
      </span>
      <input type="text" name="search" class="form-control search-input border-start-0" placeholder="ابحث باسم الطالب..."
       value="{{ request('search') }}">
     </div>
    </div>

    {{-- فلترة حسب الحالة --}}
    <div class="col-md-4">
     <select name="status" class="form-select search-input" style="border-color: var(--border-color);">
      <option value="">جميع الحالات</option>
      <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>بانتظار الموافقة (Pending)</option>
      <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>تمت الموافقة (Approved)</option>
      <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوضة (Rejected)</option>
     </select>
    </div>

    {{-- زر التصفية --}}
    <div class="col-md-3">
     <button type="submit" class="btn fw-bold w-100"
      style="background-color: var(--hover-bg); color: var(--text-main); border: 1px solid var(--border-color);">
      تصفية الطلبات
     </button>
    </div>

   </form>
  </div>

  {{-- 3. جدول طلبات استبدال النقاط --}}
  <div class="custom-card p-4">
   <div class="table-responsive">
    <table class="table table-hover-custom align-middle mb-0" style="color: var(--text-main);">
     <thead>
      <tr style="border-bottom: 2px solid var(--border-color);">
       <th scope="col" class="pb-3 text-muted fw-normal">#</th>
       <th scope="col" class="pb-3 text-muted fw-normal">اسم الطالب</th>
       <th scope="col" class="pb-3 text-muted fw-normal">المرحلة الدراسية</th>
       <th scope="col" class="pb-3 text-muted fw-normal">النقاط المستخدمة</th>
       <th scope="col" class="pb-3 text-muted fw-normal">قيمة المكافأة (€)</th>
       <th scope="col" class="pb-3 text-muted fw-normal">الحالة</th>
       <th scope="col" class="pb-3 text-muted fw-normal">التاريخ</th>
       <th scope="col" class="pb-3 text-muted fw-normal text-start">القرارات الإدارية</th>
      </tr>
     </thead>
     <tbody>
      @forelse($rewardRequests ?? [] as $requestItem)
       <tr style="border-bottom: 1px solid var(--border-color);">
        <td class="py-3 fw-bold">#{{ $requestItem->id }}</td>

        <td class="py-3">
         <div class="d-flex align-items-center">
          <div class="rounded-circle p-2 me-2 d-flex align-items-center justify-content-center bg-soft-info"
           style="width: 35px; height: 35px;">
           <i class="fa-solid fa-graduation-cap text-info"></i>
          </div>
          <div>
           <span class="d-block fw-bold">{{ $requestItem->student->fullname ?? 'طالب' }}</span>
           <small class="text-muted">{{ $requestItem->student->phone ?? '' }}</small>
          </div>
         </div>
        </td>

        <td class="py-3 text-muted">
         @if(($requestItem->student->level ?? '') === 'ninth')
          <span class="badge bg-soft-info text-info">تاسع</span>
         @else
          <span class="badge bg-soft-warning text-warning">بكالوريا</span>
         @endif
        </td>

        <td class="py-3 fw-bold text-primary">
         <i class="fa-solid fa-star me-1 text-warning"></i> {{ $requestItem->points_to_redeem }} نقطة
        </td>

        <td class="py-3 fw-bold text-success" dir="ltr">
         €{{ number_format($requestItem->amount_paid, 2) }}
        </td>

        <td class="py-3">
         @if($requestItem->status === 'approved')
          <span class="badge-status bg-soft-success text-success">
           <i class="fa-solid fa-circle-check me-1"></i> تمت الموافقة
          </span>
         @elseif($requestItem->status === 'pending')
          <span class="badge-status bg-soft-warning text-warning">
           <i class="fa-solid fa-hourglass-half me-1"></i> قيد المراجعة
          </span>
         @else
          <span class="badge-status bg-soft-danger text-danger">
           <i class="fa-solid fa-circle-xmark me-1"></i> مرفوض
          </span>
         @endif
        </td>

        <td class="py-3 text-muted">
         {{ \Carbon\Carbon::parse($requestItem->created_at)->format('Y-m-d H:i') }}
        </td>

        <td class="py-3 text-start">
         @if($requestItem->status === 'pending')
          <div class="d-flex gap-2 justify-content-end">
           {{-- قبول الطلب --}}
           <form action="{{ route('financial.rewards.approve', $requestItem->id) ?? '#' }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="btn btn-sm btn-accept px-3 rounded shadow-sm-hover"
             onclick="return confirm('هل أنت تأكد من الموافقة على خصم النقاط وصرف المكافأة؟')">
             <i class="fa-solid fa-check me-1"></i> موافقة
            </button>
           </form>

           {{-- رفض الطلب --}}
           <form action="{{ route('financial.rewards.reject', $requestItem->id) ?? '#' }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="btn btn-sm btn-reject px-3 rounded shadow-sm-hover"
             onclick="return confirm('هل أنت تأكد من رفض هذا الطلب؟')">
             <i class="fa-solid fa-xmark me-1"></i> رفض
            </button>
           </form>
          </div>
         @else
          <span class="text-muted small">تم اتخاذ القرار</span>
         @endif
        </td>
       </tr>
      @empty
       <tr>
        <td colspan="8" class="text-center py-5 text-muted">
         <i class="fa-solid fa-gift fs-2 mb-2 d-block opacity-50"></i>
         لا توجد طلبات استبدال نقاط مسجلة حالياً.
        </td>
       </tr>
      @endforelse
     </tbody>
    </table>
   </div>

   {{-- الترقيم السريع (Pagination) --}}
   @if(isset($rewardRequests) && method_exists($rewardRequests, 'links'))
    <div class="mt-4 d-flex justify-content-end">
     {{ $rewardRequests->links() }}
    </div>
   @endif

  </div>

 </div>
@endsection