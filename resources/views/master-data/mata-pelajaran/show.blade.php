@extends('layouts.app')

@section('title', 'Detail Mata Pelajaran - ' . $mataPelajaran->nama)

@section('content')

    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Detail Mata Pelajaran: {{ $mataPelajaran->nama }}</h4>
            <div>
                <a href="{{ route('mata-pelajaran.edit', $mataPelajaran->id) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('mata-pelajaran.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Statistik Cards -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Total Kelas</h6>
                                <h3 class="mb-0">{{ $stats['total_kelas'] }}</h3>
                            </div>
                            <div>
                                <i class="fas fa-school fa-3x opacity-50"></i>
                            </div>
                        </div>
                        <small>Kelas yang menggunakan mata pelajaran ini</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Bank Soal</h6>
                                <h3 class="mb-0">{{ $stats['total_bank_soal'] }}</h3>
                            </div>
                            <div>
                                <i class="fas fa-file-alt fa-3x opacity-50"></i>
                            </div>
                        </div>
                        <small>Total bank soal tersedia</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card {{ $mataPelajaran->is_active ? 'bg-success' : 'bg-secondary' }} text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Status</h6>
                                <h3 class="mb-0">{{ $mataPelajaran->is_active ? 'Aktif' : 'Nonaktif' }}</h3>
                            </div>
                            <div>
                                <i
                                    class="fas fa-{{ $mataPelajaran->is_active ? 'check-circle' : 'times-circle' }} fa-3x opacity-50"></i>
                            </div>
                        </div>
                        <small>Status mata pelajaran</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Info Mata Pelajaran -->
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="mb-3 card-title font-weight-bold">Informasi Mata Pelajaran</h5>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Kode</strong></td>
                                <td>: {{ $mataPelajaran->kode }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nama</strong></td>
                                <td>: {{ $mataPelajaran->nama }}</td>
                            </tr>
                            <tr>
                                <td><strong>Kurikulum</strong></td>
                                <td>: {{ $mataPelajaran->kurikulum->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Kelompok Mapel</strong></td>
                                <td>: {{ $mataPelajaran->kelompokMapel->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Jenis</strong></td>
                                <td>:
                                    @if ($mataPelajaran->jenis == 'umum')
                                        <span class="badge badge-primary">Umum</span>
                                    @elseif($mataPelajaran->jenis == 'kejuruan')
                                        <span class="badge badge-success">Kejuruan</span>
                                    @else
                                        <span class="badge badge-info">Muatan Lokal</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Kategori</strong></td>
                                <td>:
                                    @if ($mataPelajaran->kategori == 'wajib')
                                        <span class="badge badge-danger">Wajib</span>
                                    @elseif($mataPelajaran->kategori == 'peminatan')
                                        <span class="badge badge-warning">Peminatan</span>
                                    @else
                                        <span class="badge badge-secondary">Lintas Minat</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>KKM</strong></td>
                                <td>: {{ $mataPelajaran->kkm }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>:
                                    @if ($mataPelajaran->is_active)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-secondary">Nonaktif</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="mb-3 card-title font-weight-bold">Riwayat</h5>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%">Dibuat</td>
                                <td>: {{ $mataPelajaran->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td>Diupdate</td>
                                <td>: {{ $mataPelajaran->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="mb-3 card-title font-weight-bold">Aksi Cepat</h5>
                        <button type="button" class="btn btn-primary btn-block btn-sm mb-2" data-toggle="modal"
                            data-target="#assignKeKelasModal">
                            <i class="fas fa-plus-circle"></i> Tugaskan ke Kelas
                        </button>
                        @if ($mataPelajaran->is_active)
                            <button type="button" class="btn btn-secondary btn-block btn-sm" id="btnToggleStatus">
                                <i class="fas fa-toggle-on"></i> Nonaktifkan
                            </button>
                        @else
                            <button type="button" class="btn btn-success btn-block btn-sm" id="btnToggleStatus">
                                <i class="fas fa-toggle-off"></i> Aktifkan
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Daftar Kelas -->
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="mb-3 card-title font-weight-bold">Daftar Kelas yang Menggunakan Mata Pelajaran Ini</h5>
                        @if ($mataPelajaran->mataPelajaranKelas->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Kelas</th>
                                            <th>Semester</th>
                                            <th>Guru Pengampu</th>
                                            <th>Jam/Minggu</th>
                                            <th width="10%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($mataPelajaran->mataPelajaranKelas as $mpk)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <a href="{{ route('kelas.show', $mpk->kelas->id) }}">
                                                        {{ $mpk->kelas->nama }}
                                                    </a>
                                                </td>
                                                <td>{{ $mpk->kelas->semester->nama ?? '-' }}</td>
                                                <td>{{ $mpk->guru->nama_lengkap ?? '-' }}</td>
                                                <td>{{ $mpk->jam_per_minggu ?? '-' }} jam</td>
                                                <td>
                                                    <a href="{{ route('kelas.show', $mpk->kelas->id) }}"
                                                        class="btn btn-info btn-sm" title="Lihat Kelas">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle"></i> Mata pelajaran ini belum ditugaskan ke kelas manapun.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Assign ke Kelas -->
    <div class="modal fade" id="assignKeKelasModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('mata-pelajaran.assign-to-kelas', $mataPelajaran->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tugaskan ke Kelas</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Pilih Kelas <span class="text-danger">*</span></label>
                            <select name="kelas_id[]" class="form-control" multiple size="8" required>
                                @foreach (\App\Models\Kelas::with('semester')->orderBy('nama')->get() as $kelas)
                                    <option value="{{ $kelas->id }}">
                                        {{ $kelas->nama }} - {{ $kelas->semester->nama ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Tekan Ctrl untuk memilih lebih dari satu</small>
                        </div>

                        <div class="form-group">
                            <label>Guru Pengampu <span class="text-danger">*</span></label>
                            <select name="guru_id" class="form-control" required>
                                <option value="">-- Pilih Guru --</option>
                                @foreach (\App\Models\Guru::active()->orderBy('nama_lengkap')->get() as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->nama_lengkap }} ({{ $guru->nip }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Jam per Minggu <span class="text-danger">*</span></label>
                            <input type="number" name="jam_per_minggu" class="form-control" min="1"
                                max="20" required>
                            <small class="form-text text-muted">Jumlah jam pelajaran per minggu</small>
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

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Toggle Status
            $('#btnToggleStatus').click(function() {
                Swal.fire({
                    title: 'Ubah Status?',
                    text: "Status mata pelajaran akan diubah!",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, ubah!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('mata-pelajaran.toggle-active', $mataPelajaran->id) }}",
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Gagal!',
                                    xhr.responseJSON.message,
                                    'error'
                                );
                            }
                        });
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
