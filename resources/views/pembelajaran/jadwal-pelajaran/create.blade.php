@extends('layouts.app')

@section('title', 'Tambah Jadwal Pelajaran')

@section('content')

    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Tambah Jadwal Pelajaran</h4>
            <a href="{{ route('jadwal-pelajaran.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('jadwal-pelajaran.store') }}" method="POST" id="formJadwal">
                            @csrf

                            <div class="form-group">
                                <label>Pilih Kelas <span class="text-danger">*</span></label>
                                <select id="kelas_id" class="form-control @error('kelas_id') is-invalid @enderror"
                                    required>
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach ($kelas as $k)
                                        <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                    @endforeach
                                </select>
                                @error('kelas_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Mata Pelajaran & Guru <span class="text-danger">*</span></label>
                                <select name="mata_pelajaran_kelas_id" id="mata_pelajaran_kelas_id"
                                    class="form-control @error('mata_pelajaran_kelas_id') is-invalid @enderror" required
                                    disabled>
                                    <option value="">-- Pilih Kelas Terlebih Dahulu --</option>
                                </select>
                                @error('mata_pelajaran_kelas_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Pilih kelas terlebih dahulu</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Hari <span class="text-danger">*</span></label>
                                        <select name="hari" class="form-control @error('hari') is-invalid @enderror"
                                            required>
                                            <option value="">-- Pilih Hari --</option>
                                            @foreach ($hari as $h)
                                                <option value="{{ $h }}"
                                                    {{ old('hari') == $h ? 'selected' : '' }}>{{ $h }}</option>
                                            @endforeach
                                        </select>
                                        @error('hari')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Ruang Kelas</label>
                                        <input type="text" name="ruang"
                                            class="form-control @error('ruang') is-invalid @enderror"
                                            value="{{ old('ruang') }}" placeholder="Contoh: R.101">
                                        @error('ruang')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Jam Mulai <span class="text-danger">*</span></label>
                                        <input type="time" name="jam_mulai"
                                            class="form-control @error('jam_mulai') is-invalid @enderror"
                                            value="{{ old('jam_mulai') }}" required>
                                        @error('jam_mulai')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Jam Selesai <span class="text-danger">*</span></label>
                                        <input type="time" name="jam_selesai"
                                            class="form-control @error('jam_selesai') is-invalid @enderror"
                                            value="{{ old('jam_selesai') }}" required>
                                        @error('jam_selesai')
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
                                <a href="{{ route('jadwal-pelajaran.index') }}" class="btn btn-secondary">
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
                        <h5 class="mb-3 font-weight-bold card-title">Informasi</h5>
                        <ul class="pl-3 mb-0">
                            <li>Field dengan tanda <span class="text-danger">*</span> wajib diisi</li>
                            <li>Pilih kelas terlebih dahulu untuk memuat mata pelajaran</li>
                            <li>Sistem akan mengecek bentrok jadwal otomatis</li>
                            <li>Guru tidak bisa mengajar 2 kelas di waktu yang sama</li>
                            <li>Kelas tidak bisa ada 2 mata pelajaran di waktu yang sama</li>
                        </ul>
                    </div>
                </div>

                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h6><i class="fas fa-exclamation-triangle"></i> Perhatian!</h6>
                        <p class="mb-0 small">Pastikan waktu jam mulai dan jam selesai tidak bentrok dengan jadwal lain
                            untuk guru atau kelas yang sama.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Load mata pelajaran ketika kelas dipilih
            $('#kelas_id').change(function() {
                var kelasId = $(this).val();
                var mapelSelect = $('#mata_pelajaran_kelas_id');

                mapelSelect.html('<option value="">Loading...</option>');
                mapelSelect.prop('disabled', true);

                if (kelasId) {
                    $.ajax({
                        url: "{{ route('jadwal-pelajaran.get-mapel-by-kelas') }}",
                        type: 'GET',
                        data: {
                            kelas_id: kelasId
                        },
                        success: function(data) {
                            mapelSelect.html(
                                '<option value="">-- Pilih Mata Pelajaran & Guru --</option>'
                            );

                            if (data.length > 0) {
                                $.each(data, function(index, item) {
                                    mapelSelect.append(
                                        $('<option></option>')
                                        .val(item.id)
                                        .text(item.label)
                                    );
                                });
                                mapelSelect.prop('disabled', false);
                            } else {
                                mapelSelect.html(
                                    '<option value="">Tidak ada mata pelajaran untuk kelas ini</option>'
                                );
                            }
                        },
                        error: function() {
                            mapelSelect.html('<option value="">Error loading data</option>');
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Gagal memuat mata pelajaran',
                            });
                        }
                    });
                } else {
                    mapelSelect.html('<option value="">-- Pilih Kelas Terlebih Dahulu --</option>');
                    mapelSelect.prop('disabled', true);
                }
            });

            // Form validation
            $('#formJadwal').submit(function(e) {
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
