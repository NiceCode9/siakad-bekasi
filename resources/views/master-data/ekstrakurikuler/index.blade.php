@extends('layouts.app')

@section('title', 'Data Ekstrakurikuler')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Data Ekstrakurikuler</h4>
        <button type="button" class="btn btn-primary btn-sm" id="btnCreate">
            <i class="fas fa-plus"></i> Tambah Ekskul
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tableEkskul" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Ekskul</th>
                            <th>Pembina</th>
                            <th>Jadwal (Hari & Jam)</th>
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

<!-- Modal Form -->
<div class="modal fade" id="modalForm" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formEkskul">
                @csrf
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Ekskul</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Ekstrakurikuler <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="nama" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Pembina</label>
                        <select name="pembina_id" id="pembina_id" class="form-control select2" style="width: 100%">
                            <option value="">-- Pilih Pembina --</option>
                            @foreach($gurus as $g)
                                <option value="{{ $g->id }}">{{ $g->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Hari</label>
                                <select name="hari" id="hari" class="form-control">
                                    <option value="">- Pilih -</option>
                                    <option value="Senin">Senin</option>
                                    <option value="Selasa">Selasa</option>
                                    <option value="Rabu">Rabu</option>
                                    <option value="Kamis">Kamis</option>
                                    <option value="Jumat">Jumat</option>
                                    <option value="Sabtu">Sabtu</option>
                                    <option value="Minggu">Minggu</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Jam Mulai</label>
                                <input type="time" name="jam_mulai" id="jam_mulai" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Jam Selesai</label>
                                <input type="time" name="jam_selesai" id="jam_selesai" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" checked>
                            <label class="custom-control-label" for="is_active">Aktif</label>
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
            $('.select2').select2({ dropdownParent: $('#modalForm') });

            var table = $('#tableEkskul').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('ekstrakurikuler.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'nama', name: 'nama' },
                    { data: 'pembina_nama', name: 'pembina.nama_lengkap' },
                    { data: 'waktu', name: 'waktu', orderable: false },
                    { data: 'status', name: 'is_active' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            $('#btnCreate').click(function() {
                $('#formEkskul')[0].reset();
                $('#id').val('');
                $('#pembina_id').val('').trigger('change');
                $('#modalTitle').text('Tambah Ekskul');
                $('#modalForm').modal('show');
            });

            // Edit
            $('#tableEkskul').on('click', '.btn-edit', function() {
                var id = $(this).data('id');
                $.get("{{ url('ekstrakurikuler') }}/" + id + "/edit", function(res) {
                    $('#id').val(res.id);
                    $('#nama').val(res.nama);
                    $('#pembina_id').val(res.pembina_id).trigger('change');
                    $('#hari').val(res.hari);
                    $('#jam_mulai').val(res.jam_mulai);
                    $('#jam_selesai').val(res.jam_selesai);
                    $('#is_active').prop('checked', res.is_active);
                    
                    $('#modalTitle').text('Edit Ekskul');
                    $('#modalForm').modal('show');
                });
            });

            // Submit
            $('#formEkskul').submit(function(e) {
                e.preventDefault();
                var id = $('#id').val();
                var url = id ? "{{ url('ekstrakurikuler') }}/" + id : "{{ route('ekstrakurikuler.store') }}";
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
                        Swal.fire('Gagal', msg, 'error');
                    }
                });
            });

            // Delete
            $('#tableEkskul').on('click', '.btn-delete', function() {
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
                            url: "{{ url('ekstrakurikuler') }}/" + id,
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
