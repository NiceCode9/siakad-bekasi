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
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-question-circle text-primary mr-2"></i> Pertanyaan <span class="text-danger">*</span>
                        </h5>
                        <textarea name="pertanyaan" id="pertanyaan" class="form-control summernote" required></textarea>
                    </div>
                </div>

                <!-- Media Upload -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-photo-video text-info mr-2"></i> Media
                            <span class="text-muted small font-weight-normal">(Gambar/Audio/Video - Max 10MB)</span>
                        </h5>
                        <div class="dropzone-custom" id="mediaDropzone">
                            <div class="dz-message">
                                <i class="simple-icon-cloud-upload d-block display-4 mb-2"></i>
                                <span>Tarik file ke sini atau klik untuk upload media soal</span>
                            </div>
                        </div>
                        <div class="mt-2 text-center">
                             <small class="text-muted italic">Format: .jpg, .jpeg, .png, .mp3, .wav, .mp4, .webm</small>
                        </div>
                    </div>
                </div>

                <!-- Opsi Jawaban Container -->
                <div class="card border-0 shadow-sm mb-4" id="containerOptions">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-tasks text-success mr-2"></i> Jawaban / Opsi
                            </h5>
                            <span class="badge badge-primary badge-pill px-3 py-2" id="typeLabel">Pilihan Ganda</span>
                        </div>

                        <div class="mt-4">
                            <!-- Block Pilihan Ganda -->
                            <div id="blockPG">
                                @php $opsi = ['a','b','c','d','e']; @endphp
                                @foreach($opsi as $k)
                                    <div class="input-group mb-3 custom-option">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text bg-light border-right-0">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="check_{{ $k }}" name="kunci_jawaban" value="{{ strtoupper($k) }}" class="custom-control-input" required>
                                                    <label class="custom-control-label font-weight-bold" for="check_{{ $k }}">{{ strtoupper($k) }}</label>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="text" name="opsi_{{ $k }}" class="form-control border-left-0 pl-0 form-control-lg" placeholder="Ketikkan teks jawaban untuk opsi {{ strtoupper($k) }}...">
                                    </div>
                                @endforeach
                                <div class="alert alert-light border mt-3 mb-0">
                                    <small class="text-muted"><i class="fas fa-info-circle text-info mr-1"></i> Pilih bulatan di sebelah kiri pada huruf opsi untuk menandai <strong>Kunci Jawaban yang Benar</strong>.</small>
                                </div>
                            </div>

                            <!-- Block Isian Singkat -->
                            <div id="blockIsian" style="display: none;">
                                <div class="form-group">
                                    <label class="font-weight-bold">Teks Kunci Jawaban</label>
                                    <input type="text" name="kunci_jawaban_text" class="form-control form-control-lg" placeholder="Masukkan jawaban singkat yang benar...">
                                    <small class="form-text text-muted bg-yellow-light p-2 mt-2 rounded">
                                        <i class="fas fa-exclamation-triangle mr-1 text-warning"></i> Sistem akan mencocokkan input siswa dengan teks ini secara tepat (tidak peka huruf besar/kecil).
                                    </small>
                                </div>
                            </div>

                            <!-- Block Uraian -->
                            <div id="blockUraian" style="display: none;">
                                <div class="text-center py-4 bg-light rounded border">
                                    <div class="display-4 text-muted mb-2"><i class="fas fa-edit"></i></div>
                                    <h6 class="text-muted">Soal Tipe Uraian</h6>
                                    <p class="text-muted small px-5">Soal uraian tidak memiliki kunci jawaban otomatis. Guru harus memberikan nilai secara manual setelah siswa mengumpulkan jawaban.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Settings -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm mb-4 sticky-top" style="top: 100px; z-index: 10;">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-cog text-secondary mr-2"></i> Pengaturan
                        </h5>

                        <div class="form-group">
                            <label class="small text-muted font-weight-bold">TIPE SOAL</label>
                            <select name="tipe_soal" id="tipe_soal" class="form-control custom-select">
                                <option value="pilihan_ganda">Pilihan Ganda</option>
                                <option value="isian_singkat">Isian Singkat</option>
                                <option value="uraian">Uraian / Essay</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="small text-muted font-weight-bold">TINGKAT KESULITAN</label>
                            <select name="tingkat_kesulitan" class="form-control custom-select">
                                <option value="mudah">Mudah</option>
                                <option value="sedang" selected>Sedang</option>
                                <option value="sulit">Sulit</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="small text-muted font-weight-bold">BOBOT NILAI</label>
                            <div class="input-group">
                                <input type="number" name="bobot" class="form-control" value="2" min="1">
                                <div class="input-group-append">
                                    <span class="input-group-text bg-light">Poin</span>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <button type="submit" class="btn btn-primary btn-block btn-lg shadow">
                            <i class="fas fa-save mr-1"></i> Simpan Soal
                        </button>

                        <a href="{{ route('bank-soal.show', $bankSoal->id) }}" class="btn btn-outline-secondary btn-block mt-2">
                            Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets') }}/css/vendor/dropzone.min.css" />
<style>
    .dropzone-custom {
        border: 2px dashed #ececec;
        border-radius: 10px;
        min-height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .dropzone-custom:hover {
        border-color: #3158c9;
        background: #f8f9ff;
    }
    .dropzone-custom .dz-message span {
        font-weight: 500;
        color: #8f8f8f;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>
<script src="{{ asset('assets') }}/js/vendor/dropzone.min.js"></script>
<script>
    Dropzone.autoDiscover = false;

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

        const mediaDropzone = new Dropzone("#mediaDropzone", {
            url: "{{ route('soal.store') }}",
            autoProcessQueue: false,
            uploadMultiple: false,
            parallelUploads: 1,
            maxFiles: 1,
            acceptedFiles: ".jpg,.jpeg,.png,.mp3,.wav,.mp4,.webm",
            addRemoveLinks: true,
            // dictRemoveFile: "<i class='fas fa-trash-alt text-danger'></i> Hapus",
            paramName: "file_media",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            init: function() {
                var myDropzone = this;
                this.on("addedfile", function() {
                    if (this.files[1] != null) this.removeFile(this.files[0]);
                });

                this.on("sending", function(file, xhr, formData) {
                    var data = $('#formSoal').serializeArray();
                    $.each(data, function(key, el) {
                        formData.append(el.name, el.value);
                    });
                    formData.append('pertanyaan', $('#pertanyaan').summernote('code'));
                });

                this.on("success", function(file, response) {
                    window.location.href = "{{ route('bank-soal.show', $bankSoal->id) }}";
                });

                this.on("error", function(file, response) {
                    $('#btnSubmit').prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Soal');
                    let errorMessage = typeof response === 'string' ? response : (response.message || 'Gagal menyimpan soal.');
                    Swal.fire({ icon: 'error', title: 'Oops...', text: errorMessage });
                });
            },
            thumbnailWidth: 200,
            previewTemplate: '<div class="dz-preview dz-file-preview mb-3"><div class="d-flex flex-row "><div class="p-0 w-30 position-relative"><div class="dz-error-mark"><span><i></i></span></div><div class="dz-success-mark"><span><i></i></span></div><div class="preview-container"><img data-dz-thumbnail class="img-thumbnail border-0" /><i class="simple-icon-doc preview-icon" ></i></div></div><div class="pl-3 pt-2 pr-2 pb-1 w-70 dz-details position-relative"><div><span data-dz-name></span></div><div class="text-primary text-extra-small" data-dz-size /><div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div><div class="dz-error-message"><span data-dz-errormessage></span></div></div></div><a href="#/" class="remove" data-dz-remove><i class="glyph-icon simple-icon-trash"></i></a></div>'
        });

        $('#formSoal').on('submit', function(e) {
            e.preventDefault();
            if (!this.checkValidity()) {
                this.reportValidity();
                return;
            }

            $('#btnSubmit').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

            if (mediaDropzone.getQueuedFiles().length > 0) {
                mediaDropzone.processQueue();
            } else {
                submitFormNormally();
            }
        });

        function submitFormNormally() {
            var formData = new FormData($('#formSoal')[0]);
            formData.set('pertanyaan', $('#pertanyaan').summernote('code'));

            $.ajax({
                url: "{{ route('soal.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    window.location.href = "{{ route('bank-soal.show', $bankSoal->id) }}";
                },
                error: function(xhr) {
                    $('#btnSubmit').prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Soal');
                    let errorMessage = xhr.responseJSON.message || 'Gagal menyimpan soal.';
                    Swal.fire({ icon: 'error', title: 'Kesalahan', html: errorMessage });
                }
            });
        }

        $('#tipe_soal').change(function() {
            var type = $(this).val();
            var text = $("#tipe_soal option:selected").text();
            $('#typeLabel').text(text);
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
