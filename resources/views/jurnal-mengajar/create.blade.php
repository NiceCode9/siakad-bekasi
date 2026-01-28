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
                            <select name="jadwal_pelajaran_id" class="form-control select2-single" required>
                                <option value="">Pilih Jadwal...</option>
                                @foreach($schedules as $schedule)
                                    <option value="{{ $schedule->id }}">
                                        {{ $schedule->hari }} | {{ $schedule->mataPelajaranKelas->mataPelajaran->nama }} ({{ $schedule->mataPelajaranKelas->kelas->nama }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

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
                                    <input type="time" name="jam_mulai" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Jam Selesai</label>
                                    <input type="time" name="jam_selesai" class="form-control" required>
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

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jumlah Siswa Hadir</label>
                                    <input type="number" name="jumlah_hadir" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jumlah Siswa Tidak Hadir</label>
                                    <input type="number" name="jumlah_tidak_hadir" class="form-control" value="0">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Catatan Tambahan</label>
                            <textarea name="catatan" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="text-right">
                            <a href="{{ route('jurnal-mengajar.index') }}" class="btn btn-outline-secondary">BATAL</a>
                            <button type="submit" class="btn btn-primary">SIMPAN JURNAL</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
