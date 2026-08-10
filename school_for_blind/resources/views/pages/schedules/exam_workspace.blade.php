@extends('layouts.app')

@section('content')
 <div class="container-fluid p-0">
  <!-- شريط الأزرار العلوي -->
  <div class="d-flex justify-content-between align-items-center mb-3 p-3 rounded custom-card">
   <div>
    <h4 class="fw-bold mb-1" style="color: var(--text-main);">
     ساحة عمل جدول الامتحان - {{ $level === 'ninth' ? 'الصف التاسع' : 'البكالوريا' }}
    </h4>
    <span class="text-muted fs-6">قم بإدراج المواد وتحديد التواريخ لنشر الإعلان</span>
   </div>
   <div class="d-flex gap-2">
    <button type="button" class="btn btn-outline-secondary px-4 fw-bold" onclick="window.history.back()">
     <i class="fa-solid fa-arrow-right ms-2"></i> تراجع
    </button>
    <button type="button" class="btn px-4 fw-bold" style="background-color: var(--accent-color); color: #000;"
     onclick="attemptPublish()">
     <i class="fa-solid fa-bullhorn ms-1"></i> حفظ ونشر كإعلان
    </button>
   </div>
  </div>

  <div class="row gap-3 m-0 flex-nowrap" style="min-height: 75vh;">

   <!-- القائمة الجانبية للمواد -->
   <div class="col-md-3 custom-card p-3 shadow-sm overflow-auto" id="subjectsSidebar">
    <h6 class="fw-bold mb-3" style="color: var(--text-main);">دليل المواد الدراسية</h6>
    <p class="text-muted" style="font-size: 0.8rem;">
     <span class="text-success"><i class="fa-solid fa-square"></i></span> مجدولة &nbsp;
     <span class="text-danger"><i class="fa-solid fa-square"></i></span> مكررة (خطأ) &nbsp;
     <span class="text-warning"><i class="fa-solid fa-square"></i></span> غير مجدولة
    </p>
    <div id="subjectsSidebarList" class="d-flex flex-column gap-2">
     <!-- يتم تعبئة المواد هنا ديناميكياً -->
    </div>
   </div>

   <!-- مساحة العمل والجدول -->
   <div class="col-md-9 flex-grow-1 custom-card p-4 shadow-sm position-relative overflow-hidden" id="workspaceContainer"
    style="background-color: var(--bg-main); cursor: grab;">

    <div class="d-flex justify-content-between align-items-center mb-4" style="z-index: 100; position: relative;">
     <h5 class="fw-bold mb-0">المواد المجدولة</h5>
     <button class="btn btn-sm shadow-sm" style="background-color: #3b82f6; color: #fff; font-weight: bold;"
      onclick="openExamModal()">
      <i class="fa-solid fa-plus ms-1"></i> إضافة امتحان (سطر جديد)
     </button>
    </div>

    <!-- عنصر Panzoom -->
    <div id="panzoom-element" style="transform-origin: center; width: 100%;">
     <div class="custom-card p-3 shadow-sm w-100">
      <div class="table-responsive">
       <table class="table table-bordered text-center align-middle mb-0" style="color: var(--text-main);">
        <thead>
         <tr class="table-header-custom">
          <th>التاريخ</th>
          <th>المادة</th>
          <th>توقيت البدء</th>
          <th>توقيت الانتهاء</th>
          <th>إجراءات</th>
         </tr>
        </thead>
        <tbody id="examTableBody">
         <!-- يتم التعبئة ديناميكياً -->
         <tr>
          <td colspan="5" class="text-muted py-4">لم يتم إضافة أي مواد لجدول الامتحان بعد.</td>
         </tr>
        </tbody>
       </table>
      </div>
     </div>
    </div>

   </div>
  </div>
 </div>

 <!-- مودال إضافة / تعديل مادة في الجدول -->
 <div class="modal fade" id="examSlotModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
   <div class="modal-content glass-modal">
    <div class="modal-header">
     <h5 class="modal-title fw-bold">إضافة مادة لجدول الامتحان</h5>
     <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body text-end">
     <form id="examSlotForm">
      <input type="hidden" id="editIndex" value="-1">

      <div class="mb-3">
       <label class="form-label fw-bold text-muted">اختر المادة</label>
       <select class="form-select search-input" id="slotSubject" required>
        <option value="">-- يرجى اختيار المادة --</option>
        @foreach($subjects as $subject)
         <option value="{{ $subject->id }}">{{ $subject->name }}</option>
        @endforeach
       </select>
      </div>

      <div class="mb-3">
       <label class="form-label fw-bold text-muted">تاريخ الامتحان</label>
       <input type="date" class="form-control search-input" id="slotDate" required>
      </div>

      <div class="row">
       <div class="col-6 mb-3">
        <label class="form-label fw-bold text-muted">توقيت البدء</label>
        <input type="time" class="form-control search-input" id="slotStartTime" required>
       </div>
       <div class="col-6 mb-3">
        <label class="form-label fw-bold text-muted">توقيت الانتهاء</label>
        <input type="time" class="form-control search-input" id="slotEndTime" required>
       </div>
      </div>
     </form>
    </div>
    <div class="modal-footer d-flex justify-content-between">
     <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
     <button type="button" class="btn btn-primary btn-sm px-4" onclick="saveExamSlot()">إدراج في الجدول</button>
    </div>
   </div>
  </div>
 </div>

 <!-- مودال تحذير المواد الناقصة -->
 <div class="modal fade" id="missingSubjectsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
   <div class="modal-content glass-modal border-warning">
    <div class="modal-header bg-soft-warning border-bottom-0">
     <h5 class="modal-title fw-bold text-warning"><i class="fa-solid fa-triangle-exclamation me-2"></i>مواد غير مجدولة
     </h5>
     <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body text-center">
     <p>يوجد بعض المواد في القائمة (باللون البرتقالي) لم يتم إدراجها في برنامج الامتحان.</p>
     <p class="mb-0 fw-bold" style="color: var(--text-main);">هل تريد تجاهل التحذير والمتابعة لنشر الجدول؟</p>
    </div>
    <div class="modal-footer justify-content-center border-top-0">
     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">تراجع</button>
     <button type="button" class="btn fw-bold text-dark" style="background-color: #f59e0b;"
      onclick="proceedToPublish()">نعم، متابعة النشر</button>
    </div>
   </div>
  </div>
 </div>

 <!-- مودال نشر الإعلان -->
 <div class="modal fade" id="publishModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
   <div class="modal-content glass-modal">
    <div class="modal-header border-bottom">
     <h5 class="modal-title fw-bold text-primary"><i class="fa-solid fa-paper-plane me-2"></i>نشر الجدول كإعلان</h5>
     <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body text-end">
     <form id="publishForm">
      <div class="mb-3">
       <label class="form-label fw-bold text-muted">عنوان الإعلان (اختياري)</label>
       <input type="text" id="pubTitle" class="form-control search-input"
        placeholder="برنامج امتحانات {{ $level == 'ninth' ? 'التاسع' : 'البكالوريا' }}">
      </div>
      <div class="mb-3">
       <label class="form-label fw-bold text-muted">الرسالة المرفقة (اختياري)</label>
       <textarea id="pubContent" class="form-control search-input" rows="3"
        placeholder="تم اصدار برنامج امتحانات جديد، يرجى الاطلاع عليه من قسم الجداول."></textarea>
      </div>
      <div class="mb-3">
       <label class="form-label fw-bold text-muted">الجمهور المستهدف</label>
       <select id="pubAudience" class="form-select search-input" multiple>
        <option value="student" selected>الطلاب</option>
        <option value="caregiver" selected>أولياء الأمور</option>
        <option value="teacher" selected>الأساتذة</option>
       </select>
       <small class="text-muted d-block mt-1">تحديد الكل ينشره للجميع</small>
      </div>
      <div id="publishAlert" class="alert d-none mt-2 mb-0"></div>
     </form>
    </div>
    <div class="modal-footer border-top d-flex justify-content-between">
     <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
     <button type="button" class="btn btn-sm px-4 fw-bold" style="background-color: var(--accent-color); color: #000;"
      id="finalPublishBtn" onclick="submitPublish()">
      <span id="pubBtnText">تأكيد النشر</span>
      <span id="pubBtnLoader" class="spinner-border spinner-border-sm d-none"></span>
     </button>
    </div>
   </div>
  </div>
 </div>

@endsection

@push('scripts')
 <!-- مكتبة Panzoom للتقريب والتبعيد السلس -->
 <script src="https://cdn.jsdelivr.net/npm/@panzoom/panzoom@4.5.1/dist/panzoom.min.js"></script>

 <script>
  const allSubjects = @json($subjects);
  let exams = [];
  const level = '{{ $level }}';
  const type = '{{ $type }}';

  const canvasElement = document.getElementById('panzoom-element');
  const container = document.getElementById('workspaceContainer');
  const panzoom = Panzoom(canvasElement, {
   maxScale: 2,
   minScale: 0.4,
   startScale: 0.85
  });
  container.addEventListener('wheel', panzoom.zoomWithWheel);

  function renderUI() {
   let subjectCounts = {};
   allSubjects.forEach(s => subjectCounts[s.id] = 0);
   exams.forEach(ex => {
    if (subjectCounts[ex.subject_id] !== undefined) {
     subjectCounts[ex.subject_id]++;
    }
   });

   const sidebar = document.getElementById('subjectsSidebarList');
   sidebar.innerHTML = '';
   allSubjects.forEach(s => {
    let count = subjectCounts[s.id];
    let colorClass = 'bg-soft-warning text-warning border-warning';
    let icon = '<i class="fa-solid fa-triangle-exclamation"></i>';
    let label = 'غير مستخدمة';

    if (count === 1) {
     colorClass = 'bg-soft-success text-success border-success';
     icon = '<i class="fa-solid fa-check"></i>';
     label = 'مجدولة';
    } else if (count > 1) {
     colorClass = 'bg-soft-danger text-danger border-danger';
     icon = '<i class="fa-solid fa-xmark"></i>';
     label = 'تكرار خاطئ!';
    }

    sidebar.innerHTML += `
                      <div class="p-2 rounded border d-flex justify-content-between align-items-center ${colorClass}" style="background-color: var(--bg-main); transition: all 0.3s ease;">
                          <span class="fw-bold" style="font-size: 0.85rem;">${s.name}</span>
                          <span class="badge ${colorClass.split(' ')[0]} rounded-pill">${icon} ${label}</span>
                      </div>
                  `;
   });

   const tbody = document.getElementById('examTableBody');
   tbody.innerHTML = '';

   if (exams.length === 0) {
    tbody.innerHTML = '<tr><td colspan="5" class="text-muted py-4">لم يتم إضافة أي مواد لجدول الامتحان بعد.</td></tr>';
    return;
   }

   let sortedExams = [...exams].sort((a, b) => new Date(a.date) - new Date(b.date));

   sortedExams.forEach(ex => {
    let subject = allSubjects.find(s => s.id == ex.subject_id);
    let count = subjectCounts[ex.subject_id];

    // فحص تكرار المادة
    let cellClass = '';
    let alertIcon = '';
    if (count > 1) {
     cellClass = 'bg-soft-danger text-danger';
     alertIcon = '<i class="fa-solid fa-circle-exclamation text-danger ms-1"></i>';
    } else if (count === 1) {
     cellClass = 'bg-soft-success text-success';
    }

    // فحص صحة التوقيت (إذا كان الانتهاء قبل أو يساوي البدء)
    let isTimeError = ex.start_time >= ex.end_time;
    let timeClass = isTimeError ? 'bg-soft-danger text-danger fw-bold' : '';
    let timeAlert = isTimeError ? '<i class="fa-solid fa-triangle-exclamation text-danger ms-1" title="خطأ: وقت الانتهاء قبل البدء"></i>' : '';

    tbody.innerHTML += `
           <tr style="border-bottom: 1px solid var(--border-color);">
               <td class="fw-bold align-middle">${ex.date}</td>
               <td class="fw-bold align-middle ${cellClass}">${subject ? subject.name : '-'} ${alertIcon}</td>
               <td class="align-middle ${timeClass}">${ex.start_time}</td>
               <td class="align-middle ${timeClass}">${ex.end_time} ${timeAlert}</td>
               <td class="align-middle">
                   <button class="btn btn-sm btn-outline-primary" onclick="openExamModal('${ex.id}')"><i class="fa-solid fa-pen"></i></button>
                   <button class="btn btn-sm btn-outline-danger" onclick="deleteExam('${ex.id}')"><i class="fa-solid fa-trash"></i></button>
               </td>
           </tr>
       `;
   });
  }

  function openExamModal(examId = null) {
   let form = document.getElementById('examSlotForm');
   form.reset();

   if (examId) {
    let ex = exams.find(e => e.id === examId);
    document.getElementById('editIndex').value = examId;
    document.getElementById('slotSubject').value = ex.subject_id;
    document.getElementById('slotDate').value = ex.date;
    document.getElementById('slotStartTime').value = ex.start_time;
    document.getElementById('slotEndTime').value = ex.end_time;
   } else {
    document.getElementById('editIndex').value = "-1";
    let today = new Date().toISOString().split('T')[0];
    document.getElementById('slotDate').value = today;
   }

   new bootstrap.Modal(document.getElementById('examSlotModal')).show();
  }

  function saveExamSlot() {
   let subject_id = document.getElementById('slotSubject').value;
   let date = document.getElementById('slotDate').value;
   let start_time = document.getElementById('slotStartTime').value;
   let end_time = document.getElementById('slotEndTime').value;
   let editId = document.getElementById('editIndex').value;

   if (!subject_id || !date || !start_time || !end_time) {
    alert('يرجى تعبئة جميع الحقول');
    return;
   }

   if (editId !== "-1") {
    let index = exams.findIndex(e => e.id === editId);
    exams[index] = { id: editId, subject_id, date, start_time, end_time };
   } else {
    exams.push({ id: Math.random().toString(36).substr(2, 9), subject_id, date, start_time, end_time });
   }

   bootstrap.Modal.getInstance(document.getElementById('examSlotModal')).hide();
   renderUI();
  }

  function deleteExam(id) {
   exams = exams.filter(e => e.id !== id);
   renderUI();
  }

  function attemptPublish() {
   let hasTimeErrors = exams.some(ex => ex.start_time >= ex.end_time);
   if (hasTimeErrors) {
    alert("لا يمكن النشر! هناك خطأ في أوقات الامتحانات (وقت الانتهاء يسبق أو يساوي وقت البدء)، يرجى إصلاح الخلايا الملونة بالأحمر.");
    return;
   }
   if (exams.length === 0) {
    alert("لا يمكن نشر جدول فارغ! يرجى إضافة مواد امتحانية أولاً.");
    return;
   }

   let hasErrors = false;
   let hasMissing = false;

   let subjectCounts = {};
   allSubjects.forEach(s => subjectCounts[s.id] = 0);
   exams.forEach(ex => {
    if (subjectCounts[ex.subject_id] !== undefined) {
     subjectCounts[ex.subject_id]++;
    }
   });

   allSubjects.forEach(s => {
    if (subjectCounts[s.id] > 1) hasErrors = true;
    if (subjectCounts[s.id] === 0) hasMissing = true;
   });

   if (hasErrors) {
    alert("لا يمكن النشر! هناك مواد تم إدراجها أكثر من مرة وتظهر باللون الأحمر. يرجى تصحيح الجدول أولاً.");
    return;
   }

   if (hasMissing) {
    new bootstrap.Modal(document.getElementById('missingSubjectsModal')).show();
    return;
   }

   openPublishModal();
  }

  function proceedToPublish() {
   bootstrap.Modal.getInstance(document.getElementById('missingSubjectsModal')).hide();
   openPublishModal();
  }

  function openPublishModal() {
   new bootstrap.Modal(document.getElementById('publishModal')).show();
  }

  function submitPublish() {
   let pubTitle = document.getElementById('pubTitle').value.trim();
   let pubContent = document.getElementById('pubContent').value.trim();

   // جلب الفئات المستهدفة وتحويلها لمصفوفة لتطابق الكونترولر
   let selectedAudiences = Array.from(document.getElementById('pubAudience').selectedOptions).map(opt => opt.value);
   if (selectedAudiences.length === 3 || selectedAudiences.length === 0) {
    selectedAudiences = ['all'];
   }

   if (pubTitle === '') pubTitle = `برنامج امتحانات ${level === 'ninth' ? 'الصف التاسع' : 'البكالوريا'}`;
   if (pubContent === '') pubContent = "تم إصدار برنامج امتحانات جديد، يرجى الاطلاع عليه من قسم الجداول.";

   // تحويل البيانات للشكل المهيكل (صفوف وأعمدة)
   const daysArr = ["الأحد", "الإثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة", "السبت"];
   let structuredContent = {
    columns: ["اليوم والتاريخ", "المادة", "التوقيت"],
    rows: exams.map(ex => {
     let d = new Date(ex.date);
     let dayName = daysArr[d.getDay()];
     let subjectName = allSubjects.find(s => s.id == ex.subject_id)?.name || '';

     return {
      date: `${dayName} ${ex.date}`,
      subject: subjectName,
      time: `${ex.start_time} - ${ex.end_time}`
     };
    })
   };

   // بناء الـ Payload النهائي للإرسال
   let payload = {
    _token: '{{ csrf_token() }}',
    title: pubTitle,
    type: 'exam_schedule',
    content: structuredContent,
    target_audience: selectedAudiences,
    level: [level]
   };

   let submitBtn = document.getElementById('finalPublishBtn');
   let btnText = document.getElementById('pubBtnText');
   let btnLoader = document.getElementById('pubBtnLoader');
   let alertBox = document.getElementById('publishAlert');

   submitBtn.disabled = true;
   btnText.classList.add('d-none');
   btnLoader.classList.remove('d-none');
   alertBox.classList.add('d-none');

   fetch('{{ route("dashboard.announcements.store") }}', {
    method: 'POST',
    body: JSON.stringify(payload),
    headers: {
     'Content-Type': 'application/json',
     'X-Requested-With': 'XMLHttpRequest',
     'Accept': 'application/json'
    }
   })
    .then(response => response.json().then(data => ({ status: response.status, body: data })))
    .then(res => {
     if (res.status === 201 || res.status === 200) {
      alertBox.className = 'alert alert-success mt-3 mb-0';
      alertBox.innerHTML = '<i class="fa-solid fa-check me-2"></i> تم نشر الجدول بنجاح!';
      alertBox.classList.remove('d-none');

      setTimeout(() => {
       window.location.href = '{{ route("dashboard.schedules.index") }}';
      }, 2000);
     } else {
      let errorTxt = res.body.message || 'حدث خطأ أثناء الإرسال';
      if (res.body.errors) {
       errorTxt = Object.values(res.body.errors).map(err => err.join('<br>')).join('<br>');
      }
      alertBox.className = 'alert alert-danger mt-3 mb-0';
      alertBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-2"></i> ' + errorTxt;
      alertBox.classList.remove('d-none');
     }
    })
    .catch(error => {
     alertBox.className = 'alert alert-danger mt-3 mb-0';
     alertBox.innerHTML = 'حدث خطأ في الاتصال بالسيرفر.';
     alertBox.classList.remove('d-none');
    })
    .finally(() => {
     submitBtn.disabled = false;
     btnText.classList.remove('d-none');
     btnLoader.classList.add('d-none');
    });
  }

  window.onload = function () {
   renderUI();
  };
 </script>
@endpush