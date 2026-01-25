@extends('layouts.app')

@section('title', 'Edit Mata Pelajaran')

@section('content')

    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Edit Mata Pelajaran</h4>
            <a href="{{ route('mata-pelajaran.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="row">
            <div class="col-md-8 mb-3">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('mata-pelajaran.update', $mataPelajaran->id) }}" method="POST"
                            id="formMataPelajaran">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Kurikulum <span class="text-danger">*</span></label>
                                        <select name="kurikulum_id"
                                            class="form-control @error('kurikulum_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Kurikulum --</option>
                                            @foreach ($kurikulum as $k)
                                                <option value="{{ $k->id }}"
                                                    {{ old('kurikulum_id', $mataPelajaran->kurikulum_id) == $k->id ? 'selected' : '' }}>
                                                    {{ $k->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('kurikulum_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Kelompok Mata Pelajaran</label>
                                        <select name="kelompok_mapel_id"
                                            class="form-control @error('kelompok_mapel_id') is-invalid @enderror">
                                            <option value="">-- Pilih Kelompok --</option>
                                            @foreach ($kelompokMapel as $km)
                                                <option value="{{ $km->id }}"
                                                    {{ old('kelompok_mapel_id', $mataPelajaran->kelompok_mapel_id) == $km->id ? 'selected' : '' }}>
                                                    {{ $km->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('kelompok_mapel_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Opsional</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Kode Mata Pelajaran <span class="text-danger">*</span></label>
                                        <input type="text" name="kode"
                                            class="form-control @error('kode') is-invalid @enderror"
                                            value="{{ old('kode', $mataPelajaran->kode) }}" placeholder="Contoh: MTK"
                                            required>
                                        @error('kode')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Kode unik maksimal 20 karakter</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nama Mata Pelajaran <span class="text-danger">*</span></label>
                                        <input type="text" name="nama"
                                            class="form-control @error('nama') is-invalid @enderror"
                                            value="{{ old('nama', $mataPelajaran->nama) }}"
                                            placeholder="Contoh: Matematika" required>
                                        @error('nama')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Jenis <span class="text-danger">*</span></label>
                                        <select name="jenis" class="form-control @error('jenis') is-invalid @enderror"
                                            required>
                                            <option value="">-- Pilih Jenis --</option>
                                            <option value="umum"
                                                {{ old('jenis', $mataPelajaran->jenis) == 'umum' ? 'selected' : '' }}>Umum
                                            </option>
                                            <option value="kejuruan"
                                                {{ old('jenis', $mataPelajaran->jenis) == 'kejuruan' ? 'selected' : '' }}>
                                                Kejuruan</option>
                                            <option value="muatan_lokal"
                                                {{ old('jenis', $mataPelajaran->jenis) == 'muatan_lokal' ? 'selected' : '' }}>
                                                Muatan Lokal</option>
                                        </select>
                                        @error('jenis')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Kategori <span class="text-danger">*</span></label>
                                        <select name="kategori" class="form-control @error('kategori') is-invalid @enderror"
                                            required>
                                            <option value="">-- Pilih Kategori --</option>
                                            <option value="wajib"
                                                {{ old('kategori', $mataPelajaran->kategori) == 'wajib' ? 'selected' : '' }}>
                                                Wajib</option>
                                            <option value="peminatan"
                                                {{ old('kategori', $mataPelajaran->kategori) == 'peminatan' ? 'selected' : '' }}>
                                                Peminatan</option>
                                            <option value="lintas_minat"
                                                {{ old('kategori', $mataPelajaran->kategori) == 'lintas_minat' ? 'selected' : '' }}>
                                                Lintas Minat</option>
                                        </select>
                                        @error('kategori')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>KKM (Kriteria Ketuntasan Minimal) <span class="text-danger">*</span></label>
                                        <input type="number" name="kkm"
                                            class="form-control @error('kkm') is-invalid @enderror"
                                            value="{{ old('kkm', $mataPelajaran->kkm) }}" min="0" max="100"
                                            step="0.01" required>
                                        @error('kkm')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Nilai 0-100</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row mb-1">
                                        <label class="col-12 col-form-label">Status Aktif</label>
                                        <div class="col-12">
                                            <div class="custom-switch custom-switch-small custom-switch-secondary mb-2">
                                                <input class="custom-switch-input" id="is_active" name="is_active"
                                                    value="1" type="checkbox"
                                                    {{ old('is_active', $mataPelajaran->is_active) ? 'checked' : '' }}>
                                                <label class="custom-switch-btn" for="is_active"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update
                                </button>
                                <a href="{{ route('mata-pelajaran.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-4 card-title font-weight-bold">Informasi Mata Pelajaran</h5>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%">Dibuat</td>
                                <td>: {{ $mataPelajaran->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td>Diupdate</td>
                                <td>: {{ $mataPelajaran->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td>Digunakan di</td>
                                <td>: {{ $mataPelajaran->mataPelajaranKelas()->count() }} kelas</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-4 card-title font-weight-bold">Catatan</h5>
                        <ul class="pl-3 mb-0">
                            <li>Field dengan tanda <span class="text-danger">*</span> wajib diisi</li>
                            <li>Kode mata pelajaran harus unik</li>
                            <li>Hati-hati mengubah jenis dan kategori jika sudah digunakan di kelas</li>
                            <li>Perubahan KKM akan mempengaruhi perhitungan nilai</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Form validation with SweetAlert
            $('#formMataPelajaran').submit(function(e) {
                if (!this.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();

                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Mohon lengkapi semua field yang wajib diisi!',
                    });
                }
                $(this).addClass('was-validated');
            });
        });
    </script>
@endpush
