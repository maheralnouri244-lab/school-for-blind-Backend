@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">

        {{-- رأس الصفحة --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold m-0" style="color: var(--text-main);">أوراق الطلاب والتسليمات</h2>
                <p class="text-muted mt-1 mb-0">امتحان: <span class="fw-bold"
                        style="color: var(--accent-color);">{{ $exam->title }}</span></p>
            </div>
            <a href="{{ route('dashboard.exams.index') }}" class="btn px-4 rounded-pill fw-bold shadow-sm-hover"
               style="background-color: var(--hover-bg); color: var(--text-main); border: 1px solid var(--border-color); transition: transform 0.2s;">
                <i class="fa-solid fa-arrow-right me-2"></i> عودة للامتحانات
            </a>
        </div>

        {{-- رسائل النظام التنبيهية --}}
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

        {{-- شريط التصفية والإجراءات الجماعية --}}
        <div class="custom-card mb-4 p-3 border" style="border-color: var(--border-color) !important;">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                <form action="{{ route('dashboard.exams.submissions', $exam->id) }}" method="GET"
                    class="d-flex gap-2 align-items-center">
                    <select name="status" class="form-select search-input rounded-pill bg-transparent" 
                            style="min-width: 220px; color: var(--text-main); border: 1px solid var(--border-color);">
                        <option value="">كل الحالات (عرض الجميع)</option>
                        <option value="pending_grading" {{ request('status') == 'pending_grading' ? 'selected' : '' }}>قيد تصحيح الأستاذ</option>
                        <option value="pending_approval" {{ request('status') == 'pending_approval' ? 'selected' : '' }}>بانتظار الختم الإداري</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>معتمدة للطلاب</option>
                    </select>
                    <button type="submit" class="btn rounded-pill px-4 fw-bold shadow-sm-hover"
                            style="background-color: rgba(59, 130, 246, 0.12); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.5); transition: transform 0.2s;">
                        <i class="fa-solid fa-filter me-1"></i> تصفية
                    </button>
                </form>

                <form action="{{ route('dashboard.exams.approve-all', $exam->id) }}" method="POST"
                    onsubmit="return confirm('هل أنت متأكد من اعتماد جميع الأوراق المعلقة؟ سيتمكن الطلاب من رؤية نتائجهم.');">
                    @csrf
                    <button type="submit" class="btn px-4 rounded-pill fw-bold shadow-sm-hover"
                            style="background-color: var(--accent-color); color: #111827; border: none; transition: transform 0.2s;">
                        <i class="fa-solid fa-check-double me-2"></i> اعتماد كل الأوراق المعلقة
                    </button>
                </form>

            </div>
        </div>

        {{-- جدول عرض تسليمات الطلاب --}}
        <div class="custom-card p-0 overflow-hidden rounded-4 border"
            style="border-color: var(--border-color) !important; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
            <div class="table-responsive">
                <table class="table table-hover-custom mb-0 text-center"
                    style="color: var(--text-main); border-color: var(--border-color);">
                    <thead style="background-color: var(--hover-bg);">
                        <tr>
                            <th class="py-3 px-3 border-0">#</th>
                            <th class="py-3 px-4 text-start border-0">اسم الطالب</th>
                            <th class="py-3 px-3 border-0">المجموع النهائي</th>
                            <th class="py-3 px-3 border-0">الحالة</th>
                            <th class="py-3 px-3 border-0">تاريخ التسليم</th>
                            <th class="py-3 px-4 text-center border-0">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $submission)
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td class="align-middle px-3">{{ $loop->iteration }}</td>

                                {{-- خلية اسم الطالب --}}
                                <td class="align-middle px-4 text-start fw-bold">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="p-2 rounded-circle bg-soft-info d-flex align-items-center justify-content-center"
                                            style="width: 38px; height: 38px;">
                                            <i class="fa-solid fa-user text-info" style="font-size: 1rem;"></i>
                                        </div>
                                        <span>{{ $submission->student->fullname ?? $submission->student->name ?? 'طالب غير معروف' }}</span>
                                    </div>
                                </td>

                                <td class="align-middle fw-bold fs-5 px-3" style="color: var(--accent-color);">
                                    {{ $submission->score }}
                                </td>

                                <td class="align-middle px-3">
                                    @if($submission->status === 'approved')
                                        <span class="badge px-3 py-2 rounded-pill fw-normal" style="background-color: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid #10b981;">تم الاعتماد</span>
                                    @elseif($submission->status === 'pending_approval')
                                        <span class="badge px-3 py-2 rounded-pill fw-normal" style="background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid #f59e0b;">بانتظار الاعتماد</span>
                                    @elseif($submission->status === 'pending_grading')
                                        <span class="badge px-3 py-2 rounded-pill fw-normal" style="background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid #3b82f6;">قيد تصحيح الأستاذ</span>
                                    @else
                                        <span class="badge px-3 py-2 rounded-pill fw-normal" style="background-color: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid #ef4444;">مرفوضة</span>
                                    @endif
                                </td>

                                <td class="align-middle px-3" dir="ltr" style="color: var(--text-muted);">
                                    {{ \Carbon\Carbon::parse($submission->created_at)->format('Y-m-d H:i') }}
                                </td>

                                <td class="align-middle px-4 text-center">
                                    <div class="d-flex justify-content-center gap-2">

                                        {{-- زر عرض التفاصيل: متدرج أزرق --}}
                                        <button type="button"
                                            class="btn btn-sm text-white shadow-sm-hover d-flex align-items-center justify-content-center"
                                            data-bs-toggle="modal" data-bs-target="#submissionModal{{ $submission->id }}"
                                            style="width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #9ca3af, #3b82f6); box-shadow: 0 0 10px rgba(59, 130, 246, 0.3); border: none; transition: all 0.2s;" title="عرض التفاصيل">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>

                                        {{-- زر الاعتماد المفرد: متدرج فسفوري --}}
                                        @if($submission->status === 'pending_approval')
                                            <form action="{{ route('dashboard.exams.submissions.approve', $submission->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-sm shadow-sm-hover d-flex align-items-center justify-content-center"
                                                    style="width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #9ca3af, var(--accent-color)); box-shadow: 0 0 10px rgba(163, 230, 53, 0.3); color: #111827; border: none; transition: all 0.2s;" title="اعتماد الورقة">
                                                    <i class="fa-solid fa-check"></i>
                                                </button>
                                            </form>
                                        @endif

                                    </div>
                                </td>
                            </tr>

                            {{-- النافذة المنبثقة (Modal) لعرض ورقة حل الطالب --}}
                            <div class="modal fade" id="submissionModal{{ $submission->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                                    <div class="modal-content glass-modal text-end" dir="rtl"
                                        style="border: 1px solid var(--border-color); color: var(--text-main); border-radius: 16px; overflow: hidden;">

                                        <div class="modal-header d-flex justify-content-between align-items-center"
                                            style="border-bottom: 1px solid var(--border-color); background-color: rgba(59, 130, 246, 0.03);">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="p-2 rounded-circle bg-soft-info d-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px;">
                                                    <i class="fa-solid fa-file-lines text-info fs-5"></i>
                                                </div>
                                                <div>
                                                    <h5 class="modal-title fw-bold mb-0" style="color: var(--text-main);">مراجعة ورقة إجابة الطالب</h5>
                                                    <small class="text-muted">{{ $submission->student->fullname ?? 'غير معروف' }}</small>
                                                </div>
                                            </div>
                                            <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body p-4">
                                            <div class="d-flex justify-content-between align-items-center p-3 rounded mb-4"
                                                style="background-color: var(--bg-main); border: 1px dashed var(--border-color);">
                                                <span class="fw-bold fs-5 text-muted">المجموع النهائي الحاصل عليه:</span>
                                                <div class="px-4 py-2 rounded-pill fw-bold fs-4"
                                                    style="background-color: rgba(163, 230, 53, 0.12); color: var(--accent-color);">
                                                    {{ $submission->score }} درجة
                                                </div>
                                            </div>

                                            <h6 class="fw-bold mb-3" style="color: var(--text-main);"><i class="fa-solid fa-list-check me-2 text-info"></i>تفاصيل الأسئلة والإجابات:</h6>

                                            <div class="d-flex flex-column gap-3">
                                                @forelse($submission->answers ?? [] as $index => $answer)
                                                    <div class="p-3 rounded-4 shadow-sm-hover"
                                                        style="background-color: var(--hover-bg); border: 1px solid var(--border-color); transition: all 0.2s;">

                                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                                            <strong style="color: var(--text-main); max-width: 75%; text-align: right;">
                                                                <span class="text-info me-1">{{ $index + 1 }}.</span>
                                                                {{ $answer->question->description ?? 'نص السؤال غير متوفر' }}
                                                            </strong>
                                                            <span class="badge px-3 py-2 rounded-pill d-flex align-items-center gap-1"
                                                                style="background-color: {{ $answer->is_correct ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)' }}; 
                                                                       color: {{ $answer->is_correct ? '#10b981' : '#ef4444' }}; 
                                                                       border: 1px solid {{ $answer->is_correct ? '#10b981' : '#ef4444' }}; font-size: 0.85rem;">
                                                                <span>{{ $answer->points_earned ?? 0 }}</span>
                                                                <small class="opacity-75">نقطة</small>
                                                            </span>
                                                        </div>

                                                        <div class="row g-2">
                                                            <div class="col-md-6">
                                                                <div class="p-3 rounded-3 h-100"
                                                                    style="background-color: var(--bg-main); border: 1px solid var(--border-color); text-align: right;">
                                                                    <small class="text-muted d-block mb-2"><i class="fa-solid fa-pen text-secondary me-1"></i> إجابة الطالب الحالية:</small>

                                                                    @if($answer->choice_id && $answer->choice)
                                                                        <p class="mb-0 fw-bold {{ $answer->is_correct ? 'text-success' : 'text-danger' }}">
                                                                            <i class="fa-regular fa-circle-dot me-1"></i> {{ $answer->choice->choice_text ?? 'خيار غير معروف' }}
                                                                        </p>
                                                                    @elseif($answer->text_answer)
                                                                        <p class="mb-0" style="color: var(--text-main); white-space: pre-line;">{{ $answer->text_answer }}</p>
                                                                    @else
                                                                        <p class="mb-0 text-muted fst-italic"><i class="fa-solid fa-xmark me-1 text-danger"></i> لم يقم بالإجابة.</p>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="p-3 rounded-3 h-100"
                                                                    style="background-color: rgba(16, 185, 129, 0.03); border: 1px dashed rgba(16, 185, 129, 0.3); text-align: right;">
                                                                    <small class="text-success d-block mb-2"><i class="fa-solid fa-circle-check me-1"></i> الإجابة الصحيحة النموذجية:</small>

                                                                    @if($answer->question && $answer->question->choices && $answer->question->choices->count() > 0)
                                                                        @php
                                                                            $correctChoice = $answer->question->choices->where('is_correct', 1)->first();
                                                                        @endphp
                                                                        @if($correctChoice)
                                                                            <p class="mb-0 fw-bold text-success"><i class="fa-solid fa-check-double me-1"></i> {{ $correctChoice->choice_text }}</p>
                                                                        @else
                                                                            <span class="text-muted small">لم يحدد الأستاذ خياراً صحيحاً</span>
                                                                        @endif
                                                                    @elseif($answer->question && $answer->question->correct_answer)
                                                                        <p class="mb-0 text-success" style="white-space: pre-line;">{{ $answer->question->correct_answer }}</p>
                                                                    @else
                                                                        <span class="text-muted small">سؤال مقالي (يصحح يدوياً)</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="text-center py-4 text-muted">
                                                        <i class="fa-solid fa-file-circle-xmark fs-2 mb-2 opacity-50"></i>
                                                        <p class="mb-0">لا توجد تفاصيل إجابات مسجلة لهذه الورقة بالكامل.</p>
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-folder-open fs-1 d-block mb-3 opacity-50"></i>
                                    لا توجد أوراق طلاب مرتبطة بهذا الامتحان أو تناسب الفلتر المختار حالياً.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($submissions->hasPages())
                <div class="d-flex justify-content-center p-3 border-top" style="border-color: var(--border-color) !important;" dir="ltr">
                    {{ $submissions->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection