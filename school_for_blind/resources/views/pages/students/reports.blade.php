@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">التقارير الخاصة بالطالب: <span class="text-primary">{{ $student->fullname }}</span></h4>
        <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">رجوع</a>
    </div>

    {{-- فلاتر بحث مرنة (النوع والوقت اختياريان) --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form action="{{ route('students.reports', $student->id) }}" method="GET" class="row align-items-end g-3">
                
                {{-- نوع التقرير --}}
                <div class="col-md-4">
                    <label for="report_type" class="form-label fw-bold small">نوع التقرير</label>
                    <select name="report_type" id="report_type" class="form-select">
                        <option value="all" {{ request('report_type') == 'all' ? 'selected' : '' }}>-- كل التقارير (جميع الأنواع) --</option>
                        <option value="daily" {{ request('report_type') == 'daily' ? 'selected' : '' }}>يومي فقط</option>
                        <option value="monthly" {{ request('report_type') == 'monthly' ? 'selected' : '' }}>شهري فقط</option>
                        <option value="yearly" {{ request('report_type') == 'yearly' ? 'selected' : '' }}>سنوي فقط</option>
                    </select>
                </div>

                {{-- فلتر الوقت (اختياري تماماً) --}}
                <div class="col-md-6">
                    <label for="reference_date" class="form-label fw-bold small">الوقت / التاريخ (اختياري)</label>
                    <input type="text" name="reference_date" id="reference_date" class="form-control" 
                           value="{{ request('reference_date') }}" 
                           placeholder="اتركه فارغاً لكل الأوقات، أو اكتب (2026 أو 2026-06 أو 2026-06-05)">
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-filter me-1"></i> تصفية</button>
                </div>
            </form>
        </div>
    </div>

    {{-- عرض البيانات --}}
    <div class="card shadow-sm border-0">
        <div class="card-header var(--bg-card)">
            <h6 class="mb-0 fw-bold">{{ $message }}</h6>
        </div>
        <div class="card-body p-4">
            @if(count($summaries) > 0 || count($yearlyReports) > 0)

                {{-- 1. عرض التقارير اليومية والشهرية المخزنة --}}
                @foreach($summaries as $summary)
                    @php $report = json_decode($summary->data, true); @endphp
                    
                    @if($summary->type === 'daily')
                        {{-- كرت التقرير اليومي --}}
                        <div class="card border mb-4 shadow-sm">
                            <div class="card-header bg-soft-primary d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0 text-primary">
                                    <i class="fa-solid fa-calendar-day me-2"></i> تقرير يومي - <span dir="ltr">{{ $summary->reference_date }}</span>
                                </h6>
                                <span class="badge bg-primary">يومي</span>
                            </div>
                            <div class="card-body">
                                <h6 class="fw-bold mb-3"><i class="fa-solid fa-user-clock text-info me-2"></i> سجل الحضور</h6>
                                <div class="row mb-3">
                                    @forelse($report['attendance'] ?? [] as $session)
                                        <div class="col-md-6 mb-2">
                                            <div class="p-3 border rounded {{ $session['is_attended'] ? 'bg-soft-success border-success' : 'bg-soft-danger border-danger' }}">
                                                <h6 class="fw-bold mb-1">{{ $session['room_name'] ?? 'درس' }}</h6>
                                                <small class="text-muted">حضور الطالب: {{ $session['student_presence_minutes'] }} دقيقة من {{ $session['total_room_minutes'] }}</small>
                                                <div class="mt-2">
                                                    <span class="badge {{ $session['is_attended'] ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $session['is_attended'] ? 'حاضر' : 'غائب' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-muted small">لا توجد دروس مسجلة في هذا اليوم.</p>
                                    @endforelse
                                </div>

                                <h6 class="fw-bold mb-2"><i class="fa-solid fa-star text-warning me-2"></i> درجات التقييمات</h6>
                                <div class="row">
                                    @forelse($report['grades_today'] ?? [] as $grade)
                                        <div class="col-md-4 mb-2">
                                            <div class="p-2 border rounded bg-light text-center">
                                                <span class="badge bg-info mb-1">{{ $grade['type'] === 'quiz' ? 'كويز' : 'اختبار' }}</span>
                                                <div class="fw-bold">{{ $grade['title'] }}</div>
                                                <span class="text-success fw-bold">{{ $grade['score'] }} درجة</span>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-muted small">لا توجد درجات مرصودة.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                    @elseif($summary->type === 'monthly')
                        {{-- كرت التقرير الشهري --}}
                        @php $attColor = ($report['attendance_percentage'] ?? 0) >= 80 ? 'text-success' : 'text-danger'; @endphp
                        <div class="card border mb-4 shadow-sm">
                            <div class="card-header bg-soft-info d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0 text-info">
                                    <i class="fa-solid fa-calendar-days me-2"></i> تقرير شهري - <span dir="ltr">{{ $summary->reference_date }}</span>
                                </h6>
                                <span class="badge bg-info">شهري</span>
                            </div>
                            <div class="card-body">
                                <div class="row g-3 text-center">
                                    <div class="col-md-3 col-6 p-3 border rounded bg-light">
                                        <span class="text-muted small">نسبة الحضور</span>
                                        <h3 class="fw-bold {{ $attColor }}" dir="ltr">{{ $report['attendance_percentage'] ?? 0 }}%</h3>
                                    </div>
                                    <div class="col-md-3 col-6 p-3 border rounded bg-light">
                                        <span class="text-muted small">الكويزات المنجزة</span>
                                        <h3 class="fw-bold text-info">{{ $report['solved_quizzes_count'] ?? 0 }}</h3>
                                    </div>
                                    <div class="col-md-3 col-6 p-3 border rounded" style="background-color: #fff9e6;">
                                        <span class="text-warning small fw-bold">كويزات مهملة</span>
                                        <h3 class="fw-bold text-warning">{{ $report['neglected_quizzes'] ?? 0 }}</h3>
                                    </div>
                                    <div class="col-md-3 col-6 p-3 border rounded" style="background-color: #ffeeee;">
                                        <span class="text-danger small fw-bold">اختبارات مهملة</span>
                                        <h3 class="fw-bold text-danger">{{ $report['neglected_exams'] ?? 0 }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

                {{-- 2. عرض التقارير السنوية إن وجدت --}}
                @foreach($yearlyReports as $yearly)
                    <div class="card border mb-4 shadow-sm">
                        <div class="card-header bg-soft-success d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-success">
                                <i class="fa-solid fa-graduation-cap me-2"></i> تقرير سنوي - عام <span dir="ltr">{{ $yearly['academic_year'] }}</span>
                            </h6>
                            <span class="badge bg-success">سنوي</span>
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="text-muted mb-2">المعدل النهائي التراكمي</h5>
                            @php $avgColor = $yearly['data']['final_average_percentage'] >= 50 ? 'text-success' : 'text-danger'; @endphp
                            <div class="display-3 fw-bolder mb-3 {{ $avgColor }}" dir="ltr">{{ $yearly['data']['final_average_percentage'] }}%</div>
                            <p class="text-muted mb-0">مجموع درجات الطالب: <strong>{{ $yearly['data']['total_student_score'] }}</strong> من <strong>{{ $yearly['data']['total_max_score'] }}</strong></p>
                        </div>
                    </div>
                @endforeach

            @else
                <div class="text-center py-5 text-muted">
                    <i class="fa-solid fa-folder-open fs-1 mb-3 opacity-50"></i>
                    <h5 class="fw-bold">لا توجد تقارير متاحة</h5>
                    <p>حاول تغيير خيارات البحث أو اترك حقل التاريخ فارغاً لجلب الكل.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection