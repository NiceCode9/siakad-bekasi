@extends('layouts.app')

@section('title', 'Input Nilai - ' . $mpk->mataPelajaran->nama)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Input Nilai: Semua Komponen</h4>
            <div class="text-muted">
                {{ $mpk->mataPelajaran->nama }} | {{ $kelas->nama }}
                @if($mpk->kkm)
                    <span class="badge badge-success ml-2">KKM: {{ $mpk->kkm }}</span>
                @endif
            </div>
        </div>
        <a href="{{ route('nilai.index', ['kelas_id' => $kelas->id]) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if(isset($cbtSchedules) && $cbtSchedules->isNotEmpty())
        <div class="alert alert-warning shadow-sm border-left-warning mb-3">
            <div class="d-flex align-items-center">
                <div class="mr-3">
                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                </div>
                <div>
                    <h6 class="alert-heading mb-1 font-weight-bold">Peringatan: Terintegrasi dengan CBT</h6>
                    <span>Beberapa komponen nilai sudah terhubung dengan jadwal ujian CBT. Nilai siswa akan otomatis terisi saat mereka menyelesaikan ujian tersebut. Kolom dengan tanda <i class="fas fa-desktop text-warning"></i> CBT tidak bisa diubah secara manual.</span>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('nilai.store') }}" method="POST" id="formNilai">
        @csrf
        <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
        <input type="hidden" name="mata_pelajaran_kelas_id" value="{{ $mpk->id }}">
        <input type="hidden" name="semester_id" value="{{ $semester->id }}">

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%" class="text-center align-middle">No</th>
                                <th width="20%" class="align-middle">Nama Siswa</th>
                                @foreach($komponen as $comp)
                                    <th class="text-center align-middle" style="min-width: 150px;">
                                        {{ $comp->nama }}
                                        <br>
                                        <small class="text-muted">{{ $comp->bobot }}%</small>
                                        @if(isset($cbtSchedules[$comp->id]))
                                            <i class="fas fa-desktop text-warning ml-1" title="Terintegrasi CBT"></i>
                                        @endif
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswa as $sk)
                                <tr>
                                    <td class="text-center active-row align-middle">{{ $loop->iteration }}</td>
                                    <td class="align-middle">
                                        <strong>{{ $sk->siswa->nama_lengkap }}</strong><br>
                                        <small class="text-muted">{{ $sk->siswa->nis }}</small>
                                    </td>
                                    @foreach($komponen as $comp)
                                        @php
                                            $val = $existing->has($sk->siswa->id) ? $existing[$sk->siswa->id]->firstWhere('komponen_nilai_id', $comp->id) : null;
                                            $nilai = $val ? $val->nilai : '';
                                            $ket = $val ? $val->keterangan : '';
                                            $isCbt = isset($cbtSchedules[$comp->id]);
                                        @endphp
                                        <td>
                                            <input type="number" name="nilai[{{ $sk->siswa->id }}][{{ $comp->id }}][angka]"
                                                   class="form-control text-center input-nilai"
                                                   value="{{ $nilai }}"
                                                   min="0" max="100" step="0.01"
                                                   placeholder="0"
                                                   {{ $isCbt ? 'readonly title=CBT' : '' }}>
                                            <input type="text" name="nilai[{{ $sk->siswa->id }}][{{ $comp->id }}][keterangan]"
                                                   class="form-control text-center mt-1 form-control-sm"
                                                   value="{{ $ket }}"
                                                   placeholder="Catatan..."
                                                   {{ $isCbt ? 'readonly' : '' }}>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white text-right">
                <button type="submit" class="btn btn-primary px-5">
                    <i class="fas fa-save"></i> Simpan Nilai
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const kkm = {{ $mpk->kkm ?? 0 }};

        function checkKkm(input) {
            const val = parseFloat(input.val());
            if (!isNaN(val) && val < kkm) {
                input.addClass('is-invalid text-danger font-weight-bold');
            } else {
                input.removeClass('is-invalid text-danger font-weight-bold');
            }
        }

        // Initial check
        $('input[type="number"]').each(function() {
            checkKkm($(this));
        });

        // Live check
        $('input[type="number"]').on('input', function() {
            checkKkm($(this));
        });

        // Highlight active row on focus
        $('input').focus(function() {
            $(this).closest('tr').addClass('table-primary');
        }).blur(function() {
            $(this).closest('tr').removeClass('table-primary');
        });
    });
</script>
@endpush
