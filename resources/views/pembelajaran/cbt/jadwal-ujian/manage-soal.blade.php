@extends('layouts.app')

@section('title', 'Kelola Soal Ujian')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Kelola Soal: {{ $jadwalUjian->nama_ujian }}</h4>
            <small class="text-muted">{{ $jadwalUjian->mataPelajaranKelas->kelas->nama }} - {{ $jadwalUjian->mataPelajaranKelas->mataPelajaran->nama }}</small>
        </div>
        <div>
            <a href="{{ route('jadwal-ujian.show', $jadwalUjian->id) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalAdd">
                <i class="fas fa-plus"></i> Tambah Manual
            </button>
            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalRegenerate">
                <i class="fas fa-sync"></i> Regenerate (Komposisi)
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white">
            <i class="fas fa-info-circle text-info"></i> Drag & Drop baris untuk mengubah urutan soal.
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tableSoal">
                    <thead>
                        <tr>
                            <th width="5%">Urut</th>
                            <th>Soal</th>
                            <th>Tipe</th>
                            <th>Kesulitan</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="sortableSoal">
                        @foreach($jadwalUjian->soalUjian()->orderBy('urutan')->get() as $su)
                            <tr data-id="{{ $su->id }}">
                                <td class="handle" style="cursor: move;"><i class="fas fa-bars text-muted"></i> {{ $su->urutan }}</td>
                                <td>{!! Str::limit(strip_tags($su->soal->pertanyaan), 100) !!}</td>
                                <td>{{ ucfirst($su->soal->tipe_soal) }}</td>
                                <td>
                                    @if($su->soal->tingkat_kesulitan == 'mudah') <span class="badge badge-success">Mudah</span>
                                    @elseif($su->soal->tingkat_kesulitan == 'sedang') <span class="badge badge-warning">Sedang</span>
                                    @else <span class="badge badge-danger">Sulit</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('jadwal-ujian.remove-soal', $su->id) }}" method="POST" onsubmit="return confirm('Hapus soal ini dari jadwal?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-times"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Manual -->
<div class="modal fade" id="modalAdd" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Soal Manual</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p>Pilih soal dari Bank: <strong>{{ $jadwalUjian->bankSoal->nama }}</strong></p>
                <div class="list-group" style="max-height: 400px; overflow-y: auto;">
                    @php
                        // Get questions NOT already in exam
                        $existingIds = $jadwalUjian->soalUjian->pluck('soal_id')->toArray();
                        $available = $jadwalUjian->bankSoal->soal()->whereNotIn('id', $existingIds)->get();
                    @endphp
                    
                    @forelse($available as $s)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge badge-info">{{ $s->tingkat_kesulitan }}</span>
                                <span class="text-muted ml-2">{!! Str::limit(strip_tags($s->pertanyaan), 80) !!}</span>
                            </div>
                            <form action="{{ route('jadwal-ujian.add-soal', $jadwalUjian->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="soal_id" value="{{ $s->id }}">
                                <button type="submit" class="btn btn-sm btn-primary">Pilih</button>
                            </form>
                        </div>
                    @empty
                        <div class="alert alert-warning">Semua soal dari bank ini sudah dimasukkan.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Regenerate -->
<div class="modal fade" id="modalRegenerate" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('jadwal-ujian.regenerate-soal', $jadwalUjian->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Regenerate Komposisi Soal</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> Tindakan ini akan <strong>MENGHAPUS</strong> semua soal yang ada saat ini dan menggantinya dengan acak baru sesuai komposisi.
                    </div>
                    <div class="form-group">
                        <label>Jumlah Soal Mudah</label>
                        <input type="number" name="jml_mudah" class="form-control" value="0" min="0">
                    </div>
                    <div class="form-group">
                        <label>Jumlah Soal Sedang</label>
                        <input type="number" name="jml_sedang" class="form-control" value="0" min="0">
                    </div>
                    <div class="form-group">
                        <label>Jumlah Soal Sulit</label>
                        <input type="number" name="jml_sulit" class="form-control" value="0" min="0">
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="acak_urutan" name="acak_urutan" checked>
                        <label class="custom-control-label" for="acak_urutan">Acak urutan hasil generate</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Regenerate</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    var el = document.getElementById('sortableSoal');
    var sortable = Sortable.create(el, {
        handle: '.handle',
        animation: 150,
        onEnd: function (evt) {
            var order = [];
            $('#sortableSoal tr').each(function() {
                order.push($(this).data('id'));
            });

            $.ajax({
                url: "{{ route('jadwal-ujian.reorder-soal') }}",
                type: 'POST',
                data: {
                    order: order,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log('Order updated');
                    // Optional: update visual numbers
                }
            });
        }
    });
</script>
@endpush
