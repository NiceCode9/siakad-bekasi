@extends('layouts.app')

@section('title', 'Tambah Kelas')

@section('content')

    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Tambah Kelas</h4>
            <a href="{{ route('kelas.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('kelas.store') }}" method="POST" id="formKelas">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Semester <span class="text-danger">*</span></label>
                                        <select name="semester_id"
                                            class="form-control @error('semester_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Semester --</option>
                                            @foreach ($semester as $s)
                                                <option value="{{ $s->id }}"
                                                    {{ old('semester_id') == $s->id ? 'selected' : '' }}>
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
                                        <select name="tingkat" id="tingkat"
                                            class="form-control @error('tingkat') is-invalid @enderror" required>
                                            <option value="">-- Pilih Tingkat --</option>
                                            <option value="X" {{ old('tingkat') == 'X' ? 'selected' : '' }}>X</option>
                                            <option value="XI" {{ old('tingkat') == 'XI' ? 'selected' : '' }}>XI
                                            </option>
                                            <option value="XII" {{ old('tingkat') == 'XII' ? 'selected' : '' }}>XII
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
                                        <select name="jurusan_id" id="jurusan_id"
                                            class="form-control @error('jurusan_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Jurusan --</option>
                                            @foreach ($jurusan as $j)
                                                <option value="{{ $j->id }}" data-kode="{{ $j->kode }}"
                                                    data-singkatan="{{ $j->singkatan }}"
                                                    {{ old('jurusan_id') == $j->id ? 'selected' : '' }}>
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
                                        <input type="text" name="nama" id="nama"
                                            class="form-control @error('nama') is-invalid @enderror"
                                            value="{{ old('nama') }}" placeholder="Contoh: X RPL 1" required>
                                        @error('nama')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Akan otomatis terisi saat memilih tingkat dan
                                            jurusan</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Kode Kelas <span class="text-danger">*</span></label>
                                        <input type="text" name="kode" id="kode"
                                            class="form-control @error('kode') is-invalid @enderror"
                                            value="{{ old('kode') }}" placeholder="Contoh: X-RPL-1-2024/2025" required>
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
                                            value="{{ old('kuota', 36) }}" min="1" max="50" required>
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
                                                    {{ old('wali_kelas_id') == $g->id ? 'selected' : '' }}>
                                                    {{ $g->nama_lengkap }} ({{ $g->nip }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('wali_kelas_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Opsional, bisa diisi nanti</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Ruang Kelas</label>
                                        <input type="text" name="ruang_kelas"
                                            class="form-control @error('ruang_kelas') is-invalid @enderror"
                                            value="{{ old('ruang_kelas') }}" placeholder="Contoh: R.101">
                                        @error('ruang_kelas')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan
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
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Informasi</h6>
                    </div>
                    <div class="card-body">
                        <ul class="pl-3">
                            <li>Field dengan tanda <span class="text-danger">*</span> wajib diisi</li>
                            <li>Nama kelas akan otomatis terisi saat memilih tingkat dan jurusan</li>
                            <li>Kode kelas harus unik dan tidak boleh sama</li>
                            <li>Kuota maksimal 50 siswa per kelas</li>
                            <li>Wali kelas bisa diisi sekarang atau nanti</li>
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
            // Auto generate nama dan kode kelas
            function generateNamaKode() {
                var tingkat = $('#tingkat').val();
                var jurusan = $('#jurusan_id').find(':selected');
                var singkatan = jurusan.data('singkatan');
                var kode_jurusan = jurusan.data('kode');

                if (tingkat && singkatan) {
                    // Generate nama (contoh: X RPL 1)
                    var nama = tingkat + ' ' + singkatan + ' 1';
                    $('#nama').val(nama);

                    // Generate kode (contoh: X-RPL-1-2024/2025)
                    if (kode_jurusan) {
                        var kode = tingkat + '-' + kode_jurusan + '-1';
                        $('#kode').val(kode);
                    }
                }
            }

            $('#tingkat, #jurusan_id').change(function() {
                generateNamaKode();
            });

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
