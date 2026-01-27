@extends('layouts.app')

@section('title', 'Detail Legger - ' . $legger->kelas->nama)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>Legger Nilai: {{ $legger->kelas->nama }}</h1>
            <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                <ol class="breadcrumb pt-0">
                    <li class="breadcrumb-item"><a href="{{ route('legger.index') }}">Legger</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detail</li>
                </ol>
            </nav>
            <div class="top-right-button-container">
                <a href="{{ route('legger.excel', $legger->id) }}" class="btn btn-success btn-lg">EXPORT EXCEL</a>
                <a href="{{ route('legger.pdf', $legger->id) }}" class="btn btn-danger btn-lg">EXPORT PDF</a>
            </div>
            <div class="separator mb-5"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-4">
                        <p><strong>Semester:</strong> {{ $legger->semester->nama }} ({{ $legger->semester->tahunAkademik->tahun }})</p>
                        <p><strong>Wali Kelas:</strong> {{ $legger->kelas->waliKelas->nama ?? '-' }}</p>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm text-center">
                            <thead class="thead-light">
                                <tr>
                                    <th rowspan="2" class="align-middle">No</th>
                                    <th rowspan="2" class="align-middle">NIS</th>
                                    <th rowspan="2" class="align-middle">Nama Siswa</th>
                                    <th colspan="{{ $subjects->count() }}">Mata Pelajaran</th>
                                    <th rowspan="2" class="align-middle">Rerata</th>
                                    <th rowspan="2" class="align-middle">Rank</th>
                                    <th colspan="3">Absensi</th>
                                </tr>
                                <tr>
                                    @foreach($subjects as $mps)
                                        <th title="{{ $mps->mataPelajaran->nama }}">{{ $mps->mataPelajaran->kode }}</th>
                                    @endforeach
                                    <th>S</th>
                                    <th>I</th>
                                    <th>A</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($raports as $idx => $raport)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>{{ $raport->siswa->nis }}</td>
                                    <td class="text-left">{{ $raport->siswa->nama }}</td>
                                    
                                    @php 
                                        $totalNilai = 0;
                                        $countNilai = 0;
                                    @endphp
                                    
                                    @foreach($subjects as $mps)
                                        @php 
                                            $detail = $raport->raportDetail->where('mata_pelajaran_id', $mps->mata_pelajaran_id)->first();
                                            $nilai = $detail ? round($detail->nilai_akhir) : '-';
                                            if(is_numeric($nilai)) {
                                                $totalNilai += $nilai;
                                                $countNilai++;
                                            }
                                        @endphp
                                        <td>{{ $nilai }}</td>
                                    @endforeach

                                    <td><strong>{{ round($raport->average_score, 2) }}</strong></td>
                                    <td>{{ $raport->ranking }}</td>
                                    <td>{{ $raport->jumlah_sakit }}</td>
                                    <td>{{ $raport->jumlah_izin }}</td>
                                    <td>{{ $raport->jumlah_alpha }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        <p><small>* S: Sakit, I: Izin, A: Alpha</small></p>
                        <p><small>* Kode Mapel:</small><br>
                        @foreach($subjects as $mps)
                            <small><strong>{{ $mps->mataPelajaran->kode }}</strong>: {{ $mps->mataPelajaran->nama }} | </small>
                        @endforeach
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
