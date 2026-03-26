@extends('layouts.app')

@section('title', 'Statistik Akademik')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>Statistik Akademik - {{ $semesterAktif->tahunAkademik->nama }} ({{ $semesterAktif->nama }})</h1>
            <div class="separator mb-5"></div>
        </div>
    </div>

    <div class="row">
        <!-- 1. Rata-rata Nilai per Kelas -->
        <div class="col-md-8 col-sm-12 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Rata-rata Nilai per Kelas</h5>
                    <div class="dashboard-line-chart">
                        <canvas id="chartAverageGrades"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Statistik Presensi Global -->
        <div class="col-md-4 col-sm-12 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Persentase Kehadiran Siswa</h5>
                    <div class="dashboard-donut-chart">
                        <canvas id="chartAttendance"></canvas>
                    </div>
                    <div class="mt-4">
                        @foreach($attendanceStats as $stat)
                        <div class="mb-2">
                            <span class="badge" style="background-color: {{ $stat['color'] }}">&nbsp;</span>
                            <span class="ml-2">{{ $stat['label'] }}: <strong>{{ $stat['value'] }}</strong></span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

        </div>
    </div>

    <div class="row">
        <!-- 3. Progres Jurnal per Guru -->
        <div class="col-md-6 col-sm-12 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Progres Jurnal Mengajar (Top 10 Guru)</h5>
                    <div class="dashboard-line-chart">
                        <canvas id="chartJournalProgress"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Monitoring Capaian Kurikulum -->
        <div class="col-md-6 col-sm-12 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Capaian Kurikulum per Kelas (%)</h5>
                    <div class="dashboard-line-chart">
                        <canvas id="chartCurriculumProgress"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/vendor/Chart.bundle.min.js') }}"></script>
<script>
    $(document).ready(function() {
        // 1. Chart Average Grades
        const ctxGrades = document.getElementById('chartAverageGrades').getContext('2d');
        new Chart(ctxGrades, {
            type: 'bar',
            data: {
                labels: {!! json_encode($averageGrades->pluck('label')) !!},
                datasets: [{
                    label: 'Rata-rata Nilai',
                    data: {!! json_encode($averageGrades->pluck('value')) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            max: 100
                        }
                    }]
                }
            }
        });

        // 2. Chart Attendance
        const ctxAttendance = document.getElementById('chartAttendance').getContext('2d');
        new Chart(ctxAttendance, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($attendanceStats->pluck('label')) !!},
                datasets: [{
                    data: {!! json_encode($attendanceStats->pluck('value')) !!},
                    backgroundColor: {!! json_encode($attendanceStats->pluck('color')) !!},
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: false
                }
            }
        });

        // 3. Chart Journal Progress
        const ctxJournal = document.getElementById('chartJournalProgress').getContext('2d');
        new Chart(ctxJournal, {
            type: 'horizontalBar',
            data: {
                labels: {!! json_encode($journalProgress->pluck('label')) !!},
                datasets: [{
                    label: 'Jumlah Jurnal',
                    data: {!! json_encode($journalProgress->pluck('value')) !!},
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    xAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        // 4. Chart Curriculum Progress
        const ctxCurriculum = document.getElementById('chartCurriculumProgress').getContext('2d');
        new Chart(ctxCurriculum, {
            type: 'bar',
            data: {
                labels: {!! json_encode($curriculumProgress->pluck('label')) !!},
                datasets: [{
                    label: 'Progres Capaian (%)',
                    data: {!! json_encode($curriculumProgress->pluck('value')) !!},
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            max: 100
                        }
                    }]
                }
            }
        });
    });
</script>
@endpush
