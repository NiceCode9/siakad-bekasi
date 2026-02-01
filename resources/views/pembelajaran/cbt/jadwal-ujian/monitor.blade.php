@extends('layouts.app')

@section('title', 'Monitoring Ujian')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Monitor: {{ $jadwalUjian->nama_ujian }}</h4>
            <p class="text-muted mb-0">{{ $jadwalUjian->mataPelajaranKelas->kelas->nama }} | {{ $jadwalUjian->mataPelajaranKelas->mataPelajaran->nama }}</p>
        </div>
        <div>
            <span class="badge badge-info p-2" id="autoRefreshBadge">Auto Refresh: On (10s)</span>
            <a href="{{ route('jadwal-ujian.show', $jadwalUjian->id) }}" class="btn btn-secondary btn-sm ml-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-primary text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Total Siswa</h6>
                    <h2 class="mb-0" id="stat-total">{{ count($data) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-warning text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Sedang Mengerjakan</h6>
                    <h2 class="mb-0" id="stat-mengerjakan">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-success text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Selesai</h6>
                    <h2 class="mb-0" id="stat-selesai">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-secondary text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Belum Mulai</h6>
                    <h2 class="mb-0" id="stat-belum">0</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="monitorTable">
                    <thead>
                        <tr>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Status</th>
                            <th>Mulai</th>
                            <th>Selesai</th>
                            <th>Pelanggaran</th>
                            <th>Nilai</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="monitorBody">
                        @foreach($data as $s)
                            <tr id="row-{{ $s['id'] }}">
                                <td>{{ $s['nis'] }}</td>
                                <td><strong>{{ $s['nama'] }}</strong></td>
                                <td class="status-cell">
                                    {!! getStatusBadge($s['status']) !!}
                                </td>
                                <td>{{ $s['waktu_mulai'] }}</td>
                                <td>{{ $s['waktu_submit'] }}</td>
                                <td>
                                    @if($s['pelanggaran'] > 0)
                                        <span class="badge badge-danger">{{ $s['pelanggaran'] }}</span>
                                    @else
                                        0
                                    @endif
                                </td>
                                <td class="font-weight-bold text-primary">{{ $s['nilai'] }}</td>
                                <td class="text-right">
                                    @if($s['ujian_siswa_id'])
                                        <a href="{{ route('ujian-siswa.review', $s['ujian_siswa_id']) }}" class="btn btn-outline-info btn-xs">Detail</a>
                                    @else
                                        <button class="btn btn-outline-secondary btn-xs" disabled>Detail</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@php
function getStatusBadge($status) {
    $badges = [
        'belum_mulai' => '<span class="badge badge-secondary">Belum Mulai</span>',
        'sedang_mengerjakan' => '<span class="badge badge-warning">Mengerjakan</span>',
        'selesai' => '<span class="badge badge-success">Selesai</span>',
    ];
    return $badges[$status] ?? $status;
}
@endphp
@endsection

@push('scripts')
<script>
    function refreshData() {
        $.ajax({
            url: window.location.href,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                // Update Stats
                $('#stat-total').text(response.stats.total);
                $('#stat-mengerjakan').text(response.stats.mengerjakan);
                $('#stat-selesai').text(response.stats.selesai);
                $('#stat-belum').text(response.stats.belum);

                // Update Table
                let html = '';
                response.data.forEach(s => {
                    let statusBadge = '';
                    if(s.status == 'belum_mulai') statusBadge = '<span class="badge badge-secondary">Belum Mulai</span>';
                    else if(s.status == 'sedang_mengerjakan') statusBadge = '<span class="badge badge-warning">Mengerjakan</span>';
                    else if(s.status == 'selesai') statusBadge = '<span class="badge badge-success">Selesai</span>';

                    let violationBadge = s.pelanggaran > 0 ? `<span class="badge badge-danger">${s.pelanggaran}</span>` : '0';

                    html += `
                        <tr id="row-${s.id}">
                            <td>${s.nis}</td>
                            <td><strong>${s.nama}</strong></td>
                            <td class="status-cell">${statusBadge}</td>
                            <td>${s.waktu_mulai}</td>
                            <td>${s.waktu_submit}</td>
                            <td>${violationBadge}</td>
                            <td class="font-weight-bold text-primary">${s.nilai}</td>
                            <td class="text-right">
                                ${s.ujian_siswa_id ? `<a href="{{ url('ujian-siswa/review') }}/${s.ujian_siswa_id}" class="btn btn-outline-info btn-xs">Detail</a>` : `<button class="btn btn-outline-secondary btn-xs" disabled>Detail</button>`}
                            </td>
                        </tr>
                    `;
                });
                $('#monitorBody').html(html);
            }
        });
    }

    // Refresh every 10 seconds
    setInterval(refreshData, 10000);
    $(document).ready(refreshData); // Initial load to fix stats
</script>
@endpush
