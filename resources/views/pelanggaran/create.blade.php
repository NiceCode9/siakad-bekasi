@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>Lapor Pelanggaran Siswa</h1>
            <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                <ol class="breadcrumb pt-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pelanggaran-siswa.index') }}">Pelanggaran Siswa</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Lapor Baru</li>
                </ol>
            </nav>
            <div class="separator mb-5"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-8 offset-md-2">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-4">Form Laporan Pelanggaran</h5>

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('pelanggaran-siswa.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="tanggal">Tanggal Kejadian</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="kelas_id">Kelas</label>
                            <select class="form-control select2-single" id="kelas_id" name="kelas_id">
                                <option value="">Pilih Kelas...</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama }} ({{ $k->jurusan->singkatan }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="siswa_id">Nama Siswa</label>
                            <select class="form-control select2-single" id="siswa_id" name="siswa_id" required disabled>
                                <option value="">Pilih Kelas Terlebih Dahulu...</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="jenis_pelanggaran">Jenis Pelanggaran</label>
                            <input type="text" class="form-control" id="jenis_pelanggaran" name="jenis_pelanggaran" placeholder="Contoh: Terlambat, Atribut Tidak Lengkap" required>
                            <small class="form-text text-muted">Deskripsi singkat jenis pelanggaran.</small>
                        </div>

                        <div class="form-group">
                            <label for="kronologi">Kronologi / Keterangan Tambahan</label>
                            <textarea class="form-control" id="kronologi" name="kronologi" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg btn-block">Kirim Laporan</button>
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
        $('#kelas_id').on('change', function() {
            var kelasId = $(this).val();
            var $siswaSelect = $('#siswa_id');

            if(kelasId) {
                $siswaSelect.prop('disabled', true).html('<option value="">Memuat data...</option>');
                
                $.ajax({
                    url: "{{ route('pelanggaran-siswa.get-students') }}",
                    type: "GET",
                    data: { kelas_id: kelasId },
                    success: function(data) {
                        var options = '<option value="">Pilih Siswa...</option>';
                        $.each(data, function(key, value) {
                            options += '<option value="'+ value.id +'">'+ value.nama_lengkap + ' (' + value.nis + ')</option>';
                        });
                        $siswaSelect.html(options).prop('disabled', false);
                    },
                    error: function() {
                        alert('Gagal memuat data siswa');
                        $siswaSelect.html('<option value="">Gagal memuat data</option>');
                    }
                });
            } else {
                $siswaSelect.prop('disabled', true).html('<option value="">Pilih Kelas Terlebih Dahulu...</option>');
            }
        });
    });
</script>
@endpush
