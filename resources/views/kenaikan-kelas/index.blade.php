@extends('layouts.app')

@section('title', 'Kenaikan Kelas')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>Kenaikan / Kelulusan Siswa</h1>
            <div class="separator mb-5"></div>
        </div>
    </div>

    <div class="row">
        <!-- Selection Form -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-4">Mulai Proses Baru</h5>
                    <form action="{{ route('kenaikan-kelas.simulasi') }}" method="GET">
                        <div class="form-group">
                            <label>Dari Kelas (Tingkat & Nama)</label>
                            <select name="kelas_asal_id" class="form-control select2-single" required>
                                <option value="">Pilih Kelas...</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->nama }} ({{ $class->jurusan->nama }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tahun Akademik Target</label>
                            <select name="tahun_akademik_id" class="form-control select2-single" required>
                                <option value="">Pilih Tahun...</option>
                                @foreach($tahunAkademiks as $ta)
                                    <option value="{{ $ta->id }}">{{ $ta->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">MULAI SIMULASI</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- History Table -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-4">Riwayat Kenaikan/Kelulusan</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Tahun Akademik</th>
                                    <th>Total Siswa</th>
                                    <th>Naik/Lulus</th>
                                    <th>Tidak Naik</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($history as $item)
                                <tr>
                                    <td>{{ $item->tanggal_proses->format('d/m/Y') }}</td>
                                    <td>{{ $item->tahunAkademik->nama }}</td>
                                    <td>{{ $item->total_siswa }}</td>
                                    <td><span class="badge badge-success">{{ $item->total_naik }}</span></td>
                                    <td><span class="badge badge-danger">{{ $item->total_tidak_naik }}</span></td>
                                    <td><span class="badge badge-outline-primary">{{ strtoupper($item->status) }}</span></td>
                                    <td>
                                        <a href="{{ route('kenaikan-kelas.show', $item->id) }}" class="btn btn-xs btn-outline-info">Detail</a>
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
</div>
@endsection
