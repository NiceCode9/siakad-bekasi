@extends('layouts.app')

@section('title', 'Jadwal Ujian CBT')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Jadwal Ujian</h4>
            <small class="text-muted">Kelola sesi ujian siswa</small>
        </div>
        <a href="{{ route('jadwal-ujian.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Buat Jadwal
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tableJadwal" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Ujian</th>
                            <th>Kelas & Mapel</th>
                            <th>Waktu Pelaksanaan</th>
                            <th>Durasi</th>
                            <th>Token</th>
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
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#tableJadwal').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('jadwal-ujian.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'nama_ujian', name: 'nama_ujian' },
                    { data: 'kelas_mapel', name: 'mataPelajaranKelas.kelas.nama' },
                    { data: 'waktu', name: 'tanggal_mulai' },
                    { data: 'durasi', name: 'durasi', render: function(data){ return data + ' menit'; } },
                    { data: 'token', name: 'token' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            $('#tableJadwal').on('click', '.btn-delete', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus jadwal?',
                    text: 'Data yang dihapus tidak dapat dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('jadwal-ujian') }}/" + id,
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
