@extends('layouts.app')

@section('content')

    <div id="splash-screen">
        <svg xmlns="http://www.w3.org/2000/svg" height="200px" width="200px" viewBox="0 0 200 200" class="pencil">
            <defs>
                <clipPath id="pencil-eraser">
                    <rect height="30" width="30" ry="5" rx="5"></rect>
                </clipPath>
            </defs>
            <circle transform="rotate(-113,100,100)" stroke-linecap="round" stroke-dashoffset="439.82"
                stroke-dasharray="439.82 439.82" stroke-width="2" stroke="currentColor" fill="none" r="70"
                class="pencil__stroke"></circle>
            <g transform="translate(100,100)" class="pencil__rotate">
                <g fill="none">
                    <circle transform="rotate(-90)" stroke-dashoffset="402" stroke-dasharray="402.12 402.12"
                        stroke-width="30" stroke="hsl(223,90%,50%)" r="64" class="pencil__body1"></circle>
                    <circle transform="rotate(-90)" stroke-dashoffset="465" stroke-dasharray="464.96 464.96"
                        stroke-width="10" stroke="hsl(223,90%,60%)" r="74" class="pencil__body2"></circle>
                    <circle transform="rotate(-90)" stroke-dashoffset="339" stroke-dasharray="339.29 339.29"
                        stroke-width="10" stroke="hsl(223,90%,40%)" r="54" class="pencil__body3"></circle>
                </g>
                <g transform="rotate(-90) translate(49,0)" class="pencil__eraser">
                    <g class="pencil__eraser-skew">
                        <rect height="30" width="30" ry="5" rx="5" fill="hsl(223,90%,70%)"></rect>
                        <rect clip-path="url(#pencil-eraser)" height="30" width="5" fill="hsl(223,90%,60%)"></rect>
                        <rect height="20" width="30" fill="hsl(223,10%,90%)"></rect>
                        <rect height="20" width="15" fill="hsl(223,10%,70%)"></rect>
                        <rect height="20" width="5" fill="hsl(223,10%,80%)"></rect>
                        <rect height="2" width="30" y="6" fill="hsla(223,10%,10%,0.2)"></rect>
                        <rect height="2" width="30" y="13" fill="hsla(223,10%,10%,0.2)"></rect>
                    </g>
                </g>
                <g transform="rotate(-90) translate(49,-30)" class="pencil__point">
                    <polygon points="15 0,30 30,0 30" fill="hsl(33,90%,70%)"></polygon>
                    <polygon points="15 0,6 30,0 30" fill="hsl(33,90%,50%)"></polygon>
                    <polygon points="15 0,20 10,10 10" fill="hsl(223,10%,10%)"></polygon>
                </g>
            </g>
        </svg>
    </div>

    <div class="container-fluid p-0">

        <div class="d-flex justify-content-between align-items-center mb-3 p-3 rounded custom-card">
            <div>
                <h4 class="fw-bold mb-1" style="color: var(--text-main);">
                    ساحة عمل جداول الدوام - {{ $level === 'ninth' ? 'الصف التاسع' : 'الصف البكالوريا' }}
                </h4>
                <span class="text-muted fs-6">عدد الشعب: {{ $classes->count() }} شعب | 7 أيام × 8 حصص</span>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary" onclick="toggleSidebar()">
                    <i class="fa-solid fa-users-gear ms-1"></i> دليـل الأساتذة
                </button>
                <button type="button" class="btn px-4 fw-bold" style="background-color: var(--accent-color); color: #000;"
                    onclick="saveAllSchedules()">
                    <i class="fa-solid fa-floppy-disk ms-1"></i> حفظ ونشر الكل
                </button>
            </div>
        </div>

        <div class="d-flex position-relative gap-3 overflow-hidden" style="min-height: 80vh;">
            <div class="flex-grow-1 position-relative overflow-hidden p-2 rounded custom-card" id="workspaceContainer"
                style="background-color: var(--bg-main); cursor: grab;">
                @php
                    $count = $classes->count();
                    $cols = max(1, round(sqrt($count / 1.14)));
                    $rows = ceil($count / $cols);
                    $canvasWidth = max($cols * 620 + 100, 1000);
                    $canvasHeight = max($rows * 540 + 100, 800);
                @endphp

                <div id="workspaceCanvas"
                    style="width: {{ $canvasWidth }}px; height: {{ $canvasHeight }}px; position: absolute; top: 0; left: 0; transform-origin: 0 0;">
                    @foreach($classes as $index => $class)
                        @php
                            $col = $index % $cols;
                            $row = floor($index / $cols);
                            $left = 50 + ($col * 620);
                            $top = 50 + ($row * 540);
                        @endphp
                        <div class="custom-card p-3 schedule-card position-absolute shadow-sm culled-card"
                            id="class-card-{{ $class->id }}"
                            style="width: 580px; left: {{ $left }}px; top: {{ $top }}px; z-index: 10;"
                            data-class-id="{{ $class->id }}" data-class-name="{{ $class->name }} - شعبة {{ $class->number }}">

                            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2 card-header-drag cursor-move"
                                style="cursor: move;">
                                <div class="d-flex align-items-center gap-2">
                                    <h5 class="fw-bold mb-0 text-primary">
                                        <i class="fa-solid fa-up-down-left-right me-1 text-muted"></i> {{ $class->name }} (شعبة
                                        {{ $class->number }})
                                    </h5>
                                    <button type="button" class="btn btn-sm btn-outline-secondary border-0 ms-2"
                                        onclick="toggleMinimizeCard(this, '{{ $class->id }}')"
                                        style="z-index: 1050; position: relative;">
                                        <i class="fa-solid fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger border-0"
                                        onclick="clearClassSchedule('{{ $class->id }}')"
                                        style="z-index: 1050; position: relative;">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-soft-warning text-warning fw-bold" id="counter-{{ $class->id }}">56
                                        حصة متبقية</span>
                                    <span class="badge bg-soft-info text-info">تفاعلي</span>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered text-center align-middle mb-0"
                                    style="font-size: 0.82rem; color: var(--text-main);">
                                    <thead>
                                        <tr class="table-header-custom">
                                            <th style="width: 70px;">اليوم / الحصة</th>
                                            @for($p = 1; $p <= 8; $p++)
                                                <th>ح {{ $p }}</th>
                                            @endfor
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $days = [
                                                '1' => 'الأحد',
                                                '2' => 'الإثنين',
                                                '3' => 'الثلاثاء',
                                                '4' => 'الأربعاء',
                                                '5' => 'الخميس',
                                                '6' => 'الجمعة',
                                                '7' => 'السبت'
                                            ];
                                        @endphp

                                        @foreach($days as $dayKey => $dayName)
                                            <tr>
                                                <td class="fw-bold bg-soft-secondary">{{ $dayName }}</td>
                                                @for($p = 1; $p <= 8; $p++)
                                                    <td class="cell-slot cursor-pointer position-relative p-1"
                                                        data-class-id="{{ $class->id }}" data-day="{{ $dayKey }}" data-period="{{ $p }}"
                                                        onclick="openAssignModal('{{ $class->id }}', '{{ $dayKey }}', '{{ $p }}', '{{ $dayName }}')"
                                                        style="height: 48px; border: 1px solid var(--border-color);">
                                                        <div class="slot-content d-flex flex-column justify-content-center h-100">
                                                            <span class="subject-text text-muted" style="font-size: 0.75rem;">-</span>
                                                            <span class="teacher-text fw-bold text-success"
                                                                style="font-size: 0.68rem;"></span>
                                                        </div>
                                                    </td>
                                                @endfor
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="custom-card p-3 shadow-sm border-start" id="teacherDrawer"
                style="width: 320px; min-width: 320px; display: none; height: 80vh; overflow-y: auto; z-index: 100;">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <h6 class="fw-bold m-0" style="color: var(--text-main);">دليل أوقات الأساتذة</h6>
                    <button type="button" class="btn-close" onclick="toggleSidebar()"></button>
                </div>

                <p class="text-muted" style="font-size: 0.8rem;">
                    العداد يعرض عدد الحصص المسندة حالياً بالأخضر.
                </p>

                <div class="d-flex flex-column gap-2" id="teachersList">
                    @foreach($teachers as $teacher)
                        <div class="teacher-wrapper">
                            <div class="p-2 rounded border teacher-item cursor-pointer shadow-sm-hover position-relative"
                                id="teacher-card-{{ $teacher->id }}" style="background-color: var(--bg-main); z-index: 2;"
                                onclick="toggleTeacherInfo('{{ $teacher->id }}')">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold"
                                        style="font-size: 0.85rem; color: var(--text-main);">{{ $teacher->full_name }}</span>
                                    <span class="badge bg-success rounded-pill" id="teacher-count-{{ $teacher->id }}">0
                                        حصص</span>
                                </div>
                            </div>
                            <div class="teacher-info-container shadow-sm" id="teacher-info-{{ $teacher->id }}"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="assignModal" tabindex="-1" aria-hidden="true" style="z-index: 1055;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glass-modal">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTitle">تعيين حصة جديدة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-start">
                    <input type="hidden" id="selectedClassId">
                    <input type="hidden" id="selectedDay">
                    <input type="hidden" id="selectedPeriod">

                    <div class="mb-3 text-end">
                        <label class="form-label fw-bold text-muted">1. اختر المادة الدراسية</label>
                        <select class="form-select search-input" id="subjectSelect" onchange="onSubjectChanged()">
                            <option value="">-- اختر مادة --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3 text-end">
                        <label class="form-label fw-bold text-muted">2. اختر المدرس (المتاحون والمخصصون فقط)</label>
                        <select class="form-select search-input" id="teacherSelect" disabled>
                            <option value="">-- اختر مادة أولاً --</option>
                        </select>
                        <div class="form-text text-danger d-none" id="noTeacherAlert">
                            تنبيه: لا يوجد أساتذة متاحون ومخصصون لهذه المادة وهذه الشعبة في هذا الوقت!
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearCurrentSlot()">تفريغ
                        الخلية</button>
                    <div>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                        <button type="button" class="btn btn-primary btn-sm px-3"
                            onclick="applySlotAssignment()">تطبيق</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="teacherAvailabilityModal" tabindex="-1" aria-hidden="true" style="z-index: 1055;">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content glass-modal">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-primary" id="availabilityModalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" id="availabilityModalBody">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/@panzoom/panzoom@4.5.1/dist/panzoom.min.js"></script>
    <script>
        let gridMatrix = {};
        const allTeachers = @json($teachers);
        const existingSchedules = @json($existingSchedules);

        existingSchedules.forEach(schedule => {
            const day = schedule.day_of_week;
            const period = schedule.period_number;
            const classId = schedule.class_id;

            if (!gridMatrix[day]) gridMatrix[day] = {};
            if (!gridMatrix[day][period]) gridMatrix[day][period] = {};

            gridMatrix[day][period][classId] = {
                subjectId: schedule.subject_id,
                subjectName: schedule.subject ? schedule.subject.name : '',
                teacherId: schedule.teacher_id,
                teacherName: schedule.teacher ? (schedule.teacher.full_name || schedule.teacher.first_name) : ''
            };
        });

        const splashStartTime = Date.now();

        window.onload = function () {
            Object.keys(gridMatrix).forEach(day => {
                Object.keys(gridMatrix[day]).forEach(period => {
                    Object.keys(gridMatrix[day][period]).forEach(classId => {
                        let slot = gridMatrix[day][period][classId];
                        updateCellUI(classId, day, period, slot.subjectName, slot.teacherName);
                    });
                });
            });

            updateTeacherCounters();
            updateAllClassCounters();
            evaluateGridConflicts();
            performCulling();

            const elapsedTime = Date.now() - splashStartTime;
            const minSplashTime = 6000;
            const timeLeft = Math.max(0, minSplashTime - elapsedTime);

            setTimeout(() => {
                hideSplashScreen();
            }, timeLeft);
        };

        function hideSplashScreen() {
            const splashScreen = document.getElementById('splash-screen');
            if (splashScreen) {
                splashScreen.classList.add('hidden-splash');
                setTimeout(() => splashScreen.remove(), 500);
            }
        }

        const canvasElement = document.getElementById('workspaceCanvas');
        const panzoom = Panzoom(canvasElement, {
            maxScale: 2,
            minScale: 0.3,
            contain: 'outside',
            excludeClass: 'schedule-card'
        });
        canvasElement.addEventListener('panzoomchange', performCulling);

        const container = document.getElementById('workspaceContainer');
        container.addEventListener('wheel', panzoom.zoomWithWheel);

        let isDraggingCard = false;
        document.querySelectorAll('.schedule-card').forEach(card => {
            const header = card.querySelector('.card-header-drag');
            let startX, startY, initialLeft, initialTop;

            header.addEventListener('mousedown', (e) => {
                isDraggingCard = true;
                startX = e.clientX;
                startY = e.clientY;
                initialLeft = card.offsetLeft;
                initialTop = card.offsetTop;
                card.style.zIndex = 1000;

                function onMouseMove(e) {
                    if (!isDraggingCard) return;
                    const scale = panzoom.getScale();
                    const dx = (e.clientX - startX) / scale;
                    const dy = (e.clientY - startY) / scale;
                    card.style.left = `${initialLeft + dx}px`;
                    card.style.top = `${initialTop + dy}px`;
                }

                function onMouseUp() {
                    isDraggingCard = false;
                    card.style.zIndex = 10;
                    document.removeEventListener('mousemove', onMouseMove);
                    document.removeEventListener('mouseup', onMouseUp);
                }

                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
            });
        });

        function toggleSidebar() {
            const drawer = document.getElementById('teacherDrawer');
            drawer.style.display = (drawer.style.display === 'none') ? 'block' : 'none';
        }

        function openAssignModal(classId, day, period, dayName) {
            document.getElementById('selectedClassId').value = classId;
            document.getElementById('selectedDay').value = day;
            document.getElementById('selectedPeriod').value = period;
            document.getElementById('modalTitle').innerText = `تعيين حصة (${dayName} - الحصة ${period})`;

            let subjectSelect = document.getElementById('subjectSelect');
            let teacherSelect = document.getElementById('teacherSelect');

            let current = getSlotData(day, period, classId);
            if (current && current.subjectId) {
                subjectSelect.value = current.subjectId;
                onSubjectChanged(current.teacherId);
            } else {
                subjectSelect.value = "";
                teacherSelect.innerHTML = '<option value="">-- اختر مادة أولاً --</option>';
                teacherSelect.disabled = true;
            }

            document.getElementById('noTeacherAlert').classList.add('d-none');
            let bsModal = new bootstrap.Modal(document.getElementById('assignModal'));
            bsModal.show();
        }

        function onSubjectChanged(selectedTeacherId = null) {
            const currentClassId = document.getElementById('selectedClassId').value;
            const subjectId = document.getElementById('subjectSelect').value;
            const teacherSelect = document.getElementById('teacherSelect');
            const alertBox = document.getElementById('noTeacherAlert');

            if (!subjectId) {
                teacherSelect.innerHTML = '<option value="">-- اختر مادة أولاً --</option>';
                teacherSelect.disabled = true;
                alertBox.classList.add('d-none');
                return;
            }

            let availableTeachers = allTeachers.filter(t => {
                let teachesSubject = t.subject_list && t.subject_list.some(s => s.id.toString() === subjectId.toString());
                let assignedToClass = t.classes && t.classes.some(c => c.id.toString() === currentClassId.toString());
                return teachesSubject && assignedToClass;
            });

            teacherSelect.innerHTML = '<option value="">-- اختر مدرس --</option>';

            if (availableTeachers.length === 0) {
                alertBox.classList.remove('d-none');
                teacherSelect.disabled = true;
            } else {
                alertBox.classList.add('d-none');
                teacherSelect.disabled = false;
                availableTeachers.forEach(t => {
                    let opt = document.createElement('option');
                    opt.value = t.id;
                    opt.textContent = t.full_name;
                    if (selectedTeacherId && selectedTeacherId.toString() === t.id.toString()) {
                        opt.selected = true;
                    }
                    teacherSelect.appendChild(opt);
                });
            }
        }

        function getBusyTeacherIdsAt(day, period, excludeClassId) {
            let busy = [];
            if (gridMatrix[day] && gridMatrix[day][period]) {
                Object.keys(gridMatrix[day][period]).forEach(cId => {
                    if (cId.toString() !== excludeClassId.toString()) {
                        let slot = gridMatrix[day][period][cId];
                        if (slot && slot.teacherId) {
                            busy.push(slot.teacherId.toString());
                        }
                    }
                });
            }
            return busy;
        }

        function applySlotAssignment() {
            const classId = document.getElementById('selectedClassId').value;
            const day = document.getElementById('selectedDay').value;
            const period = document.getElementById('selectedPeriod').value;

            const subjectSelect = document.getElementById('subjectSelect');
            const subjectId = subjectSelect.value;
            const subjectName = subjectSelect.options[subjectSelect.selectedIndex]?.text || '';

            const teacherSelect = document.getElementById('teacherSelect');
            const teacherId = teacherSelect.value;
            const teacherName = teacherSelect.options[teacherSelect.selectedIndex]?.text || '';

            if (!subjectId || !teacherId) {
                alert('يرجى اختيار المادة والأستاذ معاً.');
                return;
            }

            if (!gridMatrix[day]) gridMatrix[day] = {};
            if (!gridMatrix[day][period]) gridMatrix[day][period] = {};

            gridMatrix[day][period][classId] = {
                subjectId: subjectId,
                subjectName: subjectName,
                teacherId: teacherId,
                teacherName: teacherName
            };

            updateCellUI(classId, day, period, subjectName, teacherName);
            updateTeacherCounters();
            updateClassCounter(classId);
            evaluateGridConflicts();
            bootstrap.Modal.getInstance(document.getElementById('assignModal')).hide();
        }

        function clearCurrentSlot() {
            const classId = document.getElementById('selectedClassId').value;
            const day = document.getElementById('selectedDay').value;
            const period = document.getElementById('selectedPeriod').value;

            if (gridMatrix[day] && gridMatrix[day][period] && gridMatrix[day][period][classId]) {
                delete gridMatrix[day][period][classId];
            }

            updateCellUI(classId, day, period, '-', '');
            updateTeacherCounters();
            updateClassCounter(classId);
            evaluateGridConflicts();
            bootstrap.Modal.getInstance(document.getElementById('assignModal')).hide();
        }

        function getSlotData(day, period, classId) {
            if (gridMatrix[day] && gridMatrix[day][period] && gridMatrix[day][period][classId]) {
                return gridMatrix[day][period][classId];
            }
            return null;
        }

        function updateCellUI(classId, day, period, subject, teacherName) {
            const cell = document.querySelector(`.cell-slot[data-class-id="${classId}"][data-day="${day}"][data-period="${period}"]`);
            if (cell) {
                cell.querySelector('.subject-text').innerText = subject;
                cell.querySelector('.teacher-text').innerText = teacherName;
            }
        }

        function updateTeacherCounters() {
            let counts = {};
            Object.keys(gridMatrix).forEach(day => {
                Object.keys(gridMatrix[day]).forEach(period => {
                    Object.keys(gridMatrix[day][period]).forEach(cId => {
                        let tId = gridMatrix[day][period][cId].teacherId;
                        if (tId) {
                            counts[tId] = (counts[tId] || 0) + 1;
                        }
                    });
                });
            });

            allTeachers.forEach(t => {
                let badge = document.getElementById(`teacher-count-${t.id}`);
                if (badge) {
                    let cnt = counts[t.id] || 0;
                    badge.innerText = `${cnt} حصص`;
                }
            });
        }

        function saveAllSchedules() {
            let schedulesPayload = [];
            const dayKeys = ["1", "2", "3", "4", "5", "6", "7"];

            document.querySelectorAll('.schedule-card').forEach(card => {
                let classId = card.getAttribute('data-class-id');
                dayKeys.forEach(day => {
                    for (let p = 1; p <= 8; p++) {
                        let slot = getSlotData(day, p, classId);
                        if (slot && slot.subjectId && slot.teacherId) {
                            schedulesPayload.push({
                                class_id: classId,
                                day: day,
                                period: p,
                                subject_id: slot.subjectId,
                                teacher_id: slot.teacherId
                            });
                        } else {
                            schedulesPayload.push({
                                class_id: classId,
                                day: day,
                                period: p,
                                subject_id: null,
                                teacher_id: null
                            });
                        }
                    }
                });
            });

            fetch("{{ route('dashboard.schedules.storeBulk') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    schedules: schedulesPayload
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        window.location.href = "{{ route('dashboard.schedules.index') }}";
                    } else {
                        alert('حدث خطأ: ' + data.message);
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('حدث خطأ بالاتصال مع السيرفر.');
                });
        }

        function updateAllClassCounters() {
            document.querySelectorAll('.schedule-card').forEach(card => {
                updateClassCounter(card.getAttribute('data-class-id'));
            });
        }

        function updateClassCounter(classId) {
            let assignedCount = 0;
            const totalSlots = 56;

            Object.keys(gridMatrix).forEach(day => {
                Object.keys(gridMatrix[day]).forEach(period => {
                    if (gridMatrix[day][period][classId] && gridMatrix[day][period][classId].subjectId) {
                        assignedCount++;
                    }
                });
            });

            const remaining = totalSlots - assignedCount;
            const badge = document.getElementById(`counter-${classId}`);

            if (badge) {
                badge.innerText = `${remaining} حصة متبقية`;
                if (remaining === 0) {
                    badge.classList.remove('bg-soft-warning', 'text-warning');
                    badge.classList.add('bg-soft-success', 'text-success');
                    badge.innerText = `مكتمل`;
                } else {
                    badge.classList.remove('bg-soft-success', 'text-success');
                    badge.classList.add('bg-soft-warning', 'text-warning');
                }
            }
        }

        function clearClassSchedule(classId) {
            if (!confirm('هل أنت متأكد من تفريغ جدول هذه الشعبة بالكامل؟')) return;

            const dayKeys = ["1", "2", "3", "4", "5", "6", "7"];
            dayKeys.forEach(day => {
                for (let p = 1; p <= 8; p++) {
                    if (gridMatrix[day] && gridMatrix[day][p] && gridMatrix[day][p][classId]) {
                        delete gridMatrix[day][p][classId];
                        updateCellUI(classId, day, p, '-', '');
                    }
                }
            });

            updateTeacherCounters();
            updateClassCounter(classId);
            evaluateGridConflicts();
        }

        let currentHighlight = null;

        document.addEventListener('dblclick', function (e) {
            const cell = e.target.closest('.cell-slot');

            if (cell) {
                const subject = cell.querySelector('.subject-text').innerText;
                if (subject !== '-') {
                    toggleHighlight(subject);
                }
            } else {
                removeHighlight();
            }
        });

        function toggleHighlight(subjectName) {
            if (currentHighlight === subjectName) {
                removeHighlight();
                return;
            }

            currentHighlight = subjectName;

            document.querySelectorAll('.cell-slot').forEach(cell => {
                const cellSub = cell.querySelector('.subject-text').innerText;
                if (cellSub === subjectName) {
                    cell.classList.remove('dimmed');
                    cell.classList.add('highlighted');
                } else {
                    cell.classList.remove('highlighted');
                    cell.classList.add('dimmed');
                }
            });
        }

        function removeHighlight() {
            currentHighlight = null;
            document.querySelectorAll('.cell-slot').forEach(cell => {
                cell.classList.remove('dimmed', 'highlighted');
            });
        }
        function evaluateGridConflicts() {
            document.querySelectorAll('.cell-slot').forEach(cell => {
                cell.classList.remove('bg-soft-danger', 'bg-soft-warning');
                cell.style.backgroundColor = 'transparent';
            });

            let teacherSchedules = {};

            Object.keys(gridMatrix).forEach(day => {
                Object.keys(gridMatrix[day]).forEach(period => {
                    Object.keys(gridMatrix[day][period]).forEach(classId => {
                        let tId = gridMatrix[day][period][classId].teacherId;
                        if (tId) {
                            if (!teacherSchedules[tId]) teacherSchedules[tId] = [];
                            teacherSchedules[tId].push({ day: day, period: period, classId: classId });
                        }
                    });
                });
            });

            Object.keys(gridMatrix).forEach(day => {
                Object.keys(gridMatrix[day]).forEach(period => {
                    Object.keys(gridMatrix[day][period]).forEach(classId => {
                        let tId = gridMatrix[day][period][classId].teacherId;
                        if (!tId) return;

                        let cell = document.querySelector(`.cell-slot[data-class-id="${classId}"][data-day="${day}"][data-period="${period}"]`);
                        if (!cell) return;

                        let isHardConflict = teacherSchedules[tId].filter(s => s.day === day && s.period === period).length > 1;

                        let teacherData = allTeachers.find(t => t.id.toString() === tId.toString());
                        let isAvailable = false;

                        if (teacherData && teacherData.availabilities && teacherData.availabilities.length > 0) {
                            isAvailable = teacherData.availabilities.some(a => a.day_of_week.toString() === day.toString() && a.period_number.toString() === period.toString());
                        }

                        if (isHardConflict) {
                            cell.style.backgroundColor = '';
                            cell.classList.add('bg-soft-danger');
                        } else if (!isAvailable) {
                            cell.style.backgroundColor = '';
                            cell.classList.add('bg-soft-warning');
                        } else {
                            cell.style.backgroundColor = 'rgba(163, 230, 53, 0.15)';
                        }
                    });
                });
            });
        }

        function toggleTeacherInfo(teacherId) {
            const container = document.getElementById(`teacher-info-${teacherId}`);
            const card = document.getElementById(`teacher-card-${teacherId}`);

            if (container.classList.contains('expanded')) {
                container.classList.remove('expanded');
                card.classList.remove('active');
                return;
            }

            const teacher = allTeachers.find(t => t.id.toString() === teacherId.toString());
            if (!teacher) return;

            let subjectsHtml = teacher.subject_list && teacher.subject_list.length > 0
                ? teacher.subject_list.map(s => `<span class="badge bg-soft-info text-info me-1 mb-1">${s.name}</span>`).join('')
                : '<span class="text-muted" style="font-size:0.75rem;">لا يوجد</span>';

            let classesHtml = teacher.classes && teacher.classes.length > 0
                ? teacher.classes.map(c => `<span class="badge bg-soft-warning text-warning me-1 mb-1">${c.name} (${c.number})</span>`).join('')
                : '<span class="text-muted" style="font-size:0.75rem;">لا يوجد</span>';

            let html = `
                                                                                            <div class="mb-3 text-end">
                                                                                                <div class="fw-bold text-muted mb-1" style="font-size: 0.75rem;">المواد التي يدرسها:</div>
                                                                                                <div class="d-flex flex-wrap justify-content-end">${subjectsHtml}</div>
                                                                                            </div>
                                                                                            <div class="mb-3 text-end">
                                                                                                <div class="fw-bold text-muted mb-1" style="font-size: 0.75rem;">الشعب المخصصة:</div>
                                                                                                <div class="d-flex flex-wrap justify-content-end">${classesHtml}</div>
                                                                                            </div>
                                                                                            <div class="fw-bold text-muted mb-2 text-end" style="font-size: 0.75rem;">أوقات التفرغ:</div>
                                                                                            <div class="table-responsive">
                                                                                                <table class="table table-bordered text-center align-middle mb-0" style="font-size: 0.7rem; color: var(--text-main);">
                                                                                                    <thead>
                                                                                                        <tr class="table-header-custom">   
                                                                                                            <th class="p-1">يوم</th>
                                                                                                            ${[1, 2, 3, 4, 5, 6, 7, 8].map(p => `<th class="p-1">${p}</th>`).join('')}
                                                                                                        </tr>
                                                                                                    </thead>
                                                                                                    <tbody>
                                                                                        `;

            const dayKeys = ["1", "2", "3", "4", "5", "6", "7"];
            const dayNames = ["أحد", "إثن", "ثلا", "أرب", "خمي", "جمع", "سبت"];

            dayKeys.forEach((dKey, idx) => {
                html += `<tr><td class="fw-bold bg-soft-secondary p-1" style="font-size: 0.65rem;">${dayNames[idx]}</td>`;
                for (let p = 1; p <= 8; p++) {
                    let isAvailable = false;
                    if (teacher.availabilities && teacher.availabilities.length > 0) {
                        isAvailable = teacher.availabilities.some(a => a.day_of_week.toString() === dKey.toString() && a.period_number.toString() === p.toString());
                    }

                    if (isAvailable) {
                        html += `<td class="bg-soft-success text-success fw-bold p-0 align-middle"><i class="fa-solid fa-check" style="font-size: 0.6rem;"></i></td>`;
                    } else {
                        html += `<td class="text-muted bg-light p-0 align-middle" style="opacity: 0.5; font-size: 0.6rem;">-</td>`;
                    }
                }
                html += '</tr>';
            });

            html += `
                                                                                                    </tbody>
                                                                                                </table>
                                                                                            </div>
                                                                                        `;

            container.innerHTML = html;
            container.classList.add('expanded');
            card.classList.add('active');
        }

        function toggleMinimizeCard(btn, classId) {
            const card = document.getElementById(`class-card-${classId}`);
            const icon = btn.querySelector('i');

            if (card.classList.contains('card-minimized')) {
                card.classList.remove('card-minimized');
                icon.classList.remove('fa-plus');
                icon.classList.add('fa-minus');
            } else {
                card.classList.add('card-minimized');
                icon.classList.remove('fa-minus');
                icon.classList.add('fa-plus');
            }
        }

        function performCulling() {
            const containerRect = container.getBoundingClientRect();
            const canvasRect = canvasElement.getBoundingClientRect();
            const scale = panzoom.getScale();

            const buffer = 500;

            document.querySelectorAll('.schedule-card').forEach(card => {
                const x = parseFloat(card.style.left);
                const y = parseFloat(card.style.top);
                const w = 580;
                const h = card.classList.contains('card-minimized') ? 80 : 540;

                const cardScreenLeft = canvasRect.left + (x * scale);
                const cardScreenRight = cardScreenLeft + (w * scale);
                const cardScreenTop = canvasRect.top + (y * scale);
                const cardScreenBottom = cardScreenTop + (h * scale);

                const isVisible = (
                    cardScreenRight > containerRect.left - buffer &&
                    cardScreenLeft < containerRect.right + buffer &&
                    cardScreenBottom > containerRect.top - buffer &&
                    cardScreenTop < containerRect.bottom + buffer
                );

                if (isVisible) {
                    card.classList.remove('culled-card');
                } else {
                    card.classList.add('culled-card');
                }
            });
        }
    </script>
@endpush