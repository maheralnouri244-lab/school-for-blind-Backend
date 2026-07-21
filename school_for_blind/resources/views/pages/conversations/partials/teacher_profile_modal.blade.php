<div class="modal-header d-flex justify-content-between align-items-center" style="border-bottom: 1px solid var(--border-color); background-color: rgba(234, 179, 8, 0.05);">
    <div class="d-flex align-items-center gap-3">
        <div class="p-2 rounded-circle bg-soft-warning d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="fa-solid fa-chalkboard-user text-warning fs-5"></i>
        </div>
        <h5 class="modal-title fw-bold mb-0" style="color: var(--text-main);">الملف الشخصي للمدرس</h5>
    </div>
    <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-4 text-start" dir="rtl">
    <div class="d-flex align-items-center gap-3 mb-4 p-3 rounded-3" style="background-color: var(--hover-bg);">
        <div>
            <h5 class="fw-bold mb-1">{{ $user->full_name }}</h5>
            <small class="text-muted"><i class="fa-solid fa-phone me-1"></i>{{ $user->phone }}</small>
        </div>
    </div>

    <form action="{{ route('punishments.apply') }}" method="POST">
        @csrf
        <input type="hidden" name="punishable_id" value="{{ $user->id }}">
        <input type="hidden" name="punishable_type" value="App\Models\Teacher">

        <h6 class="fw-bold mb-3 text-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i>تطبيق عقوبة على المدرس</h6>
        <div class="mb-3 text-start">
            <label class="form-label text-muted">اختر نوع العقوبة</label>
            <select name="punishment_id" class="form-select bg-transparent text-main border-color" required>
                <option value="">-- اختر من القائمة --</option>
                @if(isset($punishments))
                    @foreach($punishments as $punishment)
                        <option value="{{ $punishment->id }}">{{ $punishment->description }}</option>
                    @endforeach
                @endif
            </select>
        </div>
        <button type="submit" class="btn btn-danger w-100 fw-bold">تأكيد تطبيق العقوبة</button>
    </form>
</div>