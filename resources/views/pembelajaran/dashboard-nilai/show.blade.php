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
            @if($raport)
                <a href="{{ route('raport.show', $raport->id) }}" class="btn btn-info shadow-sm">
                    <i class="fas fa-file-pdf mr-1"></i> Lihat Raport
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Academic Grades -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 font-weight-bold text-primary"><i class="fas fa-book mr-2"></i> Nilai Akademik</h6>
                    <span class="badge badge-primary">{{ $grades->count() }} Mata Pelajaran</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Mata Pelajaran</th>
                                    <th class="text-center" width="100">Nilai Akhir</th>
                                    <th class="text-center" width="100">Predikat</th>
                                    <th class="text-center" width="120">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($grades as $g)
                                    <tr>
                                        <td>
                                            <span class="font-weight-bold d-block">{{ $g->mataPelajaran->nama }}</span>
                                            <small class="text-muted">{{ $g->mataPelajaran->kode }}</small>
                                        </td>
                                        <td class="text-center align-middle h5 mb-0">
                                            <span class="{{ $g->nilai_akhir < 75 ? 'text-danger' : 'text-success' }}">
                                                {{ number_format($g->nilai_akhir, 1) }}
                                            </span>
                                        </td>
                                        <td class="text-center align-middle font-weight-bold">{{ $g->predikat }}</td>
                                        <td class="text-center align-middle">
                                            @if($g->is_manual_override)
                                                <span class="badge badge-warning" title="Override: {{ $g->override_reason }}">Manual</span>
                                            @else
                                                <span class="badge badge-light">Sistem</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Belum ada data nilai akademik.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
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
