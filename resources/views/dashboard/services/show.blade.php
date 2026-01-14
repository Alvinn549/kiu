@extends('dashboard.layouts.main')

@section('title', 'Detail Layanan')

@section('page-heading')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Detail Layanan</h3>
                <p class="text-subtitle text-muted">Informasi detail layanan</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('services.index') }}">Layanan</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail Layanan</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <section class="section">
        <div class="mb-3">
            <a href="{{ route('services.index') }}" class="btn rounded-pill btn-warning">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Data Layanan</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <h6 class="text-muted mb-1">Nama Layanan</h6>
                            <p class="fs-5 fw-bold text-dark">{{ $service->name }}</p>
                        </div>

                        <div class="mb-4">
                            <h6 class="text-muted mb-1">Kode Layanan</h6>
                            <p class="fs-5 text-dark font-monospace bg-light d-inline-block px-2 rounded">
                                {{ $service->code }}
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-4">
                            <h6 class="text-muted mb-1">Status</h6>
                            <div>
                                @if ($service->is_active)
                                    <span class="badge bg-success fs-6">
                                        <i class="bi bi-check-circle me-1"></i> Aktif
                                    </span>
                                @else
                                    <span class="badge bg-secondary fs-6">
                                        <i class="bi bi-x-circle me-1"></i> Tidak Aktif
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-4">
                                <h6 class="text-muted mb-1">Rata-rata waktu tunggu</h6>
                                <p class="text-dark">
                                    {{ $service->avg_wait_time ?? '-' }} menit
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
