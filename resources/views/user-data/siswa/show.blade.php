    {{-- show.blade.php --}}
    @extends('layouts.app')

    @section('title', 'Detail Siswa - ' . $siswa->nama_lengkap)

    @section('content')

        <div class="container-fluid">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Detail Siswa: {{ $siswa->nama_lengkap }}</h4>
                <div>
                    <button type="button" class="btn btn-warning btn-sm" id="btnEdit" data-id="{{ $siswa->id }}">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <a href="{{ route('siswa.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            <!-- Statistik Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-1">Prestasi</h6>
                                    <h3 class="mb-0">{{ $stats['total_prestasi'] }}</h3>
                                </div>
                                <div><i class="fas fa-trophy fa-3x opacity-50"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-1">Pelanggaran</h6>
                                    <h3 class="mb-0">{{ $stats['total_pelanggaran'] }}</h3>
                                </div>
                                <div><i class="fas fa-exclamation-triangle fa-3x opacity-50"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-1">Nilai</h6>
                                    <h3 class="mb-0">{{ $stats['total_nilai'] }}</h3>
                                </div>
                                <div><i class="fas fa-book fa-3x opacity-50"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Biodata -->
                <div class="col-md-4">
                    <!-- Foto & Status -->
                    <div class="card text-center">
                        <div class="card-body">
                            @if ($siswa->foto)
                                <img src="{{ asset('storage/' . $siswa->foto) }}" alt="Foto"
                                    class="img-fluid rounded-circle mb-3"
                                    style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <div class="bg-secondary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                    style="width: 150px; height: 150px;">
                                    <i class="fas fa-user fa-5x text-white"></i>
                                </div>
                            @endif
                            <h5>{{ $siswa->nama_lengkap }}</h5>
                            <p class="text-muted mb-1">{{ $siswa->nisn }}</p>
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
                            <span class="badge badge-{{ $color }}">{{ ucfirst($siswa->status) }}</span>
                        </div>
                    </div>

                    <!-- Identitas -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-id-card"></i> Identitas</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="45%"><strong>NISN</strong></td>
                                    <td>: {{ $siswa->nisn }}</td>
                                </tr>
                                <tr>
                                    <td><strong>NIS</strong></td>
                                    <td>: {{ $siswa->nis }}</td>
                                </tr>
                                <tr>
                                    <td><strong>NIK</strong></td>
                                    <td>: {{ $siswa->nik ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>JK</strong></td>
                                    <td>: {{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>TTL</strong></td>
                                    <td>: {{ $siswa->tempat_lahir ?? '-' }},
                                        {{ $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('d/m/Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Agama</strong></td>
                                    <td>: {{ $siswa->agama ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Kontak -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-phone"></i> Kontak</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="45%"><strong>Telepon</strong></td>
                                    <td>: {{ $siswa->telepon ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email</strong></td>
                                    <td>: {{ $siswa->email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Alamat</strong></td>
                                    <td>: {{ $siswa->alamat ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="col-md-8">
                    <ul class="nav nav-tabs" id="siswaTab" role="tablist">
                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#kelas"><i
                                    class="fas fa-school"></i> Kelas</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#keluarga"><i
                                    class="fas fa-users"></i> Keluarga</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#pendidikan"><i
                                    class="fas fa-graduation-cap"></i> Pendidikan</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#fisik"><i
                                    class="fas fa-heartbeat"></i> Fisik</a></li>
                    </ul>

                    <div class="tab-content">
                        <!-- Tab Kelas -->
                        <div class="tab-pane fade show active" id="kelas">
                            <div class="card">
                                <div class="card-body">
                                    @if ($siswa->siswaKelas->count() > 0)
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Kelas</th>
                                                    <th>Semester</th>
                                                    <th>Tgl Masuk</th>
                                                    <th>Tgl Keluar</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($siswa->siswaKelas as $sk)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $sk->kelas->nama ?? '-' }}</td>
                                                        <td>{{ $sk->kelas->semester->nama ?? '-' }}</td>
                                                        <td>{{ $sk->tanggal_masuk ? $sk->tanggal_masuk->format('d/m/Y') : '-' }}
                                                        </td>
                                                        <td>{{ $sk->tanggal_keluar ? $sk->tanggal_keluar->format('d/m/Y') : '-' }}
                                                        </td>
                                                        <td><span
                                                                class="badge badge-{{ $sk->status == 'aktif' ? 'success' : 'secondary' }}">{{ $sk->status }}</span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="alert alert-info"><i class="fas fa-info-circle"></i> Siswa belum
                                            terdaftar di kelas</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Tab Keluarga -->
                        <div class="tab-pane fade" id="keluarga">
                            <div class="card">
                                <div class="card-body">
                                    @if ($siswa->orangTua)
                                        <h6>Data Ayah</h6>
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td width="30%">Nama</td>
                                                <td>: {{ $siswa->orangTua->nama_ayah ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Pekerjaan</td>
                                                <td>: {{ $siswa->orangTua->pekerjaan_ayah ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Telepon</td>
                                                <td>: {{ $siswa->orangTua->telepon_ayah ?? '-' }}</td>
                                            </tr>
                                        </table>
                                        <hr>
                                        <h6>Data Ibu</h6>
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td width="30%">Nama</td>
                                                <td>: {{ $siswa->orangTua->nama_ibu ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Pekerjaan</td>
                                                <td>: {{ $siswa->orangTua->pekerjaan_ibu ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Telepon</td>
                                                <td>: {{ $siswa->orangTua->telepon_ibu ?? '-' }}</td>
                                            </tr>
                                        </table>
                                    @else
                                        <div class="alert alert-info"><i class="fas fa-info-circle"></i> Data orang tua
                                            belum dilengkapi</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Tab Pendidikan -->
                        <div class="tab-pane fade" id="pendidikan">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="30%">Asal Sekolah</td>
                                            <td>: {{ $siswa->asal_sekolah ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Tahun Lulus SMP</td>
                                            <td>: {{ $siswa->tahun_lulus_smp ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Tanggal Masuk</td>
                                            <td>:
                                                {{ $siswa->tanggal_masuk ? $siswa->tanggal_masuk->format('d/m/Y') : '-' }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Fisik -->
                        <div class="tab-pane fade" id="fisik">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="30%">Tinggi Badan</td>
                                            <td>: {{ $siswa->tinggi_badan ?? '-' }} cm</td>
                                        </tr>
                                        <tr>
                                            <td>Berat Badan</td>
                                            <td>: {{ $siswa->berat_badan ?? '-' }} kg</td>
                                        </tr>
                                        <tr>
                                            <td>Golongan Darah</td>
                                            <td>: {{ $siswa->golongan_darah ?? '-' }}</td>
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
        <div class="modal fade" id="formModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Siswa</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body" id="formModalBody"></div>
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
                    $.get("{{ url('siswa') }}/" + id + "/edit", function(data) {
                        $('#formModalBody').html(data);
                    });
                });

                $(document).on('submit', '#formSiswa', function(e) {
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
            });
        </script>
    @endpush
