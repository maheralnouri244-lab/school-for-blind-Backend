@extends('layouts.app')

@section('content')
 <div class="container-fluid py-4">

  {{-- رأس الصفحة --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
   <h2 class="fw-bold m-0" style="color: var(--text-main);">تعديل بيانات الكويز</h2>
   <a href="{{ route('dashboard.quizzes.index') }}" class="btn px-4 rounded-pill fw-bold shadow-sm-hover"
    style="background-color: var(--hover-bg); color: var(--text-main); border: 1px solid var(--border-color); transition: transform 0.2s;">
    <i class="fa-solid fa-arrow-right me-2"></i> عودة للقائمة
   </a>
  </div>

  <div class="row justify-content-center">
   <div class="col-lg-8">
    {{-- الكرت الرئيسي للفورم --}}
    <div class="custom-card p-4 shadow-sm-hover border"
     style="border-color: var(--border-color) !important; border-radius: 16px;">

     {{-- رسائل الخطأ --}}
     @if ($errors->any())
      <div class="alert alert-danger rounded-3 fw-bold mb-4">
       <ul class="mb-0">
        @foreach ($errors->all() as $error)
         <li>{{ $error }}</li>
        @endforeach
       </ul>
      </div>
     @endif

     <form action="{{ route('dashboard.quizzes.update', $quiz->id) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="row mb-4">
       <div class="col-md-12 text-start" dir="rtl">
        <label for="lesson_id" class="form-label fw-bold text-muted mb-2">الدرس المرتبط بالكويز <span
          class="text-danger">*</span></label>
        <select name="lesson_id" id="lesson_id" class="form-select bg-transparent rounded-3" required
         style="color: var(--text-main); border: 1px solid var(--border-color);">
         <option value="" disabled>-- اختر الدرس --</option>
         @foreach($lessons as $lesson)
          <option value="{{ $lesson->id }}" {{ old('lesson_id', $quiz->lesson_id) == $lesson->id ? 'selected' : '' }}>
           {{ $lesson->title }}
          </option>
         @endforeach
        </select>
       </div>
      </div>

      <div class="col-md-4 text-start" dir="rtl">
       <label for="numofquestions" class="form-label fw-bold text-muted mb-2">عدد الأسئلة (تلقائي)</label>
       <input type="number" name="numofquestions" id="numofquestions" class="form-control rounded-3"
        style="background-color: rgba(0,0,0,0.05); color: var(--text-muted); border: 1px dashed var(--border-color);"
        value="{{ $quiz->numofquestions }}" readonly>
      </div>

      <div class="col-md-4 text-start" dir="rtl">
       <label for="timelimit" class="form-label fw-bold text-muted mb-2">المدة (بالدقائق) <span
         class="text-danger">*</span></label>
       <input type="number" name="timelimit" id="timelimit" class="form-control bg-transparent rounded-3" required
        style="color: var(--text-main); border: 1px solid var(--border-color);" min="1"
        value="{{ old('timelimit', $quiz->timelimit) }}">
      </div>

      <div class="col-md-4 text-start" dir="rtl">
       <label for="totalmark" class="form-label fw-bold text-muted mb-2">العلامة الكلية (تلقائي)</label>
       <input type="number" name="totalmark" id="totalmark" class="form-control rounded-3"
        style="background-color: rgba(0,0,0,0.05); color: var(--text-muted); border: 1px dashed var(--border-color);"
        value="{{ $quiz->totalmark }}" readonly>
      </div>

      {{-- قسم الأزرار --}}
      <div class="d-flex justify-content-end align-items-center pt-4 border-top"
       style="border-color: var(--border-color) !important;">
       <button type="submit" class="btn px-5 py-2 rounded-pill fw-bold shadow-sm-hover"
        style="background-color: var(--accent-color); color: #111827; border: none; transition: transform 0.2s;">
        <i class="fa-solid fa-circle-check me-2"></i> تحديث البيانات
       </button>
      </div>

     </form>
    </div>
   </div>
  </div>
 </div>
@endsection