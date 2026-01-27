@extends('layouts.app')

@section('title', 'Komponen Nilai')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Komponen Nilai</h4>
        <button type="button" class="btn btn-primary btn-sm" id="btnCreate">
            <i class="fas fa-plus"></i> Tambah Komponen
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tableKomponen" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Kurikulum</th>
                            <th>Kode</th>
                            <th>Nama Komponen</th>
                            <th>Kategori</th>
                            <th>Bobot (%)</th>
                            <th>Keterangan</th>
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
<div class="modal fade" id="modalForm" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formKomponen">
                @csrf
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Komponen</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kurikulum <span class="text-danger">*</span></label>
                        <select name="kurikulum_id" id="kurikulum_id" class="form-control" required>
                            <option value="">-- Pilih Kurikulum --</option>
                            @foreach($kurikulum as $k)
                                <option value="{{ $k->id }}">{{ $k->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                             <div class="form-group">
                                <label>Kode <span class="text-danger">*</span></label>
                                <input type="text" name="kode" id="kode" class="form-control" placeholder="Ex: PH1" required>
                            </div>
                        </div>
                        <div class="col-md-8">
                             <div class="form-group">
                                <label>Nama <span class="text-danger">*</span></label>
                                <input type="text" name="nama" id="nama" class="form-control" placeholder="Ex: Penilaian Harian 1" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kategori <span class="text-danger">*</span></label>
                                <select name="kategori" id="kategori" class="form-control" required>
                                    <option value="pengetahuan">Pengetahuan</option>
                                    <option value="keterampilan">Keterampilan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Bobot (%) <span class="text-danger">*</span></label>
                                <input type="number" name="bobot" id="bobot" class="form-control" min="0" max="100" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" rows="2"></textarea>
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
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#tableKomponen').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('komponen-nilai.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'kurikulum_nama', name: 'kurikulum.nama' },
                    { data: 'kode', name: 'kode' },
                    { data: 'nama', name: 'nama' },
                    { data: 'kategori', name: 'kategori' },
                    { data: 'bobot', name: 'bobot' },
                    { data: 'keterangan', name: 'keterangan' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            $('#btnCreate').click(function() {
                $('#formKomponen')[0].reset();
                $('#id').val('');
                $('#modalTitle').text('Tambah Komponen');
                $('#modalForm').modal('show');
            });

            // Edit
            $('#tableKomponen').on('click', '.btn-edit', function() {
                var id = $(this).data('id');
                $.get("{{ url('komponen-nilai') }}/" + id + "/edit", function(res) {
                    $('#id').val(res.id);
                    $('#kurikulum_id').val(res.kurikulum_id);
                    $('#kode').val(res.kode);
                    $('#nama').val(res.nama);
                    $('#kategori').val(res.kategori);
                    $('#bobot').val(res.bobot);
                    $('#keterangan').val(res.keterangan);
                    
                    $('#modalTitle').text('Edit Komponen');
                    $('#modalForm').modal('show');
                });
            });

            // Submit
            $('#formKomponen').submit(function(e) {
                e.preventDefault();
                var id = $('#id').val();
                var url = id ? "{{ url('komponen-nilai') }}/" + id : "{{ route('komponen-nilai.store') }}";
                var method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#modalForm').modal('hide');
                        Swal.fire('Berhasil', res.message, 'success');
                        table.draw();
                    },
                    error: function(xhr) {
                        var msg = xhr.responseJSON.message || 'Terjadi kesalahan';
                        if(xhr.responseJSON.errors) {
                            msg = '';
                            $.each(xhr.responseJSON.errors, function(k, v) { msg += v[0] + '<br>'; });
                        }
                        Swal.fire('Gagal', msg, 'error');
                    }
                });
            });

            // Delete
            $('#tableKomponen').on('click', '.btn-delete', function() {
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
                            url: "{{ url('komponen-nilai') }}/" + id,
                            type: 'DELETE',
                            data: { _token: "{{ csrf_token() }}" },
                            success: function(res) {
                                Swal.fire('Berhasil', res.message, 'success');
                                table.draw();
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal', xhr.responseJSON.message, 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
