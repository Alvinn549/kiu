@extends('dashboard.layouts.main')

@section('title', 'Edit Layanan & Operator')

@section('content')
    <section class="section">
        <div class="mb-3">
            <a href="{{ route('services.index') }}" class="btn rounded-pill btn-warning">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <form action="{{ route('services.update', $service->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card shadow-sm">
                <div class="card-header bg-light-primary">
                    <h5 class="card-title">1. Data Layanan</h5>
                </div>
                <div class="card-body mt-3">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Nama Layanan</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $service->name) }}"
                                    placeholder="Contoh: Cetak KTP">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="code" class="form-label">Kode Layanan</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror"
                                    id="code" name="code" value="{{ old('code', $service->code) }}"
                                    placeholder="Contoh: KTP-01">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="opening_time" class="form-label">Jam Buka</label>
                                <input type="time" class="form-control @error('opening_time') is-invalid @enderror"
                                    id="opening_time" name="opening_time"
                                    value="{{ old('opening_time', $service->opening_time) }}">
                                @error('opening_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="closing_time" class="form-label">Jam Tutup</label>
                                <input type="time" class="form-control @error('closing_time') is-invalid @enderror"
                                    id="closing_time" name="closing_time"
                                    value="{{ old('closing_time', $service->closing_time) }}">
                                @error('closing_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="max_queue_per_day" class="form-label">Maks. Antrean / Hari</label>
                                <input type="number" class="form-control @error('max_queue_per_day') is-invalid @enderror"
                                    id="max_queue_per_day" name="max_queue_per_day"
                                    value="{{ old('max_queue_per_day', $service->max_queue_per_day) }}"
                                    placeholder="Contoh: 100">
                                @error('max_queue_per_day')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="icon" class="form-label">Ikon Layanan (Gambar)</label>
                                <input type="file" class="form-control @error('icon') is-invalid @enderror"
                                    id="icon" name="icon" accept="image/*">
                                <small class="text-muted">Format: JPG, PNG, WEBP. Maks: 2MB</small>
                                @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-light-primary">
                    <h5 class="card-title">2. Operator</h5>
                </div>
                <div class="card-body mt-3">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                    id="username" name="username"
                                    value="{{ old('username', $service->operator->name ?? '') }}"
                                    placeholder="Nama Operator">
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email"
                                    value="{{ old('email', $service->operator->email ?? '') }}"
                                    placeholder="email@example.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" placeholder="Kosongkan jika tidak ingin ganti">
                                <small class="text-muted">Biarkan kosong jika tidak ingin mengubah password.</small>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn rounded-pill btn-primary">
                        <i class="bi bi-save me-1"></i> Perbarui Data
                    </button>
                </div>
            </div>
        </form>
    </section>
@endsection
