@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets') }}/css/vendor/component-custom-switch.min.css" />
@endpush

@section('title', 'Data Kurikulum')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1>Manajemen Kurikulum</h1>
                <div class="top-right-button-container">
                    <button class="btn btn-primary btn-sm" id="btn-add">
                        <i class="simple-icon-plus"></i> Tambah Data
                    </button>
                </div>
                <div class="separator mb-5"></div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="text-nowrap" id="kurikulum-table">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama</th>
                                        <th>Tahun Mulai</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kurikulum as $item)
                                        <tr>
                                            <td>{{ $item->kode }}</td>
                                            <td>{{ $item->nama }}</td>
                                            <td>{{ $item->tahun_mulai }}</td>
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
                                                        data-id="{{ $item->id }}" title="Edit Kurikulum">
                                                        <i class="simple-icon-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-danger btn-sm btn-delete" title="Hapus Kurikulum"
                                                        data-id="{{ $item->id }}">
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

    <div class="modal fade" id="kurikulumModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Kurikulum</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="kurikulumForm">
                    <div class="modal-body">
                        <input type="hidden" id="kurikulum_id" name="kurikulum_id">

                        <div class="form-group">
                            <label for="kode">Kode Kurikulum <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kode" name="kode">
                            {{-- <small class="form-text text-muted">Nama menu yang akan ditampilkan</small> --}}
                        </div>

                        <div class="form-group">
                            <label for="nama">Nama Kurikulum</label>
                            <input type="text" class="form-control" id="nama" name="nama">
                            {{-- <small class="form-text text-muted">Naam</small> --}}
                        </div>

                        <div class="form-group">
                            <label for="tahun_mulai">Tahun Mulai</label>
                            <input type="number" class="form-control" name="tahun_mulai" id="tahun_mulai" min="2000"
                                max="3000" placeholder="YYYY" required>
                            <small class="form-text text-muted">Tahun Kurikulum dimulai</small>
                        </div>

                        <div class="form-group">
                            <label for="deskripsi">Deskripsi <span class="text-danger">*</span></label>
                            <textarea name="deskripsi" id="deskripsi" class="form-control" rows="5"></textarea>
                        </div>

                        <div class="form-group row mb-1">
                            <label class="col-12 col-form-label">Status Aktif</label>
                            <div class="col-12">
                                <div class="custom-switch custom-switch-small custom-switch-secondary mb-2">
                                    <input class="custom-switch-input" id="is_active" name="is_active" type="checkbox"
                                        checked>
                                    <label class="custom-switch-btn" for="is_active"></label>
                                </div>
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

            $('#kurikulum-table').DataTable({
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

            function clearValidationErrors() {
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
                $('.text-danger').remove();
            }


            $('#btn-add').click(function() {
                $('#kurikulumForm')[0].reset();
                $('#menu_id').val('');
                $('#modalTitle').text('Tambah Menu');
                $('#is_active').prop('checked', true);
                clearValidationErrors();
                $('#kurikulumModal').modal('show');
            });

            $(document).on('click', '.btn-edit', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ route('kurikulum.edit', ':kurikulum') }}".replace(':kurikulum', id),
                    type: 'GET',
                    success: function(response) {
                        console.log(response.data.id);
                        $('#kurikulum_id').val(response.data.id);
                        $('#kode').val(response.data.kode);
                        $('#nama').val(response.data.nama);
                        $('#tahun_mulai').val(response.data.tahun_mulai);
                        $('#deskripsi').val(response.data.deskripsi);
                        $('#is_active').prop('checked', response.data.is_active);

                        $('#modalTitle').text('Edit Kurikulum');
                        $('#kurikulumModal').modal('show');
                    },
                    error: function() {
                        Swal.fire('Error!', 'Gagal memuat data kurikulum', 'error');
                    }
                });
            });

            $('#kurikulumForm').submit(function(e) {
                e.preventDefault();

                var id = $('#kurikulum_id').val();
                var url = id ? "{{ url('kurikulum') }}/" + id :
                    "{{ route('kurikulum.store') }}";
                var type = id ? 'PUT' : 'POST';

                var formData = {
                    kode: $('#kode').val(),
                    nama: $('#nama').val(),
                    tahun_mulai: $('#tahun_mulai').val(),
                    deskripsi: $('#deskripsi').val(),
                    is_active: $('#is_active').is(':checked') ? 1 : 0
                };

                if (id) {
                    formData._method = 'PUT'; // 🔥 method spoofing
                }

                $('#btn-save').prop('disabled', true).html(
                    '<i class="simple-icon-refresh"></i> Menyimpan...');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        clearValidationErrors();
                        $('#kurikulumModal').modal('hide');

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

                        // Bersihkan error lama
                        $('.is-invalid').removeClass('is-invalid');
                        $('.invalid-feedback').remove();

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;

                            $.each(errors, function(field, messages) {
                                let input = $('[name="' + field + '"]');

                                // untuk checkbox
                                if (input.attr('type') === 'checkbox') {
                                    input.closest('.form-group')
                                        .append('<div class="text-danger mt-1">' +
                                            messages[0] + '</div>');
                                } else {
                                    input.addClass('is-invalid');
                                    input.after('<div class="invalid-feedback">' +
                                        messages[0] + '</div>');
                                    console.log(input)
                                }
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

            // Delete Button
            $(document).on('click', '.btn-delete', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data menu akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('kurikulum.destroy', ':kurikulum') }}".replace(
                                ':kurikulum',
                                id),
                            type: 'DELETE',
                            success: function(response) {
                                table.ajax.reload();
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
                                        'Gagal menghapus menu'
                                });
                            }
                        });
                    }
                });
            });

            // Set Active Kurikulum (Button)
            $(document).on('click', '.btn-set-active', function() {
                let button = $(this);
                let id = button.data('id');

                Swal.fire({
                    title: 'Aktifkan Kurikulum?',
                    text: 'Kurikulum lain akan otomatis dinonaktifkan',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Aktifkan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('kurikulum.set-active', ':kurikulum') }}"
                                .replace(':kurikulum', id),
                            type: 'POST',
                            success: function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Kurikulum berhasil diaktifkan',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function() {
                                Swal.fire(
                                    'Gagal!',
                                    'Tidak dapat mengaktifkan kurikulum',
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
