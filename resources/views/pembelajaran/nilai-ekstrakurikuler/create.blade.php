@extends('layouts.app')

@section('title', 'Input Nilai Ekstrakurikuler')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Input Nilai Ekstrakurikuler</h4>
            <div class="text-muted">{{ $kelas->nama }}</div>
        </div>
        <a href="{{ route('nilai-ekstrakurikuler.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="{{ route('nilai-ekstrakurikuler.store') }}" method="POST">
        @csrf
        <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
        <input type="hidden" name="semester_id" value="{{ $semester->id }}">

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="30%">Nama Siswa</th>
                                <th width="25%">Nama Ekskul</th>
                                <th width="15%">Predikat</th>
                                <th width="25%">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswa as $sk)
                                @php
                                    // Get first record found for this semester
                                    $val = $existing[$sk->siswa->id][0] ?? null;
                                    $ekskulId = $val ? $val->ekstrakurikuler_id : '';
                                    $pred = $val ? $val->predikat : '';
                                    $ket = $val ? $val->keterangan : '';
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $sk->siswa->nama_lengkap }}</strong><br>
                                        <small class="text-muted">{{ $sk->siswa->nis }}</small>
                                    </td>
                                    <td>
                                        <select name="nilai[{{ $sk->siswa->id }}][ekstrakurikuler_id]" class="form-control select2">
                                            <option value="">-- Pilih Ekskul --</option>
                                            @foreach($ekskulList as $e)
                                                <option value="{{ $e->id }}" {{ $ekskulId == $e->id ? 'selected' : '' }}>{{ $e->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="nilai[{{ $sk->siswa->id }}][predikat]" class="form-control">
                                            <option value="">--</option>
                                            <option value="A" {{ $pred == 'A' ? 'selected' : '' }}>A (Sangat Baik)</option>
                                            <option value="B" {{ $pred == 'B' ? 'selected' : '' }}>B (Baik)</option>
                                            <option value="C" {{ $pred == 'C' ? 'selected' : '' }}>C (Cukup)</option>
                                            <option value="D" {{ $pred == 'D' ? 'selected' : '' }}>D (Kurang)</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="nilai[{{ $sk->siswa->id }}][keterangan]" class="form-control" value="{{ $ket }}" placeholder="Keterangan...">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white text-right">
                <button type="submit" class="btn btn-primary px-5">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({ width: '100%' });
    });
</script>
@endpush
