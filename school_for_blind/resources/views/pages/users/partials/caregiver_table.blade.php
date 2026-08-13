<div class="table-responsive shadow-sm"
 style="border-radius: 16px; border: 1px solid var(--border-color); overflow: hidden; background-color: var(--bg-card);">
 <table class="table table-hover-custom align-middle mb-0" style="color: var(--text-main);">
  <thead style="background-color: var(--hover-bg);">
   <tr>
    <th scope="col" class="py-3 px-4 text-muted fw-bold text-start border-0">رقم ولي الأمر</th>
    <th scope="col" class="py-3 text-muted fw-bold text-center border-0">عدد الأبناء</th>
    <th scope="col" class="py-3 px-4 text-muted fw-bold text-start border-0">أسماء الأبناء</th>
   </tr>
  </thead>
  <tbody>
   @forelse($users as $caregiver)
    <tr style="border-bottom: 1px solid var(--border-color); cursor: pointer; transition: background-color 0.2s ease;"
     onclick="openUserDetails('caregiver', {{ $caregiver->id }})">

     <td class="py-3 px-4 text-start fw-bold" dir="ltr">
      <div class="d-flex align-items-center gap-3" dir="rtl">
       <div class="p-2 rounded-circle d-flex align-items-center justify-content-center shadow-sm bg-soft-warning"
        style="width: 45px; height: 45px;">
        <i class="fa-solid fa-users fs-5 text-warning"></i>
       </div>
       <span dir="ltr" style="color: var(--text-main);">{{ $caregiver->phone }}</span>
      </div>
     </td>

     <td class="py-3 text-center">
      <span class="px-3 py-2 rounded-pill fw-bold shadow-sm"
       style="font-size: 0.85rem; background-color: var(--bg-main); border: 1px solid var(--border-color); color: var(--text-main);">
       {{ $caregiver->students->count() }}
      </span>
     </td>

     <td class="py-3 px-4 text-start">
      <div class="d-flex flex-wrap gap-2">
       @foreach($caregiver->students as $student)
        <span
         class="badge-status bg-soft-info text-info px-3 py-2 rounded-pill fw-bold shadow-sm border border-info border-opacity-25">
         {{ $student->fullname }}
        </span>
       @endforeach
      </div>
     </td>

    </tr>
   @empty
    <tr>
     <td colspan="3" class="text-center py-5 text-muted fw-bold">
      <div class="d-flex flex-column align-items-center justify-content-center">
       <div class="p-4 rounded-circle mb-3 shadow-sm"
        style="background-color: var(--hover-bg); border: 1px solid var(--border-color);">
        <i class="fa-regular fa-folder-open fs-1 text-muted" style="opacity: 0.6;"></i>
       </div>
       <span>لا يوجد أولياء أمور لعرضهم حالياً.</span>
      </div>
     </td>
    </tr>
   @endforelse
  </tbody>
 </table>
</div>