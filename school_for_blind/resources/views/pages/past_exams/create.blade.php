@extends('layouts.app')

@section('content')
  <div class="container-fluid py-4">

    {{-- رأس الصفحة --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold m-0" style="color: var(--text-main);">إضافة دورة وزارية جديدة</h2>
      <a href="{{ route('dashboard.past-exams.index') }}" class="btn px-4 rounded-pill fw-bold shadow-sm-hover"
        style="background-color: var(--hover-bg); color: var(--text-main); border: 1px solid var(--border-color); transition: transform 0.2s;">
        <i class="fa-solid fa-arrow-right me-2"></i> عودة للقائمة
      </a>
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-8">
        {{-- الكرت الرئيسي للفورم --}}
        <div class="custom-card p-4 shadow-sm-hover border"
          style="border-color: var(--border-color) !important; border-radius: 16px;">

          {{-- عرض رسائل الخطأ إن وجدت --}}
          @if ($errors->any())
            <div class="alert alert-danger rounded-3 fw-bold mb-4">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form action="{{ route('dashboard.past-exams.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- عنوان الدورة --}}
            <div class="mb-4 text-start" dir="rtl">
              <label for="title" class="form-label fw-bold text-muted mb-2">عنوان الدورة <span
                  class="text-danger">*</span></label>
              <input type="text" name="title" id="title" class="form-control bg-transparent rounded-3" required
                style="color: var(--text-main); border: 1px solid var(--border-color);"
                placeholder="مثال: دورة 2023 - رياضيات" value="{{ old('title') }}">
            </div>

            <div class="row mb-4 g-4">
              {{-- المادة --}}
              <div class="col-md-6 text-start" dir="rtl">
                <label for="subject_id" class="form-label fw-bold text-muted mb-2">المادة <span
                    class="text-danger">*</span></label>
                <select name="subject_id" id="subject_id" class="form-select bg-transparent rounded-3" required
                  style="color: var(--text-main); border: 1px solid var(--border-color);">
                  <option value="" selected disabled>-- اختر المادة --</option>
                  @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                      {{ $subject->name }}
                    </option>
                  @endforeach
                </select>
              </div>

              {{-- السنة الدراسية --}}
              <div class="col-md-6 text-start" dir="rtl">
                <label for="year" class="form-label fw-bold text-muted mb-2">السنة الدراسية <span
                    class="text-danger">*</span></label>
                <input type="number" name="year" id="year" class="form-control bg-transparent rounded-3" required
                  min="2000" style="color: var(--text-main); border: 1px solid var(--border-color);"
                  max="{{ date('Y') + 1 }}" value="{{ old('year', date('Y')) }}">
              </div>
            </div>

            <div class="row mb-4 g-4">
              {{-- الفصل / الدورة --}}
              <div class="col-md-6 text-start" dir="rtl">
                <label for="session" class="form-label fw-bold text-muted mb-2">الدورة <span
                    class="text-danger">*</span></label>
                <select name="session" id="session" class="form-select bg-transparent rounded-3" required
                  style="color: var(--text-main); border: 1px solid var(--border-color);">
                  <option value="first" {{ old('session') == 'first' ? 'selected' : '' }}>الأولى</option>
                  <option value="second" {{ old('session') == 'second' ? 'selected' : '' }}>الثانية</option>
                  <option value="complementary" {{ old('session') == 'complementary' ? 'selected' : '' }}>تكميلية</option>
                </select>
              </div>

              {{-- ملف الحل الصوتي --}}
              <div class="col-md-6 text-start" dir="rtl">
                <label for="voice_solution" class="form-label fw-bold text-muted mb-2">ملف الحل الصوتي الشامل
                  (اختياري)</label>
                <input type="file" name="voice_solution" id="voice_solution" class="form-control bg-transparent rounded-3"
                  accept="audio/*" style="color: var(--text-main); border: 1px solid var(--border-color);">
                <small class="text-muted d-block mt-1">الملفات المدعومة: mp3, wav, aac (الحد الأقصى: 20MB)</small>
              </div>
            </div>

            {{-- قسم الحفظ السفلي الموحد --}}
            <div class="d-flex justify-content-between align-items-center pt-4 border-top"
              style="border-color: var(--border-color) !important;">
              <div>
                <span class="badge px-3 py-2 rounded-pill fw-normal"
                  style="background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid #f59e0b;">
                  <i class="fa-solid fa-lock me-1"></i> سيتم حفظ الدورة كمسودة أولاً
                </span>
              </div>
              <button type="submit" class="btn px-5 py-2 rounded-pill fw-bold shadow-sm-hover"
                style="background-color: var(--accent-color); color: #111827; border: none; transition: transform 0.2s;">
                <i class="fa-solid fa-save me-2"></i> حفظ كمسودة
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
@endsection