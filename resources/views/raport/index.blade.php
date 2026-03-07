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
            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin'))
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-4">Filter Data</h5>
                    <form action="{{ route('raport.index') }}" method="GET" id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tahun Akademik</label>
                                    <select name="tahun_akademik_id" id="tahun_akademik_id" class="form-control select2-single">
                                        <option value="">Semua Tahun Akademik</option>
                                        @foreach($tahunAkademiks as $tahun)
                                            <option value="{{ $tahun->id }}" {{ $filterTahun == $tahun->id ? 'selected' : '' }}>{{ $tahun->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Semester</label>
                                    <select name="semester_id" id="semester_id" class="form-control select2-single">
                                        <option value="">Pilih Semester</option>
                                        @if($filterTahun)
                                            {{-- Will be populated via JS or initial load --}}
                                            @foreach(\App\Models\Semester::where('tahun_akademik_id', $filterTahun)->get() as $sem)
                                                <option value="{{ $sem->id }}" {{ $filterSemester == $sem->id ? 'selected' : '' }}>{{ $sem->nama }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Siswa</label>
                                    <select name="siswa_id" id="siswa_id" class="form-control select2-single">
                                        <option value="">Cari Siswa...</option>
                                        @foreach($allSiswa as $s)
                                            <option value="{{ $s->id }}" {{ $filterSiswa == $s->id ? 'selected' : '' }}>{{ $s->nisn }} - {{ $s->nama_lengkap }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="d-flex">
                                        <button type="submit" class="btn btn-primary btn-block">Filter</button>
                                        <a href="{{ route('raport.index') }}" class="btn btn-outline-secondary ml-2">Reset</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-4">Daftar Siswa Kelas: {{ $kelas ? $kelas->nama : 'Semua Kelas' }} (Semester {{$semester ? $semester->nama :'' }})</h5>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered datatable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NISN</th>
                                    <th>Nama Siswa</th>
                                    <th>Aksi & Status per Komponen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($siswas as $idx => $siswa)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>{{ $siswa->nisn }}</td>
                                    <td>{{ $siswa->nama_lengkap }}</td>
                                    <td colspan="2">
                                        <div class="row">
                                            @foreach($komponens as $comp)
                                                @php
                                                    $raportComp = $siswa->raports->where('komponen_nilai_id', $comp->id)->first();
                                                @endphp
                                                <div class="col-md-6 mb-3">
                                                    <div class="border p-2 rounded shadow-sm bg-white">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <strong>{{ $comp->nama }}</strong>
                                                            @if($raportComp)
                                                                <span class="badge badge-{{ $raportComp->status == 'published' ? 'success' : ($raportComp->status == 'approved' ? 'info' : 'secondary') }}">
                                                                    {{ strtoupper($raportComp->status) }}
                                                                </span>
                                                            @else
                                                                <span class="badge badge-warning">BELUM GENERATE</span>
                                                            @endif
                                                        </div>
                                                        <div class="btn-group w-100">
                                                            @if($raportComp)
                                                                <a href="{{ route('raport.show', $raportComp->id) }}" class="btn btn-info btn-xs" title="Lihat Detail"><i class="simple-icon-eye"></i></a>
                                                                <form action="{{ route('raport.generate', [$siswa->id, $semester->id, $comp->id]) }}" method="POST" class="d-inline w-50" style="margin: 0;">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-warning btn-xs w-100" style="border-radius: 0;" onclick="return confirm('Generate ulang raport akan memperbarui nilai. Lanjutkan?')" title="Generate Ulang"><i class="simple-icon-refresh"></i> Re-gen</button>
                                                                </form>
                                                                <a href="{{ route('raport.print', $raportComp->id) }}" class="btn btn-primary btn-xs w-50" title="Cetak Raport" style="border-top-left-radius: 0; border-bottom-left-radius: 0;"><i class="simple-icon-printer"></i> Cetak</a>
                                                            @else
                                                                <form action="{{ route('raport.generate', [$siswa->id, $semester->id, $comp->id]) }}" method="POST" class="w-100 mb-0">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-success btn-xs btn-block"><i class="simple-icon-plus"></i> Generate Raport</button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
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
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2-single').select2({
                theme: "bootstrap",
                placeholder: "",
                maximumSelectionSize: 6,
                containerCssClass: ":all:"
            });
        }

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

        // Dependent Dropdown for Semester
        $('#tahun_akademik_id').on('change', function() {
            let tahunId = $(this).val();
            let semesterSelect = $('#semester_id');
            
            semesterSelect.html('<option value="">Memuat...</option>');
            
            if (tahunId) {
                $.ajax({
                    url: "{{ route('raport.get-semesters', ':id') }}".replace(':id', tahunId),
                    type: "GET",
                    success: function(data) {
                        semesterSelect.html('<option value="">Pilih Semester</option>');
                        $.each(data, function(key, value) {
                            semesterSelect.append('<option value="' + value.id + '">' + value.nama + '</option>');
                        });
                    }
                });
            } else {
                semesterSelect.html('<option value="">Pilih Semester</option>');
            }
        });
    });
</script>
@endpush
