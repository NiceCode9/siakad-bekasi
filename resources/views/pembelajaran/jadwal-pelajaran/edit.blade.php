@extends('layouts.app')

@section('title', 'Edit Jadwal Pelajaran')

@section('content')

    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Edit Jadwal Pelajaran</h4>
            <a href="{{ route('jadwal-pelajaran.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('jadwal-pelajaran.update', $jadwalPelajaran->id) }}" method="POST"
                            id="formJadwal">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label>Kelas</label>
                                <input type="text" class="form-control"
                                    value="{{ $jadwalPelajaran->mataPelajaranGuru->mataPelajaranKelas->kelas->nama ?? '-' }}"
                                    readonly>
                                <small class="form-text text-muted">Kelas tidak dapat diubah</small>
                            </div>

                            <div class="form-group">
                                <label>Mata Pelajaran & Guru</label>
                                <input type="text" class="form-control"
                                    value="{{ ($jadwalPelajaran->mataPelajaranGuru->mataPelajaranKelas->mataPelajaran->nama ?? '-') . ' - ' . ($jadwalPelajaran->mataPelajaranGuru->guru->nama_lengkap ?? '-') }}"
                                    readonly>
                                <small class="form-text text-muted">Mata pelajaran dan guru tidak dapat diubah</small>
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
                                                    {{ old('hari', $jadwalPelajaran->hari) == $h ? 'selected' : '' }}>
                                                    {{ $h }}</option>
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
                                            value="{{ old('ruang', $jadwalPelajaran->ruang) }}" placeholder="Contoh: R.101">
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
                                            value="{{ old('jam_mulai', $jadwalPelajaran->jam_mulai->format('H:i')) }}"
                                            required>
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
                                            value="{{ old('jam_selesai', $jadwalPelajaran->jam_selesai->format('H:i')) }}"
                                            required>
                                        @error('jam_selesai')
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
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%">Dibuat</td>
                                <td>: {{ $jadwalPelajaran->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td>Diupdate</td>
                                <td>: {{ $jadwalPelajaran->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3 font-weight-bold card-title">Catatan</h5>
                        <ul class="pl-3 mb-0">
                            <li>Kelas dan mata pelajaran tidak dapat diubah</li>
                            <li>Sistem akan mengecek bentrok jadwal otomatis</li>
                            <li>Pastikan tidak ada bentrok dengan jadwal lain</li>
                        </ul>
                    </div>
                </div>

                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h6><i class="fas fa-exclamation-triangle"></i> Perhatian!</h6>
                        <p class="mb-0 small">Perubahan jadwal dapat mempengaruhi kegiatan belajar mengajar. Pastikan semua
                            pihak terkait sudah diinformasikan.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
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
