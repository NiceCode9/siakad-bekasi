@extends('layouts.app')

@section('title', 'Buat Jadwal Ujian')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Jadwalkan Ujian Baru</h4>
        <a href="{{ route('jadwal-ujian.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('jadwal-ujian.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Ujian <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_ujian" class="form-control" placeholder="Contoh: UH 1 Biologi" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jenis Ujian <span class="text-danger">*</span></label>
                                    <select name="jenis_ujian" class="form-control" required>
                                        <option value="ulangan_harian">Ulangan Harian</option>
                                        <option value="uts">UTS</option>
                                        <option value="uas">UAS</option>
                                        <option value="ujian_praktik">Ujian Praktik</option>
                                        <option value="ujian_sekolah">Ujian Sekolah</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Pilih Kelas & Mapel (Bisa Banyak) <span class="text-danger">*</span></label>
                                    <select name="mata_pelajaran_kelas_id[]" class="form-control select2" multiple="multiple" required data-placeholder="Pilih Kelas & Mapel">
                                        @foreach($kelas as $k)
                                            <optgroup label="Kelas {{ $k->nama }}">
                                                @foreach($k->mataPelajaranKelas as $mpk)
                                                    <option value="{{ $mpk->id }}">{{ $mpk->mataPelajaran->nama }} ({{ $k->nama }})</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Jadwal akan dibuat terpisah untuk setiap kelas yang dipilih.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Bank Soal <span class="text-danger">*</span></label>
                                    <select name="bank_soal_id" class="form-control select2" required>
                                        <option value="">-- Pilih Bank Soal --</option>
                                        @foreach($bankSoal as $b)
                                            <option value="{{ $b->id }}">{{ $b->kode }} - {{ $b->nama }} ({{ $b->mataPelajaran->nama }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Waktu Mulai <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="tanggal_mulai" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Waktu Selesai <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="tanggal_selesai" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                             <div class="col-md-6">
                                <div class="form-group">
                                    <label>Durasi (Menit) <span class="text-danger">*</span></label>
                                    <input type="number" name="durasi" class="form-control" value="60" min="1" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jumlah Soal <span class="text-danger">*</span></label>
                                    <input type="number" name="jumlah_soal" class="form-control" value="20" min="1" required>
                                    <small class="text-muted">Total soal yang diambil dari Bank Soal</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="form-group">
                            <label class="d-block">Pengaturan Tambahan</label>
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" class="custom-control-input" id="acak_soal" name="acak_soal" checked>
                                <label class="custom-control-label" for="acak_soal">Acak Soal</label>
                            </div>
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" class="custom-control-input" id="acak_opsi" name="acak_opsi" checked>
                                <label class="custom-control-label" for="acak_opsi">Acak Opsi Jawaban</label>
                            </div>
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" class="custom-control-input" id="tampilkan_nilai" name="tampilkan_nilai" checked>
                                <label class="custom-control-label" for="tampilkan_nilai">Tampilkan Nilai di Akhir</label>
                            </div>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-calendar-check"></i> Buat Jadwal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle"></i> Catatan</h5>
                <ul class="pl-3 mb-0">
                    <li>Pastikan Bank Soal memiliki jumlah soal yang cukup.</li>
                    <li>Sistem akan generate <strong>Token</strong> otomatis.</li>
                    <li>Ujian hanya bisa diakses siswa pada rentang waktu yang ditentukan.</li>
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
        $('.select2').select2();
    });
</script>
@endpush
