@extends('layouts.app')

@section('title', 'Tambah Tempat PKL')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Tambah Data Industri / Tempat PKL</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('perusahaan-pkl.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label>Nama Perusahaan / Industri <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                            @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label>Bidang Usaha</label>
                            <input type="text" name="bidang_usaha" class="form-control" value="{{ old('bidang_usaha') }}" placeholder="Contoh: IT Consultant, Bengkel, Pertanian">
                        </div>

                        <div class="form-group">
                            <label>Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control" rows="3">{{ old('alamat') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Telepon</label>
                                    <input type="text" name="telepon" class="form-control" value="{{ old('telepon') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kontak Person (Nama)</label>
                                    <input type="text" name="nama_kontak" class="form-control" value="{{ old('nama_kontak') }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                             <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jabatan Kontak</label>
                                    <input type="text" name="jabatan_kontak" class="form-control" value="{{ old('jabatan_kontak') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Kuota Siswa</label>
                            <input type="number" name="kuota" class="form-control" value="0" min="0">
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                                <label class="custom-control-label" for="is_active">Status Aktif</label>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('perusahaan-pkl.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
