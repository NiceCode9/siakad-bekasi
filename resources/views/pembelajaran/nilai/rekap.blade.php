@extends('layouts.app')

@section('title', 'Rekap Nilai & Pasca Penilaian')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Rekap Nilai: {{ $mpk->mataPelajaran->nama }}</h4>
            <div class="text-muted">
                Kelas: {{ $kelas->nama }} | Semester: {{ $semesterAktif->nama }}
                @if($mpk->kkm)
                    <span class="badge badge-success ml-2">KKM: {{ $mpk->kkm }}</span>
                @endif
            </div>
        </div>
        <div>
            <a href="{{ route('nilai.index', ['kelas_id' => $kelas->id]) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-0">
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered table-hover mb-0" style="font-size: 0.85rem;">
                    <thead class="thead-light text-center">
                        <tr>
                            <th rowspan="2" class="align-middle" width="50">No</th>
                            <th rowspan="2" class="align-middle" style="min-width: 200px;">Nama Siswa</th>
                            <th colspan="{{ $components->count() }}">Komponen Nilai</th>
                            <th class="align-middle bg-light" width="80">Rata-Rata</th>
                        </tr>
                        <tr>
                            @foreach($components as $c)
                                <th width="70">{{ $c->nama }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siswa as $idx => $sk)
                            @php
                                $grades = $existingGrades->get($sk->siswa_id) ?? collect();
                                $sum = 0;
                                $count = 0;
                                foreach($components as $c) {
                                    $g = $grades->where('komponen_nilai_id', $c->id)->first();
                                    if($g) {
                                        $sum += $g->nilai;
                                        $count++;
                                    }
                                }
                                $avg = $count > 0 ? $sum / $count : 0;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $idx + 1 }}</td>
                                <td>{{ $sk->siswa->nama_lengkap }}</td>
                                @foreach($components as $c)
                                    @php $g = $grades->where('komponen_nilai_id', $c->id)->first(); @endphp
                                    <td class="text-center {{ $g ? '' : 'text-muted' }} {{ ($g && $mpk->kkm && $g->nilai < $mpk->kkm) ? 'text-danger font-weight-bold' : '' }}">
                                        {{ $g ? number_format($g->nilai, 0) : '-' }}
                                    </td>
                                @endforeach
                                <td class="text-center font-weight-bold bg-light {{ ($avg < ($mpk->kkm ?? 0)) ? 'text-danger' : '' }}">
                                    {{ number_format($avg, 1) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .btn-xs { padding: 0.1rem 0.4rem; font-size: 0.75rem; }
    .bg-info-light { background-color: rgba(23, 162, 184, 0.1); }
</style>
@endpush

@push('scripts')
<script>
// No extra scripts needed
</script>
@endpush
