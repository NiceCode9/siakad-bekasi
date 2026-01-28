@extends('layouts.app')

@section('title', 'E-Learning Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>E-Learning</h1>
            <div class="separator mb-5"></div>
        </div>
    </div>

    <div class="row">
        @forelse($subjects as $item)
            <div class="col-md-4 col-sm-6 col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="iconsminds-blackboard text-primary" style="font-size: 40px;"></i>
                        </div>
                        <h5 class="card-title text-center">{{ $item->mataPelajaran->nama }}</h5>
                        <p class="text-muted text-center mb-2">
                            Kelas: {{ $item->kelas->nama }}
                        </p>
                        <p class="text-muted text-center mb-4">
                            Guru: {{ $item->guru->nama_lengkap }}
                        </p>
                        <div class="text-center">
                            <a href="{{ route('elearning.course', $item->id) }}" class="btn btn-primary btn-sm btn-block">MASUK KELAS</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    Belum ada kelas yang tersedia untuk Anda saat ini.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
