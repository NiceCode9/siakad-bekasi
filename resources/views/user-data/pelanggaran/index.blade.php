@extends('layouts.app')

@section('title', 'Pelanggaran Siswa')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Pelanggaran Siswa</h4>
            <div>
                <button type="button" class="btn btn-primary btn-sm" id="btnCreate">
                    <i class="fas fa-plus"></i> Catat Pelanggaran
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="pelanggaranTable" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Siswa</th>
                                <th>Tanggal</th>
                                <th>Jenis Pelanggaran</th>
                                <th>Kategori</th>
                                <th>Poin</th>
                                <th>Status</th>
                                <th width="15%">Aksi</th>
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
                    <h5 class="modal-title" id="formModalLabel">Form Pelanggaran</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="formModalBody">
                    <!-- Form via AJAX -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#pelanggaranTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('pelanggaran-siswa.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_siswa',
                        name: 'siswa.nama_lengkap'
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'jenis_pelanggaran',
                        name: 'jenis_pelanggaran'
                    },
                    {
                        data: 'kategori',
                        name: 'kategori'
                    },
                    {
                        data: 'poin',
                        name: 'poin'
                    },
                    {
                        data: 'status_badge',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Create
            $('#btnCreate').click(function() {
                $('#formModalLabel').text('Catat Pelanggaran');
                $('#formModalBody').html(
                    '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
                $('#formModal').modal('show');

                $.get("{{ route('pelanggaran-siswa.create') }}", function(data) {
                    $('#formModalBody').html(data);
                });
            });

            // Edit
            $('#pelanggaranTable').on('click', '.btn-edit', function() {
                var id = $(this).data('id');
                $('#formModalLabel').text('Edit Pelanggaran');
                $('#formModalBody').html(
                    '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
                $('#formModal').modal('show');

                $.get("{{ url('user-data/pelanggaran-siswa') }}/" + id + "/edit", function(data) {
                    $('#formModalBody').html(data);
                });
            });

            // Submit Form
            $(document).on('submit', '#formPelanggaran', function(e) {
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
                        var errorMessage = xhr.responseJSON.message || 'Terjadi kesalahan';
                        
                        if(errors){
                             errorMessage = '';
                             $.each(errors, function(key, value) {
                                errorMessage += value[0] + '<br>';
                            });
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
            $('#pelanggaranTable').on('click', '.btn-delete', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data pelanggaran " + name + " akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('user-data/pelanggaran-siswa') }}/" + id,
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire('Berhasil!', response.message, 'success');
                                table.draw();
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal!', xhr.responseJSON.message, 'error');
                            }
                        });
                    }
                });
            });

        });
    </script>
@endpush
