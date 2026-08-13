@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4" dir="rtl">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="custom-card p-5 shadow-lg border-0"
                    style="background-color: var(--bg-card); border-radius: 20px; color: var(--text-main);">
                    
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="p-3 rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="background-color: var(--hover-bg); width: 60px; height: 60px;">
                            <i class="fa-solid fa-user-gear fs-3" style="color: var(--accent-color);"></i>
                        </div>
                        <h4 class="fw-bold mb-0">إعداد وتفعيل بيانات المعلم: <span style="color: var(--accent-color);">{{ $teacher->full_name }}</span></h4>
                    </div>
                    
                    <hr style="border-color: var(--border-color); opacity: 1;">

                    <form action="{{ route('dashboard.users.teacher.setup.submit', $teacher->id) }}" method="POST">
                        @csrf

                        <h5 class="fw-bold mb-4 mt-4" style="color: var(--text-main);"><span class="text-muted me-2">1.</span> البيانات الأساسية والمالية</h5>
                        <div class="row g-4">
                            <div class="col-md-6 text-end">
                                <label class="form-label fw-bold text-muted mb-2">الاسم الكامل</label>
                                <input type="text" name="full_name"
                                    class="form-control search-input py-2 shadow-sm @error('full_name') is-invalid @enderror"
                                    value="{{ old('full_name', $teacher->full_name) }}"
                                    style="border-radius: 10px;">
                                @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 text-end">
                                <label class="form-label fw-bold text-muted mb-2">رقم الهاتف</label>
                                <input type="text" name="phone" class="form-control search-input py-2 shadow-sm @error('phone') is-invalid @enderror"
                                    value="{{ old('phone', $teacher->phone) }}" dir="ltr"
                                    style="border-radius: 10px;">
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 text-end">
                                <label class="form-label fw-bold text-muted mb-2">المستوى التعليمي</label>
                                <select name="level" id="teacher-level"
                                    class="form-select search-input py-2 shadow-sm @error('level') is-invalid @enderror"
                                    style="border-radius: 10px;">
                                    <option value="ninth" {{ old('level', $activeLevel) == 'ninth' ? 'selected' : '' }}>التاسع
                                    </option>
                                    <option value="twelfth" {{ old('level', $activeLevel) == 'twelfth' ? 'selected' : '' }}>
                                        البكالوريا</option>
                                </select>
                                @error('level')
                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 text-end">
                                <label class="form-label fw-bold text-muted mb-2">رقم البطاقة / Stripe Account ID (اختياري)</label>
                                <input type="text" name="stripe_account_id"
                                    class="form-control search-input py-2 shadow-sm @error('stripe_account_id') is-invalid @enderror"
                                    value="{{ old('stripe_account_id', $teacher->stripe_account_id) }}"
                                    placeholder="أدخل رقم البطاقة إن وجد..." dir="ltr"
                                    style="border-radius: 10px;">
                                @error('stripe_account_id')
                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <h5 class="fw-bold mb-4 mt-5" style="color: var(--text-main);"><span class="text-muted me-2">2.</span> الشعب المدرسية المتاحة</h5>
                        <div class="row g-3" id="classes-container">
                            @forelse($classes as $class)
                                <div class="col-md-3">
                                    <div class="form-check p-3 rounded-3 shadow-sm transition-all shadow-sm-hover d-flex align-items-center"
                                        style="border: 1px solid var(--border-color); background-color: var(--bg-main);">
                                        <input class="form-check-input m-0 ms-3 shadow-none" type="checkbox" name="classes[]"
                                            value="{{ $class->id }}" id="class_{{ $class->id }}" {{ in_array($class->id, $teacher->classes->pluck('id')->toArray()) ? 'checked' : '' }}
                                            style="cursor: pointer; width: 1.2rem; height: 1.2rem; border-color: var(--border-color);">
                                        <label class="form-check-label fw-bold m-0 w-100" for="class_{{ $class->id }}" style="cursor: pointer; color: var(--text-main);">
                                            {{ $class->name }}
                                        </label>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-muted fw-bold p-3 rounded-3" style="background-color: var(--bg-main); border: 1px dashed var(--border-color);">لا يوجد شعب مضافة لهذا المستوى.</div>
                            @endforelse
                        </div>
                        @error('classes')
                        <div class="text-danger small mt-2 fw-bold">{{ $message }}</div> @enderror

                        <h5 class="fw-bold mb-4 mt-5" style="color: var(--text-main);"><span class="text-muted me-2">3.</span> المواد وسعر الحصة</h5>
                        <div class="row g-3" id="subjects-container">
                            @forelse($subjects as $subject)
                                @php
                                    $teacherSubjects = $teacher->getRelation('subjects') ?? collect();
                                    $pivot = $teacherSubjects->where('id', $subject->id)->first();

                                    $isChecked = $pivot ? true : false;
                                    $price = $pivot ? $pivot->pivot->price_for_lesson : 0;
                                @endphp
                                <div class="col-md-6">
                                    <div class="p-3 rounded-3 d-flex align-items-center justify-content-between shadow-sm transition-all shadow-sm-hover"
                                        style="border: 1px solid var(--border-color); background-color: var(--bg-main);">
                                        <div class="form-check d-flex align-items-center m-0">
                                            <input class="form-check-input subject-checkbox m-0 ms-3 shadow-none" type="checkbox" name="subjects[]"
                                                value="{{ $subject->id }}" id="subject_{{ $subject->id }}" {{ $isChecked ? 'checked' : '' }}
                                                style="cursor: pointer; width: 1.2rem; height: 1.2rem; border-color: var(--border-color);">
                                            <label class="form-check-label fw-bold m-0" for="subject_{{ $subject->id }}" style="cursor: pointer; color: var(--text-main);">
                                                {{ $subject->name }}
                                            </label>
                                        </div>
                                        <div style="width: 150px;">
                                            <div class="input-group input-group-sm shadow-sm rounded-3 overflow-hidden">
                                                <input type="number" name="prices[{{ $subject->id }}]" id="price_{{ $subject->id }}"
                                                    value="{{ $price }}" class="form-control text-center border-0 shadow-none py-2 fw-bold"
                                                    placeholder="السعر" {{ $isChecked ? '' : 'disabled' }}
                                                    style="background-color: {{ $isChecked ? 'var(--bg-card)' : 'var(--hover-bg)' }}; color: var(--text-main);">
                                                <span class="input-group-text border-0" style="background-color: var(--hover-bg); color: var(--text-muted); font-weight: bold;">$</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-muted fw-bold p-3 rounded-3" style="background-color: var(--bg-main); border: 1px dashed var(--border-color);">لا يوجد مواد مضافة لهذا المستوى.</div>
                            @endforelse
                        </div>
                        @error('subjects')
                        <div class="text-danger small mt-2 fw-bold">{{ $message }}</div> @enderror

                        <h5 class="fw-bold mb-4 mt-5" style="color: var(--text-main);"><span class="text-muted me-2">4.</span> أوقات فراغ المعلم</h5>
                        <div class="table-responsive rounded-3 shadow-sm overflow-hidden" style="border: 1px solid var(--border-color);">
                            <table class="table table-hover-custom mb-0 text-center align-middle"
                                style="color: var(--text-main); border-color: var(--border-color);">
                                <thead style="background-color: var(--hover-bg);">
                                    <tr>
                                        <th class="py-3" style="border-bottom: 2px solid var(--border-color) !important;">اليوم / الحصة</th>
                                        @for($p = 1; $p <= 7; $p++)
                                            <th class="py-3" style="border-bottom: 2px solid var(--border-color) !important;">الحصة {{ $p }}</th>
                                        @endfor
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $days = [
                                            1 => 'الأحد',
                                            2 => 'الإثنين',
                                            3 => 'الثلاثاء',
                                            4 => 'الأربعاء',
                                            5 => 'الخميس',
                                            6 => 'الجمعة',
                                            7 => 'السبت'
                                        ];
                                    @endphp
                                    @foreach($days as $dayNum => $dayName)
                                        <tr>
                                            <td class="fw-bold py-3" style="background-color: var(--hover-bg);">{{ $dayName }}</td>
                                            @for($p = 1; $p <= 7; $p++)
                                                @php
                                                    $isAvailable = isset($availabilities[$dayNum]) && in_array($p, $availabilities[$dayNum]);
                                                @endphp
                                                <td class="py-3">
                                                    <input type="checkbox" name="availabilities[{{ $dayNum }}][]" value="{{ $p }}"
                                                        class="form-check-input shadow-none m-0" {{ $isAvailable ? 'checked' : '' }}
                                                        style="cursor: pointer; width: 1.2rem; height: 1.2rem; border-color: var(--border-color);">
                                                </td>
                                            @endfor
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex gap-3 mt-5 pt-3 border-top" style="border-color: var(--border-color) !important;">
                            <button type="submit" class="btn btn-accept px-5 py-3 fw-bold shadow-sm rounded-3"><i
                                    class="fa-solid fa-check me-2"></i> اعتماد وتثبيت قبول الأستاذ</button>
                            <a href="{{ route('dashboard.users.index') }}" class="btn btn-outline-custom px-4 py-3 fw-bold rounded-3">إلغاء
                                والعودة</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function bindSubjectCheckboxes() {
                document.querySelectorAll('.subject-checkbox').forEach(checkbox => {
                    const priceInput = document.getElementById('price_' + checkbox.value);
                    if (priceInput) {
                        if (checkbox.checked) {
                            priceInput.removeAttribute('disabled');
                            priceInput.style.backgroundColor = 'var(--bg-card)';
                        } else {
                            priceInput.setAttribute('disabled', 'disabled');
                            priceInput.value = 0;
                            priceInput.style.backgroundColor = 'var(--hover-bg)';
                        }
                    }

                    checkbox.removeEventListener('change', handleCheckboxChange); 
                    checkbox.addEventListener('change', handleCheckboxChange);
                });
            }

            function handleCheckboxChange(event) {
                const priceInput = document.getElementById('price_' + event.target.value);
                if (priceInput) {
                    if (event.target.checked) {
                        priceInput.removeAttribute('disabled');
                        priceInput.style.backgroundColor = 'var(--bg-card)';
                    } else {
                        priceInput.setAttribute('disabled', 'disabled');
                        priceInput.value = 0;
                        priceInput.style.backgroundColor = 'var(--hover-bg)';
                    }
                }
            }

            bindSubjectCheckboxes();

            const levelSelect = document.getElementById('teacher-level');

            levelSelect.addEventListener('change', function (event) {
                if (!event.isTrusted) return;

                const level = this.value;
                console.log("Level changed manually to:", level); 

                fetch(`/dashboard/users/fetch-data-by-level?level=${level}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(res => res.json())
                    .then(data => {
                        console.log("Data received from AJAX:", data); 

                        const classesContainer = document.getElementById('classes-container');
                        classesContainer.innerHTML = '';
                        if (data.classes && data.classes.length > 0) {
                            data.classes.forEach(c => {
                                classesContainer.innerHTML += `
                                            <div class="col-md-3">
                                                <div class="form-check p-3 rounded-3 shadow-sm transition-all shadow-sm-hover d-flex align-items-center" style="border: 1px solid var(--border-color); background-color: var(--bg-main);">
                                                    <input class="form-check-input m-0 ms-3 shadow-none" type="checkbox" name="classes[]" value="${c.id}" id="class_${c.id}" style="cursor: pointer; width: 1.2rem; height: 1.2rem; border-color: var(--border-color);">
                                                    <label class="form-check-label fw-bold m-0 w-100" for="class_${c.id}" style="cursor: pointer; color: var(--text-main);">
                                                        ${c.name}
                                                    </label>
                                                </div>
                                            </div>
                                        `;
                            });
                        } else {
                            classesContainer.innerHTML = '<div class="col-12 text-muted fw-bold p-3 rounded-3" style="background-color: var(--bg-main); border: 1px dashed var(--border-color);">لا يوجد شعب مضافة لهذا المستوى.</div>';
                        }

                        const subjectsContainer = document.getElementById('subjects-container');
                        subjectsContainer.innerHTML = '';
                        if (data.subjects && data.subjects.length > 0) {
                            data.subjects.forEach(s => {
                                subjectsContainer.innerHTML += `
                                            <div class="col-md-6">
                                                <div class="p-3 rounded-3 d-flex align-items-center justify-content-between shadow-sm transition-all shadow-sm-hover" style="border: 1px solid var(--border-color); background-color: var(--bg-main);">
                                                    <div class="form-check d-flex align-items-center m-0">
                                                        <input class="form-check-input subject-checkbox m-0 ms-3 shadow-none" type="checkbox" name="subjects[]" value="${s.id}" id="subject_${s.id}" style="cursor: pointer; width: 1.2rem; height: 1.2rem; border-color: var(--border-color);">
                                                        <label class="form-check-label fw-bold m-0" for="subject_${s.id}" style="cursor: pointer; color: var(--text-main);">
                                                            ${s.name}
                                                        </label>
                                                    </div>
                                                    <div style="width: 150px;">
                                                        <div class="input-group input-group-sm shadow-sm rounded-3 overflow-hidden">
                                                            <input type="number" name="prices[${s.id}]" id="price_${s.id}" value="0" class="form-control text-center border-0 shadow-none py-2 fw-bold" placeholder="السعر" disabled style="background-color: var(--hover-bg); color: var(--text-main);">
                                                            <span class="input-group-text border-0" style="background-color: var(--hover-bg); color: var(--text-muted); font-weight: bold;">$</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                            });
                        } else {
                            subjectsContainer.innerHTML = '<div class="col-12 text-muted fw-bold p-3 rounded-3" style="background-color: var(--bg-main); border: 1px dashed var(--border-color);">لا يوجد مواد مضافة لهذا المستوى.</div>';
                        }

                        bindSubjectCheckboxes();
                    })
                    .catch(error => console.error("Error fetching data:", error));
            });
        });
    </script>
@endpush