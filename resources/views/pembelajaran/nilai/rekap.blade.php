@extends('layouts.app')

@section('title', 'Rekap Nilai & Pasca Penilaian')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Rekap Nilai: {{ $mpk->mataPelajaran->nama }}</h4>
            <small class="text-muted">Kelas: {{ $kelas->nama }} | Semester: {{ $semesterAktif->nama }}</small>
        </div>
        <div>
            <a href="{{ route('nilai.index', ['kelas_id' => $kelas->id]) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" style="font-size: 0.85rem;">
                    <thead class="thead-light text-center">
                        <tr>
                            <th rowspan="2" class="align-middle" width="50">No</th>
                            <th rowspan="2" class="align-middle" style="min-width: 200px;">Nama Siswa</th>
                            <th colspan="{{ $components->count() }}">Komponen Nilai</th>
                            <th rowspan="2" class="align-middle bg-light" width="80">Nilai Akhir (Sistem)</th>
                            <th rowspan="2" class="align-middle bg-info text-white" width="100">Nilai Akhir (Manual)</th>
                            <th rowspan="2" class="align-middle" width="100">Aksi</th>
                        </tr>
                        <tr>
                            @foreach($components as $c)
                                <th width="70">{{ $c->nama }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siswa as $idx => $sk)
                            @php
                                $grades = $existingGrades->get($sk->siswa_id) ?? collect();
                                $sum = 0;
                                $count = 0;
                                foreach($components as $c) {
                                    $g = $grades->where('komponen_nilai_id', $c->id)->first();
                                    if($g) {
                                        $sum += $g->nilai;
                                        $count++;
                                    }
                                }
                                $avg = $count > 0 ? $sum / $count : 0;
                                $raportDetail = $raportDetails->get($sk->siswa_id);
                            @endphp
                            <tr>
                                <td class="text-center">{{ $idx + 1 }}</td>
                                <td>{{ $sk->siswa->nama_lengkap }}</td>
                                @foreach($components as $c)
                                    @php $g = $grades->where('komponen_nilai_id', $c->id)->first(); @endphp
                                    <td class="text-center {{ $g ? '' : 'text-muted' }}">
                                        {{ $g ? number_format($g->nilai, 0) : '-' }}
                                    </td>
                                @endforeach
                                <td class="text-center font-weight-bold bg-light">
                                    {{ number_format($avg, 1) }}
                                </td>
                                <td class="text-center font-weight-bold {{ $raportDetail && $raportDetail->is_manual_override ? 'text-danger' : 'text-info' }}">
                                    @if($raportDetail)
                                        {{ number_format($raportDetail->nilai_akhir, 1) }}
                                        @if($raportDetail->is_manual_override)
                                            <i class="fas fa-exclamation-circle small" title="Override: {{ $raportDetail->override_reason }}"></i>
                                        @endif
                                    @else
                                        <small class="text-muted italic">Belum generate raport</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($raportDetail)
                                        @if($isWali)
                                            <button type="button" class="btn btn-outline-info btn-xs" 
                                                onclick="openOverrideModal({{ $raportDetail->id }}, '{{ $sk->siswa->nama_lengkap }}', {{ $avg }}, {{ $raportDetail->nilai_akhir_manual ?? 'null' }}, '{{ $raportDetail->override_reason ?? '' }}')">
                                                <i class="fas fa-edit"></i> Override
                                            </button>
                                        @else
                                            <span class="badge badge-light text-muted">Akses Wali Kelas</span>
                                        @endif
                                    @else
                                        <span class="badge badge-light text-muted">Generate Raport Dulu</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Override Modal -->
<div class="modal fade" id="overrideModal" tabindex="-1" role="dialog" aria-labelledby="overrideModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="overrideModalLabel">Override Nilai Akhir</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="overrideForm">
                @csrf
                <input type="hidden" name="raport_detail_id" id="modal_detail_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Siswa</label>
                        <input type="text" id="modal_siswa_nama" class="form-control-plaintext font-weight-bold" readonly>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group text-center p-2 bg-light rounded">
                                <label class="small text-uppercase">Nilai Sistem</label>
                                <h3 id="modal_nilai_sistem">0</h3>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group text-center p-2 bg-info-light rounded">
                                <label class="small text-uppercase">Nilai Manual</label>
                                <input type="number" name="nilai_akhir_manual" id="modal_nilai_manual" class="form-control text-center font-weight-bold" step="0.01" min="0" max="100" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <label>Alasan Perubahan / Catatan</label>
                        <textarea name="override_reason" id="modal_reason" class="form-control" rows="3" placeholder="Contoh: Pembulatan ke atas, penyesuaian nilai keaktifan, dll" required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .btn-xs { padding: 0.1rem 0.4rem; font-size: 0.75rem; }
    .bg-info-light { background-color: rgba(23, 162, 184, 0.1); }
</style>
@endpush

@push('scripts')
<script>
function openOverrideModal(id, nama, sistem, manual, reason) {
    $('#modal_detail_id').val(id);
    $('#modal_siswa_nama').val(nama);
    $('#modal_nilai_sistem').text(sistem.toFixed(1));
    $('#modal_nilai_manual').val(manual || sistem.toFixed(1));
    $('#modal_reason').val(reason);
    $('#overrideModal').modal('show');
}

$('#overrideForm').on('submit', function(e) {
    e.preventDefault();
    const btn = $(this).find('button[type="submit"]');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

    $.ajax({
        url: "{{ route('nilai.override') }}",
        method: "POST",
        data: $(this).serialize(),
        success: function(res) {
            Swal.fire('Berhasil!', res.message, 'success').then(() => {
                location.reload();
            });
        },
        error: function(err) {
            btn.prop('disabled', false).text('Simpan Perubahan');
            Swal.fire('Gagal!', err.responseJSON.message || 'Terjadi kesalahan sistem.', 'error');
        }
    });
});
</script>
@endpush
