@extends('dashboard.layouts.main')

@section('title', 'Counter Dashboard')

@section('css')
    <link href="{{ asset('theme/dashboard/assets/extensions/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">

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
    <section x-data="counterDashboard()" x-init="init()" class="position-relative min-vh-100">

        <div x-show="isLoading" x-transition.opacity>
            <div class="d-flex flex-column align-items-center justify-content-center" style="height: 80vh;">
                <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="mt-3">
                    <span class="text-muted fw-medium">Memuat data...</span>
                </div>
            </div>
        </div>

        <div x-show="!isLoading" x-transition.opacity style="display: none;">

            <div class="row">
                <div class="col-12">
                    <div class="card bg-white-subtle border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <div class="row g-4 align-items-center">

                                <div class="col-12 col-md-4 position-relative">
                                    <div class="d-flex flex-column pe-md-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <span
                                                class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 d-flex align-items-center gap-2">
                                                <i class="bi bi-shop-window"></i>
                                                <span class="fw-bold" x-text="counter?.name || 'No Counter'"></span>
                                            </span>
                                        </div>
                                        <h4 class="fw-bold mb-0 text-truncate" :title="user?.name" x-text="user?.name">
                                        </h4>
                                        <small class="text-secondary fw-medium text-truncate"
                                            x-text="'@' + (user?.username || '')"></small>
                                    </div>

                                    <div class="d-none d-md-block position-absolute end-0 top-50 translate-middle-y bg-light-subtle"
                                        style="width: 1px; height: 70%;"></div>
                                </div>

                                <div class="col-12 col-md-3 position-relative">
                                    <div class="px-md-2">
                                        <label class="text-uppercase text-muted fw-bold d-block mb-1"
                                            style="font-size: 0.65rem; letter-spacing: 1px;">Layanan Aktif</label>
                                        <h5 class="fw-black mb-0 text-truncate" :title="service?.name || 'Unavailable'"
                                            x-text="service?.name || 'Unavailable'"></h5>

                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <span
                                                class="badge bg-light text-secondary border border-light-subtle rounded-pill fw-normal px-2"
                                                style="font-size: 0.75rem;">
                                                <i class="bi bi-clock me-1"></i>
                                                <span
                                                    x-text="service ? (service.opening_time.substr(0,5) + ' - ' + service.closing_time.substr(0,5)) : '--:--'"></span>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="d-none d-md-block position-absolute end-0 top-50 translate-middle-y bg-light-subtle"
                                        style="width: 1px; height: 70%;"></div>
                                </div>

                                <div class="col-12 col-md-2">
                                    <div class="text-start text-md-center px-md-1">
                                        <h2 class="fw-black font-monospace mb-0 lh-1" x-text="clockTime">--:--</h2>
                                        <small class="text-secondary fw-medium d-block text-truncate"
                                            x-text="clockDate">...</small>
                                    </div>
                                </div>

                                <div class="col-12 col-md-3">
                                    <div class="dropdown w-100 ps-md-2">
                                        <button
                                            class="btn w-100 border border-light-subtle shadow-sm rounded-pill px-3 py-2 d-flex align-items-center justify-content-between gap-3 btn-hover-scale bg-white-subtle"
                                            type="button" data-bs-toggle="dropdown">

                                            <div class="d-flex align-items-center gap-2 overflow-hidden">
                                                <span
                                                    class="d-flex align-items-center justify-content-center bg-opacity-10 rounded-circle flex-shrink-0"
                                                    :class="`bg-${statusColor}`" style="width: 28px; height: 28px;">
                                                    <span class="rounded-circle status-dot-pulse"
                                                        :class="[`bg-${statusColor}`, `pulse-${statusColor}`]"
                                                        style="width: 10px; height: 10px; box-shadow: 0 0 0 2px rgba(255,255,255,0.8);"></span>
                                                </span>
                                                <span class="fw-bold small text-truncate" x-text="statusLabel"></span>
                                            </div>
                                            <i class="bi bi-chevron-down text-muted ms-1 flex-shrink-0"
                                                style="font-size: 0.7rem;"></i>
                                        </button>

                                        <ul
                                            class="dropdown-menu dropdown-menu-end w-100 shadow-lg border-0 rounded-4 p-2 mt-2">
                                            <li class="px-3 py-2 text-uppercase text-muted fw-bold"
                                                style="font-size: 0.65rem;">
                                                Ubah Status Loket
                                            </li>
                                            <template x-for="(label, key) in statusMap" :key="key">
                                                <li>
                                                    <button type="button" @click="updateStatus(key)"
                                                        class="dropdown-item rounded-3 py-2 d-flex align-items-center justify-content-between"
                                                        :class="counter?.status === key ? 'active fw-bold' : ''">
                                                        <span x-text="label"></span>
                                                        <template x-if="counter?.status === key">
                                                            <i class="bi bi-check-lg text-white mb-2"></i>
                                                        </template>
                                                    </button>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <template x-for="item in statusCards">
                    <div class="col-6 col-md-3">
                        <div class="card rounded-4 border-0 p-3 text-center btn-hover-scale">

                            <div class="fs-4 mb-1" :class="item.color">
                                <i :class="`bi ${item.icon}`"></i>
                            </div>

                            <h4 class="fw-black mb-1" x-text="item.val || 0"></h4>

                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;"
                                x-text="item.label">
                            </small>
                        </div>
                    </div>
                </template>
            </div>

            <div class="row">
                <div class="col-lg-8">

                    <template x-if="!currentQueue">
                        <div class="card rounded-4 border-0 shadow-sm text-center py-5 d-flex flex-column justify-content-center"
                            style="min-height: 550px;">

                            <div class="mb-4 position-relative">
                                <i class="bi bi-megaphone-fill text-primary" style="font-size: 3rem;"></i>
                                <template x-if="nextQueue">
                                    <span
                                        class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-danger border border-white">
                                        New!
                                    </span>
                                </template>
                            </div>

                            <h3 class="fw-bold mb-2">Loket Tersedia</h3>

                            <template x-if="nextQueue">
                                <div>
                                    <p class="text-muted mb-4">Antrian berikutnya <span class="fw-bold"
                                            x-text="nextQueue.ticket_number"></span> siap dipanggil.</p>
                                    <div class="d-flex justify-content-center">
                                        <button type="button" @click="callQueue(nextQueue.id)"
                                            class="btn btn-primary btn-lg rounded-pill px-5 py-3 shadow-lg btn-pulse-primary fw-bold">
                                            <i class="bi bi-broadcast me-2"></i> Panggil Sekarang
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <template x-if="!nextQueue">
                                <div>
                                    <p class="text-muted mb-4 px-5">Belum ada antrian baru. Silahkan istirahat sejenak atau
                                        menunggu pengunjung.</p>
                                    <div>
                                        <button type="button" class="btn text-muted border rounded-pill px-4 py-2"
                                            disabled>
                                            <span class="spinner-border spinner-border-sm me-2"></span> Menunggu Data...
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="currentQueue">
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
                                    <span class="fw-bold" x-text="timerDisplay">00:00:00</span>
                                </span>
                            </div>

                            <div class="card-body text-center d-flex flex-column justify-content-center">
                                <div class="mb-5 position-relative">
                                    <p class="text-uppercase text-muted fw-bold text-spacing-wide mb-0 small">Nomor Antrian
                                    </p>
                                    <h1 class="mb-0"
                                        style="font-size: 7rem; line-height: 1; text-shadow: 0 4px 10px rgba(0,0,0,0.05);"
                                        x-text="currentQueue.ticket_number">--</h1>
                                </div>

                                <div class="row g-3 px-md-5 mb-5">
                                    <div class="col-6">
                                        <button type="button" @click="callQueue(currentQueue.id)"
                                            class="btn btn-white text-warning border border-warning w-100 py-3 rounded-4 btn-hover-scale">
                                            <i class="bi bi-arrow-counterclockwise fs-4 me-2"></i>
                                            <span class="fw-bold fs-5">Panggil Ulang</span>
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button type="button" @click="completeQueue(currentQueue.id)"
                                            class="btn btn-success w-100 py-3 rounded-4 btn-hover-scale">
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
                                    <button type="button" @click="skipQueue(currentQueue.id)"
                                        class="btn btn-outline-danger btn-sm rounded-pill px-4 hover-lift">
                                        <i class="bi bi-slash-circle me-1"></i> Lewati (Skip)
                                    </button>
                                    <button type="button"
                                        class="btn btn-outline-secondary btn-sm rounded-pill px-4 hover-lift">
                                        <i class="bi bi-arrow-left-right me-1"></i> Transfer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 rounded-4" x-data="{ tab: 'waiting' }"
                        style="min-height: 550px; max-height: 550px;">

                        <div class="card-header bg-transparent border-0 p-2">
                            <ul class="nav nav-pills nav-fill bg-light-subtle p-1 rounded-pill">
                                <li class="nav-item">
                                    <button type="button" class="nav-link rounded-pill fw-bold small py-2"
                                        :class="tab === 'waiting' ? 'active' : ''" @click="tab='waiting'">
                                        Menunggu (<span x-text="waitingList.length"></span>)
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link rounded-pill fw-bold small py-2"
                                        :class="tab === 'history' ? 'active' : ''" @click="tab='history'">
                                        Riwayat
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body p-0 custom-scroll" style="overflow-y: auto;">

                            <div x-show="tab === 'waiting'">
                                <div class="list-group list-group-flush px-2 pb-2">
                                    <template x-for="(q, index) in waitingList" :key="q.id">
                                        <div
                                            class="list-group-item border-0 rounded-3 mb-1 p-3 d-flex justify-content-between align-items-center bg-transparent hover-bg-light">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-white-subtle shadow-sm rounded-circle d-flex align-items-center justify-content-center me-3 text-primary fw-bold border"
                                                    style="width: 42px; height: 42px;" x-text="index + 1"></div>
                                                <div>
                                                    <h6 class="mb-0 fw-black" x-text="q.ticket_number"></h6>
                                                    <small class="text-muted" style="font-size: 0.75rem;">Walk-in
                                                        Customer</small>
                                                </div>
                                            </div>
                                            <button type="button" @click="directCallQueue(q.id)"
                                                class="btn btn-light btn-sm rounded-circle shadow-sm text-primary">
                                                <i class="bi bi-play-fill" data-bs-toggle="tooltip"
                                                    title="Panggil Langsung"></i>
                                            </button>
                                        </div>
                                    </template>

                                    <template x-if="waitingList.length === 0">
                                        <div>
                                            <div class="text-center mt-5 opacity-50">
                                                <i class="bi bi-inbox fs-1 me-2"></i>
                                            </div>
                                            <div class="text-center opacity-50">
                                                <small>Tidak ada antrian menunggu</small>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div x-show="tab === 'history'" style="display: none;">
                                <div class="list-group list-group-flush px-2 pb-2">
                                    <template x-for="h in historyList" :key="h.id">
                                        <div
                                            class="list-group-item border-0 rounded-3 mb-1 p-3 d-flex justify-content-between align-items-center bg-transparent">
                                            <span class="fw-bold text-secondary" x-text="h.ticket_number"></span>
                                            <span class="badge rounded-pill px-3"
                                                :class="h.status === 'completed' ? 'bg-success-subtle text-success' :
                                                    'bg-danger-subtle text-danger'"
                                                x-text="h.status === 'completed' ? 'Selesai' : 'Skipped'"></span>
                                        </div>
                                    </template>

                                    <template x-if="historyList.length === 0">
                                        <div>
                                            <div class="text-center mt-5 opacity-50">
                                                <i class="bi bi-inbox fs-1 me-2"></i>
                                            </div>
                                            <div class="text-center opacity-50">
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
        </div>
    </section>
@endsection

@section('js')
    <script src="{{ asset('theme/dashboard/assets/extensions/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        function counterDashboard() {
            return {
                isLoading: true,
                errorMessage: '',
                user: null,
                counter: null,
                service: null,
                stats: {
                    waiting: 0,
                    completed: 0,
                    skipped: 0,
                    avg_time: '0m'
                },
                currentQueue: null,
                nextQueue: null,
                waitingList: [],
                historyList: [],
                statusMap: {
                    'open': 'Buka',
                    'break': 'Istirahat',
                    'closed': 'Tutup'
                },
                clockTime: '--:--',
                clockDate: '...',
                timerDisplay: '00:00:00',
                timerInterval: null,
                statusCards: [{
                        val: this.stats?.waiting,
                        icon: 'bi-hourglass-split',
                        color: 'text-warning',
                        label: 'Menunggu'
                    },
                    {
                        val: this.stats?.completed,
                        icon: 'bi-check-circle-fill',
                        color: 'text-success',
                        label: 'Selesai'
                    },
                    {
                        val: this.stats?.skipped,
                        icon: 'bi-slash-circle-fill',
                        color: 'text-danger',
                        label: 'Skipped'
                    },
                    {
                        val: this.stats?.avg_time,
                        icon: 'bi-clock-history',
                        color: 'text-primary',
                        label: 'Avg Time'
                    }
                ],
                csrfToken: document.querySelector('meta[name="csrf-token"]').content,

                async init() {
                    setInterval(() => this.updateClock(), 1000);
                    this.updateClock();

                    await this.fetchData();

                    if (this.counter && this.counter.service_id) {
                        this.initBroadcastListener();
                    }
                },

                async fetchData(showLoading = false) {

                    if (showLoading) this.isLoading = true;

                    try {
                        const url = "{{ route('ajax.dashboard.staff.current-queue') }}";
                        const res = await fetch(url, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                            }
                        });
                        const data = await res.json();

                        this.user = data.user;
                        this.counter = data.counter;
                        this.service = data.service;
                        this.stats = data.stats;
                        this.currentQueue = data.currentQueue;
                        this.nextQueue = data.nextQueue;
                        this.waitingList = data.waitingList;
                        this.historyList = data.historyList;

                        this.checkAndStartTimer(data.serverTimeMs);

                    } catch (error) {
                        console.error("Fetch Error:", error);
                    } finally {
                        setTimeout(() => {
                            this.isLoading = false;
                        }, 300);
                    }
                },

                initBroadcastListener() {
                    Echo.private(`service.${this.counter.service_id}`)
                        .listen(".got-queue", (event) => {
                            this.fetchData(false);
                        });
                },

                async sendAction(url, method = 'PUT', body = {}) {
                    this.isLoading = true;

                    try {
                        const res = await fetch(url, {
                            method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify(body)
                        });

                        const data = await res.json();

                        if (!res.ok) {
                            throw new Error(data.message || 'Terjadi kesalahan pada server');
                        }

                        this.fetchData(false);
                    } catch (e) {
                        console.error("Action Error:", e);
                        this.isLoading = false;

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: e.message,
                            confirmButtonText: 'Oke',
                            confirmButtonColor: '#dc3545',
                            timer: 5000
                        });
                    }
                },

                callQueue(id) {
                    const url = "{{ route('ajax.queues.call', ':ID') }}"
                        .replace(':ID', id);

                    this.sendAction(url);
                },

                directCallQueue(id) {
                    const url = "{{ route('ajax.queues.direct-call', ':ID') }}"
                        .replace(':ID', id);

                    this.sendAction(url);
                },

                completeQueue(id) {
                    const url = "{{ route('ajax.queues.complete', ':ID') }}"
                        .replace(':ID', id);
                    this.sendAction(url);
                },

                skipQueue(id) {
                    const url = "{{ route('ajax.queues.skip', ':ID') }}"
                        .replace(':ID', id);
                    this.sendAction(url);
                },

                updateStatus(status) {
                    const url = "{{ route('ajax.set-status-counter', ':ID') }}"
                        .replace(':ID', this.counter.id);

                    this.sendAction(url, 'PUT', {
                        status
                    });
                },

                get statusColor() {
                    const colors = {
                        'open': 'success',
                        'break': 'warning',
                        'closed': 'danger'
                    };
                    return colors[this.counter?.status] || 'secondary';
                },

                get statusLabel() {
                    return this.statusMap[this.counter?.status] || 'Offline';
                },

                checkAndStartTimer(serverTimeMs) {
                    if (this.timerInterval) clearInterval(this.timerInterval);

                    if (this.currentQueue) {
                        const storageKey = `q_start_${this.currentQueue.id}`;
                        let startTime = localStorage.getItem(storageKey);

                        if (!startTime) {
                            startTime = Date.now();
                            localStorage.setItem(storageKey, startTime);
                        }

                        this.timerInterval = setInterval(() => {
                            const diff = Math.floor((Date.now() - startTime) / 1000);
                            const h = Math.floor(diff / 3600).toString().padStart(2, '0');
                            const m = Math.floor((diff % 3600) / 60).toString().padStart(2,
                                '0');
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
