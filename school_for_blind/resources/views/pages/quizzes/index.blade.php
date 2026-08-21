@extends('layouts.app')

@section('content')
  <div class="container-fluid py-4">

    {{-- رأس الصفحة --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold" style="color: var(--text-main);">إدارة الكويزات التفاعلية</h2>
    </div>

    {{-- كرت التصفية --}}
    {{-- كرت التصفية --}}
    <div class="custom-card mb-4 p-3 border" style="border-color: var(--border-color) !important;">
      <form action="{{ route('dashboard.quizzes.index') }}" method="GET" class="row g-3 align-items-center">

        <div class="col-md-4">
          <input type="text" name="search" class="form-control search-input rounded-pill bg-transparent"
            style="color: var(--text-main); border: 1px solid var(--border-color);" placeholder="ابحث باسم الدرس..."
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

        {{-- الفلتر الجديد للأرشيف --}}
        <div class="col-md-3">
          <select name="archive_status" class="form-select search-input rounded-pill bg-transparent text-danger fw-bold"
            style="border: 1px solid var(--border-color);">
            <option value="active" {{ request('archive_status') == 'active' ? 'selected' : '' }}>الكويزات النشطة</option>
            <option value="archived" {{ request('archive_status') == 'archived' ? 'selected' : '' }}>الأرشيف (المحذوفة)
            </option>
            <option value="all" {{ request('archive_status') == 'all' ? 'selected' : '' }}>عرض الكل (نشط + مؤرشف)</option>
          </select>
        </div>

        <div class="col-md-2">
          <button type="submit" class="btn w-100 rounded-pill fw-bold shadow-sm-hover"
            style="background-color: rgba(59, 130, 246, 0.12); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.5); transition: transform 0.2s;">
            <i class="fa-solid fa-magnifying-glass me-1"></i> تصفية
          </button>
        </div>
      </form>
    </div>

    {{-- جدول الكويزات --}}
    <div class="custom-card p-0 overflow-hidden rounded-4 border"
      style="border-color: var(--border-color) !important; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
      <div class="table-responsive">
        <table class="table table-hover-custom mb-0 text-center"
          style="color: var(--text-main); border-color: var(--border-color);">
          <thead style="background-color: var(--hover-bg);">
            <tr>
              <th class="py-3 px-3 border-0">#</th>
              <th class="py-3 px-4 text-start border-0">الدرس والمادة</th>
              <th class="py-3 px-3 border-0">الأستاذ</th>
              <th class="py-3 px-3 border-0">عدد الأسئلة</th>
              <th class="py-3 px-3 border-0">المدة</th>
              <th class="py-3 px-3 border-0">العلامة الكلية</th>
              <th class="py-3 px-4 border-0">الإجراءات</th>
            </tr>
          </thead>
          <tbody>
            @forelse($quizzes as $quiz)
              <tr style="border-bottom: 1px solid var(--border-color);">
                <td class="align-middle px-3">{{ $loop->iteration }}</td>

                <td class="align-middle px-4 text-start fw-bold">
                  <div class="d-flex align-items-center gap-3">
                    <div class="p-2 rounded-circle d-flex align-items-center justify-content-center"
                      style="width: 38px; height: 38px; background-color: rgba(163, 230, 53, 0.15);">
                      <i class="fa-solid fa-bolt" style="font-size: 1rem; color: var(--accent-color);"></i>
                    </div>
                    <div>
                      <span class="d-block">{{ $quiz->lesson->title ?? 'درس غير محدد' }}</span>
                      <small class="text-muted fw-normal">{{ $quiz->subject->name ?? 'مادة غير محددة' }}</small>
                    </div>
                  </div>
                </td>

                <td class="align-middle px-3">{{ $quiz->teacher->full_name ?? 'غير محدد' }}</td>
                <td class="align-middle px-3">{{ $quiz->numofquestions }} سؤال</td>
                <td class="align-middle px-3">{{ $quiz->timelimit }} دقيقة</td>
                <td class="align-middle px-3 fw-bold text-success">{{ $quiz->totalmark }}</td>

                <td class="align-middle px-4">
                  <div class="d-flex justify-content-center gap-2">

                    @if($quiz->trashed())
                      {{-- أزرار العرض فقط في حالة الأرشيف --}}
                      <span
                        class="badge bg-soft-danger text-danger border border-danger d-flex align-items-center px-3 me-2">مؤرشف</span>
                    @else
                      {{-- الأزرار التشغيلية تظهر فقط إذا كان الكويز نشطاً --}}
                      <form action="{{ route('dashboard.quizzes.regrade', $quiz->id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('هل أنت متأكد من رغبتك بإعادة تصحيح أوراق جميع الطلاب لهذا الكويز؟');">
                        @csrf
                        <button type="submit"
                          class="btn btn-sm text-white shadow-sm-hover d-flex align-items-center justify-content-center"
                          style="width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #9ca3af, #f59e0b); box-shadow: 0 0 10px rgba(245, 158, 11, 0.3); border: none; transition: all 0.2s;"
                          title="إعادة التصحيح التلقائي">
                          <i class="fa-solid fa-rotate-right"></i>
                        </button>
                      </form>
                    @endif

                    <a href="{{ route('dashboard.quizzes.submissions', $quiz->id) }}"
                      class="btn btn-sm shadow-sm-hover d-flex align-items-center justify-content-center"
                      style="width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #9ca3af, var(--accent-color)); box-shadow: 0 0 10px rgba(163, 230, 53, 0.3); color: #111827; border: none; transition: all 0.2s;"
                      title="عرض التسليمات والتفاصيل">
                      <i class="fa-solid fa-graduation-cap"></i>
                    </a>

                    <a href="{{ route('dashboard.quizzes.show', $quiz->id) }}"
                      class="btn btn-sm text-white shadow-sm-hover d-flex align-items-center justify-content-center"
                      style="width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #9ca3af, #3b82f6); box-shadow: 0 0 10px rgba(59, 130, 246, 0.3); border: none; transition: all 0.2s;"
                      title="إدارة الأسئلة والتفاصيل">
                      <i class="fa-solid fa-eye"></i>
                    </a>

                    @if(!$quiz->trashed())
                      <form action="{{ route('dashboard.quizzes.destroy', $quiz->id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('هل أنت متأكد من حذف هذا الكويز نهائياً؟ تنبيه: سيتم حذف جميع تسليمات الطلاب المرتبطة به!');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                          class="btn btn-sm text-white shadow-sm-hover d-flex align-items-center justify-content-center"
                          style="width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #9ca3af, #ef4444); box-shadow: 0 0 10px rgba(239, 68, 68, 0.3); border: none; transition: all 0.2s;"
                          title="أرشفة">
                          <i class="fa-solid fa-trash"></i>
                        </button>
                      </form>
                    @endif

                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                  <i class="fa-solid fa-folder-open fs-1 d-block mb-3 opacity-50"></i>
                  لا يوجد كويزات مضافة حالياً.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if($quizzes->hasPages())
        <div class="d-flex justify-content-center p-3 border-top" style="border-color: var(--border-color) !important;"
          dir="ltr">
          {{ $quizzes->links() }}
        </div>
      @endif
    </div>

  </div>
@endsection