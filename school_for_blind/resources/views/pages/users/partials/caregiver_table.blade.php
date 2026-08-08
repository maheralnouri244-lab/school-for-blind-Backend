<div class="table-responsive rounded-4 border"
 style="border-color: var(--border-color) !important; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
 <table class="table table-hover-custom align-middle mb-0" style="color: var(--text-main);">
  <thead style="background-color: var(--hover-bg);">
   <tr>
    <th scope="col" class="py-3 px-4 text-muted fw-bold text-start border-0">رقم ولي الأمر</th>
    <th scope="col" class="py-3 text-muted fw-bold text-center border-0">عدد الأبناء</th>
    <th scope="col" class="py-3 text-muted fw-bold text-start border-0">أسماء الأبناء</th>
   </tr>
  </thead>
  <tbody>
   @forelse($users as $caregiver)
    <tr style="border-bottom: 1px solid var(--border-color); cursor: pointer;"
     onclick="openUserDetails('caregiver', {{ $caregiver->id }})">
     <td class="py-3 px-4 text-start fw-bold" dir="ltr">
      <div class="d-flex align-items-center gap-3" dir="rtl">
       <div class="p-2 rounded-circle d-flex align-items-center justify-content-center bg-soft-warning"
        style="width: 40px; height: 40px;">
        <i class="fa-solid fa-users text-warning fs-6"></i>
       </div>
       <span dir="ltr">{{ $caregiver->phone }}</span>
      </div>
     </td>
     <td class="py-3 text-center">
      <span class="badge bg-secondary px-3 py-2 rounded-pill fw-normal" style="font-size: 0.85rem;">
       {{ $caregiver->students->count() }}
      </span>
     </td>
     <td class="py-3 text-start">
      @foreach($caregiver->students as $student)
       <span class="badge bg-soft-info text-info me-1">{{ $student->fullname }}</span>
      @endforeach
     </td>
    </tr>
   @empty
    <tr>
     <td colspan="3" class="text-center py-5 text-muted">
      <i class="fa-regular fa-folder-open fs-1 mb-3 d-block" style="opacity: 0.5;"></i>
      لا يوجد أولياء أمور لعرضهم حالياً.
     </td>
    </tr>
   @endforelse
  </tbody>
 </table>
</div>