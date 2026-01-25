@extends('layouts.app')

@section('title', 'Data Kelas')

@section('content')

    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Data Kelas</h4>
            <div>
                <button type="button" class="btn btn-info btn-sm mr-2" data-toggle="modal" data-target="#bulkCreateModal">
                    <i class="fas fa-plus-circle"></i> Bulk Create
                </button>
                <button type="button" class="btn btn-warning btn-sm mr-2" data-toggle="modal" data-target="#copyKelasModal">
                    <i class="fas fa-copy"></i> Copy Kelas
                </button>
                <a href="{{ route('kelas.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Kelas
                </a>
            </div>
        </div>

        <!-- Filter -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Semester</label>
                            <select id="filter_semester" class="form-control">
                                <option value="">Semua Semester</option>
                                @foreach ($semester as $s)
                                    <option value="{{ $s->id }}">
                                        {{ $s->nama }} - {{ $s->tahunAkademik->nama ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tingkat</label>
                            <select id="filter_tingkat" class="form-control">
                                <option value="">Semua Tingkat</option>
                                <option value="X">X</option>
                                <option value="XI">XI</option>
                                <option value="XII">XII</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Jurusan</label>
                            <select id="filter_jurusan" class="form-control">
                                <option value="">Semua Jurusan</option>
                                @foreach ($jurusan as $j)
                                    <option value="{{ $j->id }}">{{ $j->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="button" id="btnFilter" class="btn btn-primary btn-sm btn-block">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="kelasTable" class="table-hover text-nowrap" style="width: 100%;">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Kode</th>
                                <th>Nama Kelas</th>
                                <th>Tingkat</th>
                                <th>Jurusan</th>
                                <th>Wali Kelas</th>
                                <th>Semester</th>
                                <th>Kuota/Siswa</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Bulk Create -->
    <div class="modal fade" id="bulkCreateModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="formBulkCreate" action="{{ route('kelas.bulk-create') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Bulk Create Kelas</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Semester <span class="text-danger">*</span></label>
                            <select name="semester_id" class="form-control" required>
                                <option value="">-- Pilih Semester --</option>
                                @foreach ($semester as $s)
                                    <option value="{{ $s->id }}">{{ $s->nama }} -
                                        {{ $s->tahunAkademik->nama ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Jurusan <span class="text-danger">*</span></label>
                            <select name="jurusan_id" class="form-control" required>
                                <option value="">-- Pilih Jurusan --</option>
                                @foreach ($jurusan as $j)
                                    <option value="{{ $j->id }}">{{ $j->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tingkat <span class="text-danger">*</span></label>
                            <select name="tingkat" class="form-control" required>
                                <option value="">-- Pilih Tingkat --</option>
                                <option value="X">X</option>
                                <option value="XI">XI</option>
                                <option value="XII">XII</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Jumlah Kelas <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah_kelas" class="form-control" min="1" max="10"
                                required>
                            <small class="form-text text-muted">Maksimal 10 kelas</small>
                        </div>
                        <div class="form-group">
                            <label>Kuota per Kelas <span class="text-danger">*</span></label>
                            <input type="number" name="kuota" class="form-control" min="1" max="50"
                                required>
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

    <!-- Modal Copy Kelas -->
    <div class="modal fade" id="copyKelasModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="formCopyKelas" action="{{ route('kelas.copy-from-previous') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Copy Kelas dari Semester Sebelumnya</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Semester Asal <span class="text-danger">*</span></label>
                            <select name="semester_asal_id" class="form-control" required>
                                <option value="">-- Pilih Semester Asal --</option>
                                @foreach ($semester as $s)
                                    <option value="{{ $s->id }}">{{ $s->nama }} -
                                        {{ $s->tahunAkademik->nama ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tingkat Asal <span class="text-danger">*</span></label>
                            <select name="tingkat_asal" class="form-control" required>
                                <option value="">-- Pilih Tingkat Asal --</option>
                                <option value="X">X</option>
                                <option value="XI">XI</option>
                                <option value="XII">XII</option>
                            </select>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label>Semester Tujuan <span class="text-danger">*</span></label>
                            <select name="semester_tujuan_id" class="form-control" required>
                                <option value="">-- Pilih Semester Tujuan --</option>
                                @foreach ($semester as $s)
                                    <option value="{{ $s->id }}">{{ $s->nama }} -
                                        {{ $s->tahunAkademik->nama ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tingkat Tujuan <span class="text-danger">*</span></label>
                            <select name="tingkat_tujuan" class="form-control" required>
                                <option value="">-- Pilih Tingkat Tujuan --</option>
                                <option value="X">X</option>
                                <option value="XI">XI</option>
                                <option value="XII">XII</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">Copy Kelas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // DataTable
            var table = $('#kelasTable').DataTable({
                processing: true,
                serverSide: true,
                sDom: '<"row view-filter"<"col-sm-12"<"float-right"l><"float-left"f><"clearfix">>>t<"row view-pager"<"col-sm-12"<"text-center"ip>>>',
                responsive: true,
                ajax: {
                    url: "{{ route('kelas.index') }}",
                    data: function(d) {
                        d.semester_id = $('#filter_semester').val();
                        d.tingkat = $('#filter_tingkat').val();
                        d.jurusan_id = $('#filter_jurusan').val();
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
                        data: 'tingkat',
                        name: 'tingkat'
                    },
                    {
                        data: 'jurusan_nama',
                        name: 'jurusan.nama'
                    },
                    {
                        data: 'wali_kelas_nama',
                        name: 'waliKelas.nama_lengkap'
                    },
                    {
                        data: 'semester_info',
                        name: 'semester.nama'
                    },
                    {
                        data: 'kuota_info',
                        name: 'kuota',
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
                    search: "_INPUT_",
                    searchPlaceholder: "Cari...",
                    processing: "Memuat data...",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        previous: "<i class='simple-icon-arrow-left'></i>",
                        next: "<i class='simple-icon-arrow-right'></i>"
                    },
                    emptyTable: "Tidak ada data yang tersedia",
                    zeroRecords: "Tidak ada data yang cocok",
                }
            });

            // Filter
            $('#btnFilter').click(function() {
                table.draw();
            });

            // Delete
            $('#kelasTable').on('click', '.btn-delete', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data kelas akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('kelas') }}/" + id,
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
