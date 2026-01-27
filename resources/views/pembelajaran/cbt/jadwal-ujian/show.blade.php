@extends('layouts.app')

@section('title', 'Detail Jadwal Ujian')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">{{ $jadwalUjian->nama_ujian }}</h4>
            <span class="badge badge-{{ $jadwalUjian->status == 'aktif' ? 'success' : ($jadwalUjian->status == 'draft' ? 'secondary' : 'dark') }}">
                {{ strtoupper($jadwalUjian->status) }}
            </span>
        </div>
        <a href="{{ route('jadwal-ujian.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header bg-white font-weight-bold">Informasi Ujian</div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <small class="text-muted d-block uppercase">Token Ujian</small>
                        <h1 class="display-4 font-weight-bold text-primary mb-0">{{ $jadwalUjian->token }}</h1>
                        <p class="small text-muted">Berikan token ini kepada siswa</p>
                    </div>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted">Kelas</td>
                            <td class="font-weight-bold">{{ $jadwalUjian->mataPelajaranKelas->kelas->nama }}</td>
                        </tr>
                        <tr>
                             <td class="text-muted">Mapel</td>
                            <td class="font-weight-bold">{{ $jadwalUjian->mataPelajaranKelas->mataPelajaran->nama }}</td>
                        </tr>
                        <tr>
                             <td class="text-muted">Bank Soal</td>
                            <td class="font-weight-bold">{{ $jadwalUjian->bankSoal->kode }}</td>
                        </tr>
                        <tr>
                             <td class="text-muted">Mulai</td>
                            <td>{{ $jadwalUjian->tanggal_mulai->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                             <td class="text-muted">Selesai</td>
                            <td>{{ $jadwalUjian->tanggal_selesai->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                             <td class="text-muted">Durasi</td>
                            <td>{{ $jadwalUjian->durasi }} Menit</td>
                        </tr>
                    </table>

                    <hr>
                    <label class="font-weight-bold">Kontrol Status</label>
                    <form action="{{ route('jadwal-ujian.status', $jadwalUjian->id) }}" method="POST">
                        @csrf
                        <div class="btn-group btn-block">
                            <button type="submit" name="status" value="draft" class="btn btn-secondary {{ $jadwalUjian->status == 'draft' ? 'active' : '' }}">Draft</button>
                            <button type="submit" name="status" value="aktif" class="btn btn-success {{ $jadwalUjian->status == 'aktif' ? 'active' : '' }}">Aktif</button>
                            <button type="submit" name="status" value="selesai" class="btn btn-dark {{ $jadwalUjian->status == 'selesai' ? 'active' : '' }}">Selesai</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Daftar Soal Terpilih (Generated)</h6>
                    <span class="badge badge-info">{{ $jadwalUjian->soalUjian->count() }} Soal</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th width="10%">No</th>
                                    <th>Pertanyaan</th>
                                    <th>Tipe</th>
                                    <th>Bobot</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwalUjian->soalUjian as $su)
                                    <tr>
                                        <td>{{ $su->urutan }}</td>
                                        <td>{!! Str::limit(strip_tags($su->soal->pertanyaan), 80) !!}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $su->soal->tipe_soal)) }}</td>
                                        <td>{{ $su->soal->bobot }}</td>
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
