@extends('layouts.app')

@section('title', 'Penilaian PKL')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Penilaian PKL Siswa</h4>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-nowrap" style="width:100%" id="tableNilaiPkl">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Siswa</th>
                                <th>Perusahaan</th>
                                <th>Nilai Akhir</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#tableNilaiPkl').DataTable({
                processing: true,
                responsive: true,
                serverSide: true,
                ajax: "{{ route('pkl-nilai.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'siswa_info',
                        name: 'siswa.nama_lengkap'
                    },
                    {
                        data: 'perusahaan',
                        name: 'perusahaanPkl.nama'
                    },
                    {
                        data: 'nilai_akhir',
                        name: 'nilaiPkl.nilai_akhir'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });
    </script>
@endpush
