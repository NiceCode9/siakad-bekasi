@extends('layouts.app')

@section('title', 'Detail Nilai Siswa')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('dashboard-nilai.index') }}" class="btn btn-circle btn-light mr-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h4 class="mb-0">{{ $siswa->nama_lengkap }}</h4>
                <small class="text-muted">Kelas: {{ $kelas->nama }} | NISN: {{ $siswa->nisn }}</small>
            </div>
        </div>
        <div>
            @if($raports->count() > 0)
                <div class="dropdown d-inline-block">
                    <button class="btn btn-info shadow-sm dropdown-toggle" type="button" data-toggle="dropdown">
                        <i class="fas fa-file-pdf mr-1"></i> Lihat Raport
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        @foreach($raports as $r)
                            <a class="dropdown-item" href="{{ route('raport.show', $r->id) }}">
                                {{ $r->komponenNilai->nama ?? 'Komponen' }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Academic Grades -->
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 font-weight-bold text-primary"><i class="fas fa-book mr-2"></i> Nilai Akademik per Mata Pelajaran</h6>
                <span class="badge badge-primary">{{ $gradesGrouped->count() }} Mata Pelajaran</span>
            </div>

            <div class="row">
                @forelse($gradesGrouped as $mapelId => $mapelGrades)
                    @php $firstGrade = $mapelGrades->first(); @endphp
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="font-weight-bold mb-0 text-dark">{{ $firstGrade->mataPelajaran->nama }}</h6>
                                        <small class="text-muted">{{ $firstGrade->mataPelajaran->kode }}</small>
                                    </div>
                                    <div class="text-right">
                                        @php 
                                            $avgNilai = $mapelGrades->avg('nilai_akhir');
                                        @endphp
                                        <div class="h4 mb-0 font-weight-bold {{ $avgNilai < 75 ? 'text-danger' : 'text-success' }}">
                                            {{ round($avgNilai) }}
                                        </div>
                                        <small class="text-muted text-uppercase" style="font-size: 0.6rem;">Rata-rata</small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body py-2">
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless mb-0" style="font-size: 0.85rem;">
                                        <thead>
                                            <tr class="text-muted">
                                                <th>Komponen</th>
                                                <th class="text-center">Nilai</th>
                                                <th class="text-center">PRD</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($mapelGrades as $g)
                                                <tr>
                                                    <td class="py-1">
                                                        <span class="text-secondary">{{ $g->komponen_nama }}</span>
                                                        @if($g->is_manual_override)
                                                            <i class="fas fa-info-circle text-warning ml-1" title="Manual Override"></i>
                                                        @endif
                                                    </td>
                                                    <td class="text-center py-1 font-weight-bold text-dark">{{ round($g->nilai_akhir) }}</td>
                                                    <td class="text-center py-1"><span class="badge badge-light border">{{ $g->predikat }}</span></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-folder-open fa-3x text-muted mb-3 opacity-20"></i>
                                <p class="text-muted mb-0">Belum ada data nilai akademik yang terdeteksi.</p>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Sidebar Assessments -->
        <div class="col-md-4">
            <!-- Attendance -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 font-weight-bold text-primary"><i class="fas fa-calendar-check mr-2"></i> Presensi Semester</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4 border-right">
                            <h4 class="mb-0 text-info">{{ $attendance->sakit ?? 0 }}</h4>
                            <small class="text-muted">Sakit</small>
                        </div>
                        <div class="col-4 border-right">
                            <h4 class="mb-0 text-warning">{{ $attendance->izin ?? 0 }}</h4>
                            <small class="text-muted">Izin</small>
                        </div>
                        <div class="col-4">
                            <h4 class="mb-0 text-danger">{{ $attendance->alpha ?? 0 }}</h4>
                            <small class="text-muted">Alpha</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attitudes -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 font-weight-bold text-primary"><i class="fas fa-heart mr-2"></i> Penilaian Sikap</h6>
                    <a href="{{ route('nilai-sikap.index', ['kelas_id' => $kelas->id]) }}" class="btn btn-xs btn-outline-primary">Edit</a>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush text-sm">
                        @foreach(['spiritual' => 'Spiritual', 'sosial' => 'Sosial'] as $key => $label)
                            @php $s = $sikap->where('aspek', $key)->first(); @endphp
                            <li class="list-group-item">
                                <span class="d-block font-weight-bold">{{ $label }}</span>
                                @if($s)
                                    <span class="badge badge-success mb-1">Predikat: {{ $s->predikat }}</span>
                                    <p class="mb-0 small text-muted">{{ Str::limit($s->deskripsi, 100) }}</p>
                                @else
                                    <span class="text-muted italic small">Belum dinilai</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Ekstrakurikuler -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 font-weight-bold text-primary"><i class="fas fa-football-ball mr-2"></i> Ekstrakurikuler</h6>
                    <a href="{{ route('nilai-ekstrakurikuler.index', ['kelas_id' => $kelas->id]) }}" class="btn btn-xs btn-outline-primary">Edit</a>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($ekskul as $e)
                            <li class="list-group-item">
                                <span class="d-block font-weight-bold">{{ $e->ekstrakurikuler->nama }}</span>
                                <span class="badge badge-info">Predikat: {{ $e->predikat }}</span>
                                <p class="mb-0 small text-muted">{{ $e->keterangan }}</p>
                            </li>
                        @empty
                            <li class="list-group-item text-center py-3 text-muted small">Belum ada nilai ekskul.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-xs { padding: 0.1rem 0.4rem; font-size: 0.7rem; }
    .btn-circle { width: 35px; height: 35px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; padding: 0; }
    .text-sm { font-size: 0.85rem; }
</style>
@endpush
