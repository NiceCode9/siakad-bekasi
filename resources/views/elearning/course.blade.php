@extends('layouts.app')

@section('title', $subject->mataPelajaranKelas->mataPelajaran->nama)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>{{ $subject->mataPelajaranKelas->mataPelajaran->nama }} - {{ $subject->mataPelajaranKelas->kelas->nama }}</h1>
            <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                <ol class="breadcrumb pt-0">
                    <li class="breadcrumb-item"><a href="{{ route('elearning.index') }}">E-Learning</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $subject->mataPelajaranKelas->mataPelajaran->nama }}</li>
                </ol>
            </nav>
            <div class="separator mb-5"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <ul class="nav nav-tabs separator-tabs ml-0 mb-5" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="materi-tab" data-toggle="tab" href="#materi" role="tab">MATERI</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tugas-tab" data-toggle="tab" href="#tugas" role="tab">TUGAS</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="forum-tab" data-toggle="tab" href="#forum" role="tab">FORUM DISKUSI</a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Materi Tab -->
                <div class="tab-pane show active" id="materi" role="tabpanel">
                    @role('guru')
                        <div class="text-right mb-4">
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addMateriModal">
                                <i class="simple-icon-plus"></i> TAMBAH MATERI
                            </button>
                        </div>
                    @endrole
                    <div class="row">
                        @forelse($subject->materiAjar as $materi)
                            <div class="col-12 mb-3">
                                <div class="card d-flex flex-row">
                                    <div class="p-4 d-flex align-items-center">
                                        @if($materi->tipe == 'file')
                                            <i class="simple-icon-doc text-primary" style="font-size: 30px;"></i>
                                        @elseif($materi->tipe == 'video')
                                            <i class="simple-icon-control-play text-danger" style="font-size: 30px;"></i>
                                        @else
                                            <i class="simple-icon-link text-info" style="font-size: 30px;"></i>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        <h5 class="mb-1">{{ $materi->judul }}</h5>
                                        <p class="text-muted text-small mb-2">{{ $materi->deskripsi }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted text-small">{{ $materi->tanggal_publish->format('d M Y') }} | Dilihat: {{ $materi->view_count }}x</span>
                                            <div>
                                                @if($materi->tipe == 'file')
                                                    <a href="{{ route('materi.download', $materi->id) }}" class="btn btn-xs btn-outline-primary">DOWNLOAD</a>
                                                @else
                                                    <a href="{{ $materi->url }}" target="_blank" class="btn btn-xs btn-outline-info">BUKA LINK</a>
                                                @endif
                                                @role('guru')
                                                    <form action="{{ route('materi.destroy', $materi->id) }}" method="POST" class="d-inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-xs btn-outline-danger" onclick="return confirm('Hapus materi?')">HAPUS</button>
                                                    </form>
                                                @endrole
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">Materi belum tersedia.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Tugas Tab -->
                <div class="tab-pane" id="tugas" role="tabpanel">
                    @role('guru')
                        <div class="text-right mb-4">
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addTugasModal">
                                <i class="simple-icon-plus"></i> BUAT TUGAS
                            </button>
                        </div>
                    @endrole
                    <div class="row">
                        @forelse($subject->tugas as $tugas)
                            <div class="col-12 mb-3">
                                <div class="card d-flex flex-row">
                                    <div class="p-4 d-flex align-items-center">
                                        <i class="simple-icon-notebook text-warning" style="font-size: 30px;"></i>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="mb-1">{{ $tugas->judul }}</h5>
                                        <p class="text-muted text-small mb-2">Deadline: {{ $tugas->tanggal_deadline->format('d M Y, H:i') }}</p>
                                        <div class="text-right">
                                            <a href="{{ route('tugas.show', $tugas->id) }}" class="btn btn-xs btn-primary">LIHAT DETAIL</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">Belum ada tugas.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Forum Tab -->
                <div class="tab-pane" id="forum" role="tabpanel">
                    <div class="text-right mb-4">
                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addForumModal">
                            <i class="simple-icon-speech"></i> BUAT TOPIK DISKUSI
                        </button>
                    </div>
                    <div class="row">
                        @forelse($subject->forumDiskusi as $forum)
                            <div class="col-12 mb-3">
                                <div class="card card-body">
                                    <h5 class="mb-1">{{ $forum->judul }}</h5>
                                    <p class="text-muted text-small mb-2">Oleh: {{ $forum->pembuat->name }} | {{ $forum->created_at->diffForHumans() }} | Komentar: {{ $forum->forumKomentar->count() }}</p>
                                    <div class="text-right">
                                        <a href="{{ route('forum.show', $forum->id) }}" class="btn btn-xs btn-outline-primary">IKUT DISKUSI</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">Belum ada diskusi.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals for Guru -->
@role('guru')
<div class="modal fade" id="addMateriModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="{{ route('materi.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="mata_pelajaran_guru_id" value="{{ $subject->id }}">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Tambah Materi</h5></div>
                <div class="modal-body">
                    <div class="form-group"><label>Judul</label><input type="text" name="judul" class="form-control" required></div>
                    <div class="form-group"><label>Tipe</label>
                        <select name="tipe" class="form-control">
                            <option value="file">File (PDF/DOCX/PPT)</option>
                            <option value="url">Link Website</option>
                            <option value="video">Link Video</option>
                        </select>
                    </div>
                    <div class="form-group"><label>File</label><input type="file" name="file" class="form-control"></div>
                    <div class="form-group"><label>URL</label><input type="url" name="url" class="form-control"></div>
                    <div class="form-group"><label>Keterangan</label><textarea name="deskripsi" class="form-control"></textarea></div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="is_published" class="custom-control-input" id="checkPub" checked>
                        <label class="custom-control-label" for="checkPub">Publish Sekarang</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-primary">SIMPAN</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="addTugasModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="{{ route('tugas.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="mata_pelajaran_guru_id" value="{{ $subject->id }}">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Buat Tugas Baru</h5></div>
                <div class="modal-body">
                    <div class="form-group"><label>Judul Tugas</label><input type="text" name="judul" class="form-control" required></div>
                    <div class="form-group"><label>Deadline</label><input type="datetime-local" name="tanggal_deadline" class="form-control" required></div>
                    <div class="form-group"><label>Lampiran</label><input type="file" name="file_lampiran" class="form-control"></div>
                    <div class="form-group"><label>Instruksi</label><textarea name="deskripsi" class="form-control" rows="5"></textarea></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-primary">PUBLISH TUGAS</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endrole

<div class="modal fade" id="addForumModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="{{ route('forum.store') }}" method="POST">
            @csrf
            <input type="hidden" name="mata_pelajaran_guru_id" value="{{ $subject->id }}">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Buat Topik Diskusi</h5></div>
                <div class="modal-body">
                    <div class="form-group"><label>Judul Topik</label><input type="text" name="judul" class="form-control" required></div>
                    <div class="form-group"><label>Konten</label><textarea name="konten" class="form-control" rows="5" required></textarea></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-primary">BUAT TOPIK</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
