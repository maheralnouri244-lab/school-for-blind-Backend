@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold m-0" style="color: var(--text-main);">أوراق الطلاب والتسليمات</h2>
            <p class="text-muted mt-1 mb-0">امتحان: <span class="fw-bold" style="color: var(--accent-color);">{{ $exam->title }}</span></p>
        </div>
        <a href="{{ route('dashboard.exams.index') }}" class="btn btn-secondary px-4 rounded-pill">
            <i class="fa-solid fa-arrow-right me-2"></i> عودة للامتحانات
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3 mb-4 fw-bold">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info rounded-3 mb-4 fw-bold">
            <i class="fa-solid fa-circle-info me-2"></i> {{ session('info') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-3 mb-4 fw-bold">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="custom-card mb-4 p-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            
            <form action="{{ route('dashboard.exams.submissions', $exam->id) }}" method="GET" class="d-flex gap-2 align-items-center">
                <select name="status" class="form-select search-input rounded-pill" style="min-width: 220px;">
                    <option value="">كل الحالات (عرض الجميع)</option>
                    <option value="pending_grading" {{ request('status') == 'pending_grading' ? 'selected' : '' }}>قيد تصحيح الأستاذ</option>
                    <option value="pending_approval" {{ request('status') == 'pending_approval' ? 'selected' : '' }}>بانتظار الختم الإداري</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>معتمدة للطلاب</option>
                </select>
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                    <i class="fa-solid fa-filter me-1"></i> تصفية
                </button>
            </form>

            <form action="{{ route('dashboard.exams.approve-all', $exam->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من اعتماد جميع الأوراق المعلقة؟ سيتمكن الطلاب من رؤية نتائجهم.');">
                @csrf
                <button type="submit" class="btn btn-accept px-4 rounded-pill fw-bold">
                    <i class="fa-solid fa-check-double me-2"></i> اعتماد كل الأوراق المعلقة
                </button>
            </form>

        </div>
    </div>

    <div class="custom-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover-custom mb-0 text-center" style="color: var(--text-main); border-color: var(--border-color);">
                <thead style="background-color: var(--hover-bg);">
                    <tr>
                        <th class="py-3">#</th>
                        <th class="py-3">اسم الطالب</th>
                        <th class="py-3">المجموع</th>
                        <th class="py-3">الحالة</th>
                        <th class="py-3">تاريخ التسليم</th>
                        <th class="py-3">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $submission)
                        <tr>
                            <td class="align-middle">{{ $loop->iteration }}</td>
                            <td class="align-middle fw-bold">{{ $submission->student->fullname ?? $submission->student->name ?? 'طالب غير معروف' }}</td>
                            <td class="align-middle fw-bold fs-5" style="color: var(--accent-color);">
                                {{ $submission->score }}
                            </td>
                            <td class="align-middle">
                                @if($submission->status === 'approved')
                                    <span class="badge-status bg-soft-success">تم الاعتماد</span>
                                @elseif($submission->status === 'pending_approval')
                                    <span class="badge-status bg-soft-warning">بانتظار الاعتماد</span>
                                @elseif($submission->status === 'pending_grading')
                                    <span class="badge-status bg-soft-info">قيد تصحيح الأستاذ</span>
                                @else
                                    <span class="badge-status bg-soft-danger">مرفوضة</span>
                                @endif
                            </td>
                            <td class="align-middle" dir="ltr">
                                {{ \Carbon\Carbon::parse($submission->created_at)->format('Y-m-d H:i') }}
                            </td>
                            <td class="align-middle">
                                @if($submission->status === 'pending_approval')
                                    <form action="{{ route('dashboard.exams.submissions.approve', $submission->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-accept rounded-pill px-3 fw-bold" title="اعتماد الورقة">
                                            <i class="fa-solid fa-check me-1"></i> اعتماد
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-folder-open fs-1 d-block mb-3 opacity-50"></i>
                                لا يوجد أوراق طلاب مرتبطة بهذا الامتحان أو تناسب الفلتر الحالي.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($submissions->hasPages())
            <div class="d-flex justify-content-center p-3 border-top" style="border-color: var(--border-color) !important;">
                {{ $submissions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection