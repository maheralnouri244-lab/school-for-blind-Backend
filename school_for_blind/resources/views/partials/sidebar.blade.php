<div class="d-flex flex-column h-100 p-3" style="color: var(--text-main); background-color: var(--bg-card);">

  <a href="/"
    class="d-flex align-items-center mb-4 mt-2 text-decoration-none justify-content-between transition-transform shadow-sm-hover px-2">
    <span class="fs-3 fw-bold" style="color: var(--text-main);">SESB</span>
    <i class="fa-regular fa-face-laugh-beam fs-2" style="color: var(--accent-color);"></i>
  </a>

  <hr style="border-color: var(--border-color); opacity: 1;">

  <ul class="nav nav-pills flex-column mb-auto gap-2 overflow-y-auto" style="overflow-x: hidden;">

    <li class="nav-item">
      <a href="{{ route('dashboard') }}"
        class="nav-link py-3 px-3 d-flex align-items-center justify-content-between {{ request()->routeIs('dashboard') ? 'nav-link-active' : 'nav-link-custom' }}">
        <span class="fw-bold">لوحة القيادة</span>
        <i class="fa-solid fa-gauge-high"></i>
      </a>
    </li>

    <li class="nav-item mt-3 mb-1 px-3 text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">أقسام
      رئيسية</li>

    <li class="nav-item">
      <a href="{{ route('dashboard.users.index') }}"
        class="nav-link py-2 px-3 d-flex align-items-center justify-content-between {{ request()->routeIs('dashboard.users.*') ? 'nav-link-active' : 'nav-link-custom' }}">
        <span>إدارة المستخدمين والطلبات</span>
        <i class="fa-solid fa-users-gear text-muted"></i>
      </a>
    </li>

    <li class="nav-item">
      <a href="{{ route('admin.active-calls') }}"
        class="nav-link py-2 px-3 d-flex align-items-center justify-content-between {{ request()->routeIs('admin.active-calls', 'rooms.*') ? 'nav-link-active' : 'nav-link-custom' }}">
        <span>الدروس الجارية حالياً</span>
        <div class="d-flex align-items-center gap-2">
          @if(isset($activeCallsCount) && $activeCallsCount > 0)
            <span class="spinner-grow spinner-grow-sm text-danger" role="status"
              style="--bs-spinner-width: 0.6rem; --bs-spinner-height: 0.6rem; animation-duration: 1.5s;"></span>
          @endif
          <i class="fa-solid fa-headset text-muted"></i>
        </div>
      </a>
    </li>

    <li class="nav-item">
      <a href="{{ route('content.monitor') }}"
        class="nav-link py-2 px-3 d-flex align-items-center justify-content-between {{ request()->routeIs('content.monitor', 'reports.*', 'punishments.*') ? 'nav-link-active' : 'nav-link-custom' }}">
        <span>مراقب المحتوى</span>
        <i class="fa-solid fa-shield-halved text-muted"></i>
      </a>
    </li>

    <li class="nav-item">
      <a href="{{ route('dashboard.support.index') }}"
        class="nav-link py-2 px-3 d-flex align-items-center justify-content-between {{ request()->routeIs('dashboard.support.*') ? 'nav-link-active' : 'nav-link-custom' }}">
        <span>طلبات الدعم الفني</span>
        <i class="fa-solid fa-headset text-muted"></i>
      </a>
    </li>

    <li class="nav-item mt-3 mb-1 px-3 text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">إضافي</li>

    <li class="nav-item">
      <a href="{{ route('classes') }}"
        class="nav-link py-2 px-3 d-flex align-items-center justify-content-between {{ request()->routeIs('classes') ? 'nav-link-active' : 'nav-link-custom' }}">
        <span>صفوفنا</span>
        <i class="fa-regular fa-folder-open text-muted"></i>
      </a>
    </li>

    <li class="nav-item">
      <a href="{{ route('announcements.index') }}"
        class="nav-link py-2 px-3 d-flex align-items-center justify-content-between {{ request()->routeIs('announcements.*') ? 'nav-link-active' : 'nav-link-custom' }}">
        <span>الإعلانات والتنبيهات</span>
        <i class="fa-solid fa-bullhorn text-warning"></i>
      </a>
    </li>

    <li class="nav-item">
      <a href="{{ route('logs.index') }}"
        class="nav-link py-2 px-3 d-flex align-items-center justify-content-between {{ request()->routeIs('logs.index') ? 'nav-link-active' : 'nav-link-custom' }}">
        <span>سجلاتنا</span>
        <i class="fa-solid fa-table-list text-muted"></i>
      </a>
    </li>

    <li class="nav-item mt-3 mb-1 px-3 text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">النظام
      المالي</li>

    <li class="nav-item">
      <a class="nav-link py-2 px-3 d-flex align-items-center justify-content-between cursor-pointer {{ request()->routeIs('financial.*') ? 'nav-link-active' : 'nav-link-custom' }}"
        data-bs-toggle="collapse" href="#financialDropdown" role="button"
        aria-expanded="{{ request()->routeIs('financial.*') ? 'true' : 'false' }}">
        <span>الإدارة المالية</span>
        <i class="fa-solid fa-vault text-muted"></i>
      </a>

      <div class="collapse {{ request()->routeIs('financial.*') ? 'show' : '' }} mt-1" id="financialDropdown">
        <ul class="nav flex-column me-3 py-2 pe-3 gap-1" style="border-right: 2px solid var(--border-color);">
          <li class="nav-item">
            <a href="{{ route('financial.index') }}"
              class="nav-link py-2 px-3 rounded {{ request()->routeIs('financial.index') ? 'fw-bold text-success bg-soft-success' : 'text-muted nav-link-custom' }}"
              style="font-size: 0.85rem;">
              اللوحة الرئيسية
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('financial.donations') }}"
              class="nav-link py-2 px-3 rounded {{ request()->routeIs('financial.donations') ? 'fw-bold text-success bg-soft-success' : 'text-muted nav-link-custom' }}"
              style="font-size: 0.85rem;">
              سجل التبرعات
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('financial.salaries') }}"
              class="nav-link py-2 px-3 rounded {{ request()->routeIs('financial.salaries') ? 'fw-bold text-success bg-soft-success' : 'text-muted nav-link-custom' }}"
              style="font-size: 0.85rem;">
              رواتب الأساتذة
            </a>
          </li>
          {{-- <li class="nav-item">
            <a href="{{ route('financial.rewards') }}"
              class="nav-link py-2 px-3 rounded {{ request()->routeIs('financial.rewards') ? 'fw-bold text-success bg-soft-success' : 'text-muted nav-link-custom' }}"
              style="font-size: 0.85rem;">
              طلبات المكافآت
            </a>
          </li> --}}
          <li class="nav-item">
            <a href="{{ route('financial.transactions') }}"
              class="nav-link py-2 px-3 rounded {{ request()->routeIs('financial.transactions') ? 'fw-bold text-success bg-soft-success' : 'text-muted nav-link-custom' }}"
              style="font-size: 0.85rem;">
              التدقيق المالي
            </a>
          </li>
        </ul>
      </div>
    </li>

    <li class="nav-item mt-3 mb-1 px-3 text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">المحتوى
      الأكاديمي</li>

    <li class="nav-item">
      <a href="{{ route('dashboard.past-exams.index') }}"
        class="nav-link py-2 px-3 d-flex align-items-center justify-content-between {{ request()->routeIs('dashboard.past-exams.*') ? 'nav-link-active' : 'nav-link-custom' }}">
        <span>الدورات الوزارية</span>
        <i class="fa-solid fa-book-open text-muted"></i>
      </a>
    </li>

    <li class="nav-item">
      <a href="{{ route('dashboard.exams.index') }}"
        class="nav-link py-2 px-3 d-flex align-items-center justify-content-between {{ request()->routeIs('dashboard.exams.*') ? 'nav-link-active' : 'nav-link-custom' }}">
        <span>الامتحانات والمذاكرات</span>
        <i class="fa-solid fa-file-signature text-muted"></i>
      </a>
    </li>

    <li class="nav-item mb-3">
      <a href="{{ route('dashboard.schedules.index') }}"
        class="nav-link py-2 px-3 d-flex align-items-center justify-content-between {{ request()->routeIs('dashboard.schedules.*') ? 'nav-link-active' : 'nav-link-custom' }}">
        <span>إدارة الجداول</span>
        <i class="fa-regular fa-calendar-days text-muted"></i>
      </a>
    </li>

  </ul>

  <hr style="border-color: var(--border-color); opacity: 1;">

  <div class="d-flex align-items-center justify-content-between px-3 py-2 mt-2 rounded"
    style="background-color: var(--hover-bg); border: 1px solid var(--border-color);">
    <span style="font-size: 0.9rem; font-weight: 500;">الوضع الليلي</span>
    <div class="form-check form-switch m-0" style="transform: scale(1.2); transform-origin: left;">
      <input class="form-check-input shadow-none" type="checkbox" role="switch" id="themeSwitch" onclick="toggleTheme()"
        checked style="background-color: var(--accent-color); border-color: var(--accent-color); cursor: pointer;">
    </div>
  </div>

</div>