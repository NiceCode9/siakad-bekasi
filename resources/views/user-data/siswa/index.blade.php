@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')

    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Data Siswa</h4>
            <div>
                <button type="button" class="btn btn-success btn-sm mr-2" data-toggle="modal" data-target="#importModal">
                    <i class="fas fa-file-import"></i> Import
                </button>
                <a href="{{ route('siswa.export') }}" class="btn btn-info btn-sm mr-2">
                    <i class="fas fa-file-export"></i> Export
                </a>
                <button type="button" class="btn btn-primary btn-sm" id="btnCreate">
                    <i class="fas fa-plus"></i> Tambah Siswa
                </button>
            </div>
        </div>

        <!-- Filter -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label>Kelas</label>
                            <select id="filter_kelas" class="form-control form-control-sm">
                                <option value="">Semua Kelas</option>
                                @foreach ($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama }} - {{ $k->semester->nama ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label>Status</label>
                            <select id="filter_status" class="form-control form-control-sm">
                                <option value="">Semua Status</option>
                                @foreach ($status as $s)
                                    <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label>Jenis Kelamin</label>
                            <select id="filter_jk" class="form-control form-control-sm">
                                <option value="">Semua</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label>&nbsp;</label>
                        <div>
                            <button type="button" id="btnFilter" class="btn btn-primary btn-sm">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <button type="button" id="btnReset" class="btn btn-secondary btn-sm">
                                <i class="fas fa-redo"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="siswaTable" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>NISN</th>
                                <th>NIS</th>
                                <th>Nama Lengkap</th>
                                <th>JK</th>
                                <th>Kelas</th>
                                <th>Orang Tua</th>
                                <th>Telepon</th>
                                <th>Status</th>
                                <th width="13%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="formModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModalLabel">Form Siswa</h5>
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

    <!-- Modal Assign Kelas -->
    <div class="modal fade" id="assignKelasModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="formAssignKelas">
                    <div class="modal-header">
                        <h5 class="modal-title">Assign ke Kelas</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="siswa_id">
                        <div class="form-group">
                            <label>Nama Siswa</label>
                            <input type="text" id="siswa_nama" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="form-group">
                            <label>Pilih Kelas <span class="text-danger">*</span></label>
                            <select name="kelas_id" id="kelas_id" class="form-control form-control-sm" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama }} - {{ $k->semester->nama ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Masuk</label>
                            <input type="date" name="tanggal_masuk" class="form-control form-control-sm"
                                value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Assign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Import -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Import Data Siswa</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>File Excel <span class="text-danger">*</span></label>
                            <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                            <small class="form-text text-muted">Format: .xlsx atau .xls (max 2MB)</small>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Download template Excel terlebih dahulu untuk memudahkan
                            import data.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            // DataTable
            var table = $('#siswaTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('siswa.index') }}",
                    data: function(d) {
                        d.kelas_id = $('#filter_kelas').val();
                        d.status = $('#filter_status').val();
                        d.jenis_kelamin = $('#filter_jk').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nisn',
                        name: 'nisn'
                    },
                    {
                        data: 'nis',
                        name: 'nis'
                    },
                    {
                        data: 'nama_lengkap',
                        name: 'nama_lengkap'
                    },
                    {
                        data: 'jk',
                        name: 'jenis_kelamin'
                    },
                    {
                        data: 'kelas_aktif',
                        name: 'kelas_aktif',
                        orderable: false
                    },
                    {
                        data: 'orang_tua.nama_ayah',
                        name: 'orangTua.nama_ayah',
                        defaultContent: '-'
                    },
                    {
                        data: 'telepon',
                        name: 'telepon'
                    },
                    {
                        data: 'status_badge',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [3, 'asc']
                ],
                language: {
                    processing: "Memuat data...",
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    },
                    emptyTable: "Tidak ada data yang tersedia",
                    zeroRecords: "Tidak ada data yang cocok"
                }
            });

            // Filter
            $('#btnFilter').click(function() {
                table.draw();
            });

            // Reset Filter
            $('#btnReset').click(function() {
                $('#filter_kelas').val('');
                $('#filter_status').val('');
                $('#filter_jk').val('');
                table.draw();
            });

            // Create
            $('#btnCreate').click(function() {
                $('#formModalLabel').text('Tambah Siswa');
                $('#formModalBody').html(
                    '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
                $('#formModal').modal('show');

                $.get("{{ route('siswa.create') }}", function(data) {
                    $('#formModalBody').html(data);
                });
            });

            // Edit
            $('#siswaTable').on('click', '.btn-edit', function() {
                var id = $(this).data('id');
                $('#formModalLabel').text('Edit Siswa');
                $('#formModalBody').html(
                    '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
                $('#formModal').modal('show');

                $.get("{{ url('user-data/siswa') }}/" + id + "/edit", function(data) {
                    $('#formModalBody').html(data);
                });
            });

            // Submit Form (Create/Update)
            $(document).on('submit', '#formSiswa', function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                var url = $(this).attr('action');
                var method = $(this).find('input[name="_method"]').val() || 'POST';

                if (method === 'PUT') {
                    formData.append('_method', 'PUT');
                }

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
                            timer: 3000,
                            showConfirmButton: false
                        });
                        table.draw();
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

            // Assign Kelas
            $('#siswaTable').on('click', '.btn-assign-kelas', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');

                $('#siswa_id').val(id);
                $('#siswa_nama').val(name);
                $('#assignKelasModal').modal('show');
            });

            // Submit Assign Kelas
            $('#formAssignKelas').submit(function(e) {
                e.preventDefault();

                var siswaId = $('#siswa_id').val();
                var formData = {
                    _token: "{{ csrf_token() }}",
                    kelas_id: $('#kelas_id').val(),
                    tanggal_masuk: $('input[name="tanggal_masuk"]').val()
                };

                $.ajax({
                    url: "{{ url('user-data/siswa') }}/" + siswaId + "/assign-kelas",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#assignKelasModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 3000,
                            showConfirmButton: false
                        });
                        table.draw();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON.message
                        });
                    }
                });
            });

            // Delete
            $('#siswaTable').on('click', '.btn-delete', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data siswa " + name + " akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('user-data/siswa') }}/" + id,
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Berhasil!',
                                    response.message,
                                    'success'
                                );
                                table.draw();
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
