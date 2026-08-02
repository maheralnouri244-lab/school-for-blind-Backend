@extends('layouts.app')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1" style="color: var(--text-main);">سجل المكالمات السابقة</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0;">تاريخ الحصص المنتهية مع تفاصيل المدة والأجر</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge px-4 py-2 rounded-pill fw-bold" style="background-color: rgba(59, 130, 246, 0.12); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.5); font-size: 0.9rem;">
                    إجمالي المكالمات: {{ $pastCalls->count() }}
                </span>
                <a href="{{ route('admin.active-calls') }}" class="btn px-4 rounded-pill fw-bold shadow-sm-hover d-flex align-items-center gap-2" style="background-color: var(--hover-bg); color: var(--text-main); border: 1px solid var(--border-color); transition: transform 0.2s;">
                    <i class="fa-solid fa-video text-muted"></i>
                    الدروس الجارية
                </a>
            </div>
        </div>

        <div class="custom-card mb-4 p-3 border" style="border-color: var(--border-color) !important; border-radius: 16px;">
            <form action="{{ url()->current() }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-3">
                    <select name="teacher_id" class="form-select search-input rounded-pill bg-transparent" style="color: var(--text-main); border: 1px solid var(--border-color);">
                        <option value="">كل الأساتذة</option>
                        @if(isset($teachers))
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->full_name ?? $teacher->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="subject_id" class="form-select search-input rounded-pill bg-transparent" style="color: var(--text-main); border: 1px solid var(--border-color);">
                        <option value="">كل المواد</option>
                        @if(isset($subjects))
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->full_name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="payment_status" class="form-select search-input rounded-pill bg-transparent" style="color: var(--text-main); border: 1px solid var(--border-color);">
                        <option value="">كل حالات الدفع</option>
                        <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>مستحق الأجر</option>
                        <option value="deducted" {{ request('payment_status') == 'deducted' ? 'selected' : '' }}>مخصوم الأجر</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>تم الدفع (مغلق)</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn w-100 rounded-pill fw-bold shadow-sm-hover" style="background-color: rgba(59, 130, 246, 0.12); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.5); transition: transform 0.2s;">
                        <i class="fa-solid fa-filter me-1"></i> تصفية النتائج
                    </button>
                </div>
            </form>
        </div>

        <div class="custom-card p-0 overflow-hidden rounded-4 border" style="border-color: var(--border-color) !important; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
            @if($pastCalls->isEmpty())
                <div class="text-center py-5">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 80px; height: 80px; background-color: var(--hover-bg);">
                        <i class="fa-solid fa-clock-rotate-left fs-1 text-muted opacity-75"></i>
                    </div>
                    <h5 class="fw-bold" style="color: var(--text-main);">لا توجد مكالمات منتهية في السجل</h5>
                    <p class="text-muted">لم يتم العثور على أي بيانات مطابقة لخيارات التصفية الحالية.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover-custom mb-0 text-center align-middle" style="color: var(--text-main); border-color: var(--border-color);">
                        <thead style="background-color: var(--hover-bg);">
                            <tr>
                                <th class="py-3 px-3 border-0">اسم الغرفة</th>
                                <th class="py-3 px-3 border-0">الأستاذ</th>
                                <th class="py-3 px-3 border-0">الشعبة</th>
                                <th class="py-3 px-3 border-0">المادة</th>
                                <th class="py-3 px-3 border-0">التوقيت</th>
                                <th class="py-3 px-3 border-0">المدة</th>
                                <th class="py-3 px-3 border-0">الأجر المالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pastCalls as $call)
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <td class="px-3 fw-bold">{{ $call->room_name }}</td>

                                    <td class="px-3">
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <div class="p-2 rounded-circle bg-soft-info d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <i class="fa-solid fa-user-tie text-info" style="font-size: 0.85rem;"></i>
                                            </div>
                                            <span class="fw-bold">{{ $call->creator->full_name ?? $call->creator->name ?? 'غير معروف' }}</span>
                                        </div>
                                    </td>

                                    <td class="px-3">
                                        <span class="badge px-3 py-2 rounded-pill fw-normal" style="background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid #3b82f6;">
                                            {{ $call->schoolClass->name ?? 'شعبة ' . $call->class_id }}
                                        </span>
                                    </td>

                                    <td class="px-3">
                                        @if(auth('admin')->check() && in_array(auth('admin')->user()->role, ['Super Admin', 'Academic Manager']))
                                            <select class="form-select form-select-sm subject-select rounded-pill mx-auto fw-bold" data-room-id="{{ $call->id }}" style="background-color: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color); min-width: 130px; text-align: center;">
                                                <option value="">-- حدد المادة --</option>
                                                @if($call->creator && method_exists($call->creator, 'subjects'))
                                                    @foreach($call->creator->subjects()->get() as $subj)
                                                        <option value="{{ $subj->id }}" {{ $call->subject_id == $subj->id ? 'selected' : '' }}>
                                                            {{ $subj->full_name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        @else
                                            <span class="badge px-3 py-2 rounded-pill fw-normal" style="background-color: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color);">
                                                {{ $call->subject->full_name ?? 'لم تحدد المادة' }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-3" style="font-size: 0.85rem;" dir="ltr">
                                        <div class="mb-1 d-flex align-items-center justify-content-center gap-1" style="color: var(--text-main);">
                                            <i class="fa-solid fa-play text-success" style="font-size: 0.7rem;"></i> 
                                            <span>{{ \Carbon\Carbon::parse($call->started_at)->format('Y-m-d H:i') }}</span>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-center gap-1" style="color: var(--text-muted);">
                                            <i class="fa-solid fa-stop text-danger" style="font-size: 0.7rem;"></i> 
                                            <span>{{ $call->ended_at ? \Carbon\Carbon::parse($call->ended_at)->format('Y-m-d H:i') : '-----' }}</span>
                                        </div>
                                    </td>

                                    <td class="px-3">
                                        @php
                                            $duration = 0;
                                            if ($call->started_at && $call->ended_at) {
                                                $duration = \Carbon\Carbon::parse($call->started_at)->diffInMinutes($call->ended_at);
                                            }
                                        @endphp
                                        <span class="badge px-3 py-2 rounded-pill fw-bold" style="background-color: {{ $duration >= 30 ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)' }}; color: {{ $duration >= 30 ? '#10b981' : '#ef4444' }}; border: 1px solid {{ $duration >= 30 ? '#10b981' : '#ef4444' }};">
                                            {{ number_format($duration, 0) }} دقيقة
                                        </span>
                                    </td>

                                    <td class="px-3">
                                        @if($call->payment_status === 'paid')
                                            <span class="badge px-3 py-2 rounded-pill fw-bold w-100 d-inline-flex align-items-center justify-content-center gap-1" style="background-color: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid #10b981;">
                                                <i class="fa-solid fa-lock"></i> تم الدفع
                                            </span>
                                        @elseif(auth('admin')->check() && auth('admin')->user()->role === 'Super Admin')
                                            <button class="btn btn-sm rounded-pill fw-bold payment-toggle-btn w-100 shadow-sm-hover d-flex align-items-center justify-content-center gap-1" 
                                                data-room-id="{{ $call->id }}"
                                                data-status="{{ $call->payment_status }}"
                                                style="background-color: {{ $call->payment_status === 'unpaid' ? 'rgba(16, 185, 129, 0.12)' : 'rgba(239, 68, 68, 0.12)' }}; color: {{ $call->payment_status === 'unpaid' ? '#10b981' : '#ef4444' }}; border: 1px solid {{ $call->payment_status === 'unpaid' ? '#10b981' : '#ef4444' }}; transition: all 0.2s;">
                                                @if($call->payment_status === 'unpaid')
                                                    <i class="fa-solid fa-check-circle"></i> مستحق الأجر
                                                @else
                                                    <i class="fa-solid fa-circle-xmark"></i> مخصوم الأجر
                                                @endif
                                            </button>
                                        @else
                                            <span class="badge px-3 py-2 rounded-pill fw-bold w-100 d-inline-flex align-items-center justify-content-center gap-1" 
                                                style="background-color: {{ $call->payment_status === 'unpaid' ? 'rgba(16, 185, 129, 0.12)' : 'rgba(239, 68, 68, 0.12)' }}; color: {{ $call->payment_status === 'unpaid' ? '#10b981' : '#ef4444' }}; border: 1px solid {{ $call->payment_status === 'unpaid' ? '#10b981' : '#ef4444' }};">
                                                @if($call->payment_status === 'unpaid')
                                                    <i class="fa-solid fa-check-circle"></i> مستحق الأجر
                                                @else
                                                    <i class="fa-solid fa-circle-xmark"></i> مخصوم الأجر
                                                @endif
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        @if(auth('admin')->check() && in_array(auth('admin')->user()->role, ['Super Admin', 'Academic Manager']))
            const selects = document.querySelectorAll('.subject-select');
            selects.forEach(select => {
                select.addEventListener('change', async function () {
                    const roomId = this.getAttribute('data-room-id');
                    const subjectId = this.value;
                    if (!subjectId) return;

                    this.disabled = true;

                    try {
                        const response = await fetch("{{ route('rooms.assign_subject') }}", {
                            method: 'POST',
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": csrfToken
                            },
                            body: JSON.stringify({ room_id: roomId, subject_id: subjectId })
                        });
                        const data = await response.json();
                        if (!response.ok) {
                            alert(data.message || 'Error');
                        }
                    } catch (error) {
                        alert('Connection Failed');
                    } finally {
                        this.disabled = false;
                    }
                });
            });
        @endif

        @if(auth('admin')->check() && auth('admin')->user()->role === 'Super Admin')
            const toggleBtns = document.querySelectorAll('.payment-toggle-btn');
            toggleBtns.forEach(btn => {
                btn.addEventListener('click', async function () {
                    const roomId = this.getAttribute('data-room-id');
                    let currentStatus = this.getAttribute('data-status');

                    if (currentStatus === 'paid') return;

                    let newStatus = currentStatus === 'unpaid' ? 'deducted' : 'unpaid';
                    this.disabled = true;

                    try {
                        const response = await fetch(`/rooms/${roomId}/toggle-payment`, {
                            method: 'POST',
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": csrfToken
                            },
                            body: JSON.stringify({ payment_status: newStatus })
                        });

                        if (response.ok) {
                            this.setAttribute('data-status', newStatus);
                            if (newStatus === 'unpaid') {
                                this.style.backgroundColor = 'rgba(16, 185, 129, 0.12)';
                                this.style.color = '#10b981';
                                this.style.borderColor = '#10b981';
                                this.innerHTML = '<i class="fa-solid fa-check-circle"></i> مستحق الأجر';
                            } else {
                                this.style.backgroundColor = 'rgba(239, 68, 68, 0.12)';
                                this.style.color = '#ef4444';
                                this.style.borderColor = '#ef4444';
                                this.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> مخصوم الأجر';
                            }
                        } else {
                            alert('Error');
                        }
                    } catch (error) {
                        alert('Connection Failed');
                    } finally {
                        this.disabled = false;
                    }
                });
            });
        @endif
    });
    </script>
@endsection