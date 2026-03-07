@extends('layouts.app')

@section('title', 'Mata Pelajaran Saya')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0">Mata Pelajaran Saya</h4>
                <p class="text-muted mb-0">Guru: {{ $guru->nama_lengkap }}</p>
            </div>
        </div>

        <!-- Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('teacher.my-subjects') }}" method="GET" class="row align-items-end">
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label>Filter Tahun Akademik</label>
                            <select name="tahun_akademik_id" class="form-control select2-single">
                                <option value="">Semua Tahun Akademik</option>
                                @foreach($tahunAkademiks as $ta)
                                    <option value="{{ $ta->id }}" {{ request('tahun_akademik_id') == $ta->id ? 'selected' : '' }}>
                                        {{ $ta->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label>Cari (Mapel / Kelas)</label>
                            <input type="text" name="search" class="form-control" placeholder="Masukkan nama mapel atau kelas..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    @if(request('tahun_akademik_id') || request('search'))
                        <div class="col-md-1">
                            <a href="{{ route('teacher.my-subjects') }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Assignments List -->
        @forelse($assignments as $period => $items)
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="font-weight-bold card-title"><i class="fas fa-calendar-check mr-2"></i> Periode: {{ $period }}</h6>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Kode Mapel</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Kelas</th>
                                    <th>Jam/Minggu</th>
                                    <th>KKM</th>
                                    <th>Capaian Kompetensi</th>
                                    <th width="12%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><span class="badge badge-info">{{ $item->mataPelajaran->kode }}</span></td>
                                        <td><strong>{{ $item->mataPelajaran->nama }}</strong></td>
                                        <td>{{ $item->kelas->nama }}</td>
                                        <td>{{ $item->jam_per_minggu }} JP</td>
                                        <td>
                                            <span id="kkm-display-{{ $item->id }}" class="badge badge-success" style="font-size: 14px;">
                                                {{ $item->kkm ?? '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-info btn-sm view-kompetensi-btn"
                                                data-id="{{ $item->id }}"
                                                data-mapel="{{ $item->mataPelajaran->nama }}"
                                                data-kelas="{{ $item->kelas->nama }}"
                                                data-ca="{{ $item->capaian_kompetensi_a }}"
                                                data-cb="{{ $item->capaian_kompetensi_b }}"
                                                data-cc="{{ $item->capaian_kompetensi_c }}"
                                                data-cd="{{ $item->capaian_kompetensi_d }}"
                                                title="Lihat Capaian Kompetensi">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-warning btn-sm edit-kkm-btn"
                                                    data-id="{{ $item->id }}"
                                                    data-mapel="{{ $item->mataPelajaran->nama }}"
                                                    data-kelas="{{ $item->kelas->nama }}"
                                                    data-kkm="{{ $item->kkm }}"
                                                    data-ca="{{ $item->capaian_kompetensi_a }}"
                                                    data-cb="{{ $item->capaian_kompetensi_b }}"
                                                    data-cc="{{ $item->capaian_kompetensi_c }}"
                                                    data-cd="{{ $item->capaian_kompetensi_d }}"
                                                    title="Edit Data Mapel">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="{{ route('jadwal-pelajaran.by-kelas', $item->kelas_id) }}" class="btn btn-info btn-sm" title="Lihat Jadwal">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </a>
                                                <a href="{{ route('nilai.index', ['kelas_id' => $item->kelas_id, 'mata_pelajaran_id' => $item->mata_pelajaran_id]) }}" class="btn btn-primary btn-sm" title="Input Nilai">
                                                    <i class="fas fa-list-ol"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-3">
                            {{ $assignmentsList->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i> Anda belum memiliki penugasan mata pelajaran pada periode ini.
            </div>
        @endforelse
    </div>

    <!-- Modal Edit KKM -->
    <div class="modal fade" id="editKkmModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit KKM</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editKkmForm">
                    @csrf
                    <input type="hidden" name="id" id="edit-id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Mata Pelajaran</label>
                            <input type="text" id="edit-mapel" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label>Kelas</label>
                            <input type="text" id="edit-kelas" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label>Nilai KKM <span class="text-danger">*</span></label>
                            <input type="number" name="kkm" id="edit-kkm" class="form-control" min="0" max="100" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label>Capaian - Sangat Baik (Predikat A) <span class="text-danger">*</span></label>
                            <textarea name="capaian_kompetensi_a" id="edit-ca" class="form-control" rows="2" 
                                placeholder="Contoh: Sangat mampu memahami materi..." required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Capaian - Baik (Predikat B) <span class="text-danger">*</span></label>
                            <textarea name="capaian_kompetensi_b" id="edit-cb" class="form-control" rows="2" 
                                placeholder="Contoh: Mampu memahami materi..." required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Capaian - Cukup (Predikat C) <span class="text-danger">*</span></label>
                            <textarea name="capaian_kompetensi_c" id="edit-cc" class="form-control" rows="2" 
                                placeholder="Contoh: Cukup mampu memahami materi..." required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Capaian - Kurang (Predikat D) <span class="text-danger">*</span></label>
                            <textarea name="capaian_kompetensi_d" id="edit-cd" class="form-control" rows="2" 
                                placeholder="Contoh: Kurang mampu memahami materi..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal View Kompetensi -->
    <div class="modal fade" id="viewKompetensiModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Capaian Kompetensi</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <h6 class="font-weight-bold mb-1" id="view-mapel-title"></h6>
                        <p class="text-muted small" id="view-kelas-title"></p>
                    </div>
                    <hr>
                    <div id="view-kompetensi-content" class="p-3 bg-light rounded shadow-sm" style="white-space: pre-wrap; line-height: 1.6; font-size: 16px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Handle edit button click
            $('.edit-kkm-btn').on('click', function() {
                const id = $(this).data('id');
                const mapel = $(this).data('mapel');
                const kelas = $(this).data('kelas');
                const kkm = $(this).data('kkm');

                $('#edit-id').val(id);
                $('#edit-mapel').val(mapel);
                $('#edit-kelas').val(kelas);
                $('#edit-kkm').val(kkm);
                $('#edit-ca').val($(this).data('ca'));
                $('#edit-cb').val($(this).data('cb'));
                $('#edit-cc').val($(this).data('cc'));
                $('#edit-cd').val($(this).data('cd'));

                $('#editKkmModal').modal('show');
            });

            // Handle view competency button click
            $(document).on('click', '.view-kompetensi-btn', function() {
                const mapel = $(this).data('mapel');
                const kelas = $(this).data('kelas');

                $('#view-mapel-title').text(mapel);
                $('#view-kelas-title').text('Kelas: ' + kelas);
                
                let contentHTML = `
                    <div class="mb-2"><strong>Sangat Baik (A):</strong><br>${$(this).data('ca') || '<em class="text-muted">Kosong</em>'}</div>
                    <div class="mb-2"><strong>Baik (B):</strong><br>${$(this).data('cb') || '<em class="text-muted">Kosong</em>'}</div>
                    <div class="mb-2"><strong>Cukup (C):</strong><br>${$(this).data('cc') || '<em class="text-muted">Kosong</em>'}</div>
                    <div class="mb-0"><strong>Kurang (D):</strong><br>${$(this).data('cd') || '<em class="text-muted">Kosong</em>'}</div>
                `;
                
                $('#view-kompetensi-content').html(contentHTML);
                $('#viewKompetensiModal').modal('show');
            });

            // Handle form submission
            $('#editKkmForm').on('submit', function(e) {
                e.preventDefault();
                const id = $('#edit-id').val();
                const kkm = $('#edit-kkm').val();
                const url = `{{ route('teacher.update-kkm', ':id') }}`.replace(':id', id);

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        kkm: kkm,
                        capaian_kompetensi_a: $('#edit-ca').val(),
                        capaian_kompetensi_b: $('#edit-cb').val(),
                        capaian_kompetensi_c: $('#edit-cc').val(),
                        capaian_kompetensi_d: $('#edit-cd').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            $(`#kkm-display-${id}`).text(response.kkm);
                            
                            // Update attributes on the button too
                            const btnEdit = $(`.edit-kkm-btn[data-id="${id}"]`);
                            btnEdit.data('kkm', response.kkm);
                            btnEdit.data('ca', response.capaian_kompetensi_a);
                            btnEdit.data('cb', response.capaian_kompetensi_b);
                            btnEdit.data('cc', response.capaian_kompetensi_c);
                            btnEdit.data('cd', response.capaian_kompetensi_d);
                            
                            const btnView = $(`.view-kompetensi-btn[data-id="${id}"]`);
                            btnView.data('ca', response.capaian_kompetensi_a);
                            btnView.data('cb', response.capaian_kompetensi_b);
                            btnView.data('cc', response.capaian_kompetensi_c);
                            btnView.data('cd', response.capaian_kompetensi_d);

                            $('#editKkmModal').modal('hide');

                            // Show success message (using simple alert if no sweetalert available)
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('Berhasil', response.message, 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                alert(response.message);
                            }
                        }
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan';
                        alert(message);
                    }
                });
            });
        });
    </script>
@endpush
