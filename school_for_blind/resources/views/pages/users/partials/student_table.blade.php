<div class="table-responsive shadow-sm"
 style="border-radius: 16px; border: 1px solid var(--border-color); overflow: hidden; background-color: var(--bg-card);">
 <table class="table table-hover-custom align-middle mb-0" style="color: var(--text-main);">
  <thead style="background-color: var(--hover-bg);">
   <tr>
    <th scope="col" class="py-3 px-4 text-muted fw-bold text-start border-0">اسم الطالب</th>
    <th scope="col" class="py-3 text-muted fw-bold text-center border-0">رقم الهاتف</th>
    <th scope="col" class="py-3 text-muted fw-bold text-center border-0">المستوى</th>
    <th scope="col" class="py-3 px-4 text-muted fw-bold text-start border-0">الحالة</th>
    <th scope="col" class="py-3 px-4 text-muted fw-bold text-start border-0">فرض عقوبة</th>
   </tr>
  </thead>
  <tbody>
   @forelse($users as $student)
    <tr style="border-bottom: 1px solid var(--border-color); cursor: pointer; transition: background-color 0.2s ease;"
     onclick="openUserDetails('student', {{ $student->id }})">

     <td class="py-3 px-4 text-start fw-bold">
      <div class="d-flex align-items-center gap-3">
       <div class="p-2 rounded-circle d-flex align-items-center justify-content-center shadow-sm bg-soft-info"
        style="width: 45px; height: 45px;">
        <i class="fa-solid fa-user-graduate fs-5 text-info"></i>
       </div>
       <span style="color: var(--text-main);">{{ $student->fullname }}</span>
      </div>
     </td>

     <td class="py-3 text-center" dir="ltr">
      <span class="text-muted fw-medium">{{ $student->phone }}</span>
     </td>

     <td class="py-3 text-center">
      <span class="px-3 py-2 rounded-pill fw-bold shadow-sm"
       style="font-size: 0.85rem; background-color: var(--bg-main); border: 1px solid var(--border-color); color: var(--text-main);">
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
       class="badge-status bg-soft-{{ $color }} text-{{ $color }} px-3 py-2 rounded-pill d-inline-flex align-items-center gap-2 fw-bold shadow-sm">
       <span style="width: 8px; height: 8px; border-radius: 50%; background-color: currentColor;"></span>
       {{ $statusText }}
      </span>
     </td>

     <td class="py-3 px-4">
      @if($student->status === 'approved')
       <button type="button"
        class="btn btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center transition-all bg-soft-danger"
        data-user-id="{{ $student->id }}" data-user-model="App\Models\Student" data-target-type="student"
        data-user-name="{{ $student->fullname }}" onclick="openNewPunishmentModal(this)" title="إجراء إداري / عقوبة"
        style="width: 40px; height: 40px; border: 1px solid rgba(220, 53, 69, 0.2);">
        <i class="fa-solid fa-user-shield text-danger fs-6"></i>
       </button>
      @else
       <span class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center"
        title="لا يمكن معاقبة مستخدم قيد الانتظار أو مرفوض"
        style="width: 40px; height: 40px; background-color: var(--bg-main); border: 1px solid var(--border-color); opacity: 0.6; cursor: not-allowed;">
        <i class="fa-solid fa-shield text-muted fs-6"></i>
       </span>
      @endif
     </td>

    </tr>
   @empty
    <tr>
     <td colspan="5" class="text-center py-5 text-muted fw-bold">
      <div class="d-flex flex-column align-items-center justify-content-center">
       <div class="p-4 rounded-circle mb-3" style="background-color: var(--hover-bg);">
        <i class="fa-regular fa-folder-open fs-1 text-muted" style="opacity: 0.6;"></i>
       </div>
       <span>لا يوجد طلاب لعرضهم حالياً.</span>
      </div>
     </td>
    </tr>
   @endforelse
  </tbody>
 </table>
</div>