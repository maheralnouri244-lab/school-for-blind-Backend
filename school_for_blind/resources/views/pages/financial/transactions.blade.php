@extends('layouts.app')

@section('content')
 <div class="container-fluid p-0">

  {{-- ترويسة الصفحة والأزرار الإدارية --}}
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
   <div>
    <h4 class="fw-bold mb-1" style="color: var(--text-main);">سجل العمليات والتدقيق المالي</h4>
    <p class="text-muted mb-0 small">مراقبة وتتبع جميع الحركات المالية (الواردة والصادرة) بالتفصيل</p>
   </div>
   <div class="d-flex gap-2">
    <button class="btn btn-sm fw-bold px-3 py-2 shadow-sm-hover"
     style="background-color: var(--hover-bg); color: var(--text-main); border: 1px solid var(--border-color); border-radius: 8px;">
     <i class="fa-solid fa-print me-1"></i> طباعة السجل
    </button>
    <button class="btn btn-sm fw-bold px-3 py-2 shadow-sm-hover"
     style="background-color: var(--accent-color); color: #fff; border-radius: 8px;">
     <i class="fa-solid fa-file-csv me-1"></i> تصدير (CSV)
    </button>
   </div>
  </div>

  {{-- 1. بطاقات الإحصائيات (ملخص مالي سريع) --}}
  <div class="row g-4 mb-4">

   {{-- إجمالي الواردات (الإيداعات) --}}
   <div class="col-lg-3 col-md-6">
    <div class="custom-card p-4 d-flex align-items-center justify-content-between">
     <div>
      <p class="text-muted mb-1 small fw-bold">إجمالي الواردات (إيداع)</p>
      <h3 class="fw-bold mb-0 text-success">€{{ number_format($totalDeposits ?? 0, 2) }}</h3>
     </div>
     <div class="p-3 rounded-circle bg-soft-success d-flex align-items-center justify-content-center"
      style="width: 50px; height: 50px;">
      <i class="fa-solid fa-arrow-trend-up fs-4 text-success"></i>
     </div>
    </div>
   </div>

   {{-- إجمالي الصادرات (السحوبات) --}}
   <div class="col-lg-3 col-md-6">
    <div class="custom-card p-4 d-flex align-items-center justify-content-between">
     <div>
      <p class="text-muted mb-1 small fw-bold">إجمالي الصادرات (سحب)</p>
      <h3 class="fw-bold mb-0 text-warning">€{{ number_format($totalWithdrawals ?? 0, 2) }}</h3>
     </div>
     <div class="p-3 rounded-circle bg-soft-warning d-flex align-items-center justify-content-center"
      style="width: 50px; height: 50px;">
      <i class="fa-solid fa-arrow-trend-down fs-4 text-warning"></i>
     </div>
    </div>
   </div>

   {{-- صافي الحركة المالية --}}
   <div class="col-lg-3 col-md-6">
    <div class="custom-card p-4 d-flex align-items-center justify-content-between">
     <div>
      <p class="text-muted mb-1 small fw-bold">صافي الحركة</p>
      <h3 class="fw-bold mb-0" style="color: var(--text-main);" dir="ltr">
       {{ (($totalDeposits ?? 0) - ($totalWithdrawals ?? 0)) >= 0 ? '+' : '-' }}€{{ number_format(abs(($totalDeposits ?? 0) - ($totalWithdrawals ?? 0)), 2) }}
      </h3>
     </div>
     <div class="p-3 rounded-circle bg-soft-info d-flex align-items-center justify-content-center"
      style="width: 50px; height: 50px;">
      <i class="fa-solid fa-scale-balanced fs-4 text-info"></i>
     </div>
    </div>
   </div>

   {{-- إجمالي عدد العمليات --}}
   <div class="col-lg-3 col-md-6">
    <div class="custom-card p-4 d-flex align-items-center justify-content-between">
     <div>
      <p class="text-muted mb-1 small fw-bold">عدد العمليات المسجلة</p>
      <h3 class="fw-bold mb-0" style="color: var(--text-main);">{{ $transactionsCount ?? 0 }}</h3>
     </div>
     <div class="p-3 rounded-circle bg-soft-danger d-flex align-items-center justify-content-center"
      style="width: 50px; height: 50px;">
      <i class="fa-solid fa-list-ol fs-4 text-danger"></i>
     </div>
    </div>
   </div>

  </div>

  {{-- 2. الفلاتر ومحرك البحث (مهم جداً للتدقيق) --}}
  <div class="custom-card p-4 mb-4">
   <form action="{{ route('financial.transactions') ?? '#' }}" method="GET" class="row g-3">

    {{-- بحث بالوصف أو رقم العملية --}}
    <div class="col-md-4">
     <div class="input-group">
      <span class="input-group-text border-end-0"
       style="background-color: var(--bg-main); border-color: var(--border-color); color: var(--text-muted);">
       <i class="fa-solid fa-magnifying-glass"></i>
      </span>
      <input type="text" name="search" class="form-control search-input border-start-0"
       placeholder="ابحث برقم العملية أو الوصف..." value="{{ request('search') }}">
     </div>
    </div>

    {{-- فلترة حسب النوع --}}
    <div class="col-md-2">
     <select name="type" class="form-select search-input" style="border-color: var(--border-color);">
      <option value="">كل العمليات</option>
      <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>إيداع (واردات)</option>
      <option value="withdrawal" {{ request('type') == 'withdrawal' ? 'selected' : '' }}>سحب (صادرات)</option>
     </select>
    </div>

    {{-- فلترة حسب التاريخ (من) --}}
    <div class="col-md-2">
     <input type="date" name="date_from" class="form-control search-input" style="border-color: var(--border-color);"
      value="{{ request('date_from') }}" title="من تاريخ">
    </div>

    {{-- فلترة حسب التاريخ (إلى) --}}
    <div class="col-md-2">
     <input type="date" name="date_to" class="form-control search-input" style="border-color: var(--border-color);"
      value="{{ request('date_to') }}" title="إلى تاريخ">
    </div>

    {{-- زر التصفية --}}
    <div class="col-md-2">
     <button type="submit" class="btn fw-bold w-100"
      style="background-color: var(--hover-bg); color: var(--text-main); border: 1px solid var(--border-color);">
      تصفية
     </button>
    </div>

   </form>
  </div>

  {{-- 3. الجدول التفصيلي --}}
  <div class="custom-card p-4">
   <div class="table-responsive">
    <table class="table table-hover-custom align-middle mb-0" style="color: var(--text-main);">
     <thead>
      <tr style="border-bottom: 2px solid var(--border-color);">
       <th scope="col" class="pb-3 text-muted fw-normal">رقم العملية (ID)</th>
       <th scope="col" class="pb-3 text-muted fw-normal">نوع الحركة</th>
       <th scope="col" class="pb-3 text-muted fw-normal">المبلغ</th>
       <th scope="col" class="pb-3 text-muted fw-normal">التفاصيل / الوصف</th>
       <th scope="col" class="pb-3 text-muted fw-normal">المرجع (Reference)</th>
       <th scope="col" class="pb-3 text-muted fw-normal text-start">التاريخ والوقت</th>
      </tr>
     </thead>
     <tbody>
      @forelse($transactions ?? [] as $transaction)
       <tr style="border-bottom: 1px solid var(--border-color);">

        <td class="py-3 fw-bold">#{{ $transaction->id }}</td>

        <td class="py-3">
         @if($transaction->type === 'deposit')
          <span class="badge-status bg-soft-success text-success">
           <i class="fa-solid fa-arrow-down me-1"></i> إيداع
          </span>
         @else
          <span class="badge-status bg-soft-warning text-warning">
           <i class="fa-solid fa-arrow-up me-1"></i> سحب
          </span>
         @endif
        </td>

        <td class="py-3 fw-bold {{ $transaction->type === 'deposit' ? 'text-success' : 'text-warning' }}" dir="ltr">
         {{ $transaction->type === 'deposit' ? '+' : '-' }}€{{ number_format($transaction->amount, 2) }}
        </td>

        <td class="py-3 text-muted">
         <span class="d-inline-block text-truncate" style="max-width: 300px;" title="{{ $transaction->description }}">
          {{ $transaction->description ?? 'بدون وصف' }}
         </span>
        </td>

        <td class="py-3">
         @if($transaction->reference_id)
          @php
           $refType = class_basename($transaction->reference_type);
          @endphp
          <span class="badge bg-soft-info text-info fw-normal" style="font-size: 0.8rem;">
           {{ $refType }} #{{ $transaction->reference_id }}
          </span>
         @else
          <span class="text-muted small">-</span>
         @endif
        </td>

        <td class="py-3 text-muted text-start" dir="ltr">
         <i class="fa-regular fa-clock me-1 opacity-50"></i>
         {{ \Carbon\Carbon::parse($transaction->created_at)->format('Y-m-d H:i') }}
        </td>

       </tr>
      @empty
       <tr>
        <td colspan="6" class="text-center py-5 text-muted">
         <i class="fa-solid fa-file-invoice-dollar fs-2 mb-2 d-block opacity-50"></i>
         لا توجد عمليات مالية مطابقة للبحث.
        </td>
       </tr>
      @endforelse
     </tbody>
    </table>
   </div>

   {{-- الترقيم السريع (Pagination) --}}
   @if(isset($transactions) && method_exists($transactions, 'links'))
    <div class="mt-4 d-flex justify-content-end">
     {{ $transactions->links() }}
    </div>
   @endif

  </div>

 </div>
@endsection