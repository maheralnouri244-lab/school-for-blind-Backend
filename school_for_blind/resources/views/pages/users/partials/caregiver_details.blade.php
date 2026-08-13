<div class="row g-4 text-end" dir="rtl">
  <div class="col-12">
    <h6 class="fw-bold mb-3" style="color: #f59e0b;"><i class="fa-solid fa-children me-2"></i>الأبناء المسجلين (مرتبطين
      بهذا الرقم)</h6>

    <div class="p-4 shadow-sm"
      style="background-color: var(--bg-main); border: 1px solid var(--border-color); border-radius: 16px;">
      <div class="table-responsive shadow-sm"
        style="border-radius: 12px; border: 1px solid var(--border-color); overflow: hidden; background-color: var(--bg-card);">
        <table class="table table-hover-custom text-center align-middle mb-0" style="color: var(--text-main);">
          <thead style="background-color: var(--hover-bg);">
            <tr>
              <th class="py-3 border-0 text-muted fw-bold">اسم الطالب</th>
              <th class="py-3 border-0 text-muted fw-bold">المستوى</th>
              <th class="py-3 border-0 text-muted fw-bold">الشعبة</th>
              <th class="py-3 border-0 text-muted fw-bold">الحالة</th>
            </tr>
          </thead>
          <tbody>
            @foreach($user->students as $student)
              <tr style="border-bottom: 1px solid var(--border-color); transition: background-color 0.2s ease;">

                <td class="py-3 fw-bold">
                  <div class="d-flex align-items-center justify-content-center gap-3">
                    <div
                      class="p-2 rounded-circle d-flex align-items-center justify-content-center shadow-sm bg-soft-info"
                      style="width: 38px; height: 38px;">
                      <i class="fa-solid fa-user-graduate fs-6 text-info"></i>
                    </div>
                    <span style="color: var(--text-main);">{{ $student->fullname }}</span>
                  </div>
                </td>

                <td class="py-3">
                  <span class="badge bg-soft-info text-info rounded-pill px-3 py-2 shadow-sm fw-bold">
                    {{ $student->level == 'twelfth' ? 'بكالوريا' : 'تاسع' }}
                  </span>
                </td>

                <td class="py-3">
                  <span class="badge px-3 py-2 rounded-pill fw-bold shadow-sm"
                    style="background-color: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-main);">
                    {{ $student->class->name ?? 'غير محدد' }}
                  </span>
                </td>

                <td class="py-3">
                  @php
                    $statusColors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
                    $color = $statusColors[$student->status] ?? 'secondary';
                    $statusNames = ['pending' => 'قيد الانتظار', 'approved' => 'مقبول', 'rejected' => 'مرفوض'];
                   @endphp
                  <span
                    class="badge-status bg-soft-{{ $color }} text-{{ $color }} rounded-pill px-3 py-2 shadow-sm fw-bold d-inline-flex align-items-center gap-2">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background-color: currentColor;"></span>
                    {{ $statusNames[$student->status] ?? $student->status }}
                  </span>
                </td>

              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <input type="hidden" id="current_user_status" value="approved">
</div>