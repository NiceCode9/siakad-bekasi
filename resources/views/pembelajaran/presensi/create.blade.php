@extends('layouts.app')

@section('title', 'Input Presensi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Input Presensi</h4>
            <p class="mb-0 text-muted">Kelas: {{ $kelas->nama }} | Tanggal: {{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}</p>
        </div>
        <a href="{{ route('presensi.index', ['kelas_id' => $kelas->id, 'tanggal' => $tanggal]) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="{{ route('presensi.store') }}" method="POST">
        @csrf
        <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
        <input type="hidden" name="tanggal" value="{{ $tanggal }}">

        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                 <div class="d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Siswa</h6>
                    <div>
                        <button type="button" class="btn btn-outline-success btn-sm mark-all" data-status="H">Semua Hadir</button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="30%">Nama Siswa</th>
                                <th width="40%" class="text-center">Status Kehadiran</th>
                                <th width="25%">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswa as $sk)
                                @php
                                    $current = $existing[$sk->siswa->id] ?? null;
                                    $status = $current ? $current->status : 'H'; // Default Hadir
                                    $ket = $current ? $current->keterangan : '';
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $sk->siswa->nama_lengkap }}</strong><br>
                                        <small class="text-muted">{{ $sk->siswa->nis }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                            <label class="btn btn-outline-success btn-sm {{ $status == 'H' ? 'active' : '' }}">
                                                <input type="radio" name="presensi[{{ $sk->siswa->id }}][status]" value="H" {{ $status == 'H' ? 'checked' : '' }}> Hadir
                                            </label>
                                            <label class="btn btn-outline-info btn-sm {{ $status == 'I' ? 'active' : '' }}">
                                                <input type="radio" name="presensi[{{ $sk->siswa->id }}][status]" value="I" {{ $status == 'I' ? 'checked' : '' }}> Izin
                                            </label>
                                            <label class="btn btn-outline-warning btn-sm {{ $status == 'S' ? 'active' : '' }}">
                                                <input type="radio" name="presensi[{{ $sk->siswa->id }}][status]" value="S" {{ $status == 'S' ? 'checked' : '' }}> Sakit
                                            </label>
                                            <label class="btn btn-outline-danger btn-sm {{ $status == 'A' ? 'active' : '' }}">
                                                <input type="radio" name="presensi[{{ $sk->siswa->id }}][status]" value="A" {{ $status == 'A' ? 'checked' : '' }}> Alpha
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="presensi[{{ $sk->siswa->id }}][keterangan]" class="form-control form-control-sm" placeholder="Keterangan..." value="{{ $ket }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white text-right">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="fas fa-save"></i> Simpan Presensi
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.mark-all').click(function() {
            var status = $(this).data('status');
            $('input[type="radio"][value="' + status + '"]').prop('checked', true).trigger('change');
            
            // Visual update for bootstrap toggle
             $('label.btn').removeClass('active');
             $('input[type="radio"][value="' + status + '"]').parent('label').addClass('active');
        });
    });
</script>
@endpush
