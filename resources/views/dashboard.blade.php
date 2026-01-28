@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>Selamat Datang, {{ auth()->user()->name }}!</h1>
            <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                <ol class="breadcrumb pt-0">
                    <li class="breadcrumb-item">
                        <a href="#">Home</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
            </nav>
            <div class="separator mb-5"></div>
        </div>
    </div>

    @role(['admin', 'super-admin'])
    <div class="row sortable">
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="iconsminds-student-male-female text-primary" style="font-size: 32px;"></i>
                    <p class="card-text mb-0">Total Siswa</p>
                    <p class="lead text-center">{{ $totalSiswa }}</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="iconsminds-male text-primary" style="font-size: 32px;"></i>
                    <p class="card-text mb-0">Total Guru</p>
                    <p class="lead text-center">{{ $totalGuru }}</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="iconsminds-door text-primary" style="font-size: 32px;"></i>
                    <p class="card-text mb-0">Total Kelas</p>
                    <p class="lead text-center">{{ $totalKelas }}</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="iconsminds-bell text-primary" style="font-size: 32px;"></i>
                    <p class="card-text mb-0">Notifikasi</p>
                    <p class="lead text-center">{{ $unreadCount }}</p>
                </div>
            </div>
        </div>
    </div>
    @endrole

    @role('guru')
    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Jadwal Hari Ini</h5>
                    <div class="scroll dashboard-list-with-user">
                        @forelse($todaySchedules as $schedule)
                        <div class="d-flex flex-row mb-3 pb-3 border-bottom">
                            <div class="pl-3">
                                <a href="#">
                                    <p class="font-weight-medium mb-0">{{ $schedule->mataPelajaranKelas->mataPelajaran->nama }}</p>
                                    <p class="text-muted mb-0 text-small">{{ $schedule->jam_mulai->format('H:i') }} - {{ $schedule->jam_selesai->format('H:i') }} | Kelas {{ $schedule->mataPelajaranKelas->kelas->nama }}</p>
                                </a>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted text-center py-4">Tidak ada jadwal hari ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body text-center py-5">
                    <h5 class="card-title">Tugas Belum Dinilai</h5>
                    <p class="display-3 font-weight-bold text-primary">{{ $pendingGrades }}</p>
                    <a href="{{ route('elearning.index') }}" class="btn btn-primary">LIHAT E-LEARNING</a>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Tugas Terbaru</h5>
                    <ul class="list-unstyled mb-0">
                        @foreach($recentTasks as $task)
                        <li class="mb-2">
                            <a href="{{ route('tugas.show', $task->id) }}" class="d-flex align-items-center">
                                <i class="simple-icon-doc mr-2"></i>
                                <span>{{ $task->judul }}</span>
                            </a>
                            <small class="text-muted d-block">{{ $task->created_at->diffForHumans() }}</small>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endrole

    @role('siswa')
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Jadwal Pelajaran Hari Ini</h5>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Jam</th>
                                <th>Mata Pelajaran</th>
                                <th>Guru</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($todaySchedules as $schedule)
                            <tr>
                                <td>{{ $schedule->jam_mulai->format('H:i') }}</td>
                                <td>{{ $schedule->mataPelajaranKelas->mataPelajaran->nama }}</td>
                                <td>{{ $schedule->mataPelajaranKelas->guru->nama }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">Libur! Tidak ada jadwal.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
             <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title text-danger">Deadline Tugas Mendatang</h5>
                    <ul class="list-group list-group-flush">
                        @forelse($upcomingDeadlines as $task)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $task->judul }}</strong><br>
                                <small>{{ $task->mataPelajaranKelas->mataPelajaran->nama }}</small>
                            </div>
                            <span class="badge badge-pill badge-danger">{{ $task->tanggal_deadline->format('d M') }}</span>
                        </li>
                        @empty
                        <li class="list-group-item text-center">Yeay! Tidak ada tugas menumpuk.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endrole

    @role('orang-tua')
    <div class="row">
        @forelse($children as $child)
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <img src="{{ asset('assets/img/profiles/l-2.jpg') }}" class="img-thumbnail border-0 rounded-circle mb-3 list-thumbnail" />
                        <h5 class="mb-1">{{ $child['siswa']->nama }}</h5>
                        <p class="text-muted">{{ $child['siswa']->nisn }} | Kelas {{ $child['siswa']->kelas->first()->nama ?? 'N/A' }}</p>
                    </div>
                    
                    <div class="separator mb-3"></div>
                    
                    <h6>Kehadiran Semester Ini</h6>
                    <div class="progress mb-3" style="height: 10px;">
                        @php
                            $total = $child['attendanceSummary']['hadir'] + $child['attendanceSummary']['absen'];
                            $percent = $total > 0 ? ($child['attendanceSummary']['hadir'] / $total) * 100 : 0;
                        @endphp
                        <div class="progress-bar" role="progressbar" style="width: {{ $percent }}%" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <span>Hadir: {{ $child['attendanceSummary']['hadir'] }}</span>
                        <span>Absen: {{ $child['attendanceSummary']['absen'] }}</span>
                    </div>

                    <h6>Nilai Terbaru</h6>
                    <ul class="list-unstyled mb-0">
                        @forelse($child['recentGrades'] as $grade)
                        <li class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $grade->mataPelajaranKelas->mataPelajaran->nama }}</span>
                            <span class="badge badge-pill badge-outline-primary">{{ $grade->nilai }}</span>
                        </li>
                        @empty
                        <li class="text-muted text-center small">Belum ada nilai terinput.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <p class="text-muted mb-0">Data anak tidak ditemukan. Silakan hubungi admin.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
    @endrole

</div>
@endsection
