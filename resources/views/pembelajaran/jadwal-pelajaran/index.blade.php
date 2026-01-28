@extends('layouts.app')

@section('title', 'Jadwal Pelajaran')

@section('content')

    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Jadwal Pelajaran</h4>
            <div>
                <div class="btn-group mr-2" role="group">
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown">
                        <i class="fas fa-eye"></i> Lihat Jadwal
                    </button>
                    <div class="dropdown-menu">
                        <h6 class="dropdown-header">Pilih Kelas</h6>
                        @foreach ($kelas as $k)
                            <a class="dropdown-item" href="{{ route('jadwal-pelajaran.by-kelas', $k->id) }}">
                                {{ $k->nama }}
                            </a>
                        @endforeach
                    </div>
                </div>
                <a href="{{ route('jadwal-pelajaran.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Jadwal
                </a>
            </div>
        </div>

        <!-- Filter -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label>Kelas</label>
                            <select id="filter_kelas" class="form-control form-control-sm">
                                <option value="">Semua Kelas</option>
                                @foreach ($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label>Hari</label>
                            <select id="filter_hari" class="form-control form-control-sm">
                                <option value="">Semua Hari</option>
                                @foreach ($hari as $h)
                                    <option value="{{ $h }}">{{ $h }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
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
                    <table id="jadwalTable" class="data-table">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Hari</th>
                                <th>Waktu</th>
                                <th>Kelas</th>
                                <th>Mata Pelajaran</th>
                                <th>Guru</th>
                                <th>Ruang</th>
                                <th width="10%">Aksi</th>
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
            var table = $('#jadwalTable').DataTable({
                sDom: '<"row view-filter"<"col-sm-12"<"float-right"l><"float-left"f><"clearfix">>>t<"row view-pager"<"col-sm-12"<"text-center"ip>>>',
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('jadwal-pelajaran.index') }}",
                    data: function(d) {
                        d.kelas_id = $('#filter_kelas').val();
                        d.hari = $('#filter_hari').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'hari_badge',
                        name: 'hari'
                    },
                    {
                        data: 'waktu',
                        name: 'waktu'
                    },
                    {
                        data: 'kelas_nama',
                        name: 'mataPelajaranKelas.kelas.nama'
                    },
                    {
                        data: 'mapel_nama',
                        name: 'mataPelajaranKelas.mataPelajaran.nama'
                    },
                    {
                        data: 'guru_nama',
                        name: 'mataPelajaranKelas.guru.nama_lengkap'
                    },
                    {
                        data: 'ruang',
                        name: 'ruang'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'asc'],
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
                $('#filter_kelas').val('');
                $('#filter_hari').val('');
                table.draw();
            });

            // Delete
            $('#jadwalTable').on('click', '.btn-delete', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Jadwal pelajaran akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('pembelajaran/jadwal-pelajaran') }}/" + id,
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
