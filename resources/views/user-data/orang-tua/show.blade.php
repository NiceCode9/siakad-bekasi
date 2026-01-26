{{-- show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Orang Tua')

@section('content')

    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Detail Orang Tua</h4>
            <div>
                <button type="button" class="btn btn-warning btn-sm" id="btnEdit" data-id="{{ $orangTua->id }}">
                    <i class="fas fa-edit"></i> Edit
                </button>
                @if (!$orangTua->user_id)
                    <button type="button" class="btn btn-success btn-sm" id="btnCreateAccount" data-id="{{ $orangTua->id }}">
                        <i class="fas fa-user-plus"></i> Buat Akun
                    </button>
                @endif
                <a href="{{ route('orang-tua.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Data Orang Tua -->
            <div class="col-md-6">
                <!-- Data Ayah -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-male"></i> Data Ayah</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="35%"><strong>NIK</strong></td>
                                <td>: {{ $orangTua->nik_ayah ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nama</strong></td>
                                <td>: {{ $orangTua->nama_ayah ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Pekerjaan</strong></td>
                                <td>: {{ $orangTua->pekerjaan_ayah ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Pendidikan</strong></td>
                                <td>: {{ $orangTua->pendidikan_ayah ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Penghasilan</strong></td>
                                <td>: {{ $orangTua->penghasilan_ayah ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Telepon</strong></td>
                                <td>: {{ $orangTua->telepon_ayah ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Data Ibu -->
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0"><i class="fas fa-female"></i> Data Ibu</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="35%"><strong>NIK</strong></td>
                                <td>: {{ $orangTua->nik_ibu ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nama</strong></td>
                                <td>: {{ $orangTua->nama_ibu ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Pekerjaan</strong></td>
                                <td>: {{ $orangTua->pekerjaan_ibu ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Pendidikan</strong></td>
                                <td>: {{ $orangTua->pendidikan_ibu ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Penghasilan</strong></td>
                                <td>: {{ $orangTua->penghasilan_ibu ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Telepon</strong></td>
                                <td>: {{ $orangTua->telepon_ibu ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if ($orangTua->nama_wali)
                    <!-- Data Wali -->
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0"><i class="fas fa-user"></i> Data Wali</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="35%"><strong>Nama</strong></td>
                                    <td>: {{ $orangTua->nama_wali ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Pekerjaan</strong></td>
                                    <td>: {{ $orangTua->pekerjaan_wali ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Telepon</strong></td>
                                    <td>: {{ $orangTua->telepon_wali ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Alamat -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-map-marker-alt"></i> Alamat</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $orangTua->alamat ?? '-' }}</p>
                    </div>
                </div>

                <!-- Info Akun -->
                @if ($orangTua->user)
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-user-lock"></i> Informasi Akun</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="35%"><strong>Username</strong></td>
                                    <td>: {{ $orangTua->user->username }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email</strong></td>
                                    <td>: {{ $orangTua->user->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Role</strong></td>
                                    <td>: <span class="badge badge-info">{{ ucfirst($orangTua->user->role) }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Status</strong></td>
                                    <td>:
                                        @if ($orangTua->user->is_active)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Daftar Anak -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-users"></i> Daftar Anak ({{ $orangTua->siswa->count() }})</h6>
                    </div>
                    <div class="card-body">
                        @if ($orangTua->siswa->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>NISN</th>
                                            <th>Nama</th>
                                            <th>Kelas</th>
                                            <th>Status</th>
                                            <th width="10%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($orangTua->siswa as $siswa)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $siswa->nisn }}</td>
                                                <td>{{ $siswa->nama_lengkap }}</td>
                                                <td>{{ $siswa->kelasAktif->first()->nama ?? '-' }}</td>
                                                <td>
                                                    @php
                                                        $colors = [
                                                            'aktif' => 'success',
                                                            'lulus' => 'info',
                                                            'pindah' => 'warning',
                                                            'keluar' => 'secondary',
                                                            'DO' => 'danger',
                                                        ];
                                                        $color = $colors[$siswa->status] ?? 'secondary';
                                                    @endphp
                                                    <span
                                                        class="badge badge-{{ $color }}">{{ ucfirst($siswa->status) }}</span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('siswa.show', $siswa->id) }}"
                                                        class="btn btn-info btn-sm" title="Detail">
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
                                <i class="fas fa-info-circle"></i> Belum ada anak yang terdaftar
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Info Tambahan -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informasi</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%">Dibuat</td>
                                <td>: {{ $orangTua->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td>Terakhir Update</td>
                                <td>: {{ $orangTua->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td>Total Anak</td>
                                <td>: {{ $orangTua->siswa->count() }} siswa</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="formModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Orang Tua</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body" id="formModalBody"></div>
            </div>
        </div>
    </div>

    <!-- Modal Create Account -->
    <div class="modal fade" id="createAccountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formCreateAccount">
                    <div class="modal-header">
                        <h5 class="modal-title">Buat Akun</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control form-control-sm" required>
                        </div>
                        <div class="form-group">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control form-control-sm" required>
                        </div>
                        <div class="form-group">
                            <label>Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Buat Akun</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#btnEdit').click(function() {
                var id = $(this).data('id');
                $('#formModalBody').html(
                    '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
                $('#formModal').modal('show');
                $.get("{{ url('user-data/orang-tua') }}/" + id + "/edit", function(data) {
                    $('#formModalBody').html(data);
                });
            });

            $(document).on('submit', '#formOrangTua', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    url: $(this).attr('action'),
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
                            timer: 2000
                        }).then(() => location.reload());
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            html: xhr.responseJSON.message
                        });
                    }
                });
            });

            $('#btnCreateAccount').click(function() {
                $('#createAccountModal').modal('show');
            });

            $('#formCreateAccount').submit(function(e) {
                e.preventDefault();
                var id = "{{ $orangTua->id }}";
                $.ajax({
                    url: "{{ url('user-data/orang-tua') }}/" + id + "/create-account",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        username: $('input[name="username"]').val(),
                        email: $('input[name="email"]').val(),
                        password: $('input[name="password"]').val()
                    },
                    success: function(response) {
                        $('#createAccountModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 2000
                        }).then(() => location.reload());
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            html: xhr.responseJSON.message
                        });
                    }
                });
            });
        });
    </script>
@endpush
