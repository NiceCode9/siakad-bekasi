@extends('layouts.app')

@section('title', 'Tambah Penempatan PKL')

@push('styles')
    <style>
        .select2-container--bootstrap4 .select2-selection--multiple {
            min-height: calc(1.5em + 0.75rem + 2px) !important;
        }

        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
            background-color: #007bff;
            border: 1px solid #006fe6;
            color: #fff;
            padding: 0 5px;
            margin-top: 0.35rem;
        }

        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove {
            color: #fff;
            margin-right: 5px;
        }

        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #eee;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3 font-weight-bold">Tambah Data Penempatan PKL</h5>
                        <form action="{{ route('pkl.store') }}" method="POST">
                            @csrf

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="form-group">
                                <label>Pilih Siswa <span class="text-danger">* (Bisa pilih lebih dari satu)</span></label>
                                <select name="siswa_id[]"
                                    class="form-control select2-multiple @error('siswa_id') is-invalid @enderror"
                                    multiple="multiple" required>
                                    @foreach ($kelas as $k)
                                        <optgroup label="{{ $k->nama }}">
                                            @foreach ($k->siswaKelas()->where('status', 'aktif')->with('siswa')->get() as $sk)
                                                <option value="{{ $sk->siswa->id }}"
                                                    {{ collect(old('siswa_id'))->contains($sk->siswa->id) ? 'selected' : '' }}>
                                                    {{ $sk->siswa->nis }} -
                                                    {{ $sk->siswa->nama_lengkap }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                @error('siswa_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Tempat PKL <span class="text-danger">*</span></label>
                                <select name="perusahaan_pkl_id"
                                    class="form-control select2 @error('perusahaan_pkl_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Industri --</option>
                                    @foreach ($perusahaanPkl as $tm)
                                        <option value="{{ $tm->id }}"
                                            {{ old('perusahaan_pkl_id') == $tm->id ? 'selected' : '' }}>
                                            {{ $tm->nama }} ({{ $tm->bidang_usaha }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('perusahaan_pkl_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Pembimbing Sekolah (Guru)</label>
                                        <select name="pembimbing_sekolah_id"
                                            class="form-control select2 @error('pembimbing_sekolah_id') is-invalid @enderror">
                                            <option value="">-- Pilih Guru --</option>
                                            @foreach ($gurus as $guru)
                                                <option value="{{ $guru->id }}"
                                                    {{ old('pembimbing_sekolah_id') == $guru->id ? 'selected' : '' }}>
                                                    {{ $guru->nama_lengkap }}</option>
                                            @endforeach
                                        </select>
                                        @error('pembimbing_sekolah_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Pembimbing Lapangan (Industri)</label>
                                        <input type="text" name="pembimbing_industri"
                                            class="form-control @error('pembimbing_industri') is-invalid @enderror"
                                            value="{{ old('pembimbing_industri') }}"
                                            placeholder="Nama Pembimbing di Industri">
                                        @error('pembimbing_industri')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tanggal Mulai</label>
                                        <input type="date" name="tanggal_mulai"
                                            class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                            value="{{ old('tanggal_mulai') }}" required>
                                        @error('tanggal_mulai')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tanggal Selesai</label>
                                        <input type="date" name="tanggal_selesai"
                                            class="form-control @error('tanggal_selesai') is-invalid @enderror"
                                            value="{{ old('tanggal_selesai') }}" required>
                                        @error('tanggal_selesai')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control @error('status') is-invalid @enderror">
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending
                                    </option>
                                    <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="selesai" {{ old('status') == 'selesai' ? 'selected' : '' }}>Selesai
                                    </option>
                                    <option value="batal" {{ old('status') == 'batal' ? 'selected' : '' }}>Batal</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="{{ route('pkl.index') }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2, .select2-multiple').select2({
                theme: 'bootstrap',
                placeholder: 'Pilih...',
                width: '100%',
                allowClear: true
            });
        });
    </script>
@endpush
