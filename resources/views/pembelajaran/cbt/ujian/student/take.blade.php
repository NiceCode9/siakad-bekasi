@extends('layouts.exam')

@section('title', 'Ujian Berlangsung')

@section('content')
<div class="container-fluid user-select-none"> <!-- Prevent copy paste basic -->
    <!-- Timer Header -->
    <div class="d-flex justify-content-between align-items-center bg-white p-3 shadow-sm rounded mb-3 sticky-top" style="z-index: 1000;">
        <h5 class="mb-0 text-truncate" style="max-width: 50%;">{{ $jadwal->nama_ujian }}</h5>
        <div class="d-flex align-items-center">
            <div id="saveStatus" class="mr-3 text-muted small animate__animated" style="display: none;">
                <span class="status-icon mr-1">🔄</span> <span class="status-text">Menyimpan...</span>
            </div>
            <div class="text-danger font-weight-bold h5 mb-0" id="timerBadge">
                <i class="fas fa-stopwatch"></i> <span id="timer">Loading...</span>
            </div>
        </div>
        <form action="{{ route('ujian-siswa.finish', $jadwal->id) }}" method="POST" id="formFinish">
            @csrf
            <button type="button" class="btn btn-danger btn-sm" onclick="confirmFinish()">Selesai Ujian</button>
        </form>
    </div>

    <div class="row">
        <!-- Question Area -->
        <div class="col-md-9">
            @foreach($soalList as $index => $su)
                <div class="card shadow mb-3 question-card" id="q_{{ $su->id }}" style="{{ $index != 0 ? 'display:none' : '' }}">
                    <div class="card-header bg-white d-flex justify-content-between">
                        <span class="badge badge-primary">Soal No. {{ $index + 1 }}</span>
                        <span class="text-muted small">Bobot: {{ $su->soal->bobot }}</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-4 question-text">
                            {!! $su->soal->pertanyaan !!}

                            @if($su->soal->tipe_media)
                                <div class="mt-3 text-center">
                                    @if($su->soal->tipe_media == 'image')
                                        <img src="{{ asset('storage/'.$su->soal->file) }}" class="img-fluid rounded border" style="max-height: 400px">
                                    @elseif($su->soal->tipe_media == 'audio')
                                        <audio controls src="{{ asset('storage/'.$su->soal->file) }}" class="w-100 mt-2"></audio>
                                    @elseif($su->soal->tipe_media == 'video')
                                        <video controls src="{{ asset('storage/'.$su->soal->file) }}" class="rounded shadow-sm" style="max-height: 400px; max-width: 100%;"></video>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Options / Input -->
                        @if($su->soal->tipe_soal == 'pilihan_ganda')
                            @php
                                $opsi = ['a','b','c','d','e'];
                                $currentAnswer = $jawaban[$su->id] ?? null;
                            @endphp
                            <div class="list-group">
                                @foreach($opsi as $k)
                                    @php $field = 'opsi_'.$k; @endphp
                                    @if($su->soal->$field)
                                        <label class="list-group-item list-group-item-action">
                                            <input type="radio" name="jawaban_{{ $su->id }}" value="{{ strtoupper($k) }}"
                                                onchange="saveAnswer({{ $ujianSiswa->id }}, {{ $su->id }}, this.value)"
                                                {{ $currentAnswer == strtoupper($k) ? 'checked' : '' }}>
                                            <span class="ml-2 font-weight-bold">{{ strtoupper($k) }}.</span> {{ $su->soal->$field }}
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        @elseif($su->soal->tipe_soal == 'isian_singkat')
                             @php $currentAnswer = $jawaban[$su->id] ?? ''; @endphp
                            <div class="form-group">
                                <label>Jawaban Singkat:</label>
                                <input type="text" class="form-control" name="jawaban_{{ $su->id }}"
                                    value="{{ $currentAnswer }}"
                                    onblur="saveAnswer({{ $ujianSiswa->id }}, {{ $su->id }}, this.value)">
                            </div>
                        @elseif($su->soal->tipe_soal == 'uraian')
                            @php $currentAnswer = $jawaban[$su->id] ?? ''; @endphp
                            <div class="form-group">
                                <label>Jawaban Uraian:</label>
                                <textarea class="form-control" rows="5" name="jawaban_{{ $su->id }}"
                                    onblur="saveAnswer({{ $ujianSiswa->id }}, {{ $su->id }}, this.value)">{{ $currentAnswer }}</textarea>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-between">
                        <button class="btn btn-secondary" onclick="prevQ({{ $index }})" {{ $index == 0 ? 'disabled' : '' }}>
                            <i class="fas fa-chevron-left"></i> Sebelumnya
                        </button>

                        @if($index == count($soalList) - 1)
                            <button class="btn btn-warning" onclick="confirmFinish()">
                                Selesai <i class="fas fa-check"></i>
                            </button>
                        @else
                            <button class="btn btn-primary" onclick="nextQ({{ $index }})">
                                Selanjutnya <i class="fas fa-chevron-right"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Navigation Sidebar -->
        <div class="col-md-3 d-none d-md-block">
            <div class="card shadow">
                <div class="card-header bg-white">Navigasi Soal</div>
                <div class="card-body">
                    <div class="row no-gutters">
                        @foreach($soalList as $index => $su)
                            @php
                                $hasAnswer = isset($jawaban[$su->id]) && !empty($jawaban[$su->id]);
                            @endphp
                            <div class="col-3 p-1">
                                <button class="btn btn-block btn-sm {{ $hasAnswer ? 'btn-success' : 'btn-outline-secondary' }}"
                                    id="nav_{{ $su->id }}" onclick="jumpTo({{ $index }}, 'q_{{ $su->id }}')">
                                    {{ $index + 1 }}
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // --- Security & Timer ---
    var currentQIndex = 0;
    var totalQ = {{ count($soalList) }};
    var ids = @json($soalList->pluck('id'));
    var timeLeft = {{ $sisaDetik }};
    var ujianSiswaId = {{ $ujianSiswa->id }};
    var isFullscreen = false;

    // 1. Timer
    function startTimer() {
        var timerDisplay = document.getElementById('timer');
        setInterval(function () {
            if (timeLeft <= 0) {
                 clearInterval(this);
                 Swal.fire('Waktu Habis!', 'Jawaban Anda akan tersimpan otomatis.', 'info').then(() => {
                    document.getElementById('formFinish').submit();
                 });
                 return;
            }

            var hours = Math.floor(timeLeft / 3600);
            var minutes = Math.floor((timeLeft % 3600) / 60);
            var seconds = Math.floor(timeLeft % 60);

            timerDisplay.textContent =
                (hours < 10 ? "0" + hours : hours) + ":" +
                (minutes < 10 ? "0" + minutes : minutes) + ":" +
                (seconds < 10 ? "0" + seconds : seconds);

            // Warning color
            if(timeLeft < 300) { // < 5 mins
                document.getElementById('timerBadge').classList.add('text-danger', 'blink');
            }

            timeLeft--;
        }, 1000);
    }
    startTimer();

    // 2. Fullscreen Enforcement
    function requestFullScreen() {
        var elem = document.documentElement;
        if (elem.requestFullscreen) {
            elem.requestFullscreen().catch(err => {
                console.log('FS denied');
            });
        } else if (elem.webkitRequestFullscreen) { /* Safari */
            elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) { /* IE11 */
            elem.msRequestFullscreen();
        }
    }

    // Attempt to force FS on interaction (since auto-FS is often blocked browser-side)
    $(document).on('click', function() {
        if (!document.fullscreenElement) {
             // Optional: Force or nag
             requestFullScreen();
        }
    });

    // 3. Block Shortcuts & Context Menu & Copy Paste
    document.addEventListener('contextmenu', event => event.preventDefault());
    document.onkeydown = function(e) {
        if (e.keyCode == 123) { // F12
            return false;
        }
        if (e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) { // Ctrl+Shift+I
            return false;
        }
        if (e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) { // Ctrl+Shift+J
            return false;
        }
        if (e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) { // Ctrl+U
            return false;
        }
    }

    // CSS to disable selection
    const style = document.createElement('style');
    style.innerHTML = `
        body {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
    `;
    document.head.appendChild(style);

    // 4. Violation Detection (Tab Switch / Blur)
    var violationCount = {{ $ujianSiswa->violation_count }};

    document.addEventListener("visibilitychange", function() {
        if (document.hidden) {
            handleViolation("Meninggalkan halaman ujian (Tab Switch / Minimize)");
        }
    });

    window.onblur = function() {
        // handleViolation("Kehilangan fokus browser"); // Too sensitive? maybe. Visibility logic covers tab switch.
    };

    function handleViolation(reason) {
        violationCount++;

        // Log to server
        $.ajax({
            url: "{{ route('ujian-siswa.log-violation') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                ujian_siswa_id: ujianSiswaId
            }
        });

        Swal.fire({
            title: 'Peringatan Pelanggaran!',
            text: reason + ". Pelanggaran tercatat: " + violationCount,
            icon: 'warning',
            confirmButtonText: 'Kembali Ujian',
            allowOutsideClick: false
        }).then(() => {
            requestFullScreen();
        });
    }

    // --- Navigation ---

    function showQ(index) {
        $('.question-card').hide();
        $('.question-card').eq(index).fadeIn(200);
        currentQIndex = index;
    }

    function nextQ(curr) {
        if (curr < totalQ - 1) showQ(curr + 1);
    }

    function prevQ(curr) {
        if (curr > 0) showQ(curr - 1);
    }

    function jumpTo(index, id) {
        showQ(index);
    }

    function updateSaveStatus(status, text = "") {
        const $status = $('#saveStatus');
        const $icon = $status.find('.status-icon');
        const $text = $status.find('.status-text');

        $status.show();
        if (status === 'saving') {
            $icon.html('🔄');
            $text.text('Menyimpan...');
            $status.removeClass('text-success text-danger').addClass('text-muted');
        } else if (status === 'success') {
            $icon.html('✅');
            $text.text('Tersimpan');
            $status.removeClass('text-muted text-danger').addClass('text-success');
            setTimeout(() => $status.fadeOut(), 3000);
        } else if (status === 'error') {
            $icon.html('❌');
            $text.text(text || 'Gagal Menyimpan');
            $status.removeClass('text-muted text-success').addClass('text-danger');
        }
    }

    function saveAnswer(ujianSiswaId, soalUjianId, answer, retryCount = 0) {
        if(answer === undefined || answer === null) return;
        
        // Update navigation button color immediately
        if(answer.toString().trim() !== "") {
            $('#nav_' + soalUjianId).removeClass('btn-outline-secondary').addClass('btn-success');
        } else {
            $('#nav_' + soalUjianId).removeClass('btn-success').addClass('btn-outline-secondary');
        }

        updateSaveStatus('saving');

        $.ajax({
            url: "{{ route('ujian-siswa.save-answer') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                ujian_siswa_id: ujianSiswaId,
                soal_ujian_id: soalUjianId,
                jawaban: answer
            },
            success: function(response) {
                updateSaveStatus('success');
            },
            error: function(xhr) {
                if(xhr.status === 403) {
                    updateSaveStatus('error', 'Sesi Berakhir');
                    Swal.fire('Error', xhr.responseJSON.message, 'error').then(() => {
                        window.location.reload();
                    });
                } else {
                    // Network or Server Error - Auto Retry
                    if (retryCount < 3) {
                        updateSaveStatus('error', 'Koneksi Bermasalah. Mencoba ulang... (' + (retryCount + 1) + ')');
                        setTimeout(function() {
                            saveAnswer(ujianSiswaId, soalUjianId, answer, retryCount + 1);
                        }, 2000 * (retryCount + 1)); // Exponential backoff
                    } else {
                        updateSaveStatus('error', 'Gagal menyimpan. Cek koneksi Anda!');
                        Swal.fire({
                            title: 'Gagal Menyimpan Jawaban',
                            text: 'Sistem gagal menyimpan jawaban Anda setelah beberapa kali mencoba. Harap periksa koneksi internet Anda atau hubungi pengawas.',
                            icon: 'error',
                            confirmButtonText: 'Coba Lagi Secara Manual',
                        });
                    }
                }
            }
        });
    }

    // Periodic Save for Uraian (Textarea)
    var lastSavedAnswers = {};
    function periodicSaveUraian() {
        $('textarea[name^="jawaban_"]').each(function() {
            var nameAttr = $(this).attr('name');
            var soalUjianId = nameAttr.split('_')[1];
            var val = $(this).val();

            // Only save if content changed
            if (lastSavedAnswers[soalUjianId] !== val) {
                saveAnswer(ujianSiswaId, soalUjianId, val);
                lastSavedAnswers[soalUjianId] = val;
            }
        });
    }
    // Run every 30 seconds
    setInterval(periodicSaveUraian, 30000);

    // Initial population of lastSavedAnswers to prevent over-saving on start
    $(document).ready(function() {
        $('textarea[name^="jawaban_"]').each(function() {
             var nameAttr = $(this).attr('name');
             var soalUjianId = nameAttr.split('_')[1];
             lastSavedAnswers[soalUjianId] = $(this).val();
        });
    });

    function confirmFinish() {
        Swal.fire({
            title: 'Selesaikan Ujian?',
            text: "Anda yakin ingin mengakhiri ujian ini? Jawaban tidak dapat diubah setelah ini.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Selesai!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('formFinish').submit();
            }
        })
    }

    // Init FS prompt
    $(document).ready(function() {
        Swal.fire({
            title: 'Siap Ujian?',
            text: "Ujian akan menggunakan mode layar penuh. Dilarang berpindah tab/aplikasi.",
            icon: 'info',
            confirmButtonText: 'Mulai Kerjakan',
            allowOutsideClick: false
        }).then(() => {
             requestFullScreen();
        });
    });
</script>
@endpush
