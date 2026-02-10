@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>Presensi Harian Siswa</h1>
            <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                <ol class="breadcrumb pt-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Presensi Harian</li>
                </ol>
            </nav>
            <div class="separator mb-5"></div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('presensi-harian.index') }}" method="GET" class="form-inline">
                        <label class="sr-only" for="tanggal">Tanggal</label>
                        <input type="date" class="form-control mb-2 mr-sm-2" id="tanggal" name="tanggal" value="{{ $tanggal }}">

                        <label class="sr-only" for="kelas_id">Kelas</label>
                        <select class="form-control mb-2 mr-sm-2" id="kelas_id" name="kelas_id" required>
                            <option value="">Pilih Kelas...</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}" {{ $selectedKelasId == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama }} ({{ $k->jurusan->singkatan }})
                                </option>
                            @endforeach
                        </select>

                        <button type="submit" class="btn btn-primary mb-2">Tampilkan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($selectedKelasId && count($students) > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Input Presensi - {{ date('d M Y', strtotime($tanggal)) }}</h5>
                    
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('presensi-harian.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="kelas_id" value="{{ $selectedKelasId }}">
                        <input type="hidden" name="tanggal" value="{{ $tanggal }}">

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="10%">NIS</th>
                                        <th width="25%">Nama Siswa</th>
                                        <th width="30%">Status Kehadiran</th>
                                        <th width="30%">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $index => $siswa)
                                        @php
                                            $status = $attendanceData[$siswa->id]->status ?? 'H';
                                            $keterangan = $attendanceData[$siswa->id]->keterangan ?? '';
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $siswa->nis }}</td>
                                            <td>{{ $siswa->nama_lengkap }}</td>
                                            <td>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="attendance[{{ $siswa->id }}][status]" id="status_h_{{ $siswa->id }}" value="H" {{ $status == 'H' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="status_h_{{ $siswa->id }}">Hadir</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="attendance[{{ $siswa->id }}][status]" id="status_i_{{ $siswa->id }}" value="I" {{ $status == 'I' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="status_i_{{ $siswa->id }}">Izin</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="attendance[{{ $siswa->id }}][status]" id="status_s_{{ $siswa->id }}" value="S" {{ $status == 'S' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="status_s_{{ $siswa->id }}">Sakit</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="attendance[{{ $siswa->id }}][status]" id="status_a_{{ $siswa->id }}" value="A" {{ $status == 'A' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="status_a_{{ $siswa->id }}">Alpha</label>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm" name="attendance[{{ $siswa->id }}][keterangan]" value="{{ $keterangan }}" placeholder="Keterangan (opsional)">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 text-right">
                            <button type="submit" class="btn btn-primary btn-lg">Simpan Presensi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @elseif($selectedKelasId)
    <div class="row">
        <div class="col-12">
            <div class="alert alert-warning">Tidak ada siswa aktif di kelas ini.</div>
        </div>
    </div>
    @endif
</div>
@endsection
