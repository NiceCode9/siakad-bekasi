@extends('layouts.app')

@section('title', 'Simulasi Kenaikan Kelas')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>Simulasi Kenaikan/Kelulusan: {{ $kelasAsal->nama }}</h1>
            <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                <ol class="breadcrumb pt-0">
                    <li class="breadcrumb-item"><a href="{{ route('kenaikan-kelas.index') }}">Kenaikan Kelas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Simulasi</li>
                </ol>
            </nav>
            <div class="separator mb-5"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-3 mb-4">
            <div class="card h-100 border-left-success shadow-sm">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <p class="text-muted mb-1">Total Siswa</p>
                    <p class="lead font-weight-bold text-primary mb-0" id="total-siswa">{{ count($students) }}</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3 mb-4">
            <div class="card h-100 border-left-info shadow-sm">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <p class="text-muted mb-1">Rekomendasi Naik</p>
                    <p class="lead font-weight-bold text-success mb-0" id="total-naik">-</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3 mb-4">
            <div class="card h-100 border-left-warning shadow-sm">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <p class="text-muted mb-1">Perlu Tinjauan</p>
                    <p class="lead font-weight-bold text-danger mb-0" id="total-tinjau">-</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3 mb-4">
            <div class="card h-100 shadow-sm bg-gradient-light">
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="mb-0 font-weight-bold">KKM Dinamis</label>
                        <span class="badge badge-primary px-3 py-1" id="kkm-val">{{ $kkmDefault }}</span>
                    </div>
                    <input type="range" class="custom-range" id="kkm-slider" min="50" max="95" step="1" value="{{ $kkmDefault }}">
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body bg-light rounded d-flex flex-wrap justify-content-between align-items-center">
                    <div class="mb-2 mb-md-0">
                        <span class="text-muted mr-3"><i class="simple-icon-settings"></i> Aksi Massal:</span>
                        <button type="button" class="btn btn-outline-info btn-sm mr-2" id="apply-recom">
                             <i class="simple-icon-check"></i> Terapkan Semua Rekomendasi
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm" id="apply-all-promoted">
                             <i class="simple-icon-layers"></i> Semua Naik Kelas
                        </button>
                    </div>
                    <div class="search-sm d-inline-block float-md-right mr-1 mb-1 align-top">
                        <input id="searchSiswa" placeholder="Cari Siswa...">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('kenaikan-kelas.eksekusi') }}" method="POST">
        @csrf
        <input type="hidden" name="kelas_asal_id" value="{{ $kelasAsal->id }}">
        <input type="hidden" name="tahun_akademik_id" value="{{ $tahunAkademikTarget->id }}">

        <div class="row">
            <div class="col-12">
                <div class="card mb-4 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="siswaTable" data-source-class-name="{{ $kelasAsal->nama }}">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3 px-4" width="50">No</th>
                                        <th class="py-3">SISWA</th>
                                        <th class="py-3 text-center">NILAI</th>
                                        <th class="py-3 text-center">ABSENSI</th>
                                        <th class="py-3 text-center">REKOMENDASI</th>
                                        <th class="py-3 text-center" width="180">STATUS AKHIR</th>
                                        <th class="py-3 text-center" width="220">KELAS TUJUAN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $idx => $siswa)
                                        @php
                                            $raport = $siswa->raports->first();
                                            $avg = $raport ? $raport->average_score : 0;
                                            $alpha = $raport ? ($raport->jumlah_alpha ?? 0) : 0;
                                            $absensi = $raport ? (($raport->jumlah_sakit ?? 0) + ($raport->jumlah_izin ?? 0) + $alpha) : 0;
                                            
                                            $isPromotedClass = ($kelasAsal->tingkat != 'XII');
                                        @endphp
                                        <tr class="siswa-row" 
                                            data-avg="{{ $avg }}" 
                                            data-alpha="{{ $alpha }}"
                                            data-id="{{ $siswa->id }}"
                                            data-tingkat-original="{{ $kelasAsal->tingkat }}">
                                            <td class="text-center align-middle">{{ $idx + 1 }}</td>
                                            <td class="align-middle">
                                                <strong>{{ $siswa->nama_lengkap }}</strong><br>
                                                <small class="text-muted">{{ $siswa->nis }}</small>
                                                <input type="hidden" name="students[{{ $idx }}][id]" value="{{ $siswa->id }}">
                                            </td>
                                            <td class="text-center align-middle font-weight-bold">{{ round($avg, 2) }}</td>
                                            <td class="text-center align-middle">
                                                <span class="text-{{ $alpha > 3 ? 'danger' : 'muted' }}">{{ $absensi }}</span>
                                                @if($alpha > 0)
                                                    <br><small class="text-danger">Alpha: {{ $alpha }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle recom-badge-cell">
                                                <!-- Dynamic via JS -->
                                            </td>
                                            <td class="align-middle">
                                                <select name="students[{{ $idx }}][status]" class="form-control form-control-sm status-select select2-no-search">
                                                    @if(!$isPromotedClass)
                                                        <option value="lulus">Lulus</option>
                                                        <option value="mengulang">Mengulang</option>
                                                    @else
                                                        <option value="naik">Naik Kelas</option>
                                                        <option value="tidak_naik">Tidak Naik</option>
                                                        <option value="mengulang">Mengulang</option>
                                                    @endif
                                                </select>
                                            </td>
                                            <td class="align-middle">
                                                <select name="students[{{ $idx }}][kelas_tujuan_id]" class="form-control form-control-sm target-class-select select2-no-search">
                                                    <option value="">-- Keluar / Alumni --</option>
                                                    @foreach($targetClasses as $tc)
                                                        <option value="{{ $tc->id }}" data-tingkat="{{ $tc->tingkat }}">
                                                            {{ $tc->nama }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-end">
                            <div class="col-lg-8 mb-3 mb-lg-0">
                                <h5 class="mb-3">Konfirmasi Eksekusi</h5>
                                <textarea name="keterangan" class="form-control" rows="2" placeholder="Tuliskan catatan hasil rapat dewan guru..."></textarea>
                            </div>
                            <div class="col-lg-4 text-right">
                                <a href="{{ route('kenaikan-kelas.index') }}" class="btn btn-outline-secondary mr-2">BATAL</a>
                                <button type="submit" class="btn btn-primary btn-lg px-5 shadow" onclick="return confirm('Apakah Anda yakin data sudah benar?')">EKSEKUSI SEKARANG</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const $kkmSlider = $('#kkm-slider');
    const $kkmValue = $('#kkm-val');
    const isXII = "{{ $kelasAsal->tingkat == 'XII' }}";
    const sourceClassId = "{{ $kelasAsal->id }}";
    
    function updateRecommendations() {
        const kkm = parseInt($kkmSlider.val());
        $kkmValue.text(kkm);
        
        let countNaik = 0;
        let countTinjau = 0;
        
        $('.siswa-row').each(function() {
            const row = $(this);
            const avg = parseFloat(row.data('avg'));
            const alpha = parseInt(row.data('alpha'));
            
            // Logic: Nilai >= KKM AND Alpha <= 3
            const isEligible = (avg >= kkm && alpha <= 3);
            const badgeCell = row.find('.recom-badge-cell');
            
            if (isEligible) {
                badgeCell.html('<span class="badge badge-pill badge-outline-success px-3">NAIK</span>');
                countNaik++;
                row.removeClass('table-warning text-muted');
            } else {
                badgeCell.html('<span class="badge badge-pill badge-outline-danger px-3">Tinjau</span>');
                countTinjau++;
                row.addClass('table-warning text-muted');
            }
            
            // Store eligibility for bulk action
            row.data('eligible', isEligible);
        });
        
        $('#total-naik').text(countNaik);
        $('#total-tinjau').text(countTinjau);
    }
    
    // Initial run
    updateRecommendations();
    
    $kkmSlider.on('input', updateRecommendations);
    
    // Apply Recommendations
    $('#apply-recom').on('click', function() {
        if (!confirm('Terapkan status otomatis berdasarkan Nilai KKM & Absensi?')) return;
        
        $('.siswa-row').each(function() {
            const row = $(this);
            const isEligible = row.data('eligible');
            const statusSelect = row.find('.status-select');
            const targetSelect = row.find('.target-class-select');
            const currentTingkat = row.data('tingkat-original');
            
            if (isEligible) {
                statusSelect.val(isXII ? 'lulus' : 'naik').trigger('change');
            } else {
                statusSelect.val(isXII ? 'mengulang' : 'tidak_naik').trigger('change');
            }
        });
    });
    
    // Multi Selection: All Promoted
    $('#apply-all-promoted').on('click', function() {
        if (!confirm('Mark all as PROMOTED?')) return;
        $('.status-select').val(isXII ? 'lulus' : 'naik').trigger('change');
    });
    
    // Status Change Logic
    $('.status-select').on('change', function() {
        const row = $(this).closest('.siswa-row');
        const status = $(this).val();
        const targetSelect = row.find('.target-class-select');
        const tingkatSekarang = row.data('tingkat-original');
        const sourceClassName = $('#siswaTable').data('source-class-name');
        
        // Roman to Level
        const romanLevelMap = {'X': 10, 'XI': 11, 'XII' : 12};
        const levelNum = romanLevelMap[tingkatSekarang];
        
        if (status === 'naik' || status === 'lulus') {
            // Pick first class with next level
            const nextLevel = levelNum + 1;
            targetSelect.find('option').each(function() {
                const targetLevel = romanLevelMap[$(this).data('tingkat')];
                if (targetLevel === nextLevel) {
                    targetSelect.val($(this).val());
                    return false;
                }
            });
        } else if (status === 'tidak_naik' || status === 'mengulang') {
            // Find class in target year with SAME NAME as source class
            let foundByName = false;
            targetSelect.find('option').each(function() {
                if ($(this).text().trim() === sourceClassName) {
                    targetSelect.val($(this).val());
                    foundByName = true;
                    return false;
                }
            });

            // Fallback: Pick first class with SAME level
            if (!foundByName) {
                targetSelect.find('option').each(function() {
                    const targetLevel = romanLevelMap[$(this).data('tingkat')];
                    if (targetLevel === levelNum) {
                        targetSelect.val($(this).val());
                        return false;
                    }
                });
            }
        }
    });

    // Search function
    $("#searchSiswa").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#siswaTable tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
</script>
@endpush
