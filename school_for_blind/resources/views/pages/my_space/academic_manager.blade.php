@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
  <!-- الهيدر نظيف وبدون زر الـ Modal -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold m-0" style="color: var(--text-main);">مساحتي</h3>
  </div>

  @include('pages.my_space.partials.notes')
  @include('pages.my_space.partials.profile_info')

  <!-- كرت إعادة تعيين كلمة المرور المدمج بالواجهة -->
  <div class="card mt-4 mb-5 shadow-sm"> <!-- الكلاس card يأخذ ألوان الـ Dark Mode تلقائياً من الـ CSS -->
    <div class="card-body">
        <h5 class="fw-bold mb-4" style="color: var(--text-main);">
            <i class="fas fa-key me-2" style="color: var(--warning-color);"></i> إعادة تعيين كلمة مرور حساب الأهل
        </h5>
        
        <div id="searchSection">
            <label class="form-label fw-bold" style="color: var(--text-main);">رقم هاتف الطالب</label>
            <div class="input-group mb-2">
                <!-- حقل الإدخال سيستخدم ألوان --bg-main و --text-main بشكل آلي -->
                <input type="text" id="inlineStudentPhone" class="form-control" placeholder="أدخل رقم الطالب للبحث...">
                <button class="btn btn-primary px-4" id="btnSearchStudent" type="button">بحث</button>
            </div>
            <div id="searchAlert"></div>
        </div>

        <!-- تفاصيل الطالب (مخفية بالبداية وتظهر بعد البحث) -->
        <!-- استخدمنا --hover-bg لتمييز خلفية التفاصيل قليلاً عن لون الكرت -->
        <div id="studentDetailsSection" class="d-none mt-4 p-4 rounded" style="background-color: var(--hover-bg); border: 1px solid var(--border-color);">
            <h6 class="fw-bold mb-3" style="color: var(--info-color);">
                <i class="fas fa-user-graduate me-2"></i> بيانات الطالب:
            </h6>
            
            <div class="mb-2" style="color: var(--text-main);">
                <strong>الاسم:</strong> <span id="lblStudentName" class="text-muted ms-1"></span>
            </div>
            <div class="mb-2" style="color: var(--text-main);">
                <strong>رقم الطالب:</strong> <span id="lblStudentPhone" class="text-muted ms-1"></span>
            </div>
            <div class="mb-3" style="color: var(--text-main);">
                <strong>رقم ولي الأمر:</strong> <span id="lblParentPhone" class="text-muted ms-1"></span>
            </div>
            
            <hr style="border-color: var(--border-color);">
            
            <!-- استخدمنا كلاس .btn-accept الجاهز بملفك لأنه يحمل لون النجاح --success-color -->
            <button type="button" class="btn btn-accept w-100 fw-bold py-2 mt-2" id="btnConfirmReset">
                <i class="fab fa-whatsapp me-2"></i> تبديل وإرسال الرسالة
            </button>
            <div id="actionAlert" class="mt-3"></div>
        </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    const btnSearch = document.getElementById('btnSearchStudent');
    const btnConfirm = document.getElementById('btnConfirmReset');
    
    // 1. عملية البحث عن الطالب
    if(btnSearch) {
        btnSearch.addEventListener('click', function () {
            let phoneInput = document.getElementById('inlineStudentPhone');
            let phone = phoneInput ? phoneInput.value : '';
            let btn = this;
            let alertBox = document.getElementById('searchAlert');
            let detailsSection = document.getElementById('studentDetailsSection');

            if (!phone) {
                alertBox.innerHTML = `<div class="alert alert-warning py-2 mt-2 m-0 text-sm" style="background-color: var(--bg-card); color: var(--warning-color); border-color: var(--warning-color);">يرجى إدخال رقم الطالب أولاً.</div>`;
                return;
            }

            let originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btn.disabled = true;
            alertBox.innerHTML = '';
            detailsSection.classList.add('d-none');
            document.getElementById('actionAlert').innerHTML = '';

            fetch('{{ route("my_space.get_student_info") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ phone: phone })
            })
            .then(response => {
                if (!response.ok) { throw new Error('Network response was not ok'); }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('lblStudentName').innerText = data.data.name;
                    document.getElementById('lblStudentPhone').innerText = data.data.phone;
                    document.getElementById('lblParentPhone').innerText = data.data.parent_phone;
                    detailsSection.classList.remove('d-none');
                } else {
                    alertBox.innerHTML = `<div class="alert alert-danger py-2 mt-2 m-0 text-sm" style="background-color: var(--bg-card); color: var(--danger-color); border-color: var(--danger-color);">${data.message}</div>`;
                }
            })
            .catch(error => {
                console.error("Fetch Error: ", error);
                alertBox.innerHTML = `<div class="alert alert-danger py-2 mt-2 m-0 text-sm" style="background-color: var(--bg-card); color: var(--danger-color); border-color: var(--danger-color);">حدث خطأ بالشبكة أو في السيرفر. (راجع الـ Console)</div>`;
            })
            .finally(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });
    }

    // 2. عملية تبديل وإرسال كلمة المرور
    if(btnConfirm) {
        btnConfirm.addEventListener('click', function () {
            let phone = document.getElementById('lblStudentPhone').innerText;
            let btn = this;
            let alertBox = document.getElementById('actionAlert');

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> جاري التبديل والإرسال...';
            alertBox.innerHTML = '';

            fetch('{{ route("my_space.reset_password") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ phone: phone })
            })
            .then(response => {
                 if (!response.ok) { throw new Error('Network response was not ok'); }
                 return response.json();
            })
            .then(data => {
                if (data.success) {
                    alertBox.innerHTML = `<div class="alert alert-success m-0" style="background-color: var(--bg-card); color: var(--success-color); border-color: var(--success-color);"><i class="fas fa-check-circle me-1"></i> ${data.message}</div>`;
                    btn.style.display = 'none'; // إخفاء الزر بعد النجاح
                    
                    // مسح حقل الإدخال لتسهيل عملية بحث جديدة
                    document.getElementById('inlineStudentPhone').value = '';
                } else {
                    alertBox.innerHTML = `<div class="alert alert-danger m-0" style="background-color: var(--bg-card); color: var(--danger-color); border-color: var(--danger-color);">${data.message || 'حدث خطأ غير متوقع'}</div>`;
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fab fa-whatsapp me-2"></i> تبديل وإرسال الرسالة';
                }
            })
            .catch(error => {
                console.error("Fetch Error: ", error);
                alertBox.innerHTML = `<div class="alert alert-danger m-0" style="background-color: var(--bg-card); color: var(--danger-color); border-color: var(--danger-color);">حدث خطأ في الاتصال بالسيرفر. (راجع الـ Console)</div>`;
                btn.disabled = false;
                btn.innerHTML = '<i class="fab fa-whatsapp me-2"></i> تبديل وإرسال الرسالة';
            });
        });
    }

    // 3. إخفاء تفاصيل الطالب إذا تم تعديل رقم الهاتف (اختياري للـ UX)
    let phoneInput = document.getElementById('inlineStudentPhone');
    if(phoneInput) {
        phoneInput.addEventListener('input', function() {
            document.getElementById('studentDetailsSection').classList.add('d-none');
            document.getElementById('searchAlert').innerHTML = '';
            document.getElementById('actionAlert').innerHTML = '';
            
            // إعادة إظهار زر التبديل في حال كان مخفي من عملية سابقة ناجحة
            let btnConfirmReset = document.getElementById('btnConfirmReset');
            btnConfirmReset.style.display = 'block';
            btnConfirmReset.disabled = false;
            btnConfirmReset.innerHTML = '<i class="fab fa-whatsapp me-2"></i> تبديل وإرسال الرسالة';
        });
    }
});
</script>
@endpush