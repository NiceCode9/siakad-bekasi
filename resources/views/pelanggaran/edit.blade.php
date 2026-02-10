@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>Proses Pelanggaran Siswa</h1>
            <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                <ol class="breadcrumb pt-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pelanggaran-siswa.index') }}">Pelanggaran Siswa</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Proses</li>
                </ol>
            </nav>
            <div class="separator mb-5"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-8 offset-md-2">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-4">Detail Pelanggaran</h5>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label font-weight-bold">Tanggal</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext">{{ date('d F Y', strtotime($pelanggaranSiswa->tanggal)) }}</p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label font-weight-bold">Nama Siswa</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext">{{ $pelanggaranSiswa->siswa->nama_lengkap }} ({{ $pelanggaranSiswa->siswa->nis }})</p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label font-weight-bold">Jenis Pelanggaran</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext">{{ $pelanggaranSiswa->jenis_pelanggaran }}</p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label font-weight-bold">Kronologi</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext">{{ $pelanggaranSiswa->kronologi ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label font-weight-bold">Pelapor</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext">{{ $pelanggaranSiswa->pelapor->nama_lengkap ?? 'System' }}</p>
                        </div>
                    </div>

                    <hr>

                    <h5 class="mb-4">Tindak Lanjut (Kesiswaan / BK)</h5>

                    <form action="{{ route('pelanggaran-siswa.update', $pelanggaranSiswa->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="kategori">Kategori Pelanggaran</label>
                            <select class="form-control" id="kategori" name="kategori">
                                <option value="ringan" {{ $pelanggaranSiswa->kategori == 'ringan' ? 'selected' : '' }}>Ringan</option>
                                <option value="sedang" {{ $pelanggaranSiswa->kategori == 'sedang' ? 'selected' : '' }}>Sedang</option>
                                <option value="berat" {{ $pelanggaranSiswa->kategori == 'berat' ? 'selected' : '' }}>Berat</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="poin">Poin Pelanggaran</label>
                            <input type="number" class="form-control" id="poin" name="poin" value="{{ $pelanggaranSiswa->poin }}" min="0" required>
                            <small class="form-text text-muted">Masukkan poin sesuai bobot pelanggaran.</small>
                        </div>

                        <div class="form-group">
                            <label for="sanksi">Sanksi / Tindakan</label>
                            <textarea class="form-control" id="sanksi" name="sanksi" rows="3" required>{{ $pelanggaranSiswa->sanksi }}</textarea>
                            <small class="form-text text-muted">Jelaskan sanksi atau tindakan pembinaan yang diberikan.</small>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="proses" {{ $pelanggaranSiswa->status == 'proses' ? 'selected' : '' }}>Proses (Belum Selesai)</option>
                                <option value="selesai" {{ $pelanggaranSiswa->status == 'selesai' ? 'selected' : '' }}>Selesai (Sudah Ditindaklanjuti)</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg btn-block">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
