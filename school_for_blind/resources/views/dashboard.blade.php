@extends('layouts.app')

@section('content')
  <div id="splash-screen">
    <img src="{{ asset('img/logo-light.png') }}" alt="SESB Light" class="splash-logo logo-light">
    <img src="{{ asset('img/logo-dark.png') }}" alt="SESB Dark" class="splash-logo logo-dark">
  </div>

  <div class="container-fluid p-0"></div>
  <div class="container-fluid p-0">

    <div class="row g-4 mb-4">

      <div class="col-lg-4">
        <div class="custom-card h-100 d-flex flex-column justify-content-between">

          <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
              <p class="text-muted mb-1 fs-6 fw-bold">إجمالي الطلاب المسجلين</p>
              <h2 class="fw-bold mb-0" style="color: var(--text-main);">{{$studentsCount}}</h2>
            </div>
            <div class="p-3 rounded d-flex align-items-center justify-content-center bg-soft-info"
              style="width: 55px; height: 55px;">
              <i class="fa-solid fa-graduation-cap fs-3 text-info"></i>
            </div>
          </div>

          <div class="d-flex align-items-center gap-2 mt-auto pt-3 border-top"
            style="border-color: var(--border-color) !important;">

            <a href="{{ route('dashboard.users.index', ['type' => 'student']) }}"
              class="btn flex-grow-1 py-2 px-3 fw-bold d-flex align-items-center justify-content-center gap-2 transition-all"
              style="background-color: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color); border-radius: 10px; font-size: 0.9rem;">
              <i class="fa-solid fa-users-gear text-muted"></i>
              <span>إدارة الطلاب</span>
            </a>

            <a href="{{ route('dashboard.users.index', ['type' => 'student', 'status' => 'pending']) }}"
              class="btn flex-grow-1 py-2 px-3 fw-bold d-flex align-items-center justify-content-center gap-2 transition-all shadow-sm"
              style="background-color: #3b82f6; color: #ffffff; border: none; border-radius: 10px; font-size: 0.9rem;">
              <i class="fa-solid fa-user-plus"></i>
              <span>الطلبات</span>
              @if(isset($pendingstudentsCount) && $pendingstudentsCount > 0)
                <span class="badge bg-white text-primary rounded-pill px-2 py-1"
                  style="font-size: 0.75rem;">{{ $pendingstudentsCount }}</span>
              @endif
            </a>
          </div>

        </div>
      </div>

      <div class="col-lg-4">
        <div class="custom-card h-100 d-flex flex-column justify-content-between">

          <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
              <p class="text-muted mb-1 fs-6 fw-bold">إجمالي المعلمين المسجلين</p>
              <h2 class="fw-bold mb-0" style="color: var(--text-main);">{{$teachersCount}}</h2>
            </div>
            <div class="p-3 rounded d-flex align-items-center justify-content-center"
              style="width: 55px; height: 55px; background-color: rgba(163, 230, 53, 0.15);">
              <i class="fa-solid fa-person-chalkboard fs-3" style="color: var(--accent-color);"></i>
            </div>
          </div>

          <div class="d-flex align-items-center gap-2 mt-auto pt-3 border-top"
            style="border-color: var(--border-color) !important;">

            <a href="{{ route('dashboard.users.index', ['type' => 'teacher']) }}"
              class="btn flex-grow-1 py-2 px-3 fw-bold d-flex align-items-center justify-content-center gap-2 transition-all"
              style="background-color: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color); border-radius: 10px; font-size: 0.9rem;">
              <i class="fa-solid fa-chalkboard-user text-muted"></i>
              <span>إدارة المعلمين</span>
            </a>

            <a href="{{ route('dashboard.users.index', ['type' => 'teacher', 'status' => 'pending']) }}"
              class="btn flex-grow-1 py-2 px-3 fw-bold d-flex align-items-center justify-content-center gap-2 transition-all shadow-sm"
              style="background-color: #84cc16; color: #ffffff; border: none; border-radius: 10px; font-size: 0.9rem;">
              <i class="fa-solid fa-user-check"></i>
              <span>الطلبات</span>
              @if(isset($pendingteachersCount) && $pendingteachersCount > 0)
                <span class="badge bg-white text-success rounded-pill px-2 py-1"
                  style="font-size: 0.75rem;">{{ $pendingteachersCount }}</span>
              @endif
            </a>
          </div>

        </div>
      </div>

      <div class="col-lg-4">
        <a href="{{ route('content.monitor') }}" class="text-decoration-none d-block h-100">
          <div class="custom-card h-100 shadow-sm-hover" style="transition: transform 0.2s; cursor: pointer;">
            <div class="d-flex justify-content-between mb-4">
              <h5 class="fw-bold mb-0" style="color: var(--text-main);">مراقب المحتوى</h5>
              <div class="p-1 rounded d-flex align-items-center justify-content-center"
                style="width: 30px; height: 30px; background-color: rgba(163, 230, 53, 0.15);">
                <i class="fa-solid fa-shield-halved" style="color: var(--accent-color);"></i>
              </div>
            </div>

            <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
              <li class="d-flex align-items-center justify-content-between p-2 rounded shadow-sm-hover"
                style="transition: all 0.2s;">
                <div class="d-flex align-items-center">
                  <span class="p-2 rounded-circle me-3"
                    style="background: linear-gradient(135deg, #9ca3af, var(--accent-color)); box-shadow: 0 0 10px rgba(163, 230, 53, 0.3); width: 16px; height: 16px; display: inline-block;"></span>
                  <span style="color: var(--text-main); font-weight: 500;">البلاغات</span>
                </div>
                <i class="fa-solid fa-chevron-left text-muted fs-6"></i>
              </li>

              <li class="d-flex align-items-center justify-content-between p-2 rounded shadow-sm-hover"
                style="transition: all 0.2s;">
                <div class="d-flex align-items-center">
                  <span class="p-2 rounded-circle me-3"
                    style="background: linear-gradient(135deg, #9ca3af, #3b82f6); box-shadow: 0 0 10px rgba(59, 130, 246, 0.3); width: 16px; height: 16px; display: inline-block;"></span>
                  <span style="color: var(--text-main); font-weight: 500;">مشاكل تقنية</span>
                </div>
                <i class="fa-solid fa-chevron-left text-muted fs-6"></i>
              </li>

              <li class="d-flex align-items-center justify-content-between p-2 rounded shadow-sm-hover"
                style="transition: all 0.2s;">
                <div class="d-flex align-items-center">
                  <span class="p-2 rounded-circle me-3"
                    style="background: linear-gradient(135deg, #9ca3af, var(--accent-color)); box-shadow: 0 0 10px rgba(163, 230, 53, 0.3); width: 16px; height: 16px; display: inline-block;"></span>
                  <span style="color: var(--text-main); font-weight: 500;">المحتوى</span>
                </div>
                <i class="fa-solid fa-chevron-left text-muted fs-6"></i>
              </li>

              <li class="d-flex align-items-center justify-content-between p-2 rounded shadow-sm-hover"
                style="transition: all 0.2s;">
                <div class="d-flex align-items-center">
                  <span class="p-2 rounded-circle me-3"
                    style="background: linear-gradient(135deg, #9ca3af, #3b82f6); box-shadow: 0 0 10px rgba(59, 130, 246, 0.3); width: 16px; height: 16px; display: inline-block;"></span>
                  <span style="color: var(--text-main); font-weight: 500;">المحادثات</span>
                </div>
                <i class="fa-solid fa-chevron-left text-muted fs-6"></i>
              </li>
            </ul>
          </div>
        </a>
      </div>

    </div>

    <div class="row g-4 mb-4">

      <div class="col-lg-8">
        <div class="custom-card h-100 d-flex flex-column justify-content-center">
          <h5 class="fw-bold mb-4 text-center" style="color: var(--text-main);">تسجيل الطلاب حسب الشهر</h5>
          <canvas id="barChart" height="300"></canvas>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="custom-card h-100 d-flex flex-column justify-content-center">
          <h5 class="fw-bold mb-4 text-center" style="color: var(--text-main);">حالة الطلبات</h5>
          <canvas id="donutChart" height="200"></canvas>
        </div>
      </div>

    </div>

    <div class="row g-4 mb-4">
      <div class="col-12">
        <div class="custom-card">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0" style="color: var(--text-main);">سجل العمليات</h5>
            <a href="{{ route('logs.index') }}" class="btn btn-sm px-3"
              style="background-color: var(--hover-bg); color: var(--text-main); border: 1px solid var(--border-color); text-decoration: none;">
              عرض الكل
            </a>
          </div>

          <div class="table-responsive"
            style="border-radius: 16px; border: 1px solid var(--border-color); overflow: hidden;">
            <table class="table table-hover-custom align-middle mb-0" style="color: var(--text-main);">
              <thead>
                <tr style="border-bottom: 2px solid var(--border-color);">
                  <th scope="col" class="pb-3 text-muted fw-normal text-end px-3">الأنشطة</th>
                  <th scope="col" class="pb-3 text-muted fw-normal text-end px-3">القسم</th>
                  <th scope="col" class="pb-3 text-muted fw-normal text-end px-3">الحالة</th>
                  <th scope="col" class="pb-3 text-muted fw-normal text-start px-3">التاريخ</th>
                </tr>
              </thead>
              <tbody>
                @forelse($activities as $log)
                  <tr style="border-bottom: 1px solid var(--border-color);">
                    <td class="py-3 px-3">
                      <div class="d-flex align-items-center">
                        <div class="rounded-circle p-2 me-2 d-flex align-items-center justify-content-center"
                          style="width: 35px; height: 35px; background-color: var(--bg-main);">
                          @if($log->event === 'created')
                            <i class="fa-solid fa-plus text-success fs-6"></i>
                          @elseif($log->event === 'updated')
                            <i class="fa-solid fa-pen text-warning fs-6"></i>
                          @elseif($log->event === 'deleted')
                            <i class="fa-solid fa-trash text-danger fs-6"></i>
                          @else
                            <i class="fa-solid fa-file-signature text-info fs-6"></i>
                          @endif
                        </div>
                      </div>
                    </td>

                    <td class="py-3 text-muted px-3">
                      {{ class_basename($log->subject_type) }}
                    </td>

                    <td class="py-3 px-3">
                      @php
                        $eventColors = ['created' => 'success', 'updated' => 'warning', 'deleted' => 'danger'];
                        $eventNames = ['created' => 'إنشاء', 'updated' => 'تعديل', 'deleted' => 'حذف'];
                        $color = $eventColors[$log->event] ?? 'info';
                        $name = $eventNames[$log->event] ?? $log->event;
                      @endphp
                      <span class="badge-status bg-soft-{{ $color }} text-{{ $color }} px-2 py-1 rounded">
                        {{ $name }}
                      </span>
                    </td>

                    <td class="py-3 text-muted text-start px-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <span>{{ $log->created_at->format('Y-m-d') }}</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2" data-bs-toggle="modal"
                          data-bs-target="#logModal{{ $log->id }}">
                          <i class="fa-solid fa-eye"></i>
                        </button>
                      </div>
                    </td>
                  </tr>

                  <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content text-end" dir="rtl">
                        <div class="modal-header d-flex justify-content-between">
                          <h5 class="modal-title">تفاصيل العملية ({{ $name }})</h5>
                          <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-start" dir="ltr">
                          @if(isset($log->properties['custom_info']) && is_array($log->properties['custom_info']))
                            <div class="mb-4 p-3 rounded"
                              style="background-color: var(--hover-bg); border: 1px solid var(--border-color);">
                              <h6 class="text-end fw-bold mb-3" style="color: var(--text-main);">:معلومات إضافية</h6>
                              <div class="row text-end" dir="rtl">
                                @foreach($log->properties['custom_info'] as $key => $value)
                                  <div class="col-md-4 col-6 mb-3">
                                    <small class="text-muted d-block">{{ ucfirst(str_replace('_', ' ', $key)) }}</small>
                                    <strong style="color: var(--text-main);">
                                      {{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : ($value ?? '-') }}
                                    </strong>
                                  </div>
                                @endforeach
                              </div>
                            </div>
                          @endif

                          @if(isset($log->properties['attributes']))
                            <h6 class="text-success text-end">:القيم (الجديدة)</h6>
                            <pre
                              class="p-2 rounded"><code>{{ json_encode($log->properties['attributes'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                          @endif

                          @if(isset($log->properties['old']))
                            <h6 class="text-danger text-end mt-3">:القيم (القديمة)</h6>
                            <pre
                              class="p-2 rounded"><code>{{ json_encode($log->properties['old'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                          @endif

                          @if(!isset($log->properties['attributes']) && !isset($log->properties['old']))
                            <p class="text-muted text-end">لا توجد تفاصيل إضافية مسجلة لهذه العملية.</p>
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>
                @empty
                  <tr>
                    <td colspan="4" class="text-center py-4 text-muted">لا توجد سجلات.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      setTimeout(function () {
        const splash = document.getElementById('splash-screen');
        if (splash) {
          splash.classList.add('hidden-splash');
          setTimeout(() => {
            splash.style.display = 'none';
          }, 500);
        }
      }, 2000);
    });
    Chart.defaults.color = '#9ca3af';
    Chart.defaults.borderColor = '#2d3748';


    const barCtx = document.getElementById('barChart').getContext('2d');
    new Chart(barCtx, {
      type: 'bar',
      data: {
        labels: @json($chartLabels),
        datasets: [
          {
            label: 'الطلاب',
            data: @json($studentsChartData),
            backgroundColor: 'rgba(59, 130, 246, 0.6)',
            borderColor: '#3b82f6',
            borderWidth: 1,
            borderRadius: 4,
            barPercentage: 0.6
          },
          {
            label: 'المعلمون',
            data: @json($teachersChartData),
            backgroundColor: 'rgba(132, 204, 22, 0.6)',
            borderColor: '#84cc16',
            borderWidth: 1,
            borderRadius: 4,
            barPercentage: 0.6
          }
        ]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: true,
            position: 'top',
            labels: {
              usePointStyle: true
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1
            }
          }
        }
      }
    });

    const donutCtx = document.getElementById('donutChart').getContext('2d');
    new Chart(donutCtx, {
      type: 'doughnut',
      data: {
        labels: ['الطلاب', 'المعلمين'],
        datasets: [{
          data: [{{ $pendingstudentsCount ?? 0 }}, {{ $pendingteachersCount ?? 0 }}],
          backgroundColor: [
            'rgba(59, 130, 246, 0.7)',
            'rgba(132, 204, 22, 0.7)'
          ],
          borderWidth: 0,
          hoverOffset: 4
        }]
      },
      options: {
        responsive: true,
        cutout: '75%',
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              padding: 20,
              usePointStyle: true
            }
          }
        }
      }
    });
  </script>
@endpush