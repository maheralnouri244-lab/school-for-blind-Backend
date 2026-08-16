@extends('layouts.app')

@section('content')
 <div class="container-fluid p-0">

  {{-- ترويسة الصفحة --}}
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
   <div>
    <h4 class="fw-bold mb-1" style="color: var(--text-main);">إدارة رواتب المدرسين</h4>
    <p class="text-muted mb-0 small">تحويل الرواتب عبر نظام Stripe ومتابعة المستحقات المالية</p>
   </div>
   <button class="btn btn-sm fw-bold px-3 py-2 shadow-sm-hover"
    style="background-color: var(--hover-bg); color: var(--text-main); border: 1px solid var(--border-color); border-radius: 8px;">
    <i class="fa-solid fa-clock-rotate-left me-1"></i> سجل التحويلات السابقة
   </button>
  </div>

  {{-- 1. بطاقات الإحصائيات --}}
  <div class="row g-4 mb-4">

   {{-- إجمالي المستحقات قيد الانتظار --}}
   <div class="col-lg-4 col-md-6">
    <div class="custom-card p-4 d-flex align-items-center justify-content-between">
     <div>
      <p class="text-muted mb-1 small fw-bold">إجمالي المستحقات (Pending)</p>
      <h3 class="fw-bold mb-0 text-warning">€{{ number_format($totalPendingSalaries ?? 0, 2) }}</h3>
     </div>
     <div class="p-3 rounded-circle bg-soft-warning d-flex align-items-center justify-content-center"
      style="width: 50px; height: 50px;">
      <i class="fa-solid fa-hourglass-half fs-4 text-warning"></i>
     </div>
    </div>
   </div>

   {{-- إجمالي الرواتب المدفوعة (للشهر الحالي) --}}
   <div class="col-lg-4 col-md-6">
    <div class="custom-card p-4 d-flex align-items-center justify-content-between">
     <div>
      <p class="text-muted mb-1 small fw-bold">الرواتب المدفوعة (الشهر الحالي)</p>
      <h3 class="fw-bold mb-0 text-success">€{{ number_format($totalPaidSalaries ?? 0, 2) }}</h3>
     </div>
     <div class="p-3 rounded-circle bg-soft-success d-flex align-items-center justify-content-center"
      style="width: 50px; height: 50px;">
      <i class="fa-solid fa-check-double fs-4 text-success"></i>
     </div>
    </div>
   </div>

   {{-- عدد المدرسين الإجمالي --}}
   <div class="col-lg-4 col-md-6">
    <div class="custom-card p-4 d-flex align-items-center justify-content-between">
     <div>
      <p class="text-muted mb-1 small fw-bold">إجمالي المدرسين</p>
      <h3 class="fw-bold mb-0" style="color: var(--text-main);">{{ $teachersCount ?? 0 }}</h3>
     </div>
     <div class="p-3 rounded-circle bg-soft-info d-flex align-items-center justify-content-center"
      style="width: 50px; height: 50px;">
      <i class="fa-solid fa-person-chalkboard fs-4 text-info"></i>
     </div>
    </div>
   </div>

  </div>

  {{-- 2. جدول الرواتب بانتظار التحويل --}}
  <div class="custom-card p-4 mb-4">
   <h5 class="fw-bold mb-4" style="color: var(--text-main);">رواتب بانتظار التحويل (Action Required)</h5>
   <div class="table-responsive">
    <table class="table table-hover-custom align-middle mb-0" style="color: var(--text-main);">
     <thead>
      <tr style="border-bottom: 2px solid var(--border-color);">
       <th scope="col" class="pb-3 text-muted fw-normal">المدرس</th>
       <th scope="col" class="pb-3 text-muted fw-normal">حالة حساب Stripe</th>
       <th scope="col" class="pb-3 text-muted fw-normal">الراتب المستحق</th>
       <th scope="col" class="pb-3 text-muted fw-normal text-start">إجراء التحويل</th>
      </tr>
     </thead>
     <tbody>
      @forelse($pendingTeachers ?? [] as $teacher)
       <tr style="border-bottom: 1px solid var(--border-color);">

        {{-- بيانات المدرس --}}
        <td class="py-3">
         <div class="d-flex align-items-center">
          <div class="rounded-circle p-2 me-2 d-flex align-items-center justify-content-center bg-soft-info"
           style="width: 35px; height: 35px;">
           <i class="fa-solid fa-user-tie text-info"></i>
          </div>
          <div>
           <span class="d-block fw-bold">{{ $teacher->full_name }}</span>
           <small class="text-muted">{{ $teacher->phone ?? 'لا يوجد رقم' }}</small>
          </div>
         </div>
        </td>

        <td class="py-3">
         @if($teacher->stripe_account_id)
          <span class="badge-status bg-soft-success text-success">
           <i class="fa-solid fa-link me-1"></i> مربوط وجاهز
          </span>
         @else
          <span class="badge-status bg-soft-danger text-danger">
           <i class="fa-solid fa-link-slash me-1"></i> غير مربوط
          </span>
         @endif
        </td>

        {{-- الراتب المستحق --}}
        <td class="py-3 fw-bold text-warning">
         <span dir="ltr" class="d-inline-block">
          €{{ number_format($teacher->salary ?? 0, 2) }}
         </span>
        </td>

        {{-- أزرار الإجراء --}}
        <td class="py-3 text-start">
         @if($teacher->stripe_account_id)
          <form action="{{ route('financial.paySalary') }}" method="POST" class="m-0 d-inline-block">
           @csrf
           <input type="hidden" name="teacher_id" value="{{ $teacher->id }}">
           <input type="hidden" name="amount" value="{{ $teacher->salary ?? 0 }}">
           <button type="submit" class="btn btn-sm btn-accept px-3 rounded shadow-sm-hover">
            <i class="fa-solid fa-paper-plane me-1"></i> تحويل الآن
           </button>
          </form>
         @else
          <button type="button" class="btn btn-sm btn-outline-secondary px-3 rounded" disabled
           title="يجب على المدرس إضافة رقم الـ IBAN أولاً">
           <i class="fa-solid fa-paper-plane me-1"></i> تحويل الآن
          </button>
          <small class="d-block mt-1 text-danger" style="font-size: 0.75rem;">ينتظر إدخال البنك</small>
         @endif
        </td>

       </tr>
      @empty
       <tr>
        <td colspan="5" class="text-center py-5 text-muted">
         <i class="fa-solid fa-check-double fs-2 mb-2 d-block text-success opacity-50"></i>
         جميع الرواتب مدفوعة، لا توجد مستحقات معلقة حالياً.
        </td>
       </tr>
      @endforelse
     </tbody>
    </table>
   </div>
  </div>

 </div>
@endsection