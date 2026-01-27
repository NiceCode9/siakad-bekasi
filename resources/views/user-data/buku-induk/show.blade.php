@extends('layouts.app')

@section('title', 'Detail Buku Induk')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h4 class="mb-0">Detail Buku Induk Siswa</h4>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('buku-induk.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Data Diri Singkat -->
        <div class="col-md-3">
            <div class="card shadow-sm mb-3">
                <div class="card-body text-center">
                    @if($siswa->foto)
                        <img src="{{ asset('storage/' . $siswa->foto) }}" class="img-fluid rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($siswa->nama_lengkap) }}&background=random" class="img-fluid rounded-circle mb-3" style="width: 120px;">
                    @endif
                    <h5>{{ $siswa->nama_lengkap }}</h5>
                    <p class="text-muted mb-1">{{ $siswa->nisn }} / {{ $siswa->nis }}</p>
                    <span class="badge badge-primary">{{ $siswa->kelasAktif->first()->nama ?? 'Belum ada kelas' }}</span>
                </div>
            </div>
            
            <div class="card shadow-sm">
                 <div class="card-header bg-white">
                    <h6 class="mb-0">Status</h6>
                </div>
                <div class="card-body">
                     <p class="mb-1"><strong>Status Siswa:</strong> <br> {{ ucfirst($siswa->status) }}</p>
                     <p class="mb-1"><strong>Data Induk:</strong> <br>
                        @if($siswa->bukuInduk)
                             <span class="text-success"><i class="fas fa-check-circle"></i> Lengkap</span>
                        @else
                             <span class="text-warning"><i class="fas fa-exclamation-circle"></i> Belum Lengkap</span>
                        @endif
                     </p>
                </div>
            </div>
        </div>

        <!-- Detail Buku Induk Tabs -->
        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-header bg-white p-0">
                    <ul class="nav nav-tabs nav-fill" id="bukuIndukTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab">
                                <i class="fas fa-user"></i> Data Pribadi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="academic-tab" data-toggle="tab" href="#academic" role="tab">
                                <i class="fas fa-book"></i> Catatan Akademik
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="history-tab" data-toggle="tab" href="#history" role="tab">
                                <i class="fas fa-history"></i> Riwayat Kelas
                            </a>
                        </li>
                         <li class="nav-item">
                            <a class="nav-link" id="grades-tab" data-toggle="tab" href="#grades" role="tab">
                                <i class="fas fa-chart-bar"></i> Riwayat Nilai
                            </a>
                        </li>
                         <li class="nav-item">
                            <a class="nav-link" id="mutation-tab" data-toggle="tab" href="#mutation" role="tab">
                                <i class="fas fa-exchange-alt"></i> Mutasi
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="bukuIndukTabContent">
                        
                        <!-- Tab Data Pribadi -->
                        <div class="tab-pane fade show active" id="profile" role="tabpanel">
                            <h6 class="border-bottom pb-2">Identitas Siswa</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-4">Nama Lengkap</dt>
                                        <dd class="col-sm-8">{{ $siswa->nama_lengkap }}</dd>

                                        <dt class="col-sm-4">NIS / NISN</dt>
                                        <dd class="col-sm-8">{{ $siswa->nis }} / {{ $siswa->nisn }}</dd>

                                        <dt class="col-sm-4">Tempat, Tgl Lahir</dt>
                                        <dd class="col-sm-8">{{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('d F Y') : '-' }}</dd>

                                        <dt class="col-sm-4">Jenis Kelamin</dt>
                                        <dd class="col-sm-8">{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</dd>
                                        
                                        <dt class="col-sm-4">Agama</dt>
                                        <dd class="col-sm-8">{{ $siswa->agama ?? '-' }}</dd>
                                    </dl>
                                </div>
                                <div class="col-md-6">
                                     <dl class="row">
                                        <dt class="col-sm-4">Telepon</dt>
                                        <dd class="col-sm-8">{{ $siswa->telepon ?? '-' }}</dd>

                                        <dt class="col-sm-4">Email</dt>
                                        <dd class="col-sm-8">{{ $siswa->email ?? '-' }}</dd>

                                        <dt class="col-sm-4">Alamat</dt>
                                        <dd class="col-sm-8">{{ $siswa->alamat ?? '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Nama Ayah</dt>
                                        <dd class="col-sm-8">{{ $siswa->orangTua->nama_ayah ?? '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Nama Ibu</dt>
                                        <dd class="col-sm-8">{{ $siswa->orangTua->nama_ibu ?? '-' }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Catatan Akademik (Buku Induk Data) -->
                        <div class="tab-pane fade" id="academic" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 border-bottom pb-2">Detail Data Induk</h6>
                                <button class="btn btn-warning btn-sm btn-edit-induk" 
                                        onclick="location.href='{{ route('buku-induk.edit', $siswa->id) }}'">
                                    <i class="fas fa-pencil-alt"></i> Edit Data
                                </button>
                            </div>
                            
                             @if($siswa->bukuInduk)
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <th width="30%">Nomor Induk</th>
                                        <td>{{ $siswa->bukuInduk->nomor_induk ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nomor Peserta Ujian</th>
                                        <td>{{ $siswa->bukuInduk->nomor_peserta_ujian ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nomor Seri Ijazah</th>
                                        <td>{{ $siswa->bukuInduk->nomor_seri_ijazah ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nomor Seri SKHUN</th>
                                        <td>{{ $siswa->bukuInduk->nomor_seri_skhun ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Lulus</th>
                                        <td>{{ $siswa->bukuInduk->tanggal_lulus ? $siswa->bukuInduk->tanggal_lulus->format('d F Y') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Riwayat Pendidikan</th>
                                        <td>{!! nl2br(e($siswa->bukuInduk->riwayat_pendidikan ?? '-')) !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Riwayat Kesehatan</th>
                                        <td>{!! nl2br(e($siswa->bukuInduk->riwayat_kesehatan ?? '-')) !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Catatan Khusus</th>
                                        <td>{!! nl2br(e($siswa->bukuInduk->catatan_khusus ?? '-')) !!}</td>
                                    </tr>
                                </table>
                            @else
                                <div class="alert alert-warning">
                                    Data Buku Induk belum dilengkapi. Silakan klik tombol Edit untuk melengkapi.
                                </div>
                            @endif
                        </div>

                        <!-- Tab Riwayat Kelas -->
                        <div class="tab-pane fade" id="history" role="tabpanel">
                            <h6 class="border-bottom pb-2">Riwayat Kelas</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tahun Ajar</th>
                                            <th>Semester</th>
                                            <th>Kelas</th>
                                            <th>Tgl Masuk</th>
                                            <th>Tgl Keluar</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($riwayatKelas as $rk)
                                            <tr>
                                                <td>{{ $rk->kelas->tahunAkademik->nama ?? '-' }}</td>
                                                <td>{{ $rk->kelas->semester->nama ?? '-' }}</td>
                                                <td>{{ $rk->kelas->nama }}</td>
                                                <td>{{ $rk->tanggal_masuk ? $rk->tanggal_masuk->format('d/m/Y') : '-' }}</td>
                                                <td>{{ $rk->tanggal_keluar ? $rk->tanggal_keluar->format('d/m/Y') : '-' }}</td>
                                                <td>
                                                    @if($rk->status == 'aktif')
                                                        <span class="badge badge-success">Aktif</span>
                                                    @else
                                                        <span class="badge badge-secondary">{{ ucfirst($rk->status) }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">Tidak ada data riwayat kelas.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                         <!-- Tab Riwayat Nilai -->
                        <div class="tab-pane fade" id="grades" role="tabpanel">
                             <h6 class="border-bottom pb-2">Riwayat Nilai</h6>
                             @forelse($riwayatNilai as $semester => $nilaiList)
                                <div class="card mb-3 border">
                                    <div class="card-header bg-light py-2">
                                        <strong>Semester: {{ $semester }}</strong>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-sm table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Mata Pelajaran</th>
                                                    <th>Jenis Nilai</th>
                                                    <th>Nilai</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($nilaiList as $nilai)
                                                    <tr>
                                                        <td>{{ $nilai->mataPelajaranKelas->mataPelajaran->nama ?? '-' }}</td>
                                                        <td>{{ ucfirst(str_replace('_', ' ', $nilai->jenis_nilai)) }}</td>
                                                        <td class="font-weight-bold">{{ $nilai->nilai }}</td>
                                                        <td>{{ $nilai->keterangan }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                             @empty
                                <div class="alert alert-info">Belum ada data nilai.</div>
                             @endforelse
                        </div>

                        <!-- Tab Mutasi -->
                        <div class="tab-pane fade" id="mutation" role="tabpanel">
                            <h6 class="border-bottom pb-2">Riwayat Mutasi</h6>
                             <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Jenis Mutasi</th>
                                            <th>Sekolah Asal/Tujuan</th>
                                            <th>Alasan</th>
                                            <th>No. Surat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($riwayatMutasi as $rm)
                                            <tr>
                                                <td>{{ $rm->tanggal ? $rm->tanggal->format('d/m/Y') : '-' }}</td>
                                                <td>{{ ucfirst($rm->jenis_mutasi) }}</td>
                                                <td>
                                                    @if($rm->jenis_mutasi == 'masuk')
                                                        Dari: {{ $rm->dari_sekolah }}
                                                    @else
                                                        Ke: {{ $rm->ke_sekolah }}
                                                    @endif
                                                </td>
                                                <td>{{ $rm->alasan }}</td>
                                                <td>{{ $rm->nomor_surat }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">Tidak ada data mutasi.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
