@extends('dashboard.layouts.main')

@section('title', 'Detail Pemanggil')

@section('page-heading')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Detail Pemanggil</h3>
                <p class="text-subtitle text-muted">Informasi detail data pemanggil (Loket)</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('counters.index') }}">Pemanggil</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail Pemanggil</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <section class="section">
        <div class="mb-3">
            <a href="{{ route('counters.index') }}" class="btn rounded-pill btn-warning">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Data Pemanggil</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <h6 class="text-muted mb-1">Nama Pemanggil / Loket</h6>
                            <p class="fs-5 fw-bold text-dark">{{ $counter->name }}</p>
                        </div>

                        <div class="mb-4">
                            <h6 class="text-muted mb-1">Layanan Terkait</h6>
                            <p class="fs-5 text-dark">
                                @if ($counter->service)
                                    <span class="fw-bold">{{ $counter->service->name }}</span>
                                    <span class="badge bg-light text-dark border ms-2 font-monospace">
                                        {{ $counter->service->code }}
                                    </span>
                                @else
                                    <span class="text-danger fst-italic">Tidak ada layanan</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-4">
                            <h6 class="text-muted mb-1">Status Saat Ini</h6>
                            <div>
                                @if ($counter->status == 'open')
                                    <span class="badge bg-success fs-6">
                                        <i class="bi bi-door-open me-1"></i> Open (Buka)
                                    </span>
                                @elseif($counter->status == 'break')
                                    <span class="badge bg-warning text-dark fs-6">
                                        <i class="bi bi-pause-circle me-1"></i> Break (Istirahat)
                                    </span>
                                @else
                                    <span class="badge bg-danger fs-6">
                                        <i class="bi bi-door-closed me-1"></i> Closed (Tutup)
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
