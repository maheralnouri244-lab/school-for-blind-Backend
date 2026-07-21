@extends('layouts.app')

@section('content')
  <div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="fw-bold" style="color: var(--text-main);">إدارة بلاغات المحتوى والمحادثات</h4>
      <a href="{{ route('content.monitor') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-right me-2"></i> رجوع
      </a>
    </div>

    @if(session('success'))
      <div class="alert alert-success bg-soft-success border-0 text-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    {{-- قسم الفلاتر --}}
    <div class="custom-card mb-4">
      <form action="{{ route('reports.index') }}" method="GET" class="row g-3 align-items-center">
        <div class="col-md-4">
          <label class="form-label text-muted">حالة البلاغ</label>
          <select name="status" class="form-select bg-transparent text-main border-color" onchange="this.form.submit()">
            <option value="pending" {{ request('status') == 'pending' || !request('status') ? 'selected' : '' }}>معلق
              (Pending)
            </option>
            <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>تمت المراجعة (Reviewed)
            </option>
            <option value="dismissed" {{ request('status') == 'dismissed' ? 'selected' : '' }}>مرفوض/متجاهل (Dismissed)
            </option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label text-muted">نوع المُبلّغ عليه</label>
          <select name="user_type" class="form-select bg-transparent text-main border-color"
            onchange="this.form.submit()">
            <option value="">الكل</option>
            <option value="student" {{ request('user_type') == 'student' ? 'selected' : '' }}>الطلاب</option>
            <option value="teacher" {{ request('user_type') == 'teacher' ? 'selected' : '' }}>الأساتذة</option>
          </select>
        </div>
      </form>
    </div>

    {{-- جدول عرض البلاغات --}}
    <div class="custom-card">
      <div class="table-responsive">
        <table class="table table-hover-custom align-middle mb-0" style="color: var(--text-main);">
          <thead>
            <tr style="border-bottom: 2px solid var(--border-color);">
              <th class="pb-3 text-muted fw-normal">المُبْلِغ (Reporter)</th>
              <th class="pb-3 text-muted fw-normal">المُبْلَغ عليه (Reported)</th>
              <th class="pb-3 text-muted fw-normal">السبب</th>
              <th class="pb-3 text-muted fw-normal text-center">التاريخ</th>
              <th class="pb-3 text-muted fw-normal text-start">الإجراءات</th>
            </tr>
          </thead>
          <tbody>
            @forelse($reports as $report)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                      <td class="py-3">
                        <span class="fw-bold">
                          {{-- <?php echo e(class_basename($report->reported_type)); ?> --}}
                        {{ $report->reporter->role ?? $report->reporter->fullname ??
              $report->reporter->full_name ?? 'مستخدم محذوف' }}
              </span>

                        <small
                          class="text-muted d-block">{{ class_basename($report->reporter_type) == 'Student' ? 'طالب' 
                          : (class_basename($report->reporter_type) == 'Teacher' ? 'أستاذ' : 
                          (class_basename($report->reporter_type) == 'Admin' ? 'أدمن' :  'اهل')) }}</small>
                      </td>
                      <td class="py-3">
                        <span
                          class="fw-bold text-danger">{{ $report->reported->fullname ?? $report->reported->full_name 
                          ?? $report->reported->role }}</span>
                        <small
                          class="text-muted d-block">{{ class_basename($report->reported_type) == 'Student' ? 'طالب' 
                          : (class_basename($report->reported_type) == 'Teacher' ? 'أستاذ' : 
                          (class_basename($report->reported_type) == 'Admin' ? 'أدمن' :  'اهل')) }}</small>
                      </td>
                      <td class="py-3 text-muted"
                        style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        {{ $report->reason ?? 'بدون سبب مذكور' }}
                      </td>
                      <td class="py-3 text-center text-muted">
                        {{ $report->created_at->format('Y-m-d') }}
                      </td>
                      <td class="py-3 text-start">
                        <div class="d-flex justify-content-end gap-2">
                          {{-- زر عرض وتطبيق الإجراء --}}
                          <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                            data-bs-target="#reportModal{{ $report->id }}">
                            <i class="fa-solid fa-eye me-1"></i> معالجة
                          </button>

                          @if($report->status === 'pending')
                            {{-- زر التجاهل السريع --}}
                            <form action="{{ route('reports.update-status', $report->id) }}" method="POST"
                              onsubmit="return confirm('هل أنت متأكد من تجاهل هذا البلاغ؟')">
                              @csrf
                              <input type="hidden" name="status" value="dismissed">
                              <button type="submit" class="btn btn-sm btn-reject">تجاهل</button>
                            </form>
                          @endif
                        </div>
                      </td>
                    </tr>

                    {{-- المودال الخاص بتفاصيل البلاغ ومعاقبة الشخص --}}
                    <div class="modal fade" id="reportModal{{ $report->id }}" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-md">
                        <div class="modal-content text-end" dir="rtl">
                          <div class="modal-header d-flex justify-content-between">
                            <h5 class="modal-title fw-bold" style="color: var(--text-main);">معالجة البلاغ رقم #{{ $report->id }}
                            </h5>
                            <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <div class="mb-4 p-3 rounded"
                              style="background-color: var(--hover-bg); border: 1px solid var(--border-color);">
                              <h6 class="fw-bold mb-2" style="color: var(--text-main);">تفاصيل البلاغ:</h6>
                              <p class="text-muted mb-1"><strong>المُبلِغ:</strong>
                                {{ $report->reporter->fullname ?? $report->reporter->full_name ?? $report->reporter->role }}</p>
                              <p class="text-muted mb-1"><strong>المُبلَغ عليه:</strong> <span
                                  class="text-danger">{{ $report->reported->fullname ?? $report->reported->full_name ?? $report->reporter->role }}</span>
                              </p>
                              <p class="text-muted mb-0"><strong>السبب المذكور:</strong> {{ $report->reason }}</p>
                            </div>

                            @if($report->status === 'pending')
                              {{-- فورم فرض العقوبة المربوط بالبلاغ --}}
                              <form action="{{ route('punishments.apply') }}" method="POST">
                                @csrf
                                {{-- إرسال بيانات الشخص المستهدف والبلاغ مخفية --}}
                                <input type="hidden" name="punishable_id" value="{{ $report->reported_id }}">
                                <input type="hidden" name="punishable_type" value="{{ $report->reported_type }}">
                                <input type="hidden" name="report_id" value="{{ $report->id }}">

                                <h6 class="fw-bold mb-3" style="color: var(--text-main);">اتخاذ إجراء وفرض عقوبة:</h6>

                                <div class="mb-3">
                                  <label class="form-label text-muted">اختر العقوبة المناسبة</label>
                                  <select name="punishment_id" class="form-select bg-transparent text-main border-color" required>
                                    <option value="">-- اختر من القائمة --</option>
                                    @foreach($punishments as $punishment)
                                      <option value="{{ $punishment->id }}">{{ $punishment->name }} (مستوى {{ $punishment->level }})
                                      </option>
                                    @endforeach
                                  </select>
                                </div>

                                <div class="mb-4">
                                  <label class="form-label text-muted">تخصيص مدة العقوبة بالدقائق (اختياري)</label>
                                  <input type="number" name="duration_minutes"
                                    class="form-control bg-transparent text-main border-color"
                                    placeholder="اتركه فارغاً للاعتماد على المدة الافتراضية للعقوبة">
                                  <small class="text-muted d-block mt-1">إذا تُرِك فارغاً، فسيتم تطبيق المدة المحددة مسبقاً لنوع
                                    العقوبة.</small>
                                </div>

                                <div class="d-flex gap-2">
                                  <button type="submit" class="btn btn-accept flex-grow-1">تطبيق العقوبة وإغلاق البلاغ</button>
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                </div>
                              </form>
                            @else
                              <div class="alert alert-info bg-soft-info border-0 text-info text-center m-0">
                                هذا البلاغ تمت معالجته مسبقاً وحالته الحالية هي: <strong>{{ $report->status }}</strong>
                              </div>
                            @endif
                          </div>
                        </div>
                      </div>
                    </div>
            @empty
              <tr>
                <td colspan="5" class="text-center py-4 text-muted">لا توجد بلاغات تطابق الفلاتر المحددة.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-4">
        {{ $reports->links() }}
      </div>
    </div>
  </div>

  <style>
    .bg-transparent {
      background-color: transparent !important;
    }

    .text-main {
      color: var(--text-main) !important;
    }

    .border-color {
      border-color: var(--border-color) !important;
    }
  </style>
@endsection