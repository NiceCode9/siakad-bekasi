@extends('layouts.app')

@section('title', 'Detail Jadwal Pelajaran')

@section('content')

    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Detail Jadwal Pelajaran</h4>
            <div>
                <a href="{{ route('jadwal-pelajaran.edit', $jadwalPelajaran->id) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('jadwal-pelajaran.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Info Jadwal -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-calendar-alt"></i> Informasi Jadwal</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td width="35%"><strong>Hari</strong></td>
                                <td>:
                                    @php
                                        $colors = [
                                            'Senin' => 'primary',
                                            'Selasa' => 'success',
                                            'Rabu' => 'warning',
                                            'Kamis' => 'info',
                                            'Jumat' => 'danger',
                                            'Sabtu' => 'secondary',
                                        ];
                                        $color = $colors[$jadwalPelajaran->hari] ?? 'primary';
                                    @endphp
                                    <span class="badge badge-{{ $color }}">{{ $jadwalPelajaran->hari }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Waktu</strong></td>
                                <td>: <strong>{{ $jadwalPelajaran->jam_mulai->format('H:i') }} -
                                        {{ $jadwalPelajaran->jam_selesai->format('H:i') }}</strong></td>
                            </tr>
                            <tr>
                                <td><strong>Durasi</strong></td>
                                <td>: {{ $jadwalPelajaran->jam_mulai->diffInMinutes($jadwalPelajaran->jam_selesai) }} menit
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Ruang</strong></td>
                                <td>: {{ $jadwalPelajaran->ruang ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fas fa-school"></i> Informasi Kelas</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td width="35%"><strong>Kelas</strong></td>
                                <td>: {{ $jadwalPelajaran->mataPelajaranKelas->kelas->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tingkat</strong></td>
                                <td>: {{ $jadwalPelajaran->mataPelajaranKelas->kelas->tingkat ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Jurusan</strong></td>
                                <td>:
                                    {{ $jadwalPelajaran->mataPelajaranKelas->kelas->jurusan->nama ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Wali Kelas</strong></td>
                                <td>:
                                    {{ $jadwalPelajaran->mataPelajaranKelas->kelas->waliKelas->nama_lengkap ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Semester</strong></td>
                                <td>:
                                    {{ $jadwalPelajaran->mataPelajaranKelas->kelas->semester->nama ?? '-' }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-book"></i> Informasi Mata Pelajaran</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td width="35%"><strong>Kode</strong></td>
                                <td>:
                                    {{ $jadwalPelajaran->mataPelajaranKelas->mataPelajaran->kode ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Nama</strong></td>
                                <td>:
                                    {{ $jadwalPelajaran->mataPelajaranKelas->mataPelajaran->nama ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Jenis</strong></td>
                                <td>:
                                    @if ($jadwalPelajaran->mataPelajaranKelas->mataPelajaran->jenis == 'umum')
                                        <span class="badge badge-primary">Umum</span>
                                    @elseif($jadwalPelajaran->mataPelajaranKelas->mataPelajaran->jenis == 'kejuruan')
                                        <span class="badge badge-success">Kejuruan</span>
                                    @else
                                        <span class="badge badge-info">Muatan Lokal</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>KKM</strong></td>
                                <td>:
                                    {{ $jadwalPelajaran->mataPelajaranKelas->mataPelajaran->kkm ?? '-' }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <h6 class="mb-0"><i class="fas fa-user-tie"></i> Informasi Guru</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td width="35%"><strong>NIP</strong></td>
                                <td>: {{ $jadwalPelajaran->mataPelajaranKelas->guru->nip ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nama</strong></td>
                                <td>: {{ $jadwalPelajaran->mataPelajaranKelas->guru->nama_lengkap ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email</strong></td>
                                <td>: {{ $jadwalPelajaran->mataPelajaranKelas->guru->email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Telepon</strong></td>
                                <td>: {{ $jadwalPelajaran->mataPelajaranKelas->guru->telepon ?? '-' }}</td>
                            </tr>

                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-history"></i> Riwayat</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="35%">Dibuat</td>
                                <td>: {{ $jadwalPelajaran->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td>Diupdate</td>
                                <td>: {{ $jadwalPelajaran->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
        });
    </script>
@endpush
