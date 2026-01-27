@extends('layouts.app')

@section('title', 'Ujian Saya')

@section('content')
<div class="container-fluid">
    <h4>Daftar Ujian</h4>
    <p class="text-muted">Ujian yang tersedia untuk kelas Anda saat ini.</p>

    <div class="row">
        @forelse($ujianList as $ujian)
            @php
                // Determine status manually for display color
                $now = now();
                $isOpen = $now->between($ujian->tanggal_mulai, $ujian->tanggal_selesai);
                $isDone = false; // Need to check relationship if loaded, or fetch separately?
                // In index method we didn't eager load 'ujianSiswa'.
                // Ideally optimized query. For now let's assume controller handles it or we rely on logic upon entering.
                
                $cardColor = 'border-left-primary';
                if (!$isOpen) $cardColor = 'border-left-secondary';
            @endphp
            <div class="col-md-6 mb-4">
                <div class="card shadow h-100 py-2 {{ $cardColor }}">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    {{ $ujian->mataPelajaranKelas->mataPelajaran->nama }}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $ujian->nama_ujian }}</div>
                                <div class="mt-2 small text-muted">
                                    <i class="fas fa-clock"></i> {{ $ujian->tanggal_mulai->format('d M H:i') }} - {{ $ujian->tanggal_selesai->format('d M H:i') }}
                                    <br>
                                    <i class="fas fa-stopwatch"></i> {{ $ujian->durasi }} Menit
                                </div>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('ujian-siswa.show', $ujian->id) }}" class="btn btn-primary">
                                    Lihat / Mulai
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">Tidak ada jadwal ujian aktif saat ini.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection
