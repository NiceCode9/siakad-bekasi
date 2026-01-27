@extends('layouts.app')

@section('title', 'Bank Soal CBT')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Bank Soal</h4>
            <small class="text-muted">Kumpulan soal ujian komputer</small>
        </div>
        <div>
            <button type="button" class="btn btn-success btn-sm mr-1" data-toggle="modal" data-target="#modalImport">
                <i class="fas fa-file-excel"></i> Import Excel
            </button>
            <a href="{{ route('bank-soal.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Buat Bank Soal
            </a>
        </div>
    </div>

    <!-- Filter -->
    <div class="card mb-3">
        <div class="card-body py-3">
            <form id="filterForm" class="row align-items-end">
                <div class="col-md-3">
                    <label>Mata Pelajaran</label>
                    <select name="mata_pelajaran_id" id="filter_mapel" class="form-control select2">
                        <option value="">L- Semua Mapel --</option>
                        @foreach($mapel as $m)
                            <option value="{{ $m->id }}">{{ $m->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                     <label>Tingkat Kesulitan</label>
                     <select name="tingkat_kesulitan" id="filter_tingkat" class="form-control">
                        <option value="">-- Semua --</option>
                        <option value="mudah">Mudah</option>
                        <option value="sedang">Sedang</option>
                        <option value="sulit">Sulit</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" id="btnFilter" class="btn btn-secondary btn-block">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tableBankSoal" class="table table-bordered table-striped table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Kode</th>
                            <th>Mata Pelajaran</th>
                            <th>Nama Bank Soal</th>
                            <th>Tingkat</th>
                            <th>Jumlah Soal</th>
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

<!-- Modal Import -->
<div class="modal fade" id="modalImport" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('bank-soal.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Import Soal dari Excel (CSV)</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Target Bank Soal <span class="text-danger">*</span></label>
                        {{-- Usually we import into existing bank. Loading all banks via AJAX might be better if many. --}}
                        {{-- For now let's reuse DataTables logic or just simple select --}}
                        <select name="bank_soal_id" class="form-control select2" style="width:100%" required>
                            <option value="">-- Pilih Bank --</option>
                           {{-- We need to pass banks to view. Controller index passes only mapel. 
                                Let's load via AJAX or just use the ones we have? 
                                Actually controller index passes `mapel` only. 
                                Let's assume user creates bank first, then imports.
                                So we list all? Or we can query in view? No. 
                                Let's update controller to pass banks? Or use select2 ajax.
                                Quick fix: just add logic to load recent banks or all?
                                Let's reload banks in View Composer or inject.
                                For now, I'll use a placeholder and suggest user to go to detail page to import? 
                                Or simply, add `banks` to index.
                           --}}
                           @php $banks = \App\Models\BankSoal::orderBy('nama')->limit(100)->get(); @endphp
                           @foreach($banks as $b)
                               <option value="{{ $b->id }}">{{ $b->kode }} - {{ $b->nama }}</option>
                           @endforeach
                        </select>
                        <small class="text-muted">Hanya menampilkan 100 bank terbaru.</small>
                    </div>
                    <div class="form-group">
                        <label>File CSV <span class="text-danger">*</span></label>
                        <input type="file" name="file_import" class="form-control-file" required accept=".csv,.txt">
                    </div>
                    <div class="alert alert-info small">
                        Gunakan template ini: <a href="{{ route('bank-soal.template') }}" class="font-weight-bold">Download Template CSV</a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
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
            $('.select2').select2();

            var table = $('#tableBankSoal').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('bank-soal.index') }}",
                    data: function(d) {
                        d.mata_pelajaran_id = $('#filter_mapel').val();
                        d.tingkat_kesulitan = $('#filter_tingkat').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'kode', name: 'kode' },
                    { data: 'mapel_nama', name: 'mataPelajaran.nama' },
                    { data: 'nama', name: 'nama' },
                    { data: 'tingkat_kesulitan', name: 'tingkat_kesulitan' },
                    { data: 'jumlah_soal', name: 'jumlah_soal', searchable: false },
                    { data: 'status', name: 'is_active' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            $('#btnFilter').click(function(){
                table.draw();
            });

            $('#tableBankSoal').on('click', '.btn-delete', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus data?',
                    text: 'Data yang dihapus tidak dapat dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('bank-soal') }}/" + id,
                            type: 'DELETE',
                            data: { _token: "{{ csrf_token() }}" },
                            success: function(res) {
                                Swal.fire('Berhasil', res.message, 'success');
                                table.draw();
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal', xhr.responseJSON.message || 'Error', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
