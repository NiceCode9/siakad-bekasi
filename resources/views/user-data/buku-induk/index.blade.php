@extends('layouts.app')

@section('title', 'Buku Induk Siswa')

@section('content')

    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Buku Induk Siswa</h4>
            <div>
                 <!-- Optional: Export button or other global actions -->
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="bukuIndukTable" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>NIS / NISN</th>
                                <th>Nama Lengkap</th>
                                <th>Kelas</th>
                                <th>Status Data</th>
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
                    <h5 class="modal-title" id="formModalLabel">Data Buku Induk</h5>
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
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#bukuIndukTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('buku-induk.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nis_nisn',
                        name: 'nis'
                    },
                    {
                        data: 'nama_lengkap',
                        name: 'nama_lengkap'
                    },
                    {
                        data: 'kelas',
                        name: 'kelasAktif.nama'
                    },
                    {
                        data: 'status_data',
                        name: 'status_data',
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

            // Edit Data Induk
            $('#bukuIndukTable').on('click', '.btn-primary, .btn-warning', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                
                $('#formModalLabel').text('Edit Data Induk');
                $('#formModalBody').html(
                    '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
                $('#formModal').modal('show');

                $.get(url, function(data) {
                    $('#formModalBody').html(data);
                });
            });

            // Submit Form
            $(document).on('submit', '#formBukuInduk', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                var url = $(this).attr('action');
                
                // Add _method PUT
                formData.append('_method', 'PUT');

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
                        var errorMessage = xhr.responseJSON.message;
                        
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
        });
    </script>
@endpush
