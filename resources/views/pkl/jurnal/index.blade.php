@extends('layouts.app')

@section('title', 'Jurnal Kegiatan PKL')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Jurnal Kegiatan PKL</h4>
            @if($pkl)
                <small class="text-muted">{{ $pkl->perusahaanPkl->nama }} | Pembimbing: {{ $pkl->pembimbingSekolah->nama ?? '-' }}</small>
            @endif
        </div>
        @if($pkl && $pkl->status == 'aktif')
        <a href="{{ route('jurnal-pkl.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah Jurnal Harian
        </a>
        @endif
    </div>

    @if(!$pkl)
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i> Anda belum memiliki penempatan PKL aktif. Silakan hubungi koordinator PKL.
    </div>
    @else
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="tableJurnal">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Tanggal</th>
                            <th>Kegiatan</th>
                            <th width="10%">Foto</th>
                            <th width="10%">Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Modal View Image -->
<div class="modal fade" id="modalViewImage" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-0">
                <img src="" id="imgPreview" class="img-fluid">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        @if($pkl)
        $('#tableJurnal').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('jurnal-pkl.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'tanggal_monitoring', name: 'tanggal_monitoring' },
                { data: 'kegiatan', name: 'kegiatan' },
                { data: 'foto_preview', name: 'foto', orderable: false, searchable: false },
                { data: 'status_label', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
        @endif

        $(document).on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Hapus Jurnal?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('jurnal-pkl') }}/" + id,
                        type: 'DELETE',
                        data: { _token: "{{ csrf_token() }}" },
                        success: function(response) {
                            $('#tableJurnal').DataTable().ajax.reload();
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

    function viewImage(url) {
        $('#imgPreview').attr('src', url);
        $('#modalViewImage').modal('show');
    }
</script>
@endpush
