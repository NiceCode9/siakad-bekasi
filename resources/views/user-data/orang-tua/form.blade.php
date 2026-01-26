<form id="formOrangTua" action="{{ $action }}" method="POST">
    @csrf
    @if ($method === 'PUT')
        <input type="hidden" name="_method" value="PUT">
    @endif

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" id="orangTuaFormTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="ayah-tab" data-toggle="tab" href="#ayah" role="tab">
                <i class="fas fa-male"></i> Data Ayah
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="ibu-tab" data-toggle="tab" href="#ibu" role="tab">
                <i class="fas fa-female"></i> Data Ibu
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="wali-tab" data-toggle="tab" href="#wali" role="tab">
                <i class="fas fa-user"></i> Data Wali
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="alamat-tab" data-toggle="tab" href="#alamat" role="tab">
                <i class="fas fa-map-marker-alt"></i> Alamat
            </a>
        </li>
        @if (!$orangTua)
            <li class="nav-item">
                <a class="nav-link" id="akun-tab" data-toggle="tab" href="#akun" role="tab">
                    <i class="fas fa-user-lock"></i> Akun
                </a>
            </li>
        @else
            @if ($orangTua->user)
                <li class="nav-item">
                    <a class="nav-link" id="akun-tab" data-toggle="tab" href="#akun" role="tab">
                        <i class="fas fa-user-lock"></i> Akun
                    </a>
                </li>
            @endif
        @endif
    </ul>

    <!-- Tab content -->
    <div class="tab-content mt-3">
        <!-- Tab Data Ayah -->
        <div class="tab-pane fade show active" id="ayah" role="tabpanel">
            <div class="form-group">
                <label>NIK Ayah</label>
                <input type="text" name="nik_ayah" class="form-control form-control-sm"
                    value="{{ old('nik_ayah', $orangTua->nik_ayah ?? '') }}" maxlength="16" placeholder="16 digit">
            </div>

            <div class="form-group">
                <label>Nama Ayah</label>
                <input type="text" name="nama_ayah" class="form-control form-control-sm"
                    value="{{ old('nama_ayah', $orangTua->nama_ayah ?? '') }}">
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Pekerjaan Ayah</label>
                        <input type="text" name="pekerjaan_ayah" class="form-control form-control-sm"
                            value="{{ old('pekerjaan_ayah', $orangTua->pekerjaan_ayah ?? '') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Pendidikan Ayah</label>
                        <select name="pendidikan_ayah" class="form-control form-control-sm">
                            <option value="">-- Pilih --</option>
                            @foreach (['SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'] as $pend)
                                <option value="{{ $pend }}"
                                    {{ old('pendidikan_ayah', $orangTua->pendidikan_ayah ?? '') == $pend ? 'selected' : '' }}>
                                    {{ $pend }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Penghasilan Ayah</label>
                        <select name="penghasilan_ayah" class="form-control form-control-sm">
                            <option value="">-- Pilih --</option>
                            <option value="< 1 juta"
                                {{ old('penghasilan_ayah', $orangTua->penghasilan_ayah ?? '') == '< 1 juta' ? 'selected' : '' }}>
                                < 1 juta</option>
                            <option value="1-2 juta"
                                {{ old('penghasilan_ayah', $orangTua->penghasilan_ayah ?? '') == '1-2 juta' ? 'selected' : '' }}>
                                1-2 juta</option>
                            <option value="2-5 juta"
                                {{ old('penghasilan_ayah', $orangTua->penghasilan_ayah ?? '') == '2-5 juta' ? 'selected' : '' }}>
                                2-5 juta</option>
                            <option value="5-10 juta"
                                {{ old('penghasilan_ayah', $orangTua->penghasilan_ayah ?? '') == '5-10 juta' ? 'selected' : '' }}>
                                5-10 juta</option>
                            <option value="> 10 juta"
                                {{ old('penghasilan_ayah', $orangTua->penghasilan_ayah ?? '') == '> 10 juta' ? 'selected' : '' }}>
                                > 10 juta</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Telepon Ayah</label>
                        <input type="text" name="telepon_ayah" class="form-control form-control-sm"
                            value="{{ old('telepon_ayah', $orangTua->telepon_ayah ?? '') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Data Ibu -->
        <div class="tab-pane fade" id="ibu" role="tabpanel">
            <div class="form-group">
                <label>NIK Ibu</label>
                <input type="text" name="nik_ibu" class="form-control form-control-sm"
                    value="{{ old('nik_ibu', $orangTua->nik_ibu ?? '') }}" maxlength="16" placeholder="16 digit">
            </div>

            <div class="form-group">
                <label>Nama Ibu</label>
                <input type="text" name="nama_ibu" class="form-control form-control-sm"
                    value="{{ old('nama_ibu', $orangTua->nama_ibu ?? '') }}">
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Pekerjaan Ibu</label>
                        <input type="text" name="pekerjaan_ibu" class="form-control form-control-sm"
                            value="{{ old('pekerjaan_ibu', $orangTua->pekerjaan_ibu ?? '') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Pendidikan Ibu</label>
                        <select name="pendidikan_ibu" class="form-control form-control-sm">
                            <option value="">-- Pilih --</option>
                            @foreach (['SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'] as $pend)
                                <option value="{{ $pend }}"
                                    {{ old('pendidikan_ibu', $orangTua->pendidikan_ibu ?? '') == $pend ? 'selected' : '' }}>
                                    {{ $pend }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Penghasilan Ibu</label>
                        <select name="penghasilan_ibu" class="form-control form-control-sm">
                            <option value="">-- Pilih --</option>
                            <option value="< 1 juta"
                                {{ old('penghasilan_ibu', $orangTua->penghasilan_ibu ?? '') == '< 1 juta' ? 'selected' : '' }}>
                                < 1 juta</option>
                            <option value="1-2 juta"
                                {{ old('penghasilan_ibu', $orangTua->penghasilan_ibu ?? '') == '1-2 juta' ? 'selected' : '' }}>
                                1-2 juta</option>
                            <option value="2-5 juta"
                                {{ old('penghasilan_ibu', $orangTua->penghasilan_ibu ?? '') == '2-5 juta' ? 'selected' : '' }}>
                                2-5 juta</option>
                            <option value="5-10 juta"
                                {{ old('penghasilan_ibu', $orangTua->penghasilan_ibu ?? '') == '5-10 juta' ? 'selected' : '' }}>
                                5-10 juta</option>
                            <option value="> 10 juta"
                                {{ old('penghasilan_ibu', $orangTua->penghasilan_ibu ?? '') == '> 10 juta' ? 'selected' : '' }}>
                                > 10 juta</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Telepon Ibu</label>
                        <input type="text" name="telepon_ibu" class="form-control form-control-sm"
                            value="{{ old('telepon_ibu', $orangTua->telepon_ibu ?? '') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Data Wali -->
        <div class="tab-pane fade" id="wali" role="tabpanel">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Isi data wali jika siswa tidak tinggal dengan orang tua kandung
            </div>

            <div class="form-group">
                <label>Nama Wali</label>
                <input type="text" name="nama_wali" class="form-control form-control-sm"
                    value="{{ old('nama_wali', $orangTua->nama_wali ?? '') }}">
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Pekerjaan Wali</label>
                        <input type="text" name="pekerjaan_wali" class="form-control form-control-sm"
                            value="{{ old('pekerjaan_wali', $orangTua->pekerjaan_wali ?? '') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Telepon Wali</label>
                        <input type="text" name="telepon_wali" class="form-control form-control-sm"
                            value="{{ old('telepon_wali', $orangTua->telepon_wali ?? '') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Alamat -->
        <div class="tab-pane fade" id="alamat" role="tabpanel">
            <div class="form-group">
                <label>Alamat Lengkap</label>
                <textarea name="alamat" class="form-control form-control-sm" rows="3">{{ old('alamat', $orangTua->alamat ?? '') }}</textarea>
            </div>
        </div>

        <!-- Tab Akun -->
        @if (!$orangTua)
            <div class="tab-pane fade" id="akun" role="tabpanel">
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="create_account"
                            name="create_account" value="1">
                        <label class="custom-control-label" for="create_account">
                            Buat akun untuk orang tua ini
                        </label>
                    </div>
                    <small class="form-text text-muted">Orang tua bisa login dan melihat data anaknya</small>
                </div>

                <div id="account_fields" style="display: none;">
                    <div class="form-group">
                        <label>Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control form-control-sm"
                            value="{{ old('username') }}">
                    </div>

                    <div class="form-group">
                        <label>Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control form-control-sm"
                            value="{{ old('email') }}">
                    </div>

                    <div class="form-group">
                        <label>Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control form-control-sm">
                        <small class="form-text text-muted">Minimal 6 karakter</small>
                    </div>
                </div>
            </div>
        @else
            @if ($orangTua->user)
                <div class="tab-pane fade" id="akun" role="tabpanel">
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Orang tua sudah memiliki akun
                    </div>

                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control form-control-sm"
                            value="{{ old('username', $orangTua->user->username) }}">
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control form-control-sm"
                            value="{{ old('email', $orangTua->user->email) }}">
                    </div>

                    <div class="form-group">
                        <label>Password Baru</label>
                        <input type="password" name="password" class="form-control form-control-sm">
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password</small>
                    </div>
                </div>
            @endif
        @endif
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

<script>
    $(document).ready(function() {
        // Toggle account fields
        $('#create_account').change(function() {
            if ($(this).is(':checked')) {
                $('#account_fields').slideDown();
                $('#account_fields input').attr('required', true);
            } else {
                $('#account_fields').slideUp();
                $('#account_fields input').attr('required', false);
            }
        });
    });
</script>
