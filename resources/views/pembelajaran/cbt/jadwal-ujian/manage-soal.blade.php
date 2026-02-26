@extends('layouts.app')

@section('title', 'Kelola Soal Ujian')

@push('styles')
<style>
    .soal-table th {
        background-color: #f4f6f9;
        color: #495057;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        border-top: none;
    }
    .soal-table td {
        vertical-align: middle;
    }
    .soal-table tbody tr {
        transition: all 0.2s ease;
    }
    .soal-table tbody tr:hover {
        background-color: #f8f9fa;
        /* transform: scale(1.001); */
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        z-index: 10;
        position: relative;
    }
    .handle {
        cursor: grab;
        color: #adb5bd;
    }
    .handle:active {
        cursor: grabbing;
    }
    .sortable-ghost {
        opacity: 0.4;
        background-color: #f8f9fa;
    }
    .sortable-chosen {
        background-color: #e9ecef;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div class="mb-3 mb-md-0">
            <h3 class="font-weight-bold text-dark mb-1">Kelola Soal: {{ $jadwalUjian->nama_ujian }}</h3>
            <div class="text-muted d-flex align-items-center">
                <i class="fas fa-layer-group text-primary mr-2"></i>
                <span>{{ $jadwalUjian->mataPelajaranKelas->kelas->nama }}</span>
                <span class="mx-2">•</span>
                <span>{{ $jadwalUjian->mataPelajaranKelas->mataPelajaran->nama }}</span>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-primary shadow-sm mr-2 mb-2" data-toggle="modal" data-target="#modalAdd">
                <i class="fas fa-plus mr-1"></i> Pilih Manual
            </button>
            <button class="btn btn-warning shadow-sm mr-2 mb-2" data-toggle="modal" data-target="#modalRegenerate">
                <i class="fas fa-sync-alt mr-1"></i> Acak Komposisi
            </button>
            <a href="{{ route('jadwal-ujian.show', $jadwalUjian->id) }}" class="btn btn-outline-secondary shadow-sm mb-2">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Alert Bank Soal -->
    @if(!$jadwalUjian->bank_soal_id)
        <div class="card bg-warning text-dark border-0 shadow-sm mb-4">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center p-4">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <div class="rounded-circle bg-white p-3 mr-4 shadow-sm">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                    </div>
                    <div>
                        <h5 class="font-weight-bold mb-1">Bank Soal Belum Dikaitkan</h5>
                        <p class="mb-0 text-dark" style="opacity: 0.85;">Jadwal ini belum memiliki sumber bank soal. Kaitkan bank soal agar mempermudah pengambilan soal.</p>
                    </div>
                </div>
                <button class="btn btn-dark btn-lg shadow-sm" data-toggle="modal" data-target="#modalLinkBank">
                    <i class="fas fa-link mr-2"></i> Kaitkan Sekarang
                </button>
            </div>
        </div>
    @elseif($jadwalUjian->bank_soal_id)
        <div class="alert alert-info border-0 shadow-sm d-flex justify-content-between align-items-center px-4 py-3 mb-4">
            <div>
                <i class="fas fa-database mr-2"></i> Bank Soal Terkait: <strong>{{ $jadwalUjian->bankSoal->kode }} - {{ $jadwalUjian->bankSoal->nama }}</strong>
            </div>
            <span class="badge badge-light text-primary">{{ $jadwalUjian->jumlah_soal }} Target Soal</span>
        </div>
    @endif

    <!-- Main Table Card -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                <i class="fas fa-info-circle text-info mr-1"></i> Drag icon garis (<i class="fas fa-bars mx-1"></i>) untuk mengubah urutan soal
            </div>
            <span class="badge badge-primary px-3 py-1">{{ $jadwalUjian->soalUjian->count() }} Terpilih</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                <table class="table table-hover soal-table mb-0" id="tableSoal">
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="55%">Pertanyaan</th>
                            <th width="15%">Tipe Soal</th>
                            <th width="15%" class="text-center">Kesulitan</th>
                            <th width="10%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="sortableSoal">
                        @foreach($jadwalUjian->soalUjian()->orderBy('urutan')->get() as $su)
                            <tr data-id="{{ $su->id }}">
                                <td class="text-center handle">
                                    <i class="fas fa-bars mr-2"></i>
                                    <span class="font-weight-bold text-dark">{{ $su->urutan }}</span>
                                </td>
                                <td>
                                    <div class="text-dark line-height-normal">
                                        {!! Str::limit(strip_tags($su->soal->pertanyaan), 120) !!}
                                    </div>
                                </td>
                                <td>
                                    @if($su->soal->tipe_soal == 'pilihan_ganda')
                                        <span class="badge badge-info"><i class="fas fa-list-ul mr-1"></i> PG</span>
                                    @elseif($su->soal->tipe_soal == 'isian_singkat')
                                        <span class="badge badge-warning"><i class="fas fa-keyboard mr-1"></i> Isian Singkat</span>
                                    @else
                                        <span class="badge badge-success"><i class="fas fa-align-left mr-1"></i> Uraian</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($su->soal->tingkat_kesulitan == 'mudah')
                                        <span class="badge badge-success px-2 py-1">Mudah</span>
                                    @elseif($su->soal->tingkat_kesulitan == 'sedang')
                                        <span class="badge badge-warning px-2 py-1">Sedang</span>
                                    @else
                                        <span class="badge badge-danger px-2 py-1">Sulit</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('jadwal-ujian.remove-soal', $su->id) }}" method="POST" onsubmit="return confirm('Hapus soal ini dari jadwal?')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus dari daftar">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach

                        @if($jadwalUjian->soalUjian->count() == 0)
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <img src="{{ asset('img/undraw_empty.svg') }}" alt="Empty" class="img-fluid mb-4 mt-2" style="max-width: 200px; opacity: 0.6;">
                                    <h5 class="text-muted font-weight-normal mb-3">Belum ada soal terpilih</h5>
                                    @if($jadwalUjian->bank_soal_id)
                                        <button class="btn btn-primary shadow-sm mx-1" data-toggle="modal" data-target="#modalAdd">
                                            <i class="fas fa-hand-pointer mr-2"></i> Pilih Soal Manual
                                        </button>
                                        <button class="btn btn-warning shadow-sm mx-1" data-toggle="modal" data-target="#modalRegenerate">
                                            <i class="fas fa-random mr-2"></i> Ambil Acak
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Link Bank -->
<div class="modal fade" id="modalLinkBank" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-link mr-2"></i> Kaitkan Bank Soal</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('jadwal-ujian.link-bank-soal', $jadwalUjian->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-dark">Pilih Bank Soal Tersedia</label>
                        <select name="bank_soal_id" class="form-control" required style="width: 100%;">
                            <option value="">-- Pilih Bank Soal --</option>
                            @foreach($bankSoals as $b)
                                <option value="{{ $b->id }}">{{ $b->kode }} - {{ $b->nama }} ({{ $b->mataPelajaran->nama }})</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted mt-2">Hanya menampilkan bank soal yang sesuai dengan mata pelajaran ujian.</small>
                    </div>

                    <div class="custom-control custom-checkbox bg-light p-3 rounded border">
                        <input type="checkbox" class="custom-control-input" id="generate_auto" name="generate_auto" value="1" checked>
                        <label class="custom-control-label font-weight-bold text-primary ml-2 pt-1" for="generate_auto" style="cursor: pointer;">
                            Langsung ambil {{ $jadwalUjian->jumlah_soal }} soal secara acak
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">Simpan Kaitan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Add Manual -->
<div class="modal fade" id="modalAdd" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-white border-bottom pb-3 pt-4 px-4">
                <h5 class="modal-title font-weight-bold text-dark"><i class="fas fa-plus-circle text-primary mr-2"></i> Tambah Soal Manual</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0 bg-light">
                @if($jadwalUjian->bank_soal_id)
                    <div class="bg-primary text-white px-4 py-2 small d-flex justify-content-between align-items-center">
                        <span>Sumber: <strong>{{ $jadwalUjian->bankSoal->nama }}</strong></span>
                        <span><i class="fas fa-database"></i></span>
                    </div>
                    <div class="p-3 border-bottom bg-white">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-search text-muted"></i></span>
                            </div>
                            <input type="text" id="searchSoal" class="form-control border-left-0 pl-0" placeholder="Cari pertanyaan, tipe, atau kesulitan...">
                        </div>
                    </div>

                    <div id="soalContainer" class="list-group list-group-flush" style="max-height: 50vh; overflow-y: auto;">
                        <div class="text-center py-5">
                            <i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i>
                            <p class="text-muted">Memuat data soal...</p>
                        </div>
                    </div>

                    <div id="paginationContainer" class="p-3 bg-white border-top d-flex justify-content-center">
                        <!-- Pagination links will be inserted here -->
                    </div>
                @else
                    <div class="p-5 text-center">
                        <i class="fas fa-link fa-4x text-muted mb-4" style="opacity: 0.3;"></i>
                        <h4 class="text-dark">Ops, Bank Soal Belum Ada</h4>
                        <p class="text-muted mb-4">Anda harus mengaitkan jadwal dengan bank soal terlebih dahulu sebelum bisa menambahkan soal secara manual.</p>
                        <button class="btn btn-primary px-4 shadow-sm" data-dismiss="modal" data-toggle="modal" data-target="#modalLinkBank">
                            Kaitkan Bank Soal Sekarang
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Regenerate -->
<div class="modal fade" id="modalRegenerate" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            @if($jadwalUjian->bank_soal_id)
                <div class="modal-header bg-warning border-0 pt-4 pb-3 px-4">
                    <h5 class="modal-title font-weight-bold text-dark"><i class="fas fa-sync-alt mr-2"></i> Regenerate Komposisi</h5>
                    <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('jadwal-ujian.regenerate-soal', $jadwalUjian->id) }}" method="POST">
                    @csrf
                    <div class="modal-body p-4 bg-light">
                        <div class="alert alert-danger border-0 shadow-sm mb-4">
                            <div class="d-flex">
                                <i class="fas fa-exclamation-circle fa-2x mr-3 mt-1"></i>
                                <div>
                                    <strong>PERINGATAN!</strong>
                                    <p class="mb-0 small mt-1">Tindakan ini akan <strong>menghapus</strong> susunan sebelumnya dan mengganti total soal di jadwal ujian sesuai dengan jumlah yang Anda tentukan di bawah ini.</p>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body">
                                <h6 class="font-weight-bold text-center border-bottom pb-2 mb-3">Tentukan Komposisi Tingkat Kesulitan</h6>
                                <div class="row">
                                    <div class="col-4 text-center">
                                        <label class="text-success font-weight-bold mb-1"><i class="fas fa-star-half-alt"></i> Mudah</label>
                                        <input type="number" name="jml_mudah" class="form-control text-center font-weight-bold" value="0" min="0">
                                    </div>
                                    <div class="col-4 text-center">
                                        <label class="text-warning font-weight-bold mb-1"><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i> Sedang</label>
                                        <input type="number" name="jml_sedang" class="form-control text-center font-weight-bold" value="0" min="0">
                                    </div>
                                    <div class="col-4 text-center">
                                        <label class="text-danger font-weight-bold mb-1"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i> Sulit</label>
                                        <input type="number" name="jml_sulit" class="form-control text-center font-weight-bold" value="0" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="custom-control custom-switch pl-4">
                            <input type="checkbox" class="custom-control-input" id="acak_urutan" name="acak_urutan" checked>
                            <label class="custom-control-label font-weight-bold text-dark pt-1 pl-2" style="cursor: pointer;" for="acak_urutan">Acak urutan kemunculan soal</label>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 py-3 bg-white">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning font-weight-bold shadow-sm px-4">
                            <i class="fas fa-bolt mr-1"></i> Eksekusi Regenerate
                        </button>
                    </div>
                </form>
            @else
                <div class="modal-header border-0 px-4 pt-4">
                    <h5 class="modal-title font-weight-bold text-dark"><i class="fas fa-sync-alt mr-2 text-warning"></i> Regenerate Komposisi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center p-5">
                     <i class="fas fa-database fa-4x text-muted mb-4" style="opacity: 0.3;"></i>
                    <h5 class="text-dark">Bank Soal Belum Ditetapkan</h5>
                    <p class="text-muted mb-4">Anda harus mengaitkan jadwal dengan bank soal terlebih dahulu.</p>
                    <button class="btn btn-primary px-4 shadow-sm" data-dismiss="modal" data-toggle="modal" data-target="#modalLinkBank">
                        Kaitkan Sekarang
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Sortable JS ---
        var el = document.getElementById('sortableSoal');
        if(el) {
            var sortable = Sortable.create(el, {
                handle: '.handle',
                animation: 200,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onEnd: function (evt) {
                    var order = [];
                    $('#sortableSoal tr').each(function(index, element) {
                        order.push($(this).data('id'));
                        $(this).find('.handle span').text(index + 1);
                    });

                    $.ajax({
                        url: "{{ route('jadwal-ujian.reorder-soal') }}",
                        type: 'POST',
                        data: {
                            order: order,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            console.log('Order saved successfully');
                        },
                        error: function() {
                            alert('Gagal menyimpan urutan soal. Silakan refresh halaman.');
                        }
                    });
                }
            });
        }

        // --- AJAX Pagination & Search for Modal Add Manual ---
        let fetchUrl = "{{ route('jadwal-ujian.available-soal', $jadwalUjian->id) }}";

        function loadAvailableSoals(url = fetchUrl) {
            let searchQuery = $('#searchSoal').val() || '';
            let finalUrl = new URL(url, window.location.origin);
            if (searchQuery) {
                finalUrl.searchParams.set('search', searchQuery);
            }

            $('#soalContainer').html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i><p class="text-muted">Memuat data soal...</p></div>');

            $.ajax({
                url: finalUrl.toString(),
                type: 'GET',
                success: function(response) {
                    $('#soalContainer').html(response.html);
                    $('#paginationContainer').html(response.pagination);
                },
                error: function(xhr) {
                    $('#soalContainer').html('<div class="p-4 text-center text-danger">Gagal memuat soal. Coba lagi.</div>');
                    console.error('Error fetching data:', xhr);
                }
            });
        }

        // Load data when modal opens if bank soal is linked
        $('#modalAdd').on('shown.bs.modal', function () {
            if ($('#soalContainer').children().length > 0 && $('#soalContainer').find('.fa-spinner').length === 0) {
                // Already loaded, don't reload unless search changes
                return;
            }
            loadAvailableSoals();
        });

        // Search typing delay implementation
        let typingTimer;
        let doneTypingInterval = 500;

        $('#searchSoal').on('keyup', function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(function() {
                loadAvailableSoals();
            }, doneTypingInterval);
        });

        $('#searchSoal').on('keydown', function () {
            clearTimeout(typingTimer);
        });

        // Pagination Click Event (Delegate to document because links are injected)
        $(document).on('click', '#paginationContainer .pagination a', function(e) {
            e.preventDefault();
            let url = $(this).attr('href');
            loadAvailableSoals(url);
        });

        // --- AJAX Add Soal ---
        $(document).on('submit', '.add-soal-form', function(e) {
            e.preventDefault();
            let form = $(this);
            let btn = form.find('button[type="submit"]');
            let originalText = btn.html();

            btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if(response.status === 'success') {
                        // 1. Remove item from modal list with animation
                        let listItem = form.closest('.list-group-item');
                        listItem.slideUp(300, function() { $(this).remove(); });

                        // 2. Add row to main table
                        let su = response.data;
                        let s = su.soal;

                        // Determine badges
                        let diffBadge = '';
                        if(s.tingkat_kesulitan === 'mudah') diffBadge = '<span class="badge badge-success px-2 py-1">Mudah</span>';
                        else if(s.tingkat_kesulitan === 'sedang') diffBadge = '<span class="badge badge-warning px-2 py-1">Sedang</span>';
                        else diffBadge = '<span class="badge badge-danger px-2 py-1">Sulit</span>';

                        let typeBadge = '';
                        if(s.tipe_soal === 'pilihan_ganda') typeBadge = '<span class="badge badge-info"><i class="fas fa-list-ul mr-1"></i> PG</span>';
                        else if(s.tipe_soal === 'isian_singkat') typeBadge = '<span class="badge badge-warning"><i class="fas fa-keyboard mr-1"></i> Isian Singkat</span>';
                        else typeBadge = '<span class="badge badge-success"><i class="fas fa-align-left mr-1"></i> Uraian</span>';

                        let strippedText = s.pertanyaan.replace(/(<([^>]+)>)/gi, "");
                        let shortQ = strippedText.length > 120 ? strippedText.substring(0, 120) + "..." : strippedText;

                        let removeUrl = "{{ route('jadwal-ujian.remove-soal', ':id') }}".replace(':id', su.id);
                        let csrf = '{{ csrf_token() }}';

                        let newRow = `
                            <tr data-id="${su.id}" style="display:none; background-color: #e8f4f8;">
                                <td class="text-center handle">
                                    <i class="fas fa-bars mr-2"></i>
                                    <span class="font-weight-bold text-dark">${su.urutan}</span>
                                </td>
                                <td><div class="text-dark line-height-normal">${shortQ}</div></td>
                                <td>${typeBadge}</td>
                                <td class="text-center">${diffBadge}</td>
                                <td class="text-center">
                                    <form action="${removeUrl}" method="POST" class="remove-soal-form d-inline">
                                        <input type="hidden" name="_token" value="${csrf}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus dari daftar">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        `;

                        // Remove empty state if it exists
                        $('#sortableSoal').find('tr td[colspan="5"]').closest('tr').remove();

                        // Append and animate
                        let $newRow = $(newRow);
                        $('#sortableSoal').append($newRow);
                        $newRow.fadeIn(400, function() {
                            $(this).css('background-color', ''); // remove highlight
                        });

                        updateCounter();
                        recalculateOrder();
                    }
                },
                error: function(xhr) {
                    alert('Gagal menambahkan soal. Silakan coba lagi.');
                    btn.html(originalText).prop('disabled', false);
                }
            });
        });

        // --- AJAX Remove Soal ---
        $(document).on('submit', '.remove-soal-form', function(e) {
            e.preventDefault();
            if(!confirm('Hapus soal ini dari jadwal?')) return;

            let form = $(this);
            let row = form.closest('tr');
            let btn = form.find('button');

            btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
            row.css('opacity', '0.5');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if(response.status === 'success') {
                        row.fadeOut(300, function() {
                            $(this).remove();
                            recalculateOrder();
                            updateCounter();

                            // Check if empty
                            if($('#sortableSoal tr').length === 0) {
                                let emptyRow = `
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <img src="{{ asset('img/undraw_empty.svg') }}" alt="Empty" class="img-fluid mb-4 mt-2" style="max-width: 200px; opacity: 0.6;">
                                        <h5 class="text-muted font-weight-normal mb-3">Belum ada soal terpilih</h5>
                                        @if($jadwalUjian->bank_soal_id)
                                            <button class="btn btn-primary shadow-sm mx-1" data-toggle="modal" data-target="#modalAdd" onclick="$('#modalAdd').modal('show')">
                                                <i class="fas fa-hand-pointer mr-2"></i> Pilih Soal Manual
                                            </button>
                                            <button class="btn btn-warning shadow-sm mx-1" data-toggle="modal" data-target="#modalRegenerate" onclick="$('#modalRegenerate').modal('show')">
                                                <i class="fas fa-random mr-2"></i> Ambil Acak
                                            </button>
                                        @endif
                                    </td>
                                </tr>`;
                                $('#sortableSoal').html(emptyRow);
                            }
                        });

                        // If modal is open, we might want to refresh its content so the removed question becomes available again
                        if ($('#modalAdd').hasClass('show')) {
                            loadAvailableSoals();
                        }
                    }
                },
                error: function(xhr) {
                    alert('Gagal menghapus soal.');
                    btn.html('<i class="fas fa-trash-alt"></i>').prop('disabled', false);
                    row.css('opacity', '1');
                }
            });
        });

        // Helper function to update the top counter badge
        function updateCounter() {
            let count = $('#sortableSoal tr[data-id]').length;
            $('.badge-primary.px-3.py-1').text(count + ' Terpilih');
        }

        // Helper function to recalculate visible numbering without saving to DB (DB is updated on drag end)
        function recalculateOrder() {
            $('#sortableSoal tr[data-id]').each(function(index) {
                $(this).find('.handle span').text(index + 1);
            });
        }

    });
</script>
@endpush
