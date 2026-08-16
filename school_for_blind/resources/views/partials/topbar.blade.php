<header class="d-flex align-items-center justify-content-between p-3 border-bottom shadow-sm"
  style="background-color: var(--bg-card); border-color: var(--border-color) !important;">

  <div class="d-flex align-items-center gap-4">
    <a href="{{ route('my-space') }}"
      class="btn d-flex align-items-center gap-2 px-4 py-2 rounded-pill fw-bold shadow-sm transition-all text-white"
      style="background-color: #3b82f6; border: none; font-size: 0.9rem;">
      <i class="fa-solid fa-briefcase"></i>
      مساحتي
    </a>

    <div class="d-flex align-items-center gap-3 p-2 rounded transition-all">
      <img
        src="{{ Auth::guard('admin')->user()->profile_image ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::guard('admin')->user()->name ?? 'Admin') . '&background=3b82f6&color=fff' }}"
        alt="Admin Avatar" class="rounded-circle shadow-sm" width="45" height="45"
        style="border: 2px solid var(--accent-color);">

      <div class="d-flex flex-column lh-1">
        <span class="fw-bold fs-6" style="color: var(--text-main);">
          {{ Auth::guard('admin')->user()->name ?? 'مدير النظام' }}
        </span>
        <span class="mt-2 fw-medium" style="color: var(--text-muted); font-size: 0.75rem;">
          {{ Auth::guard('admin')->user()->role }}
        </span>
      </div>
    </div>
  </div>

  <div class="d-none d-md-flex align-items-center bg-soft-info px-3 py-2 rounded-pill"
    style="border: 1px solid var(--border-color);">
    <i class="fa-regular fa-calendar text-info me-2 ms-2"></i>
    <span class="fw-bold" style="color: var(--text-main); font-size: 0.85rem;">
      {{ \Carbon\Carbon::now()->locale('ar')->translatedFormat('l، j F Y') }}
    </span>
  </div>

</header>