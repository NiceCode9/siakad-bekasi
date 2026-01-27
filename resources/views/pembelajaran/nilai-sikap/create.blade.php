@extends('layouts.app')

@section('title', 'Input Nilai Sikap - ' . ucfirst($aspek))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Input Nilai Sikap: {{ ucfirst($aspek) }}</h4>
            <div class="text-muted">{{ $kelas->nama }}</div>
        </div>
        <a href="{{ route('nilai-sikap.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="{{ route('nilai-sikap.store') }}" method="POST">
        @csrf
        <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
        <input type="hidden" name="semester_id" value="{{ $semester->id }}">
        <input type="hidden" name="aspek" value="{{ $aspek }}">

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="30%">Nama Siswa</th>
                                <th width="20%">Predikat</th>
                                <th width="45%">Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswa as $sk)
                                @php
                                    $val = $existing[$sk->siswa->id] ?? null;
                                    $pred = $val ? $val->predikat : '';
                                    $desc = $val ? $val->deskripsi : '';
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $sk->siswa->nama_lengkap }}</strong><br>
                                        <small class="text-muted">{{ $sk->siswa->nis }}</small>
                                    </td>
                                    <td>
                                        <select name="nilai[{{ $sk->siswa->id }}][predikat]" class="form-control">
                                            <option value="">-- Pilih --</option>
                                            <option value="SB" {{ $pred == 'SB' ? 'selected' : '' }}>Sangat Baik</option>
                                            <option value="B" {{ $pred == 'B' ? 'selected' : '' }}>Baik</option>
                                            <option value="C" {{ $pred == 'C' ? 'selected' : '' }}>Cukup</option>
                                            <option value="K" {{ $pred == 'K' ? 'selected' : '' }}>Kurang</option>
                                        </select>
                                    </td>
                                    <td>
                                        <textarea name="nilai[{{ $sk->siswa->id }}][deskripsi]" class="form-control" rows="1" placeholder="Deskripsi perilaku...">{{ $desc }}</textarea>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white text-right">
                <button type="submit" class="btn btn-primary px-5">
                    <i class="fas fa-save"></i> Simpan Nilai Sikap
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
