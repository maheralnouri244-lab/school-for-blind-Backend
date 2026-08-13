<header class="d-flex align-items-center justify-content-between p-3 border-bottom shadow-sm"
  style="background-color: var(--bg-card); border-color: var(--border-color) !important;">

  <div class="dropdown">
    <div class="d-flex align-items-center gap-3 p-2 rounded cursor-pointer transition-all shadow-sm-hover"
      data-bs-toggle="dropdown" aria-expanded="false">

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

      <i class="fa-solid fa-chevron-down ms-2 transition-transform"
        style="font-size: 0.8rem; color: var(--text-muted);"></i>
    </div>

    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2"
      style="background-color: var(--bg-card); border-radius: 12px; min-width: 200px;">

      <li>
        <a class="dropdown-item d-flex align-items-center gap-3 py-2 px-3 transition-all" href="#">
          <i class="fa-regular fa-circle-user text-muted fs-5"></i>
          <span class="fw-bold" style="color: var(--text-main); font-size: 0.9rem;">الملف الشخصي</span>
        </a>
      </li>

      <li>
        <hr class="dropdown-divider my-2" style="border-color: var(--border-color);">
      </li>
    </ul>
  </div>

  <div class="d-none d-md-flex align-items-center bg-soft-info px-3 py-2 rounded-pill"
    style="border: 1px solid var(--border-color);">
    <i class="fa-regular fa-calendar text-info me-2 ms-2"></i>
    <span class="fw-bold" style="color: var(--text-main); font-size: 0.85rem;">
      {{ \Carbon\Carbon::now()->locale('ar')->translatedFormat('l، j F Y') }}
    </span>
  </div>

</header>