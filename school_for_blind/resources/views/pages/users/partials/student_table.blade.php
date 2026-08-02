<div class="table-responsive rounded-4 border"
 style="border-color: var(--border-color) !important; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
 <table class="table table-hover-custom align-middle mb-0" style="color: var(--text-main);">
  <thead style="background-color: var(--hover-bg);">
   <tr>
    <th scope="col" class="py-3 px-4 text-muted fw-bold text-start border-0">اسم الطالب</th>
    <th scope="col" class="py-3 text-muted fw-bold text-center border-0">رقم الهاتف</th>
    <th scope="col" class="py-3 text-muted fw-bold text-center border-0">المستوى</th>
    <th scope="col" class="py-3 px-4 text-muted fw-bold text-start border-0">الحالة</th>
   </tr>
  </thead>
  <tbody>
   @forelse($users as $student)
    <tr style="border-bottom: 1px solid var(--border-color); cursor: pointer;"
     onclick="openUserDetails('student', {{ $student->id }})">
     <td class="py-3 px-4 text-start fw-bold">
      <div class="d-flex align-items-center gap-3">
       <div class="p-2 rounded-circle d-flex align-items-center justify-content-center bg-soft-info"
        style="width: 40px; height: 40px;">
        <i class="fa-solid fa-user-graduate text-info fs-6"></i>
       </div>
       <span>{{ $student->fullname }}</span>
      </div>
     </td>
     <td class="py-3 text-center" dir="ltr">
      <span class="text-muted">{{ $student->phone }}</span>
     </td>
     <td class="py-3 text-center">
      <span class="badge bg-secondary px-3 py-2 rounded-pill fw-normal" style="font-size: 0.85rem;">
       {{ $student->level === 'ninth' ? 'التاسع' : 'البكالوريا' }}
      </span>
     </td>
     <td class="py-3 px-4 text-start">
      @php
       $statusColors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
       $color = $statusColors[$student->status] ?? 'secondary';
       $statusNames = ['pending' => 'قيد الانتظار', 'approved' => 'مقبول', 'rejected' => 'مرفوض'];
       $statusText = $statusNames[$student->status] ?? $student->status;
      @endphp
      <span
       class="badge-status bg-soft-{{ $color }} text-{{ $color }} px-3 py-2 rounded-pill d-inline-flex align-items-center gap-2">
       <span style="width: 6px; height: 6px; border-radius: 50%; background-color: currentColor;"></span>
       {{ $statusText }}
      </span>
     </td>
    </tr>
   @empty
    <tr>
     <td colspan="4" class="text-center py-5 text-muted">
      <i class="fa-regular fa-folder-open fs-1 mb-3 d-block" style="opacity: 0.5;"></i>
      لا يوجد طلاب لعرضهم حالياً.
     </td>
    </tr>
   @endforelse
  </tbody>
 </table>
</div>