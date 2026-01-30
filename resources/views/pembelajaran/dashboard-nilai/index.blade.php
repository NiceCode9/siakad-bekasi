@extends('layouts.app')

@section('title', 'Dashboard Nilai Siswa')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Dashboard Nilai & Progress Penilaian</h4>
            <small class="text-muted">Kelas: {{ $kelas->nama ?? '-' }} | Semester: {{ $semesterAktif->nama }}</small>
        </div>
        @if(Auth::user()->hasRole(['admin', 'super-admin']))
            <form action="{{ route('dashboard-nilai.index') }}" method="GET" class="form-inline">
                <select name="kelas_id" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                    @foreach($allKelas as $k)
                        <option value="{{ $k->id }}" {{ ($kelas->id ?? 0) == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>

    @if(!$kelas)
        <div class="alert alert-warning">Data kelas tidak ditemukan untuk semester ini.</div>
    @else
        <div class="row">
            <!-- Summary Stats -->
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-primary text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-uppercase font-weight-bold">Total Siswa</small>
                                <h2 class="mb-0">{{ $siswas->count() }}</h2>
                            </div>
                            <i class="fas fa-users fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-success text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-uppercase font-weight-bold">Total Mata Pelajaran</small>
                                <h2 class="mb-0">{{ $subjectCount }}</h2>
                            </div>
                            <i class="fas fa-book fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 font-weight-bold"><i class="fas fa-list mr-2 text-primary"></i> Progress Penilaian per Siswa</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th>Nama Siswa</th>
                                <th class="text-center">Nilai Akademik</th>
                                <th class="text-center">Sikap Spr.</th>
                                <th class="text-center">Sikap Sos.</th>
                                <th class="text-center">Ekskul</th>
                                <th class="text-center">Status Raport</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($siswas as $idx => $sk)
                                <tr>
                                    <td class="text-center align-middle">{{ $idx + 1 }}</td>
                                    <td class="align-middle">
                                        <span class="font-weight-bold text-dark">{{ $sk->siswa->nama_lengkap }}</span><br>
                                        <small class="text-muted">{{ $sk->siswa->nisn }}</small>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="progress progress-sm mb-1" style="height: 6px;">
                                            <div class="progress-bar {{ $sk->stats['akademik_percent'] == 100 ? 'bg-success' : 'bg-warning' }}" 
                                                 role="progressbar" style="width: {{ $sk->stats['akademik_percent'] }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $sk->stats['akademik_count'] }} / {{ $subjectCount }} Mapel</small>
                                    </td>
                                    <td class="text-center align-middle">
                                        <i class="fas {{ $sk->stats['sikap_spiritual'] ? 'fa-check-circle text-success' : 'fa-times-circle text-muted opacity-50' }}"></i>
                                    </td>
                                    <td class="text-center align-middle">
                                        <i class="fas {{ $sk->stats['sikap_sosial'] ? 'fa-check-circle text-success' : 'fa-times-circle text-muted opacity-50' }}"></i>
                                    </td>
                                    <td class="text-center align-middle">
                                        <i class="fas {{ $sk->stats['ekskul'] ? 'fa-check-circle text-success' : 'fa-times-circle text-muted opacity-50' }}"></i>
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($sk->stats['raport_status'] == 'published')
                                            <span class="badge badge-success">Published</span>
                                        @elseif($sk->stats['raport_status'] == 'approved')
                                            <span class="badge badge-info">Approved</span>
                                        @elseif($sk->stats['raport_status'] == 'draft')
                                            <span class="badge badge-warning">Draft/Process</span>
                                        @else
                                            <span class="badge badge-secondary">Belum Ada</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="btn-group">
                                            <a href="{{ route('dashboard-nilai.show', $sk->siswa_id) }}" class="btn btn-primary btn-sm" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-secondary btn-sm dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right shadow border-0">
                                                <h6 class="dropdown-header">Input Penilaian</h6>
                                                <a class="dropdown-item" href="{{ route('nilai-sikap.create', ['kelas_id' => $kelas->id, 'aspek' => 'spiritual']) }}">
                                                    <i class="fas fa-praying-hands mr-2 text-warning"></i> Nilai Spiritual
                                                </a>
                                                <a class="dropdown-item" href="{{ route('nilai-sikap.create', ['kelas_id' => $kelas->id, 'aspek' => 'sosial']) }}">
                                                    <i class="fas fa-hands-helping mr-2 text-primary"></i> Nilai Sosial
                                                </a>
                                                <a class="dropdown-item" href="{{ route('nilai-ekstrakurikuler.create', ['kelas_id' => $kelas->id]) }}">
                                                    <i class="fas fa-running mr-2 text-success"></i> Nilai Ekskul
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="{{ route('raport.index') }}">
                                                    <i class="fas fa-file-invoice mr-2 text-info"></i> Ke Menu Raport
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Belum ada data siswa di kelas ini.</td>
                                </tr>
                            @endforelse
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
    .opacity-50 { opacity: 0.5; }
    .bg-info-light { background-color: rgba(23, 162, 184, 0.1); }
    .dropdown-item { font-size: 0.85rem; padding: 0.5rem 1rem; }
    .dropdown-header { font-size: 0.75rem; text-transform: uppercase; font-weight: bold; color: #adb5bd; }
</style>
@endpush
