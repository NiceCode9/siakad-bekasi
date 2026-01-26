@extends('layouts.app')

@section('title', 'Detail Guru - ' . $guru->nama_lengkap)

@section('content')

    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Detail Guru: {{ $guru->nama_lengkap_gelar }}</h4>
            <div>
                <button type="button" class="btn btn-warning btn-sm" id="btnEdit" data-id="{{ $guru->id }}">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <a href="{{ route('guru.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Statistik Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Wali Kelas</h6>
                                <h3 class="mb-0">{{ $stats['total_wali_kelas'] }}</h3>
                            </div>
                            <div>
                                <i class="fas fa-chalkboard-teacher fa-3x opacity-50"></i>
                            </div>
                        </div>
                        <small>Total kelas yang diampu sebagai wali kelas</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Mengajar</h6>
                                <h3 class="mb-0">{{ $stats['total_mengajar'] }}</h3>
                            </div>
                            <div>
                                <i class="fas fa-book-reader fa-3x opacity-50"></i>
                            </div>
                        </div>
                        <small>Total mata pelajaran yang diajarkan</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
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
                        <small>Total bank soal yang dibuat</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Biodata -->
            <div class="col-md-4">
                <!-- Foto -->
                <div class="card text-center">
                    <div class="card-body">
                        @if ($guru->foto)
                            <img src="{{ asset('storage/' . $guru->foto) }}" alt="Foto"
                                class="img-fluid rounded-circle mb-3"
                                style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="bg-secondary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                style="width: 150px; height: 150px;">
                                <i class="fas fa-user fa-5x text-white"></i>
                            </div>
                        @endif
                        <h5>{{ $guru->nama_lengkap_gelar }}</h5>
                        <p class="text-muted mb-1">{{ $guru->nip ?? '-' }}</p>
                        @if ($guru->is_active)
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-secondary">Nonaktif</span>
                        @endif
                    </div>
                </div>

                <!-- Info Pribadi -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-id-card"></i> Informasi Pribadi</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="45%"><strong>NIP</strong></td>
                                <td>: {{ $guru->nip ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>NUPTK</strong></td>
                                <td>: {{ $guru->nuptk ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Jenis Kelamin</strong></td>
                                <td>: {{ $guru->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tempat Lahir</strong></td>
                                <td>: {{ $guru->tempat_lahir ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Lahir</strong></td>
                                <td>: {{ $guru->tanggal_lahir ? $guru->tanggal_lahir->format('d/m/Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Agama</strong></td>
                                <td>: {{ $guru->agama ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Info Kontak -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-phone"></i> Informasi Kontak</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="45%"><strong>Email</strong></td>
                                <td>: {{ $guru->email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Telepon</strong></td>
                                <td>: {{ $guru->telepon ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Alamat</strong></td>
                                <td>: {{ $guru->alamat ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Info Kepegawaian -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-briefcase"></i> Informasi Kepegawaian</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="45%"><strong>Status</strong></td>
                                <td>: {{ $guru->status_kepegawaian ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Masuk</strong></td>
                                <td>: {{ $guru->tanggal_masuk ? $guru->tanggal_masuk->format('d/m/Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Masa Kerja</strong></td>
                                <td>:
                                    @if ($guru->tanggal_masuk)
                                        {{ $guru->tanggal_masuk->diffForHumans(null, true) }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Data Mengajar & Wali Kelas -->
            <div class="col-md-8">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" id="guruTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="wali-tab" data-toggle="tab" href="#wali" role="tab">
                            <i class="fas fa-chalkboard-teacher"></i> Wali Kelas ({{ $stats['total_wali_kelas'] }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="mengajar-tab" data-toggle="tab" href="#mengajar" role="tab">
                            <i class="fas fa-book-reader"></i> Mata Pelajaran ({{ $stats['total_mengajar'] }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="akun-tab" data-toggle="tab" href="#akun" role="tab">
                            <i class="fas fa-user-lock"></i> Akun
                        </a>
                    </li>
                </ul>

                <!-- Tab content -->
                <div class="tab-content">
                    <!-- Tab Wali Kelas -->
                    <div class="tab-pane fade show active" id="wali" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                @if ($guru->kelasWali->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered table-hover">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th width="5%">No</th>
                                                    <th>Kelas</th>
                                                    <th>Tingkat</th>
                                                    <th>Semester</th>
                                                    <th>Tahun Akademik</th>
                                                    <th width="10%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($guru->kelasWali as $kelas)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $kelas->nama }}</td>
                                                        <td>{{ $kelas->tingkat }}</td>
                                                        <td>{{ $kelas->semester->nama ?? '-' }}</td>
                                                        <td>{{ $kelas->semester->tahunAkademik->nama ?? '-' }}</td>
                                                        <td>
                                                            <a href="{{ route('kelas.show', $kelas->id) }}"
                                                                class="btn btn-info btn-sm">
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
                                        <i class="fas fa-info-circle"></i> Guru ini belum menjadi wali kelas
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Tab Mengajar -->
                    <div class="tab-pane fade" id="mengajar" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                @if ($guru->mataPelajaranGuru->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered table-hover">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th width="5%">No</th>
                                                    <th>Mata Pelajaran</th>
                                                    <th>Kelas</th>
                                                    <th>Jam/Minggu</th>
                                                    <th width="10%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($guru->mataPelajaranGuru as $mpg)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $mpg->mataPelajaranKelas->mataPelajaran->nama ?? '-' }}</td>
                                                        <td>{{ $mpg->mataPelajaranKelas->kelas->nama ?? '-' }}</td>
                                                        <td>{{ $mpg->mataPelajaranKelas->jam_per_minggu ?? '-' }} jam</td>
                                                        <td>
                                                            <a href="{{ route('kelas.show', $mpg->mataPelajaranKelas->kelas->id) }}"
                                                                class="btn btn-info btn-sm">
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
                                        <i class="fas fa-info-circle"></i> Guru ini belum mengajar mata pelajaran apapun
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Tab Akun -->
                    <div class="tab-pane fade" id="akun" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="30%"><strong>Username</strong></td>
                                        <td>: {{ $guru->user->username }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email</strong></td>
                                        <td>: {{ $guru->user->email }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Role</strong></td>
                                        <td>: <span class="badge badge-primary">{{ ucfirst($guru->user->role) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status Akun</strong></td>
                                        <td>:
                                            @if ($guru->user->is_active)
                                                <span class="badge badge-success">Aktif</span>
                                            @else
                                                <span class="badge badge-secondary">Nonaktif</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Dibuat</strong></td>
                                        <td>: {{ $guru->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Terakhir Update</strong></td>
                                        <td>: {{ $guru->updated_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="formModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Guru</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="formModalBody">
                    <!-- Form akan dimuat via AJAX -->
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Edit
            $('#btnEdit').click(function() {
                var id = $(this).data('id');
                $('#formModalBody').html(
                    '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
                $('#formModal').modal('show');

                $.get("{{ url('user-data/guru') }}/" + id + "/edit", function(data) {
                    $('#formModalBody').html(data);
                });
            });

            // Submit Form
            $(document).on('submit', '#formGuru', function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                var url = $(this).attr('action');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#formModal').modal('hide');
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
                        var errors = xhr.responseJSON.errors;
                        var errorMessage = '';

                        if (errors) {
                            $.each(errors, function(key, value) {
                                errorMessage += value[0] + '<br>';
                            });
                        } else {
                            errorMessage = xhr.responseJSON.message || 'Terjadi kesalahan';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            html: errorMessage
                        });
                    }
                });
            });

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
        });
    </script>
@endpush
