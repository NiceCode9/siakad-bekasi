<form id="formGuru" action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if ($method === 'PUT')
        <input type="hidden" name="_method" value="PUT">
    @endif

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" id="guruFormTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="akun-tab" data-toggle="tab" href="#akun" role="tab">
                <i class="fas fa-user-lock"></i> Akun
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="biodata-tab" data-toggle="tab" href="#biodata" role="tab">
                <i class="fas fa-id-card"></i> Biodata
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="kepegawaian-tab" data-toggle="tab" href="#kepegawaian" role="tab">
                <i class="fas fa-briefcase"></i> Kepegawaian
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
                        <input type="text" name="username" class="form-control"
                            value="{{ old('username', $guru->user->username ?? '') }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control"
                            value="{{ old('email', $guru->user->email ?? '') }}" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Password {!! $guru ? '' : "<span class='text-danger'>*</span>" !!}</label>
                        <input type="password" name="password" class="form-control"
                            placeholder="{{ $guru ? 'Kosongkan jika tidak diubah' : '' }}"
                            {{ $guru ? '' : 'required' }}>
                        <small class="form-text text-muted">Minimal 6 karakter</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group row mb-1">
                        <label class="col-12 col-form-label">Status Aktif</label>
                        <div class="col-12">
                            <div class="custom-switch custom-switch-small custom-switch-secondary mb-2">
                                <input class="custom-switch-input" id="is_active" name="is_active" type="checkbox"
                                    value="1" {{ old('is_active', $guru->is_active ?? true) ? 'checked' : '' }}>
                                <label class="custom-switch-btn" for="is_active"></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Biodata -->
        <div class="tab-pane fade" id="biodata" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Gelar Depan</label>
                        <input type="text" name="gelar_depan" class="form-control"
                            value="{{ old('gelar_depan', $guru->gelar_depan ?? '') }}" placeholder="Contoh: Dr.">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Gelar Belakang</label>
                        <input type="text" name="gelar_belakang" class="form-control"
                            value="{{ old('gelar_belakang', $guru->gelar_belakang ?? '') }}" placeholder="Contoh: M.Pd">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="nama_lengkap" class="form-control"
                    value="{{ old('nama_lengkap', $guru->nama_lengkap ?? '') }}" required>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="jenis_kelamin" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            <option value="L"
                                {{ old('jenis_kelamin', $guru->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>
                                Laki-laki</option>
                            <option value="P"
                                {{ old('jenis_kelamin', $guru->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>
                                Perempuan</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Agama</label>
                        <select name="agama" class="form-control">
                            <option value="">-- Pilih --</option>
                            @foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                                <option value="{{ $agama }}"
                                    {{ old('agama', $guru->agama ?? '') == $agama ? 'selected' : '' }}>
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
                        <input type="text" name="tempat_lahir" class="form-control"
                            value="{{ old('tempat_lahir', $guru->tempat_lahir ?? '') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control"
                            value="{{ old('tanggal_lahir', $guru->tanggal_lahir ?? '') }}">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control" rows="2">{{ old('alamat', $guru->alamat ?? '') }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Telepon</label>
                        <input type="text" name="telepon" class="form-control"
                            value="{{ old('telepon', $guru->telepon ?? '') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Email Guru</label>
                        <input type="email" name="email_guru" class="form-control"
                            value="{{ old('email_guru', $guru->email ?? '') }}"
                            placeholder="Email alternatif (opsional)">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Foto</label>
                <input type="file" name="foto" class="form-control-file" accept="image/*">
                <small class="form-text text-muted">Format: JPG, JPEG, PNG (max 2MB)</small>
                @if ($guru && $guru->foto)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $guru->foto) }}" alt="Foto" class="img-thumbnail"
                            width="100">
                    </div>
                @endif
            </div>
        </div>

        <!-- Tab Kepegawaian -->
        <div class="tab-pane fade" id="kepegawaian" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>NIP</label>
                        <input type="text" name="nip" class="form-control"
                            value="{{ old('nip', $guru->nip ?? '') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>NUPTK</label>
                        <input type="text" name="nuptk" class="form-control"
                            value="{{ old('nuptk', $guru->nuptk ?? '') }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Status Kepegawaian</label>
                        <select name="status_kepegawaian" class="form-control">
                            <option value="">-- Pilih --</option>
                            @foreach (['PNS', 'PPPK', 'GTY', 'GTT', 'Honorer'] as $status)
                                <option value="{{ $status }}"
                                    {{ old('status_kepegawaian', $guru->status_kepegawaian ?? '') == $status ? 'selected' : '' }}>
                                    {{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" class="form-control"
                            value="{{ old('tanggal_masuk', $guru->tanggal_masuk ?? '') }}">
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
