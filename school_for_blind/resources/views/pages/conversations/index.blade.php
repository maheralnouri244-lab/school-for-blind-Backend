@extends('layouts.app')

@section('content')
  <div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="fw-bold" style="color: var(--text-main);">المحادثات والقنوات</h4>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ route('content.monitor') }}" class="text-decoration-none"
              style="color: var(--accent-color);">مراقب المحتوى</a></li>
          <li class="breadcrumb-item active" aria-current="page" style="color: var(--text-muted);">المحادثات</li>
        </ol>
      </nav>
    </div>

    <div class="custom-card">
      <form action="{{ route('dashboard.conversations.index') }}" method="GET" class="mb-4">
        <div class="row g-3">
          <div class="col-12 col-md-3">
            <label class="form-label small fw-bold" style="color: var(--text-muted);">بحث باسم المحادثة</label>
            <div class="input-group">
              <span class="input-group-text border-0" style="background-color: var(--bg-main); color: var(--text-muted);">
                <i class="fa-solid fa-magnifying-glass"></i>
              </span>
              <input type="text" name="search" value="{{ request('search') }}"
                class="form-control border-0 shadow-none search-input" placeholder="اكتب للبحث...">
            </div>
          </div>

          <div class="col-12 col-md-3">
            <label class="form-label small fw-bold" style="color: var(--text-muted);">الأستاذ</label>
            <select name="teacher_id" class="form-select border-0 shadow-none"
              style="background-color: var(--bg-main); color: var(--text-main);">
              <option value="">جميع الأساتذة</option>
              @foreach($teachers as $teacher)
                <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                  {{ $teacher->full_name ?? ($teacher->first_name . ' ' . $teacher->last_name) }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-12 col-md-2">
            <label class="form-label small fw-bold" style="color: var(--text-muted);">المادة والصف</label>
            <select name="subject_id" class="form-select border-0 shadow-none"
              style="background-color: var(--bg-main); color: var(--text-main);">
              <option value="">جميع المواد والصفوف</option>
              @foreach($subjects as $subject)
                <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                  {{ $subject->name }} -
                  ({{ $subject->grade_level === 'ninth' ? 'تاسع' : ($subject->grade_level === 'twelfth' ? 'بكالوريا' : 'غير محدد') }})
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-12 col-md-2">
            <label class="form-label small fw-bold" style="color: var(--text-muted);">نوع المحادثة</label>
            <select name="type" class="form-select border-0 shadow-none"
              style="background-color: var(--bg-main); color: var(--text-main);">
              <option value="">جميع الأنواع</option>
              <option value="channel" {{ request('type') == 'channel' ? 'selected' : '' }}>قناة</option>
              <option value="discussion" {{ request('type') == 'discussion' ? 'selected' : '' }}>مناقشة</option>
              <option value="teacher_admin" {{ request('type') == 'teacher_admin' ? 'selected' : '' }}>محادثة إدارية
              </option>
            </select>
          </div>

          <div class="col-12 col-md-2 d-flex align-items-end gap-2">
            <button type="submit" class="btn w-100 fw-bold" style="background-color: var(--accent-color); color: #fff;">
              تصفية
            </button>
            @if(request()->hasAny(['search', 'teacher_id', 'subject_id', 'type']))
              <a href="{{ route('dashboard.conversations.index') }}" class="btn btn-outline-secondary"
                title="إلغاء الفلاتر">
                <i class="fa-solid fa-rotate-left"></i>
              </a>
            @endif
          </div>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-hover-custom align-middle mb-0" style="color: var(--text-main);">
          <thead>
            <tr style="border-bottom: 2px solid var(--border-color);">
              <th scope="col" class="pb-3 text-muted fw-normal">اسم المحادثة</th>
              <th scope="col" class="pb-3 text-muted fw-normal">النوع</th>
              <th scope="col" class="pb-3 text-muted fw-normal">المادة والصف</th>
              <th scope="col" class="pb-3 text-muted fw-normal">الأستاذ المرتبط</th>
              <th scope="col" class="pb-3 text-muted fw-normal">تاريخ الإنشاء</th>
              <th scope="col" class="pb-3 text-muted fw-normal text-start">الإجراءات</th>
            </tr>
          </thead>
          <tbody>
            @forelse($conversations as $conv)
              <tr style="border-bottom: 1px solid var(--border-color);">
                <td class="py-3 fw-bold">{{ $conv->name ?? 'محادثة بدلاً من اسم' }}</td>
                <td class="py-3">
                  @if($conv->type === 'channel')
                    <span class="badge-status bg-soft-info text-info px-2 py-1 rounded">قناة</span>
                  @elseif($conv->type === 'discussion')
                    <span class="badge-status bg-soft-warning text-warning px-2 py-1 rounded">مجموعة نقاش</span>
                  @else
                    <span class="badge-status bg-soft-success text-success px-2 py-1 rounded">محادثة إدارية</span>
                  @endif
                </td>
                <td class="py-3 text-muted">
                  @if($conv->subject)
                    {{ $conv->subject->name }}
                    ({{ $conv->subject->grade_level === 'ninth' ? 'تاسع' : ($conv->subject->grade_level === 'twelfth' ? 'بكالوريا' : 'غير محدد') }})
                  @else
                    غير محدد
                  @endif
                </td>
                <td class="py-3 text-muted">{{ $conv->teacher->full_name ?? ($conv->teacher->first_name ?? 'غير محدد') }}
                </td>
                <td class="py-3 text-muted">{{ $conv->created_at->format('Y-m-d') }}</td>
                <td class="py-3 text-start">
                  <a href="{{ route('dashboard.conversations.show', $conv->id) }}" class="btn btn-sm"
                    style="background-color: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color);">
                    عرض المحادثة <i class="fa-solid fa-arrow-left ms-1"></i>
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-4 text-muted">لا توجد محادثات تطابق الفلاتر المحددة.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-4 d-flex justify-content-center">
        {{ $conversations->links() }}
      </div>
    </div>
  </div>
@endsection