@extends('layouts.app')

@section('title', 'Detail Raport - ' . $raport->siswa->nama)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>Detail Raport: {{ $raport->siswa->nama }}</h1>
            <div class="top-right-button-container d-flex">
                @can('approve-raport')
                    @if($raport->status == 'draft')
                        <form action="{{ route('raport.approve', $raport->id) }}" method="POST" class="mr-2">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg">SETUJUI KEPSEK</button>
                        </form>
                    @endif
                @endcan

                @can('manage-raport')
                    @if($raport->status == 'approved')
                        <form action="{{ route('raport.publish', $raport->id) }}" method="POST" class="mr-2">
                            @csrf
                            <button type="submit" class="btn btn-info btn-lg">PUBLISH RAPORT</button>
                        </form>
                    @endif
                @endcan

                <a href="{{ route('raport.print', $raport->id) }}" class="btn btn-primary btn-lg top-right-button">
                    <i class="simple-icon-printer"></i> CETAK PDF
                </a>
            </div>
            <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                <ol class="breadcrumb pt-0">
                    <li class="breadcrumb-item"><a href="{{ route('raport.index') }}">Raport</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detail</li>
                </ol>
            </nav>
            <div class="separator mb-5"></div>
        </div>
    </div>

    <form action="{{ route('raport.update', $raport->id) }}" method="POST">
        @csrf
        <div class="row">
            <!-- Academic Scores -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-4">Capaian Hasil Belajar</h5>
                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>Mata Pelajaran</th>
                                    <th class="text-center">Pengetahuan</th>
                                    <th class="text-center">Keterampilan</th>
                                    <th class="text-center">Nilai Akhir</th>
                                    <th class="text-center">Predikat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($raport->raportDetail as $detail)
                                <tr>
                                    <td>{{ $detail->mataPelajaran->nama }}</td>
                                    <td class="text-center">{{ number_format($detail->nilai_pengetahuan, 0) }}</td>
                                    <td class="text-center">{{ number_format($detail->nilai_keterampilan, 0) }}</td>
                                    <td class="text-center"><strong>{{ number_format($detail->nilai_akhir, 0) }}</strong></td>
                                    <td class="text-center">{{ $detail->predikat }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- PKL & Ekskul -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="mb-4">Praktik Kerja Lapangan</h5>
                                @if($nilaiPkl)
                                    <p><strong>Mitra:</strong> {{ $nilaiPkl->pkl->perusahaanPkl->nama }}</p>
                                    <p><strong>Nilai Akhir:</strong> {{ $nilaiPkl->nilai_akhir }}</p>
                                    <p><strong>Keterangan:</strong> {{ $nilaiPkl->catatan_industri ?? '-' }}</p>
                                @else
                                    <p class="text-muted italic">Data PKL belum tersedia.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="mb-4">Ekstrakurikuler</h5>
                                @forelse($nilaiEkskul as $ekskul)
                                    <p><strong>{{ $ekskul->ekstrakurikuler->nama }}:</strong> {{ $ekskul->nilai }} ({{ $ekskul->predikat }})</p>
                                @empty
                                    <p class="text-muted italic">Data Ekstrakurikuler belum tersedia.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sikap -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-4">Perkembangan Karakter (Sikap)</h5>
                        @if($nilaiSikap)
                            <div class="mb-3">
                                <h6>Spiritual</h6>
                                <p>{{ $nilaiSikap->deskripsi_spiritual ?? 'Baik' }}</p>
                            </div>
                            <div>
                                <h6>Sosial</h6>
                                <p>{{ $nilaiSikap->deskripsi_sosial ?? 'Baik' }}</p>
                            </div>
                        @else
                            <p class="text-muted italic">Data Sikap belum diisi.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar: Attendance & Notes -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-4">Ketidakhadiran</h5>
                        <div class="form-group">
                            <label>Sakit</label>
                            <input type="number" name="jumlah_sakit" class="form-control" value="{{ $raport->jumlah_sakit }}">
                        </div>
                        <div class="form-group">
                            <label>Izin</label>
                            <input type="number" name="jumlah_izin" class="form-control" value="{{ $raport->jumlah_izin }}">
                        </div>
                        <div class="form-group">
                            <label>Tanpa Keterangan (Alpha)</label>
                            <input type="number" name="jumlah_alpha" class="form-control" value="{{ $raport->jumlah_alpha }}">
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-4">Catatan Wali Kelas</h5>
                        <div class="form-group">
                            <textarea name="catatan_wali_kelas" class="form-control" rows="5">{{ $raport->catatan_wali_kelas }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body text-center">
                        <button type="submit" class="btn btn-success btn-lg btn-block">SIMPAN PERUBAHAN</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
