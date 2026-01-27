@extends('layouts.app')

@section('title', 'Persetujuan Jurnal PKL')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Persetujuan Jurnal PKL Siswa</h4>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="tableJurnalMentor">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Siswa</th>
                            <th width="12%">Tanggal</th>
                            <th>Kegiatan</th>
                            <th width="10%">Foto</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
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
        var table = $('#tableJurnalMentor').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('jurnal-pkl.pembimbing') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'siswa_info', name: 'pkl.siswa.nama' },
                { data: 'tanggal', name: 'tanggal' },
                { data: 'kegiatan', name: 'kegiatan' },
                { data: 'foto_preview', name: 'foto', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    });

    function viewImage(url) {
        $('#imgPreview').attr('src', url);
        $('#modalViewImage').modal('show');
    }

    function approve(id) {
        updateStatus(id, 'disetujui');
    }

    function reject(id) {
        Swal.fire({
            title: 'Tolak Jurnal',
            text: 'Berikan alasan penolakan:',
            input: 'text',
            showCancelButton: true,
            confirmButtonText: 'Tolak',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                updateStatus(id, 'ditolak', result.value);
            }
        });
    }

    function updateStatus(id, status, catatan = '') {
        $.ajax({
            url: "{{ url('jurnal-pkl') }}/" + id + "/status",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                status: status,
                catatan: catatan
            },
            success: function(response) {
                $('#tableJurnalMentor').DataTable().ajax.reload();
                Swal.fire('Berhasil', response.message, 'success');
            }
        });
    }
</script>
@endpush
