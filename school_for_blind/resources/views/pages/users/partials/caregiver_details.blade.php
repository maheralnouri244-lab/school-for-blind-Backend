<div class="row g-4 text-end" dir="rtl">
 <div class="col-12">
  <label class="text-muted small d-block mb-3">الأبناء المسجلين (مرتبطين بهذا الرقم)</label>
  <div class="table-responsive border rounded">
   <table class="table mb-0 text-center align-middle" style="color: var(--text-main);">
    <thead style="background-color: var(--hover-bg);">
     <tr>
      <th>اسم الطالب</th>
      <th>المستوى</th>
      <th>الشعبة</th>
      <th>الحالة</th>
     </tr>
    </thead>
    <tbody>
     @foreach($user->students as $student)
      <tr>
       <td>{{ $student->fullname }}</td>
       <td>{{ $student->level == 'twelfth' ? 'بكالوريا' : 'تاسع' }}</td>
       <td>{{ $student->class->name ?? 'غير محدد' }}</td>
       <td>
        @php
         $statusColors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
         $color = $statusColors[$student->status] ?? 'secondary';
         $statusNames = ['pending' => 'قيد الانتظار', 'approved' => 'مقبول', 'rejected' => 'مرفوض'];
        @endphp
        <span
         class="badge bg-soft-{{ $color }} text-{{ $color }}">{{ $statusNames[$student->status] ?? $student->status }}</span>
       </td>
      </tr>
     @endforeach
    </tbody>
   </table>
  </div>
 </div>
 <input type="hidden" id="current_user_status" value="approved">
</div>