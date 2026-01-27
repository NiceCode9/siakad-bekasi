@extends('layouts.app')

@section('title', 'Detail Ujian')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Ujian</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h4 class="font-weight-bold">{{ $jadwal->nama_ujian }}</h4>
                        <p class="text-muted">{{ $jadwal->mataPelajaranKelas->mataPelajaran->nama }}</p>
                    </div>

                    <table class="table table-borderless">
                        <tr>
                            <td width="30%">Waktu Pelaksanaan</td>
                            <td>: {{ $jadwal->tanggal_mulai->format('d/m/Y H:i') }} s/d {{ $jadwal->tanggal_selesai->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td>Durasi</td>
                            <td>: {{ $jadwal->durasi }} Menit</td>
                        </tr>
                        <tr>
                            <td>Jumlah Soal</td>
                            <td>: {{ $jadwal->jumlah_soal }} Soal</td>
                        </tr>
                        <tr>
                            <td>Status Anda</td>
                            <td>: 
                                @if($ujianSiswa)
                                    @if($ujianSiswa->status == 'selesai')
                                        <span class="badge badge-success">SELESAI</span>
                                        @if($jadwal->tampilkan_nilai)
                                            <div class="mt-2 h4">Nilai: <strong>{{ number_format($ujianSiswa->nilai, 1) }}</strong></div>
                                        @endif
                                    @else
                                        <span class="badge badge-warning">SEDANG MENGERJAKAN</span>
                                    @endif
                                @else
                                    <span class="badge badge-secondary">BELUM MULAI</span>
                                @endif
                            </td>
                        </tr>
                    </table>

                    <hr>

                    @if($ujianSiswa && $ujianSiswa->status == 'selesai')
                        <div class="text-center">
                            <a href="{{ route('ujian-siswa.index') }}" class="btn btn-secondary">Kembali ke Daftar</a>
                        </div>
                    @elseif($ujianSiswa && $ujianSiswa->status == 'sedang_mengerjakan')
                         <div class="text-center">
                            <a href="{{ route('ujian-siswa.take', $jadwal->id) }}" class="btn btn-primary btn-lg pulse-button">Lanjutkan Mengerjakan</a>
                        </div>
                    @else
                        {{-- New Session --}}
                        <form action="{{ route('ujian-siswa.start', $jadwal->id) }}" method="POST" class="text-center">
                            @csrf
                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">
                                    <label class="sr-only">Token Ujian</label>
                                    <input type="text" name="token" class="form-control text-center text-uppercase font-weight-bold" placeholder="Masukkan Token Ujian" required autocomplete="off" style="letter-spacing: 3px;">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-lg px-5">Mulai Ujian</button>
                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .pulse-button {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7); }
        70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(0, 123, 255, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(0, 123, 255, 0); }
    }
</style>
@endpush
