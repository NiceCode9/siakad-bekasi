@extends('layouts.app')

@section('title', 'Forum: '.$forum->judul)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>Diskusi: {{ $forum->judul }}</h1>
            <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                <ol class="breadcrumb pt-0">
                    <li class="breadcrumb-item"><a href="{{ route('elearning.index') }}">E-Learning</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('elearning.course', $forum->mata_pelajaran_guru_id) }}">Mata Pelajaran</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Diskusi</li>
                </ol>
            </nav>
            <div class="separator mb-5"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- Original Topic -->
            <div class="card mb-4 border-primary">
                <div class="card-body">
                    <div class="d-flex mb-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($forum->pembuat->name) }}" class="rounded-circle mr-3" width="50">
                        <div>
                            <h5 class="mb-0">{{ $forum->pembuat->name }}</h5>
                            <span class="text-muted text-small">{{ $forum->created_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                    <h4>{{ $forum->judul }}</h4>
                    <p>{!! nl2br(e($forum->konten)) !!}</p>
                </div>
            </div>

            <!-- Replies -->
            <h5 class="mb-4">Komentar ({{ $forum->forumKomentar->count() }})</h5>
            @foreach($forum->forumKomentar as $reply)
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex mb-2">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($reply->user->name) }}" class="rounded-circle mr-2" width="30">
                            <div>
                                <strong>{{ $reply->user->name }}</strong>
                                <span class="text-muted text-small ml-2">{{ $reply->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <p class="mb-0">{{ $reply->konten }}</p>
                    </div>
                </div>
            @endforeach

            <!-- Reply Form -->
            <div class="card mt-5">
                <div class="card-body">
                    <h5 class="mb-4">Tinggalkan Komentar</h5>
                    <form action="{{ route('forum.reply', $forum->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <textarea name="konten" class="form-control" rows="4" placeholder="Tulis komentar Anda di sini..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">KIRIM KOMENTAR</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
