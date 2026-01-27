<form id="formSiswa" action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if ($method === 'PUT')
        <input type="hidden" name="_method" value="PUT">
    @endif

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" id="siswaFormTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="akun-tab" data-toggle="tab" href="#akun" role="tab">
                <i class="fas fa-user-lock"></i> Akun
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="identitas-tab" data-toggle="tab" href="#identitas" role="tab">
                <i class="fas fa-id-card"></i> Identitas
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="alamat-tab" data-toggle="tab" href="#alamat" role="tab">
                <i class="fas fa-map-marker-alt"></i> Alamat
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="pendidikan-tab" data-toggle="tab" href="#pdd" role="tab">
                <i class="fas fa-graduation-cap"></i> Pendidikan
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="fisik-tab" data-toggle="tab" href="#df" role="tab">
                <i class="fas fa-heartbeat"></i> Data Fisik
            </a>
        </li>
    </ul>

    <!-- Tab content -->
    <div class="tab-content mt-3">
        <!-- Tab Akun -->
        <div class="tab-pane fade show active" id="akun" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control form-control-sm"
                            value="{{ old('username', $siswa->user->username ?? '') }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control form-control-sm"
                            value="{{ old('email', $siswa->user->email ?? '') }}" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Password {{ $siswa ? '' : '<span class="text-danger">*</span>' }}</label>
                        <input type="password" name="password" class="form-control form-control-sm"
                            placeholder="{{ $siswa ? 'Kosongkan jika tidak diubah' : '' }}"
                            {{ $siswa ? '' : 'required' }}>
                        <small class="form-text text-muted">Minimal 6 karakter</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Orang Tua</label>
                        <select name="orang_tua_id" class="form-control form-control-sm">
                            <option value="">-- Pilih Orang Tua --</option>
                            @foreach ($orangTua as $ot)
                                <option value="{{ $ot->id }}"
                                    {{ old('orang_tua_id', $siswa->orang_tua_id ?? '') == $ot->id ? 'selected' : '' }}>
                                    {{ $ot->nama_ayah }} / {{ $ot->nama_ibu }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Identitas -->
        <div class="tab-pane fade" id="identitas" role="tabpanel">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>NISN <span class="text-danger">*</span></label>
                        <input type="text" name="nisn" class="form-control form-control-sm"
                            value="{{ old('nisn', $siswa->nisn ?? '') }}" maxlength="10" required>
                        <small class="form-text text-muted">10 digit</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>NIS <span class="text-danger">*</span></label>
                        <input type="text" name="nis" class="form-control form-control-sm"
                            value="{{ old('nis', $siswa->nis ?? '') }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>NIK</label>
                        <input type="text" name="nik" class="form-control form-control-sm"
                            value="{{ old('nik', $siswa->nik ?? '') }}" maxlength="16">
                        <small class="form-text text-muted">16 digit</small>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="nama_lengkap" class="form-control form-control-sm"
                    value="{{ old('nama_lengkap', $siswa->nama_lengkap ?? '') }}" required>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="jenis_kelamin" class="form-control form-control-sm" required>
                            <option value="">-- Pilih --</option>
                            <option value="L"
                                {{ old('jenis_kelamin', $siswa->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>
                                Laki-laki</option>
                            <option value="P"
                                {{ old('jenis_kelamin', $siswa->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>
                                Perempuan</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Agama</label>
                        <select name="agama" class="form-control form-control-sm">
                            <option value="">-- Pilih --</option>
                            @foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                                <option value="{{ $agama }}"
                                    {{ old('agama', $siswa->agama ?? '') == $agama ? 'selected' : '' }}>
                                    {{ $agama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="form-control form-control-sm"
                            value="{{ old('tempat_lahir', $siswa->tempat_lahir ?? '') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control form-control-sm"
                            value="{{ old('tanggal_lahir', $siswa->tanggal_lahir ?? '') }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Anak Ke</label>
                        <input type="number" name="anak_ke" class="form-control form-control-sm"
                            value="{{ old('anak_ke', $siswa->anak_ke ?? '') }}" min="1">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Jumlah Saudara</label>
                        <input type="number" name="jumlah_saudara" class="form-control form-control-sm"
                            value="{{ old('jumlah_saudara', $siswa->jumlah_saudara ?? '') }}" min="0">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Telepon</label>
                        <input type="text" name="telepon" class="form-control form-control-sm"
                            value="{{ old('telepon', $siswa->telepon ?? '') }}">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Email Siswa</label>
                <input type="email" name="email_siswa" class="form-control form-control-sm"
                    value="{{ old('email_siswa', $siswa->email ?? '') }}" placeholder="Email alternatif (opsional)">
            </div>

            <div class="form-group">
                <label>Foto</label>
                <input type="file" name="foto" class="form-control-file" accept="image/*">
                <small class="form-text text-muted">Format: JPG, JPEG, PNG (max 2MB)</small>
                @if ($siswa && $siswa->foto)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $siswa->foto) }}" alt="Foto" class="img-thumbnail"
                            width="100">
                    </div>
                @endif
            </div>

            @if ($siswa)
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control form-control-sm">
                        @foreach (['aktif', 'lulus', 'pindah', 'keluar', 'DO'] as $status)
                            <option value="{{ $status }}"
                                {{ old('status', $siswa->status ?? '') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <!-- Tab Alamat -->
        <div class="tab-pane fade" id="alamat" role="tabpanel">
            <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control form-control-sm" rows="2">{{ old('alamat', $siswa->alamat ?? '') }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>RT</label>
                        <input type="text" name="rt" class="form-control form-control-sm"
                            value="{{ old('rt', $siswa->rt ?? '') }}" maxlength="5">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>RW</label>
                        <input type="text" name="rw" class="form-control form-control-sm"
                            value="{{ old('rw', $siswa->rw ?? '') }}" maxlength="5">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Kelurahan/Desa</label>
                        <input type="text" name="kelurahan" class="form-control form-control-sm"
                            value="{{ old('kelurahan', $siswa->kelurahan ?? '') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Kecamatan</label>
                        <input type="text" name="kecamatan" class="form-control form-control-sm"
                            value="{{ old('kecamatan', $siswa->kecamatan ?? '') }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Kota/Kabupaten</label>
                        <input type="text" name="kota" class="form-control form-control-sm"
                            value="{{ old('kota', $siswa->kota ?? '') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Provinsi</label>
                        <input type="text" name="provinsi" class="form-control form-control-sm"
                            value="{{ old('provinsi', $siswa->provinsi ?? '') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Kode Pos</label>
                        <input type="text" name="kode_pos" class="form-control form-control-sm"
                            value="{{ old('kode_pos', $siswa->kode_pos ?? '') }}" maxlength="10">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Pendidikan -->
        <div class="tab-pane fade" id="pdd" role="tabpanel">
            <div class="form-group">
                <label>Asal Sekolah (SMP)</label>
                <input type="text" name="asal_sekolah" class="form-control form-control-sm"
                    value="{{ old('asal_sekolah', $siswa->asal_sekolah ?? '') }}">
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tahun Lulus SMP</label>
                        <input type="number" name="tahun_lulus_smp" class="form-control form-control-sm"
                            value="{{ old('tahun_lulus_smp', $siswa->tahun_lulus_smp ?? '') }}" min="2000"
                            max="{{ date('Y') + 1 }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" class="form-control form-control-sm"
                            value="{{ old('tanggal_masuk', $siswa->tanggal_masuk ?? date('Y-m-d')) }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Data Fisik -->
        <div class="tab-pane fade" id="df" role="tabpanel">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tinggi Badan (cm)</label>
                        <input type="number" name="tinggi_badan" class="form-control form-control-sm"
                            value="{{ old('tinggi_badan', $siswa->tinggi_badan ?? '') }}" min="0"
                            max="300" step="0.1">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Berat Badan (kg)</label>
                        <input type="number" name="berat_badan" class="form-control form-control-sm"
                            value="{{ old('berat_badan', $siswa->berat_badan ?? '') }}" min="0"
                            max="200" step="0.1">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Golongan Darah</label>
                        <select name="golongan_darah" class="form-control form-control-sm">
                            <option value="">-- Pilih --</option>
                            @foreach (['A', 'B', 'AB', 'O'] as $gol)
                                <option value="{{ $gol }}"
                                    {{ old('golongan_darah', $siswa->golongan_darah ?? '') == $gol ? 'selected' : '' }}>
                                    {{ $gol }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <div class="form-group mb-0">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Simpan
        </button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times"></i> Batal
        </button>
    </div>
</form>
