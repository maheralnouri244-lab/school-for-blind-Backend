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
            @forelse($user->punishments as $index => $punishment)
                @php
                    $isExpired = $punishment->pivot->expires_at && \Carbon\Carbon::parse($punishment->pivot->expires_at)->isPast();
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="fw-bold text-danger">{{ $punishment->description ?? $punishment->name }}</td>
                    <td>{{ $punishment->pivot->created_at ? $punishment->pivot->created_at->format('Y-m-d H:i') : '-' }}</td>
                    <td>{{ $punishment->pivot->expires_at ? \Carbon\Carbon::parse($punishment->pivot->expires_at)->format('Y-m-d H:i') : 'دائمة' }}</td>
                    <td>
                        @if($isExpired)
                            <span class="badge bg-secondary">منتهية</span>
                        @else
                            <span class="badge bg-danger">نشطة حالياً</span>
                        @endif
                    </td>
                    <td>
                        @if(!$isExpired)
                            <form action="{{ route('punishments.revoke', $punishment->pivot->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من إلغاء هذه العقوبة؟')">
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
                    <td colspan="6" class="text-center py-4 text-muted">لا يوجد أي عقوبات مسجلة لهذا الحساب.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>