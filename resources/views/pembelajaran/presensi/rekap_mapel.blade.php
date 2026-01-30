@extends('layouts.app')

@section('title', 'Rekap Presensi per Mapel')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Rekap Presensi per Mata Pelajaran</h4>
            <small class="text-muted">Laporan absensi siswa berdasarkan sesi jam pelajaran</small>
        </div>
        <a href="{{ route('presensi.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('presensi.rekap-mapel') }}" method="GET" class="form-inline">
                <label class="mr-2">Kelas:</label>
                <select name="kelas_id" class="form-control mr-3" required onchange="this.form.submit()">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ $kelasId == $k->id ? 'selected' : '' }}>
                            {{ $k->nama }}
                        </option>
                    @endforeach
                </select>

                <label class="mr-2">Mapel:</label>
                <select name="mata_pelajaran_id" class="form-control mr-3" required>
                    <option value="">-- Pilih Mapel --</option>
                    @foreach($mataPelajaran as $m)
                        <option value="{{ $m->id }}" {{ $mapelId == $m->id ? 'selected' : '' }}>
                            {{ $m->nama }}
                        </option>
                    @endforeach
                </select>

                <label class="mr-2">Bulan:</label>
                <select name="bulan" class="form-control mr-2">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate(null, $i)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>

                <label class="mr-2">Tahun:</label>
                <select name="tahun" class="form-control mr-3">
                    @for($i = date('Y')-1; $i <= date('Y')+1; $i++)
                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filter
                </button>
            </form>
        </div>
    </div>

    @if($dataRekap)
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">Rekap: {{ $selectedKelas->nama }} | {{ $mataPelajaran->where('id', $mapelId)->first()->nama ?? '' }}</h6>
                    <small>{{ \Carbon\Carbon::createFromDate($tahun, $bulan)->translatedFormat('F Y') }}</small>
                </div>
                <button onclick="window.print()" class="btn btn-success btn-sm"><i class="fas fa-print"></i> Cetak Laporan</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-hover mb-0" style="font-size: 0.8rem;">
                        <thead class="thead-light text-center">
                            <tr>
                                <th rowspan="2" class="align-middle" style="min-width: 180px; left: 0; position: sticky; background: #f8f9fa; z-index: 2;">Nama Siswa</th>
                                <th colspan="{{ $dataRekap['journals']->count() ?: 1 }}">Pertemuan / Sesi Jurnal</th>
                                <th colspan="4" class="align-middle">Total</th>
                            </tr>
                            <tr>
                                @forelse($dataRekap['journals'] as $j)
                                    <th style="min-width: 40px;" title="{{ $j->tanggal->format('d/m/Y') }}">
                                        {{ $j->tanggal->format('d/m') }}
                                    </th>
                                @empty
                                    <th>-</th>
                                @endforelse
                                <th class="bg-success text-white" width="30">H</th>
                                <th class="bg-info text-white" width="30">I</th>
                                <th class="bg-warning text-white" width="30">S</th>
                                <th class="bg-danger text-white" width="30">A</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dataRekap['siswa'] as $sk)
                                @php
                                    $studentPresensi = $dataRekap['presensi'][$sk->siswa->id] ?? collect();
                                    $presensiByJournal = $studentPresensi->keyBy('jurnal_mengajar_id');
                                    
                                    $h = $studentPresensi->where('status', 'H')->count();
                                    $i = $studentPresensi->where('status', 'I')->count();
                                    $s = $studentPresensi->where('status', 'S')->count();
                                    $a = $studentPresensi->where('status', 'A')->count();
                                @endphp
                                <tr>
                                    <td style="left: 0; position: sticky; background: #fff; z-index: 1;">{{ $sk->siswa->nama_lengkap }}</td>
                                    @forelse($dataRekap['journals'] as $j)
                                        @php 
                                            $p = $presensiByJournal[$j->id] ?? null;
                                            $color = '';
                                            if($p) {
                                                if($p->status == 'H') $color = 'text-success font-weight-bold';
                                                elseif($p->status == 'I') $color = 'text-info font-weight-bold';
                                                elseif($p->status == 'S') $color = 'text-warning font-weight-bold';
                                                elseif($p->status == 'A') $color = 'text-danger font-weight-bold';
                                            }
                                        @endphp
                                        <td class="text-center {{ $color }}">{{ $p ? $p->status : '-' }}</td>
                                    @empty
                                        <td class="text-center text-muted small">Belum ada data</td>
                                    @endforelse
                                    <td class="text-center font-weight-bold">{{ $h }}</td>
                                    <td class="text-center font-weight-bold">{{ $i }}</td>
                                    <td class="text-center font-weight-bold">{{ $s }}</td>
                                    <td class="text-center font-weight-bold">{{ $a }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white">
                <div class="row small">
                    <div class="col-md-3"><span class="badge badge-success">H</span> : Hadir</div>
                    <div class="col-md-3"><span class="badge badge-info">I</span> : Izin</div>
                    <div class="col-md-3"><span class="badge badge-warning">S</span> : Sakit</div>
                    <div class="col-md-3"><span class="badge badge-danger">A</span> : Alpha</div>
                </div>
            </div>
        </div>
    @elseif($kelasId && $mapelId)
        <div class="text-center py-5 bg-white border rounded">
            <i class="fas fa-info-circle fa-4x text-muted mb-3"></i>
            <p>Tidak ada data jurnal mengajar di bulan ini untuk mata pelajaran tersebut.</p>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    @media print {
        .btn, form, .breadcrumb-container, .navbar, .footer { display: none !important; }
        .card { border: none !important; box-shadow: none !important; }
        .table-responsive { overflow: visible !important; }
        .container-fluid { padding: 0 !important; }
        body { background: white !important; }
    }
</style>
@endpush
