@extends('layouts.app')

@section('title', 'Atur Mata Pelajaran - ' . $kelas->nama)

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0">Atur Mata Pelajaran</h4>
                <p class="text-muted mb-0">Kelas: {{ $kelas->nama }} ({{ $kelas->semester->nama ?? '-' }})</p>
            </div>
            <div>
                <a href="{{ route('kelas.show', $kelas->id) }}" class="btn btn-secondary btn-sm mr-2">
                    <i class="fas fa-arrow-left"></i> Kembali ke Kelas
                </a>
                <button type="button" class="btn btn-primary btn-sm" id="btnAddMapel">
                    <i class="fas fa-plus"></i> Tambah Mapel
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="mapelKelasTable" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Kode</th>
                                <th>Mata Pelajaran</th>
                                <th>Guru Pengajar</th>
                                <th>Jam/Minggu</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form Add Mapel -->
    <div class="modal fade" id="modalAddMapel" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="formAddMapel">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Mata Pelajaran ke Kelas</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Mata Pelajaran <span class="text-danger">*</span></label>
                            <select name="mata_pelajaran_id" id="selectMapel" class="form-control" style="width: 100%" required>
                                <option value="">-- Pilih Mapel --</option>
                                @foreach($mataPelajaran as $mp)
                                    <option value="{{ $mp->id }}">{{ $mp->nama }} - {{ $mp->kode }} ({{ ucfirst($mp->jenis) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Guru Pengajar <span class="text-danger">*</span></label>
                            <select name="guru_id" class="form-control" style="width: 100%" required>
                                <option value="">-- Pilih Guru --</option>
                                @foreach (\App\Models\Guru::active()->orderBy('nama_lengkap')->get() as $g)
                                    <option value="{{ $g->id }}">{{ $g->nama_lengkap }} ({{ $g->nip ?? '-' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Jam Per Minggu <span class="text-danger">*</span></label>
                            <input type="number" name="jam_per_minggu" class="form-control" value="2" min="1" max="10" required>
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

    <!-- Modal Edit Mapel -->
    <div class="modal fade" id="modalEditMapel" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="formEditMapel">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Mata Pelajaran</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Mata Pelajaran</label>
                            <input type="text" id="edit_nama_mapel" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label>Jam Per Minggu <span class="text-danger">*</span></label>
                            <input type="number" name="jam_per_minggu" id="edit_jam" class="form-control" min="1" max="10" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Assign Guru -->
    <div class="modal fade" id="modalAssignGuru" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="formAssignGuru">
                    @csrf
                    <input type="hidden" name="mata_pelajaran_kelas_id" id="assign_mpk_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Atur Guru Pengajar</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Mata Pelajaran</label>
                            <input type="text" id="assign_nama_mapel" class="form-control" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label>Pilih Guru <span class="text-danger">*</span></label>
                            <select name="guru_id" id="selectGuru" class="form-control" style="width: 100%" required>
                                <!-- Async data -->
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                         <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
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
            // Select2 Init
            $('#selectMapel').select2({ dropdownParent: $('#modalAddMapel') });
            $('#selectGuru').select2({
                dropdownParent: $('#modalAssignGuru'),
                placeholder: 'Cari Guru...',
                ajax: {
                    url: "{{ route('mata-pelajaran-kelas.get-gurus') }}",
                    dataType: 'json',
                    delay: 250,
                    processResults: function (data) {
                        return { results: data };
                    },
                    cache: true
                }
            });

            // DataTable
            var table = $('#mapelKelasTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('kelas.mata-pelajaran.index', $kelas->id) }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'kode_mapel', name: 'mataPelajaran.kode' },
                    { data: 'nama_mapel', name: 'mataPelajaran.nama' },
                    { data: 'guru_pengajar', name: 'guru_pengajar', orderable: false, searchable: false },
                    { data: 'jam_per_minggu', name: 'jam_per_minggu' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            // Add Mapel
            $('#btnAddMapel').click(function() {
                $('#formAddMapel')[0].reset();
                $('#selectMapel').val('').trigger('change');
                $('#modalAddMapel').modal('show');
            });

            $('#formAddMapel').submit(function(e) {
                e.preventDefault();
                $.post("{{ route('kelas.mata-pelajaran.store', $kelas->id) }}", $(this).serialize())
                    .done(function(res) {
                        $('#modalAddMapel').modal('hide');
                        Swal.fire('Berhasil', res.message, 'success');
                        table.draw();
                    })
                    .fail(function(xhr) {
                        Swal.fire('Gagal', xhr.responseJSON.message, 'error');
                    });
            });

            // Edit Mapel
            $('#mapelKelasTable').on('click', '.btn-edit-mapel', function() {
                var id = $(this).data('id');
                $.get("{{ url('mata-pelajaran-kelas') }}/" + id, function(res) {
                    $('#edit_id').val(res.id);
                    $('#edit_nama_mapel').val(res.mata_pelajaran.nama);
                    $('#edit_jam').val(res.jam_per_minggu);
                    $('#modalEditMapel').modal('show');
                });
            });

            $('#formEditMapel').submit(function(e) {
                e.preventDefault();
                var id = $('#edit_id').val();
                $.ajax({
                    url: "{{ url('mata-pelajaran-kelas') }}/" + id,
                    type: 'PUT',
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#modalEditMapel').modal('hide');
                        Swal.fire('Berhasil', res.message, 'success');
                        table.draw();
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal', xhr.responseJSON.message, 'error');
                    }
                });
            });

            // Delete Mapel
            $('#mapelKelasTable').on('click', '.btn-delete-mapel', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus Mata Pelajaran?',
                    text: 'Data mata pelajaran akan dihapus dari kelas ini.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('mata-pelajaran-kelas') }}/" + id,
                            type: 'DELETE',
                            data: { _token: "{{ csrf_token() }}" },
                            success: function(res) {
                                Swal.fire('Berhasil', res.message, 'success');
                                table.draw();
                            }
                        });
                    }
                });
            });

             // Assign Guru Modal
            $('#mapelKelasTable').on('click', '.btn-assign-guru', function() {
                var id = $(this).data('id');
                
                $.get("{{ url('mata-pelajaran-kelas') }}/" + id, function(res) {
                    $('#assign_mpk_id').val(id);
                    $('#assign_nama_mapel').val(res.mata_pelajaran.nama);
                    
                    if (res.guru) {
                        var newOption = new Option(res.guru.nama_lengkap, res.guru.id, true, true);
                        $('#selectGuru').append(newOption).trigger('change');
                    } else {
                        $('#selectGuru').val('').trigger('change');
                    }

                    $('#modalAssignGuru').modal('show');
                });
            });

            // Submit Add Guru
            $('#formAssignGuru').submit(function(e) {
                e.preventDefault();
                var id = $('#assign_mpk_id').val();
                $.post("{{ url('mata-pelajaran-kelas') }}/" + id + "/assign-guru", $(this).serialize())
                    .done(function(res) {
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false });
                        $('#selectGuru').val('').trigger('change');
                        
                        // Refresh guru list manually or just reload table
                        table.draw();
                        
                         // Re-open modal logic could be complex due to async, simpler to close or refresh List
                         // For now, let's close modal - user can open again to see changes or add more
                         $('#modalAssignGuru').modal('hide');
                    })
                    .fail(function(xhr) {
                         Swal.fire('Gagal', xhr.responseJSON.message, 'error');
                    });
            });

        });
    </script>
@endpush
