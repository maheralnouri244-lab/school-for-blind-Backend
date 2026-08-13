<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SESB - تسجيل الدخول</title>
  <style>
    :root {
      --bg-main: #f3f4f6;
      --bg-card: #ffffff;
      --border-color: #e5e7eb;
      --text-main: #1f2937;
      --text-muted: #6b7280;
      --accent-color: #3b82f6;
      --focus-ring: 0 0 0 4px rgba(59, 130, 246, 0.5);
      --radius-md: 8px;
      --radius-lg: 12px;
      --shadow-card: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
      --transition: all 0.3s ease-in-out;
    }

    [data-theme="dark"] {
      --bg-main: #0b1120;
      --bg-card: #151c2c;
      --border-color: #2d3748;
      --text-main: #ffffff;
      --text-muted: #9ca3af;
      --accent-color: #84cc16;
      --focus-ring: 0 0 0 4px rgba(132, 204, 22, 0.5);
      --shadow-card: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.2);
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Amiri', system-ui, -apple-system, sans-serif;
    }

    body {
      background-color: var(--bg-main);
      color: var(--text-main);
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 20px;
      transition: background-color 0.3s ease, color 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .watermark-container {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) scale(1);
      width: 100%;
      max-width: 600px;
      opacity: 0.03;
      z-index: -1;
      pointer-events: none;
      transition: opacity 1s ease, transform 1s cubic-bezier(0.4, 0, 0.2, 1);
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .watermark-container.active {
      opacity: 1;
      transform: translate(-50%, -50%) scale(0.5);
      z-index: 9998;
    }

    .watermark-logo {
      width: 100%;
      height: auto;
    }

    .logo-dark {
      display: none;
    }

    .logo-light {
      display: block;
    }

    [data-theme="dark"] .logo-light {
      display: none;
    }

    [data-theme="dark"] .logo-dark {
      display: block;
    }

    #splash-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background-color: var(--bg-main);
      opacity: 0;
      visibility: hidden;
      z-index: 9997;
      transition: opacity 0.5s ease;
    }

    #splash-overlay.active {
      opacity: 1;
      visibility: visible;
    }

    .sr-only {
      position: absolute;
      width: 1px;
      height: 1px;
      padding: 0;
      margin: -1px;
      overflow: hidden;
      clip: rect(0, 0, 0, 0);
      white-space: nowrap;
      border-width: 0;
    }

    .login-card {
      background-color: var(--bg-card);
      padding: 40px;
      border: 1px solid var(--border-color);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-card);
      width: 100%;
      max-width: 480px;
      transition: var(--transition);
      text-align: center;
      position: relative;
      z-index: 10;
    }

    .login-card.fade-out {
      opacity: 0;
      transform: translateY(20px);
      pointer-events: none;
    }

    .brand-header {
      margin-bottom: 30px;
    }

    .main-heading {
      font-size: 28px;
      font-weight: 800;
      margin-bottom: 8px;
      color: var(--text-main);
    }

    .sub-heading {
      font-size: 16px;
      color: var(--text-muted);
      margin-bottom: 30px;
    }

    .form-group {
      margin-bottom: 20px;
      text-align: right;
    }

    .form-label {
      display: block;
      font-weight: 600;
      font-size: 14px;
      margin-bottom: 8px;
      color: var(--text-main);
    }

    .input-wrapper {
      position: relative;
    }

    .form-input {
      width: 100%;
      padding: 14px 16px;
      font-size: 16px;
      border: 1px solid var(--border-color);
      border-radius: var(--radius-md);
      background-color: var(--bg-main);
      color: var(--text-main);
      transition: var(--transition);
    }

    .form-input:focus {
      outline: none;
      border-color: var(--accent-color);
      box-shadow: var(--focus-ring);
    }

    .form-input::placeholder {
      color: var(--text-muted);
      opacity: 0.8;
    }

    .submit-btn {
      width: 100%;
      padding: 16px;
      background-color: var(--accent-color);
      color: #ffffff;
      border: none;
      border-radius: var(--radius-md);
      font-size: 18px;
      font-weight: 700;
      cursor: pointer;
      transition: var(--transition);
      margin-top: 10px;
    }

    .submit-btn:hover {
      opacity: 0.9;
    }

    .submit-btn:focus {
      outline: none;
      box-shadow: var(--focus-ring);
    }

    .theme-switch-wrapper {
      position: fixed;
      top: 20px;
      left: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
      background: var(--bg-card);
      padding: 10px 15px;
      border: 1px solid var(--border-color);
      border-radius: 50px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      z-index: 20;
    }

    .theme-label {
      font-size: 14px;
      color: var(--text-muted);
    }

    .theme-switch {
      position: relative;
      display: inline-block;
      width: 50px;
      height: 26px;
    }

    .theme-switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: var(--border-color);
      transition: .4s;
      border-radius: 34px;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 18px;
      width: 18px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      transition: .4s;
      border-radius: 50%;
    }

    input:checked+.slider {
      background-color: var(--accent-color);
    }

    input:focus+.slider {
      box-shadow: var(--focus-ring);
    }

    input:checked+.slider:before {
      transform: translateX(24px);
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .login-card {
      animation: fadeIn 0.5s ease-out;
    }

    .password-toggle-btn {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: var(--text-muted);
      cursor: pointer;
      padding: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: var(--transition);
    }

    .password-toggle-btn:hover {
      color: var(--accent-color);
    }

    .password-input-padded {
      padding-left: 45px !important;
    }
  </style>
</head>

<body>

  <div class="watermark-container" id="watermark">
    <img src="{{ asset('img/logo-light.png') }}" alt="Watermark Light" class="watermark-logo logo-light">
    <img src="{{ asset('img/logo-dark.png') }}" alt="Watermark Dark" class="watermark-logo logo-dark">
  </div>

  <div id="splash-overlay"></div>

  <main>
    <div class="theme-switch-wrapper">
      <span class="theme-label" id="theme-status">الوضع الفاتح</span>
      <label class="theme-switch" for="checkbox">
        <input type="checkbox" id="checkbox" />
        <div class="slider round"></div>
        <span class="sr-only">تبديل الوضع الليلي</span>
      </label>
    </div>

    <section class="login-card" id="loginCard" aria-labelledby="login-heading">
      <div class="brand-header">
        <h1 id="login-heading" class="main-heading">تسجيل الدخول</h1>
        <p class="sub-heading">مرحباً بك في لوحة قيادة مدرسة SESB</p>
      </div>

      <form id="loginForm" action="{{ route('dashboard.login') }}" method="POST">
        @csrf

        @if ($errors->any())
          <div
            style="background-color: rgba(220, 38, 38, 0.1); color: #dc2626; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid rgba(220, 38, 38, 0.2); text-align: right;">
            <ul style="list-style: none; margin: 0; padding: 0;">
              @foreach ($errors->all() as $error)
                <li>⚠️ {{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="form-group">
          <label for="username" class="form-label">اسم المستخدم أو البريد الإلكتروني</label>
          <div class="input-wrapper">
            <input type="text" id="username" name="username" class="form-input" placeholder="ادخل البريد الإلكتروني"
              required value="{{ old('username') }}">
          </div>
        </div>

        <div class="form-group">
          <label for="password" class="form-label">كلمة المرور</label>
          <div class="input-wrapper">
            <input type="password" id="password" name="password" class="form-input password-input-padded"
              placeholder="ادخل كلمة المرور" required>
            <button type="button" id="togglePasswordBtn" class="password-toggle-btn" aria-label="إظهار كلمة المرور">
              <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
              <svg id="eyeSlashIcon" style="display: none;" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <path
                  d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24">
                </path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
              </svg>
            </button>
          </div>
        </div>

        <button type="submit" class="submit-btn">تسجيل الدخول</button>
      </form>
    </section>
  </main>

  <script>
    const toggleSwitch = document.querySelector('.theme-switch input[type="checkbox"]');
    const themeStatus = document.getElementById('theme-status');
    const currentTheme = localStorage.getItem('theme');

    if (currentTheme) {
      document.documentElement.setAttribute('data-theme', currentTheme);
      if (currentTheme === 'dark') {
        toggleSwitch.checked = true;
        themeStatus.textContent = "الوضع الداكن";
      }
    }

    function switchTheme(e) {
      if (e.target.checked) {
        document.documentElement.setAttribute('data-theme', 'dark');
        localStorage.setItem('theme', 'dark');
        themeStatus.textContent = "الوضع الداكن";
      } else {
        document.documentElement.removeAttribute('data-theme');
        localStorage.setItem('theme', 'light');
        themeStatus.textContent = "الوضع الفاتح";
      }
    }

    toggleSwitch.addEventListener('change', switchTheme, false);

    const togglePasswordBtn = document.getElementById('togglePasswordBtn');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    const eyeSlashIcon = document.getElementById('eyeSlashIcon');

    togglePasswordBtn.addEventListener('click', function () {
      const isPassword = passwordInput.getAttribute('type') === 'password';
      passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

      if (isPassword) {
        eyeIcon.style.display = 'none';
        eyeSlashIcon.style.display = 'block';
      } else {
        eyeIcon.style.display = 'block';
        eyeSlashIcon.style.display = 'none';
      }
    });

    const loginForm = document.getElementById('loginForm');
    const watermark = document.getElementById('watermark');
    const splashOverlay = document.getElementById('splash-overlay');
    const loginCard = document.getElementById('loginCard');

    loginForm.addEventListener('submit', function (e) {
      const usernameVal = document.getElementById('username').value.trim();
      const passwordVal = document.getElementById('password').value.trim();

      if (usernameVal && passwordVal) {
        e.preventDefault();

        loginCard.classList.add('fade-out');
        splashOverlay.classList.add('active');
        watermark.classList.add('active');

        setTimeout(() => {
          loginForm.submit();
        }, 1500);
      }
    });
  </script>
</body>

</html>