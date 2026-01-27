<form id="formPrestasi" action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if ($method === 'PUT')
        <input type="hidden" name="_method" value="PUT">
    @endif

    <div class="form-group">
        <label>Siswa <span class="text-danger">*</span></label>
        <select name="siswa_id" id="selectSiswa" class="form-control" style="width: 100%" required>
            @if(isset($prestasi) && $prestasi->siswa)
                <option value="{{ $prestasi->siswa_id }}" selected>
                    {{ $prestasi->siswa->nama_lengkap }} ({{ $prestasi->siswa->nisn }})
                </option>
            @endif
        </select>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Jenis Prestasi <span class="text-danger">*</span></label>
                <select name="jenis" class="form-control" required>
                    <option value="Akademik" {{ (isset($prestasi) && $prestasi->jenis == 'Akademik') ? 'selected' : '' }}>Akademik</option>
                    <option value="Non-Akademik" {{ (isset($prestasi) && $prestasi->jenis == 'Non-Akademik') ? 'selected' : '' }}>Non-Akademik</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Tanggal <span class="text-danger">*</span></label>
                <input type="date" name="tanggal" class="form-control" value="{{ isset($prestasi) && $prestasi->tanggal ? $prestasi->tanggal->format('Y-m-d') : date('Y-m-d') }}" required>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label>Nama Prestasi <span class="text-danger">*</span></label>
        <input type="text" name="nama_prestasi" class="form-control" value="{{ $prestasi->nama_prestasi ?? '' }}" placeholder="Contoh: Juara 1 Lomba Matematika" required>
    </div>

    <div class="row">
        <div class="col-md-6">
             <div class="form-group">
                <label>Peringkat <span class="text-danger">*</span></label>
                <input type="text" name="peringkat" class="form-control" value="{{ $prestasi->peringkat ?? '' }}" placeholder="Contoh: Juara 1, Harapan 1" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Tingkat <span class="text-danger">*</span></label>
                <input type="text" name="tingkat" class="form-control" value="{{ $prestasi->tingkat ?? '' }}" placeholder="Contoh: Kabupaten/Kota, Provinsi, Nasional" required>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label>Penyelenggara</label>
        <input type="text" name="penyelenggara" class="form-control" value="{{ $prestasi->penyelenggara ?? '' }}" placeholder="Contoh: Dinas Pendidikan">
    </div>

    <div class="form-group">
        <label>Keterangan</label>
        <textarea name="keterangan" class="form-control" rows="2">{{ $prestasi->keterangan ?? '' }}</textarea>
    </div>

    <div class="form-group">
        <label>File Sertifikat</label>
        <input type="file" name="file_sertifikat" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png">
        <small class="text-muted">Format: PDF, JPG, PNG (Max 2MB)</small>
        @if(isset($prestasi) && $prestasi->file_sertifikat)
            <div class="mt-2">
                <a href="{{ Storage::url($prestasi->file_sertifikat) }}" target="_blank" class="text-info">
                    <i class="fas fa-file-download"></i> Lihat Sertifikat Saat Ini
                </a>
            </div>
        @endif
    </div>

    <hr>
    <div class="text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>

<script>
    $('#selectSiswa').select2({
        placeholder: 'Cari Siswa...',
        dropdownParent: $('#formModal'),
        ajax: {
            url: "{{ route('siswa.search') }}",
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        }
    });
</script>
