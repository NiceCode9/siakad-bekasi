@extends('layouts.app')

@section('title', 'Tambah Penempatan PKL')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Tambah Data Penempatan PKL</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('pkl-siswa.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label>Pilih Siswa <span class="text-danger">*</span></label>
                            <select name="siswa_id" class="form-control select2" required>
                                <option value="">-- Pilih Siswa --</option>
                                @foreach($kelas as $k)
                                    <optgroup label="{{ $k->nama }}">
                                        @foreach($k->siswaKelas()->where('status', 'aktif')->with('siswa')->get() as $sk)
                                            <option value="{{ $sk->siswa->id }}">{{ $sk->siswa->nis }} - {{ $sk->siswa->nama }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Tempat PKL <span class="text-danger">*</span></label>
                            <select name="tempat_pkl_id" class="form-control select2" required>
                                <option value="">-- Pilih Industri --</option>
                                @foreach($tempatPkl as $tm)
                                    <option value="{{ $tm->id }}">{{ $tm->nama }} ({{ $tm->bidang_usaha }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Pembimbing Sekolah (Guru)</label>
                                    <select name="pembimbing_sekolah_id" class="form-control select2">
                                        <option value="">-- Pilih Guru --</option>
                                        @foreach($gurus as $guru)
                                            <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Pembimbing Lapangan (Industri)</label>
                                    <input type="text" name="pembimbing_industri" class="form-control" placeholder="Nama Pembimbing di Industri">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulai" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tanggal Selesai</label>
                                    <input type="date" name="tanggal_selesai" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="pengajuan">Pengajuan</option>
                                <option value="disetujui">Disetujui</option>
                                <option value="aktif">Aktif</option>
                                <option value="selesai">Selesai</option>
                            </select>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('pkl-siswa.index') }}" class="btn btn-secondary">Batal</a>
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
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: 'Pilih...',
            allowClear: true
        });
    });
</script>
@endpush
