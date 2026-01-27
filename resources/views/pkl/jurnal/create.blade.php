@extends('layouts.app')

@section('title', 'Tambah Jurnal PKL')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Tambah Jurnal Harian PKL</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('jurnal-pkl.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="pkl_id" value="{{ $pkl->id }}">
                        
                        <div class="form-group">
                            <label>Tanggal Kegiatan <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_monitoring" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Kegiatan / Pekerjaan yang Dilakukan <span class="text-danger">*</span></label>
                            <textarea name="kegiatan" class="form-control" rows="5" placeholder="Jelaskan secara detail apa yang Anda kerjakan hari ini..." required></textarea>
                        </div>

                        <div class="form-group">
                            <label>Foto Dokumentasi (Opsional)</label>
                            <input type="file" name="foto" class="form-control-file" accept="image/*">
                            <small class="text-muted">Format: JPG, PNG, Max 2MB.</small>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary px-4">Simpan Jurnal</button>
                            <a href="{{ route('jurnal-pkl.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
