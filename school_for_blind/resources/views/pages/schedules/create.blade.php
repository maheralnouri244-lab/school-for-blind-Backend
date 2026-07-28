@extends('layouts.app')

@section('content')
   <div class="container-fluid p-0d-flex flex-column align-items-center justify-content-center" style="min-height: 70vh;">
       <h2 class="fw-bold mb-5 text-center" style="color: var(--text-main); transition: opacity 0.4s ease;" id="mainTitle">
           ماذا تريد أن تنشئ اليوم؟
       </h2>

       <div class="row w-100 justify-content-center" id="optionsContainer">

           {{-- الخطوة 1: اختيار نوع الجدول --}}
           <div class="col-md-5 mb-4" id="timetableOption">
               <div class="custom-card h-100 d-flex flex-column align-items-center justify-content-center text-center p-5 shadow-sm-hover cursor-pointer transition-transform" 
                    onclick="selectType('school_timetable')" 
                    style="border: 2px solid transparent; hover: border-color: var(--accent-color);">
                   <i class="fa-regular fa-calendar-days mb-3" style="font-size: 4rem; color: var(--accent-color);"></i>
                   <h4 class="fw-bold" style="color: var(--text-main);">جدول دوام أسبوعي</h4>
                   <p class="text-muted mt-2">إنشاء وإدارة برامج الدوام الأسبوعية للطلاب والأساتذة.</p>
               </div>
           </div>

           <div class="col-md-5 mb-4" id="examOption">
               <div class="custom-card h-100 d-flex flex-column align-items-center justify-content-center text-center p-5 shadow-sm-hover cursor-pointer transition-transform" 
                    onclick="selectType('exam_schedule')"
                    style="border: 2px solid transparent;">
                   <i class="fa-solid fa-file-signature mb-3" style="font-size: 4rem; color: #3b82f6;"></i>
                   <h4 class="fw-bold" style="color: var(--text-main);">جدول امتحانات ومذاكرات</h4>
                   <p class="text-muted mt-2">ترتيب جداول الامتحانات وتوزيعها على الشعب.</p>
               </div>
           </div>

           {{-- الخطوة 2: اختيار الصف (مخفية بالبداية) --}}
           <div class="col-md-5 mb-4 d-none" id="ninthOption">
               <a href="#" id="linkNinth" class="text-decoration-none">
                   <div class="custom-card h-100 d-flex flex-column align-items-center justify-content-center text-center p-5 shadow-sm-hover transition-transform"
                        style="background: linear-gradient(135deg, rgba(163, 230, 53, 0.1), transparent);">
                       <h3 class="fw-bold" style="color: var(--text-main);">الصف التاسع</h3>
                   </div>
               </a>
           </div>

           <div class="col-md-5 mb-4 d-none" id="twelfthOption">
               <a href="#" id="linkTwelfth" class="text-decoration-none">
                   <div class="custom-card h-100 d-flex flex-column align-items-center justify-content-center text-center p-5 shadow-sm-hover transition-transform"
                        style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), transparent);">
                       <h3 class="fw-bold" style="color: var(--text-main);">البكالوريا</h3>
                   </div>
               </a>
           </div>

       </div>

       {{-- زر التراجع --}}
       <button class="btn btn-outline-secondary mt-4 d-none fw-bold px-4" id="backBtn" onclick="goBack()">
           <i class="fa-solid fa-arrow-right ms-2"></i> تراجع
       </button>
   </div>
@endsection

@push('scripts')
   <script>
       let selectedType = '';

       function selectType(type) {
           selectedType = type;

           // إخفاء خيارات النوع بتأثير Fade
           document.getElementById('timetableOption').style.opacity = '0';
           document.getElementById('examOption').style.opacity = '0';

           setTimeout(() => {
               document.getElementById('timetableOption').classList.add('d-none');
               document.getElementById('examOption').classList.add('d-none');

               // تحديث العناوين والروابط
               document.getElementById('mainTitle').innerText = 'اختر المرحلة الدراسية';

               let workspaceUrl = "{{ route('dashboard.schedules.workspace') }}";
               document.getElementById('linkNinth').href = `${workspaceUrl}?type=${type}&level=ninth`;
               document.getElementById('linkTwelfth').href = `${workspaceUrl}?type=${type}&level=twelfth`;

               // إظهار خيارات الصفوف
               let ninth = document.getElementById('ninthOption');
               let twelfth = document.getElementById('twelfthOption');
               let backBtn = document.getElementById('backBtn');

               ninth.classList.remove('d-none');
               twelfth.classList.remove('d-none');
               backBtn.classList.remove('d-none');

               // Animation In
               requestAnimationFrame(() => {
                   ninth.style.opacity = '1';
                   twelfth.style.opacity = '1';
                   ninth.style.transform = 'scale(1)';
                   twelfth.style.transform = 'scale(1)';
               });
           }, 300);
       }

       function goBack() {
           document.getElementById('ninthOption').style.opacity = '0';
           document.getElementById('twelfthOption').style.opacity = '0';

           setTimeout(() => {
               document.getElementById('ninthOption').classList.add('d-none');
               document.getElementById('twelfthOption').classList.add('d-none');
               document.getElementById('backBtn').classList.add('d-none');

               document.getElementById('mainTitle').innerText = 'ماذا تريد أن تنشئ اليوم؟';

               let timetable = document.getElementById('timetableOption');
               let exam = document.getElementById('examOption');

               timetable.classList.remove('d-none');
               exam.classList.remove('d-none');

               requestAnimationFrame(() => {
                   timetable.style.opacity = '1';
                   exam.style.opacity = '1';
               });
           }, 300);
       }
   </script>
   <style>
       .transition-transform { transition: all 0.3s ease; opacity: 1; transform: scale(1); }
       #ninthOption, #twelfthOption { opacity: 0; transform: scale(0.95); }
       .shadow-sm-hover:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
   </style>
@endpush