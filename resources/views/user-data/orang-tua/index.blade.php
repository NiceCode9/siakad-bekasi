@extends('layouts.app')

@section('title', 'Data Orang Tua')

@section('content')

    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Data Orang Tua</h4>
            <div>
                <button type="button" class="btn btn-success btn-sm mr-2" data-toggle="modal" data-target="#importModal">
                    <i class="fas fa-file-import"></i> Import
                </button>
                <a href="{{ route('orang-tua.export') }}" class="btn btn-info btn-sm mr-2">
                    <i class="fas fa-file-export"></i> Export
                </a>
                <button type="button" class="btn btn-primary btn-sm" id="btnCreate">
                    <i class="fas fa-plus"></i> Tambah Orang Tua
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="orangTuaTable" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Ayah</th>
                                <th>Nama Ibu</th>
                                <th>Nama Wali</th>
                                <th>Telepon</th>
                                <th>Jumlah Anak</th>
                                <th>Punya Akun</th>
                                <th width="12%">Aksi</th>
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
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModalLabel">Form Orang Tua</h5>
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

    <!-- Modal Create Account -->
    <div class="modal fade" id="createAccountModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="formCreateAccount">
                    <div class="modal-header">
                        <h5 class="modal-title">Buat Akun</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="orangtua_id">
                        <div class="form-group">
                            <label>Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" id="acc_username" class="form-control form-control-sm"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="acc_email" class="form-control form-control-sm"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="acc_password" class="form-control form-control-sm"
                                required>
                            <small class="form-text text-muted">Minimal 6 karakter</small>
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

    <!-- Modal Import -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('orang-tua.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Import Data Orang Tua</h5>
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
            var table = $('#orangTuaTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('orang-tua.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_ayah',
                        name: 'nama_ayah'
                    },
                    {
                        data: 'nama_ibu',
                        name: 'nama_ibu'
                    },
                    {
                        data: 'nama_wali',
                        name: 'nama_wali'
                    },
                    {
                        data: 'telepon',
                        name: 'telepon',
                        orderable: false
                    },
                    {
                        data: 'jumlah_anak',
                        name: 'siswa_count'
                    },
                    {
                        data: 'has_account',
                        name: 'has_account',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'asc']
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

            // Create
            $('#btnCreate').click(function() {
                $('#formModalLabel').text('Tambah Orang Tua');
                $('#formModalBody').html(
                    '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
                $('#formModal').modal('show');

                $.get("{{ route('orang-tua.create') }}", function(data) {
                    $('#formModalBody').html(data);
                });
            });

            // Edit
            $('#orangTuaTable').on('click', '.btn-edit', function() {
                var id = $(this).data('id');
                $('#formModalLabel').text('Edit Orang Tua');
                $('#formModalBody').html(
                    '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
                $('#formModal').modal('show');

                $.get("{{ url('user-data/orang-tua') }}/" + id + "/edit", function(data) {
                    $('#formModalBody').html(data);
                });
            });

            // Submit Form (Create/Update)
            $(document).on('submit', '#formOrangTua', function(e) {
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

            // Create Account
            $('#orangTuaTable').on('click', '.btn-create-account', function() {
                var id = $(this).data('id');
                $('#orangtua_id').val(id);
                $('#formCreateAccount')[0].reset();
                $('#createAccountModal').modal('show');
            });

            // Submit Create Account
            $('#formCreateAccount').submit(function(e) {
                e.preventDefault();

                var id = $('#orangtua_id').val();
                var formData = {
                    _token: "{{ csrf_token() }}",
                    username: $('#acc_username').val(),
                    email: $('#acc_email').val(),
                    password: $('#acc_password').val()
                };

                $.ajax({
                    url: "{{ url('user-data/orang-tua') }}/" + id + "/create-account",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#createAccountModal').modal('hide');
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

            // Delete
            $('#orangTuaTable').on('click', '.btn-delete', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data orang tua akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('user-data/orang-tua') }}/" + id,
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
