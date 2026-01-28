@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets') }}/css/vendor/component-custom-switch.min.css" />
@endpush

@section('title', 'Data Tahun Akademik')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1>Manajemen Tahun Akademik</h1>
                <div class="top-right-button-container">
                    <button class="btn btn-primary btn-sm" id="btn-add">
                        <i class="simple-icon-plus"></i> Tambah Data
                    </button>
                </div>
                <div class="separator mb-5"></div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="text-nowrap" id="tahun-akademik-table">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama</th>
                                        <th>Kurikulum</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tahunAkademik as $item)
                                        <tr>
                                            <td>{{ $item->kode }}</td>
                                            <td>{{ $item->nama }}</td>
                                            <td>{{ $item->kurikulum->nama ?? '-' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d/m/Y') }}</td>
                                            <td>
                                                @if ($item->is_active)
                                                    <button class="btn btn-success btn-sm" disabled>
                                                        <i class="simple-icon-check"></i> Aktif
                                                    </button>
                                                @else
                                                    <button class="btn btn-outline-primary btn-sm btn-set-active"
                                                        data-id="{{ $item->id }}">
                                                        <i class="simple-icon-power"></i> Aktifkan
                                                    </button>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-info btn-sm btn-edit"
                                                        data-id="{{ $item->id }}" title="Edit Tahun Akademik">
                                                        <i class="simple-icon-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-danger btn-sm btn-delete"
                                                        title="Hapus Tahun Akademik" data-id="{{ $item->id }}">
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
    <div class="modal fade" id="tahunAkademikModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Tahun Akademik</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="tahunAkademikForm">
                    <div class="modal-body">
                        <input type="hidden" id="tahun_akademik_id" name="tahun_akademik_id">

                        <div class="form-group">
                            <label for="kode">Kode Tahun Akademik <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kode" name="kode"
                                placeholder="Contoh: TA2024">
                        </div>

                        <div class="form-group">
                            <label for="nama">Nama Tahun Akademik <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama" name="nama"
                                placeholder="Contoh: Tahun Akademik 2024/2025">
                        </div>

                        <div class="form-group">
                            <label for="kurikulum_id">Kurikulum <span class="text-danger">*</span></label>
                            <select class="form-control" id="kurikulum_id" name="kurikulum_id">
                                <option value="">-- Pilih Kurikulum --</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="tanggal_mulai">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_mulai" id="tanggal_mulai">
                        </div>

                        <div class="form-group">
                            <label for="tanggal_selesai">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_selesai" id="tanggal_selesai">
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

    <!-- Modal Activation -->
    <div class="modal fade" id="activateSemesterModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Semester untuk Diaktifkan</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="activateSemesterForm">
                    <div class="modal-body">
                        <input type="hidden" id="activate_tahun_akademik_id">
                        <div class="form-group">
                            <label>Semester <span class="text-danger">*</span></label>
                            <div id="semester-options-container">
                                <!-- Radio buttons will be injected here -->
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Aktifkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // CSRF Token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#tahun-akademik-table').DataTable({
                sDom: '<"row view-filter"<"col-sm-12"<"float-right"l><"float-left"f><"clearfix">>>t<"row view-pager"<"col-sm-12"<"text-center"ip>>>',
                responsive: true,
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

            function clearValidationErrors() {
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
                $('.text-danger').remove();
            }

            function loadKurikulum(selectedKurikulumId = '') {
                $.ajax({
                    url: "{{ route('tahun-akademik.get-kurikulum') }}",
                    type: 'GET',
                    success: function(response) {
                        let options = '<option value="">-- Pilih Kurikulum --</option>';

                        if (response.data && response.data.length > 0) {
                            response.data.forEach(function(item) {
                                options += `<option value="${item.id}">${item.nama}</option>`;
                            });
                        }

                        $('#kurikulum_id').html(options);

                        if (selectedKurikulumId != '') {
                            $('#kurikulum_id').val(selectedKurikulumId);
                        }
                    },
                    error: function() {
                        console.error('Gagal memuat data kurikulum');
                    }
                });
            }

            $('#btn-add').click(function() {
                $('#tahunAkademikForm')[0].reset();
                $('#tahun_akademik_id').val('');
                $('#modalTitle').text('Tambah Tahun Akademik');
                clearValidationErrors();
                loadKurikulum();
                $('#tahunAkademikModal').modal('show');
            });

            $(document).on('click', '.btn-edit', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ route('tahun-akademik.edit', ':tahunAkademik') }}".replace(
                        ':tahunAkademik', id),
                    type: 'GET',
                    success: function(response) {
                        $('#tahun_akademik_id').val(response.data.id);
                        $('#kode').val(response.data.kode);
                        $('#nama').val(response.data.nama);
                        $('#kurikulum_id').val(response.data.kurikulum_id);
                        $('#tanggal_mulai').val(response.data.tanggal_mulai);
                        $('#tanggal_selesai').val(response.data.tanggal_selesai);

                        loadKurikulum(response.data.kurikulum_id);
                        $('#modalTitle').text('Edit Tahun Akademik');
                        $('#tahunAkademikModal').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Gagal memuat data tahun akademik', 'error');
                    }
                });
            });

            $('#tahunAkademikForm').submit(function(e) {
                e.preventDefault();

                var id = $('#tahun_akademik_id').val();
                var url = id ? "{{ url('tahun-akademik') }}/" + id :
                    "{{ route('tahun-akademik.store') }}";

                var formData = {
                    kode: $('#kode').val(),
                    nama: $('#nama').val(),
                    kurikulum_id: $('#kurikulum_id').val(),
                    tanggal_mulai: $('#tanggal_mulai').val(),
                    tanggal_selesai: $('#tanggal_selesai').val()
                };

                if (id) {
                    formData._method = 'PUT';
                }

                $('#btn-save').prop('disabled', true).html(
                    '<i class="simple-icon-refresh"></i> Menyimpan...');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        clearValidationErrors();
                        $('#tahunAkademikModal').modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: true
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        $('.is-invalid').removeClass('is-invalid');
                        $('.invalid-feedback').remove();

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;

                            $.each(errors, function(field, messages) {
                                let input = $('[name="' + field + '"]');
                                input.addClass('is-invalid');
                                input.after('<div class="invalid-feedback">' +
                                    messages[0] + '</div>');
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                            });
                        }
                    },
                    complete: function() {
                        $('#btn-save').prop('disabled', false).html('Simpan');
                    }
                });
            });

            $(document).on('click', '.btn-delete', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data tahun akademik akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('tahun-akademik.destroy', ':tahunAkademik') }}"
                                .replace(
                                    ':tahunAkademik', id),
                            type: 'DELETE',
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: xhr.responseJSON?.message ||
                                        'Gagal menghapus tahun akademik'
                                });
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.btn-set-active', function() {
                let id = $(this).data('id');
                $('#activate_tahun_akademik_id').val(id);
                
                $.ajax({
                    url: "{{ route('tahun-akademik.get-semesters', ':id') }}".replace(':id', id),
                    type: 'GET',
                    success: function(response) {
                        let html = '';
                        if (response.data && response.data.length > 0) {
                            response.data.forEach(function(semester, index) {
                                html += `
                                    <div class="custom-control custom-radio mb-2">
                                        <input type="radio" id="semester_${semester.id}" name="semester_id" 
                                            class="custom-control-input" value="${semester.id}" ${index === 0 ? 'checked' : ''}>
                                        <label class="custom-control-label" for="semester_${semester.id}">
                                            ${semester.nama} (${semester.kode})
                                        </label>
                                    </div>
                                `;
                            });
                            $('#semester-options-container').html(html);
                            $('#activateSemesterModal').modal('show');
                        } else {
                            Swal.fire('Error!', 'Tahun akademik ini tidak memiliki data semester.', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Gagal memuat data semester.', 'error');
                    }
                });
            });

            $('#activateSemesterForm').submit(function(e) {
                e.preventDefault();
                let id = $('#activate_tahun_akademik_id').val();
                let semesterId = $('input[name="semester_id"]:checked').val();

                Swal.fire({
                    title: 'Aktifkan Tahun Akademik?',
                    text: 'Tahun akademik lain akan otomatis dinonaktifkan',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Aktifkan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('tahun-akademik.set-active', [':id', ':semester']) }}"
                                .replace(':id', id)
                                .replace(':semester', semesterId),
                            type: 'POST',
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Gagal!',
                                    xhr.responseJSON?.message || 'Tidak dapat mengaktifkan tahun akademik',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
