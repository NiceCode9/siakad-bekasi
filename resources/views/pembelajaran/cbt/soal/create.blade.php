@extends('layouts.app')

@section('title', 'Tambah Soal')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Tambah Soal Baru</h4>
            <small class="text-muted">{{ $bankSoal->nama }}</small>
        </div>
        <a href="{{ route('bank-soal.show', $bankSoal->id) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="{{ route('soal.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="bank_soal_id" value="{{ $bankSoal->id }}">

        <div class="row">
            <div class="col-md-9">
                <!-- Editor Pertanyaan -->
                <div class="card mb-3">
                    <div class="card-header bg-white">
                        <label class="mb-0 font-weight-bold">Pertanyaan <span class="text-danger">*</span></label>
                    </div>
                    <div class="card-body p-0">
                        <textarea name="pertanyaan" id="pertanyaan" class="form-control summernote" required></textarea>
                    </div>
                </div>

                <!-- Media Upload -->
                <div class="card mb-3">
                    <div class="card-header bg-white">
                        <label class="mb-0 font-weight-bold">Media (Gambar/Audio/Video) <span class="text-muted small">(Opsional, Max 10MB)</span></label>
                    </div>
                    <div class="card-body">
                        <div class="custom-file">
                            <input type="file" name="file_media" class="custom-file-input" id="fileMedia" accept=".jpg,.jpeg,.png,.mp3,.wav,.mp4,.webm">
                            <label class="custom-file-label" for="fileMedia">Pilih File...</label>
                        </div>
                    </div>
                </div>

                <!-- Opsi Jawaban Container -->
                <div class="card mb-3" id="containerOptions">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <label class="mb-0 font-weight-bold">Jawaban / Opsi</label>
                        <span class="badge badge-info" id="typeLabel">Pilihan Ganda</span>
                    </div>
                    <div class="card-body">
                        
                        <!-- Block Pilihan Ganda -->
                        <div id="blockPG">
                            @php $opsi = ['a','b','c','d','e']; @endphp
                            @foreach($opsi as $k)
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <input type="radio" name="kunci_jawaban" value="{{ strtoupper($k) }}" required>
                                            <span class="ml-2 font-weight-bold">{{ strtoupper($k) }}</span>
                                        </div>
                                    </div>
                                    <input type="text" name="opsi_{{ $k }}" class="form-control" placeholder="Teks jawaban opsi {{ strtoupper($k) }}...">
                                </div>
                            @endforeach
                            <small class="text-muted"><i class="fas fa-info-circle"></i> Pilih radio button di sebelah kiri untuk menandai Kunci Jawaban Benar.</small>
                        </div>

                        <!-- Block Isian Singkat -->
                        <div id="blockIsian" style="display: none;">
                            <div class="form-group">
                                <label>Kunci Jawaban Singkat</label>
                                <input type="text" name="kunci_jawaban_text" class="form-control" placeholder="Jawaban yang benar...">
                                <small class="form-text text-muted">Sistem akan mencocokkan input siswa dengan teks ini (case-insensitive).</small>
                            </div>
                        </div>

                        <!-- Block Uraian -->
                        <div id="blockUraian" style="display: none;">
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
                                <option value="pilihan_ganda">Pilihan Ganda</option>
                                <option value="isian_singkat">Isian Singkat</option>
                                <option value="uraian">Uraian / Essay</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tingkat Kesulitan</label>
                            <select name="tingkat_kesulitan" class="form-control">
                                <option value="mudah">Mudah</option>
                                <option value="sedang" selected>Sedang</option>
                                <option value="sulit">Sulit</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Bobot Nilai</label>
                            <input type="number" name="bobot" class="form-control" value="2" min="1">
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary btn-block btn-lg">
                            <i class="fas fa-save"></i> Simpan Soal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<!-- Summernote / Trumbowyg / CKEditor -->
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
                ['insert', ['link', 'picture', 'table']], // Image upload supported if configured
            ]
        });

        $('#tipe_soal').change(function() {
            var type = $(this).val();
            var text = $("#tipe_soal option:selected").text();
            
            $('#typeLabel').text(text);

            // Reset visibility
            $('#blockPG, #blockIsian, #blockUraian').hide();
            
            // Required attribute handling
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
