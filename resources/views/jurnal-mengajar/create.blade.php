@extends('layouts.app')

@section('title', 'Isi Jurnal Mengajar')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>Isi Jurnal Mengajar</h1>
            <div class="separator mb-5"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-8 offset-md-2">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('jurnal-mengajar.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label>Pilih Jadwal Mengajar</label>
                            <select name="jadwal_pelajaran_id" id="jadwal_pelajaran_id" class="form-control select2-single" required onchange="window.location.href='{{ route('jurnal-mengajar.create') }}?jadwal_pelajaran_id=' + this.value">
                                <option value="">Pilih Jadwal...</option>
                                @foreach($schedules as $schedule)
                                    <option value="{{ $schedule->id }}" {{ request('jadwal_pelajaran_id') == $schedule->id ? 'selected' : '' }}>
                                        {{ $schedule->hari }} | {{ $schedule->mataPelajaranKelas->mataPelajaran->nama }} ({{ $schedule->mataPelajaranKelas->kelas->nama }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if($selectedSchedule)
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tanggal</label>
                                    <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Jam Mulai</label>
                                    <input type="time" name="jam_mulai" class="form-control" value="{{ $selectedSchedule->jam_mulai->format('H:i') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Jam Selesai</label>
                                    <input type="time" name="jam_selesai" class="form-control" value="{{ $selectedSchedule->jam_selesai->format('H:i') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Materi / Pokok Bahasan</label>
                            <textarea name="materi" class="form-control" rows="3" required placeholder="Contoh: Pengenalan Aljabar Linear..."></textarea>
                        </div>

                        <div class="form-group">
                            <label>Metode Pembelajaran</label>
                            <input type="text" name="metode_pembelajaran" class="form-control" placeholder="Contoh: Ceramah, Diskusi Kelompok...">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Hambatan</label>
                                    <textarea name="hambatan" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Solusi</label>
                                    <textarea name="solusi" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- attendance section -->
                        <div class="card bg-light mb-4 mt-4">
                            <div class="card-header bg-dark text-white d-flex justify-content-between">
                                <h6 class="mb-0 py-1">Presensi Siswa ({{ $selectedSchedule->mataPelajaranKelas->kelas->nama }})</h6>
                                <div class="text-right">
                                    <small>Set Semua: </small>
                                    <button type="button" class="btn btn-xs btn-outline-light" onclick="setAll('H')">Hadir</button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th width="5%">No</th>
                                                <th>Nama Siswa</th>
                                                <th class="text-center" width="25%">Status</th>
                                                <th width="30%">Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($students as $sk)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $sk->siswa->nama_lengkap }}</td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm btn-group-toggle" data-toggle="buttons">
                                                        <label class="btn btn-outline-success active" title="Hadir">
                                                            <input type="radio" name="presensi[{{ $sk->siswa->id }}]" value="H" checked> H
                                                        </label>
                                                        <label class="btn btn-outline-info" title="Izin">
                                                            <input type="radio" name="presensi[{{ $sk->siswa->id }}]" value="I"> I
                                                        </label>
                                                        <label class="btn btn-outline-warning" title="Sakit">
                                                            <input type="radio" name="presensi[{{ $sk->siswa->id }}]" value="S"> S
                                                        </label>
                                                        <label class="btn btn-outline-danger" title="Alpha">
                                                            <input type="radio" name="presensi[{{ $sk->siswa->id }}]" value="A"> A
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text" name="keterangan[{{ $sk->siswa->id }}]" class="form-control form-control-sm" placeholder="...">
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Catatan Tambahan</label>
                            <textarea name="catatan" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="text-right">
                            <a href="{{ route('jurnal-mengajar.index') }}" class="btn btn-outline-secondary px-4">BATAL</a>
                            <button type="submit" class="btn btn-primary px-4 shadow">SIMPAN JURNAL & ABSENSI</button>
                        </div>
                        @else
                        <div class="text-center py-5">
                            <i class="simple-icon-calendar fa-4x text-muted mb-3 d-block"></i>
                            <p class="text-muted">Silakan pilih jadwal mengajar terlebih dahulu untuk memunculkan daftar absen siswa.</p>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function setAll(val) {
        document.querySelectorAll('input[type="radio"][value="' + val + '"]').forEach(el => {
            el.click();
        });
    }
</script>
@endpush
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
