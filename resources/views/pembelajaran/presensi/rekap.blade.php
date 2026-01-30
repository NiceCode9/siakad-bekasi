@extends('layouts.app')

@section('title', 'Rekap Presensi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Rekap Presensi Bulanan</h4>
        <div>
            <a href="{{ route('presensi.rekap-mapel') }}" class="btn btn-info btn-sm mr-2">
                <i class="fas fa-book"></i> Rekap per Mapel
            </a>
            <a href="{{ route('presensi.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('presensi.rekap') }}" method="GET" class="form-inline">
                <label class="mr-2">Kelas:</label>
                <select name="kelas_id" class="form-control mr-4" required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ $kelasId == $k->id ? 'selected' : '' }}>
                            {{ $k->nama }}
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
                <select name="tahun" class="form-control mr-4">
                    @for($i = date('Y')-1; $i <= date('Y')+1; $i++)
                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Tampilkan
                </button>
            </form>
        </div>
    </div>

    @if($dataRekap)
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between">
                <h6 class="mb-0 pt-2">Rekap Kelas: {{ $selectedKelas->nama }} - {{ \Carbon\Carbon::createFromDate($tahun, $bulan)->translatedFormat('F Y') }}</h6>
                <button onclick="window.print()" class="btn btn-success btn-sm"><i class="fas fa-print"></i> Cetak</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-hover mb-0" style="font-size: 0.85rem;">
                        <thead class="thead-light text-center">
                            <tr>
                                <th rowspan="2" class="align-middle" style="min-width: 200px; left: 0; position: sticky; background: #f8f9fa;">Nama Siswa</th>
                                <th colspan="{{ $dataRekap['days'] }}">Tanggal</th>
                                <th colspan="4">Total</th>
                            </tr>
                            <tr>
                                @for($d = 1; $d <= $dataRekap['days']; $d++)
                                    <th style="min-width: 25px;">{{ $d }}</th>
                                @endfor
                                <th class="bg-success text-white" title="Hadir">H</th>
                                <th class="bg-info text-white" title="Izin">I</th>
                                <th class="bg-warning text-white" title="Sakit">S</th>
                                <th class="bg-danger text-white" title="Alpha">A</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dataRekap['siswa'] as $sk)
                                @php
                                    $studentPresensi = $dataRekap['presensi'][$sk->siswa->id] ?? collect();
                                    $presensiByDate = $studentPresensi->keyBy(function($item) {
                                        return $item->tanggal->format('j');
                                    });
                                    
                                    $h = $studentPresensi->where('status', 'H')->count();
                                    $i = $studentPresensi->where('status', 'I')->count();
                                    $s = $studentPresensi->where('status', 'S')->count();
                                    $a = $studentPresensi->where('status', 'A')->count();
                                @endphp
                                <tr>
                                    <td style="left: 0; position: sticky; background: #fff;">{{ $sk->siswa->nama_lengkap }}</td>
                                    @for($d = 1; $d <= $dataRekap['days']; $d++)
                                        @php 
                                            $p = $presensiByDate[$d] ?? null;
                                            $color = '';
                                            $text = '';
                                            if($p) {
                                                $text = $p->status;
                                                if($p->status == 'H') $color = 'text-success font-weight-bold';
                                                elseif($p->status == 'I') $color = 'text-info font-weight-bold';
                                                elseif($p->status == 'S') $color = 'text-warning font-weight-bold';
                                                elseif($p->status == 'A') $color = 'text-danger font-weight-bold';
                                            }
                                        @endphp
                                        <td class="text-center {{ $color }}">{{ $text }}</td>
                                    @endfor
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
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    @media print {
        .btn, form { display: none; }
        .card { border: none; }
        .table-responsive { overflow: visible; }
    }
</style>
@endpush
