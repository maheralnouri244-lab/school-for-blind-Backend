@extends('layouts.app')

@section('content')
  <div class="container-fluid p-0">
    <div class="row mb-4">
      <div class="col-12">
        <h4 class="fw-bold" style="color: var(--text-main);">إدارة المستخدمين وطلبات الانضمام</h4>
      </div>
    </div>

    <div class="custom-card mb-4 shadow-sm" style="border-radius: 16px;">
      <div class="row g-3">
        <div class="col-md-2">
          <select id="filter-type" class="form-select search-input py-2">
            <option value="student">الطلاب</option>
            <option value="teacher">المعلمين</option>
            <option value="caregiver">أولياء الأمور</option>
          </select>
        </div>
        <div class="col-md-2">
          <select id="filter-status" class="form-select search-input py-2">
            <option value="">كل الحالات</option>
            <option value="pending">قيد الانتظار</option>
            <option value="approved">مقبول</option>
            <option value="rejected">مرفوض</option>
          </select>
        </div>
        <div class="col-md-2">
          <select id="filter-level" class="form-select search-input py-2">
            <option value="">كل المستويات</option>
            <option value="ninth">التاسع</option>
            <option value="twelfth">البكالوريا</option>
          </select>
        </div>
        <div class="col-md-3">
          <select id="filter-class" class="form-select search-input py-2">
            <option value="">كل الشعب</option>
            @foreach($classes as $class)
              <option value="{{ $class->id }}">
                {{ $class->name }} ({{ $class->level === 'ninth' ? 'تاسع' : 'بكالوريا' }})
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <div class="input-group">
            <span class="input-group-text border-0"
              style="background-color: var(--bg-main); color: var(--text-muted); border-radius: 0 8px 8px 0;">
              <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input type="text" id="filter-search" class="form-control search-input py-2 border-start-0"
              placeholder="بحث بالاسم أو الرقم..." style="border-radius: 8px 0 0 8px;">
          </div>
        </div>
      </div>
    </div>

    <div class="custom-card shadow-sm" style="border-radius: 16px; overflow: hidden;">
      <div id="table-container">
        <div class="text-center py-5">
          <i class="fa-solid fa-circle-notch fa-spin fs-1 text-muted"></i>
        </div>
      </div>
      <div id="pagination-container" class="mt-4 d-flex justify-content-center" dir="ltr"></div>
    </div>
  </div>

  <div class="modal fade" id="dynamicDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content glass-modal border-0 shadow-lg">
        <div class="modal-header border-0 p-4">
          <h4 class="fw-bold mb-0" id="modal-user-name" style="color: var(--text-main);"></h4>
          <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4 text-end" dir="rtl">
          <div id="modal-body-content"></div>
          <div id="student-action-container" class="mt-4 pt-3 border-top d-none"
            style="border-color: var(--border-color) !important;">
            <label class="form-label fw-bold mb-2 text-muted">حدد الشعبة:</label>
            <select id="modal_class_id" class="form-select search-input py-2"></select>
          </div>
        </div>
        <div class="modal-footer border-0 p-4 d-none" id="modal-footer-actions">
          <div class="d-flex gap-3 w-100 text-end" dir="rtl">
            <button id="btn-approve" class="btn btn-accept flex-grow-1 py-3 shadow-sm d-none rounded-3">تأكيد
              القبول</button>
            <a id="btn-teacher-setup" href="#"
              class="btn btn-primary flex-grow-1 py-3 shadow-sm fw-bold d-none rounded-3">إعداد بيانات المعلم وقبوله</a>
            <button id="btn-reject" class="btn btn-reject flex-grow-1 py-3 shadow-sm d-none rounded-3">رفض نهائي</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="userPunishmentsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content glass-modal border-0 shadow-lg text-end" dir="rtl">
        <div class="modal-header border-bottom p-4" style="border-color: var(--border-color) !important;">
          <h5 class="fw-bold mb-0" style="color: var(--danger-color);"><i
              class="fa-solid fa-triangle-exclamation me-2"></i>سجل العقوبات</h5>
          <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4" id="punishments-modal-body">
          <div class="text-center py-4"><i class="fa-solid fa-circle-notch fa-spin fs-2 text-muted"></i></div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="editStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content glass-modal border-0 shadow-lg text-end" dir="rtl">
        <div class="modal-header border-bottom p-4" style="border-color: var(--border-color) !important;">
          <h5 class="fw-bold mb-0 text-main">تعديل بيانات الطالب</h5>
          <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <form id="editStudentForm">
            <input type="hidden" id="edit_student_id">
            <div class="mb-3">
              <label class="form-label text-muted fw-bold">الاسم الكامل</label>
              <input type="text" id="edit_fullname" class="form-control search-input" required>
            </div>
            <div class="mb-3">
              <label class="form-label text-muted fw-bold">اسم الأب</label>
              <input type="text" id="edit_fathersname" class="form-control search-input">
            </div>
            <div class="mb-3">
              <label class="form-label text-muted fw-bold">رقم الهاتف</label>
              <input type="text" id="edit_phone" class="form-control search-input text-end" dir="ltr" required>
            </div>
            <div class="mb-3">
              <label class="form-label text-muted fw-bold">رقم ولي الأمر</label>
              <input type="text" id="edit_parent_phone" class="form-control search-input text-end" dir="ltr" required>
            </div>
            <div class="mb-3">
              <label class="form-label text-muted fw-bold">المستوى</label>
              <select id="edit_level" class="form-select search-input">
                <option value="ninth">تاسع</option>
                <option value="twelfth">بكالوريا</option>
              </select>
            </div>
            <div class="mb-4">
              <label class="form-label text-muted fw-bold">الشعبة</label>
              <select id="edit_class_id" class="form-select search-input">
                <option value="">-- اختر الشعبة --</option>
              </select>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow-sm">حفظ التعديلات</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="newPunishmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered text-end" dir="rtl">
      <div class="modal-content glass-modal border-0 shadow-lg">
        <div class="modal-header border-bottom p-4" style="border-color: var(--border-color) !important;">
          <div class="d-flex align-items-center gap-3">
            <div class="p-2 rounded-circle bg-soft-danger d-flex align-items-center justify-content-center"
              style="width: 45px; height: 45px;">
              <i class="fa-solid fa-gavel fs-5" style="color: var(--danger-color);"></i>
            </div>
            <div>
              <h5 class="modal-title fw-bold mb-1" style="color: var(--text-main);">فرض إجراء إداري</h5>
              <small class="text-muted">المستخدم: <span id="new_user_name_display" class="fw-bold"
                  style="color: var(--danger-color);"></span></small>
            </div>
          </div>
          <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <form action="{{ route('punishments.apply') }}" method="POST">
          @csrf
          <input type="hidden" name="punishable_id" id="new_punishable_id">
          <input type="hidden" name="punishable_type" id="new_punishable_type">

          <div class="modal-body p-4">
            <div class="mb-2">
              <label class="form-label text-muted fw-bold mb-2">اختر الإجراء / العقوبة المناسبة:</label>
              <select name="punishment_id" id="new_punishment_select" class="form-select search-input py-2" required>
                <option value="" disabled selected>جاري تحميل العقوبات...</option>
              </select>
            </div>
          </div>

          <div class="modal-footer border-top p-4 d-flex justify-content-between"
            style="border-color: var(--border-color) !important;">
            <button type="button" class="btn btn-outline-custom px-4 py-2 fw-bold" data-bs-dismiss="modal">إلغاء</button>
            <button type="submit"
              class="btn btn-reject px-4 py-2 fw-bold d-flex align-items-center gap-2 rounded-3 shadow-sm">
              <i class="fa-solid fa-check"></i>
              <span>تأكيد فرض العقوبة</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const typeSelect = document.getElementById('filter-type');
      const statusSelect = document.getElementById('filter-status');
      const levelSelect = document.getElementById('filter-level');
      const classSelect = document.getElementById('filter-class');
      const searchInput = document.getElementById('filter-search');
      const tableContainer = document.getElementById('table-container');
      const paginationContainer = document.getElementById('pagination-container');

      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.has('type')) typeSelect.value = urlParams.get('type');
      if (urlParams.has('status')) statusSelect.value = urlParams.get('status');
      if (urlParams.has('level')) levelSelect.value = urlParams.get('level');
      if (urlParams.has('class_id')) classSelect.value = urlParams.get('class_id');
      if (urlParams.has('search')) searchInput.value = urlParams.get('search');

      function fetchUsers(page = 1) {
        tableContainer.innerHTML = '<div class="text-center py-5"><i class="fa-solid fa-circle-notch fa-spin fs-1 text-muted"></i></div>';

        const params = new URLSearchParams({
          type: typeSelect.value,
          status: statusSelect.value,
          level: levelSelect.value,
          class_id: classSelect.value,
          search: searchInput.value,
          page: page
        });

        fetch(`/dashboard/users/filter?${params.toString()}`, {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
          .then(response => response.json())
          .then(data => {
            tableContainer.innerHTML = data.html;
            paginationContainer.innerHTML = data.pagination;

            const paginationLinks = paginationContainer.querySelectorAll('.pagination a');
            paginationLinks.forEach(link => {
              link.addEventListener('click', function (e) {
                e.preventDefault();
                const url = new URL(this.href);
                fetchUsers(url.searchParams.get('page'));
              });
            });
          });
      }

      typeSelect.addEventListener('change', () => fetchUsers());
      statusSelect.addEventListener('change', () => fetchUsers());
      levelSelect.addEventListener('change', () => fetchUsers());
      classSelect.addEventListener('change', () => fetchUsers());

      let debounceTimer;
      searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => fetchUsers(), 500);
      });

      fetchUsers();
    });

    let activeUserId = null;
    let activeUserType = null;

    window.openUserDetails = function (type, id) {
      activeUserId = id;
      activeUserType = type;

      const modalElement = document.getElementById('dynamicDetailsModal');
      const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
      document.body.appendChild(modalElement);

      document.getElementById('modal-body-content').innerHTML = '<div class="text-center py-5"><i class="fa-solid fa-circle-notch fa-spin fs-1 text-muted"></i></div>';
      document.getElementById('student-action-container').classList.add('d-none');
      document.getElementById('modal-footer-actions').classList.add('d-none');
      document.getElementById('btn-approve').classList.add('d-none');
      document.getElementById('btn-teacher-setup').classList.add('d-none');
      document.getElementById('btn-reject').classList.add('d-none');

      modal.show();

      fetch(`/dashboard/users/${type}/${id}/details`, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      })
        .then(res => {
          if (res.status === 401 || res.status === 419) {
            window.location.reload();
            throw new Error('Expired');
          }
          return res.json();
        })
        .then(data => {
          document.getElementById('modal-user-name').innerText = data.name;
          document.getElementById('modal-body-content').innerHTML = data.html;

          const statusElem = document.getElementById('current_user_status');
          const status = statusElem ? statusElem.value : '';
          const footer = document.getElementById('modal-footer-actions');

          if (status === 'pending') {
            footer.classList.remove('d-none');
            document.getElementById('btn-reject').classList.remove('d-none');

            if (type === 'student') {
              document.getElementById('btn-approve').classList.remove('d-none');
              document.getElementById('student-action-container').classList.remove('d-none');

              const classSelectModal = document.getElementById('modal_class_id');
              classSelectModal.innerHTML = '<option value="">جاري التحميل...</option>';

              fetch(`/dashboard/users/fetch-data-by-level?level=${data.level}`, {
                headers: {
                  'X-Requested-With': 'XMLHttpRequest',
                  'Accept': 'application/json'
                }
              })
                .then(res => res.json())
                .then(classData => {
                  classSelectModal.innerHTML = '<option value="">-- اختر الشعبة --</option>';
                  classData.classes.forEach(c => {
                    classSelectModal.insertAdjacentHTML('beforeend', `<option value="${c.id}">${c.name}</option>`);
                  });
                });
            } else {
              const btnSetup = document.getElementById('btn-teacher-setup');
              btnSetup.classList.remove('d-none');
              btnSetup.href = `/dashboard/users/teacher/${id}/setup`;
            }
          }
        })
        .catch(error => {
          if (error.message !== 'Expired') {
            document.getElementById('modal-body-content').innerHTML = '<div class="text-center py-4 text-danger">حدث خطأ أثناء جلب البيانات.</div>';
          }
        });
    };

    document.getElementById('btn-approve').addEventListener('click', function () {
      if (activeUserType === 'student') {
        const classId = document.getElementById('modal_class_id').value;
        if (!classId) {
          alert('الرجاء اختيار الشعبة');
          return;
        }
        updateUserStatus('approved', classId);
      }
    });

    document.getElementById('btn-reject').addEventListener('click', function () {
      if (confirm('هل أنت متأكد من الرفض النهائي لهذا الطلب؟')) {
        updateUserStatus('rejected');
      }
    });

    function updateUserStatus(status, classId = null) {
      const body = { status: status };
      if (classId) body.class_id = classId;

      fetch(`/dashboard/users/${activeUserType}/${activeUserId}/status`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(body)
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('dynamicDetailsModal')).hide();
            document.getElementById('filter-type').dispatchEvent(new Event('change'));
          }
        });
    }

    window.fetchUserPunishments = function (type, id) {
      const detailsModalElement = document.getElementById('dynamicDetailsModal');
      const detailsModal = bootstrap.Modal.getInstance(detailsModalElement);
      if (detailsModal) {
        detailsModal.hide();
      }

      const punModalElement = document.getElementById('userPunishmentsModal');
      document.body.appendChild(punModalElement); // Fixes the Z-index freeze
      const punModal = bootstrap.Modal.getOrCreateInstance(punModalElement);

      const body = document.getElementById('punishments-modal-body');
      body.innerHTML = '<div class="text-center py-4"><i class="fa-solid fa-circle-notch fa-spin fs-2 text-muted"></i></div>';

      punModal.show();

      fetch(`/dashboard/users/${type}/${id}/punishments`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
      })
        .then(res => {
          if (res.status === 401 || res.status === 419) {
            window.location.reload();
            throw new Error('Expired');
          }
          return res.json();
        })
        .then(data => {
          if (data.success) {
            body.innerHTML = data.html;
          }
        })
        .catch(error => {
          if (error.message !== 'Expired') {
            body.innerHTML = '<div class="text-center py-4 text-danger">حدث خطأ أثناء جلب البيانات.</div>';
          }
        });
    };

    document.getElementById('editStudentForm').addEventListener('submit', function (e) {
      e.preventDefault();
      const id = document.getElementById('edit_student_id').value;
      const data = {
        fullname: document.getElementById('edit_fullname').value,
        fathersname: document.getElementById('edit_fathersname').value,
        phone: document.getElementById('edit_phone').value,
        parent_phone: document.getElementById('edit_parent_phone').value,
        level: document.getElementById('edit_level').value,
      };

      fetch(`/dashboard/users/student/${id}/update`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
      })
        .then(res => res.json())
        .then(response => {
          if (response.success) {
            bootstrap.Modal.getInstance(document.getElementById('editStudentModal')).hide();
            document.getElementById('filter-type').dispatchEvent(new Event('change'));
          } else {
            alert('حدث خطأ أثناء حفظ التعديلات!');
          }
        });
    });

    function fetchClassesForEditModal(level, selectedClassId = null) {
      const classSelect = document.getElementById('edit_class_id');
      classSelect.innerHTML = '<option value="">جاري التحميل...</option>';

      fetch(`/dashboard/users/fetch-data-by-level?level=${level}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
      })
        .then(res => {
          if (res.status === 401 || res.status === 419) {
            window.location.reload();
            throw new Error('Expired');
          }
          return res.json();
        })
        .then(data => {
          classSelect.innerHTML = '<option value="">-- اختر الشعبة --</option>';
          if (data.classes && data.classes.length > 0) {
            data.classes.forEach(c => {
              const selected = (selectedClassId == c.id) ? 'selected' : '';
              classSelect.insertAdjacentHTML('beforeend', `<option value="${c.id}" ${selected}>${c.name}</option>`);
            });
          } else {
            classSelect.innerHTML = '<option value="">لا يوجد شعب لهذا المستوى</option>';
          }
        })
        .catch(error => {
          if (error.message !== 'Expired') console.error("Error fetching data:", error);
        });
    }

    window.openEditStudentModal = function (button) {
      const detailsModalElement = document.getElementById('dynamicDetailsModal');
      const detailsModal = bootstrap.Modal.getInstance(detailsModalElement);
      if (detailsModal) detailsModal.hide();

      const studentId = button.getAttribute('data-id');
      const fullname = button.getAttribute('data-fullname');
      const fathersname = button.getAttribute('data-fathersname');
      const phone = button.getAttribute('data-phone');
      const parentPhone = button.getAttribute('data-parent');
      const level = button.getAttribute('data-level');
      const classId = button.getAttribute('data-class');

      document.getElementById('edit_student_id').value = studentId;
      document.getElementById('edit_fullname').value = fullname;
      document.getElementById('edit_fathersname').value = fathersname;
      document.getElementById('edit_phone').value = phone;
      document.getElementById('edit_parent_phone').value = parentPhone;
      document.getElementById('edit_level').value = level;

      fetchClassesForEditModal(level, classId);

      const editModalElement = document.getElementById('editStudentModal');
      document.body.appendChild(editModalElement); // Fixes the Z-index freeze
      const editModal = bootstrap.Modal.getOrCreateInstance(editModalElement);
      editModal.show();
    };

    document.getElementById('edit_level').addEventListener('change', function () {
      fetchClassesForEditModal(this.value);
    });

    document.getElementById('editStudentForm').addEventListener('submit', function (e) {
      e.preventDefault();
      const id = document.getElementById('edit_student_id').value;
      const data = {
        fullname: document.getElementById('edit_fullname').value,
        fathersname: document.getElementById('edit_fathersname').value,
        phone: document.getElementById('edit_phone').value,
        parent_phone: document.getElementById('edit_parent_phone').value,
        level: document.getElementById('edit_level').value,
        class_id: document.getElementById('edit_class_id').value,
      };

      fetch(`/dashboard/users/student/${id}/update`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        body: JSON.stringify(data)
      })
        .then(res => {
          if (res.status === 401 || res.status === 419) {
            window.location.reload();
            throw new Error('Expired');
          }
          return res.json();
        })
        .then(response => {
          if (response.success) {
            bootstrap.Modal.getInstance(document.getElementById('editStudentModal')).hide();
            const applyBtn = document.getElementById('btn-apply-filters');
            if (applyBtn) applyBtn.click();
          } else {
            alert('حدث خطأ أثناء حفظ التعديلات!');
          }
        })
        .catch(error => {
          if (error.message !== 'Expired') alert('حدث خطأ أثناء حفظ التعديلات!');
        });
    });

    window.openNewPunishmentModal = function (button) {
      event.stopPropagation();
      const userId = button.getAttribute('data-user-id');
      const userModel = button.getAttribute('data-user-model');
      const targetType = button.getAttribute('data-target-type');
      const userName = button.getAttribute('data-user-name');

      document.getElementById('new_punishable_id').value = userId;
      document.getElementById('new_punishable_type').value = userModel;
      document.getElementById('new_user_name_display').textContent = userName;

      const selectElem = document.getElementById('new_punishment_select');
      selectElem.innerHTML = '<option value="" disabled selected>جاري تحميل العقوبات...</option>';

      const modalElement = document.getElementById('newPunishmentModal');
      document.body.appendChild(modalElement);
      const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
      modal.show();

      fetch(`/punishments/api-types`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
        .then(res => res.json())
        .then(data => {
          selectElem.innerHTML = '<option value="" disabled selected>-- اختر الإجراء المناسب --</option>';

          const filteredPunishments = data.filter(p => p.target_type === targetType || p.target_type === 'all');

          if (filteredPunishments.length === 0) {
            selectElem.innerHTML = '<option value="" disabled>لا يوجد عقوبات متاحة لهذا النوع</option>';
            return;
          }

          filteredPunishments.forEach(p => {
            selectElem.insertAdjacentHTML('beforeend',
              `<option value="${p.id}">${p.name} - (${p.description || 'بدون وصف'})</option>`
            );
          });
        })
        .catch(error => {
          selectElem.innerHTML = '<option value="" disabled>حدث خطأ في تحميل البيانات</option>';
        });
    };
  </script>
@endpush