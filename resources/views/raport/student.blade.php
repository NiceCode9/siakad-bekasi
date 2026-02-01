@extends('layouts.app')

@section('title', 'Raport Saya')

@section('content')
<div class="row">
    <div class="col-12">
        <h1>Raport Saya</h1>
        <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
            <ol class="breadcrumb pt-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
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
                <h5 class="mb-4">Riwayat Nilai & Raport</h5>
                
                <div class="table-responsive">
                    <table class="table table-striped table-bordered datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tahun Akademik</th>
                                <th>Semester</th>
                                <th>Kelas</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($raports as $idx => $raport)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>{{ $raport->semester->tahunAkademik->nama ?? '-' }}</td>
                                <td>{{ $raport->semester->nama }}</td>
                                <td>{{ $raport->kelas->nama }}</td>
                                <td>
                                    <span class="badge badge-{{ $raport->status == 'published' ? 'success' : 'secondary' }}">
                                        {{ strtoupper($raport->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($raport->status == 'published')
                                        <a href="{{ route('raport.show', $raport->id) }}" class="btn btn-info btn-sm">
                                            <i class="simple-icon-eye"></i> Lihat Raport
                                        </a>
                                        <a href="{{ route('raport.print', $raport->id) }}" class="btn btn-primary btn-sm">
                                            <i class="simple-icon-printer"></i> Download PDF
                                        </a>
                                    @else
                                        <span class="text-muted small">Belum dipublikasikan</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada raport yang tersedia.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if ($('.datatable').length > 0) {
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
        }
    });
</script>
@endpush
