@extends('dashboard.layouts.main')

@section('title', 'Edit Pemanggil')

@section('page-heading')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Edit Pemanggil</h3>
                <p class="text-subtitle text-muted">Form edit data pemanggil (Loket)</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('counters.index') }}">Pemanggil</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Pemanggil</li>
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

        <form action="{{ route('counters.update', $counter) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Data Pemanggil</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Nama Pemanggil / Loket</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $counter->name) }}" placeholder="Contoh: Loket 1">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="service_id" class="form-label">Layanan</label>
                                <select class="form-select @error('service_id') is-invalid @enderror" id="service_id"
                                    name="service_id">
                                    <option value="" selected disabled>-- Pilih Layanan --</option>
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}"
                                            {{ old('service_id', $counter->service_id) == $service->id ? 'selected' : '' }}>
                                            {{ $service->name }} ({{ $service->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('service_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="status" class="form-label">Status Awal</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status"
                                    name="status">
                                    @foreach ($counter_status as $status => $label)
                                        <option value="{{ $status }}"
                                            {{ old('status', $counter->status) == $status ? 'selected' : '' }}>
                                            {{ Str::ucfirst($label) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn rounded-pill btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan Pemanggil
                    </button>
                </div>
            </div>
        </form>
    </section>
@endsection
