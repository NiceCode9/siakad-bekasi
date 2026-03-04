@extends('layouts.app')

@section('title', 'Ujian Saya')

@section('content')
<div class="container-fluid">
    <h4>Daftar Ujian</h4>
    <p class="text-muted">Ujian yang tersedia untuk kelas Anda saat ini.</p>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('ujian-siswa.index') }}" method="GET" class="row">
                        <div class="col-md-8">
                            <div class="form-group mb-md-0">
                                <label class="small text-muted">Filter Mata Pelajaran</label>
                                <select name="mata_pelajaran_id" class="form-control select2-single">
                                    <option value="">-- Semua Mata Pelajaran --</option>
                                    @foreach($mapelOptions as $m)
                                        <option value="{{ $m->id }}" {{ request('mata_pelajaran_id') == $m->id ? 'selected' : '' }}>
                                            {{ $m->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            @if(request()->has('mata_pelajaran_id'))
                                <a href="{{ route('ujian-siswa.index') }}" class="btn btn-light ml-2">
                                    <i class="fas fa-undo"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @forelse($ujianList as $ujian)
            @php
                // Determine status manually for display color
                $now = now();
                $isOpen = $now->between($ujian->tanggal_mulai, $ujian->tanggal_selesai);

                $myUjian = $ujian->ujianSiswa->first();
                $isDone = $myUjian && $myUjian->status === 'selesai';

                $cardColor = 'border-left-primary shadow';
                if ($isDone) {
                    $cardColor = 'border-left-success shadow bg-light';
                } elseif (!$isOpen) {
                    $cardColor = 'border-left-secondary';
                }

                $textColor = $isDone ? 'text-success' : 'text-primary';
            @endphp
            <div class="col-md-6 mb-4">
                <div class="card shadow h-100 py-2 {{ $cardColor }}">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold {{ $textColor }} text-uppercase mb-1">
                                    {{ $ujian->mataPelajaranKelas->mataPelajaran->nama }}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $ujian->nama_ujian }}</div>
                                <div class="mt-2 small text-muted">
                                    <i class="fas fa-clock"></i> {{ $ujian->tanggal_mulai->format('d M H:i') }} - {{ $ujian->tanggal_selesai->format('d M H:i') }}
                                    <br>
                                    <i class="fas fa-stopwatch"></i> {{ $ujian->durasi }} Menit
                                    @if($isDone)
                                        <br>
                                        <span class="badge badge-success mt-1">Selesai Dikerjakan</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-auto">
                                @if($isDone)
                                    <a href="{{ route('ujian-siswa.show', $ujian->id) }}" class="btn btn-outline-success">
                                        Lihat Hasil
                                    </a>
                                @elseif(Auth::user()->hasRole('siswa'))
                                    <a href="{{ route('ujian-siswa.show', $ujian->id) }}" class="btn btn-primary {{ !$isOpen ? 'disabled' : '' }}">
                                        {{ $isOpen ? 'Mulai Ujian' : 'Belum Dibuka' }}
                                    </a>
                                @else
                                    <a href="{{ route('jadwal-ujian.show', $ujian->id) }}" class="btn btn-info">
                                        Detail Review
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">Tidak ada jadwal ujian yang ditemukan.</div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $ujianList->links('pagination::bootstrap-4') }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
    });
</script>
@endpush
