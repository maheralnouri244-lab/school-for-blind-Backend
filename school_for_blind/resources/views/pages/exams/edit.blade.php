@extends('layouts.app')

@section('content')
 <div class="container-fluid py-4">

  {{-- رأس الصفحة --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
   <h2 class="fw-bold m-0" style="color: var(--text-main);">تعديل بيانات الامتحان</h2>
   <a href="{{ route('dashboard.exams.index') }}" class="btn px-4 rounded-pill fw-bold shadow-sm-hover"
      style="background-color: var(--hover-bg); color: var(--text-main); border: 1px solid var(--border-color); transition: transform 0.2s;">
    <i class="fa-solid fa-arrow-right me-2"></i> عودة للقائمة
   </a>
  </div>

  <div class="row justify-content-center">
   <div class="col-lg-8">
    {{-- الكرت الرئيسي للفورم --}}
    <div class="custom-card p-4 shadow-sm-hover border" style="border-color: var(--border-color) !important; border-radius: 16px;">

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

     <form action="{{ route('dashboard.exams.update', $exam->id) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="mb-4 text-start" dir="rtl">
       <label for="title" class="form-label fw-bold text-muted mb-2">عنوان الامتحان <span class="text-danger">*</span></label>
       <input type="text" name="title" id="title" class="form-control bg-transparent rounded-3" required
        style="color: var(--text-main); border: 1px solid var(--border-color);"
        value="{{ old('title', $exam->title) }}">
      </div>

      <div class="mb-4 text-start" dir="rtl">
       <label for="description" class="form-label fw-bold text-muted mb-2">تفاصيل / نطاق الامتحان</label>
       <textarea name="description" id="description" class="form-control bg-transparent rounded-3"
        style="color: var(--text-main); border: 1px solid var(--border-color);"
        rows="3">{{ old('description', $exam->description) }}</textarea>
      </div>

      <div class="row mb-4">
       <div class="col-md-12 text-start" dir="rtl">
        <label for="subject_id" class="form-label fw-bold text-muted mb-2">المادة <span class="text-danger">*</span></label>
        <select name="subject_id" id="subject_id" class="form-select bg-transparent rounded-3" required
                style="color: var(--text-main); border: 1px solid var(--border-color);">
         <option value="" disabled>-- اختر المادة --</option>
         @foreach($subjects as $subject)
          <option value="{{ $subject->id }}" {{ old('subject_id', $exam->subject_id) == $subject->id ? 'selected' : '' }}>
           {{ $subject->name }}
          </option>
         @endforeach
        </select>
       </div>
      </div>

      <div class="row mb-5 g-4">
       <div class="col-md-6 text-start" dir="rtl">
        <label for="exam_date" class="form-label fw-bold text-muted mb-2">موعد الامتحان (التاريخ والوقت) <span class="text-danger">*</span></label>
        <input type="datetime-local" name="exam_date" id="exam_date" class="form-control bg-transparent rounded-3" required
               style="color: var(--text-main); border: 1px solid var(--border-color);"
               value="{{ old('exam_date', \Carbon\Carbon::parse($exam->exam_date)->format('Y-m-d\TH:i')) }}">
       </div>

       <div class="col-md-6 text-start" dir="rtl">
        <label for="duration_minutes" class="form-label fw-bold text-muted mb-2">المدة (بالدقائق) <span class="text-danger">*</span></label>
        <input type="number" name="duration_minutes" id="duration_minutes" class="form-control bg-transparent rounded-3" required
               style="color: var(--text-main); border: 1px solid var(--border-color);"
               min="5" max="300" value="{{ old('duration_minutes', $exam->duration_minutes) }}">
       </div>
      </div>

      {{-- قسم الأزرار وحالة الامتحان السفلية --}}
      <div class="d-flex justify-content-between align-items-center pt-4 border-top" style="border-color: var(--border-color) !important;">
       <div>
        @if(!$exam->is_published)
         <span class="badge px-3 py-2 rounded-pill fw-normal" style="background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid #f59e0b;">
             <i class="fa-solid fa-lock me-1"></i> الحالة: مسودة
         </span>
        @else
         <span class="badge px-3 py-2 rounded-pill fw-normal" style="background-color: rgba(163, 230, 53, 0.15); color: var(--accent-color); border: 1px solid var(--accent-color);">
             <i class="fa-solid fa-earth-americas me-1"></i> الحالة: منشور للطلاب
         </span>
        @endif
       </div>
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