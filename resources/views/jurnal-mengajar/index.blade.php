@extends('layouts.app')

@section('title', 'Jurnal Mengajar')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>Jurnal Mengajar</h1>
            @role('guru')
                <div class="top-right-button-container">
                    <a href="{{ route('jurnal-mengajar.create') }}" class="btn btn-primary btn-lg top-right-button">ISI JURNAL BARU</a>
                </div>
            @endrole
            <div class="separator mb-5"></div>
        </div>
    </div>

    @unlessrole('guru')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-4">Filter Pencarian</h5>
                    <form method="GET" action="{{ route('jurnal-mengajar.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Guru</label>
                                    <select name="guru_id" class="form-control select2-single">
                                        <option value="">-- Semua Guru --</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ request('guru_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->nama_lengkap_gelar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Kelas</label>
                                    <select name="kelas_id" class="form-control select2-single">
                                        <option value="">-- Semua Kelas --</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ request('kelas_id') == $class->id ? 'selected' : '' }}>{{ $class->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Mulai Tanggal</label>
                                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Sampai Tanggal</label>
                                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="d-flex">
                                        <button type="submit" class="btn btn-primary mr-2">Filter</button>
                                        <a href="{{ route('jurnal-mengajar.index') }}" class="btn btn-outline-secondary">Reset</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endunlessrole

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-hover" id="jurnalTable">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Guru</th>
                                <th>Mata Pelajaran / Kelas</th>
                                <th>Jam</th>
                                <th>Hadir/Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($journals as $journal)
                            <tr>
                                <td>{{ $journal->tanggal->format('d/m/Y') }}</td>
                                <td>{{ $journal->jadwalPelajaran->mataPelajaranKelas->guru->nama_lengkap_gelar }}</td>
                                <td>
                                    {{ $journal->jadwalPelajaran->mataPelajaranKelas->mataPelajaran->nama }}<br>
                                    <small class="text-muted">{{ $journal->jadwalPelajaran->mataPelajaranKelas->kelas->nama }}</small>
                                </td>
                                <td>{{ $journal->jam_mulai->format('H:i') }} - {{ $journal->jam_selesai->format('H:i') }}</td>
                                <td>{{ $journal->jumlah_hadir }} / {{ ($journal->jumlah_hadir + $journal->jumlah_tidak_hadir) }}</td>
                                <td>
                                    @if($journal->is_approved)
                                        <span class="badge badge-success">APPROVED</span>
                                    @else
                                        <span class="badge badge-warning">PENDING</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('jurnal-mengajar.show', $journal->id) }}" class="btn btn-xs btn-outline-info">Detail</a>
                                    @if(!$journal->is_approved && auth()->user()->hasRole(['admin', 'super-admin', 'kepala-sekolah']))
                                        <form action="{{ route('jurnal-mengajar.approve', $journal->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui jurnal ini?')">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-outline-success">Approve</button>
                                        </form>
                                    @endif
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
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#jurnalTable')) {
            $('#jurnalTable').DataTable().destroy();
        }

        $('#jurnalTable').DataTable({
            autoWidth: false,
            responsive: true,
            searching: true,
            paging: true,
            info: true,
            scrollX: true,
            scrollY: '500px',
            scrollCollapse: true,
            order: [[0, "desc"]], // Urutkan berdasarkan tanggal terbaru
            columnDefs: [
                { "orderable": false, "targets": [6] } // Matikan sorting untuk kolom Aksi
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            }
        });
    });
</script>
@endpush
