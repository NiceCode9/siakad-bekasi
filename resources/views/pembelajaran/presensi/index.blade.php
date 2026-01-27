@extends('layouts.app')

@section('title', 'Presensi Siswa')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Presensi Siswa</h4>
            <small class="text-muted">Input dan monitor kehadiran harian siswa</small>
        </div>
        <div>
            <a href="{{ route('presensi.rekap') }}" class="btn btn-info btn-sm">
                <i class="fas fa-calendar-alt"></i> Lihat Rekap Bulanan
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('presensi.index') }}" method="GET" class="form-inline">
                <label class="mr-2">Kelas:</label>
                <select name="kelas_id" class="form-control mr-4" required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama }}
                        </option>
                    @endforeach
                </select>

                <label class="mr-2">Tanggal:</label>
                <input type="date" name="tanggal" class="form-control mr-4" value="{{ $tanggal }}" required>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Tampilkan
                </button>
            </form>
        </div>
    </div>

    @if(request('kelas_id') && request('tanggal'))
        <div class="row">
            <!-- Summary Stats -->
            <div class="col-md-3">
                <div class="card bg-success text-white mb-3">
                    <div class="card-body py-3">
                         <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Hadir</h6>
                                <h3>{{ $rekap['total_h'] ?? 0 }}</h3>
                            </div>
                            <i class="fas fa-check-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white mb-3">
                    <div class="card-body py-3">
                         <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Izin</h6>
                                <h3>{{ $rekap['total_i'] ?? 0 }}</h3>
                            </div>
                            <i class="fas fa-envelope-open-text fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white mb-3">
                    <div class="card-body py-3">
                         <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Sakit</h6>
                                <h3>{{ $rekap['total_s'] ?? 0 }}</h3>
                            </div>
                            <i class="fas fa-procedures fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white mb-3">
                    <div class="card-body py-3">
                         <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Alpha</h6>
                                <h3>{{ $rekap['total_a'] ?? 0 }}</h3>
                            </div>
                            <i class="fas fa-times-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($rekap['not_recorded'] > 0)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> Terdeteksi <strong>{{ $rekap['not_recorded'] }}</strong> siswa belum diabsen hari ini.
                <a href="{{ route('presensi.create', request()->all()) }}" class="btn btn-warning btn-sm ml-2">Input Presensi Sekolah</a>
            </div>
        @elseif($rekap['presensi']->count() > 0)
             <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> Semua siswa sudah diabsen hari ini.
                 <a href="{{ route('presensi.create', request()->all()) }}" class="btn btn-success btn-sm ml-2">Edit Presensi</a>
            </div>
        @else
             <div class="alert alert-info">
                Data presensi belum tersedia.
                 <a href="{{ route('presensi.create', request()->all()) }}" class="btn btn-primary btn-sm ml-2">Mulai Input Presensi</a>
            </div>
        @endif

        <!-- Detail Table -->
        <div class="card mt-3">
            <div class="card-header bg-white">
                <h6 class="mb-0">Detail Kehadiran</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>NIS / NISN</th>
                                <th>Nama Lengkap</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rekap['siswa'] as $sk)
                                @php 
                                    $p = $rekap['presensi'][$sk->siswa->id] ?? null; 
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $sk->siswa->nis }}</td>
                                    <td>{{ $sk->siswa->nama_lengkap }}</td>
                                    <td>
                                        @if($p)
                                            @if($p->status == 'H') <span class="badge badge-success">Hadir</span>
                                            @elseif($p->status == 'I') <span class="badge badge-info">Izin</span>
                                            @elseif($p->status == 'S') <span class="badge badge-warning">Sakit</span>
                                            @elseif($p->status == 'A') <span class="badge badge-danger">Alpha</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $p->keterangan ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center">Tidak ada siswa</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @else
        <div class="text-center py-5 text-muted">
            <i class="fas fa-user-clock fa-4x mb-3"></i>
            <p>Silakan pilih Kelas dan Tanggal untuk melihat atau menginput presensi.</p>
        </div>
    @endif
</div>
@endsection
