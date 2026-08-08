<div class="d-flex flex-column h-100 p-3" style="color: var(--text-main);">

  <a href="/" class="d-flex align-items-center mb-4 text-decoration-none justify-content-between"
    style="color: var(--text-main);">
    <span class="fs-4 fw-bold">SESB</span>
    <i class="fa-regular fa-face-laugh-beam fs-2" style="color: #a3e635;"></i>
  </a>

  <hr style="border-color: var(--border-color);">

  <ul class="nav nav-pills flex-column mb-auto gap-2">

    <li class="nav-item">
      <a href="{{ route('dashboard') }}"
        class="nav-link d-flex align-items-center justify-content-between {{ request()->routeIs('dashboard') ? 'nav-link-active' : 'nav-link-custom' }}">
        <span class="fw-bold">لوحة القيادة</span>
        <i class="fa-solid fa-gauge-high"></i>
      </a>
    </li>

    <li class="nav-item mt-3 mb-1 px-3" style="color: var(--text-muted); font-size: 0.85rem;">اقسام</li>

    <li class="nav-item">
      <a href="{{ route('dashboard.users.index') }}"
        class="nav-link d-flex align-items-center justify-content-between {{ request()->routeIs('dashboard.users.*') ? 'nav-link-active' : 'nav-link-custom' }}">
        <span>إدارة المستخدمين والطلبات</span>
        <i class="fa-solid fa-users-gear"></i>
      </a>
    </li>

    <li>
      <a href="{{ route('admin.active-calls') }}"
        class="nav-link d-flex align-items-center justify-content-between {{ request()->routeIs('admin.active-calls', 'rooms.*') ? 'nav-link-active' : 'nav-link-custom' }}">
        <span>الدروس الجارية حالياً</span>
        <div class="d-flex align-items-center gap-2">
          <span class="spinner-grow spinner-grow-sm text-danger" role="status"
            style="--bs-spinner-width: 0.6rem; --bs-spinner-height: 0.6rem;"></span>
          <i class="fa-solid fa-headset"></i>
        </div>
      </a>
    </li>

    <li>
      <a href="{{ route('content.monitor') }}"
        class="nav-link d-flex align-items-center justify-content-between {{ request()->routeIs('content.monitor', 'reports.*', 'punishments.*') ? 'nav-link-active' : 'nav-link-custom' }}">
        <span>مراقب المحتوى</span>
        <i class="fa-solid fa-shield-halved"></i>
      </a>
    </li>

    <li>
      <a href="{{ route('dashboard.support.index') }}"
        class="nav-link d-flex align-items-center justify-content-between {{ request()->routeIs('dashboard.support.*') ? 'nav-link-active' : 'nav-link-custom' }}">
        <span>طلبات الدعم الفني</span>
        <i class="fa-solid fa-headset"></i>
      </a>
    </li>

    <li class="nav-item mt-3 mb-1 px-3" style="color: var(--text-muted); font-size: 0.85rem;">اضافي</li>

    <li>
      <a href="{{ route('classes') }}"
        class="nav-link d-flex align-items-center justify-content-between {{ request()->routeIs('classes') ? 'nav-link-active' : 'nav-link-custom' }}">
        <span>صفوفنا</span>
        <i class="fa-regular fa-folder-open"></i>
      </a>
    </li>

    <li>
      <a href="{{ route('logs.index') }}"
        class="nav-link d-flex align-items-center justify-content-between {{ request()->routeIs('logs.index') ? 'nav-link-active' : 'nav-link-custom' }}">
        <span>سجلاتنا</span>
        <i class="fa-solid fa-table-list"></i>
      </a>
    </li>
    {{-- قسم الإدارة المالية --}}
    <li class="nav-item mt-3 mb-1 px-3" style="color: var(--text-muted); font-size: 0.85rem;">النظام المالي</li>

    <li class="nav-item">
      <a class="nav-link d-flex align-items-center justify-content-between cursor-pointer {{ request()->routeIs('financial.*') ? 'nav-link-active' : 'nav-link-custom' }}"
        data-bs-toggle="collapse" href="#financialDropdown" role="button">

        <span>الإدارة المالية</span>

        <i class="fa-solid fa-vault"></i>
      </a>

      <div class="collapse {{ request()->routeIs('financial.*') ? 'show' : '' }}" id="financialDropdown">
        <ul class="nav flex-column me-4 mt-2 gap-1"
          style="border-right: 1px solid var(--border-color); padding-right: 15px;">

          <li class="nav-item">
            <a href="{{ route('financial.index') }}"
              class="nav-link py-2 {{ request()->routeIs('financial.index') ? 'fw-bold text-success' : 'text-muted' }}"
              style="font-size: 0.9rem;">
              اللوحة الرئيسية
            </a>
          </li>

          <li class="nav-item">
            <a href="{{ route('financial.donations') }}"
              class="nav-link py-2 {{ request()->routeIs('financial.donations') ? 'fw-bold text-success' : 'text-muted' }}"
              style="font-size: 0.9rem;">
              سجل التبرعات
            </a>
          </li>

          <li class="nav-item">
            <a href="{{ route('financial.salaries') }}"
              class="nav-link py-2 {{ request()->routeIs('financial.salaries') ? 'fw-bold text-success' : 'text-muted' }}"
              style="font-size: 0.9rem;">
              رواتب الأساتذة
            </a>
          </li>

          <li class="nav-item">
            <a href="{{ route('financial.rewards') }}"
              class="nav-link py-2 {{ request()->routeIs('financial.rewards') ? 'fw-bold text-success' : 'text-muted' }}"
              style="font-size: 0.9rem;">
              طلبات المكافآت
            </a>
          </li>

          <li class="nav-item">
            <a href="{{ route('financial.transactions') }}"
              class="nav-link py-2 {{ request()->routeIs('financial.transactions') ? 'fw-bold text-success' : 'text-muted' }}"
              style="font-size: 0.9rem;">
              التدقيق المالي
            </a>
          </li>

        </ul>
      </div>
    </li>

    {{-- قسم المحتوى الأكاديمي --}}
    <li class="nav-item mt-3 mb-1 px-3" style="color: var(--text-muted); font-size: 0.85rem;">المحتوى الأكاديمي</li>

    <li>
      <a href="{{ route('dashboard.past-exams.index') }}"
        class="nav-link d-flex align-items-center justify-content-between {{ request()->routeIs('dashboard.past-exams.*') ? 'nav-link-active' : 'nav-link-custom' }}">
        <span>الدورات الوزارية</span>
        <i class="fa-solid fa-book-open"></i>
      </a>
    </li>

    <li>
      <a href="{{ route('dashboard.exams.index') }}"
        class="nav-link d-flex align-items-center justify-content-between {{ request()->routeIs('dashboard.exams.*') ? 'nav-link-active' : 'nav-link-custom' }}">
        <span>الامتحانات و المذاكرات</span>
        <i class="fa-solid fa-file-signature"></i>
      </a>
    </li>

    <li>
      <a href="{{ route('dashboard.schedules.index') }}"
        class="nav-link d-flex align-items-center justify-content-between {{ request()->routeIs('dashboard.schedules.*') ? 'nav-link-active' : 'nav-link-custom' }}">
        <span>إدارة الجداول</span>
        <i class="fa-regular fa-calendar-days"></i>
      </a>
    </li>

  </ul>

  <hr style="border-color: var(--border-color);">

  <div class="d-flex align-items-center px-3 pb-2 mt-2">
    <div class="form-check form-switch" style="transform: scale(1.4); transform-origin: right;">
      <input class="form-check-input" type="checkbox" role="switch" id="themeSwitch" onclick="toggleTheme()" checked
        style="background-color: #a3e635; border-color: #a3e635; cursor: pointer;">
    </div>
  </div>

</div>