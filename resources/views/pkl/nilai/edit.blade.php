@extends('layouts.app')

@section('title', 'Input Nilai PKL')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Input Nilai PKL: {{ $pkl->siswa->nama }}</h5>
                    <small class="text-muted">{{ $pkl->perusahaanPkl->nama }}</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('pkl-nilai.update', $pkl->id) }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group text-center">
                                    <label>Sikap Kerja (Ind)</label>
                                    <input type="number" step="0.01" name="nilai_sikap_kerja" id="sikap" class="form-control calc-trigger text-center h4" value="{{ old('nilai_sikap_kerja', $nilai->nilai_sikap_kerja) }}" required min="0" max="100">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group text-center">
                                    <label>Keterampilan (Ind)</label>
                                    <input type="number" step="0.01" name="nilai_keterampilan" id="keterampilan" class="form-control calc-trigger text-center h4" value="{{ old('nilai_keterampilan', $nilai->nilai_keterampilan) }}" required min="0" max="100">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group text-center">
                                    <label>Pelaporan (Sek)</label>
                                    <input type="number" step="0.01" name="nilai_laporan" id="laporan" class="form-control calc-trigger text-center h4" value="{{ old('nilai_laporan', $nilai->nilai_laporan) }}" required min="0" max="100">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group text-center">
                                    <label>Nilai Sekolah</label>
                                    <input type="number" step="0.01" name="nilai_dari_sekolah" id="sekolah" class="form-control calc-trigger text-center h4" value="{{ old('nilai_dari_sekolah', $nilai->nilai_dari_sekolah) }}" required min="0" max="100">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3 bg-light p-3 rounded">
                            <div class="col-md-4 text-center">
                                <h6>Rata-rata Industri (40%)</h6>
                                <h3 id="avg-industri">0</h3>
                            </div>
                            <div class="col-md-4 text-center border-left border-right">
                                <h6>Rata-rata Sekolah (60%)</h6>
                                <h3 id="avg-sekolah">0</h3>
                            </div>
                            <div class="col-md-4 text-center">
                                <h6>Nilai Akhir</h6>
                                <h2 class="font-weight-bold text-primary" id="final-score">0</h2>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Catatan Industri</label>
                                    <textarea name="catatan_industri" class="form-control" rows="3">{{ old('catatan_industri', $nilai->catatan_industri) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Catatan Sekolah</label>
                                    <textarea name="catatan_sekolah" class="form-control" rows="3">{{ old('catatan_sekolah', $nilai->catatan_sekolah) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Tanggal Penilaian</label>
                            <input type="date" name="tanggal_penilaian" class="form-control" value="{{ old('tanggal_penilaian', $nilai->tanggal_penilaian ? $nilai->tanggal_penilaian->format('Y-m-d') : date('Y-m-d')) }}" required>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary px-4">Simpan Nilai</button>
                            <a href="{{ route('pkl-nilai.index') }}" class="btn btn-secondary">Batal</a>
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
    function calculate() {
        const sikap = parseFloat($('#sikap').val()) || 0;
        const keterampilan = parseFloat($('#keterampilan').val()) || 0;
        const laporan = parseFloat($('#laporan').val()) || 0;
        const sekolah = parseFloat($('#sekolah').val()) || 0;

        const avgInd = (sikap + keterampilan) / 2;
        const avgSek = (laporan + sekolah) / 2;
        const final = (avgInd * 0.4) + (avgSek * 0.6);

        $('#avg-industri').text(avgInd.toFixed(2));
        $('#avg-sekolah').text(avgSek.toFixed(2));
        $('#final-score').text(final.toFixed(2));
    }

    $(document).ready(function() {
        $('.calc-trigger').on('input', calculate);
        calculate(); // Initial run
    });
</script>
@endpush
