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
                @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin'))
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="mb-4">Filter Data</h5>
                            <form action="{{ route('raport.index') }}" method="GET" id="filterForm">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Tahun Akademik</label>
                                            <select name="tahun_akademik_id" id="tahun_akademik_id"
                                                class="form-control select2-single">
                                                <option value="">Semua Tahun Akademik</option>
                                                @foreach ($tahunAkademiks as $tahun)
                                                    <option value="{{ $tahun->id }}"
                                                        {{ $filterTahun == $tahun->id ? 'selected' : '' }}>
                                                        {{ $tahun->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Semester</label>
                                            <select name="semester_id" id="semester_id" class="form-control select2-single">
                                                <option value="">Pilih Semester</option>
                                                @if ($filterTahun)
                                                    {{-- Will be populated via JS or initial load --}}
                                                    @foreach (\App\Models\Semester::where('tahun_akademik_id', $filterTahun)->get() as $sem)
                                                        <option value="{{ $sem->id }}"
                                                            {{ $filterSemester == $sem->id ? 'selected' : '' }}>
                                                            {{ $sem->nama }}</option>
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
                                                @foreach ($allSiswa as $s)
                                                    <option value="{{ $s->id }}"
                                                        {{ $filterSiswa == $s->id ? 'selected' : '' }}>{{ $s->nisn }} -
                                                        {{ $s->nama_lengkap }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <div class="d-flex">
                                                <button type="submit" class="btn btn-primary btn-block">Filter</button>
                                                <a href="{{ route('raport.index') }}"
                                                    class="btn btn-outline-secondary ml-2">Reset</a>
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
                        <h5 class="mb-4">Daftar Siswa Kelas: {{ $kelas ? $kelas->nama : 'Semua Kelas' }} (Semester
                            {{ $semester ? $semester->nama : '' }})</h5>

                        <div class="row">
                            @foreach ($siswas as $idx => $siswa)
                                <div class="col-12 col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 shadow-sm border">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center mr-3"
                                                    style="width: 45px; height: 45px; font-size: 1.2rem; font-weight: bold;">
                                                    {{ substr($siswa->nama_lengkap, 0, 1) }}
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 font-weight-bold text-truncate"
                                                        title="{{ $siswa->nama_lengkap }}" style="max-width: 200px;">
                                                        {{ $siswa->nama_lengkap }}</h6>
                                                    <p class="text-muted mb-0 small">NISN: {{ $siswa->nisn }}</p>
                                                </div>
                                            </div>

                                            <div class="separator mb-3"></div>

                                            <h6 class="text-muted mb-2 small font-weight-bold">Status Komponen Raport:</h6>

                                            @php
                                                // Variabel untuk melacak status keseluruhan jika semua komponen sukses
                                                $allPublished = true;
                                                $anyGenerated = false;
                                            @endphp

                                            <div class="komponen-list" style="max-height: 250px; overflow-y: auto;">
                                                @foreach ($komponens as $comp)
                                                    @php
                                                        $raportComp = $siswa->raports
                                                            ->where('komponen_nilai_id', $comp->id)
                                                            ->first();
                                                        if ($raportComp && $raportComp->status != 'published') {
                                                            $allPublished = false;
                                                        }
                                                        if (!$raportComp) {
                                                            $allPublished = false;
                                                        } else {
                                                            $anyGenerated = true;
                                                        }
                                                    @endphp

                                                    <div class="border rounded p-2 mb-2 bg-light">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span class="font-weight-semibold small text-truncate pr-2"
                                                                title="{{ $comp->nama }}">{{ $comp->nama }}</span>
                                                            @if ($raportComp)
                                                                @php
                                                                    $badgeClass = 'secondary';
                                                                    if ($raportComp->status == 'published') {
                                                                        $badgeClass = 'success';
                                                                    } elseif ($raportComp->status == 'approved') {
                                                                        $badgeClass = 'info';
                                                                    } elseif ($raportComp->status == 'draft') {
                                                                        $badgeClass = 'warning';
                                                                    }
                                                                @endphp
                                                                <span
                                                                    class="badge badge-pill badge-{{ $badgeClass }} mb-0">{{ strtoupper($raportComp->status) }}</span>
                                                            @else
                                                                <span
                                                                    class="badge badge-pill badge-outline-secondary mb-0">BELUM
                                                                    ADA</span>
                                                            @endif
                                                        </div>

                                                        <div class="d-flex gx-2">
                                                            @if ($raportComp)
                                                                <a href="{{ route('raport.show', $raportComp->id) }}"
                                                                    class="btn btn-outline-info btn-xs flex-fill mr-1"
                                                                    title="Lihat"><i class="simple-icon-eye"></i></a>
                                                                <form
                                                                    action="{{ route('raport.generate', [$siswa->id, $semester->id, $comp->id]) }}"
                                                                    method="POST" class="flex-fill mr-1 m-0">
                                                                    @csrf
                                                                    <button type="submit"
                                                                        class="btn btn-outline-warning btn-xs w-100"
                                                                        onclick="return confirm('Generate ulang raport akan memperbarui nilai. Lanjutkan?')"
                                                                        title="Re-gen"><i
                                                                            class="simple-icon-refresh"></i></button>
                                                                </form>
                                                                <a href="{{ route('raport.print', $raportComp->id) }}"
                                                                    class="btn btn-primary btn-xs flex-fill" title="Cetak"
                                                                    target="_blank"><i class="simple-icon-printer"></i></a>
                                                            @else
                                                                <form
                                                                    action="{{ route('raport.generate', [$siswa->id, $semester->id, $comp->id]) }}"
                                                                    method="POST" class="w-100 m-0">
                                                                    @csrf
                                                                    <button type="submit"
                                                                        class="btn btn-outline-success btn-xs btn-block"><i
                                                                            class="simple-icon-plus"></i> Generate</button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div
                                            class="card-footer bg-transparent p-3 d-flex justify-content-between align-items-center">
                                            <span class="text-muted small">Status Keseluruhan:</span>
                                            @if ($allPublished && count($komponens) > 0)
                                                <span class="badge badge-success"><i class="simple-icon-check"></i>
                                                    SELESAI</span>
                                            @elseif($anyGenerated)
                                                <span class="badge badge-warning">PROSES</span>
                                            @else
                                                <span class="badge badge-secondary">KOSONG</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            @if ($siswas->isEmpty())
                                <div class="col-12 text-center py-5">
                                    <div class="text-muted">
                                        <i class="simple-icon-folder-alt" style="font-size: 3rem;"></i>
                                        <p class="mt-3">Pilih data Kelas dan Semester terlebih dahulu, atau tidak ada
                                            siswa yang ditemukan.</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Pagination Links --}}
                        <div class="row mt-4">
                            <div class="col-12 d-flex justify-content-center">
                                @if (method_exists($siswas, 'links'))
                                    {{ $siswas->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
                                @endif
                            </div>
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

            // Datatable init context removed as we are no longer using table layout

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
                                semesterSelect.append('<option value="' + value.id +
                                    '">' + value.nama + '</option>');
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
