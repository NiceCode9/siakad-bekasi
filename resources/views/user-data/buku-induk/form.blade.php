<div class="alert alert-info mb-3">
    <strong>Siswa:</strong> {{ $siswa->nama_lengkap }} ({{ $siswa->nis }} / {{ $siswa->nisn }})
</div>

<form id="formBukuInduk" action="{{ $action }}" method="POST">
    @csrf

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Nomor Induk</label>
                <input type="text" name="nomor_induk" class="form-control form-control-sm"
                    value="{{ old('nomor_induk', $bukuInduk->nomor_induk ?? $siswa->nis) }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Nomor Peserta Ujian</label>
                <input type="text" name="nomor_peserta_ujian" class="form-control form-control-sm"
                    value="{{ old('nomor_peserta_ujian', $bukuInduk->nomor_peserta_ujian ?? '') }}">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Nomor Seri Ijazah</label>
                <input type="text" name="nomor_seri_ijazah" class="form-control form-control-sm"
                    value="{{ old('nomor_seri_ijazah', $bukuInduk->nomor_seri_ijazah ?? '') }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Nomor Seri SKHUN</label>
                <input type="text" name="nomor_seri_skhun" class="form-control form-control-sm"
                    value="{{ old('nomor_seri_skhun', $bukuInduk->nomor_seri_skhun ?? '') }}">
            </div>
        </div>
    </div>

    <div class="form-group">
        <label>Tanggal Lulus</label>
        <input type="date" name="tanggal_lulus" class="form-control form-control-sm"
            value="{{ old('tanggal_lulus', $bukuInduk->tanggal_lulus ? $bukuInduk->tanggal_lulus->format('Y-m-d') : '') }}">
    </div>

    <div class="form-group">
        <label>Riwayat Pendidikan</label>
        <textarea name="riwayat_pendidikan" class="form-control form-control-sm" rows="3" placeholder="Contoh: SD Negeri 1 (2015-2021)">{{ old('riwayat_pendidikan', $bukuInduk->riwayat_pendidikan ?? '') }}</textarea>
    </div>

    <div class="form-group">
        <label>Riwayat Kesehatan</label>
        <textarea name="riwayat_kesehatan" class="form-control form-control-sm" rows="3" placeholder="Penyakit berat yang pernah diderita...">{{ old('riwayat_kesehatan', $bukuInduk->riwayat_kesehatan ?? '') }}</textarea>
    </div>

    <div class="form-group">
        <label>Catatan Khusus</label>
        <textarea name="catatan_khusus" class="form-control form-control-sm" rows="3" placeholder="Prestasi, beasiswa, atau catatan penting lainnya...">{{ old('catatan_khusus', $bukuInduk->catatan_khusus ?? '') }}</textarea>
    </div>

    <hr>
    <div class="text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>
