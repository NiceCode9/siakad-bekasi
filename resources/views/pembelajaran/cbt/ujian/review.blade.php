@extends('layouts.app')

@section('title', 'Review Hasil Ujian')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Review: {{ $ujianSiswa->siswa->nama_lengkap }}</h4>
            <span class="text-muted">{{ $jadwal->nama_ujian }} | Skor: <strong class="text-primary">{{ number_format($ujianSiswa->nilai, 2) }}</strong></span>
        </div>
        <a href="{{ route('jadwal-ujian.monitor', $jadwal->id) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali ke Monitor
        </a>
    </div>

    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-md-3">
            <div class="card shadow-sm sticky-top" style="top: 100px; z-index: 10;">
                <div class="card-body p-3">
                    <div class="card-title bg-white font-weight-bold small mb-2">Navigasi Soal</div>
                    <div class="q-nav-grid mb-3">
                        @foreach($soalList as $index => $su)
                            @php 
                                $j = $jawabans->get($su->id);
                                $statusClass = 'btn-outline-secondary';
                                if($j) {
                                    $statusClass = $j->is_benar ? 'btn-success' : 'btn-danger';
                                }
                            @endphp
                            <button class="btn btn-sm {{ $statusClass }} q-nav-btn q-nav-btn-compact" 
                                data-index="{{ $index }}" 
                                title="Soal {{ $index + 1 }}">
                                {{ $index + 1 }}
                            </button>
                        @endforeach
                    </div>
                    
                    <hr>
                    <div class="small">
                        <div class="mb-1 d-flex align-items-center">
                            <span class="badge badge-success mr-2" style="width: 12px; height: 12px; padding: 0;">&nbsp;</span> Benar
                        </div>
                        <div class="mb-1 d-flex align-items-center">
                            <span class="badge badge-danger mr-2" style="width: 12px; height: 12px; padding: 0;">&nbsp;</span> Salah
                        </div>
                        <div class="mb-1 d-flex align-items-center">
                            <span class="badge badge-secondary mr-2" style="width: 12px; height: 12px; padding: 0;">&nbsp;</span> Kosong
                        </div>
                    </div>

                    <hr>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold">Filter:</label>
                        <select class="form-control form-control-sm" id="filterReview">
                            <option value="all">Semua Soal</option>
                            <option value="salah">Hanya Salah</option>
                            <option value="benar">Hanya Benar</option>
                            <option value="kosong">Belum Dijawab</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card mt-3 shadow-sm sticky-top" style="top: 450px;">
                <div class="card-header bg-white font-weight-bold small">Informasi Peserta</div>
                <div class="card-body p-3">
                    <table class="table table-sm table-borderless mb-0 small">
                        <tr><td class="text-muted">NIS:</td><td class="font-weight-bold">{{ $ujianSiswa->siswa->nis }}</td></tr>
                        <tr><td class="text-muted">Mulai:</td><td>{{ $ujianSiswa->waktu_mulai ? $ujianSiswa->waktu_mulai->format('H:i') : '-' }}</td></tr>
                        <tr><td class="text-muted">Submit:</td><td>{{ $ujianSiswa->waktu_submit ? $ujianSiswa->waktu_submit->format('H:i') : '-' }}</td></tr>
                        <tr><td class="text-muted">Pelanggaran:</td><td><span class="badge badge-{{ $ujianSiswa->violation_count > 0 ? 'danger' : 'success' }}">{{ $ujianSiswa->violation_count }}</span></td></tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Question Display -->
        <div class="col-md-9" id="questions-container">
            @foreach($soalList as $index => $su)
                @php 
                    $j = $jawabans->get($su->id);
                    $isBenar = $j ? $j->is_benar : false;
                    $scoreValue = $j ? $j->nilai : 0;
                    $statusType = 'kosong';
                    if($j) $statusType = $isBenar ? 'benar' : 'salah';
                @endphp
                <div class="card mb-4 shadow-sm question-review-card border-{{ $j ? ($isBenar ? 'success' : 'danger') : 'secondary' }}" 
                     id="q-card-{{ $index }}"
                     data-status="{{ $statusType }}">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <span class="font-weight-bold">Soal No. {{ $index + 1 }}</span>
                        @if($j)
                            <span class="badge badge-{{ $isBenar ? 'success' : 'danger' }}">
                                {{ $isBenar ? 'Benar' : 'Salah' }} (Skor: {{ number_format($scoreValue, 2) }})
                            </span>
                        @else
                            <span class="badge badge-secondary">Tidak Dijawab</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            {!! $su->soal->pertanyaan !!}
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="p-3 rounded bg-{{ $isBenar ? 'success' : 'danger' }}-light border border-{{ $isBenar ? 'success' : 'danger' }}">
                                    <p class="mb-1 small text-muted">Jawaban Siswa:</p>
                                    <div class="font-weight-bold">
                                        {{ $j ? $j->jawaban : '(Kosong)' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 rounded bg-primary-light border border-primary">
                                    <p class="mb-1 small text-muted">Kunci Jawaban:</p>
                                    <div class="font-weight-bold text-primary">
                                        {{ $su->soal->kunci_jawaban }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($su->soal->pembahasan)
                            <div class="mt-3 p-3 bg-light rounded small">
                                <p class="mb-1 font-weight-bold"><i class="fas fa-info-circle"></i> Pembahasan:</p>
                                {!! $su->soal->pembahasan !!}
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    .bg-success-light { background-color: #d4edda; }
    .bg-danger-light { background-color: #f8d7da; }
    .bg-primary-light { background-color: #cfe2ff; }
    .bg-warning-light { background-color: #fff3cd; }
    .sticky-top { top: 80px; }
    
    .q-nav-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(35px, 1fr));
        gap: 6px;
    }
    .q-nav-btn-compact {
        width: 35px;
        height: 35px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        border-radius: 4px;
        font-weight: bold;
    }
</style>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Scroll to question
    $('.q-nav-btn').click(function() {
        let index = $(this).data('index');
        let card = $('#q-card-' + index);
        
        $('html, body').animate({
            scrollTop: card.offset().top - 100
        }, 500);
        
        // Highlight card briefly
        card.addClass('bg-warning-light');
        setTimeout(() => card.removeClass('bg-warning-light'), 2000);
    });

    // Filter logic
    $('#filterReview').change(function() {
        let val = $(this).val();
        if(val === 'all') {
            $('.question-review-card').fadeIn();
        } else {
            $('.question-review-card').hide();
            $(`.question-review-card[data-status="${val}"]`).fadeIn();
        }
    });

    // Handle initial state if filtered? No, all is fine.
});
</script>
@endpush
