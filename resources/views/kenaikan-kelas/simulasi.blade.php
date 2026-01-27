@extends('layouts.app')

@section('title', 'Simulasi Kenaikan Kelas')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>Simulasi Kenaikan/Kelulusan: {{ $kelasAsal->nama }}</h1>
            <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                <ol class="breadcrumb pt-0">
                    <li class="breadcrumb-item"><a href="{{ route('kenaikan-kelas.index') }}">Kenaikan Kelas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Simulasi</li>
                </ol>
            </nav>
            <div class="separator mb-5"></div>
        </div>
    </div>

    <form action="{{ route('kenaikan-kelas.eksekusi') }}" method="POST">
        @csrf
        <input type="hidden" name="kelas_asal_id" value="{{ $kelasAsal->id }}">
        <input type="hidden" name="tahun_akademik_id" value="{{ $tahunAkademik->id }}">

        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-4">Data Siswa & Rekomendasi</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered text-center">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>NIS/Nama</th>
                                        <th>Rerata Nilai</th>
                                        <th>Absensi (S+I+A)</th>
                                        <th>Rekomendasi</th>
                                        <th>Status Akhir</th>
                                        <th>Kelas Tujuan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $idx => $siswa)
                                        @php
                                            $raport = $siswa->raport->first();
                                            $avg = $raport->average_score ?? 0;
                                            $absensi = ($raport->jumlah_sakit ?? 0) + ($raport->jumlah_izin ?? 0) + ($raport->jumlah_alpha ?? 0);
                                            
                                            // Simple logic for recommendation
                                            $isEligible = ($avg >= 70 && ($raport->jumlah_alpha ?? 0) <= 3);
                                            $recom = $isEligible ? 'Naik' : 'Tinjau Ulang';
                                            if ($kelasAsal->tingkat == 12) $recom = $isEligible ? 'Lulus' : 'Tinjau Ulang';
                                        @endphp
                                        <tr>
                                            <td>{{ $idx + 1 }}</td>
                                            <td class="text-left">
                                                <strong>{{ $siswa->nis }}</strong><br>
                                                {{ $siswa->nama }}
                                                <input type="hidden" name="students[{{ $idx }}][id]" value="{{ $siswa->id }}">
                                            </td>
                                            <td>{{ round($avg, 2) }}</td>
                                            <td>{{ $absensi }} (Alpha: {{ $raport->jumlah_alpha ?? 0 }})</td>
                                            <td>
                                                <span class="badge badge-{{ $isEligible ? 'success' : 'warning' }}">
                                                    {{ $recom }}
                                                </span>
                                            </td>
                                            <td>
                                                <select name="students[{{ $idx }}][status]" class="form-control form-control-sm status-select">
                                                    @if($kelasAsal->tingkat == 12)
                                                        <option value="lulus" {{ $isEligible ? 'selected' : '' }}>Lulus</option>
                                                        <option value="mengulang" {{ !$isEligible ? 'selected' : '' }}>Mengulang</option>
                                                    @else
                                                        <option value="naik" {{ $isEligible ? 'selected' : '' }}>Naik Kelas</option>
                                                        <option value="tidak_naik" {{ !$isEligible ? 'selected' : '' }}>Tidak Naik</option>
                                                    @endif
                                                </select>
                                            </td>
                                            <td>
                                                <select name="students[{{ $idx }}][kelas_tujuan_id]" class="form-control form-control-sm target-class">
                                                    <option value="">-- Kenal/Lulus --</option>
                                                    @foreach($targetClasses as $tc)
                                                        <option value="{{ $tc->id }}" 
                                                            {{ ($isEligible && $tc->tingkat == $kelasAsal->tingkat + 1) || (!$isEligible && $tc->id == $kelasAsal->id) ? 'selected' : '' }}>
                                                            {{ $tc->nama }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-4">Konfirmasi Eksekusi</h5>
                        <div class="form-group">
                            <label>Keterangan Tambahan</label>
                            <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Rapat Dewan Guru tanggal ..."></textarea>
                        </div>
                        <div class="alert alert-warning">
                            <strong>PERINGATAN:</strong> Tindakan ini akan mengubah status kelas siswa secara permanen dan memindahkan mereka ke kelas tujuan yang dipilih.
                        </div>
                        <div class="text-right">
                            <a href="{{ route('kenaikan-kelas.index') }}" class="btn btn-outline-secondary">BATAL</a>
                            <button type="submit" class="btn btn-danger btn-lg" onclick="return confirm('Apakah Anda yakin ingin memproses kenaikan kelas ini?')">EKSEKUSI SEKARANG</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
