@extends('layouts.app')

@section('title', 'Detail Jadwal Ujian')

@push('styles')
<style>
    .token-box {
        background-color: #f8f9fa;
        border: 2px dashed #007bff;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }
    .token-box:hover {
        background-color: #e9ecef;
        border-color: #0056b3;
    }
    .token-text {
        font-family: 'Courier New', Courier, monospace;
        font-size: 2.5rem;
        font-weight: 700;
        letter-spacing: 5px;
        color: #007bff;
        margin: 0;
    }
    .info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .info-list li {
        padding: 10px 0;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .info-list li:last-child {
        border-bottom: none;
    }
    .info-label {
        color: #6c757d;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
    }
    .info-label i {
        width: 25px;
        color: #adb5bd;
    }
    .info-value {
        font-weight: 600;
        color: #343a40;
        text-align: right;
    }
    .status-form .btn-group {
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .soal-table th {
        background-color: #f4f6f9;
        color: #495057;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        border-top: none;
    }
    .soal-table td {
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div class="mb-3 mb-md-0">
            <h3 class="mb-1 font-weight-bold text-dark">{{ $jadwalUjian->nama_ujian }}</h3>
            <div class="d-flex align-items-center">
                <span class="badge badge-{{ $jadwalUjian->status == 'aktif' ? 'success' : ($jadwalUjian->status == 'draft' ? 'secondary' : 'dark') }} px-3 py-1 mr-2" style="font-size: 0.85rem;">
                    <i class="fas fa-dot-circle mr-1"></i> {{ strtoupper($jadwalUjian->status) }}
                </span>
                <span class="text-muted small">ID: #{{ str_pad($jadwalUjian->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>
        <div>
            @if($jadwalUjian->status == 'aktif')
                <a href="{{ route('jadwal-ujian.monitor', $jadwalUjian->id) }}" class="btn btn-dark shadow-sm mr-2">
                    <i class="fas fa-desktop mr-1"></i> Monitoring Live
                </a>
            @endif
            <a href="{{ route('jadwal-ujian.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar Content -->
        <div class="col-lg-4 col-xl-3 mb-4">

            <!-- Token Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase font-weight-bold mb-3 d-flex justify-content-between align-items-center">
                        Token Ujian
                        <i class="fas fa-key text-primary"></i>
                    </h6>
                    <div class="token-box">
                        <p class="token-text">{{ $jadwalUjian->token }}</p>
                    </div>
                    <p class="small text-muted text-center mb-0">
                        <i class="fas fa-info-circle mr-1"></i> Bagikan token ini kepada siswa untuk memulai ujian.
                    </p>
                </div>
            </div>

            <!-- Informasi Ujian Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h6 class="text-dark font-weight-bold mb-0">Informasi Ujian</h6>
                </div>
                <div class="card-body">
                    <ul class="info-list">
                        <li>
                            <span class="info-label"><i class="fas fa-users"></i> Kelas</span>
                            <span class="info-value">{{ $jadwalUjian->mataPelajaranKelas->kelas->nama }}</span>
                        </li>
                        <li>
                            <span class="info-label"><i class="fas fa-book"></i> Mapel</span>
                            <span class="info-value">{{ $jadwalUjian->mataPelajaranKelas->mataPelajaran->nama }}</span>
                        </li>
                        <li>
                            <span class="info-label"><i class="fas fa-database"></i> Bank Soal</span>
                            <span class="info-value">
                                @if($jadwalUjian->bankSoal)
                                    <span class="text-primary">{{ $jadwalUjian->bankSoal->kode }}</span>
                                @else
                                    <span class="text-danger font-italic small">Belum Dikaitkan</span>
                                @endif
                            </span>
                        </li>
                        <li>
                            <span class="info-label"><i class="fas fa-hourglass-half"></i> Durasi</span>
                            <span class="info-value">{{ $jadwalUjian->durasi }} Menit</span>
                        </li>
                        <li>
                            <span class="info-label"><i class="fas fa-calendar-alt"></i> Mulai</span>
                            <span class="info-value text-right">
                                {{ $jadwalUjian->tanggal_mulai->format('d M Y') }}<br>
                                <small class="text-muted">{{ $jadwalUjian->tanggal_mulai->format('H:i') }} WIB</small>
                            </span>
                        </li>
                        <li>
                            <span class="info-label"><i class="fas fa-calendar-check"></i> Selesai</span>
                            <span class="info-value text-right">
                                {{ $jadwalUjian->tanggal_selesai->format('d M Y') }}<br>
                                <small class="text-muted">{{ $jadwalUjian->tanggal_selesai->format('H:i') }} WIB</small>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Kontrol Status Card -->
            <div class="card shadow-sm border-0 border-left-primary">
                <div class="card-body">
                    <h6 class="text-dark font-weight-bold mb-3"><i class="fas fa-sliders-h mr-2"></i> Kontrol Status</h6>
                    <form action="{{ route('jadwal-ujian.status', $jadwalUjian->id) }}" method="POST" class="status-form">
                        @csrf
                        <div class="btn-group btn-block" role="group">
                            <button type="submit" name="status" value="draft" class="btn btn-outline-secondary {{ $jadwalUjian->status == 'draft' ? 'active font-weight-bold' : '' }}">
                                Draft
                            </button>
                            <button type="submit" name="status" value="aktif" class="btn btn-outline-success {{ $jadwalUjian->status == 'aktif' ? 'active font-weight-bold' : '' }}">
                                Aktif
                            </button>
                            <button type="submit" name="status" value="selesai" class="btn btn-outline-dark {{ $jadwalUjian->status == 'selesai' ? 'active font-weight-bold' : '' }}">
                                Selesai
                            </button>
                        </div>
                    </form>
                    <p class="text-muted small mt-3 mb-0 text-center">Ubah status ujian untuk mengatur akses siswa.</p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-8 col-xl-9">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom pt-4 pb-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="text-dark font-weight-bold mb-0">Daftar Soal Terpilih</h5>
                        <small class="text-muted">Soal-soal ini yang akan dikerjakan oleh siswa.</small>
                    </div>
                    <div>
                        <span class="badge badge-pill badge-primary px-3 py-2 h6 mb-0">
                            Total: {{ $jadwalUjian->soalUjian->count() }} Soal
                        </span>
                        @if($jadwalUjian->status == 'draft')
                        <a href="{{ route('jadwal-ujian.manage-soal', $jadwalUjian->id) }}" class="btn btn-primary btn-sm ml-2 shadow-sm">
                            <i class="fas fa-edit mr-1"></i> Kelola
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($jadwalUjian->soalUjian->count() > 0)
                        <div class="table-responsive" style="max-height: 700px; overflow-y: auto;">
                            <table class="table table-hover soal-table mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="5%">No</th>
                                        <th width="65%">Pertanyaan</th>
                                        <th width="15%">Tipe Soal</th>
                                        <th class="text-center" width="15%">Bobot</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jadwalUjian->soalUjian as $su)
                                        <tr>
                                            <td class="text-center font-weight-bold text-muted">{{ $su->urutan }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($su->soal->tipe_soal == 'pilihan_ganda')
                                                        <i class="fas fa-list-ul text-info mr-3"></i>
                                                    @elseif($su->soal->tipe_soal == 'isian_singkat')
                                                        <i class="fas fa-keyboard text-warning mr-3"></i>
                                                    @else
                                                        <i class="fas fa-align-left text-success mr-3"></i>
                                                    @endif
                                                    <span class="text-dark">{!! Str::limit(strip_tags($su->soal->pertanyaan), 100) !!}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-light border">
                                                    {{ ucwords(str_replace('_', ' ', $su->soal->tipe_soal)) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-pill badge-secondary">{{ $su->soal->bobot }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <img src="{{ asset('img/undraw_empty.svg') }}" alt="Empty" class="img-fluid mb-3" style="max-width: 150px; opacity: 0.5;">
                            <h5 class="text-muted font-weight-normal">Belum ada soal yang ditambahkan.</h5>
                            @if($jadwalUjian->status == 'draft')
                                <p class="text-muted mb-4">Silakan kaitkan bank soal atau pilih soal secara manual.</p>
                                <a href="{{ route('jadwal-ujian.manage-soal', $jadwalUjian->id) }}" class="btn btn-primary px-4 py-2 shadow-sm">
                                    <i class="fas fa-tasks mr-2"></i> Kelola Soal Sekarang
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
