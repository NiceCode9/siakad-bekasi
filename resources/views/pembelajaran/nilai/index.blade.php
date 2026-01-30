@extends('layouts.app')

@section('title', 'Input Nilai Akademik')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Input Nilai Akademik</h4>
            <small class="text-muted">Kelola nilai pengetahuan dan keterampilan siswa</small>
        </div>
        <a href="{{ route('komponen-nilai.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-cogs"></i> Atur Komponen Nilai
        </a>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('nilai.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Pilih Kelas</label>
                            <select name="kelas_id" class="form-control" onchange="this.form.submit()">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request('kelas_id'))
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">Daftar Mata Pelajaran di Kelas Ini</h6>
                    </div>
                    <div class="card-body">
                        @if($subjects->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Kode</th>
                                            <th>Mata Pelajaran</th>
                                            <th>Aksi (Input Nilai per Komponen)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subjects as $mpk)
                                            <tr>
                                                <td>{{ $mpk->mataPelajaran->kode }}</td>
                                                <td>{{ $mpk->mataPelajaran->nama }}</td>
                                                <td>
                                                    @foreach($components as $comp)
                                                        <a href="{{ route('nilai.create', [
                                                            'kelas_id' => request('kelas_id'),
                                                            'mata_pelajaran_kelas_id' => $mpk->id,
                                                            'komponen_nilai_id' => $comp->id
                                                        ]) }}" class="btn btn-outline-primary btn-sm mb-1 mr-1">
                                                            {{ $comp->nama }}
                                                        </a>
                                                    @endforeach
                                                    <a href="{{ route('nilai.rekap', [
                                                        'kelas_id' => request('kelas_id'),
                                                        'mata_pelajaran_kelas_id' => $mpk->id
                                                    ]) }}" class="btn btn-info btn-sm mb-1">
                                                        <i class="fas fa-list-check"></i> Rekap & Pasca Penilaian
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                Belum ada mata pelajaran yang diatur untuk kelas ini. 
                                Silakan atur di menu <strong>Data Kelas -> Detail -> Kelola Mata Pelajaran</strong>.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
