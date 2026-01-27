@extends('layouts.app')

@section('title', 'Input Nilai - ' . $mpk->mataPelajaran->nama)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Input Nilai: {{ $komponen->nama }}</h4>
            <div class="text-muted">
                {{ $mpk->mataPelajaran->nama }} | {{ $kelas->nama }}
                <span class="badge badge-info ml-2">{{ ucfirst($komponen->kategori) }} (Bobot: {{ $komponen->bobot }}%)</span>
            </div>
        </div>
        <a href="{{ route('nilai.index', ['kelas_id' => $kelas->id]) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="{{ route('nilai.store') }}" method="POST" id="formNilai">
        @csrf
        <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
        <input type="hidden" name="mata_pelajaran_kelas_id" value="{{ $mpk->id }}">
        <input type="hidden" name="komponen_nilai_id" value="{{ $komponen->id }}">
        <input type="hidden" name="semester_id" value="{{ $semester->id }}">

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="35%">Nama Siswa</th>
                                <th width="20%">Nilai (0-100)</th>
                                <th width="40%">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswa as $sk)
                                @php
                                    $val = $existing[$sk->siswa->id] ?? null;
                                    $nilai = $val ? $val->nilai : '';
                                    $ket = $val ? $val->keterangan : '';
                                @endphp
                                <tr>
                                    <td class="text-center active-row">{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $sk->siswa->nama_lengkap }}</strong><br>
                                        <small class="text-muted">{{ $sk->siswa->nis }}</small>
                                    </td>
                                    <td>
                                        <input type="number" name="nilai[{{ $sk->siswa->id }}][angka]" 
                                               class="form-control" 
                                               value="{{ $nilai }}" 
                                               min="0" max="100" step="0.01" 
                                               placeholder="0">
                                    </td>
                                    <td>
                                        <input type="text" name="nilai[{{ $sk->siswa->id }}][keterangan]" 
                                               class="form-control" 
                                               value="{{ $ket }}" 
                                               placeholder="Catatan...">
                                    </td>
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
        // Highlight active row on focus
        $('input').focus(function() {
            $(this).closest('tr').addClass('table-primary');
        }).blur(function() {
            $(this).closest('tr').removeClass('table-primary');
        });
    });
</script>
@endpush
