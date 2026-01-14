@extends('dashboard.layouts.main')

@section('title', 'Dashboard')

@section('page-heading')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Dashboard</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .fw-black {
            font-weight: 900;
        }

        .hover-danger-soft:hover {
            background-color: #fef2f2 !important;
            border-color: #fca5a5 !important;
        }

        .hover-gray-soft:hover {
            background-color: #f8fafc !important;
            border-color: #cbd5e1 !important;
        }

        @keyframes pulse-blue {
            0% {
                box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(13, 110, 253, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(13, 110, 253, 0);
            }
        }

        .btn-pulse {
            animation: pulse-blue 2s infinite;
        }
    </style>
@endsection

@section('content')
    @php
        $currentQueue = (object) [
            'id' => 1,
            'ticket_number' => 'A-012',
            'status' => 'serving',
            'service_name' => 'Poli Umum',
            'start_time' => '2026-01-14 12:19:30',
        ];

        $nextQueue = (object) [
            'ticket_number' => 'A-013',
            'status' => 'waiting',
            'service_name' => 'Poli Umum',
        ];

        $waitingList = [
            (object) ['ticket_number' => 'A-013', 'is_online' => true],
            (object) ['ticket_number' => 'A-014', 'is_online' => false],
            (object) ['ticket_number' => 'A-015', 'is_online' => false],
        ];

        $historyList = [
            (object) ['ticket_number' => 'A-011', 'status' => 'completed'],
            (object) ['ticket_number' => 'A-010', 'status' => 'skipped'],
        ];

        $stats = (object) [
            'waiting' => 15,
            'completed' => 10,
            'skipped' => 2,
        ];

        $serverTimeMs = 0;

        if (isset($currentQueue) && $currentQueue->start_time) {
            $serverTimeMs = strtotime($currentQueue->start_time) * 1000;
        }
    @endphp

    <section>
        <div class="row mb-4">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body d-flex align-items-center p-3">
                        <div class="avatar avatar-lg me-3">
                            <img src="{{ asset('theme/dashboard/assets/compiled/jpg/1.jpg') }}" alt=""
                                class="rounded-circle" style="width: 56px; height: 56px;">
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">{{ Auth::user()->name }}</h6>
                            <small class="text-muted">Staff / {{ '@' . Auth::user()->username }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3 mb-md-0">
                <div
                    class="card border-0 shadow-sm rounded-4 h-100 bg-primary text-white position-relative overflow-hidden">
                    <div class="card-body p-3 text-center d-flex flex-column justify-content-center">
                        <small class="text-uppercase fw-bold" style="font-size: 0.7rem;">Active
                            Counter</small>
                        <h1 class="fw-bold mb-0 text-white">LOKET 01</h1>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-0 d-flex h-100">
                        <div class="flex-fill d-flex flex-column justify-content-center align-items-center border-end py-2">
                            <span class="fw-bold text-dark fs-4">{{ $stats->waiting }}</span>
                            <small class="text-muted" style="font-size: 0.65rem;">WAIT</small>
                        </div>
                        <div
                            class="flex-fill d-flex flex-column justify-content-center align-items-center border-end py-2 bg-success bg-opacity-10">
                            <span class="fw-bold text-success fs-4">{{ $stats->completed }}</span>
                            <small class="text-success" style="font-size: 0.65rem;">DONE</small>
                        </div>
                        <div class="flex-fill d-flex flex-column justify-content-center align-items-center py-2">
                            <span class="fw-bold text-danger fs-4">{{ $stats->skipped }}</span>
                            <small class="text-muted" style="font-size: 0.65rem;">SKIP</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mb-4">

                <div class="card border-2 border-dashed shadow-none mb-4 bg-light text-center {{ $currentQueue ? 'd-none' : '' }}"
                    style="border-style: dashed !important; border-color: #cbd5e1 !important;">
                    <div class="card-body py-5">
                        <div class="mb-4">
                            <div
                                class="rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center p-4">
                                <i class="bi bi-megaphone text-primary" style="font-size: 3rem;"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold text-dark">Loket Tersedia</h4>

                        {{-- Logic: Jika ada antrian menunggu, tampilkan nomornya. Jika tidak, tampilkan info kosong --}}
                        @if ($nextQueue)
                            <p class="text-muted mb-4">
                                Antrian berikutnya tersedia. <br>
                                Siap memanggil tiket <strong>{{ $nextQueue->ticket_number }}</strong>?
                            </p>
                            <button class="btn btn-primary btn-lg rounded-pill px-5 py-3 shadow-sm btn-pulse">
                                <i class="bi bi-broadcast me-2"></i> Panggil {{ $nextQueue->ticket_number }}
                            </button>
                        @else
                            <p class="text-muted mb-4">Belum ada antrian baru. Menunggu pengunjung...</p>
                            <button class="btn btn-secondary btn-lg rounded-pill px-5 py-3 shadow-sm" disabled>
                                <i class="bi bi-hourglass-split me-2"></i> Menunggu Antrian
                            </button>
                        @endif
                    </div>
                </div>

                <div class="card border-0 shadow-lg rounded-4 overflow-hidden h-100 {{ !$currentQueue ? 'd-none' : '' }}">
                    <div class="card-header border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <div>
                            <span
                                class="badge rounded-pill text-bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2">
                                <span class="spinner-grow spinner-grow-sm me-1" role="status" aria-hidden="true"></span>
                                Sedang Melayani
                            </span>
                        </div>
                        <div class="text-muted bg-light rounded-pill px-3 py-1">
                            <i class="bi bi-stopwatch me-2"></i>
                            <span class="fw-bold font-monospace text-dark" id="timer">00:00</span>
                        </div>
                    </div>

                    <div class="card-body text-center px-4 pb-4 pt-2 d-flex flex-column justify-content-center">

                        <div class="mb-2">
                            <small class="text-uppercase text-muted fw-bold letter-spacing-2 mb-2 d-block">Nomor
                                Antrian</small>
                            <div class="d-inline-block position-relative">
                                <h1 class="display-1 fw-black text-dark mb-0"
                                    style="font-size: 6rem; font-weight: 800; letter-spacing: -3px; line-height: 1;">
                                    {{ $currentQueue->ticket_number ?? '--' }}
                                </h1>
                            </div>
                        </div>

                        <div class="my-4">
                            <div
                                class="bg-light border border-secondary border-opacity-10 rounded-3 p-3 text-start d-flex justify-content-between align-items-center position-relative overflow-hidden">
                                <div class="position-absolute start-0 top-0 bottom-0 bg-primary" style="width: 4px;"></div>

                                <div class="ps-2">
                                    <small class="text-uppercase text-muted fw-bold"
                                        style="font-size: 0.65rem; letter-spacing: 1px;">
                                        <i class="bi bi-arrow-right-circle me-1"></i> Persiapan Berikutnya
                                    </small>
                                    <div class="d-flex align-items-center mt-1">
                                        @if ($nextQueue)
                                            <h4 class="mb-0 fw-bold text-dark me-2">{{ $nextQueue->ticket_number }}</h4>
                                            <span class="badge text-secondary border shadow-sm" style="font-size: 0.7rem;">
                                                Menunggu
                                            </span>
                                        @else
                                            <span class="text-muted fst-italic small">Tidak ada antrian</span>
                                        @endif
                                    </div>
                                </div>

                                @if ($nextQueue)
                                    <div class="text-end">
                                        <small class="text-muted d-block" style="font-size: 0.7rem;">Total Menunggu</small>
                                        <span class="fw-bold text-dark small">{{ count($waitingList) ?? 0 }} Orang</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <button class="btn btn-warning w-100 btn-lg  text-white shadow-sm rounded-3">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="bi bi-arrow-counterclockwise fs-4 mb-3"></i>
                                        <span class="fw-bold small">Panggil Ulang</span>
                                    </div>
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-success w-100 btn-lg  shadow rounded-3">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="bi bi-check-lg fs-4 mb-3"></i>
                                        <span class="fw-bold small">Selesaikan</span>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <div class="position-relative text-center w-100 mb-4">
                            <hr class="text-muted opacity-25">
                            <span
                                class="position-absolute top-50 start-50 translate-middle px-2 text-muted small text-uppercase"
                                style="font-size: 0.65rem;">Opsi Lainnya</span>
                        </div>

                        <div class="d-flex justify-content-center gap-3">
                            <button
                                class="btn btn-light text-danger btn-sm rounded-pill px-4 border border-danger border-opacity-10 hover-danger-soft">
                                <i class="bi bi-slash-circle me-1"></i> Lewati (Skip)
                            </button>
                            <button
                                class="btn btn-light text-secondary btn-sm rounded-pill px-4 border border-secondary border-opacity-10 hover-gray-soft">
                                <i class="bi bi-arrow-left-right me-1"></i> Transfer
                            </button>
                        </div>

                    </div>
                </div>


            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0" style="height: 100%;">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills nav-fill" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-bold " id="pills-waiting-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-waiting" type="button" role="tab">
                                    <i class="bi bi-people me-1"></i> Menunggu
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold " id="pills-history-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-history" type="button" role="tab">
                                    <i class="bi bi-clock-history me-1"></i> Riwayat
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-0 mt-2" style="max-height: 500px; overflow-y: auto;">
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-waiting" role="tabpanel">
                                <ul class="list-group list-group-flush">
                                    @foreach ($waitingList as $q)
                                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3 text-dark fw-bold"
                                                    style="width: 40px; height: 40px;">
                                                    {{ $loop->iteration }}
                                                </div>
                                                <div>
                                                    <h5 class="mb-0 fw-bold">{{ $q->ticket_number }}</h5>
                                                    @if ($q->is_online)
                                                        <small class="badge bg-info bg-opacity-10 text-info rounded-pill"
                                                            style="font-size: 0.65rem">Booking Online</small>
                                                    @else
                                                        <small class="text-muted">Walk-in</small>
                                                    @endif
                                                </div>
                                            </div>
                                            <button class="btn btn-sm btn-outline-primary rounded-circle"
                                                title="Panggil Langsung">
                                                <i class="bi bi-play-fill"></i>
                                            </button>
                                        </li>
                                    @endforeach

                                    @if (count($waitingList) == 0)
                                        <div class="text-center py-5">
                                            <p class="text-muted small">Tidak ada antrian menunggu</p>
                                        </div>
                                    @endif
                                </ul>
                            </div>

                            <div class="tab-pane fade" id="pills-history" role="tabpanel">
                                <ul class="list-group list-group-flush">
                                    @foreach ($historyList as $h)
                                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                            <div>
                                                <span class="fw-bold text-muted">{{ $h->ticket_number }}</span>
                                            </div>
                                            <div>
                                                @if ($h->status == 'completed')
                                                    <span class="badge bg-success rounded-pill">Selesai</span>
                                                @else
                                                    <span class="badge bg-danger rounded-pill">Skipped</span>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
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
            const queueId = "{{ $currentQueue->id ?? 'no-queue' }}";
            const isServing = "{{ isset($currentQueue) && $currentQueue->status == 'serving' ? 'yes' : 'no' }}";

            const serverStartTime = "{{ $serverTimeMs }}";

            const storageKey = `queue_start_${queueId}`;
            const timerEl = document.getElementById('timer');

            let intervalId;

            function formatTime(totalSeconds) {
                totalSeconds = Math.max(0, Math.floor(totalSeconds));

                const h = Math.floor(totalSeconds / 3600);
                const m = Math.floor((totalSeconds % 3600) / 60).toString().padStart(2, '0');
                const s = (totalSeconds % 60).toString().padStart(2, '0');

                if (h > 0) {
                    return `${h}:${m}:${s}`;
                }
                return `${m}:${s}`;
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

                intervalId = setInterval(() => {
                    const now = Date.now();
                    const diffInSeconds = (now - startTimestamp) / 1000;

                    if (timerEl) {
                        timerEl.innerText = formatTime(diffInSeconds);
                    }
                }, 1000);
            }

            if (isServing === 'yes') {
                startTimer();
            } else {
                if (queueId !== 'no-queue') localStorage.removeItem(storageKey);
                if (timerEl) timerEl.innerText = "00:00";
            }
        });
    </script>
@endsection
