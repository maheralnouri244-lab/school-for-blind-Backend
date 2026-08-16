<div class="custom-card mb-4 p-4 d-flex align-items-center gap-4"
 style="background: linear-gradient(135deg, var(--bg-card) 0%, var(--hover-bg) 100%); border: 1px solid var(--border-color); border-radius: 16px;">
 <div class="position-relative">
  <img
   src="{{ Auth::guard('admin')->user()->profile_image ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::guard('admin')->user()->name ?? 'Admin') . '&background=3b82f6&color=fff' }}"
   alt="Profile Avatar" class="rounded-circle shadow-sm" width="90" height="90"
   style="border: 3px solid var(--accent-color);">
  <span class="position-absolute bottom-0 end-0 p-2 bg-success rounded-circle"
   style="border: 2px solid var(--bg-card); transform: translate(-10%, -10%);"></span>
 </div>

 <div class="d-flex flex-column">
  <h4 class="fw-bold mb-2" style="color: var(--text-main);">
   {{ Auth::guard('admin')->user()->name ?? 'مدير النظام' }}
  </h4>
  <div class="d-flex flex-wrap align-items-center gap-2">
   <span class="badge bg-soft-info text-info rounded-pill px-3 py-2 fw-bold" style="font-size: 0.85rem;">
    <i class="fa-solid fa-id-badge me-1"></i> ID: {{ Auth::guard('admin')->id() }}
   </span>
   <span class="badge bg-soft-warning text-warning rounded-pill px-3 py-2 fw-bold" style="font-size: 0.85rem;">
    <i class="fa-solid fa-user-shield me-1"></i> {{ Auth::guard('admin')->user()->role }}
   </span>
   <span class="badge bg-soft-success text-success rounded-pill px-3 py-2 fw-bold" style="font-size: 0.85rem;">
    <i class="fa-solid fa-envelope me-1"></i> {{ Auth::guard('admin')->user()->email }}
   </span>
   <span class="badge bg-soft-danger text-danger rounded-pill px-3 py-2 fw-bold" style="font-size: 0.85rem;">
    <i class="fa-regular fa-calendar-check me-1"></i> انضمام:
    {{ Auth::guard('admin')->user()->created_at->format('Y-m-d') }}
   </span>
  </div>
 </div>
</div>