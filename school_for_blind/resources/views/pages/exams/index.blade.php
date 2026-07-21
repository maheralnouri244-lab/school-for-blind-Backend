@extends('layouts.app')

@section('content')
  <div class="container-fluid py-4">

    {{-- رأس الصفحة --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold" style="color: var(--text-main);">الامتحانات والمذاكرات الإدارية</h2>
      {{-- زر الإضافة الأساسي: لون سادة فسفوري متناسق مع الهوية --}}
      <a href="{{ route('dashboard.exams.create') }}" class="btn px-4 rounded-pill fw-bold shadow-sm-hover"
        style="background-color: var(--accent-color); color: #111827; border: none; transition: transform 0.2s;">
        <i class="fa-solid fa-plus me-2"></i> إضافة امتحان جديد
      </a>
    </div>

    {{-- كرت التصفية --}}
    <div class="custom-card mb-4 p-3 border" style="border-color: var(--border-color) !important;">
      <form action="{{ route('dashboard.exams.index') }}" method="GET" class="row g-3 align-items-center">

        <div class="col-md-4">
          <input type="text" name="search" class="form-control search-input rounded-pill bg-transparent"
            style="color: var(--text-main); border: 1px solid var(--border-color);" placeholder="ابحث باسم الامتحان..."
            value="{{ request('search') }}">
        </div>

        <div class="col-md-3">
          <select name="subject_id" class="form-select search-input rounded-pill bg-transparent"
            style="color: var(--text-main); border: 1px solid var(--border-color);">
            <option value="">كل المواد</option>
            @foreach($subjects as $subject)
              <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                {{ $subject->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-3">
          <select name="status" class="form-select search-input rounded-pill bg-transparent"
            style="color: var(--text-main); border: 1px solid var(--border-color);">
            <option value="">كل الحالات</option>
            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>منشورة</option>
            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
          </select>
        </div>

        <div class="col-md-2">
          {{-- زر التصفية الأساسي: لون أزرق شفاف بدون تدرج --}}
          <button type="submit" class="btn w-100 rounded-pill fw-bold shadow-sm-hover"
            style="background-color: rgba(59, 130, 246, 0.12); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.5); transition: transform 0.2s;">
            <i class="fa-solid fa-magnifying-glass me-1"></i> تصفية
          </button>
        </div>
      </form>
    </div>

    {{-- جدول الامتحانات بتغليف ناعم ومودرن --}}
    <div class="custom-card p-0 overflow-hidden rounded-4 border"
      style="border-color: var(--border-color) !important; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
      <div class="table-responsive">
        <table class="table table-hover-custom mb-0 text-center"
          style="color: var(--text-main); border-color: var(--border-color);">
          <thead style="background-color: var(--hover-bg);">
            <tr>
              <th class="py-3 px-3 border-0">#</th>
              <th class="py-3 px-4 text-start border-0">عنوان الامتحان</th>
              <th class="py-3 px-3 border-0">المادة</th>
              <th class="py-3 px-3 border-0">موعد الامتحان</th>
              <th class="py-3 px-3 border-0">المدة</th>
              <th class="py-3 px-3 border-0">الحالة</th>
              <th class="py-3 px-4 border-0">الإجراءات</th>
            </tr>
          </thead>
          <tbody>
            @forelse($exams as $exam)
              <tr style="border-bottom: 1px solid var(--border-color);">
                <td class="align-middle px-3">{{ $loop->iteration }}</td>

                {{-- عنوان الامتحان مدمج مع أيقونة زرقاء للملائمة البصرية --}}
                <td class="align-middle px-4 text-start fw-bold">
                  <div class="d-flex align-items-center gap-3">
                    <div class="p-2 rounded-circle bg-soft-info d-flex align-items-center justify-content-center"
                      style="width: 38px; height: 38px;">
                      <i class="fa-solid fa-file-pen text-info" style="font-size: 1rem;"></i>
                    </div>
                    <span>{{ $exam->title }}</span>
                  </div>
                </td>

                <td class="align-middle px-3">{{ $exam->subject->name ?? 'غير محدد' }}</td>
                <td class="align-middle px-3" dir="ltr" style="color: var(--text-muted);">
                  @if($exam->exam_date)
                    {{ \Carbon\Carbon::parse($exam->exam_date)->format('Y-m-d H:i') }}
                  @else
                    <span class="badge bg-secondary opacity-75 rounded-pill px-3 py-2 fw-normal">غير محدد</span>
                  @endif
                </td>
                <td class="align-middle px-3">{{ $exam->duration_minutes }} دقيقة</td>

                {{-- توحيد شكل حالات الامتحان --}}
                <td class="align-middle px-3">
                  @if($exam->is_published)
                    <span class="badge px-3 py-2 rounded-pill fw-normal"
                      style="background-color: rgba(163, 230, 53, 0.15); color: var(--accent-color); border: 1px solid var(--accent-color);">
                      <i class="fa-solid fa-earth-americas me-1"></i> منشور
                    </span>
                  @else
                    <span class="badge px-3 py-2 rounded-pill fw-normal"
                      style="background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid #f59e0b;">
                      <i class="fa-solid fa-lock me-1"></i> مسودة
                    </span>
                  @endif
                </td>

                {{-- أزرار الإجراءات المتدرجة مع التوهج الناعم --}}
                <td class="align-middle px-4">
                  <div class="d-flex justify-content-center gap-2">

                    {{-- زر التسليمات: تدرج رمادي لأزرق --}}
                    <a href="{{ route('dashboard.exams.submissions', $exam->id) }}"
                      class="btn btn-sm text-white shadow-sm-hover d-flex align-items-center justify-content-center"
                      style="width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #9ca3af, #3b82f6); box-shadow: 0 0 10px rgba(59, 130, 246, 0.3); border: none; transition: all 0.2s;"
                      title="أوراق وتسليمات الطلاب">
                      <i class="fa-solid fa-file-signature"></i>
                    </a>

                    {{-- زر التفاصيل: تدرج رمادي لفسفوري --}}
                    <a href="{{ route('dashboard.exams.show', $exam->id) }}"
                      class="btn btn-sm shadow-sm-hover d-flex align-items-center justify-content-center"
                      style="width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #9ca3af, var(--accent-color)); box-shadow: 0 0 10px rgba(163, 230, 53, 0.3); color: #111827; border: none; transition: all 0.2s;"
                      title="إدارة الأسئلة والتفاصيل">
                      <i class="fa-solid fa-eye"></i>
                    </a>

                    {{-- زر التعديل: تدرج رمادي لأزرق --}}
                    <a href="{{ route('dashboard.exams.edit', $exam->id) }}"
                      class="btn btn-sm text-white shadow-sm-hover d-flex align-items-center justify-content-center"
                      style="width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #9ca3af, #3b82f6); box-shadow: 0 0 10px rgba(59, 130, 246, 0.3); border: none; transition: all 0.2s;"
                      title="تعديل">
                      <i class="fa-solid fa-pen-to-square"></i>
                    </a>

                    {{-- زر الحذف: تدرج رمادي لأحمر (للحفاظ على معنى الحذف الخطير ولكن بنمط متناسق) --}}
                    <form action="{{ route('dashboard.exams.destroy', $exam->id) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('هل أنت متأكد من حذف هذا الامتحان نهائياً؟');">
                      @csrf
                      @method('DELETE')
                      <button type="submit"
                        class="btn btn-sm text-white shadow-sm-hover d-flex align-items-center justify-content-center"
                        style="width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #9ca3af, #ef4444); box-shadow: 0 0 10px rgba(239, 68, 68, 0.3); border: none; transition: all 0.2s;"
                        title="حذف">
                        <i class="fa-solid fa-trash"></i>
                      </button>
                    </form>

                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                  <i class="fa-solid fa-folder-open fs-1 d-block mb-3 opacity-50"></i>
                  لا يوجد امتحانات مضافة حالياً.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if($exams->hasPages())
        <div class="d-flex justify-content-center p-3 border-top" style="border-color: var(--border-color) !important;"
          dir="ltr">
          {{ $exams->links() }}
        </div>
      @endif
    </div>

  </div>
@endsection