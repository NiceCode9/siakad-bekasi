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

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-hover">
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
                                <td>{{ $journal->jadwalPelajaran->mataPelajaranKelas->guru->nama }}</td>
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
                                    @if(!$journal->is_approved && auth()->user()->hasRole(['admin', 'super-admin']))
                                        <form action="{{ route('jurnal-mengajar.approve', $journal->id) }}" method="POST" class="d-inline">
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
