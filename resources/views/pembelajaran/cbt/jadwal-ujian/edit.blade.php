@extends('layouts.app')

@section('title', 'Edit Jadwal Ujian')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Edit Jadwal Ujian</h4>
        <a href="{{ route('jadwal-ujian.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('jadwal-ujian.update', $jadwalUjian->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Ujian <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_ujian" class="form-control" value="{{ $jadwalUjian->nama_ujian }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jenis Ujian <span class="text-danger">*</span></label>
                                    <select name="jenis_ujian" class="form-control" required>
                                        <option value="ulangan_harian" {{ $jadwalUjian->jenis_ujian == 'ulangan_harian' ? 'selected' : '' }}>Ulangan Harian</option>
                                        <option value="uts" {{ $jadwalUjian->jenis_ujian == 'uts' ? 'selected' : '' }}>UTS</option>
                                        <option value="uas" {{ $jadwalUjian->jenis_ujian == 'uas' ? 'selected' : '' }}>UAS</option>
                                        <option value="ujian_praktik" {{ $jadwalUjian->jenis_ujian == 'ujian_praktik' ? 'selected' : '' }}>Ujian Praktik</option>
                                        <option value="ujian_sekolah" {{ $jadwalUjian->jenis_ujian == 'ujian_sekolah' ? 'selected' : '' }}>Ujian Sekolah</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kelas & Mapel <span class="text-danger">*</span></label>
                                    <select name="mata_pelajaran_kelas_id" class="form-control select2" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach($kelas as $k)
                                            <optgroup label="Kelas {{ $k->nama }}">
                                                @foreach($k->mataPelajaranKelas as $mpk)
                                                    <option value="{{ $mpk->id }}" {{ $jadwalUjian->mata_pelajaran_kelas_id == $mpk->id ? 'selected' : '' }}>{{ $mpk->mataPelajaran->nama }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Bank Soal <span class="text-danger">*</span></label>
                                    <select name="bank_soal_id" class="form-control select2" required>
                                        <option value="">-- Pilih Bank Soal --</option>
                                        @foreach($bankSoal as $b)
                                            <option value="{{ $b->id }}" {{ $jadwalUjian->bank_soal_id == $b->id ? 'selected' : '' }}>{{ $b->kode }} - {{ $b->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Waktu Mulai <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="tanggal_mulai" class="form-control" value="{{ $jadwalUjian->tanggal_mulai->format('Y-m-d\TH:i') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Waktu Selesai <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="tanggal_selesai" class="form-control" value="{{ $jadwalUjian->tanggal_selesai->format('Y-m-d\TH:i') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                             <div class="col-md-6">
                                <div class="form-group">
                                    <label>Durasi (Menit) <span class="text-danger">*</span></label>
                                    <input type="number" name="durasi" class="form-control" value="{{ $jadwalUjian->durasi }}" min="1" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jumlah Soal <span class="text-danger">*</span></label>
                                    <input type="number" name="jumlah_soal" class="form-control" value="{{ $jadwalUjian->jumlah_soal }}" min="1" required>
                                    <div class="custom-control custom-checkbox mt-1">
                                        <input type="checkbox" class="custom-control-input" id="regenerate_soal" name="regenerate_soal">
                                        <label class="custom-control-label" for="regenerate_soal">Regenerate Soal Acak (Reset)</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2">{{ $jadwalUjian->keterangan }}</textarea>
                        </div>

                        <div class="form-group">
                            <label class="d-block">Pengaturan Tambahan</label>
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" class="custom-control-input" id="acak_soal" name="acak_soal" {{ $jadwalUjian->acak_soal ? 'checked' : '' }}>
                                <label class="custom-control-label" for="acak_soal">Acak Soal</label>
                            </div>
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" class="custom-control-input" id="acak_opsi" name="acak_opsi" {{ $jadwalUjian->acak_opsi ? 'checked' : '' }}>
                                <label class="custom-control-label" for="acak_opsi">Acak Opsi Jawaban</label>
                            </div>
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" class="custom-control-input" id="tampilkan_nilai" name="tampilkan_nilai" {{ $jadwalUjian->tampilkan_nilai ? 'checked' : '' }}>
                                <label class="custom-control-label" for="tampilkan_nilai">Tampilkan Nilai di Akhir</label>
                            </div>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="alert alert-warning">
                <h5><i class="fas fa-exclamation-triangle"></i> Peringatan Edit</h5>
                <ul class="pl-3 mb-0">
                    <li>Edit jadwal hanya mengubah <strong>satu</strong> jadwal ujian ini saja.</li>
                    <li>Jadwal untuk kelas lain yang mungkin dibuat bersamaan <strong>tidak ikut berubah</strong>.</li>
                    <li>Jika mencentang <strong>Regenerate Soal</strong>, soal yang sudah di-generate akan dihapus dan diganti acak baru.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({ width: '100%' });
    });
</script>
@endpush
