@extends('layouts.app')

@section('title', 'Data Jurusan')

@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1>Data Jurusan</h1>
                <div class="top-right-button-container">
                    <button class="btn btn-primary btn-sm" id="btn-add">
                        <i class="simple-icon-plus"></i> Tambah Data
                    </button>
                </div>
                <div class="separator mb-5"></div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="jurusanTable" class="text-nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode</th>
                                        <th>Nama Jurusan</th>
                                        <th>Singkatan</th>
                                        <th>Jumlah Kelas</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="modalForm" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Jurusan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formJurusan">
                    <div class="modal-body">
                        <input type="hidden" id="jurusan_id" name="id">

                        <div class="form-group">
                            <label for="kode">Kode Jurusan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kode" name="kode" required>
                            <small class="text-danger" id="error-kode"></small>
                        </div>

                        <div class="form-group">
                            <label for="nama">Nama Jurusan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                            <small class="text-danger" id="error-nama"></small>
                        </div>

                        <div class="form-group">
                            <label for="singkatan">Singkatan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="singkatan" name="singkatan" required>
                            <small class="text-danger" id="error-singkatan"></small>
                        </div>

                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                            <small class="text-danger" id="error-deskripsi"></small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active"
                                    value="1" checked>
                                <label class="custom-control-label" for="is_active">Aktif</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btn-save">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="modalDetail" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Jurusan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Kode</th>
                                    <td id="detail-kode"></td>
                                </tr>
                                <tr>
                                    <th>Nama Jurusan</th>
                                    <td id="detail-nama"></td>
                                </tr>
                                <tr>
                                    <th>Singkatan</th>
                                    <td id="detail-singkatan"></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td id="detail-status"></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Statistik Kelas per Tingkat</h6>
                            <table class="table table-sm">
                                <tr>
                                    <th>Kelas X</th>
                                    <td id="detail-kelas-x"></td>
                                </tr>
                                <tr>
                                    <th>Kelas XI</th>
                                    <td id="detail-kelas-xi"></td>
                                </tr>
                                <tr>
                                    <th>Kelas XII</th>
                                    <td id="detail-kelas-xii"></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <h6>Deskripsi</h6>
                            <p id="detail-deskripsi" class="text-muted"></p>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Daftar Kelas</h6>
                            <div id="detail-kelas-list"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let table;
            let isEdit = false;
            let editId = null;

            // Initialize DataTable with Server-Side Processing
            table = $('#jurusanTable').DataTable({
                sDom: '<"row view-filter"<"col-sm-12"<"float-right"l><"float-left"f><"clearfix">>>t<"row view-pager"<"col-sm-12"<"text-center"ip>>>',
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('jurusan.index') }}",
                    type: 'GET'
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kode',
                        name: 'kode'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'singkatan',
                        name: 'singkatan'
                    },
                    {
                        data: 'kelas_count',
                        name: 'kelas_count',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'is_active',
                        name: 'is_active'
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'asc']
                ],
                drawCallback: function() {
                    $($(".dataTables_wrapper .pagination li:first-of-type"))
                        .find("a")
                        .addClass("prev");
                    $($(".dataTables_wrapper .pagination li:last-of-type"))
                        .find("a")
                        .addClass("next");

                    $(".dataTables_wrapper .pagination").addClass("pagination-sm");
                },
                language: {
                    processing: "Memproses...",
                    paginate: {
                        previous: "<i class='simple-icon-arrow-left'></i>",
                        next: "<i class='simple-icon-arrow-right'></i>"
                    },
                    search: "_INPUT_",
                    searchPlaceholder: "Search...",
                    lengthMenu: "Items Per Page _MENU_"
                },
            });

            // Tambah Data
            $('#btn-add').click(function() {
                isEdit = false;
                editId = null;
                $('#modalTitle').text('Tambah Jurusan');
                $('#formJurusan')[0].reset();
                $('#is_active').prop('checked', true);
                clearErrors();
                $('#modalForm').modal('show');
            });

            // Edit Data
            $('#jurusanTable').on('click', '.btn-edit', function() {
                let id = $(this).data('id');
                isEdit = true;
                editId = id;

                $.ajax({
                    url: "{{ url('jurusan') }}/" + id + "/edit",
                    type: 'GET',
                    success: function(response) {
                        $('#modalTitle').text('Edit Jurusan');
                        $('#jurusan_id').val(response.id);
                        $('#kode').val(response.kode);
                        $('#nama').val(response.nama);
                        $('#singkatan').val(response.singkatan);
                        $('#deskripsi').val(response.deskripsi);
                        $('#is_active').prop('checked', response.is_active);
                        clearErrors();
                        $('#modalForm').modal('show');
                    },
                    error: function() {
                        Swal.fire('Error!', 'Gagal mengambil data', 'error');
                    }
                });
            });

            // Detail Data
            $('#jurusanTable').on('click', '.btn-detail', function() {
                let id = $(this).data('id');

                $.ajax({
                    url: "{{ url('jurusan') }}/" + id,
                    type: 'GET',
                    success: function(response) {
                        $('#detail-kode').text(response.jurusan.kode);
                        $('#detail-nama').text(response.jurusan.nama);
                        $('#detail-singkatan').text(response.jurusan.singkatan);
                        $('#detail-deskripsi').text(response.jurusan.deskripsi || '-');

                        let statusBadge = response.jurusan.is_active ?
                            '<span class="badge badge-success">Aktif</span>' :
                            '<span class="badge badge-secondary">Nonaktif</span>';
                        $('#detail-status').html(statusBadge);

                        $('#detail-kelas-x').text(response.stats.X + ' Kelas');
                        $('#detail-kelas-xi').text(response.stats.XI + ' Kelas');
                        $('#detail-kelas-xii').text(response.stats.XII + ' Kelas');

                        // Daftar kelas
                        let kelasList =
                            '<div class="table-responsive"><table class="table table-sm table-bordered">';
                        kelasList +=
                            '<thead><tr><th>Tingkat</th><th>Nama Kelas</th><th>Wali Kelas</th></tr></thead><tbody>';

                        if (response.jurusan.kelas && response.jurusan.kelas.length > 0) {
                            response.jurusan.kelas.forEach(function(kelas) {
                                let waliKelas = kelas.wali_kelas ? kelas.wali_kelas
                                    .nama : '-';
                                kelasList += `<tr>
                            <td>${kelas.tingkat}</td>
                            <td>${kelas.nama}</td>
                            <td>${waliKelas}</td>
                        </tr>`;
                            });
                        } else {
                            kelasList +=
                                '<tr><td colspan="3" class="text-center">Belum ada kelas</td></tr>';
                        }

                        kelasList += '</tbody></table></div>';
                        $('#detail-kelas-list').html(kelasList);

                        $('#modalDetail').modal('show');
                    },
                    error: function() {
                        Swal.fire('Error!', 'Gagal mengambil data', 'error');
                    }
                });
            });

            // Submit Form
            $('#formJurusan').submit(function(e) {
                e.preventDefault();

                let formData = {
                    kode: $('#kode').val(),
                    nama: $('#nama').val(),
                    singkatan: $('#singkatan').val(),
                    deskripsi: $('#deskripsi').val(),
                    is_active: $('#is_active').is(':checked') ? 1 : 0,
                    _token: "{{ csrf_token() }}"
                };

                let url = isEdit ?
                    "{{ url('jurusan') }}/" + editId :
                    "{{ route('jurusan.store') }}";

                if (isEdit) {
                    formData._method = 'PUT';
                }

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    beforeSend: function() {
                        $('#btn-save').prop('disabled', true).text('Menyimpan...');
                        clearErrors();
                    },
                    success: function(response) {
                        $('#modalForm').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message || response.messages,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        table.draw(); // Gunakan draw() untuk server-side
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#error-' + key).text(value[0]);
                            });
                        } else {
                            Swal.fire('Error!', 'Terjadi kesalahan', 'error');
                        }
                    },
                    complete: function() {
                        $('#btn-save').prop('disabled', false).text('Simpan');
                    }
                });
            });

            // Toggle Active
            $('#jurusanTable').on('click', '.btn-toggle', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Ubah status jurusan ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Ubah!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('jurusan') }}/" + id + "/toggle-active",
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.messages,
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                table.draw(); // Gunakan draw() untuk server-side
                            },
                            error: function() {
                                Swal.fire('Error!', 'Gagal mengubah status', 'error');
                            }
                        });
                    }
                });
            });

            // Delete Data
            $('#jurusanTable').on('click', '.btn-delete', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('jurusan') }}/" + id,
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Terhapus!',
                                        text: response.messages,
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    table.draw(); // Gunakan draw() untuk server-side
                                } else {
                                    Swal.fire('Gagal!', response.messages, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error!', 'Terjadi kesalahan', 'error');
                            }
                        });
                    }
                });
            });

            // Clear form errors
            function clearErrors() {
                $('.text-danger').text('');
            }

            // Clear errors on input
            $('input, textarea').on('input', function() {
                let name = $(this).attr('name');
                $('#error-' + name).text('');
            });
        });
    </script>
@endpush
