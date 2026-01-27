<form id="formPelanggaran" action="{{ $action }}" method="POST">
    @csrf
    @if ($method === 'PUT')
        <input type="hidden" name="_method" value="PUT">
    @endif

    <div class="form-group">
        <label>Siswa <span class="text-danger">*</span></label>
        <select name="siswa_id" id="selectSiswa" class="form-control" style="width: 100%" required>
            @if(isset($pelanggaran) && $pelanggaran->siswa)
                <option value="{{ $pelanggaran->siswa_id }}" selected>
                    {{ $pelanggaran->siswa->nama_lengkap }} ({{ $pelanggaran->siswa->nisn }})
                </option>
            @endif
        </select>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Tanggal <span class="text-danger">*</span></label>
                <input type="date" name="tanggal" class="form-control" value="{{ isset($pelanggaran) && $pelanggaran->tanggal ? $pelanggaran->tanggal->format('Y-m-d') : date('Y-m-d') }}" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Kategori <span class="text-danger">*</span></label>
                <select name="kategori" class="form-control" required>
                    <option value="">-- Pilih --</option>
                    @foreach(['Ringan', 'Sedang', 'Berat'] as $k)
                        <option value="{{ $k }}" {{ (isset($pelanggaran) && $pelanggaran->kategori == $k) ? 'selected' : '' }}>{{ $k }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label>Jenis Pelanggaran <span class="text-danger">*</span></label>
                <input type="text" name="jenis_pelanggaran" class="form-control" value="{{ $pelanggaran->jenis_pelanggaran ?? '' }}" placeholder="Contoh: Terlambat, Merokok" required>
            </div>
        </div>
        <div class="col-md-4">
             <div class="form-group">
                <label>Poin Pinalti <span class="text-danger">*</span></label>
                <input type="number" name="poin" class="form-control" value="{{ $pelanggaran->poin ?? 0 }}" min="0" required>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label>Kronologi</label>
        <textarea name="kronologi" class="form-control" rows="3" required>{{ $pelanggaran->kronologi ?? '' }}</textarea>
    </div>

    <div class="form-group">
        <label>Sanksi Diberikan</label>
        <textarea name="sanksi" class="form-control" rows="2">{{ $pelanggaran->sanksi ?? '' }}</textarea>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Pelapor (Guru)</label>
                <select name="pelapor_id" id="selectGuru" class="form-control" style="width: 100%">
                    @if(isset($pelanggaran) && $pelanggaran->pelapor)
                        <option value="{{ $pelanggaran->pelapor_id }}" selected>
                            {{ $pelanggaran->pelapor->nama_lengkap }}
                        </option>
                    @endif
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    <option value="proses" {{ (isset($pelanggaran) && $pelanggaran->status == 'proses') ? 'selected' : '' }}>Proses</option>
                    <option value="selesai" {{ (isset($pelanggaran) && $pelanggaran->status == 'selesai') ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
        </div>
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

    $('#selectGuru').select2({
        placeholder: 'Cari Guru Pelapor...',
        dropdownParent: $('#formModal'),
        ajax: {
            url: "{{ route('guru.search') }}",
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
