@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1>Histori Pelanggaran: {{ $siswa->nama_lengkap }}</h1>
                <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                    <ol class="breadcrumb pt-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pelanggaran-siswa.index') }}">Pelanggaran</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pelanggaran-siswa.resume') }}">Rekap</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $siswa->nis }}</li>
                    </ol>
                </nav>
                <div class="separator mb-5"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Profil Siswa</h5>
                        <div class="text-center mb-3">
                            <img src="{{ $siswa->foto ? asset('storage/' . $siswa->foto) : asset('assets/img/profiles/l-1.jpg') }}"
                                alt="Profile" class="img-thumbnail border-0 rounded-circle mb-4 list-thumbnail">
                            <p class="list-item-heading mb-1">{{ $siswa->nama_lengkap }}</p>
                            <p class="text-muted text-small">{{ $siswa->nis }} / {{ $siswa->nisn }}</p>
                        </div>
                        <p class="text-muted text-small mb-2">Kelas Aktif</p>
                        <p class="mb-3">{{ $siswa->kelas->first()->nama ?? '-' }}</p>

                        <p class="text-muted text-small mb-2">Total Poin</p>
                        <p class="mb-3">
                            <span class="badge badge-pill badge-outline-danger">
                                {{ $siswa->pelanggaran->sum('poin') }} Poin
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Daftar Pelanggaran</h5>
                        <div class="scroll">
                            @forelse($siswa->pelanggaran->sortByDesc('tanggal') as $v)
                                <div class="d-flex flex-row mb-3 pb-3 border-bottom">
                                    <div class="pl-3 pr-2 w-100">
                                        <div class="d-flex justify-content-between">
                                            <p class="font-weight-medium mb-0 ">{{ $v->jenis_pelanggaran }}</p>
                                            <small class="text-muted">{{ $v->tanggal->format('d M Y') }}</small>
                                        </div>
                                        <div class="mb-2">
                                            <span
                                                class="badge badge-pill badge-outline-{{ $v->kategori == 'berat' ? 'danger' : ($v->kategori == 'sedang' ? 'warning' : 'info') }}">
                                                {{ ucfirst($v->kategori) }} ({{ $v->poin }} Poin)
                                            </span>
                                            <span
                                                class="badge badge-pill badge-{{ $v->status == 'selesai' ? 'success' : 'secondary' }} ml-1">
                                                {{ ucfirst($v->status) }}
                                            </span>
                                        </div>
                                        <p class="text-muted text-small mb-2"><strong>Kronologi:</strong>
                                            {{ $v->kronologi ?? '-' }}</p>
                                        <p class="text-muted text-small mb-0"><strong>Sanksi:</strong>
                                            {{ $v->sanksi ?? '-' }}</p>
                                        <small class="text-muted">Pelapor:
                                            {{ $v->pelapor->nama_lengkap ?? 'System' }}</small>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <p class="text-muted small">Belum ada catatan pelanggaran untuk siswa ini.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
