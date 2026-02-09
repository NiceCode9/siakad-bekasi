@extends('layouts.app')

@section('title', 'Kenaikan Kelas')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>Kenaikan / Kelulusan Siswa</h1>
            <div class="separator mb-5"></div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-4">Panduan & Persyaratan</h5>
                    <div class="alert alert-info">
                        <h6 class="font-weight-bold">Persyaratan Teknis:</h6>
                        <ul class="pl-3 mb-3" style="font-size: 0.85rem;">
                            <li><strong>Tahun Akademik Target</strong> harus sudah terdaftar di sistem.</li>
                            <li><strong>Kelas Tujuan</strong> harus sudah tersedia di tahun target tersebut.</li>
                            <li><strong>Nilai Raport</strong> siswa di semester asal harus sudah <strong>Dipublikasikan</strong>.</li>
                        </ul>
                        <h6 class="font-weight-bold">Langkah-langkah:</h6>
                        <ol class="pl-3 mb-0" style="font-size: 0.85rem;">
                            <li>Pilih Kelas Asal (dari tahun aktif saat ini).</li>
                            <li>Pilih Tahun Akademik Baru sebagai Target (bisa tahun non-aktif).</li>
                            <li>Tinjau rekomendasi sistem (berdasarkan nilai & absensi).</li>
                            <li>Tentukan status dan pilih Kelas Tujuan.</li>
                            <li>Klik Eksekusi untuk memproses pemindahan data.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- New Process Selection -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-4">Mulai Proses Baru</h5>
                    <form action="{{ route('kenaikan-kelas.simulasi') }}" method="GET" id="kenaikanForm">
                        <div class="form-group">
                            <label>Tahun Akademik Asal (Sumber Data)</label>
                            <select id="tahun_asal_id" class="form-control select2-single" required>
                                <option value="">Pilih Tahun Asal...</option>
                                @foreach($tahunAkademiks as $ta)
                                    <option value="{{ $ta->id }}">{{ $ta->nama }} {{ $ta->is_active ? '(AKTIF)' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Dari Kelas</label>
                            <select name="kelas_asal_id" id="kelas_asal_id" class="form-control select2-single" required disabled>
                                <option value="">Pilih Tahun Asal Dulu...</option>
                            </select>
                            <div id="classLoader" class="text-info mt-1" style="display:none; font-size: 0.8rem;">
                                <i class="simple-icon-reload"></i> Menarik data kelas...
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Tahun Akademik Target</label>
                            <select name="tahun_akademik_id" id="tahun_akademik_id" class="form-control select2-single" required>
                                <option value="">Pilih Tahun Target...</option>
                                @foreach($tahunAkademiks as $ta)
                                    <option value="{{ $ta->id }}">{{ $ta->nama }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Siswa akan dipindahkan ke tahun akademik yang dipilih.</small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">MULAI SIMULASI</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- History Table -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-4">Riwayat Kenaikan/Kelulusan Selesai</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Tahun Akademik Target</th>
                                    <th>Total Siswa</th>
                                    <th>Naik/Lulus</th>
                                    <th>Tidak Naik</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($history as $item)
                                <tr>
                                    <td>{{ $item->tanggal_proses->format('d/m/Y') }}</td>
                                    <td>{{ $item->tahunAkademik->nama }}</td>
                                    <td>{{ $item->total_siswa }}</td>
                                    <td><span class="badge badge-success">{{ $item->total_naik }}</span></td>
                                    <td><span class="badge badge-danger">{{ $item->total_tidak_naik }}</span></td>
                                    <td>
                                        <a href="{{ route('kenaikan-kelas.show', $item->id) }}" class="btn btn-xs btn-outline-info">Detail</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Belum ada riwayat proses.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#tahun_asal_id').on('change', function() {
        const tahunId = $(this).val();
        const $kelasSelect = $('#kelas_asal_id');
        const $loader = $('#classLoader');

        if (!tahunId) {
            $kelasSelect.html('<option value="">Pilih Tahun Asal Dulu...</option>').prop('disabled', true);
            return;
        }

        $loader.show();
        $kelasSelect.prop('disabled', true).html('<option value="">Sedang memuat...</option>');

        $.ajax({
            url: "{{ route('kenaikan-kelas.get-classes') }}",
            data: { tahun_akademik_id: tahunId },
            success: function(data) {
                let options = '<option value="">Pilih Kelas...</option>';
                data.forEach(function(cls) {
                    const label = cls.is_processed ? ' (Sudah Diproses)' : '';
                    const style = cls.is_processed ? 'style="color: #999;"' : '';
                    options += `<option value="${cls.id}" ${style}>${cls.nama} (${cls.jurusan})${label}</option>`;
                });
                $kelasSelect.html(options).prop('disabled', false);
            },
            error: function() {
                alert('Gagal mengambil data kelas. Silakan coba lagi.');
                $kelasSelect.html('<option value="">Gagal memuat data</option>');
            },
            complete: function() {
                $loader.hide();
            }
        });
    });
});
</script>
@endpush

