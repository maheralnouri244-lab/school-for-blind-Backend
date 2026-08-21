@extends('layouts.app')

@section('content')
 <div class="container-fluid py-4">

  {{-- رأس الصفحة --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
   <h2 class="fw-bold" style="color: var(--text-main);">تفاصيل الكويز وإدارة الأسئلة</h2>
   <a href="{{ route('dashboard.quizzes.index') }}" class="btn px-4 rounded-pill fw-bold shadow-sm-hover"
    style="background-color: var(--hover-bg); color: var(--text-main); border: 1px solid var(--border-color); transition: transform 0.2s;">
    <i class="fa-solid fa-arrow-right me-2"></i> عودة للقائمة
   </a>
  </div>

  {{-- رسائل النظام --}}
  @if ($errors->any())
   <div class="alert alert-danger rounded-3 mb-4 fw-bold">
    <ul class="mb-0">
     @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
     @endforeach
    </ul>
   </div>
  @endif

  @if(session('success'))
   <div class="alert alert-success rounded-3 mb-4 fw-bold">
    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
   </div>
  @endif

  <div class="row g-4">
   {{-- العمود الجانبي: المعلومات الأساسية والإجراءات --}}
   <div class="col-xl-4">
    {{-- كرت المعلومات الأساسية --}}
    <div class="custom-card mb-4 shadow-sm-hover border" style="border-color: var(--border-color) !important;">
     <h5 class="fw-bold mb-3" style="color: var(--text-main);">المعلومات الأساسية</h5>
     <hr style="border-color: var(--border-color);">

     <div class="mb-3">
      <span class="text-muted d-block small mb-1">الدرس المرتبط</span>
      <strong class="fs-5" style="color: var(--text-main);">{{ $quiz->lesson->title ?? 'غير محدد' }}</strong>
     </div>

     <div class="mb-3">
      <span class="text-muted d-block small mb-1">المادة</span>
      <strong style="color: var(--text-main);">{{ $quiz->subject->name ?? 'غير محدد' }}</strong>
     </div>

     <div class="row mb-4">
      <div class="col-4">
       <span class="text-muted d-block small mb-1">الأسئلة</span>
       <strong style="color: var(--text-main);">{{ $quiz->numofquestions }}</strong>
      </div>
      <div class="col-4">
       <span class="text-muted d-block small mb-1">المدة</span>
       <strong style="color: var(--text-main);">{{ $quiz->timelimit }} د</strong>
      </div>
      <div class="col-4">
       <span class="text-muted d-block small mb-1">العلامة</span>
       <strong class="text-success">{{ $quiz->totalmark }}</strong>
      </div>
     </div>

     @if(!$quiz->trashed())
      <div class="d-grid gap-2">
       <a href="{{ route('dashboard.quizzes.edit', $quiz->id) }}" class="btn rounded-pill py-2 fw-bold shadow-sm-hover"
        style="background-color: rgba(59, 130, 246, 0.12); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.5); transition: transform 0.2s;">
        <i class="fa-solid fa-pen-to-square me-2"></i> تعديل البيانات الأساسية
       </a>
      </div>
     @else
      <div class="alert alert-danger mb-0 text-center fw-bold rounded-3">
       <i class="fa-solid fa-box-archive me-1"></i> كويز مؤرشف (للعرض فقط)
      </div>
     @endif
    </div>

    {{-- كرت إجراءات الأسئلة والتسليمات --}}
    <div class="custom-card shadow-sm-hover border" style="border-color: var(--border-color) !important;">
     <h5 class="fw-bold mb-3" style="color: var(--text-main);">الإجراءات والتسليمات</h5>
     <hr style="border-color: var(--border-color);">
     <div class="d-grid gap-3">
      {{-- <button type="button" class="btn rounded-pill py-2 fw-bold shadow-sm-hover" data-bs-toggle="modal"
       data-bs-target="#createQuestionModal"
       style="background-color: var(--accent-color); color: #111827; border: none; transition: transform 0.2s;">
       <i class="fa-solid fa-plus me-2"></i> إضافة سؤال للكويز
      </button> --}}

      <a href="{{ route('dashboard.quizzes.submissions', $quiz->id) }}"
       class="btn rounded-pill py-2 fw-bold shadow-sm-hover text-center"
       style="background-color: rgba(59, 130, 246, 0.12); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.5); text-decoration: none; transition: transform 0.2s;">
       <i class="fa-solid fa-graduation-cap me-2"></i> عرض تسليمات الطلاب
      </a>
     </div>
    </div>
   </div>

   {{-- العمود الرئيسي: عرض الأسئلة المرتبطة --}}
   <div class="col-xl-8">
    <div class="custom-card h-100 border" style="border-color: var(--border-color) !important;">
     <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="fw-bold m-0" style="color: var(--text-main);">الأسئلة الحالية بالنموذج
       ({{ $quiz->questions->count() }})</h4>
     </div>
     <hr style="border-color: var(--border-color);">

     <div class="d-flex flex-column gap-3">
      @forelse($quiz->questions as $question)
       <div class="p-4 rounded-4 shadow-sm-hover mb-3"
        style="background-color: var(--hover-bg); border: 1px solid var(--border-color); transition: all 0.2s;">
        <div class="d-flex justify-content-between align-items-start mb-3">
         <div>
          <span class="badge rounded-pill px-3 py-2 me-2"
           style="background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid #3b82f6;">
           @if($question->type == 'mcq') <i class="fa-solid fa-list-ul me-1"></i> خيارات متعددة
           @elseif($question->type == 'TF') <i class="fa-solid fa-check-double me-1"></i> صح / خطأ
           @else <i class="fa-solid fa-pen-nib me-1"></i> سؤال مقالي @endif
          </span>
          <span class="badge rounded-pill px-3 py-2"
           style="background-color: rgba(163, 230, 53, 0.15); color: var(--accent-color);">
           النقاط: <strong>{{ $question->points }}</strong>
          </span>
         </div>

         {{-- هنا أضفنا زر التعديل بجانب زر الحذف --}}
         {{-- إخفاء أزرار التعديل والحذف إذا كان الكويز مؤرشف --}}
         @if(!$quiz->trashed())
          <div class="d-flex gap-2">
           <button type="button"
            class="btn btn-sm text-white shadow-sm-hover d-flex align-items-center justify-content-center"
            data-bs-toggle="modal" data-bs-target="#editAnswerModal{{ $question->id }}"
            style="width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #9ca3af, #3b82f6); box-shadow: 0 0 10px rgba(59, 130, 246, 0.3); border: none; transition: all 0.2s;"
            title="تعديل الإجابة الصحيحة">
            <i class="fa-solid fa-pen"></i>
           </button>

           <form action="{{ route('dashboard.quizzes.questions.detach', [$quiz->id, $question->id]) }}" method="POST"
            onsubmit="return confirm('هل أنت متأكد من إزالة هذا السؤال من الكويز؟');">
            @csrf
            @method('DELETE')
            <button type="submit"
             class="btn btn-sm text-white shadow-sm-hover d-flex align-items-center justify-content-center"
             style="width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #9ca3af, #ef4444); box-shadow: 0 0 10px rgba(239, 68, 68, 0.3); border: none; transition: all 0.2s;"
             title="إزالة من الكويز">
             <i class="fa-solid fa-minus"></i>
            </button>
           </form>
          </div>
         @endif

        <p class="fw-bold fs-5 mb-4" style="color: var(--text-main);">{{ $question->description }}</p>

        {{-- عرض الإجابات بناءً على نوع السؤال --}}
        @if($question->type == 'mcq')
         <div class="row g-3">
          @foreach($question->choices as $choice)
           <div class="col-md-6">
            <div class="p-3 rounded-3 d-flex align-items-center justify-content-between"
             style="background-color: var(--bg-main); border: 1px solid {{ $choice->is_correct ? '#10b981' : 'var(--border-color)' }};">
             <span style="color: var(--text-main);">{{ $choice->choice_text }}</span>
             @if($choice->is_correct)
              <i class="fa-solid fa-circle-check fs-5" style="color: #10b981;"></i>
             @endif
            </div>
           </div>
          @endforeach
         </div>
        @elseif($question->type == 'TF')
         <div class="p-3 rounded-3 fw-bold d-inline-flex align-items-center gap-2"
          style="background-color: rgba(16, 185, 129, 0.05); color: #10b981; border: 1px dashed #10b981;">
          <i class="fa-solid fa-check-double"></i> الإجابة الصحيحة:
          {{ $question->correct_answer == 'T' ? 'صح (True)' : 'خطأ (False)' }}
         </div>
        @else
         <div class="p-3 rounded-3"
          style="background-color: var(--bg-main); border: 1px solid var(--border-color); border-right: 4px solid #3b82f6;">
          <span class="text-muted small d-block mb-2"><i class="fa-solid fa-pen text-info me-1"></i> الإجابة
           النموذجية:</span>
          <strong style="color: var(--text-main); white-space: pre-line;">{{ $question->correct_answer }}</strong>
         </div>
        @endif
       </div>

       {{-- النافذة المنبثقة (Modal) لتعديل الإجابة --}}
       <div class="modal fade" id="editAnswerModal{{ $question->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content glass-modal text-end" dir="rtl"
          style="border: 1px solid var(--border-color); color: var(--text-main); border-radius: 16px;">

          <div class="modal-header d-flex justify-content-between align-items-center"
           style="border-bottom: 1px solid var(--border-color); background-color: rgba(59, 130, 246, 0.05);">
           <h5 class="modal-title fw-bold mb-0" style="color: var(--text-main);"><i
             class="fa-solid fa-pen text-info me-2"></i>تعديل الإجابة الصحيحة</h5>
           <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <form action="{{ route('dashboard.quizzes.questions.update', [$quiz->id, $question->id]) }}" method="POST">
           @csrf
           @method('PUT')
           <div class="modal-body p-4">
            <p class="text-muted mb-4 fw-bold">{{ $question->description }}</p>
            <div class="mb-4 text-start" dir="rtl">
             <label class="form-label fw-bold mb-2" style="color: var(--text-main);">علامة السؤال (النقاط):</label>
             <input type="number" name="points" class="form-control bg-transparent rounded-3"
              style="color: var(--text-main); border: 1px solid var(--border-color);" step="0.5" min="0.5" required
              value="{{ $question->points }}">
            </div>
            <hr style="border-color: var(--border-color);">
            @if($question->type === 'mcq')
             <label class="form-label fw-bold mb-3" style="color: var(--text-main);">حدد الخيار الصحيح الجديد:</label>
             <div class="d-flex flex-column gap-2">
              @foreach($question->choices as $choice)
               <div class="form-check p-3 rounded-3"
                style="background-color: var(--bg-main); border: 1px solid var(--border-color);">
                <input class="form-check-input float-end me-0 ms-3 mt-1" type="radio" name="correct_choice"
                 value="{{ $choice->id }}" id="choice{{ $choice->id }}" {{ $choice->is_correct ? 'checked' : '' }}
                 style="cursor: pointer;">
                <label class="form-check-label d-block w-100" for="choice{{ $choice->id }}"
                 style="cursor: pointer; color: var(--text-main);">
                 {{ $choice->choice_text }}
                </label>
               </div>
              @endforeach
             </div>

            @elseif($question->type === 'TF')
             <label class="form-label fw-bold mb-2" style="color: var(--text-main);">تعديل الإجابة الصحيحة:</label>
             <select name="correct_answer" class="form-select bg-transparent rounded-3"
              style="color: var(--text-main); border: 1px solid var(--border-color);">
              <option value="T" {{ $question->correct_answer == 'T' ? 'selected' : '' }}>صح (True)</option>
              <option value="F" {{ $question->correct_answer == 'F' ? 'selected' : '' }}>خطأ (False)</option>
             </select>

            @else
             <label class="form-label fw-bold mb-2" style="color: var(--text-main);">تعديل نموذج الإجابة الصحيحة:</label>
             <textarea name="correct_answer" class="form-control bg-transparent rounded-3" rows="4"
              style="color: var(--text-main); border: 1px solid var(--border-color);"
              required>{{ $question->correct_answer }}</textarea>
            @endif
           </div>

           <div class="modal-footer border-0 p-4 pt-0 d-flex gap-2">
            <button type="button" class="btn rounded-pill px-4 fw-bold shadow-sm-hover m-0" data-bs-dismiss="modal"
             style="background-color: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color);">إلغاء</button>
            <button type="submit" class="btn rounded-pill px-5 fw-bold shadow-sm-hover m-0"
             style="background-color: var(--accent-color); color: #111827; border: none;">حفظ التعديل</button>
           </div>
          </form>
         </div>
        </div>
       </div>

      @empty
       <div class="text-center py-5 text-muted">
        <i class="fa-solid fa-circle-question fs-1 d-block mb-3 opacity-50"></i>
        لا توجد أسئلة مرتبطة بهذا الكويز حالياً.
       </div>
      @endforelse
     </div>
    </div>
   </div>
  </div>
 </div>

 {{-- نافذة (Modal) إضافة سؤال جديد --}}
 <div class="modal fade" id="createQuestionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
   <div class="modal-content glass-modal text-end" dir="rtl"
    style="border: 1px solid var(--border-color); color: var(--text-main); border-radius: 16px;">

    <div class="modal-header d-flex justify-content-between align-items-center"
     style="border-bottom: 1px solid var(--border-color); background-color: rgba(163, 230, 53, 0.05);">
     <div class="d-flex align-items-center gap-3">
      <div class="p-2 rounded-circle d-flex align-items-center justify-content-center"
       style="width: 40px; height: 40px; background-color: rgba(163, 230, 53, 0.15);">
       <i class="fa-solid fa-plus fs-5" style="color: var(--accent-color);"></i>
      </div>
      <h5 class="modal-title fw-bold mb-0" style="color: var(--text-main);">إضافة سؤال جديد للكويز</h5>
     </div>
     <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <form action="{{ route('dashboard.quizzes.questions.store', $quiz->id) }}" method="POST">
     @csrf
     <div class="modal-body p-4">
      <div class="row mb-4 g-3">
       <div class="col-md-6 text-start" dir="rtl">
        <label class="form-label fw-bold text-muted">نوع السؤال</label>
        <select name="type" class="form-select bg-transparent rounded-3"
         style="color: var(--text-main); border: 1px solid var(--border-color);" required
         onchange="toggleQuestionFields(this.value)">
         <option value="mcq">خيارات متعددة (MCQ)</option>
         <option value="TF">صح وخطأ (TF)</option>
         <option value="TEXT">مقالي كتابي (TEXT)</option>
        </select>
       </div>
       <div class="col-md-6 text-start" dir="rtl">
        <label class="form-label fw-bold text-muted">علامة السؤال</label>
        <input type="number" name="points" class="form-control bg-transparent rounded-3"
         style="color: var(--text-main); border: 1px solid var(--border-color);" step="0.5" min="0" required value="1">
       </div>
      </div>

      <div class="mb-4 text-start" dir="rtl">
       <label class="form-label fw-bold text-muted">نص السؤال</label>
       <textarea name="description" class="form-control bg-transparent rounded-3"
        style="color: var(--text-main); border: 1px solid var(--border-color);" rows="3" required
        placeholder="اكتب نص السؤال..."></textarea>
      </div>

      <div id="mcq_fields_wrapper" class="p-3 rounded-4"
       style="background-color: var(--hover-bg); border: 1px dashed var(--border-color);">
       <div class="d-flex justify-content-between align-items-center mb-3">
        <label class="form-label fw-bold m-0 text-muted">الخيارات (حدد الإجابة الصحيحة)</label>
        <button type="button" class="btn btn-sm rounded-pill px-3 fw-bold shadow-sm-hover" id="addChoiceBtn"
         onclick="addNewChoice()"
         style="background-color: rgba(59, 130, 246, 0.12); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.5);">
         <i class="fa-solid fa-plus me-1"></i> إضافة خيار
        </button>
       </div>

       <div class="d-flex flex-column gap-3" id="choicesContainer">
        <div class="input-group choice-row">
         <div class="input-group-text px-3" style="background-color: var(--bg-main); border-color: var(--border-color);">
          <input class="form-check-input mt-0" type="radio" name="correct_choice" value="0" checked
           style="cursor: pointer;">
         </div>
         <input type="text" name="choices[0][text]" class="form-control bg-transparent"
          style="color: var(--text-main); border-color: var(--border-color);" placeholder="نص الخيار 1"
          id="choice_input_0" required>
        </div>

        <div class="input-group choice-row">
         <div class="input-group-text px-3" style="background-color: var(--bg-main); border-color: var(--border-color);">
          <input class="form-check-input mt-0" type="radio" name="correct_choice" value="1" style="cursor: pointer;">
         </div>
         <input type="text" name="choices[1][text]" class="form-control bg-transparent"
          style="color: var(--text-main); border-color: var(--border-color);" placeholder="نص الخيار 2"
          id="choice_input_1" required>
        </div>
       </div>
      </div>

      <div id="correct_answer_wrapper" class="d-none p-3 rounded-4"
       style="background-color: var(--hover-bg); border: 1px dashed var(--border-color);">
       <label class="form-label fw-bold text-muted mb-3">الإجابة الصحيحة</label>

       <select id="tf_input" class="form-select bg-transparent rounded-3 d-none"
        style="color: var(--text-main); border: 1px solid var(--border-color);">
        <option value="T">صح (True)</option>
        <option value="F">خطأ (False)</option>
       </select>

       <textarea id="text_input" class="form-control bg-transparent rounded-3 d-none"
        style="color: var(--text-main); border: 1px solid var(--border-color);" rows="2"
        placeholder="نموذج الإجابة..."></textarea>
      </div>
     </div>

     <div class="modal-footer border-0 p-4 pt-0 d-flex gap-2">
      <button type="button" class="btn rounded-pill px-4 fw-bold shadow-sm-hover m-0" data-bs-dismiss="modal"
       style="background-color: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color);">إلغاء</button>
      <button type="submit" class="btn rounded-pill px-5 fw-bold shadow-sm-hover m-0"
       style="background-color: var(--accent-color); color: #111827; border: none;">حفظ وإضافة</button>
     </div>
    </form>
   </div>
  </div>
 </div>

 <script>
  let choiceCount = 2;

  function addNewChoice() {
   if (choiceCount >= 6) return;
   const container = document.getElementById('choicesContainer');
   const newRow = document.createElement('div');
   newRow.className = 'input-group choice-row';
   newRow.innerHTML = `
           <div class="input-group-text px-3" style="background-color: var(--bg-main); border-color: var(--border-color);">
               <input class="form-check-input mt-0" type="radio" name="correct_choice" value="${choiceCount}" style="cursor: pointer;">
           </div>
           <input type="text" name="choices[${choiceCount}][text]" class="form-control bg-transparent" style="color: var(--text-main); border-color: var(--border-color);" placeholder="نص الخيار ${choiceCount + 1}">
         `;
   container.appendChild(newRow);
   choiceCount++;
  }

  function toggleQuestionFields(type) {
   const mcqWrapper = document.getElementById('mcq_fields_wrapper');
   const answerWrapper = document.getElementById('correct_answer_wrapper');
   const tfInput = document.getElementById('tf_input');
   const textInput = document.getElementById('text_input');

   mcqWrapper.classList.add('d-none');
   answerWrapper.classList.add('d-none');
   tfInput.classList.add('d-none');
   textInput.classList.add('d-none');

   tfInput.removeAttribute('name');
   textInput.removeAttribute('name');

   document.getElementById('choice_input_0').removeAttribute('required');
   document.getElementById('choice_input_1').removeAttribute('required');

   if (type === 'mcq') {
    mcqWrapper.classList.remove('d-none');
    document.getElementById('choice_input_0').setAttribute('required', 'required');
    document.getElementById('choice_input_1').setAttribute('required', 'required');
   } else if (type === 'TF') {
    answerWrapper.classList.remove('d-none');
    tfInput.classList.remove('d-none');
    tfInput.setAttribute('name', 'correct_answer');
   } else if (type === 'TEXT') {
    answerWrapper.classList.remove('d-none');
    textInput.classList.remove('d-none');
    textInput.setAttribute('name', 'correct_answer');
   }
  }
 </script>
@endsection