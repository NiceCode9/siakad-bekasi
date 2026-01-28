@extends('layouts.app')

@section('title', 'Data Semester')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1>Data Semester</h1>
                <div class="top-right-button-container">
                    {{-- Tombol tambah dihilangkan, sekarang via Tahun Akademik --}}
                </div>
                <div class="separator mb-5"></div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="semesterTable" class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Kode</th>
                                        <th>Tahun Akademik</th>
                                        <th>Semester</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Status</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($semester as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->kode }}</td>
                                            <td>{{ $item->tahunAkademik->nama }}</td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $item->nama == 'Ganjil' ? 'info' : 'warning' }}">
                                                    {{ $item->nama }}
                                                </span>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d/m/Y') }}</td>
                                            <td>
                                                @if ($item->is_active)
                                                    <span class="badge badge-success">Aktif</span>
                                                @else
                                                    <span class="badge badge-secondary">Tidak Aktif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    {{-- Tombol aktivasi dihilangkan, sekarang via Tahun Akademik --}}
                                                    <button type="button" class="btn btn-info btn-show"
                                                        data-id="{{ $item->id }}" title="Detail">
                                                        <i class="simple-icon-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-warning btn-edit"
                                                        data-id="{{ $item->id }}" title="Edit">
                                                        <i class="simple-icon-pencil"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-delete"
                                                        data-id="{{ $item->id }}" title="Hapus">
                                                        <i class="simple-icon-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="semesterModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Semester</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="semesterForm">
                    <div class="modal-body">
                        <input type="hidden" id="semester_id" name="semester_id">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tahun_akademik_id">Tahun Akademik <span class="text-danger">*</span></label>
                                    <select class="form-control" name="tahun_akademik_id" id="tahun_akademik_id" required>
                                        <option value="">Pilih Tahun Akademik</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama">Semester <span class="text-danger">*</span></label>
                                    <select class="form-control" name="nama" id="nama" required>
                                        <option value="">Pilih Semester</option>
                                        <option value="Ganjil">Ganjil</option>
                                        <option value="Genap">Genap</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="kode">Kode Semester <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="kode" id="kode"
                                placeholder="Contoh: 2024-1" required>
                            <small class="form-text text-muted">Format: TAHUN-SEMESTER (misal: 2024-1 untuk semester
                                ganjil)</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_mulai">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="tanggal_mulai" id="tanggal_mulai"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_selesai">Tanggal Selesai <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="tanggal_selesai"
                                        id="tanggal_selesai" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active"
                                    value="1">
                                <label class="custom-control-label" for="is_active">Set sebagai semester aktif</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalTitle">Detail Semester</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-muted">Total Kelas</h6>
                                    <h3 class="card-title" id="detail_total_kelas">0</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-muted">Total Siswa</h6>
                                    <h3 class="card-title" id="detail_total_siswa">0</h3>
                                </div>
                            </div>
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
            // Initialize DataTable
            var table = $('#semesterTable').DataTable({
                responsive: true,
                autoWidth: false,
                sDom: '<"row view-filter"<"col-sm-12"<"float-right"l><"float-left"f><"clearfix">>>t<"row view-pager"<"col-sm-12"<"text-center"ip>>>',
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
                    paginate: {
                        previous: "<i class='simple-icon-arrow-left'></i>",
                        next: "<i class='simple-icon-arrow-right'></i>"
                    },
                    search: "_INPUT_",
                    searchPlaceholder: "Search...",
                    lengthMenu: "Items Per Page _MENU_"
                },
            });

            // Load Tahun Akademik
            function loadTahunAkademik(selectedId = null) {
                $.ajax({
                    url: "{{ route('semester.get-tahun-akademik') }}",
                    type: 'GET',
                    success: function(response) {
                        var options = '<option value="">Pilih Tahun Akademik</option>';
                        // Asumsi response berisi data tahun akademik
                        // Sesuaikan dengan struktur response dari controller
                        if (response.data) {
                            response.data.forEach(function(item) {
                                options += '<option value="' + item.id + '"' +
                                    (selectedId == item.id ? ' selected' : '') + '>' +
                                    item.nama + '</option>';
                            });
                        }
                        $('#tahun_akademik_id').html(options);
                    },
                    error: function() {
                        Swal.fire('Error!', 'Gagal memuat data tahun akademik', 'error');
                    }
                });
            }

            // Tombol Tambah
            $('#btnTambah').click(function() {
                $('#semesterForm')[0].reset();
                $('#semester_id').val('');
                $('#modalTitle').text('Tambah Semester');
                loadTahunAkademik();
                $('#semesterModal').modal('show');
            });

            // Tombol Edit
            $(document).on('click', '.btn-edit', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ route('semester.edit', ':id') }}".replace(':id', id),
                    type: 'GET',
                    success: function(response) {
                        console.log(response);
                        $('#semester_id').val(response.id);
                        $('#tahun_akademik_id').val(response.tahun_akademik_id);
                        $('#nama').val(response.nama);
                        $('#kode').val(response.kode);
                        $('#tanggal_mulai').val(response.tanggal_mulai);
                        $('#tanggal_selesai').val(response.tanggal_selesai);
                        $('#is_active').prop('checked', response.is_active);

                        loadTahunAkademik(response.tahun_akademik_id);
                        $('#modalTitle').text('Edit Semester');
                        $('#semesterModal').modal('show');
                    },
                    error: function(xhr) {
                        console.log(xhr);
                        Swal.fire('Error!', 'Gagal memuat data semester', 'error');
                    }
                });
            });

            // Submit Form
            $('#semesterForm').submit(function(e) {
                e.preventDefault();

                var id = $('#semester_id').val();
                var url = id ? "{{ route('semester.update', ':id') }}".replace(':id', id) :
                    "{{ route('semester.store') }}";
                var method = id ? 'PUT' : 'POST';

                var formData = {
                    tahun_akademik_id: $('#tahun_akademik_id').val(),
                    nama: $('#nama').val(),
                    kode: $('#kode').val(),
                    tanggal_mulai: $('#tanggal_mulai').val(),
                    tanggal_selesai: $('#tanggal_selesai').val(),
                    is_active: $('#is_active').is(':checked') ? 1 : 0,
                    _token: '{{ csrf_token() }}'
                };

                if (method === 'PUT') {
                    formData._method = 'PUT';
                }

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.status) {
                            $('#semesterModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON?.errors;
                        if (errors) {
                            var errorMessage = '';
                            Object.keys(errors).forEach(function(key) {
                                errorMessage += errors[key][0] + '<br>';
                            });
                            Swal.fire('Validasi Error!', errorMessage, 'error');
                        } else {
                            Swal.fire('Error!', 'Terjadi kesalahan sistem', 'error');
                        }
                    }
                });
            });

            // Tombol Delete
            $(document).on('click', '.btn-delete', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data semester akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('semester.destroy', ':id') }}".replace(':id',
                                id),
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Terhapus!',
                                        text: response.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(function() {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire('Gagal!', response.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Error!', 'Gagal menghapus data', 'error');
                            }
                        });
                    }
                });
            });

            // Tombol Activate
            $(document).on('click', '.btn-activate', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Aktifkan Semester?',
                    text: "Semester lain akan dinonaktifkan",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, aktifkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('semester') }}/" + id + "/set-active",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: response.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(function() {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire('Gagal!', response.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Error!', 'Gagal mengaktifkan semester',
                                    'error');
                            }
                        });
                    }
                });
            });

            // Tombol Show/Detail
            $(document).on('click', '.btn-show', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ route('semester.show', ':id') }}".replace(':id', id),
                    type: 'GET',
                    success: function(response) {
                        $('#detail_total_kelas').text(response.total_kelas);
                        $('#detail_total_siswa').text(response.total_siswa);
                        $('#detailModal').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Gagal memuat detail semester', 'error');
                    }
                });
            });
        });
    </script>
@endpush
