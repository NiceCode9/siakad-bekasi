@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1>Rekap Pelanggaran Siswa</h1>
                <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                    <ol class="breadcrumb pt-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pelanggaran-siswa.index') }}">Pelanggaran</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Rekap</li>
                    </ol>
                </nav>
                <div class="separator mb-5"></div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Resume Pelanggaran per Siswa</h5>
                        <table class="table data-table table-striped">
                            <thead>
                                <tr>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th class="text-center">Total Pelanggaran</th>
                                    <th class="text-center">Total Poin</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($resume as $r)
                                    <tr>
                                        <td>{{ $r->nama_lengkap }}</td>
                                        <td>{{ $r->kelas->first()->nama ?? '-' }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-pill badge-secondary">
                                                {{ $r->pelanggaran_count }} kali
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge badge-pill badge-{{ $r->pelanggaran_sum_poin > 50 ? 'danger' : ($r->pelanggaran_sum_poin > 25 ? 'warning' : 'info') }}">
                                                {{ $r->pelanggaran_sum_poin ?? 0 }} Poin
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('pelanggaran-siswa.student', $r->id) }}"
                                                class="btn btn-xs btn-outline-primary">
                                                <i class="simple-icon-magnifier"></i> Detail Histori
                                            </a>
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

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.data-table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                }
            });
        });
    </script>
@endpush
