<div class="table-responsive shadow-sm"
    style="border-radius: 16px; border: 1px solid var(--border-color); overflow: hidden; background-color: var(--bg-card);">
    <table class="table table-hover-custom align-middle text-center mb-0" style="color: var(--text-main);">
        <thead style="background-color: var(--hover-bg);">
            <tr>
                <th scope="col" class="py-3 px-3 text-muted fw-bold border-0">#</th>
                <th scope="col" class="py-3 px-3 text-muted fw-bold border-0">نوع العقوبة</th>
                <th scope="col" class="py-3 px-3 text-muted fw-bold border-0">تاريخ الفرض</th>
                <th scope="col" class="py-3 px-3 text-muted fw-bold border-0">تاريخ الانتهاء</th>
                <th scope="col" class="py-3 px-3 text-muted fw-bold border-0">الحالة</th>
                <th scope="col" class="py-3 px-3 text-muted fw-bold border-0">إجراء</th>
            </tr>
        </thead>
        <tbody>
            @forelse($punishments as $index => $record)
                @php
                    $punishmentType = $record->punishment;
                    $isExpired = $record->expires_at && \Carbon\Carbon::parse($record->expires_at)->isPast();
                @endphp
                <tr style="border-bottom: 1px solid var(--border-color); transition: background-color 0.2s ease;">
                    <td class="py-3 px-3 fw-bold">{{ $index + 1 }}</td>

                    <td class="py-3 px-3 fw-bold" style="color: var(--danger-color);">
                        {{ $punishmentType->description ?? $punishmentType->name }}
                    </td>

                    <td class="py-3 px-3 text-muted fw-medium" dir="ltr">
                        {{ $record->created_at ? $record->created_at->format('Y-m-d H:i') : '-' }}
                    </td>

                    <td class="py-3 px-3 text-muted fw-medium" dir="ltr">
                        {{ $record->expires_at ? \Carbon\Carbon::parse($record->expires_at)->format('Y-m-d H:i') : 'دائمة' }}
                    </td>

                    <td class="py-3 px-3">
                        @if($isExpired)
                            <span class="badge-status px-3 py-2 rounded-pill fw-bold shadow-sm text-muted"
                                style="background-color: var(--hover-bg); border: 1px solid var(--border-color);">
                                منتهية
                            </span>
                        @else
                            <span
                                class="badge-status bg-soft-danger px-3 py-2 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center gap-2"
                                style="color: var(--danger-color);">
                                <span
                                    style="width: 6px; height: 6px; border-radius: 50%; background-color: currentColor;"></span>
                                نشطة حالياً
                            </span>
                        @endif
                    </td>

                    <td class="py-3 px-3">
                        @if(!$isExpired)
                            <form action="{{ route('punishments.revoke', $record->id) }}" method="POST" class="d-inline m-0"
                                onsubmit="return confirm('هل أنت متأكد من إلغاء هذه العقوبة؟')">
                                @csrf
                                <button type="submit"
                                    class="btn btn-sm btn-accept fw-bold shadow-sm rounded-pill px-3 py-2 d-flex align-items-center justify-content-center gap-2 mx-auto">
                                    <i class="fa-solid fa-rotate-left"></i> إلغاء العقوبة
                                </button>
                            </form>
                        @else
                            <span class="text-muted fw-bold">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted fw-bold">
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <div class="p-4 rounded-circle mb-3 shadow-sm"
                                style="background-color: var(--hover-bg); border: 1px solid var(--border-color);">
                                <i class="fa-solid fa-shield-halved fs-1 text-muted" style="opacity: 0.6;"></i>
                            </div>
                            <span>لا يوجد أي عقوبات مسجلة لهذا المستخدم أو رقمه.</span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>