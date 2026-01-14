@extends('dashboard.layouts.main')

@section('title', 'Pengguna')

@section('css')
    <link rel="stylesheet" href="{{ asset('theme/dashboard/assets/extensions/simple-datatables/style.css') }}">
    <link rel="stylesheet" crossorigin href="{{ asset('theme/dashboard/assets/compiled/css/table-datatable.css') }}">
@endsection

@section('page-heading')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Pengguna</h3>
                <p class="text-subtitle text-muted">List data pengguna</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pengguna</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <section class="section">
        <div class="mb-3">
            <a href="{{ route('users.create') }}" class="btn rounded-pill btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Tambah Pengguna
            </a>
        </div>
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    Data Pengguna
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Pemanggil</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->role->name }}</td>
                                <td>{{ $user->counter?->name }}</td>
                                <td>
                                    <a href="{{ route('users.edit', $user) }}" class="btn rounded-pill btn-sm btn-primary">
                                        <i class="bi bi-pencil-square me-1"></i> Edit
                                    </a>

                                    <a href="{{ route('users.show', $user) }}" class="btn rounded-pill btn-sm btn-info">
                                        <i class="bi bi-eye me-1"></i> Detail
                                    </a>

                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn rounded-pill btn-sm btn-danger"
                                            onclick="return confirm('Yakin ingin menghapus pengguna ini?')">
                                            <i class="bi bi-trash me-1"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script src="{{ asset('theme/dashboard/assets/extensions/simple-datatables/umd/simple-datatables.js') }}"></script>
    <script src="{{ asset('theme/dashboard/assets/static/js/pages/simple-datatables.js') }}"></script>
@endsection
