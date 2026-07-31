@extends('layouts.app')

@section('content')
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
                <div id="workspaceCanvas" style="width: 3500px; height: 2500px; position: absolute; top: 0; left: 0;">
                    @foreach($classes as $index => $class)
                        @php
                            $col = $index % 3;
                            $row = floor($index / 3);
                            $left = 50 + ($col * 620);
                            $top = 50 + ($row * 540);
                        @endphp
                        <div class="custom-card p-3 schedule-card position-absolute shadow-sm" id="class-card-{{ $class->id }}"
                            style="width: 580px; left: {{ $left }}px; top: {{ $top }}px; z-index: 10;"
                            data-class-id="{{ $class->id }}" data-class-name="{{ $class->name }} - شعبة {{ $class->number }}">

                            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2 card-header-drag cursor-move"
                                style="cursor: move;">
                                <h5 class="fw-bold mb-0 text-primary">
                                    <i class="fa-solid fa-up-down-left-right me-2 text-muted"></i> {{ $class->name }} (شعبة
                                    {{ $class->number }})
                                </h5>
                                <span class="badge bg-soft-info text-info">تفاعلي</span>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered text-center align-middle mb-0"
                                    style="font-size: 0.82rem; color: var(--text-main);">
                                    <thead>
                                        <tr class="table-dark">
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
                        <div class="p-2 rounded border teacher-item" id="teacher-card-{{ $teacher->id }}"
                            style="background-color: var(--bg-main);">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold"
                                    style="font-size: 0.85rem; color: var(--text-main);">{{ $teacher->full_name }}</span>
                                <span class="badge bg-success rounded-pill" id="teacher-count-{{ $teacher->id }}">0 حصص</span>
                            </div>
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
        };

        const canvasElement = document.getElementById('workspaceCanvas');
        const panzoom = Panzoom(canvasElement, {
            maxScale: 2,
            minScale: 0.3,
            contain: 'outside',
            excludeClass: 'schedule-card'
        });

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
            const day = document.getElementById('selectedDay').value;
            const period = document.getElementById('selectedPeriod').value;
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

            let busyTeacherIds = getBusyTeacherIdsAt(day, period, currentClassId);

            let availableTeachers = allTeachers.filter(t => {
                let teachesSubject = t.subject_list && t.subject_list.some(s => s.id.toString() === subjectId.toString());
                let assignedToClass = t.classes && t.classes.some(c => c.id.toString() === currentClassId.toString());
                let notBusy = !busyTeacherIds.includes(t.id.toString());

                return teachesSubject &&
                    assignedToClass &&
                    notBusy;
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
                if (teacherName !== '') {
                    cell.style.backgroundColor = 'rgba(163, 230, 53, 0.15)';
                } else {
                    cell.style.backgroundColor = 'transparent';
                }
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
    </script>
@endpush