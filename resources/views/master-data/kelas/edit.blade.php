@extends('layouts.app')

@section('title', 'Edit Kelas')

@section('content')

    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Edit Kelas</h4>
            <a href="{{ route('kelas.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('kelas.update', $kelas->id) }}" method="POST" id="formKelas">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Semester <span class="text-danger">*</span></label>
                                        <select name="semester_id"
                                            class="form-control @error('semester_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Semester --</option>
                                            @foreach ($semester as $s)
                                                <option value="{{ $s->id }}"
                                                    {{ old('semester_id', $kelas->semester_id) == $s->id ? 'selected' : '' }}>
                                                    {{ $s->nama }} - {{ $s->tahunAkademik->nama ?? '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('semester_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tingkat <span class="text-danger">*</span></label>
                                        <select name="tingkat" class="form-control @error('tingkat') is-invalid @enderror"
                                            required>
                                            <option value="">-- Pilih Tingkat --</option>
                                            <option value="X"
                                                {{ old('tingkat', $kelas->tingkat) == 'X' ? 'selected' : '' }}>X</option>
                                            <option value="XI"
                                                {{ old('tingkat', $kelas->tingkat) == 'XI' ? 'selected' : '' }}>XI</option>
                                            <option value="XII"
                                                {{ old('tingkat', $kelas->tingkat) == 'XII' ? 'selected' : '' }}>XII
                                            </option>
                                        </select>
                                        @error('tingkat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Jurusan <span class="text-danger">*</span></label>
                                        <select name="jurusan_id"
                                            class="form-control @error('jurusan_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Jurusan --</option>
                                            @foreach ($jurusan as $j)
                                                <option value="{{ $j->id }}"
                                                    {{ old('jurusan_id', $kelas->jurusan_id) == $j->id ? 'selected' : '' }}>
                                                    {{ $j->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('jurusan_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nama Kelas <span class="text-danger">*</span></label>
                                        <input type="text" name="nama"
                                            class="form-control @error('nama') is-invalid @enderror"
                                            value="{{ old('nama', $kelas->nama) }}" placeholder="Contoh: X RPL 1" required>
                                        @error('nama')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Kode Kelas <span class="text-danger">*</span></label>
                                        <input type="text" name="kode"
                                            class="form-control @error('kode') is-invalid @enderror"
                                            value="{{ old('kode', $kelas->kode) }}" placeholder="Contoh: X-RPL-1-2024/2025"
                                            required>
                                        @error('kode')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Kode unik untuk kelas</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Kuota Siswa <span class="text-danger">*</span></label>
                                        <input type="number" name="kuota"
                                            class="form-control @error('kuota') is-invalid @enderror"
                                            value="{{ old('kuota', $kelas->kuota) }}" min="1" max="50"
                                            required>
                                        @error('kuota')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Maksimal 50 siswa per kelas</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Wali Kelas</label>
                                        <select name="wali_kelas_id"
                                            class="form-control @error('wali_kelas_id') is-invalid @enderror">
                                            <option value="">-- Pilih Wali Kelas --</option>
                                            @foreach ($guru as $g)
                                                <option value="{{ $g->id }}"
                                                    {{ old('wali_kelas_id', $kelas->wali_kelas_id) == $g->id ? 'selected' : '' }}>
                                                    {{ $g->nama_lengkap }} ({{ $g->nip }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('wali_kelas_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Ruang Kelas</label>
                                        <input type="text" name="ruang_kelas"
                                            class="form-control @error('ruang_kelas') is-invalid @enderror"
                                            value="{{ old('ruang_kelas', $kelas->ruang_kelas) }}"
                                            placeholder="Contoh: R.101">
                                        @error('ruang_kelas')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update
                                </button>
                                <a href="{{ route('kelas.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Informasi Kelas</h5>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%">Dibuat</td>
                                <td>: {{ $kelas->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td>Diupdate</td>
                                <td>: {{ $kelas->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td>Jumlah Siswa</td>
                                <td>: {{ $kelas->siswaKelas()->where('status', 'aktif')->count() }} siswa</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Catatan</h5>
                        <ul class="pl-3 mb-0">
                            <li>Field dengan tanda <span class="text-danger">*</span> wajib diisi</li>
                            <li>Kode kelas harus unik</li>
                            <li>Hati-hati mengubah semester dan jurusan jika sudah ada siswa</li>
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
            $('#formKelas').submit(function(e) {
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
