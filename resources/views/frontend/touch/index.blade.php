@extends('frontend.layouts.main')

@section('title', 'Ambil Antrian')

@section('css')
    <style>
        :root {
            --card-width: 480px;
            --card-gap: 30px;
            --ease-elastic: cubic-bezier(0.34, 1.56, 0.64, 1);
            --ease-smooth: cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .kiosk-wrapper {
            background-image: url('https://images.unsplash.com/photo-1557683316-973673baf926?q=80&w=2029&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            position: relative;
        }

        .kiosk-overlay {
            background: radial-gradient(circle at center, rgba(15, 23, 42, 0.6) 0%, rgba(15, 23, 42, 0.85) 100%);
            height: 100%;
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding-top: 2rem;
            padding-bottom: 1.5rem;
            backdrop-filter: blur(2px);
        }

        .slider-viewport {
            width: 100%;
            overflow-x: auto;
            padding: 20px 5vw;
            -ms-overflow-style: none;
            mask-image: linear-gradient(to right, transparent, black 5%, black 95%, transparent);
        }

        .slider-viewport::-webkit-scrollbar {
            display: none;
        }

        .cards-grid {
            display: grid;
            grid-template-rows: repeat(2, 1fr);
            grid-auto-flow: column;
            grid-auto-columns: var(--card-width);
            gap: var(--card-gap);
            height: 60vh;
            align-content: center;
            scroll-snap-type: x mandatory;
        }

        .service-card-form {
            height: 100%;
            opacity: 0;
            animation: fadeInUp 0.8s var(--ease-smooth) forwards;
            scroll-snap-align: center;
        }

        .service-card-form:nth-child(1) {
            animation-delay: 0.1s;
        }

        .service-card-form:nth-child(2) {
            animation-delay: 0.2s;
        }

        .service-card-form:nth-child(3) {
            animation-delay: 0.3s;
        }

        .service-card-form:nth-child(4) {
            animation-delay: 0.4s;
        }

        .service-card-form:nth-child(5) {
            animation-delay: 0.5s;
        }

        .service-card-form:nth-child(6) {
            animation-delay: 0.6s;
        }

        .service-card-btn {
            border: none;
            background: none;
            padding: 0;
            width: 100%;
            height: 100%;
            text-align: left;
            perspective: 1000px;
        }

        .service-tile {
            background: rgba(255, 255, 255, 0.98);
            height: 100%;
            width: 100%;
            border-radius: 28px;
            padding: 2rem;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.4s var(--ease-smooth);
            border-left: 10px solid transparent;
            overflow: hidden;
        }

        .service-card-btn:hover .service-tile {
            transform: translateY(-12px) scale(1.03);
            background: #ffffff;
        }

        .service-card-btn:active .service-tile {
            transform: scale(0.97) !important;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1) !important;
            transition-duration: 0.1s;
        }

        .service-card-btn:disabled .service-tile {
            opacity: 0.6;
            transform: scale(0.95);
            filter: grayscale(100%);
        }

        .tile-img-box {
            position: absolute;
            right: -20px;
            bottom: -35px;
            width: 140px;
            height: 140px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.5;
            transform: rotate(-20deg);
            transition: all 0.5s var(--ease-elastic);
        }

        .tile-img-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .service-card-btn:hover .tile-img-box {
            opacity: 0.8;
            transform: rotate(0deg) scale(1.1) translate(-10px, -10px);
        }

        .tile-content h3 {
            font-size: 1.8rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.75rem;
        }

        .tile-content p {
            color: #64748b;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .accent-blue {
            border-left-color: #3b82f6;
        }

        .accent-blue h3 {
            color: #3b82f6;
        }

        .accent-teal {
            border-left-color: #14b8a6;
        }

        .accent-teal h3 {
            color: #0d9488;
        }

        .accent-purple {
            border-left-color: #a855f7;
        }

        .accent-purple h3 {
            color: #9333ea;
        }

        .accent-rose {
            border-left-color: #f43f5e;
        }

        .accent-rose h3 {
            color: #e11d48;
        }

        .accent-orange {
            border-left-color: #f97316;
        }

        .accent-orange h3 {
            color: #ea580c;
        }

        .scroll-btn {
            background: rgba(255, 255, 255, 0.15);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: white;
            width: 65px;
            height: 65px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            backdrop-filter: blur(10px);
            transition: all 0.2s var(--ease-smooth);
        }

        .scroll-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .scroll-btn:active {
            transform: scale(0.9);
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

        .nav-footer-hidden {
            opacity: 0;
            pointer-events: none;
        }

        .nav-footer-visible {
            opacity: 1;
            pointer-events: all;
        }
    </style>
@endsection

@section('content')
    <div class="kiosk-wrapper" x-data="kioskApp()" x-init="initApp()">
        <div class="kiosk-overlay">
            <div class="container-fluid px-5" style="animation: fadeInUp 1s ease-out;">
                <div class="d-flex justify-content-between align-items-end pb-4">
                    <div class="text-white">
                        <h1 class="fw-black display-5 mb-1" style="font-weight: 900; letter-spacing: -1px;">
                            Layanan Antrian
                        </h1>
                        <p class="lead opacity-75 mb-0 fs-4">Silakan sentuh layanan yang Anda butuhkan</p>
                    </div>

                    <div class="text-end text-white">
                        <div class="display-4 fw-bold font-monospace mb-0" style="line-height: 1;" x-text="time">
                            --:--
                        </div>
                        <small class="text-uppercase letter-spacing-2 opacity-75 fw-bold" x-text="date">
                            Memuat waktu...
                        </small>
                    </div>
                </div>
                <hr class="border-white opacity-25 mt-0">
            </div>

            <div class="slider-viewport" id="scrollContainer" @scroll.debounce="checkOverflow">

                <template x-if="isLoading">
                    <div class="col-span-full text-white text-center p-5">
                        <i class="fas fa-spinner fa-spin fa-3x mb-3"></i>
                        <p>Memuat Layanan...</p>
                    </div>
                </template>

                <template x-if="!isLoading && services.length > 0">
                    <div class="cards-grid">
                        <template x-for="(service, index) in services" :key="service.id">
                            <div class="service-card-wrapper h-100">
                                <button type="button" class="service-card-btn submit-btn"
                                    @click="getQueueNumber(service.id)">

                                    <div class="service-tile" :class="getColorClass(index)">
                                        <div class="tile-content z-2">
                                            <h3 x-text="service.name"></h3>

                                            <template x-if="service.avg_wait_time">
                                                <p>
                                                    <i class="fas fa-hourglass-half me-2 opacity-75"></i>
                                                    <span>± <span x-text="service.avg_wait_time"></span> Menit</span>
                                                </p>
                                            </template>

                                            <template x-if="!service.avg_wait_time">
                                                <p><i class="fas fa-check-circle me-2 opacity-75"></i>Layanan Tersedia</p>
                                            </template>
                                        </div>

                                        <div class="tile-img-box z-1">
                                            <img :src="service.image_url ||
                                                'https://cdn-icons-png.flaticon.com/512/2824/2824447.png'"
                                                :alt="service.name">
                                        </div>
                                    </div>
                                </button>
                            </div>

                        </template>
                    </div>
                </template>

                <template x-if="!isLoading && services.length === 0">
                    <div class="col-span-full text-white text-center p-5">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                        <p>Tidak ada layanan yang tersedia saat ini.</p>
                    </div>
                </template>

            </div>

            <div class="container-fluid px-5 nav-footer-hidden"
                :class="{ 'nav-footer-visible': hasOverflow, 'nav-footer-hidden': !hasOverflow }"
                style="transition: opacity 0.5s ease;">
                <div
                    class="d-flex justify-content-center align-items-center gap-4 opacity-75 hover-opacity-100 transition-all">
                    <button class="scroll-btn shadow-sm" @click="scrollGrid('left')">
                        <i class="fas fa-chevron-left fa-lg"></i>
                    </button>
                    <span class="text-white fw-bold small text-uppercase letter-spacing-2 mx-3">
                        <i class="bi bi-arrows-move me-2"></i> Geser untuk melihat lainnya
                    </span>
                    <button class="scroll-btn shadow-sm" @click="scrollGrid('right')">
                        <i class="fas fa-chevron-right fa-lg"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="loading-overlay" :class="{ 'active': isSubmitting }">
            <div class="spinner mb-4"></div>
            <h2 class="text-white fw-bold">Memproses...</h2>
        </div>

        <div class="loading-overlay" :class="{ 'active': showSuccess }"
            style="background: rgba(40, 167, 69, 0.95) !important;">
            <i class="fas fa-print fa-4x text-white mb-4"></i>
            <h2 class="text-white fw-bold">Tiket Dicetak!</h2>
            <h1 class="display-1 text-white fw-black my-3" x-text="ticketResult"></h1>
            <p class="text-white lead">Silakan ambil tiket Anda.</p>
        </div>

    </div>
@endsection

@section('js')
    <script>
        function kioskApp() {
            return {
                services: [],
                isLoading: true,
                isSubmitting: false,
                showSuccess: false,
                ticketResult: '',
                hasOverflow: false,
                time: '--:--',
                date: 'Memuat...',
                colors: ['accent-blue', 'accent-teal', 'accent-purple', 'accent-rose', 'accent-orange'],

                initApp() {
                    this.updateTime();
                    setInterval(() => this.updateTime(), 1000);

                    this.fetchServices();

                    Echo.channel('public-channel')
                        .listen('.QueueUpdated', (e) => {
                            console.log('WebSocket Event Received:', e);
                            this.refreshData();
                        });

                    window.addEventListener('resize', () => this.checkOverflow());
                },

                async fetchServices() {
                    try {
                        let url = "{{ route('ajax.touch') }}";
                        let response = await fetch(url);
                        let data = await response.json();
                        this.services = data;
                        this.$nextTick(() => this.checkOverflow());
                    } catch (error) {
                        console.error('Error fetching services:', error);
                    } finally {
                        this.isLoading = false;
                    }
                },

                refreshData() {
                    this.fetchServices();
                },

                async getQueueNumber(serviceId) {
                    if (this.isSubmitting) return;

                    this.isSubmitting = true;

                    var actionUrlTemplate = "{{ route('ajax.touch.get-queue-number', ['service' => ':ID']) }}";
                    const actionUrl = actionUrlTemplate.replace(':ID', serviceId);

                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    try {
                        const response = await fetch(actionUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        });

                        const result = await response.json();

                        if (!response.ok) {
                            throw new Error(result.message || 'Gagal mengambil antrean');
                        }

                        this.isSubmitting = false;
                        this.ticketResult = result.ticket_number;
                        this.showSuccess = true;

                        setTimeout(() => {
                            this.showSuccess = false;
                            this.ticketResult = '';
                        }, 3000);

                    } catch (error) {
                        console.error('Submission Error:', error);
                        alert('Terjadi kesalahan: ' + error.message);
                        this.isSubmitting = false;
                    }
                },

                getColorClass(index) {
                    return this.colors[index % this.colors.length];
                },

                updateTime() {
                    const now = new Date();
                    this.time = now.toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    this.date = now.toLocaleDateString('id-ID', {
                        weekday: 'long',
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                },

                checkOverflow() {
                    const container = document.getElementById('scrollContainer');
                    if (container) {
                        this.hasOverflow = container.scrollWidth > container.clientWidth + 10;
                    }
                },

                scrollGrid(direction) {
                    const container = document.getElementById('scrollContainer');
                    const root = getComputedStyle(document.documentElement);
                    const cardWidth = parseInt(root.getPropertyValue('--card-width')) || 300;
                    const cardGap = parseInt(root.getPropertyValue('--card-gap')) || 20;
                    const scrollAmount = cardWidth + cardGap;

                    container.scrollBy({
                        left: direction === 'left' ? -scrollAmount : scrollAmount,
                        behavior: 'smooth'
                    });
                }
            }
        }
    </script>
@endsection
