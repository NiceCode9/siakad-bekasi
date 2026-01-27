@extends('layouts.app')

@section('title', 'Manajemen Raport')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>Manajemen Raport</h1>
            <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                <ol class="breadcrumb pt-0">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Raport</li>
                </ol>
            </nav>
            <div class="separator mb-5"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-4">Daftar Siswa Kelas: {{ $kelas->nama ?? 'Semua Kelas' }} (Semester {{ $semester->nama }})</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered datatable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NISN</th>
                                    <th>Nama Siswa</th>
                                    <th>Status Raport</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($siswas as $idx => $siswa)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>{{ $siswa->nisn }}</td>
                                    <td>{{ $siswa->nama }}</td>
                                    <td>
                                        @if($siswa->raports->first())
                                            <span class="badge badge-{{ $siswa->raports->first()->status == 'published' ? 'success' : ($siswa->raports->first()->status == 'approved' ? 'info' : 'secondary') }}">
                                                {{ strtoupper($siswa->raports->first()->status) }}
                                            </span>
                                        @else
                                            <span class="badge badge-warning">BELUM GENERATE</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            @if($siswa->raports->first())
                                                <a href="{{ route('raport.show', $siswa->raports->first()->id) }}" class="btn btn-info btn-sm">
                                                    <i class="simple-icon-eye"></i> Detail
                                                </a>
                                                <form action="{{ route('raport.generate', [$siswa->id, $semester->id]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Generate ulang raport akan memperbarui nilai. Lanjutkan?')">
                                                        <i class="simple-icon-refresh"></i> Re-generate
                                                    </button>
                                                </form>
                                                <a href="{{ route('raport.print', $siswa->raports->first()->id) }}" class="btn btn-primary btn-sm">
                                                    <i class="simple-icon-printer"></i> Cetak
                                                </a>
                                            @else
                                                <form action="{{ route('raport.generate', [$siswa->id, $semester->id]) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="simple-icon-plus"></i> Generate Raport
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.datatable').DataTable({
            language: {
                paginate: {
                    previous: "<i class='simple-icon-arrow-left'></i>",
                    next: "<i class='simple-icon-arrow-right'></i>"
                }
            },
            drawCallback: function () {
                $($(".dataTables_wrapper .pagination li:first-of-type"))
                    .find("a")
                    .addClass("prev");
                $($(".dataTables_wrapper .pagination li:last-of-type"))
                    .find("a")
                    .addClass("next");

                $(".dataTables_wrapper .pagination").addClass("pagination-sm");
            }
        });
    });
</script>
@endpush
