<div class="d-flex justify-content-between align-items-center mb-4">
  <h5 class="fw-bold m-0" style="color: var(--text-main);">الملاحظات</h5>
</div>

<div class="row g-4 mb-4">
  <div class="col-lg-6">
    <div class="custom-card h-100 p-0 overflow-hidden">
      <div class="p-4 border-bottom d-flex justify-content-between align-items-center"
        style="background-color: rgba(245, 158, 11, 0.05); border-color: var(--border-color) !important;">
        <h5 class="fw-bold m-0" style="color: var(--text-main);"><i class="fa-solid fa-lock text-warning me-2"></i>
          ملاحظات خاصة</h5>
        @if(Auth::guard('admin')->check())
          <button type="button" class="btn btn-sm btn-outline-custom d-flex align-items-center gap-2"
            data-bs-toggle="modal" data-bs-target="#addNoteModal" data-note-type="private">
            <i class="fa-solid fa-plus"></i> إضافة
          </button>
        @endif
      </div>
      <div class="p-4 d-flex flex-column gap-3" style="max-height: 400px; overflow-y: auto;">
        @forelse($privateNotes ?? [] as $note)
          <div class="p-3 rounded shadow-sm-hover cursor-pointer"
            style="background-color: var(--hover-bg); border: 1px solid var(--border-color); transition: all 0.2s;"
            data-bs-toggle="modal" data-bs-target="#noteModal{{ $note->id }}">
            <p class="mb-1 text-truncate" style="color: var(--text-main); max-width: 100%;">{{ $note->content }}</p>
            <small class="text-muted">{{ $note->created_at->locale('ar')->diffForHumans() }}</small>
          </div>
        @empty
          <div class="text-center py-4 text-muted">لا توجد ملاحظات خاصة.</div>
        @endforelse
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="custom-card h-100 p-0 overflow-hidden">
      <div class="p-4 border-bottom d-flex justify-content-between align-items-center"
        style="background-color: rgba(59, 130, 246, 0.05); border-color: var(--border-color) !important;">
        <h5 class="fw-bold m-0" style="color: var(--text-main);"><i
            class="fa-solid fa-earth-americas text-info me-2"></i> ملاحظات عامة</h5>
        @if(Auth::guard('admin')->check())
          <button type="button" class="btn btn-sm btn-outline-custom d-flex align-items-center gap-2"
            data-bs-toggle="modal" data-bs-target="#addNoteModal" data-note-type="public">
            <i class="fa-solid fa-plus"></i> إضافة
          </button>
        @endif
      </div>
      <div class="p-4 d-flex flex-column gap-3" style="max-height: 400px; overflow-y: auto;">
        @forelse($publicNotes ?? [] as $note)
          <div class="p-3 rounded shadow-sm-hover cursor-pointer"
            style="background-color: var(--hover-bg); border: 1px solid var(--border-color); transition: all 0.2s;"
            data-bs-toggle="modal" data-bs-target="#noteModal{{ $note->id }}">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="badge bg-soft-info text-info rounded-pill px-2 py-1" style="font-size: 0.75rem;">
                ID: {{ $note->admin_id }} - {{ $note->admin->role ?? 'مدير' }}
              </span>
              <small class="text-muted">{{ $note->created_at->locale('ar')->diffForHumans() }}</small>
            </div>
            <p class="mb-0 text-truncate" style="color: var(--text-main); max-width: 100%;">{{ $note->content }}</p>
          </div>
        @empty
          <div class="text-center py-4 text-muted">لا توجد ملاحظات عامة.</div>
        @endforelse
      </div>
    </div>
  </div>
</div>

@foreach(collect($privateNotes ?? [])->merge($publicNotes ?? []) as $note)
  <div class="modal fade glass-modal" id="noteModal{{ $note->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg rounded-4">
        <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
          <h5 class="modal-title fw-bold">
            @if($note->type === 'public')
              ملاحظة عامة ({{ $note->admin->role ?? 'مدير' }})
            @else
              ملاحظة خاصة
            @endif
          </h5>
          <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="p-3 rounded" style="background-color: var(--hover-bg); border: 1px solid var(--border-color);">
            <p class="mb-0" style="white-space: pre-wrap; line-height: 1.8; color: var(--text-main);">{{ $note->content }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
@endforeach

@if(Auth::guard('admin')->check())
  <div class="modal fade glass-modal" id="addNoteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg rounded-4">
        <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
          <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
            <i class="fa-solid fa-pen-to-square text-primary"></i> إضافة ملاحظة جديدة
          </h5>
          <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{ route('notes.store') }}" method="POST">
          @csrf
          <input type="hidden" name="type" id="noteTypeInput" value="private">

          <div class="modal-body p-4">
            <div class="mb-3 d-flex align-items-center gap-2 p-3 rounded"
              style="background-color: var(--hover-bg); border: 1px solid var(--border-color);">
              <i class="fa-solid fa-circle-info text-muted"></i>
              <span id="noteTypeLabel" class="fw-bold" style="color: var(--text-main);"></span>
            </div>
            <div class="mb-0">
              <label class="form-label text-muted fw-bold">نص الملاحظة</label>
              <textarea name="content" class="form-control" rows="5" required style="resize: none;"
                placeholder="اكتب ملاحظتك هنا..."></textarea>
            </div>
          </div>
          <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
            <button type="button" class="btn btn-outline-custom px-4 fw-bold" data-bs-dismiss="modal">إلغاء</button>
            <button type="submit" class="btn btn-primary px-4 fw-bold">حفظ الملاحظة</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const addNoteModal = document.getElementById('addNoteModal');
      if (addNoteModal) {
        addNoteModal.addEventListener('show.bs.modal', function (event) {
          const button = event.relatedTarget;
          const type = button.getAttribute('data-note-type');
          const typeInput = document.getElementById('noteTypeInput');
          const typeLabel = document.getElementById('noteTypeLabel');

          typeInput.value = type;
          if (type === 'private') {
            typeLabel.innerHTML = 'سيتم إضافة هذه الملاحظة كـ <span class="text-warning">ملاحظة خاصة</span> (تظهر لك فقط)';
          } else {
            typeLabel.innerHTML = 'سيتم إضافة هذه الملاحظة كـ <span class="text-info">ملاحظة عامة</span> (تظهر لجميع المدراء)';
          }
        });
      }
    });
  </script>
@endif