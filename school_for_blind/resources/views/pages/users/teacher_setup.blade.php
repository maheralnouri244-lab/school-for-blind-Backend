@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4" dir="rtl">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="custom-card p-4 shadow-sm"
                    style="background-color: var(--bg-card); border-radius: 15px; border: 1px solid var(--border-color); color: var(--text-main);">
                    <h4 class="fw-bold mb-4"><i class="fa-solid fa-user-gear me-2"></i> إعداد وتفعيل بيانات المعلم:
                        {{ $teacher->full_name }}
                    </h4>
                    <hr style="border-color: var(--border-color);">

                    <form action="{{ route('dashboard.users.teacher.setup.submit', $teacher->id) }}" method="POST">
                        @csrf

                        <h5 class="fw-bold mb-3 mt-4">1. البيانات الأساسية والمالية</h5>
                        <div class="row g-3">
                            <div class="col-md-6 text-end">
                                <label class="form-label">الاسم الكامل</label>
                                <input type="text" name="full_name"
                                    class="form-control @error('full_name') is-invalid @enderror"
                                    value="{{ old('full_name', $teacher->full_name) }}"
                                    style="background-color: var(--bg-main); color: var(--text-main); border-color: var(--border-color);">
                                @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 text-end">
                                <label class="form-label">رقم الهاتف</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone', $teacher->phone) }}" dir="ltr"
                                    style="background-color: var(--bg-main); color: var(--text-main); border-color: var(--border-color);">
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 text-end">
                                <label class="form-label">المستوى التعليمي</label>
                                <select name="level" id="teacher-level"
                                    class="form-select @error('level') is-invalid @enderror"
                                    style="background-color: var(--bg-main); color: var(--text-main); border-color: var(--border-color);">
                                    <option value="ninth" {{ old('level', $activeLevel) == 'ninth' ? 'selected' : '' }}>التاسع
                                    </option>
                                    <option value="twelfth" {{ old('level', $activeLevel) == 'twelfth' ? 'selected' : '' }}>
                                        البكالوريا</option>
                                </select>
                                @error('level')
                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 text-end">
                                <label class="form-label">رقم البطاقة / Stripe Account ID (اختياري)</label>
                                <input type="text" name="stripe_account_id"
                                    class="form-control @error('stripe_account_id') is-invalid @enderror"
                                    value="{{ old('stripe_account_id', $teacher->stripe_account_id) }}"
                                    placeholder="أدخل رقم البطاقة إن وجد..." dir="ltr"
                                    style="background-color: var(--bg-main); color: var(--text-main); border-color: var(--border-color);">
                                @error('stripe_account_id')
                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <h5 class="fw-bold mb-3 mt-5">2. الشعب المدرسية المتاحة</h5>
                        <div class="row g-2" id="classes-container">
                            @forelse($classes as $class)
                                <div class="col-md-3">
                                    <div class="form-check p-3 border rounded"
                                        style="border-color: var(--border-color) !important;">
                                        <input class="form-check-input" type="checkbox" name="classes[]"
                                            value="{{ $class->id }}" id="class_{{ $class->id }}" {{ in_array($class->id, $teacher->classes->pluck('id')->toArray()) ? 'checked' : '' }}>
                                        <label class="form-check-label me-2 fw-semibold" for="class_{{ $class->id }}">
                                            {{ $class->name }}
                                        </label>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-muted">لا يوجد شعب مضافة لهذا المستوى.</div>
                            @endforelse
                        </div>
                        @error('classes')
                        <div class="text-danger small mt-1">{{ $message }}</div> @enderror

                        <h5 class="fw-bold mb-3 mt-5">3. المواد وسعر الحصة</h5>
                        <div class="row g-3" id="subjects-container">
                            @forelse($subjects as $subject)
                                @php
                                    $teacherSubjects = $teacher->getRelation('subjects') ?? collect();
                                    $pivot = $teacherSubjects->where('id', $subject->id)->first();

                                    $isChecked = $pivot ? true : false;
                                    $price = $pivot ? $pivot->pivot->price_for_lesson : 0;
                                @endphp
                                <div class="col-md-6">
                                    <div class="p-3 border rounded d-flex align-items-center justify-content-between"
                                        style="border-color: var(--border-color) !important;">
                                        <div class="form-check">
                                            <input class="form-check-input subject-checkbox" type="checkbox" name="subjects[]"
                                                value="{{ $subject->id }}" id="subject_{{ $subject->id }}" {{ $isChecked ? 'checked' : '' }}>
                                            <label class="form-check-label me-2 fw-bold" for="subject_{{ $subject->id }}">
                                                {{ $subject->name }}
                                            </label>
                                        </div>
                                        <div style="width: 140px;">
                                            <input type="number" name="prices[{{ $subject->id }}]" id="price_{{ $subject->id }}"
                                                value="{{ $price }}" class="form-control form-control-sm text-center"
                                                placeholder="سعر الحصة" {{ $isChecked ? '' : 'disabled' }}
                                                style="background-color: {{ $isChecked ? 'transparent' : 'var(--bg-main)' }}; color: var(--text-main); border-color: var(--border-color);">
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-muted">لا يوجد مواد مضافة لهذا المستوى.</div>
                            @endforelse
                        </div>
                        @error('subjects')
                        <div class="text-danger small mt-1">{{ $message }}</div> @enderror

                        <h5 class="fw-bold mb-3 mt-5">4. أوقات فراغ المعلم</h5>
                        <div class="table-responsive border rounded" style="border-color: var(--border-color) !important;">
                            <table class="table table-bordered mb-0 text-center align-middle"
                                style="color: var(--text-main);">
                                <thead style="background-color: var(--hover-bg);">
                                    <tr>
                                        <th>اليوم / الحصة</th>
                                        @for($p = 1; $p <= 7; $p++)
                                            <th>الحصة {{ $p }}</th>
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
                                            <td class="fw-bold" style="background-color: var(--hover-bg);">{{ $dayName }}</td>
                                            @for($p = 1; $p <= 7; $p++)
                                                @php
                                                    $isAvailable = isset($availabilities[$dayNum]) && in_array($p, $availabilities[$dayNum]);
                                                @endphp
                                                <td>
                                                    <input type="checkbox" name="availabilities[{{ $dayNum }}][]" value="{{ $p }}"
                                                        class="form-check-input" {{ $isAvailable ? 'checked' : '' }}>
                                                </td>
                                            @endfor
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex gap-3 mt-5">
                            <button type="submit" class="btn btn-success px-5 py-3 fw-bold shadow-sm"><i
                                    class="fa-solid fa-check me-2"></i> اعتماد وتثبيت قبول الأستاذ</button>
                            <a href="{{ route('dashboard.users.index') }}" class="btn btn-secondary px-4 py-3 fw-bold">إلغاء
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
                            priceInput.style.backgroundColor = 'transparent';
                        } else {
                            priceInput.setAttribute('disabled', 'disabled');
                            priceInput.value = 0;
                            priceInput.style.backgroundColor = 'var(--bg-main)';
                        }
                    }

                    // Use a standard event listener to prevent duplicate binding issues if called multiple times
                    checkbox.removeEventListener('change', handleCheckboxChange); // Remove old listener if exists
                    checkbox.addEventListener('change', handleCheckboxChange);
                });
            }

            function handleCheckboxChange(event) {
                const priceInput = document.getElementById('price_' + event.target.value);
                if (priceInput) {
                    if (event.target.checked) {
                        priceInput.removeAttribute('disabled');
                        priceInput.style.backgroundColor = 'transparent';
                    } else {
                        priceInput.setAttribute('disabled', 'disabled');
                        priceInput.value = 0;
                        priceInput.style.backgroundColor = 'var(--bg-main)';
                    }
                }
            }

            // Initialize checkboxes on page load based on PHP rendered data
            bindSubjectCheckboxes();

            const levelSelect = document.getElementById('teacher-level');

            levelSelect.addEventListener('change', function (event) {
                // Check if this was a true user interaction to prevent automatic firing on load
                if (!event.isTrusted) return;

                const level = this.value;
                console.log("Level changed manually to:", level); // Debugging

                fetch(`/dashboard/users/fetch-data-by-level?level=${level}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(res => res.json())
                    .then(data => {
                        console.log("Data received from AJAX:", data); // Debugging

                        const classesContainer = document.getElementById('classes-container');
                        classesContainer.innerHTML = '';
                        if (data.classes && data.classes.length > 0) {
                            data.classes.forEach(c => {
                                classesContainer.innerHTML += `
                                            <div class="col-md-3">
                                                <div class="form-check p-3 border rounded" style="border-color: var(--border-color) !important;">
                                                    <input class="form-check-input" type="checkbox" name="classes[]" value="${c.id}" id="class_${c.id}">
                                                    <label class="form-check-label me-2 fw-semibold" for="class_${c.id}">
                                                        ${c.name}
                                                    </label>
                                                </div>
                                            </div>
                                        `;
                            });
                        } else {
                            classesContainer.innerHTML = '<div class="col-12 text-muted">لا يوجد شعب مضافة لهذا المستوى.</div>';
                        }

                        const subjectsContainer = document.getElementById('subjects-container');
                        subjectsContainer.innerHTML = '';
                        if (data.subjects && data.subjects.length > 0) {
                            data.subjects.forEach(s => {
                                subjectsContainer.innerHTML += `
                                            <div class="col-md-6">
                                                <div class="p-3 border rounded d-flex align-items-center justify-content-between" style="border-color: var(--border-color) !important;">
                                                    <div class="form-check">
                                                        <input class="form-check-input subject-checkbox" type="checkbox" name="subjects[]" value="${s.id}" id="subject_${s.id}">
                                                        <label class="form-check-label me-2 fw-bold" for="subject_${s.id}">
                                                            ${s.name}
                                                        </label>
                                                    </div>
                                                    <div style="width: 140px;">
                                                        <input type="number" name="prices[${s.id}]" id="price_${s.id}" value="0" class="form-control form-control-sm text-center" placeholder="سعر الحصة" disabled style="background-color: var(--bg-main); color: var(--text-main); border-color: var(--border-color);">
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                            });
                        } else {
                            subjectsContainer.innerHTML = '<div class="col-12 text-muted">لا يوجد مواد مضافة لهذا المستوى.</div>';
                        }

                        // Re-bind listeners to newly created elements
                        bindSubjectCheckboxes();
                    })
                    .catch(error => console.error("Error fetching data:", error));
            });
        });
    </script>
@endpush