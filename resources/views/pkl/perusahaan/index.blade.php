@extends('layouts.app')

@section('title', 'Data Tempat PKL / Industri')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Daftar Tempat PKL / Industri</h4>
        <a href="{{ route('perusahaan-pkl.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah Industri
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped" id="tableTempatPkl">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Perusahaan</th>
                        <th>Bidang Usaha</th>
                        <th>Alamat</th>
                        <th>Kontak Person</th>
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
        $('#tableTempatPkl').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('perusahaan-pkl.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama', name: 'nama' },
                { data: 'bidang_usaha', name: 'bidang_usaha' },
                { data: 'alamat', name: 'alamat' },
                { data: 'nama_kontak', name: 'nama_kontak' },
                { data: 'status_label', name: 'is_active' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });

        $(document).on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Hapus Data?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('perusahaan-pkl') }}/" + id,
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            $('#tableTempatPkl').DataTable().ajax.reload();
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
