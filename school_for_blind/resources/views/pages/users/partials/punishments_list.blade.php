<div class="table-responsive rounded border" style="border-color: var(--border-color) !important;">
    <table class="table table-sm text-center align-middle mb-0" style="color: var(--text-main);">
        <thead style="background-color: var(--hover-bg);">
            <tr>
                <th>#</th>
                <th>نوع العقوبة</th>
                <th>تاريخ الفرض</th>
                <th>تاريخ الانتهاء</th>
                <th>الحالة</th>
                <th>إجراء</th>
            </tr>
        </thead>
        <tbody>
            {{-- تم التعديل هنا لقراءة المتغير الممرر من الكونترولر مباشرة --}}
            @forelse($punishments as $index => $record)
                @php
                    // $record يمثل سطر من جدول punishables
                    // و $record->punishment تجلب نوع العقوبة المرتبط
                    $punishmentType = $record->punishment;
                    $isExpired = $record->expires_at && \Carbon\Carbon::parse($record->expires_at)->isPast();
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    {{-- عرض اسم أو وصف العقوبة --}}
                    <td class="fw-bold text-danger">{{ $punishmentType->description ?? $punishmentType->name }}</td>

                    {{-- جلب التواريخ من السجل نفسه (بدون pivot) --}}
                    <td>{{ $record->created_at ? $record->created_at->format('Y-m-d H:i') : '-' }}</td>
                    <td>{{ $record->expires_at ? \Carbon\Carbon::parse($record->expires_at)->format('Y-m-d H:i') : 'دائمة' }}
                    </td>

                    <td>
                        @if($isExpired)
                            <span class="badge bg-secondary">منتهية</span>
                        @else
                            <span class="badge bg-danger">نشطة حالياً</span>
                        @endif
                    </td>
                    <td>
                        @if(!$isExpired)
                            {{-- إلغاء العقوبة يتم عن طريق ID السجل في جدول punishables --}}
                            <form action="{{ route('punishments.revoke', $record->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('هل أنت متأكد من إلغاء هذه العقوبة؟')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success">إلغاء العقوبة</button>
                            </form>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">لا يوجد أي عقوبات مسجلة لهذا المستخدم أو رقمه.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>