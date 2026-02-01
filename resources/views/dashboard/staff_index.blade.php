@extends('dashboard.layouts.main')

@section('title', 'Counter Dashboard')

@section('css')
    <link href="{{ asset('theme/dashboard/assets/extensions/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('theme/dashboard/assets/extensions/@fortawesome/fontawesome-free/css/all.min.css') }}"
        rel="stylesheet">

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

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(8px);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .loading-overlay.active {
            opacity: 1;
            pointer-events: all;
        }

        .spinner {
            width: 60px;
            height: 60px;
            border: 5px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .stat-pill {
            transition: all 0.2s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .stat-pill:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .dropdown-toggle[aria-expanded="true"] .chevron-rotate {
            transform: rotate(180deg);
        }

        .chevron-rotate {
            transition: transform 0.3s ease;
        }
    </style>
@endsection

@section('content')
    <section x-data="counterDashboard()" class="position-relative min-vh-100">

        <div class="loading-overlay" :class="{ 'active': isLoading || isProcessing }">
            <div class="spinner mb-4"></div>
            <h2 class="text-white fw-bold">Memproses...</h2>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card command-card border-0 rounded-4 shadow-sm">
                    <div class="card-body p-0">

                        <div class="p-4">
                            <div class="row g-4 align-items-center">
                                <div class="col-12 col-lg-4">
                                    <div class="ps-2 h-100">
                                        <div
                                            class="bg-light bg-opacity-50 rounded-4 p-3 d-flex flex-column justify-content-center h-100 border border-white">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="fas fa-desktop text-primary small"></i>
                                                    <span class="fw-bold text-primary small text-uppercase tracking-wider"
                                                        x-text="counter?.name"></span>
                                                </div>
                                                <span
                                                    class="badge rounded-pill bg-success-subtle text-success border border-success-subtle"
                                                    style="font-size: 0.6rem;">
                                                    <i class="fas fa-circle fa-xs me-1"></i> LIVE
                                                </span>
                                            </div>

                                            <div class="mt-1">
                                                <h4 class="fw-black mb-0 text-dark text-truncate" x-text="user?.name"></h4>
                                                <div class="text-muted small">
                                                    <i class="fas fa-at text-xs me-1"></i><span
                                                        x-text="user?.username"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4 text-center">
                                    <div
                                        class="d-inline-flex flex-column align-items-center px-5 py-3 rounded-4 bg-white shadow-sm border border-light transition-hover">
                                        <h2 class="fw-black font-monospace mb-0 text-dark tracking-tighter"
                                            style="font-size: 3rem; line-height: 1;" x-text="clockTime"></h2>
                                        <div
                                            class="d-flex align-items-center gap-2 text-muted mt-2 pt-2 border-top w-100 justify-content-center">
                                            <i class="far fa-calendar-alt text-primary small"></i>
                                            <span class="fw-bold small text-uppercase tracking-widest"
                                                x-text="clockDate"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="dropdown">
                                        <label class="small fw-bold text-muted text-uppercase mb-2 d-block ms-2"
                                            style="font-size: 0.65rem;">
                                            <i class="fas fa-sliders-h me-1"></i> Kontrol Loket
                                        </label>
                                        <button
                                            class="btn btn-white w-100 border shadow-sm rounded-4 px-4 py-3 d-flex align-items-center justify-content-between bg-white-subtle"
                                            type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="position-relative">
                                                    <div class="status-dot-pulse rounded-circle"
                                                        :class="`bg-${statusColor} pulse-${statusColor}`"
                                                        style="width: 12px; height: 12px;"></div>
                                                </div>
                                                <div class="text-start">
                                                    <span class="d-block fw-black text-dark lh-1"
                                                        x-text="statusLabel"></span>
                                                    <small class="text-muted" style="font-size: 0.7rem;">Ganti Status
                                                        Sekarang</small>
                                                </div>
                                            </div>
                                            <i class="fas fa-chevron-down text-muted small chevron-rotate"></i>
                                        </button>
                                        <ul
                                            class="dropdown-menu dropdown-menu-end shadow-xl border-0 rounded-4 p-2 mt-2 w-100">
                                            <template x-for="(label, key) in statusMap" :key="key">
                                                <li>
                                                    <button type="button" @click="updateStatus(key)"
                                                        class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center justify-content-between"
                                                        :class="counter?.status === key ? 'bg-primary text-white fw-bold' : ''">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <i class="fas fa-circle fa-xs"
                                                                :class="counter?.status === key ? 'text-white' : 'text-' + (
                                                                    key === 'active' ? 'success' : 'danger')"></i>
                                                            <span x-text="label"></span>
                                                        </div>
                                                        <i x-show="counter?.status === key" class="fas fa-check-circle"></i>
                                                    </button>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-primary-subtle border-top p-3 px-4 rounded-bottom-4">
                            <div class="row g-3">
                                <div class="col-6 col-lg-3">
                                    <div class="stat-pill d-flex align-items-center gap-3 p-3 rounded-4 shadow-sm bg-white">
                                        <div class="p-2 rounded-3 bg-primary-subtle text-primary text-center"
                                            style="width: 40px;">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div class="lh-sm">
                                            <p class="text-muted fw-bold text-uppercase mb-0"
                                                style="font-size: 0.55rem; letter-spacing: 0.5px;">Menunggu</p>
                                            <h4 class="fw-black mb-0 text-dark" x-text="waiting_count"></h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6 col-lg-3">
                                    <div class="stat-pill d-flex align-items-center gap-3 p-3 rounded-4 shadow-sm bg-white">
                                        <div class="p-2 rounded-3 bg-success-subtle text-success text-center"
                                            style="width: 40px;">
                                            <i class="fas fa-check-double"></i>
                                        </div>
                                        <div class="lh-sm">
                                            <p class="text-muted fw-bold text-uppercase mb-0"
                                                style="font-size: 0.55rem; letter-spacing: 0.5px;">Selesai</p>
                                            <h4 class="fw-black mb-0 text-dark" x-text="completed_count"></h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6 col-lg-3">
                                    <div class="stat-pill d-flex align-items-center gap-3 p-3 rounded-4 shadow-sm bg-white">
                                        <div class="p-2 rounded-3 bg-warning-subtle text-warning text-center"
                                            style="width: 40px;">
                                            <i class="fas fa-user-slash"></i>
                                        </div>
                                        <div class="lh-sm">
                                            <p class="text-muted fw-bold text-uppercase mb-0"
                                                style="font-size: 0.55rem; letter-spacing: 0.5px;">Dilewati</p>
                                            <h4 class="fw-black mb-0 text-dark" x-text="skipped_count"></h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6 col-lg-3">
                                    <div
                                        class="stat-pill d-flex align-items-center gap-3 p-3 rounded-4 shadow-sm bg-white">
                                        <div class="p-2 rounded-3 bg-info-subtle text-info text-center"
                                            style="width: 40px;">
                                            <i class="fas fa-hourglass-half"></i>
                                        </div>
                                        <div class="lh-sm">
                                            <p class="text-muted fw-bold text-uppercase mb-0"
                                                style="font-size: 0.55rem; letter-spacing: 0.5px;">Rata-rata</p>
                                            <h4 class="fw-black mb-0 text-dark" x-text="avg_service_time"></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">

                <template x-if="!currentTicket">
                    <div class="card rounded-4 border-0 shadow-sm text-center py-5 d-flex flex-column justify-content-center"
                        style="min-height: 550px;">
                        <div class="mb-4 position-relative">
                            <i class="bi bi-megaphone-fill text-primary" style="font-size: 3rem;"></i>
                            <template x-if="nextTicket">
                                <span
                                    class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-danger border border-white">New!</span>
                            </template>
                        </div>

                        <h3 class="fw-bold mb-2">Loket Tersedia</h3>

                        <template x-if="nextTicket">
                            <div>
                                <p class="text-muted mb-4">
                                    Antrian berikutnya <span class="fw-bold badge bg-light text-dark border"
                                        x-text="nextTicket.ticket?.ticket_number"></span>
                                    <br>
                                    <small x-text="nextTicket.service?.name"></small>
                                </p>
                                <div class="d-flex justify-content-center">
                                    <button type="button" @click="callQueue(nextTicket.id)"
                                        class="btn btn-primary btn-lg rounded-pill px-5 py-3 shadow-lg btn-pulse-primary fw-bold">
                                        <i class="bi bi-broadcast me-2"></i> Panggil Sekarang
                                    </button>
                                </div>
                            </div>
                        </template>

                        <template x-if="!nextTicket">
                            <p class="text-muted mb-4 px-5">Belum ada antrian baru.</p>
                        </template>
                    </div>
                </template>

                <template x-if="currentTicket">
                    <div class="card rounded-4 shadow-sm border-0" style="min-height: 550px;">

                        <div
                            class="card-header border-0 bg-transparent pt-4 px-4 d-flex justify-content-between align-items-center">
                            <span
                                class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-2 d-flex align-items-center gap-2">
                                <span class="spinner-grow spinner-grow-sm" role="status"></span>
                                <span class="fw-bold">Sedang Melayani</span>
                            </span>
                            <span
                                class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 d-flex align-items-center gap-2">
                                <i class="bi bi-stopwatch"></i>
                                <span class="fw-bold font-monospace" x-text="timerDisplay">00:00:00</span>
                            </span>
                        </div>

                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <div class="mb-2">
                                <p class="text-uppercase text-muted fw-bold text-spacing-wide mb-1 small">Nomor Antrian
                                </p>
                                <h1 class="mb-0 display-1 fw-black text-primary"
                                    x-text="currentTicket.ticket?.ticket_number">--</h1>
                                <span class="badge bg-light text-secondary mt-2 border"
                                    x-text="currentTicket.service?.name"></span>
                            </div>

                            <div class="row g-3 px-md-5 mb-5 mt-4">
                                <div class="col-6">
                                    <button type="button" @click="callQueue(currentTicket.id)"
                                        class="btn btn-white text-warning border border-warning w-100 py-3 rounded-4 btn-hover-scale shadow-sm">
                                        <i class="bi bi-arrow-counterclockwise fs-4 me-2"></i>
                                        <span class="fw-bold fs-5">Panggil Ulang</span>
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" @click="completeQueue(currentTicket.id)"
                                        class="btn btn-success w-100 py-3 rounded-4 btn-hover-scale shadow-lg">
                                        <i class="bi bi-check-lg fs-4 me-2"></i>
                                        <span class="fw-bold fs-5">Selesaikan</span>
                                    </button>
                                </div>
                            </div>

                            <div class="d-flex justify-content-center gap-3">
                                <button type="button" @click="skipQueue(currentTicket.id)"
                                    class="btn btn-link text-danger text-decoration-none">
                                    <i class="bi bi-slash-circle me-1"></i> Lewati (Skip)
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-4" x-data="{ tab: 'waiting' }"
                    style="min-height: 550px; max-height: 550px;">

                    <div class="card-header bg-transparent border-0 p-3">
                        <ul class="nav nav-pills nav-fill bg-light-subtle p-1 rounded-pill">
                            <li class="nav-item">
                                <button class="nav-link rounded-pill fw-bold small py-2"
                                    :class="tab === 'waiting' ? 'active shadow-sm' : ''" @click="tab='waiting'">
                                    Menunggu (<span x-text="waitingTicket.length"></span>)
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link rounded-pill fw-bold small py-2"
                                    :class="tab === 'history' ? 'active shadow-sm' : ''" @click="tab='history'">
                                    Riwayat
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body p-0 custom-scroll" style="overflow-y: auto;">

                        <div x-show="tab === 'waiting'">
                            <div class="list-group list-group-flush px-2 pb-2">
                                <template x-for="(q, index) in waitingTicket" :key="q.id">
                                    <div
                                        class="list-group-item border-0 rounded-3 mb-1 p-3 d-flex justify-content-between align-items-center bg-transparent hover-bg-light">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-white-subtle shadow-sm rounded-circle d-flex align-items-center justify-content-center me-3 text-primary fw-bold border"
                                                style="width: 42px; height: 42px;" x-text="index + 1"></div>
                                            <div>
                                                <h6 class="mb-0 fw-black" x-text="q.ticket?.ticket_number"></h6>
                                                <small class="text-muted" x-text="q.service?.name"></small>
                                            </div>
                                        </div>
                                        <button type="button" @click="directCallQueue(q.id)"
                                            class="btn btn-light btn-sm rounded-circle shadow-sm text-primary"
                                            title="Panggil Langsung">
                                            <i class="bi bi-play-fill"></i>
                                        </button>
                                    </div>
                                </template>
                                <template x-if="waitingTicket.length === 0">
                                    <div class="text-center">
                                        <div class="mt-5 opacity-50">
                                            <i class="bi bi-inbox fs-1 me-2"></i>
                                        </div>
                                        <div class="opacity-50">
                                            <small>Tidak ada antrian menunggu</small>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div x-show="tab === 'history'">
                            <div class="list-group list-group-flush px-2 pb-2">
                                <template x-for="h in historyTicket" :key="h.id">
                                    <div
                                        class="list-group-item border-0 rounded-3 mb-1 p-3 d-flex justify-content-between align-items-center bg-transparent">
                                        <div>
                                            <span class="fw-bold d-block" x-text="h.ticket?.ticket_number"></span>
                                            <small class="text-muted" x-text="h.service?.name"></small>
                                        </div>
                                        <span class="badge rounded-pill px-3"
                                            :class="h.status === 'completed' ? 'bg-success-subtle text-success' :
                                                'bg-danger-subtle text-danger'"
                                            x-text="h.status"></span>
                                    </div>
                                </template>
                                <template x-if="historyTicket.length === 0">
                                    <div class="text-center">
                                        <div class="mt-5 opacity-50">
                                            <i class="bi bi-inbox fs-1 me-2"></i>
                                        </div>
                                        <div class="opacity-50">
                                            <small>Tidak ada riwayat antrian</small>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script src="{{ asset('theme/dashboard/assets/extensions/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('theme/dashboard/assets/extensions/@fortawesome/fontawesome-free/js/all.min.js') }}"></script>
    <script>
        function counterDashboard() {
            return {
                isLoading: true,
                isProcessing: false,
                user: null,
                counter: null,
                waiting_count: 0,
                completed_count: 0,
                skipped_count: 0,
                avg_service_time: '0m',
                currentTicket: null,
                nextTicket: null,
                waitingTicket: [],
                historyTicket: [],
                statusMap: {
                    'open': 'Buka',
                    'break': 'Istirahat',
                    'closed': 'Tutup'
                },
                clockTime: '--:--',
                clockDate: '...',
                timerDisplay: '00:00:00',
                timerInterval: null,
                csrfToken: document.querySelector('meta[name="csrf-token"]').content,

                get statusColor() {
                    const colors = {
                        'open': 'success',
                        'break': 'warning',
                        'closed': 'danger'
                    };
                    return colors[this.counter?.status] || 'secondary';
                },

                get statusLabel() {
                    return this.statusMap[this.counter?.status] || '------';
                },

                async init() {
                    this.updateClock();
                    setInterval(() => this.updateClock(), 1000);
                    await this.fetchData();

                    Echo.channel('touch')
                        .listen('.QueueUpdated', (e) => {
                            console.log('WebSocket Event Received:', e);
                            this.fetchData();
                        });
                },

                async fetchData(showLoading = false) {
                    if (this.isProcessing) return;

                    this.isProcessing = true;
                    if (showLoading) this.isLoading = true;

                    try {
                        const res = await fetch("{{ route('fetch.counter-dashboard') }}", {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken
                            }
                        });
                        const data = await res.json();
                        console.log(data);

                        this.user = data.user;
                        this.counter = data.counter;
                        this.waiting_count = data.waiting_count || 0;
                        this.completed_count = data.completed_count || 0;
                        this.skipped_count = data.skipped_count || 0;
                        this.avg_service_time = data.avg_service_time || '0m';
                        this.currentTicket = data.currentTicket;
                        this.nextTicket = data.nextTicket;
                        this.waitingTicket = data.waitingTicket || [];
                        this.historyTicket = data.historyTicket || [];

                        this.startTimerFromBackend();

                    } catch (error) {
                        console.error("Fetch Error:", error);
                    } finally {
                        this.isProcessing = false;
                        setTimeout(() => this.isLoading = false, 300);
                    }
                },

                async sendAction(url, method = 'PUT', body = {}) {
                    this.isLoading = true;
                    try {
                        const res = await fetch(url, {
                            method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(body)
                        });

                        const data = await res.json();

                        if (!res.ok) throw new Error(data.message || 'Action failed');
                        this.fetchData(false);
                    } catch (e) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: e.message
                        });
                        this.isLoading = false;
                    }
                },

                callQueue(id) {
                    this.sendAction("{{ route('fetch.queues.call', ':ID') }}".replace(':ID', id));
                },

                directCallQueue(id) {
                    this.sendAction("{{ route('fetch.queues.direct-call', ':ID') }}".replace(':ID', id));
                },

                completeQueue(id) {
                    this.sendAction("{{ route('fetch.queues.complete', ':ID') }}".replace(':ID', id));
                },

                skipQueue(id) {
                    this.sendAction("{{ route('fetch.queues.skip', ':ID') }}".replace(':ID', id));
                },

                updateStatus(status) {
                    this.sendAction("{{ route('fetch.set-status-counter', ':ID') }}".replace(':ID', this.counter.id),
                        'PUT', {
                            status
                        });
                },

                startTimerFromBackend() {
                    if (this.timerInterval) clearInterval(this.timerInterval);

                    if (this.currentTicket && this.currentTicket.called_at) {
                        const startTime = new Date(this.currentTicket.called_at).getTime();

                        this.timerInterval = setInterval(() => {
                            const now = Date.now();
                            const diff = Math.floor((now - startTime) / 1000);

                            if (diff < 0) {
                                this.timerDisplay = "00:00:00";
                                return;
                            }

                            const h = Math.floor(diff / 3600).toString().padStart(2, '0');
                            const m = Math.floor((diff % 3600) / 60).toString().padStart(2, '0');
                            const s = (diff % 60).toString().padStart(2, '0');
                            this.timerDisplay = `${h}:${m}:${s}`;
                        }, 1000);
                    } else {
                        this.timerDisplay = '00:00:00';
                    }
                },

                updateClock() {
                    const now = new Date();
                    this.clockTime = now.toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false
                    });
                    this.clockDate = now.toLocaleDateString('id-ID', {
                        weekday: 'long',
                        day: 'numeric',
                        month: 'short'
                    });
                }
            }
        }
    </script>
@endsection
