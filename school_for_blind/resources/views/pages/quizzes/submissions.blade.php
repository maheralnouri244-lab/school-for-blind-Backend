@extends('layouts.app')

@section('content')
 <div class="container-fluid py-4">

  {{-- رأس الصفحة --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
   <div>
    <h2 class="fw-bold m-0" style="color: var(--text-main);">أوراق الطلاب والتسليمات</h2>
    <p class="text-muted mt-1 mb-0">كويز تفاعلي: <span class="fw-bold"
      style="color: var(--accent-color);">{{ $quiz->lesson->title ?? 'كويز درس' }}</span></p>
   </div>
   <a href="{{ route('dashboard.quizzes.index') }}" class="btn px-4 rounded-pill fw-bold shadow-sm-hover"
    style="background-color: var(--hover-bg); color: var(--text-main); border: 1px solid var(--border-color); transition: transform 0.2s;">
    <i class="fa-solid fa-arrow-right me-2"></i> عودة للكويزات
   </a>
  </div>

  {{-- رسائل النظام --}}
  @if(session('success'))
   <div class="alert alert-success rounded-3 mb-4 fw-bold">
    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
   </div>
  @endif

  {{-- شريط التصفية --}}
  <div class="custom-card mb-4 p-3 border" style="border-color: var(--border-color) !important;">
   <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
    <form action="{{ route('dashboard.quizzes.submissions', $quiz->id) }}" method="GET"
     class="d-flex gap-2 align-items-center">
     <select name="status" class="form-select search-input rounded-pill bg-transparent"
      style="min-width: 220px; color: var(--text-main); border: 1px solid var(--border-color);">
      <option value="">كل الحالات (عرض الجميع)</option>
      <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>بانتظار التصحيح اليدوي</option>
      <option value="graded" {{ request('status') == 'graded' ? 'selected' : '' }}>مصحح كلياً</option>
     </select>
     <button type="submit" class="btn rounded-pill px-4 fw-bold shadow-sm-hover"
      style="background-color: rgba(59, 130, 246, 0.12); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.5); transition: transform 0.2s;">
      <i class="fa-solid fa-filter me-1"></i> تصفية
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
          <span>{{ $submission->student->fullname ?? 'طالب غير معروف' }}</span>
         </div>
        </td>

        <td class="align-middle fw-bold fs-5 px-3" style="color: var(--accent-color);">
         {{ $submission->total_score }} / {{ $quiz->totalmark }}
        </td>

        <td class="align-middle px-3">
         @if($submission->status === 'graded')
          <span class="badge px-3 py-2 rounded-pill fw-normal"
           style="background-color: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid #10b981;">تم التصحيح</span>
         @else
          <span class="badge px-3 py-2 rounded-pill fw-normal"
           style="background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid #f59e0b;">بانتظار تصحيح
           الأستاذ</span>
         @endif
        </td>

        <td class="align-middle px-3" dir="ltr" style="color: var(--text-muted);">
         {{ \Carbon\Carbon::parse($submission->created_at)->format('Y-m-d H:i') }}
        </td>

        <td class="align-middle px-4 text-center">
         {{-- زر عرض ورقة الطالب --}}
         <button type="button"
          class="btn btn-sm text-white shadow-sm-hover d-flex align-items-center justify-content-center mx-auto"
          data-bs-toggle="modal" data-bs-target="#submissionModal{{ $submission->id }}"
          style="width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #9ca3af, #3b82f6); box-shadow: 0 0 10px rgba(59, 130, 246, 0.3); border: none; transition: all 0.2s;"
          title="عرض إجابات الطالب">
          <i class="fa-solid fa-eye"></i>
         </button>
        </td>
       </tr>

       {{-- النافذة المنبثقة (Modal) لعرض إجابات الكويز --}}
       <div class="modal fade" id="submissionModal{{ $submission->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
         <div class="modal-content glass-modal text-end" dir="rtl"
          style="border: 1px solid var(--border-color); color: var(--text-main); border-radius: 16px; overflow: hidden;">

          <div class="modal-header d-flex justify-content-between align-items-center"
           style="border-bottom: 1px solid var(--border-color); background-color: rgba(59, 130, 246, 0.03);">
           <div class="d-flex align-items-center gap-3">
            <div class="p-2 rounded-circle bg-soft-info d-flex align-items-center justify-content-center"
             style="width: 40px; height: 40px;">
             <i class="fa-solid fa-bolt text-info fs-5"></i>
            </div>
            <div>
             <h5 class="modal-title fw-bold mb-0" style="color: var(--text-main);">مراجعة إجابات الكويز</h5>
             <small class="text-muted">{{ $submission->student->fullname ?? 'غير معروف' }}</small>
            </div>
           </div>
           <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body p-4">
           <div class="d-flex justify-content-between align-items-center p-3 rounded mb-4"
            style="background-color: var(--bg-main); border: 1px dashed var(--border-color);">
            <span class="fw-bold fs-5 text-muted">المجموع النهائي:</span>
            <div class="px-4 py-2 rounded-pill fw-bold fs-4"
             style="background-color: rgba(163, 230, 53, 0.12); color: var(--accent-color);">
             {{ $submission->total_score }} / {{ $quiz->totalmark }}
            </div>
           </div>

           <div class="d-flex flex-column gap-3">
            @forelse($submission->answers ?? [] as $index => $answer)
             <div class="p-3 rounded-4 shadow-sm-hover"
              style="background-color: var(--hover-bg); border: 1px solid var(--border-color); transition: all 0.2s;">

              <div class="d-flex justify-content-between align-items-start mb-3">
               <strong style="color: var(--text-main); max-width: 75%; text-align: right;">
                <span class="text-info me-1">{{ $index + 1 }}.</span>
                {{ $answer->question->description ?? 'سؤال محذوف' }}
               </strong>
               <span class="badge px-3 py-2 rounded-pill d-flex align-items-center gap-1"
                style="background-color: {{ $answer->is_correct ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)' }}; 
                                                                          color: {{ $answer->is_correct ? '#10b981' : '#ef4444' }}; 
                                                                          border: 1px solid {{ $answer->is_correct ? '#10b981' : '#ef4444' }}; font-size: 0.85rem;">
                <span>{{ $answer->points_earned ?? 0 }}</span>
                <small class="opacity-75">/ {{ $answer->question->points ?? 0 }}</small>
               </span>
              </div>

              <div class="row g-2">
               <div class="col-md-6">
                <div class="p-3 rounded-3 h-100"
                 style="background-color: var(--bg-main); border: 1px solid var(--border-color); text-align: right;">
                 <small class="text-muted d-block mb-2"><i class="fa-solid fa-pen text-secondary me-1"></i> إجابة
                  الطالب:</small>

                 @if($answer->choice_id && $answer->choice)
                  <p class="mb-0 fw-bold {{ $answer->is_correct ? 'text-success' : 'text-danger' }}">
                   <i class="fa-regular fa-circle-dot me-1"></i> {{ $answer->choice->choice_text }}
                  </p>
                 @elseif($answer->text_answer)
                  <p class="mb-0" style="color: var(--text-main); white-space: pre-line;">{{ $answer->text_answer }}</p>
                 @elseif($answer->audio_answer)
                  <audio controls class="w-100 mt-2">
                   <source src="{{ asset($answer->audio_answer) }}" type="audio/mpeg">
                  </audio>
                 @else
                  <p class="mb-0 text-muted fst-italic"><i class="fa-solid fa-xmark me-1 text-danger"></i> لم يُجب على هذا
                   السؤال.</p>
                 @endif
                </div>
               </div>

               <div class="col-md-6">
                <div class="p-3 rounded-3 h-100"
                 style="background-color: rgba(16, 185, 129, 0.03); border: 1px dashed rgba(16, 185, 129, 0.3); text-align: right;">
                 <small class="text-success d-block mb-2"><i class="fa-solid fa-circle-check me-1"></i> الإجابة
                  النموذجية:</small>

                 @if($answer->question && $answer->question->type === 'mcq')
                  @php
                   $correctChoice = $answer->question->choices->where('is_correct', 1)->first();
                  @endphp
                  @if($correctChoice)
                   <p class="mb-0 fw-bold text-success"><i class="fa-solid fa-check-double me-1"></i>
                    {{ $correctChoice->choice_text }}</p>
                  @endif
                 @elseif($answer->question && $answer->question->type === 'TF')
                  <p class="mb-0 text-success fw-bold">
                   {{ $answer->question->correct_answer === 'T' ? 'صح (True)' : 'خطأ (False)' }}</p>
                 @elseif($answer->question && $answer->question->type === 'TEXT')
                  <p class="mb-0 text-success" style="white-space: pre-line;">
                   {{ $answer->question->correct_answer ?? 'سؤال يصحح يدوياً' }}</p>
                 @endif
                </div>
               </div>
              </div>
             </div>
            @empty
             <div class="text-center py-4 text-muted">
              <i class="fa-solid fa-file-circle-xmark fs-2 mb-2 opacity-50"></i>
              <p class="mb-0">لا توجد إجابات مسجلة.</p>
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
         لا يوجد أي تقديمات لهذا الكويز حتى الآن.
        </td>
       </tr>
      @endforelse
     </tbody>
    </table>
   </div>
   @if($submissions->hasPages())
    <div class="d-flex justify-content-center p-3 border-top" style="border-color: var(--border-color) !important;"
     dir="ltr">
     {{ $submissions->links() }}
    </div>
   @endif
  </div>
 </div>
@endsection