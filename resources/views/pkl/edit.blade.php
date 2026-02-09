@extends('layouts.app')

@section('title', 'Edit Penempatan PKL')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3 font-weight-bold">Edit Data Penempatan PKL</h5>
                        <form action="{{ route('pkl.update', $pkl->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label>Siswa</label>
                                <input type="text" class="form-control"
                                    value="{{ $pkl->siswa->nama_lengkap }} ({{ $pkl->siswa->nis }})" disabled>
                                <small class="text-muted">Siswa tidak dapat diubah setelah penempatan.</small>
                            </div>

                            <div class="form-group">
                                <label>Tempat PKL <span class="text-danger">*</span></label>
                                <select name="perusahaan_pkl_id" class="form-control select2" required>
                                    <option value="">-- Pilih Industri --</option>
                                    @foreach ($perusahaanPkl as $tm)
                                        <option value="{{ $tm->id }}"
                                            {{ $pkl->perusahaan_pkl_id == $tm->id ? 'selected' : '' }}>
                                            {{ $tm->nama }} ({{ $tm->bidang_usaha }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Pembimbing Sekolah (Guru)</label>
                                        <select name="pembimbing_sekolah_id" class="form-control select2">
                                            <option value="">-- Pilih Guru --</option>
                                            @foreach ($gurus as $guru)
                                                <option value="{{ $guru->id }}"
                                                    {{ $pkl->pembimbing_sekolah_id == $guru->id ? 'selected' : '' }}>
                                                    {{ $guru->nama_lengkap }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Pembimbing Lapangan (Industri)</label>
                                        <input type="text" name="pembimbing_industri" class="form-control"
                                            value="{{ old('pembimbing_industri', $pkl->pembimbing_industri) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tanggal Mulai</label>
                                        <input type="date" name="tanggal_mulai" class="form-control"
                                            value="{{ old('tanggal_mulai', $pkl->tanggal_mulai->format('Y-m-d')) }}"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tanggal Selesai</label>
                                        <input type="date" name="tanggal_selesai" class="form-control"
                                            value="{{ old('tanggal_selesai', $pkl->tanggal_selesai->format('Y-m-d')) }}"
                                            required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="pengajuan" {{ $pkl->status == 'pengajuan' ? 'selected' : '' }}>
                                        Pengajuan</option>
                                    <option value="disetujui" {{ $pkl->status == 'disetujui' ? 'selected' : '' }}>
                                        Disetujui</option>
                                    <option value="aktif" {{ $pkl->status == 'aktif' ? 'selected' : '' }}>Aktif
                                    </option>
                                    <option value="selesai" {{ $pkl->status == 'selesai' ? 'selected' : '' }}>Selesai
                                    </option>
                                    <option value="batal" {{ $pkl->status == 'batal' ? 'selected' : '' }}>Batal
                                    </option>
                                </select>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                <a href="{{ route('pkl.index') }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: 'Pilih...',
                allowClear: true
            });
        });
    </script>
@endpush
