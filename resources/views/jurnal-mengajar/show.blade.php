@extends('layouts.app')

@section('title', 'Detail Jurnal Mengajar')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>Detail Jurnal Mengajar</h1>
            <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                <ol class="breadcrumb pt-0">
                    <li class="breadcrumb-item"><a href="{{ route('jurnal-mengajar.index') }}">Jurnal Mengajar</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detail</li>
                </ol>
            </nav>
            <div class="separator mb-5"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-8 offset-md-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4>{{ $journal->jadwalPelajaran->mataPelajaranKelas->mataPelajaran->nama }}</h4>
                        @if($journal->is_approved)
                            <span class="badge badge-success">APPROVED</span>
                        @else
                            <span class="badge badge-warning">PENDING APPROVAL</span>
                        @endif
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Guru:</strong> {{ $journal->jadwalPelajaran->mataPelajaranKelas->guru->nama }}</p>
                            <p><strong>Kelas:</strong> {{ $journal->jadwalPelajaran->mataPelajaranKelas->kelas->nama }}</p>
                            <p><strong>Tanggal:</strong> {{ $journal->tanggal->format('l, d F Y') }}</p>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <p><strong>Waktu:</strong> {{ $journal->jam_mulai->format('H:i') }} - {{ $journal->jam_selesai->format('H:i') }}</p>
                            <p><strong>Kehadiran:</strong> {{ $journal->jumlah_hadir }} Hadir, {{ $journal->jumlah_tidak_hadir }} Absen</p>
                            @if($journal->is_approved)
                                <p><small class="text-muted">Approved by {{ $journal->approvedBy->name ?? 'System' }} at {{ $journal->updated_at->format('d/m/Y H:i') }}</small></p>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h5>Materi / Pokok Bahasan</h5>
                        <p>{!! nl2br(e($journal->materi)) !!}</p>
                    </div>

                    <div class="mb-4">
                        <h5>Metode Pembelajaran</h5>
                        <p>{{ $journal->metode_pembelajaran ?? '-' }}</p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Hambatan</h5>
                            <p>{{ $journal->hambatan ?? 'Tidak ada' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Solusi</h5>
                            <p>{{ $journal->solusi ?? '-' }}</p>
                        </div>
                    </div>

                    @if($journal->catatan)
                        <div class="mb-4">
                            <h5>Catatan Tambahan</h5>
                            <p>{{ $journal->catatan }}</p>
                        </div>
                    @endif

                    <div class="mb-4">
                        <h5>Detail Presensi Siswa</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama Siswa</th>
                                        <th width="15%" class="text-center">Status</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($journal->presensiMapel as $p)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $p->siswa->nama_lengkap }}</td>
                                        <td class="text-center">
                                            @if($p->status == 'H') <span class="badge badge-success">Hadir</span>
                                            @elseif($p->status == 'I') <span class="badge badge-info">Izin</span>
                                            @elseif($p->status == 'S') <span class="badge badge-warning">Sakit</span>
                                            @elseif($p->status == 'A') <span class="badge badge-danger">Alpha</span>
                                            @endif
                                        </td>
                                        <td>{{ $p->keterangan ?? '-' }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="4" class="text-center">Data presensi tidak ditemukan</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="text-center mt-5">
                        <a href="{{ route('jurnal-mengajar.index') }}" class="btn btn-outline-secondary">KEMBALI KE LIST</a>
                        @if(!$journal->is_approved && auth()->user()->hasRole(['admin', 'super-admin']))
                             <form action="{{ route('jurnal-mengajar.approve', $journal->id) }}" method="POST" class="d-inline ml-2">
                                @csrf
                                <button type="submit" class="btn btn-success">APPROVE JURNAL</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
