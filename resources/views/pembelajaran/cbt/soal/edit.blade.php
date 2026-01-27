@extends('layouts.app')

@section('title', 'Edit Soal')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Edit Soal</h4>
            <small class="text-muted">{{ $bankSoal->nama }}</small>
        </div>
        <a href="{{ route('bank-soal.show', $bankSoal->id) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="{{ route('soal.update', $soal->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-9">
                <!-- Editor Pertanyaan -->
                <div class="card mb-3">
                    <div class="card-header bg-white">
                        <label class="mb-0 font-weight-bold">Pertanyaan <span class="text-danger">*</span></label>
                    </div>
                    <div class="card-body p-0">
                        <textarea name="pertanyaan" id="pertanyaan" class="form-control summernote" required>{{ $soal->pertanyaan }}</textarea>
                    </div>
                </div>

                <!-- Media Upload -->
                <div class="card mb-3">
                    <div class="card-header bg-white">
                        <label class="mb-0 font-weight-bold">Media (Gambar/Audio/Video)</label>
                    </div>
                    <div class="card-body">
                         @if($soal->tipe_media)
                            <div class="alert alert-info py-2">
                                <small>Media saat ini: <strong>{{ ucfirst($soal->tipe_media) }}</strong></small>
                                @if($soal->tipe_media == 'image')
                                    <div class="mt-2"><img src="{{ asset('storage/'.$soal->gambar) }}" style="max-height: 100px; max-width: 100%;"></div>
                                @elseif($soal->tipe_media == 'audio')
                                     <div class="mt-2"><audio controls src="{{ asset('storage/'.$soal->audio) }}"></audio></div>
                                @elseif($soal->tipe_media == 'video')
                                     <div class="mt-2"><video controls src="{{ asset('storage/'.$soal->video) }}" style="max-height: 150px;"></video></div>
                                @endif
                                <div class="mt-1 small text-muted">Upload file baru di bawah jika ingin mengganti.</div>
                            </div>
                        @endif

                        <div class="custom-file">
                            <input type="file" name="file_media" class="custom-file-input" id="fileMedia" accept=".jpg,.jpeg,.png,.mp3,.wav,.mp4,.webm">
                            <label class="custom-file-label" for="fileMedia">Pilih File (Ganti)...</label>
                        </div>
                    </div>
                </div>

                <!-- Opsi Jawaban Container -->
                <div class="card mb-3" id="containerOptions">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <label class="mb-0 font-weight-bold">Jawaban / Opsi</label>
                        <span class="badge badge-info" id="typeLabel">{{ ucfirst(str_replace('_', ' ', $soal->tipe_soal)) }}</span>
                    </div>
                    <div class="card-body">
                        
                        <!-- Block Pilihan Ganda -->
                        <div id="blockPG" style="{{ $soal->tipe_soal != 'pilihan_ganda' ? 'display:none;' : '' }}">
                            @php $opsi = ['a','b','c','d','e']; @endphp
                            @foreach($opsi as $k)
                                @php $field = 'opsi_'.$k; @endphp
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <input type="radio" name="kunci_jawaban" value="{{ strtoupper($k) }}" {{ $soal->kunci_jawaban == strtoupper($k) ? 'checked' : '' }}>
                                            <span class="ml-2 font-weight-bold">{{ strtoupper($k) }}</span>
                                        </div>
                                    </div>
                                    <input type="text" name="opsi_{{ $k }}" class="form-control" value="{{ $soal->$field }}" placeholder="Teks jawaban opsi {{ strtoupper($k) }}...">
                                </div>
                            @endforeach
                            <small class="text-muted"><i class="fas fa-info-circle"></i> Pilih radio button di sebelah kiri untuk menandai Kunci Jawaban Benar.</small>
                        </div>

                        <!-- Block Isian Singkat -->
                        <div id="blockIsian" style="{{ $soal->tipe_soal != 'isian_singkat' ? 'display:none;' : '' }}">
                            <div class="form-group">
                                <label>Kunci Jawaban Singkat</label>
                                <input type="text" name="kunci_jawaban_text" class="form-control" value="{{ $soal->kunci_jawaban }}" placeholder="Jawaban yang benar...">
                                <small class="form-text text-muted">Sistem akan mencocokkan input siswa dengan teks ini (case-insensitive).</small>
                            </div>
                        </div>

                        <!-- Block Uraian -->
                        <div id="blockUraian" style="{{ $soal->tipe_soal != 'uraian' ? 'display:none;' : '' }}">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Soal Uraian dinilai secara manual oleh guru. Tidak ada kunci jawaban otomatis.
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Sidebar Settings -->
            <div class="col-md-3">
                <div class="card mb-3">
                    <div class="card-header">Pengaturan Soal</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Tipe Soal</label>
                            <select name="tipe_soal" id="tipe_soal" class="form-control">
                                <option value="pilihan_ganda" {{ $soal->tipe_soal == 'pilihan_ganda' ? 'selected' : '' }}>Pilihan Ganda</option>
                                <option value="isian_singkat" {{ $soal->tipe_soal == 'isian_singkat' ? 'selected' : '' }}>Isian Singkat</option>
                                <option value="uraian" {{ $soal->tipe_soal == 'uraian' ? 'selected' : '' }}>Uraian / Essay</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tingkat Kesulitan</label>
                            <select name="tingkat_kesulitan" class="form-control">
                                <option value="mudah" {{ $soal->tingkat_kesulitan == 'mudah' ? 'selected' : '' }}>Mudah</option>
                                <option value="sedang" {{ $soal->tingkat_kesulitan == 'sedang' ? 'selected' : '' }}>Sedang</option>
                                <option value="sulit" {{ $soal->tingkat_kesulitan == 'sulit' ? 'selected' : '' }}>Sulit</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Bobot Nilai</label>
                            <input type="number" name="bobot" class="form-control" value="{{ $soal->bobot }}" min="1">
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary btn-block btn-lg">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>
<script>
    $(document).ready(function() {
        $('.summernote').summernote({
            height: 200,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture', 'table']],
            ]
        });

        $('#tipe_soal').change(function() {
            var type = $(this).val();
            var text = $("#tipe_soal option:selected").text();
            
            $('#typeLabel').text(text);

            // Reset
            $('#blockPG, #blockIsian, #blockUraian').hide();
            $('input[name="kunci_jawaban"]').prop('required', false);

            if(type == 'pilihan_ganda') {
                $('#blockPG').show();
                $('input[name="kunci_jawaban"]').prop('required', true);
            } else if(type == 'isian_singkat') {
                $('#blockIsian').show();
            } else if(type == 'uraian') {
                $('#blockUraian').show();
            }
        });
    });
</script>
@endpush
