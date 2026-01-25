@extends('layouts.app')

@section('title', 'Data Mata Pelajaran')

@section('content')

    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Data Mata Pelajaran</h4>
            <a href="{{ route('mata-pelajaran.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Mata Pelajaran
            </a>
        </div>

        <div class="separator mb-5"></div>

        <!-- Filter -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Kurikulum</label>
                            <select id="filter_kurikulum" class="form-control form-control-sm">
                                <option value="">Semua Kurikulum</option>
                                @foreach ($kurikulum as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Jenis</label>
                            <select id="filter_jenis" class="form-control form-control-sm">
                                <option value="">Semua Jenis</option>
                                <option value="umum">Umum</option>
                                <option value="kejuruan">Kejuruan</option>
                                <option value="muatan_lokal">Muatan Lokal</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Kelompok Mapel</label>
                            <select id="filter_kelompok" class="form-control form-control-sm">
                                <option value="">Semua Kelompok</option>
                                @foreach ($kelompokMapel as $km)
                                    <option value="{{ $km->id }}">{{ $km->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Status</label>
                            <select id="filter_status" class="form-control form-control-sm">
                                <option value="">Semua Status</option>
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
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

        <!-- Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="mataPelajaranTable" class="data-table text-nowrap">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Kode</th>
                                <th>Nama Mata Pelajaran</th>
                                <th>Kurikulum</th>
                                <th>Kelompok Mapel</th>
                                <th>Jenis</th>
                                <th>Kategori</th>
                                <th>KKM</th>
                                <th>Status</th>
                                <th width="12%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // DataTable
            var table = $('#mataPelajaranTable').DataTable({
                sDom: '<"row view-filter"<"col-sm-12"<"float-right"l><"float-left"f><"clearfix">>>t<"row view-pager"<"col-sm-12"<"text-center"ip>>>',
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('mata-pelajaran.index') }}",
                    data: function(d) {
                        d.kurikulum_id = $('#filter_kurikulum').val();
                        d.jenis = $('#filter_jenis').val();
                        d.kelompok_mapel_id = $('#filter_kelompok').val();
                        d.is_active = $('#filter_status').val();
                    }
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
                        data: 'kurikulum_nama',
                        name: 'kurikulum.nama'
                    },
                    {
                        data: 'kelompok_mapel_nama',
                        name: 'kelompokMapel.nama'
                    },
                    {
                        data: 'jenis_badge',
                        name: 'jenis'
                    },
                    {
                        data: 'kategori_badge',
                        name: 'kategori'
                    },
                    {
                        data: 'kkm',
                        name: 'kkm'
                    },
                    {
                        data: 'status',
                        name: 'is_active'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [2, 'asc']
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
                    paginate: {
                        previous: "<i class='simple-icon-arrow-left'></i>",
                        next: "<i class='simple-icon-arrow-right'></i>"
                    },
                    search: "_INPUT_",
                    searchPlaceholder: "Search...",
                    lengthMenu: "Items Per Page _MENU_"
                },
            });

            // Filter
            $('#btnFilter').click(function() {
                table.draw();
            });

            // Reset Filter
            $('#btnReset').click(function() {
                $('#filter_kurikulum').val('');
                $('#filter_jenis').val('');
                $('#filter_kelompok').val('');
                $('#filter_status').val('');
                table.draw();
            });

            // Toggle Active
            $('#mataPelajaranTable').on('click', '.btn-toggle-active', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Ubah Status?',
                    text: "Status mata pelajaran akan diubah!",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, ubah!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('mata-pelajaran') }}/" + id +
                                "/toggle-active",
                            type: 'POST',
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

            // Delete
            $('#mataPelajaranTable').on('click', '.btn-delete', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data mata pelajaran akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('mata-pelajaran') }}/" + id,
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
