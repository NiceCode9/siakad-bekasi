@extends('layouts.app')

@section('title', 'Detail Bank Soal')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Info Bank Soal -->
        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">{{ $bankSoal->nama }}</h5>
                    <h6 class="card-subtitle mb-2 text-muted">{{ $bankSoal->kode }}</h6>
                    <p class="card-text mb-1">
                        <strong>Mapel:</strong> {{ $bankSoal->mataPelajaran->nama }}
                    </p>
                    <p class="card-text mb-1">
                        <strong>Tingkat:</strong> {{ ucfirst($bankSoal->tingkat_kesulitan) }}
                    </p>
                    <p class="card-text mb-3">
                        <strong>Jumlah Soal:</strong> {{ $bankSoal->soal->count() }}
                    </p>
                    <p class="text-muted small">
                        {{ $bankSoal->deskripsi }}
                    </p>
                    <hr>
                    <a href="{{ route('bank-soal.index') }}" class="btn btn-secondary btn-block mb-2">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <a href="{{ route('bank-soal.edit', $bankSoal->id) }}" class="btn btn-warning btn-block">
                        <i class="fas fa-edit"></i> Edit Bank Soal
                    </a>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    Filter Soal
                </div>
                <div class="card-body">
                    <div class="form-group mb-0">
                        <label>Cari Pertanyaan</label>
                        <input type="text" id="searchSoal" class="form-control form-control-sm" placeholder="Ketik kata kunci...">
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Soal -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Daftar Soal</h4>
                <a href="{{ route('soal.create', ['bank_soal_id' => $bankSoal->id]) }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Tambah Soal
                </a>
            </div>

            <div class="accordion" id="accordionSoal">
                @forelse($bankSoal->soal as $index => $soal)
                    <div class="card mb-2 soal-item">
                        <div class="card-header bg-white" id="heading{{ $soal->id }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <h2 class="mb-0">
                                    <button class="btn btn-link text-dark text-left" type="button" data-toggle="collapse" data-target="#collapse{{ $soal->id }}">
                                        <span class="badge badge-primary mr-2">{{ $index + 1 }}</span>
                                        {{ Str::limit(strip_tags($soal->pertanyaan), 60) }}
                                        <small class="text-muted ml-2">({{ ucfirst(str_replace('_', ' ', $soal->tipe_soal)) }})</small>
                                    </button>
                                </h2>
                                <div>
                                    <span class="badge badge-secondary mr-2">Bobot: {{ $soal->bobot }}</span>
                                    <div class="btn-group">
                                        <a href="{{ route('soal.edit', $soal->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('soal.destroy', $soal->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus soal ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="collapse{{ $soal->id }}" class="collapse {{ $index == 0 ? 'show' : '' }}" data-parent="#accordionSoal">
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong>Pertanyaan:</strong>
                                    <div class="border p-2 rounded bg-light">
                                        {!! $soal->pertanyaan !!}
                                        
                                        @if($soal->tipe_media)
                                            <div class="mt-2">
                                                @if($soal->tipe_media == 'image')
                                                    <img src="{{ asset('storage/'.$soal->gambar) }}" class="img-fluid" style="max-height: 300px">
                                                @elseif($soal->tipe_media == 'audio')
                                                    <audio controls src="{{ asset('storage/'.$soal->audio) }}" class="w-100"></audio>
                                                @elseif($soal->tipe_media == 'video')
                                                    <video controls src="{{ asset('storage/'.$soal->video) }}" style="max-height: 300px; max-width: 100%;"></video>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if($soal->tipe_soal == 'pilihan_ganda')
                                    <div class="row">
                                        @php $opsi = ['a','b','c','d','e']; @endphp
                                        @foreach($opsi as $k)
                                            @php $field = 'opsi_'.$k; @endphp
                                            @if($soal->$field)
                                                <div class="col-md-6 mb-2">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text {{ strtoupper($k) == $soal->kunci_jawaban ? 'bg-success text-white' : '' }}">
                                                                {{ strtoupper($k) }}
                                                            </span>
                                                        </div>
                                                        <div class="form-control bg-white" readonly>{{ $soal->$field }}</div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                    <div class="mt-2">
                                        <strong>Kunci Jawaban:</strong> <span class="badge badge-success">{{ $soal->kunci_jawaban }}</span>
                                    </div>
                                @elseif($soal->tipe_soal == 'isian_singkat')
                                    <div class="mt-2">
                                        <strong>Kunci Jawaban:</strong> 
                                        <div class="alert alert-success py-1 mt-1">{{ $soal->kunci_jawaban }}</div>
                                    </div>
                                @elseif($soal->tipe_soal == 'uraian')
                                    <div class="mt-2 text-muted">
                                        <em>Soal uraian memerlukan pemeriksaan manual.</em>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-file-alt fa-3x mb-3"></i>
                        <p>Belum ada soal di bank ini.</p>
                        <a href="{{ route('soal.create', ['bank_soal_id' => $bankSoal->id]) }}" class="btn btn-primary btn-sm">Buat Soal Pertama</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function(){
        // Simple client-side search
        $("#searchSoal").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $(".soal-item").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
@endpush
