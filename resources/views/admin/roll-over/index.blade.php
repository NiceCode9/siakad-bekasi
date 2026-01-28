@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <h1>Salin Data Semester (Smart Roll-over)</h1>
            <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                <ol class="breadcrumb pt-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.pengaturan.index') }}">Pengaturan</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Roll-over</li>
                </ol>
            </nav>
            <div class="separator mb-5"></div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded mb-4" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-12 col-xl-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-4">Otomatisasi Setup Semester</h5>
                    <div class="alert alert-info rounded mb-4 shadow-sm border-0 d-flex align-items-center" style="background: #e7f3ff; color: #004085;">
                        <i class="simple-icon-info mr-3" style="font-size: 1.5rem;"></i>
                        <div>
                            Fitur ini menyalin konfigurasi dari semester lama ke semester baru. 
                            <strong>Ini tidak akan menyalin data siswa atau nilai</strong>, hanya struktur akademik 
                            untuk mempermudah persiapan semester baru.
                        </div>
                    </div>

                    <form action="{{ route('admin.roll-over.process') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Semester Asal (Sumber Data)</label>
                                    <select name="from_semester_id" class="form-control select2-single" required>
                                        <option value="">Pilih Semester Asal...</option>
                                        @foreach($semesters as $s)
                                            <option value="{{ $s->id }}">{{ $s->tahunAkademik->nama }} - {{ $s->nama }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted text-extra-small">Data dari semester ini akan diduplikasi.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Semester Tujuan</label>
                                    <select name="to_semester_id" class="form-control select2-single" required>
                                        <option value="">Pilih Semester Tujuan...</option>
                                        @foreach($semesters as $s)
                                            <option value="{{ $s->id }}">{{ $s->tahunAkademik->nama }} - {{ $s->nama }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted text-extra-small">Semester baru yang masih kosong/ingin diisi.</small>
                                </div>
                            </div>
                        </div>

                        <h6 class="mb-3 font-weight-bold text-muted uppercase">PILIH DATA UNTUK DISALIN</h6>
                        <div class="separator-light mb-4"></div>

                        <div class="row mb-5">
                            <div class="col-12 mb-4">
                                <div class="custom-control custom-checkbox mb-3">
                                    <input type="checkbox" class="custom-control-input" id="copy_kelas" name="copy_kelas" value="1" checked>
                                    <label class="custom-control-label font-weight-bold" for="copy_kelas">Salin Struktur Kelas</label>
                                    <p class="text-muted text-small ml-0">Menyalin seluruh daftar kelas (Tingkat, Jurusan, Nama & Kode Kelas, Wali Kelas).</p>
                                </div>

                                <div class="custom-control custom-checkbox mb-3">
                                    <input type="checkbox" class="custom-control-input" id="copy_mapel" name="copy_mapel" value="1" checked>
                                    <label class="custom-control-label font-weight-bold" for="copy_mapel">Salin Penugasan Guru (Mata Pelajaran Kelas)</label>
                                    <p class="text-muted text-small ml-0">Menyalin data guru mana yang mengajar mata pelajaran tertentu di kelas tersebut.</p>
                                </div>

                                <div class="custom-control custom-checkbox mb-3">
                                    <input type="checkbox" class="custom-control-input" id="copy_jadwal" name="copy_jadwal" value="1" checked>
                                    <label class="custom-control-label font-weight-bold" for="copy_jadwal">Salin Jadwal Pelajaran (Slot Waktu)</label>
                                    <p class="text-muted text-small ml-0">Menyalin Hari, Jam Mulai, dan Jam Selesai untuk setiap mata pelajaran di setiap kelas.</p>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center bg-light p-4 rounded border">
                            <div class="text-muted text-small">
                                <i class="simple-icon-exclamation mr-1"></i> Proses ini mungkin memakan waktu beberapa detik.
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm" id="btn-submit">
                                <i class="simple-icon-rocket mr-2"></i> Jalankan Roll-over
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card mb-4 bg-primary text-white">
                <div class="card-body">
                    <h5 class="mb-4">Panduan Penggunaan</h5>
                    <ol class="pl-3 mb-0">
                        <li class="mb-2">Pastikan **Tahun Akademik** dan **Semester** tujuan sudah dibuat.</li>
                        <li class="mb-2">Gunakan fitur ini **hanya satu kali** per semester untuk menghindari data ganda.</li>
                        <li class="mb-2">Setelah proses selesai, periksa kembali data di menu **Master Kelas** dan **Jadwal Pelajaran**.</li>
                        <li>Proses ini **IDEMPOTEN**: Jika kelas dengan nama yang sama sudah ada di tujuan, data hanya akan disesuaikan, tidak diduplikasi ganda (berdasarkan nama kelas).</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('form').on('submit', function() {
            let btn = $('#btn-submit');
            btn.prop('disabled', true);
            btn.html('<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...');
            
            return confirm('Anda yakin ingin melakukan Roll-over data? Tindakan ini akan menambah data di semester tujuan secara otomatis.');
        });
    </script>
@endpush
