@extends('layouts.app')

@section('title', 'Edit Jurnal PKL')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Edit Jurnal Harian PKL</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('jurnal-pkl.update', $jurnalPkl->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label>Tanggal Kegiatan <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_monitoring" class="form-control" value="{{ $jurnalPkl->tanggal_monitoring->format('Y-m-d') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Kegiatan / Pekerjaan yang Dilakukan <span class="text-danger">*</span></label>
                            <textarea name="kegiatan" class="form-control" rows="5" required>{{ $jurnalPkl->kegiatan }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Foto Dokumentasi</label>
                            @if($jurnalPkl->foto)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/'.$jurnalPkl->foto) }}" class="img-thumbnail" width="150">
                                </div>
                            @endif
                            <input type="file" name="foto" class="form-control-file" accept="image/*">
                            <small class="text-muted">Biarkan kosong jika tidak ingin mengubah foto.</small>
                        </div>

                        @if($jurnalPkl->catatan_pembimbing)
                        <div class="alert alert-info">
                            <strong>Catatan Pembimbing:</strong><br>
                            {{ $jurnalPkl->catatan_pembimbing }}
                        </div>
                        @endif

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary px-4">Update Jurnal</button>
                            <a href="{{ route('jurnal-pkl.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
