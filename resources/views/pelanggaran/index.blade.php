@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1>Data Pelanggaran Siswa</h1>
                <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                    <ol class="breadcrumb pt-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pelanggaran Siswa</li>
                    </ol>
                </nav>
                <div class="separator mb-5"></div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            @can('laporkan-pelanggaran')
                                <a href="{{ route('pelanggaran-siswa.create') }}" class="btn btn-primary mb-2">
                                    <i class="simple-icon-plus mr-1"></i> Lapor Pelanggaran Baru
                                </a>
                            @endcan
                            @can('view-resume-pelanggaran')
                                <a href="{{ route('pelanggaran-siswa.resume') }}" class="btn btn-info mb-2">
                                    <i class="simple-icon-chart mr-1"></i> Rekap Pelanggaran
                                </a>
                            @endcan
                        </div>

                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Pelanggaran</th>
                                    <th>Kategori</th>
                                    <th>Poin</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pelanggaran as $p)
                                    <tr>
                                        <td>{{ date('d M Y', strtotime($p->tanggal)) }}</td>
                                        <td>{{ $p->siswa->nama_lengkap }}</td>
                                        <td>
                                            {{ $p->siswa->siswaKelas->first() ? $p->siswa->siswaKelas->first()->kelas->nama : '-' }}
                                        </td>
                                        <td>{{ $p->jenis_pelanggaran }}</td>
                                        <td>
                                            <span
                                                class="badge badge-pill badge-outline-{{ $p->kategori == 'berat' ? 'danger' : ($p->kategori == 'sedang' ? 'warning' : 'info') }}">
                                                {{ ucfirst($p->kategori) }}
                                            </span>
                                        </td>
                                        <td>{{ $p->poin }}</td>
                                        <td>
                                            <span
                                                class="badge badge-pill badge-{{ $p->status == 'selesai' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($p->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @can('proses-pelanggaran')
                                                <a href="{{ route('pelanggaran-siswa.edit', $p->id) }}"
                                                    class="btn btn-xs btn-outline-primary">
                                                    <i class="simple-icon-pencil"></i> Proses
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
