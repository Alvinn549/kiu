@extends('dashboard.layouts.main')

@section('title', 'Detail Pengguna')

@section('page-heading')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Detail Pengguna</h3>
                <p class="text-subtitle text-muted">Informasi detail data pengguna</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Pengguna</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail Pengguna</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <section class="section">
        <div class="mb-3">
            <a href="{{ route('users.index') }}" class="btn rounded-pill btn-warning">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Data Pengguna</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <h6 class="text-muted mb-1">Nama Lengkap</h6>
                            <p class="fs-5 fw-bold text-dark">{{ $user->name }}</p>
                        </div>

                        <div class="mb-4">
                            <h6 class="text-muted mb-1">Username</h6>
                            <p class="fs-5 text-dark font-monospace bg-light d-inline-block px-2 rounded">
                                {{ $user->username }}
                            </p>
                        </div>

                        <div class="mb-4">
                            <h6 class="text-muted mb-1">Email</h6>
                            <p class="fs-5 text-dark">
                                <a href="mailto:{{ $user->email }}" class="text-decoration-none text-dark">
                                    {{ $user->email }}
                                </a>
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-4">
                            <h6 class="text-muted mb-1">Role / Jabatan</h6>
                            <p class="fs-5 fw-bold text-primary">
                                {{ $user->role->name ?? 'Tidak ada role' }}
                            </p>
                        </div>

                        <div class="mb-4">
                            <h6 class="text-muted mb-1">Loket / Counter</h6>
                            <p class="fs-5 fw-bold text-dark mb-1">
                                {{ $user->counter->name ?? '-' }}
                            </p>
                            @if (!$user->counter)
                                <small class="text-muted fst-italic">
                                    <i class="bi bi-info-circle me-1"></i> User ini tidak terikat dengan loket manapun
                                    (Administrator).
                                </small>
                            @endif
                        </div>

                        <div class="mb-4">
                            <h6 class="text-muted mb-1">Terdaftar Pada</h6>
                            <p class="text-dark">
                                {{ $user->created_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
