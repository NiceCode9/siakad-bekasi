@extends('layouts.app')

@section('title', 'Penempatan PKL Siswa')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Data Penempatan PKL Siswa</h4>
        <a href="{{ route('pkl.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah Penempatan
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped" id="tablePklSiswa">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Siswa</th>
                        <th>Tempat PKL</th>
                        <th>Pembimbing Sekolah</th>
                        <th>Periode</th>
                        <th>Status</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#tablePklSiswa').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('pkl.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'siswa_nama', name: 'siswa.nama' },
                { data: 'perusahaan', name: 'perusahaanPkl.nama' },
                { data: 'pembimbing', name: 'pembimbingSekolah.nama' },
                { data: 'periode', name: 'tanggal_mulai' },
                { data: 'status_label', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });

        $(document).on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Hapus Data?',
                text: "Data penempatan ini akan dihapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('pkl') }}/" + id,
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            $('#tablePklSiswa').DataTable().ajax.reload();
                            Swal.fire('Terhapus!', response.message, 'success');
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', xhr.responseJSON.message, 'error');
                        }
                    });
                }
            })
        });
    });
</script>
@endpush
