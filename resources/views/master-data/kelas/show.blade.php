@extends('layouts.app')

@section('title', 'Detail Kelas - ' . $kelas->nama)

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
@endpush

@section('content')

    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Detail Kelas: {{ $kelas->nama }}</h4>
            <div>
                <a href="{{ route('kelas.edit', $kelas->id) }}" class="btn btn-warning btn-sm">
                    <i class="simple-icon-pencil"></i> Edit
                </a>
                <a href="{{ route('kelas.index') }}" class="btn btn-secondary btn-sm">
                    <i class="simple-icon-arrow-left-circle"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Statistik Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-2">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Total Siswa</h6>
                                <h3 class="mb-0">{{ $stats['total_siswa'] }}</h3>
                            </div>
                            <div>
                                <i class="fas fa-users fa-3x opacity-50"></i>
                            </div>
                        </div>
                        <small>dari {{ $kelas->kuota }} kuota</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Siswa Laki-laki</h6>
                                <h3 class="mb-0">{{ $stats['total_laki'] }}</h3>
                            </div>
                            <div>
                                <i class="fas fa-male fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Siswa Perempuan</h6>
                                <h3 class="mb-0">{{ $stats['total_perempuan'] }}</h3>
                            </div>
                            <div>
                                <i class="fas fa-female fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Mata Pelajaran</h6>
                                <h3 class="mb-0">{{ $stats['total_mapel'] }}</h3>
                            </div>
                            <div>
                                <i class="fas fa-book fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Info Kelas -->
            <div class="col-md-4">
                <div class="card mb-5">
                    <div class="card-body">
                        <h5 class="card-title">Informasi Kelas</h5>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Kode</strong></td>
                                <td>: {{ $kelas->kode }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nama</strong></td>
                                <td>: {{ $kelas->nama }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tingkat</strong></td>
                                <td>: {{ $kelas->tingkat }}</td>
                            </tr>
                            <tr>
                                <td><strong>Jurusan</strong></td>
                                <td>: {{ $kelas->jurusan->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Semester</strong></td>
                                <td>: {{ $kelas->semester->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tahun Akademik</strong></td>
                                <td>: {{ $kelas->semester->tahunAkademik->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Kuota</strong></td>
                                <td>: {{ $kelas->kuota }} siswa</td>
                            </tr>
                            <tr>
                                <td><strong>Sisa Kuota</strong></td>
                                <td>: {{ $stats['sisa_kuota'] }} siswa</td>
                            </tr>
                            <tr>
                                <td><strong>Ruang Kelas</strong></td>
                                <td>: {{ $kelas->ruang_kelas ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Wali Kelas -->
                <div class="card">
                    <div class="position-absolute card-top-buttons">
                        {{-- <button class="btn btn-header-light icon-button">
                            <i class="simple-icon-refresh"></i>
                        </button> --}}
                        @if ($kelas->wali_kelas_id)
                            <button type="button" class="btn btn-danger btn-sm mt-1" id="btnRemoveWaliKelas">
                                <i class="fas fa-times"></i>
                            </button>
                        @else
                            <button type="button" class="btn btn-primary btn-sm mt-1" data-toggle="modal"
                                data-target="#assignWaliKelasModal">
                                <i class="fas fa-plus"></i>
                            </button>
                        @endif
                    </div>
                    {{-- <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Wali Kelas</h6>
                        @if ($kelas->wali_kelas_id)
                            <button type="button" class="btn btn-danger btn-sm mt-1" id="btnRemoveWaliKelas">
                                <i class="fas fa-times"></i>
                            </button>
                        @else
                            <button type="button" class="btn btn-primary btn-sm mt-1" data-toggle="modal"
                                data-target="#assignWaliKelasModal">
                                <i class="fas fa-plus"></i>
                            </button>
                        @endif
                    </div> --}}
                    <div class="card-body">
                        <h5 class="card-title">Wali Kelas</h5>
                        @if ($kelas->waliKelas)
                            <div class="media">
                                <div class="media-body">
                                    <h6 class="mb-1">{{ $kelas->waliKelas->nama_lengkap }}</h6>
                                    <p class="mb-0 text-muted">NIP: {{ $kelas->waliKelas->nip }}</p>
                                    <p class="mb-0 text-muted">{{ $kelas->waliKelas->email ?? '-' }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-muted mb-0">Belum ada wali kelas</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Daftar Siswa & Mata Pelajaran -->
            <div class="col-md-8">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" id="kelasTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="siswa-tab" data-toggle="tab" href="#siswa" role="tab">
                            <i class="fas fa-users"></i> Daftar Siswa ({{ $stats['total_siswa'] }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="mapel-tab" data-toggle="tab" href="#mapel" role="tab">
                            <i class="fas fa-book"></i> Mata Pelajaran ({{ $stats['total_mapel'] }})
                        </a>
                    </li>
                </ul>

                <!-- Tab content -->
                <div class="tab-content">
                    <!-- Tab Siswa -->
                    <div class="tab-pane fade show active" id="siswa" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-hover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th>NISN</th>
                                                <th>NIS</th>
                                                <th>Nama Lengkap</th>
                                                <th>JK</th>
                                                <th>Tanggal Masuk</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($kelas->siswaKelas->where('status', 'aktif') as $sk)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $sk->siswa->nisn ?? '-' }}</td>
                                                    <td>{{ $sk->siswa->nis ?? '-' }}</td>
                                                    <td>{{ $sk->siswa->nama_lengkap ?? '-' }}</td>
                                                    <td>
                                                        @if ($sk->siswa->jenis_kelamin == 'L')
                                                            <span class="badge badge-info">L</span>
                                                        @else
                                                            <span class="badge badge-danger">P</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $sk->tanggal_masuk ? $sk->tanggal_masuk->format('d/m/Y') : '-' }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">Belum ada siswa di kelas ini
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Mata Pelajaran -->
                    <div class="tab-pane fade" id="mapel" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Daftar Mata Pelajaran</h6>
                                    <a href="{{ route('kelas.mata-pelajaran.index', $kelas->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-cog"></i> Kelola Mata Pelajaran
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-hover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th>Kode</th>
                                                <th>Mata Pelajaran</th>
                                                <th>Guru Pengampu</th>
                                                <th>Jenis</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($kelas->mataPelajaranKelas as $mpk)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $mpk->mataPelajaran->kode ?? '-' }}</td>
                                                    <td>{{ $mpk->mataPelajaran->nama ?? '-' }}</td>
                                                    <td>{{ $mpk->guru->nama_lengkap ?? '-' }}</td>
                                                    <td>
                                                        @if ($mpk->mataPelajaran->jenis == 'wajib')
                                                            <span class="badge badge-primary">Wajib</span>
                                                        @else
                                                            <span class="badge badge-warning">Pilihan</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">Belum ada mata pelajaran di
                                                        kelas ini</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Assign Wali Kelas -->
    <div class="modal fade" id="assignWaliKelasModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('kelas.assign-wali-kelas', $kelas->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tugaskan Wali Kelas</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Pilih Guru <span class="text-danger">*</span></label>
                            <select name="wali_kelas_id" class="form-control" required>
                                <option value="">-- Pilih Guru --</option>
                                @foreach (\App\Models\Guru::active()->orderBy('nama_lengkap')->get() as $g)
                                    <option value="{{ $g->id }}">{{ $g->nama_lengkap }} ({{ $g->nip }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Tugaskan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Form Remove Wali Kelas (hidden) -->
    <form id="formRemoveWaliKelas" action="{{ route('kelas.remove-wali-kelas', $kelas->id) }}" method="POST"
        style="display: none;">
        @csrf
        @method('DELETE')
    </form>

@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/js/all.min.js"></script>
    <script>
        $(document).ready(function() {
            // Remove Wali Kelas
            $('#btnRemoveWaliKelas').click(function() {
                Swal.fire({
                    title: 'Hapus Wali Kelas?',
                    text: "Wali kelas akan dihapus dari kelas ini!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#formRemoveWaliKelas').submit();
                    }
                });
            });

            // Show success/error message from session
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
