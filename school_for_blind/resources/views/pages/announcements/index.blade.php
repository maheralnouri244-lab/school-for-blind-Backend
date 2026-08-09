@extends('layouts.app')

@section('content')
 <style>
     /* تنسيقات إضافية لجمالية الـ Checkboxes والفلاتر */
     .filter-card {
         background-color: var(--hover-bg);
         border: 1px solid var(--border-color);
         border-radius: 12px;
     }
     .custom-checkbox .form-check-input:checked {
         background-color: var(--accent-color);
         border-color: var(--accent-color);
     }
     .custom-checkbox .form-check-label {
         color: var(--text-main);
         cursor: pointer;
     }
     .pagination-custom .page-link {
         background-color: var(--bg-card);
         border-color: var(--border-color);
         color: var(--text-main);
     }
     .pagination-custom .page-item.active .page-link {
         background-color: var(--accent-color);
         border-color: var(--accent-color);
         color: #fff;
     }
 </style>

 <div class="container-fluid p-0">
     <div class="row g-4">
         <div class="col-12">
             <div class="custom-card">

                 <!-- رأس الواجهة -->
                 <div class="d-flex justify-content-between align-items-center mb-4">
                     <h5 class="fw-bold mb-0" style="color: var(--text-main);">سجل الإعلانات والتنبيهات</h5>
                     <button class="btn btn-sm px-4 py-2" style="background-color: var(--accent-color); color: #fff; border-radius: 8px; font-weight: bold;" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal">
                         <i class="fa-solid fa-plus me-2"></i> إنشاء إعلان جديد
                     </button>
                 </div>

                 <!-- شريط الفلاتر الأنيق (Server-Side) -->
                 <div class="filter-card p-3 mb-4 d-flex flex-wrap gap-3 align-items-end">
                     <div class="d-flex align-items-center me-2">
                         <div class="p-2 rounded d-flex align-items-center justify-content-center" style="background-color: rgba(59, 130, 246, 0.1);">
                             <i class="fa-solid fa-filter text-primary"></i>
                         </div>
                         <span class="ms-2 fw-bold" style="color: var(--text-main);">الفلاتر:</span>
                     </div>

                     <div style="min-width: 150px;">
                         <label class="form-label text-muted mb-1" style="font-size: 0.85rem;">الجمهور المستهدف</label>
                         <select id="filterAudience" class="form-select form-select-sm search-input cursor-pointer" onchange="fetchAnnouncements(1)">
                             <option value="all">الكل</option>
                             <option value="student">الطلاب</option>
                             <option value="caregiver">أولياء الأمور</option>
                             <option value="teacher">المعلمين</option>
                         </select>
                     </div>

                     <div style="min-width: 150px;">
                         <label class="form-label text-muted mb-1" style="font-size: 0.85rem;">نوع الإعلان</label>
                         <select id="filterType" class="form-select form-select-sm search-input cursor-pointer" onchange="fetchAnnouncements(1)">
                             <option value="all">الكل</option>
                             <option value="normal">عادي</option>
                             <option value="exam_schedule">جدول امتحانات</option>
                             <option value="urgent">عاجل</option>
                         </select>
                     </div>

                     <div style="min-width: 150px;">
                         <label class="form-label text-muted mb-1" style="font-size: 0.85rem;">المرحلة الدراسية</label>
                         <select id="filterLevel" class="form-select form-select-sm search-input cursor-pointer" onchange="fetchAnnouncements(1)">
                             <option value="all">الكل</option>
                             <option value="ninth">التاسع</option>
                             <option value="twelfth">البكالوريا</option>
                         </select>
                     </div>
                 </div>

                 <!-- جدول عرض الإعلانات السابقة -->
                 <div class="table-responsive">
                     <table class="table table-hover-custom align-middle mb-0" style="color: var(--text-main);">
                         <thead>
                             <tr style="border-bottom: 2px solid var(--border-color);">
                                 <th scope="col" class="pb-3 text-muted fw-normal text-start">العنوان</th>
                                 <th scope="col" class="pb-3 text-muted fw-normal">النوع</th>
                                 <th scope="col" class="pb-3 text-muted fw-normal">الجمهور</th>
                                 <th scope="col" class="pb-3 text-muted fw-normal">المرحلة</th>
                                 <th scope="col" class="pb-3 text-muted fw-normal">التاريخ</th>
                                 <th scope="col" class="pb-3 text-muted fw-normal text-center">إجراءات</th>
                             </tr>
                         </thead>
                         <tbody id="announcementsTableBody">
                             <tr>
                                 <td colspan="6" class="text-center py-4 text-muted">جاري تحميل الإعلانات...</td>
                             </tr>
                         </tbody>
                     </table>
                 </div>

                 <!-- حاوية الباجينيشن -->
                 <nav class="mt-4 d-flex justify-content-center">
                     <ul class="pagination pagination-sm pagination-custom mb-0" id="paginationContainer">
                         <!-- أزرار الصفحات ستظهر هنا عبر الجافاسكربت -->
                     </ul>
                 </nav>

             </div>
         </div>
     </div>
 </div>

 <!-- Modal 1: إنشاء الإعلان بنظام Checkboxes -->
 <div class="modal fade glass-modal" id="createAnnouncementModal" tabindex="-1" aria-hidden="true" dir="rtl">
     <div class="modal-dialog modal-dialog-centered modal-lg">
         <div class="modal-content">
             <div class="modal-header d-flex justify-content-between align-items-center">
                 <h5 class="modal-title fw-bold">نشر إعلان جديد</h5>
                 <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>

             <form id="announcementForm">
                 @csrf
                 <div class="modal-body text-start" dir="rtl">
                     <div class="row g-3">
                         <div class="col-md-8">
                             <label style="color: var(--text-main); font-weight: 500;" class="mb-2">عنوان الإعلان</label>
                             <input type="text" name="title" class="form-control" required style="background-color: var(--bg-main); color: var(--text-main); border-color: var(--border-color);">
                         </div>

                         <div class="col-md-4">
                             <label style="color: var(--text-main); font-weight: 500;" class="mb-2">نوع الإعلان</label>
                             <select name="type" class="form-control" required style="background-color: var(--bg-main); color: var(--text-main); border-color: var(--border-color);">
                                 <option value="normal">عادي</option>
                                 <option value="exam_schedule">جدول امتحانات</option>
                                 <option value="urgent">عاجل</option>
                             </select>
                         </div>

                         <!-- اختيار الجمهور المستهدف (Checkboxes) -->
                         <div class="col-md-6 mt-4">
                             <label style="color: var(--text-main); font-weight: bold;" class="mb-3 d-block border-bottom pb-2">الجمهور المستهدف (يمكن تحديد أكثر من خيار):</label>
                             <div class="d-flex flex-column gap-2">
                                 <div class="form-check custom-checkbox">
                                     <input class="form-check-input" type="checkbox" name="target_audience[]" value="student" id="targetStudent" checked>
                                     <label class="form-check-label" for="targetStudent">الطلاب</label>
                                 </div>
                                 <div class="form-check custom-checkbox">
                                     <input class="form-check-input" type="checkbox" name="target_audience[]" value="caregiver" id="targetCaregiver">
                                     <label class="form-check-label" for="targetCaregiver">أولياء الأمور</label>
                                 </div>
                                 <div class="form-check custom-checkbox">
                                     <input class="form-check-input" type="checkbox" name="target_audience[]" value="teacher" id="targetTeacher">
                                     <label class="form-check-label" for="targetTeacher">المعلمين</label>
                                 </div>
                             </div>
                         </div>

                         <!-- اختيار المرحلة الدراسية (Checkboxes) -->
                         <div class="col-md-6 mt-4">
                             <label style="color: var(--text-main); font-weight: bold;" class="mb-3 d-block border-bottom pb-2">المرحلة الدراسية (يمكن تحديد أكثر من خيار):</label>
                             <div class="d-flex flex-column gap-2">
                                 <div class="form-check custom-checkbox">
                                     <input class="form-check-input" type="checkbox" name="level[]" value="ninth" id="levelNinth" checked>
                                     <label class="form-check-label" for="levelNinth">التاسع</label>
                                 </div>
                                 <div class="form-check custom-checkbox">
                                     <input class="form-check-input" type="checkbox" name="level[]" value="twelfth" id="levelTwelfth">
                                     <label class="form-check-label" for="levelTwelfth">البكالوريا</label>
                                 </div>
                             </div>
                         </div>

                         <div class="col-12 mt-4">
                             <label style="color: var(--text-main); font-weight: 500;" class="mb-2">المحتوى (نص الإعلان)</label>
                             <textarea name="content" class="form-control" rows="4" required style="background-color: var(--bg-main); color: var(--text-main); border-color: var(--border-color);"></textarea>
                         </div>
                     </div>

                     <div id="formAlert" class="alert d-none mt-3 mb-0"></div>
                 </div>

                 <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                     <button type="button" class="btn text-muted" data-bs-dismiss="modal">إلغاء</button>
                     <button type="submit" class="btn btn-accept px-4" id="submitBtn">
                         <span id="btnText">نشر الآن</span>
                         <span id="btnLoader" class="spinner-border spinner-border-sm d-none"></span>
                     </button>
                 </div>
             </form>
         </div>
     </div>
 </div>

 <!-- Modal 2: عرض تفاصيل الإعلان (نفسه لم يتغير) -->
 <!-- ... يوضع هنا نفس الكود الخاص بـ viewAnnouncementModal من الرسالة السابقة ... -->
 <div class="modal fade glass-modal" id="viewAnnouncementModal" tabindex="-1" aria-hidden="true" dir="rtl">
     <div class="modal-dialog modal-dialog-centered modal-lg">
         <div class="modal-content">
             <div class="modal-header d-flex justify-content-between align-items-center">
                 <h5 class="modal-title fw-bold" id="viewTitle">عنوان الإعلان</h5>
                 <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>

             <div class="modal-body text-start" dir="rtl">
                 <div class="row mb-4">
                     <div class="col-md-4 mb-2">
                         <small class="text-muted d-block mb-1">الجمهور المستهدف</small>
                         <span id="viewAudience" class="fw-bold" style="color: var(--text-main);">الطلاب</span>
                     </div>
                     <div class="col-md-4 mb-2">
                         <small class="text-muted d-block mb-1">نوع الإعلان</small>
                         <span id="viewType" class="badge-status">عادي</span>
                     </div>
                     <div class="col-md-4 mb-2">
                         <small class="text-muted d-block mb-1">المرحلة الدراسية</small>
                         <span id="viewLevel" class="fw-bold" style="color: var(--text-main);">الكل</span>
                     </div>
                     <div class="col-md-4 mt-2">
                         <small class="text-muted d-block mb-1">تاريخ النشر</small>
                         <span id="viewDate" class="fw-bold" style="color: var(--text-main); font-family: monospace;">2024-01-01</span>
                     </div>
                 </div>

                 <div>
                     <small class="text-muted d-block mb-2">محتوى الإعلان:</small>
                     <div id="viewContent" class="p-3 rounded" style="background-color: var(--bg-main); border: 1px solid var(--border-color); color: var(--text-main); min-height: 100px; white-space: pre-wrap;">
                         <!-- محتوى الإعلان سيعرض هنا -->
                     </div>
                 </div>
             </div>

             <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                 <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">إغلاق</button>
             </div>
         </div>
     </div>
 </div>
@endsection

@push('scripts')
 <script>
     let currentAnnouncements = []; // لتخزين إعلانات الصفحة الحالية للعرض في المودال

     document.addEventListener('DOMContentLoaded', function () {
         // 1. جلب البيانات عند فتح الصفحة
         fetchAnnouncements(1);

         // 2. إرسال الفورم (تمت إضافة فحص التأكد من اختيار مربع واحد على الأقل)
         document.getElementById('announcementForm').addEventListener('submit', function(e) {
             e.preventDefault();

             let form = this;
             let alertBox = document.getElementById('formAlert');

             // التأكد من اختيار جمهور واحد على الأقل ومرحلة واحدة على الأقل
             let targetChecked = form.querySelectorAll('input[name="target_audience[]"]:checked').length;
             let levelChecked = form.querySelectorAll('input[name="level[]"]:checked').length;

             if (targetChecked === 0 || levelChecked === 0) {
                 alertBox.className = 'alert alert-danger mt-3 mb-0';
                 alertBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-2"></i> يجب تحديد جمهور مستهدف واحد ومرحلة دراسية واحدة على الأقل.';
                 alertBox.classList.remove('d-none');
                 return;
             }

             let submitBtn = document.getElementById('submitBtn');
             let btnText = document.getElementById('btnText');
             let btnLoader = document.getElementById('btnLoader');

             submitBtn.disabled = true;
             btnText.classList.add('d-none');
             btnLoader.classList.remove('d-none');
             alertBox.classList.add('d-none');

             let formData = new FormData(form);

             fetch('{{ route("dashboard.announcements.store") }}', {
                 method: 'POST',
                 body: formData,
                 headers: {
                     'X-Requested-With': 'XMLHttpRequest',
                     'Accept': 'application/json'
                 }
             })
             .then(response => response.json().then(data => ({ status: response.status, body: data })))
             .then(res => {
                 if (res.status === 201 || res.status === 200) {
                     alertBox.className = 'alert alert-success mt-3 mb-0';
                     alertBox.innerHTML = '<i class="fa-solid fa-check-circle me-2"></i>' + res.body.message;
                     alertBox.classList.remove('d-none');

                     form.reset();

                     fetchAnnouncements(1); // إعادة جلب أول صفحة بعد الإضافة

                     setTimeout(() => {
                         let modalInstance = bootstrap.Modal.getInstance(document.getElementById('createAnnouncementModal'));
                         modalInstance.hide();
                         alertBox.classList.add('d-none');
                     }, 2000);
                 } else {
                     let errorTxt = res.body.message || 'حدث خطأ أثناء الإرسال';
                     alertBox.className = 'alert alert-danger mt-3 mb-0';
                     alertBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-2"></i>' + errorTxt;
                     alertBox.classList.remove('d-none');
                 }
             })
             .catch(error => {
                 alertBox.className = 'alert alert-danger mt-3 mb-0';
                 alertBox.innerHTML = '<i class="fa-solid fa-wifi me-2"></i>حدث خطأ في الاتصال بالسيرفر.';
                 alertBox.classList.remove('d-none');
             })
             .finally(() => {
                 submitBtn.disabled = false;
                 btnText.classList.remove('d-none');
                 btnLoader.classList.add('d-none');
             });
         });
     });

     // دالة جلب الإعلانات من السيرفر بناءً على الصفحة والفلاتر
     window.fetchAnnouncements = function(page = 1) {
         let filterAudience = document.getElementById('filterAudience').value;
         let filterType = document.getElementById('filterType').value;
         let filterLevel = document.getElementById('filterLevel').value;

         // تجهيز الرابط مع البارامترات
         let url = `{{ route('dashboard.announcements.api') }}?page=${page}&target_audience=${filterAudience}&type=${filterType}&level=${filterLevel}`;

         document.getElementById('announcementsTableBody').innerHTML = `<tr><td colspan="6" class="text-center py-4 text-muted"><div class="spinner-border text-primary" role="status"></div></td></tr>`;

         fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
         .then(res => res.json())
         .then(data => {
             // Laravel Paginate يُرجع مصفوفة البيانات داخل data.data
             let items = data.data || [];
             currentAnnouncements = items; // حفظ البيانات للمودال
             renderTable(items);
             renderPagination(data);
         })
         .catch(err => {
             document.getElementById('announcementsTableBody').innerHTML = `<tr><td colspan="6" class="text-center py-4 text-danger">فشل تحميل الإعلانات</td></tr>`;
         });
     }

     // دالة رسم الجدول
     function renderTable(data) {
         const tbody = document.getElementById('announcementsTableBody');
         tbody.innerHTML = ''; 

         if(data.length === 0) {
             tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-muted">لا يوجد بيانات لعرضها</td></tr>`;
             return;
         }

         data.forEach(item => {
             let typeColor = item.type === 'urgent' ? 'danger' : (item.type === 'exam_schedule' ? 'warning' : 'info');
             let typeName = item.type === 'urgent' ? 'عاجل' : (item.type === 'exam_schedule' ? 'امتحانات' : 'عادي');

             let audienceName = item.target_audience === 'all' ? 'الجميع' : (item.target_audience === 'student' ? 'الطلاب' : (item.target_audience === 'teacher' ? 'المعلمين' : 'أولياء الأمور'));
             let levelName = item.level === 'all' ? 'الكل' : (item.level === 'ninth' ? 'التاسع' : 'البكالوريا');

             let dateObj = new Date(item.created_at);
             let formattedDate = dateObj.toLocaleDateString('ar-EG', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute:'2-digit' });

             let tr = document.createElement('tr');
             tr.style.borderBottom = "1px solid var(--border-color)";
             tr.innerHTML = `
                 <td class="py-3 text-start fw-bold">${item.title || 'بدون عنوان'}</td>
                 <td class="py-3"><span class="badge-status bg-soft-${typeColor} text-${typeColor}">${typeName}</span></td>
                 <td class="py-3 text-muted">${audienceName}</td>
                 <td class="py-3 text-muted">${levelName}</td>
                 <td class="py-3 text-muted text-start" dir="ltr"><small>${formattedDate}</small></td>
                 <td class="py-3 text-center">
                     <button type="button" class="btn btn-sm btn-outline-secondary" onclick="viewAnnouncementDetails(${item.id})">
                         <i class="fa-solid fa-eye"></i>
                     </button>
                 </td>
             `;
             tbody.appendChild(tr);
         });
     }

     // دالة رسم أزرار الباجينيشن
     function renderPagination(data) {
         const container = document.getElementById('paginationContainer');
         container.innerHTML = '';

         if(data.last_page <= 1) return; // لا حاجة للباجينيشن إذا كانت صفحة واحدة

         // زر السابق
         let prevClass = data.current_page === 1 ? 'disabled' : '';
         container.innerHTML += `<li class="page-item ${prevClass}"><a class="page-link cursor-pointer" onclick="fetchAnnouncements(${data.current_page - 1})">&laquo;</a></li>`;

         // أرقام الصفحات
         for (let i = 1; i <= data.last_page; i++) {
             let activeClass = data.current_page === i ? 'active' : '';
             container.innerHTML += `<li class="page-item ${activeClass}"><a class="page-link cursor-pointer" onclick="fetchAnnouncements(${i})">${i}</a></li>`;
         }

         // زر التالي
         let nextClass = data.current_page === data.last_page ? 'disabled' : '';
         container.innerHTML += `<li class="page-item ${nextClass}"><a class="page-link cursor-pointer" onclick="fetchAnnouncements(${data.current_page + 1})">&raquo;</a></li>`;
     }

     // دالة فتح مودال التفاصيل (بقيت كما هي، تعتمد على currentAnnouncements)
     window.viewAnnouncementDetails = function(id) {
         let item = currentAnnouncements.find(x => x.id === id);
         if(!item) return;

         let typeColor = item.type === 'urgent' ? 'danger' : (item.type === 'exam_schedule' ? 'warning' : 'info');
         let typeName = item.type === 'urgent' ? 'عاجل' : (item.type === 'exam_schedule' ? 'امتحانات' : 'عادي');
         let audienceName = item.target_audience === 'all' ? 'الجميع' : (item.target_audience === 'student' ? 'الطلاب' : (item.target_audience === 'teacher' ? 'المعلمين' : 'أولياء الأمور'));
         let levelName = item.level === 'all' ? 'الكل' : (item.level === 'ninth' ? 'التاسع' : 'البكالوريا');
         let formattedDate = new Date(item.created_at).toLocaleString('ar-EG');

         document.getElementById('viewTitle').innerText = item.title || 'بدون عنوان';

         let typeBadge = document.getElementById('viewType');
         typeBadge.className = `badge-status bg-soft-${typeColor} text-${typeColor}`;
         typeBadge.innerText = typeName;

         document.getElementById('viewAudience').innerText = audienceName;
         document.getElementById('viewLevel').innerText = levelName;
         document.getElementById('viewDate').innerText = formattedDate;

         let contentDisplay = item.content;
         try {
             let parsed = JSON.parse(item.content);
             if(typeof parsed === 'object') {
                 contentDisplay = JSON.stringify(parsed, null, 2);
             }
         } catch (e) {}

         document.getElementById('viewContent').innerText = contentDisplay || 'لا يوجد محتوى';

         let viewModal = new bootstrap.Modal(document.getElementById('viewAnnouncementModal'));
         viewModal.show();
     };
 </script>
@endpush