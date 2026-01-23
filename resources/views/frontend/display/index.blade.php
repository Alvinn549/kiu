@extends('frontend.layouts.main')

@section('title', 'Antrian Display')

@section('css')
    {{-- <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;700&family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet"> --}}
    <style>
        :root {
            --bg-dark: #020617;
            --panel-bg: #0f172a;
            --accent: #3b82f6;
            --accent-gradient: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            --highlight: #facc15;
            --text-light: #f8fafc;
            --text-dim: #94a3b8;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-light);
            height: 100vh;
            overflow: hidden;
            margin: 0;
        }

        .broadcast-grid {
            display: grid;
            grid-template-columns: 1fr 500px;
            grid-template-rows: 80px 1fr;
            height: 100vh;
            width: 100vw;
        }

        .header-section {
            grid-column: 1 / -1;
            background: var(--panel-bg);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            z-index: 10;
        }

        .brand-wrapper {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .company-name {
            font-size: 1.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: white;
        }

        .clock-display {
            font-family: 'Oswald', sans-serif;
            font-size: 2.5rem;
            color: var(--highlight);
            font-weight: 500;
            text-shadow: 0 0 10px rgba(250, 204, 21, 0.3);
        }

        .merged-left-column {
            grid-row: 2 / 3;
            grid-column: 1 / 2;
            padding: 20px;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .unified-media-card {
            background: #000;
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
            height: 100%;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .media-content {
            flex-grow: 1;
            position: relative;
            background: #000;
            overflow: hidden;
        }

        .media-content video,
        .media-content img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card-footer-ticker {
            height: 60px;
            background: var(--panel-bg);
            display: flex;
            align-items: center;
        }

        .ticker-label {
            background: var(--accent);
            color: #fff;
            font-weight: 700;
            padding: 0 40px;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .ticker-text {
            color: #fff;
            font-size: 1.25rem;
            font-weight: 400;
            padding-left: 20px;
            letter-spacing: 0.5px;
        }

        .info-sidebar {
            grid-row: 2 / 3;
            grid-column: 2 / 3;
            background: var(--panel-bg);
            border-left: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            flex-direction: column;
            padding: 20px;
            gap: 20px;
        }

        .active-ticket-box {
            background: var(--accent-gradient);
            border-radius: 24px;
            padding: 40px 20px;
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-shadow: 0 20px 40px -10px rgba(59, 130, 246, 0.5);
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .status-icon {
            font-size: 4rem;
            margin-bottom: 10px;
            animation: pulseIcon 2s infinite;
        }

        .ticket-label {
            font-size: 1.25rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 5px;
        }

        .ticket-number {
            font-family: 'Oswald', sans-serif;
            font-size: 8rem;
            font-weight: 700;
            line-height: 1;
            color: #fff;
            margin: 10px 0;
            text-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .counter-badge {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #fff;
            font-size: 1.75rem;
            font-weight: 700;
            padding: 15px 30px;
            border-radius: 50px;
            margin-top: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .history-section {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 16px;
            padding: 20px;
            flex-shrink: 0;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .history-header {
            color: var(--text-dim);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .history-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 1.25rem;
        }

        .history-item:last-child {
            border-bottom: none;
        }

        .h-num {
            font-family: 'Oswald', sans-serif;
            color: var(--text-light);
        }

        .h-counter {
            color: var(--highlight);
            font-weight: 500;
            font-size: 1rem;
        }

        .flash-overlay {
            position: fixed;
            inset: 0;
            background: var(--accent);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            animation: flashBg 0.5s infinite alternate;
        }

        @keyframes pulseIcon {
            0% {
                transform: scale(1);
                opacity: 0.3;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.6;
            }

            100% {
                transform: scale(1);
                opacity: 0.3;
            }
        }

        @keyframes flashBg {
            from {
                background: #3b82f6;
            }

            to {
                background: #1d4ed8;
            }
        }
    </style>
@endsection

@section('content')

    @php
        $settings = (object) [
            'media_type' => 'iamge',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
            'slideshow_images' => [
                'https://images.unsplash.com/photo-1589829085413-56de8ae18c73?q=80&w=2000&auto=format&fit=crop',
                'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2000&auto=format&fit=crop',
                'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?q=80&w=2070&auto=format&fit=crop',
                'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=2072&auto=format&fit=crop',
            ],
            'company_name' => 'PACITAN',
            'running_text' =>
                'PENGUMUMAN: Harap menjaga kebersihan ruang tunggu. Dilarang merokok di area rumah sakit.',
        ];

        $currentTicket = (object) ['number' => 'A-012', 'counter' => 'POLI UMUM 1'];
        $history = collect([
            (object) ['number' => 'B-005', 'counter' => 'FARMASI'],
            (object) ['number' => 'A-011', 'counter' => 'POLI GIGI'],
            (object) ['number' => 'C-003', 'counter' => 'ADMIN'],
        ]);
    @endphp

    <div x-data="broadcastSystem()" x-init="initSystem()" class="broadcast-grid">

        <div class="header-section">
            <div class="brand-wrapper">
                <div
                    style="background: var(--accent); color: white; width: 45px; height: 45px; display: grid; place-items: center; border-radius: 8px;">
                    <i class="fas fa-hospital fs-4"></i>
                </div>
                <div class="company-name">{{ $settings->company_name }}</div>
            </div>
            <div class="clock-display" x-text="currentTime">--:--</div>
        </div>

        <div class="merged-left-column">
            <div class="unified-media-card">

                <div class="media-content">
                    @if ($settings->media_type === 'video')
                        <video autoplay loop muted playsinline>
                            <source src="{{ $settings->video_url }}" type="video/mp4">
                        </video>
                    @else
                        <div id="broadcastSlide" class="carousel slide carousel-fade h-100" data-bs-ride="carousel"
                            data-bs-interval="8000">
                            <div class="carousel-inner h-100">
                                @foreach ($settings->slideshow_images as $index => $image)
                                    <div class="carousel-item h-100 {{ $index === 0 ? 'active' : '' }}">
                                        <img src="{{ $image }}" alt="Slide">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="card-footer-ticker">
                    <div class="ticker-label">
                        <i class="fas fa-info-circle me-2"></i> INFO
                    </div>
                    <div style="flex:1; overflow:hidden; margin-top: 7px;">
                        <marquee class="ticker-text" scrollamount="10">
                            {{ $settings->running_text }}
                        </marquee>
                    </div>
                </div>

            </div>
        </div>

        <div class="info-sidebar">

            <div class="active-ticket-box">
                <i class="fas fa-volume-up status-icon"></i>
                <div class="ticket-label">Nomor Antrian</div>
                <div class="ticket-number" x-text="currentNumber">{{ $currentTicket->number }}</div>
                <div class="counter-badge">
                    <i class="fas fa-map-marker-alt text-warning"></i>
                    <span x-text="currentCounter">{{ $currentTicket->counter }}</span>
                </div>
            </div>

            <div class="history-section">
                <div class="history-header">
                    <i class="fas fa-clock"></i> Riwayat Panggilan
                </div>
                <div class="d-flex flex-column">
                    @foreach ($history as $h)
                        <div class="history-item">
                            <span class="h-num">{{ $h->number }}</span>
                            <span class="h-counter">{{ $h->counter }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div x-show="isCalling" style="display: none;" class="flash-overlay"
            x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <div style="font-size: 2.5rem; text-transform: uppercase; opacity: 0.9; margin-bottom: 20px;">Panggilan Nomor
            </div>
            <div style="font-family: 'Oswald', sans-serif; font-size: 14rem; font-weight: 700; line-height: 1;"
                x-text="tempNumber"></div>

            <div
                style="font-size: 3rem; font-weight: 700; background: white; color: var(--accent); padding: 15px 60px; border-radius: 100px; margin-top: 40px; box-shadow: 0 20px 50px rgba(0,0,0,0.5);">
                <i class="fas fa-arrow-right-circle me-3"></i>
                <span x-text="tempCounter"></span>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function broadcastSystem() {
            return {
                isCalling: false,
                currentNumber: '{{ $currentTicket->number }}',
                currentCounter: '{{ $currentTicket->counter }}',
                tempNumber: '',
                tempCounter: '',
                currentTime: '',

                initSystem() {
                    this.updateTime();
                    setInterval(() => this.updateTime(), 1000);
                    window.testCall = (num, counter) => this.call(num, counter);
                    window.addEventListener('keydown', (e) => {
                        if (e.code === 'Space') this.call('A-999', 'POLI SYARAF');
                    });
                },

                updateTime() {
                    const now = new Date();
                    this.currentTime = now.toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                },

                call(number, counter) {
                    if (this.isCalling) return;
                    this.tempNumber = number;
                    this.tempCounter = counter;
                    this.isCalling = true;
                    setTimeout(() => {
                        this.isCalling = false;
                        this.currentNumber = number;
                        this.currentCounter = counter;
                    }, 5000);
                }
            }
        }
    </script>
@endsection
