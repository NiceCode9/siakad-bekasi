@extends('layouts.app')

@section('title', 'Edit Buku Induk Siswa')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center mt-2">
                        <h4 class="card-title mb-0">Edit Buku Induk Siswa</h4>
                        <a href="{{ route('buku-induk.show', $siswa->id) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        @include('user-data.buku-induk.form', [
                            'method' => 'PUT',
                            'action' => route('buku-induk.update', $siswa->id),
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
