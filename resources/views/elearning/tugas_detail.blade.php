@extends('layouts.app')

@section('title', 'Detail Tugas: '.$tugas->judul)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>Detail Tugas</h1>
            <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                <ol class="breadcrumb pt-0">
                    <li class="breadcrumb-item"><a href="{{ route('elearning.index') }}">E-Learning</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('elearning.course', $tugas->mata_pelajaran_kelas_id) }}">Mata Pelajaran</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $tugas->judul }}</li>
                </ol>
            </nav>
            <div class="separator mb-5"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-5">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-4">Informasi Tugas</h5>
                    <p><strong>Judul:</strong> {{ $tugas->judul }}</p>
                    <p><strong>Deadline:</strong> <span class="text-danger">{{ $tugas->tanggal_deadline->format('d M Y, H:i') }}</span></p>
                    <hr>
                    <p><strong>Instruksi:</strong><br>{!! nl2br(e($tugas->deskripsi)) !!}</p>
                    @if($tugas->file_lampiran)
                        <a href="{{ Storage::url($tugas->file_lampiran) }}" target="_blank" class="btn btn-outline-primary btn-xs">DOWNLOAD LAMPIRAN</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-7">
            @role('siswa')
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-4">Status Pengumpulan</h5>
                        @if($submission)
                            <div class="alert alert-success">
                                Tugas sudah dikumpulkan pada {{ $submission->tanggal_submit->format('d/m/Y H:i') }}
                                <br>Status: <strong>{{ strtoupper($submission->status) }}</strong>
                                @if($submission->nilai)
                                    <br>Nilai: <span class="badge badge-primary">{{ $submission->nilai }}</span>
                                    <br>Feedback: {{ $submission->feedback }}
                                @endif
                            </div>
                            @if(!$submission->nilai)
                                <button class="btn btn-outline-info" data-toggle="collapse" data-target="#editSubmission">UBAH PENGUMPULAN</button>
                            @endif
                        @else
                            @if($tugas->isTerlambat())
                                <div class="alert alert-danger">Batas waktu pengumpulan telah berakhir.</div>
                            @else
                                <div id="submissionForm">
                                    <form action="{{ route('tugas.submit', $tugas->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group"><label>File Tugas (PDF/JPG/DOCX)</label><input type="file" name="file" class="form-control" required></div>
                                        <div class="form-group"><label>Jawaban/Catatan</label><textarea name="jawaban" class="form-control" rows="3"></textarea></div>
                                        <button type="submit" class="btn btn-primary">KIRIM TUGAS</button>
                                    </form>
                                </div>
                            @endif
                        @endif

                        <div id="editSubmission" class="collapse mt-4">
                            <form action="{{ route('tugas.submit', $tugas->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group"><label>Update File</label><input type="file" name="file" class="form-control" required></div>
                                <div class="form-group"><label>Jawaban/Catatan</label><textarea name="jawaban" class="form-control" rows="3">{{ $submission->jawaban ?? '' }}</textarea></div>
                                <button type="submit" class="btn btn-info">UPDATE TUGAS</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endrole

            @role('guru')
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-4">Daftar Pengumpulan Siswa</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Waktu</th>
                                        <th>File</th>
                                        <th>Nilai</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tugas->pengumpulanTugas as $sub)
                                        <tr>
                                            <td>{{ $sub->siswa->nama }}</td>
                                            <td>{{ $sub->tanggal_submit->format('d/m/Y H:i') }}</td>
                                            <td><a href="{{ Storage::url($sub->file_path) }}" target="_blank">View File</a></td>
                                            <td>{{ $sub->nilai ?? '-' }}</td>
                                            <td>
                                                <button class="btn btn-xs btn-outline-primary" data-toggle="modal" data-target="#gradeModal{{ $sub->id }}">NILAI</button>
                                            </td>
                                        </tr>

                                        <!-- Grade Modal -->
                                        <div class="modal fade" id="gradeModal{{ $sub->id }}" tabindex="-1" role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <form action="{{ route('tugas.grade', $sub->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-content">
                                                        <div class="modal-header"><h5 class="modal-title">Penilaian: {{ $sub->siswa->nama }}</h5></div>
                                                        <div class="modal-body">
                                                            <div class="form-group"><label>Nilai (0-100)</label><input type="number" name="nilai" class="form-control" value="{{ $sub->nilai }}" required></div>
                                                            <div class="form-group"><label>Feedback</label><textarea name="feedback" class="form-control">{{ $sub->feedback }}</textarea></div>
                                                        </div>
                                                        <div class="modal-footer"><button type="submit" class="btn btn-primary">SIMPAN NILAI</button></div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @empty
                                        <tr><td colspan="5">Belum ada yang mengumpulkan.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endrole
        </div>
    </div>
</div>
@endsection
