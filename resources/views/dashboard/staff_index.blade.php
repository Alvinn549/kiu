@extends('dashboard.layouts.main')

@section('title', 'Counter Dashboard')

@section('css')
    <style>
        .fw-black {
            font-weight: 800;
            letter-spacing: -1px;
        }

        .text-spacing-wide {
            letter-spacing: 2px;
        }

        .btn-hover-scale {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-hover-scale:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.1);
        }

        @keyframes soft-pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.4);
            }

            70% {
                box-shadow: 0 0 0 15px rgba(79, 70, 229, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(79, 70, 229, 0);
            }
        }

        .btn-pulse-primary {
            animation: soft-pulse 2s infinite;
        }

        .custom-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 10px;
        }

        @keyframes pulse-generic {
            0% {
                box-shadow: 0 0 0 0 rgba(var(--pulse-color), 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(var(--pulse-color), 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(var(--pulse-color), 0);
            }
        }

        .status-dot-pulse {
            --pulse-color: 25, 135, 84;

            animation: pulse-generic 2s infinite;
            border-radius: 50%;
        }

        .pulse-danger {
            --pulse-color: 220, 53, 69;
        }

        .pulse-primary {
            --pulse-color: 13, 110, 253;
        }

        .pulse-warning {
            --pulse-color: 255, 193, 7;
        }

        .pulse-secondary {
            --pulse-color: 108, 117, 125;
        }
    </style>
@endsection

@section('content')
    <section>
        @php
            $user = Auth::user();
            $counter = $user->counter;
            $service = $counter?->service;
            $currentStatus = $counter->status ?? 'closed';

            $statusColor = match ($currentStatus) {
                'open' => 'success',
                'break' => 'warning',
                'closed' => 'danger',
                default => 'secondary',
            };
            $statusLabel = \App\Models\Counter::STATUS[$currentStatus] ?? 'Offline';
        @endphp

        <div class="row">
            <div class="col-12">
                <div class="card bg-white-subtle border-0 shadow-sm rounded-4 ">
                    <div class="card-body p-4">
                        <div class="row g-4 align-items-center">

                            <div class="col-12 col-md-4 position-relative">
                                <div class="d-flex flex-column pe-md-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <span
                                            class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 d-flex align-items-center gap-2">
                                            <i class="bi bi-shop-window"></i>
                                            <span class="fw-bold">{{ $counter->name ?? 'No Counter' }}</span>
                                        </span>
                                    </div>
                                    <h4 class="fw-bold mb-0 text-truncate" title="{{ $user->name }}">
                                        {{ $user->name }}
                                    </h4>
                                    <small
                                        class="text-secondary fw-medium text-truncate">{{ '@' . $user->username }}</small>
                                </div>

                                <div class="d-none d-md-block position-absolute end-0 top-50 translate-middle-y bg-light-subtle"
                                    style="width: 1px; height: 70%;"></div>
                            </div>

                            <div class="col-12 col-md-3 position-relative">
                                <div class="px-md-2">
                                    <label class="text-uppercase text-muted fw-bold d-block mb-1"
                                        style="font-size: 0.65rem; letter-spacing: 1px;">Layanan Aktif</label>

                                    <h5 class="fw-black mb-0 text-truncate" title="{{ $service->name ?? 'Unavailable' }}">
                                        {{ $service->name ?? 'Unavailable' }}
                                    </h5>

                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span
                                            class="badge bg-light text-secondary border border-light-subtle rounded-pill fw-normal px-2"
                                            style="font-size: 0.75rem;">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ isset($service) ? substr($service->opening_time, 0, 5) . ' - ' . substr($service->closing_time, 0, 5) : '--:--' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="d-none d-md-block position-absolute end-0 top-50 translate-middle-y bg-light-subtle"
                                    style="width: 1px; height: 70%;"></div>
                            </div>

                            <div class="col-12 col-md-2">
                                <div class="text-start text-md-center px-md-1">
                                    <h2 class="fw-black font-monospace mb-0 lh-1" id="live-time">--:--</h2>
                                    <small class="text-secondary fw-medium d-block text-truncate"
                                        id="live-date">Loading...</small>
                                </div>
                            </div>

                            <div class="col-12 col-md-3">
                                <div class="dropdown w-100 ps-md-2">
                                    <button
                                        class="btn w-100 border border-light-subtle shadow-sm rounded-pill px-3 py-2 d-flex align-items-center justify-content-between gap-3 btn-hover-scale bg-white-subtle"
                                        type="button" data-bs-toggle="dropdown">

                                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                                            <span
                                                class="d-flex align-items-center justify-content-center bg-{{ $statusColor }} bg-opacity-10 rounded-circle flex-shrink-0"
                                                style="width: 28px; height: 28px;">
                                                <span
                                                    class="rounded-circle status-dot-pulse pulse-{{ $statusColor }} bg-{{ $statusColor }}"
                                                    style="width: 10px; height: 10px; box-shadow: 0 0 0 2px rgba(255,255,255,0.8);"></span>
                                            </span>
                                            <span class="fw-bold small text-truncate">{{ $statusLabel }}</span>
                                        </div>

                                        <i class="bi bi-chevron-down text-muted ms-1 flex-shrink-0"
                                            style="font-size: 0.7rem;"></i>
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end w-100 shadow-lg border-0 rounded-4 p-2 mt-2">
                                        <li class="px-3 py-2 text-uppercase text-muted fw-bold" style="font-size: 0.65rem;">
                                            Ubah Status Loket
                                        </li>
                                        @foreach (\App\Models\Counter::STATUS as $key => $label)
                                            <li>
                                                <form action="{{ route('counters.set-status', $counter->id) }}"
                                                    method="POST">
                                                    @csrf @method('PUT')
                                                    <input type="hidden" name="status" value="{{ $key }}">
                                                    <button type="submit"
                                                        class="dropdown-item rounded-3 py-2 d-flex align-items-center justify-content-between {{ $currentStatus == $key ? 'active fw-bold' : '' }}">
                                                        <span>{{ $label }}</span>
                                                        @if ($currentStatus == $key)
                                                            <i class="bi bi-check-lg text-white mb-2"></i>
                                                        @endif
                                                    </button>
                                                </form>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-6 col-md-3">
                <div class="card rounded-4 border-0 p-3 text-center btn-hover-scale">
                    <div class="text-warning fs-4 mb-1"><i class="bi bi-hourglass-split"></i></div>
                    <h4 class="fw-black mb-1">{{ $stats->waiting ?? 0 }}</h4>
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Menunggu</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card rounded-4 border-0 p-3 text-center btn-hover-scale">
                    <div class="text-success fs-4 mb-1"><i class="bi bi-check-circle-fill"></i></div>
                    <h4 class="fw-black mb-1">{{ $stats->completed ?? 0 }}</h4>
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Selesai</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card rounded-4 border-0 p-3 text-center btn-hover-scale">
                    <div class="text-danger fs-4 mb-1"><i class="bi bi-slash-circle-fill"></i></div>
                    <h4 class="fw-black mb-1">{{ $stats->skipped ?? 0 }}</h4>
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Skipped</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card rounded-4 border-0 p-3 text-center btn-hover-scale">
                    <div class="text-primary fs-4 mb-1"><i class="bi bi-clock-history"></i></div>
                    <h4 class="fw-black mb-1">{{ $stats->avg_time ?? '0m' }}</h4>
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Avg Time</small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                @if (!$currentQueue)
                    <div class="card rounded-4 border-0 shadow-sm text-center py-5 d-flex flex-column justify-content-center"
                        style="min-height: 500px;">

                        <div class="mb-4 position-relative">
                            <div class="bg-primary-subtle rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width: 100px; height: 100px;">
                                <i class="bi bi-megaphone-fill text-primary" style="font-size: 3rem;"></i>
                            </div>
                            @if ($nextQueue)
                                <span
                                    class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-danger border border-white">
                                    New!
                                </span>
                            @endif
                        </div>

                        <h3 class="fw-bold mb-2">Loket Tersedia</h3>

                        @if ($nextQueue)
                            <p class="text-muted mb-4">
                                Antrian berikutnya <span class="fw-bold">{{ $nextQueue->ticket_number }}</span>
                                siap dipanggil.
                            </p>
                            <div class="d-flex justify-content-center">
                                <form action="{{ route('queues.call', $nextQueue->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <button type="submit"
                                        class="btn btn-primary btn-lg rounded-pill px-5 py-3 shadow-lg btn-pulse-primary btn-hover-scale fw-bold">
                                        <i class="bi bi-broadcast me-2"></i> Panggil Sekarang
                                    </button>
                                </form>
                            </div>
                        @else
                            <p class="text-muted mb-4 px-5">Belum ada antrian baru. Silahkan istirahat sejenak atau
                                menunggu
                                pengunjung.</p>
                            <div>
                                <button class="btn btn-light text-muted border rounded-pill px-4 py-2" disabled>
                                    <span class="spinner-border spinner-border-sm me-2"></span> Menunggu Data...
                                </button>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="card rounded-4 shadow-sm border-0" style="min-height: 550px;">
                        <div
                            class="card-header border-0 bg-transparent pt-4 px-4 d-flex justify-content-between align-items-center">
                            <span
                                class="badge rounded-pill bg-white-subtle text-success shadow-sm px-3 py-2 border border-success-subtle d-flex align-items-center gap-2">
                                <span class="spinner-grow spinner-grow-sm text-success" role="status"></span>
                                <span class="fw-bold">Sedang Melayani</span>
                            </span>

                            <span
                                class="badge rounded-pill bg-white-subtle text-primary shadow-sm px-3 py-2 border border-primary-subtle d-flex align-items-center gap-2">
                                <span class="bi bi-stopwatch text-primary" role="status"></span>
                                <span class="fw-bold" id="timer">00:00:00</span>
                            </span>

                        </div>

                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <div class="mb-5 position-relative">
                                <p class="text-uppercase text-muted fw-bold text-spacing-wide mb-0 small">Nomor Antrian</p>
                                <h1 class="mb-0"
                                    style="font-size: 7rem; line-height: 1; text-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                                    {{ $currentQueue->ticket_number ?? '--' }}
                                </h1>
                            </div>

                            <div class="row g-3 px-md-5 mb-5">
                                <div class="col-6">
                                    <button
                                        class="btn btn-white text-warning border border-warning w-100 py-3 rounded-4 btn-hover-scale">
                                        <i class="bi bi-arrow-counterclockwise fs-4 me-2"></i>
                                        <span class="fw-bold fs-5">Panggil Ulang</span>
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button class="btn btn-success w-100 py-3 rounded-4 btn-hover-scale">
                                        <i class="bi bi-check-lg fs-4 me-2"></i>
                                        <span class="fw-bold fs-5">Selesaikan</span>
                                    </button>
                                </div>
                            </div>

                            <div class="position-relative text-center w-100 mb-4">
                                <hr class="text-muted opacity-25">
                                <span
                                    class="position-absolute top-50 start-50 translate-middle px-2 bg-body text-muted small text-uppercase"
                                    style="font-size: 0.65rem;">Opsi Lainnya
                                </span>
                            </div>

                            <div class="d-flex justify-content-center gap-3">
                                <button class="btn btn-outline-danger btn-sm rounded-pill px-4 hover-lift">
                                    <i class="bi bi-slash-circle me-1"></i> Lewati (Skip)
                                </button>
                                <button class="btn btn-outline-secondary btn-sm rounded-pill px-4 hover-lift">
                                    <i class="bi bi-arrow-left-right me-1"></i> Transfer
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-4" style="min-height: 550px; max-height: 550px;">
                    <div class="card-header bg-transparent border-0 p-2">
                        <ul class="nav nav-pills nav-fill bg-light-subtle p-1 rounded-pill" id="pills-tab"
                            role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active rounded-pill fw-bold small py-2" id="pills-waiting-tab"
                                    data-bs-toggle="pill" data-bs-target="#pills-waiting" type="button" role="tab">
                                    Menunggu ({{ count($waitingList) }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill fw-bold small py-2" id="pills-history-tab"
                                    data-bs-toggle="pill" data-bs-target="#pills-history" type="button" role="tab">
                                    Riwayat
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body p-0 custom-scroll" style="overflow-y: auto;">
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-waiting" role="tabpanel">
                                <div class="list-group list-group-flush px-2 pb-2">
                                    @forelse ($waitingList as $q)
                                        <div
                                            class="list-group-item border-0 rounded-3 mb-1 p-3 d-flex justify-content-between align-items-center bg-transparent hover-bg-light">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-white-subtle shadow-sm rounded-circle d-flex align-items-center justify-content-center me-3 text-primary fw-bold border"
                                                    style="width: 42px; height: 42px;">
                                                    {{ $loop->iteration }}
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-black">{{ $q->ticket_number }}</h6>
                                                    <small class="text-muted" style="font-size: 0.75rem;">Walk-in
                                                        Customer</small>
                                                </div>
                                            </div>
                                            <button class="btn btn-light btn-sm rounded-circle shadow-sm text-primary"
                                                data-bs-toggle="tooltip" title="Panggil Langsung">
                                                <i class="bi bi-play-fill"></i>
                                            </button>
                                        </div>
                                    @empty
                                        <div class="text-center py-5 opacity-50">
                                            <i class="bi bi-inbox fs-1 mb-2 d-block"></i>
                                            <small>Tidak ada antrian menunggu</small>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <div class="tab-pane fade" id="pills-history" role="tabpanel">
                                <div class="list-group list-group-flush px-2 pb-2">
                                    @forelse ($historyList as $h)
                                        <div
                                            class="list-group-item border-0 rounded-3 mb-1 p-3 d-flex justify-content-between align-items-center bg-transparent">
                                            <span class="fw-bold text-secondary">{{ $h->ticket_number }}</span>
                                            @if ($h->status == 'completed')
                                                <span
                                                    class="badge bg-success-subtle text-success rounded-pill px-3">Selesai</span>
                                            @else
                                                <span
                                                    class="badge bg-danger-subtle text-danger rounded-pill px-3">Skipped</span>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="text-center py-5 opacity-50">
                                            <small>Belum ada riwayat hari ini</small>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enable Bootstrap Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })

            const queueId = "{{ $currentQueue->id ?? 'no-queue' }}";
            const isServing = "{{ isset($currentQueue) }}";
            const serverStartTime = parseInt("{{ $serverTimeMs ?? 0 }}");
            const storageKey = `queue_start_${queueId}`;
            const timerEl = document.getElementById('timer');
            let intervalId;

            function formatTime(totalSeconds) {
                totalSeconds = Math.max(0, Math.floor(totalSeconds));
                const h = Math.floor(totalSeconds / 3600).toString().padStart(2, '0');
                const m = Math.floor((totalSeconds % 3600) / 60).toString().padStart(2, '0');
                const s = (totalSeconds % 60).toString().padStart(2, '0');
                return `${h}:${m}:${s}`;
            }

            function startTimer() {
                let startTimestamp = localStorage.getItem(storageKey);
                if (serverStartTime > 0) {
                    if (!startTimestamp || Math.abs(startTimestamp - serverStartTime) > 5000) {
                        startTimestamp = serverStartTime;
                        localStorage.setItem(storageKey, startTimestamp);
                    }
                } else if (!startTimestamp) {
                    startTimestamp = Date.now();
                    localStorage.setItem(storageKey, startTimestamp);
                }

                if (intervalId) clearInterval(intervalId);
                intervalId = setInterval(() => {
                    const now = Date.now();
                    const diffInSeconds = (now - startTimestamp) / 1000;
                    if (timerEl) timerEl.innerText = formatTime(diffInSeconds);
                }, 1000);
            }

            if (isServing) {
                startTimer();
            } else {
                if (queueId !== 'no-queue') localStorage.removeItem(storageKey);
                if (timerEl) timerEl.innerText = "00:00:00";
            }

            function updateClock() {
                const now = new Date();
                const optionsDate = {
                    weekday: 'long',
                    day: 'numeric',
                    month: 'short'
                };
                const dateString = now.toLocaleDateString('id-ID', optionsDate);
                const timeString = now.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                const timeEl = document.getElementById('live-time');
                const dateEl = document.getElementById('live-date');

                if (timeEl) timeEl.innerText = timeString;
                if (dateEl) dateEl.innerText = dateString;
            }

            updateClock();
            setInterval(updateClock, 1000);
        });
    </script>
@endsection
