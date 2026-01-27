@extends('layouts.app')

@section('title', 'Jadwal Mengajar - ' . $guru->nama_lengkap)

@section('content')

    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0">Jadwal Mengajar: {{ $guru->nama_lengkap }}</h4>
                <small class="text-muted">NIP: {{ $guru->nip ?? '-' }}</small>
            </div>
            <div>
                <button onclick="window.print()" class="btn btn-success btn-sm">
                    <i class="fas fa-print"></i> Cetak
                </button>
                <a href="{{ route('jadwal-pelajaran.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Jadwal Per Hari -->
        @php
            $allHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $colors = [
                'Senin' => 'primary',
                'Selasa' => 'success',
                'Rabu' => 'warning',
                'Kamis' => 'info',
                'Jumat' => 'danger',
                'Sabtu' => 'secondary',
            ];
        @endphp

        @foreach ($allHari as $hari)
            <div class="card mb-3">
                <div class="card-header bg-{{ $colors[$hari] }} text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-day"></i> {{ $hari }}</h5>
                </div>
                <div class="card-body">
                    @if (isset($jadwalPerHari[$hari]) && $jadwalPerHari[$hari]->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="15%">Waktu</th>
                                        <th width="30%">Mata Pelajaran</th>
                                        <th width="25%">Kelas</th>
                                        <th width="15%">Ruang</th>
                                        <th width="15%" class="no-print">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($jadwalPerHari[$hari] as $jadwal)
                                        <tr>
                                            <td>
                                                <strong>{{ $jadwal->jam_mulai->format('H:i') }}</strong> -
                                                <strong>{{ $jadwal->jam_selesai->format('H:i') }}</strong>
                                                <br>
                                                <small
                                                    class="text-muted">{{ $jadwal->jam_mulai->diffInMinutes($jadwal->jam_selesai) }}
                                                    menit</small>
                                            </td>
                                            <td>
                                                <strong>{{ $jadwal->mataPelajaranGuru->mataPelajaranKelas->mataPelajaran->nama ?? '-' }}</strong>
                                                <br>
                                                <small
                                                    class="text-muted">{{ $jadwal->mataPelajaranGuru->mataPelajaranKelas->mataPelajaran->kode ?? '-' }}</small>
                                            </td>
                                            <td>
                                                <span class="badge badge-info" style="font-size: 1rem;">
                                                    {{ $jadwal->mataPelajaranGuru->mataPelajaranKelas->kelas->nama ?? '-' }}
                                                </span>
                                            </td>
                                            <td>{{ $jadwal->ruang ?? '-' }}</td>
                                            <td class="no-print">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('jadwal-pelajaran.show', $jadwal->id) }}"
                                                        class="btn btn-info btn-sm" title="Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle"></i> Tidak ada jadwal untuk hari {{ $hari }}
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

@endsection

@push('styles')
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            .card {
                page-break-inside: avoid;
                border: 1px solid #ddd !important;
            }

            .btn,
            .sidebar,
            .navbar {
                display: none !important;
            }
        }
    </style>
@endpush
