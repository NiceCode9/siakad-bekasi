@extends('layouts.app')

@section('title', 'Detail Kenaikan Kelas')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>Detail Proses Kenaikan/Kelulusan</h1>
            <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                <ol class="breadcrumb pt-0">
                    <li class="breadcrumb-item"><a href="{{ route('kenaikan-kelas.index') }}">Kenaikan Kelas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detail #{{ $kenaikan->id }}</li>
                </ol>
            </nav>
            <div class="separator mb-5"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-4">Informasi Proses</h5>
                    <p><strong>Tahun Akademik:</strong> {{ $kenaikan->tahunAkademik->tahun }}</p>
                    <p><strong>Tanggal Proses:</strong> {{ $kenaikan->tanggal_proses->format('d/m/Y H:i') }}</p>
                    <p><strong>Diproses Oleh:</strong> {{ $kenaikan->processedBy->name }}</p>
                    <p><strong>Total Siswa:</strong> {{ $kenaikan->total_siswa }}</p>
                    <p><strong>Naik/Lulus:</strong> <span class="text-success">{{ $kenaikan->total_naik }}</span></p>
                    <p><strong>Tidak Naik:</strong> <span class="text-danger">{{ $kenaikan->total_tidak_naik }}</span></p>
                    <hr>
                    <p><strong>Keterangan:</strong><br>{{ $kenaikan->keterangan ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-4">Daftar Detail Siswa</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas Asal</th>
                                    <th>Status</th>
                                    <th>Kelas Tujuan</th>
                                    <th>Rerata</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kenaikan->kenaikanKelasDetail as $idx => $detail)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>{{ $detail->siswa->nama }}</td>
                                    <td>{{ $detail->kelasAsal->nama }}</td>
                                    <td>
                                        <span class="badge badge-{{ in_array($detail->status_kenaikan, ['naik', 'lulus']) ? 'success' : 'danger' }}">
                                            {{ strtoupper(str_replace('_', ' ', $detail->status_kenaikan)) }}
                                        </span>
                                    </td>
                                    <td>{{ $detail->kelasTujuan->nama ?? '-' }}</td>
                                    <td>{{ $detail->rata_rata_nilai }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
