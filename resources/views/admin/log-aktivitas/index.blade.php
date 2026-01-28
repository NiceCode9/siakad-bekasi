@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="mb-2">
                <h1>Log Aktivitas & Audit Trail</h1>
                <div class="top-right-button-container">
                    <form action="{{ route('admin.log-aktivitas.clear') }}" method="POST" 
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus seluruh log? Tindakan ini tidak dapat dibatalkan.')">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm shadow-sm">
                            <i class="simple-icon-trash mr-1"></i> Bersihkan Log
                        </button>
                    </form>
                </div>
                <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                    <ol class="breadcrumb pt-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Log Aktivitas</li>
                    </ol>
                </nav>
            </div>
            <div class="separator mb-5"></div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-4">Filter Log</h5>
                    <form action="{{ route('admin.log-aktivitas.index') }}" method="GET" class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="text-small text-muted text-uppercase font-weight-bold">Cari User</label>
                                <input type="text" name="user" value="{{ request('user') }}" 
                                       placeholder="Username..." class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="text-small text-muted text-uppercase font-weight-bold">Tabel</label>
                                <select name="tabel" class="form-control select2">
                                    <option value="">Semua Tabel</option>
                                    @foreach($tables as $table)
                                        <option value="{{ $table }}" {{ request('tabel') == $table ? 'selected' : '' }}>{{ $table }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="text-small text-muted text-uppercase font-weight-bold">Aktivitas</label>
                                <input type="text" name="aktivitas" value="{{ request('aktivitas') }}" 
                                       placeholder="Aktivitas (create, update...)" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-group w-100">
                                <button type="submit" class="btn btn-primary btn-block shadow-sm">
                                    <i class="simple-icon-magnifier mr-1"></i> Filter
                                </button>
                                <a href="{{ route('admin.log-aktivitas.index') }}" class="btn btn-outline-secondary btn-block mt-2">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-top-0 pt-4 pb-4 pl-4 text-muted text-uppercase text-small">Waktu</th>
                                    <th class="border-top-0 pt-4 pb-4 text-muted text-uppercase text-small">User</th>
                                    <th class="border-top-0 pt-4 pb-4 text-muted text-uppercase text-small">Aktivitas</th>
                                    <th class="border-top-0 pt-4 pb-4 text-muted text-uppercase text-small">Tabel</th>
                                    <th class="border-top-0 pt-4 pb-4 text-muted text-uppercase text-small">IP Address</th>
                                    <th class="border-top-0 pt-4 pb-4 pr-4 text-center text-muted text-uppercase text-small">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                <tr>
                                    <td class="pl-4">
                                        <p class="mb-0 text-small font-weight-bold">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y H:i:s') }}</p>
                                        <p class="text-muted text-extra-small mb-0">{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</p>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-primary text-white br-round mr-2 d-flex align-items-center justify-content-center" 
                                                 style="width: 30px; height: 30px; border-radius: 50%; font-size: 10px;">
                                                {{ substr($log->user->username ?? '?', 0, 1) }}
                                            </div>
                                            <span class="text-small">{{ $log->user->username ?? 'System' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $badgeColor = 'badge-secondary';
                                            if (strpos($log->aktivitas, 'create') !== false) $badgeColor = 'badge-success';
                                            elseif (strpos($log->aktivitas, 'update') !== false) $badgeColor = 'badge-info';
                                            elseif (strpos($log->aktivitas, 'delete') !== false) $badgeColor = 'badge-danger';
                                        @endphp
                                        <span class="badge badge-pill {{ $badgeColor }}">
                                            {{ $log->aktivitas }}
                                        </span>
                                    </td>
                                    <td>
                                        <code class="text-small">{{ $log->tabel }} #{{ $log->tabel_id }}</code>
                                    </td>
                                    <td class="text-small text-muted">
                                        {{ $log->ip_address }}
                                    </td>
                                    <td class="text-center pr-4">
                                        <button onclick="showDetails({{ $log->id }})" class="btn btn-outline-primary btn-xs icon-button">
                                            <i class="simple-icon-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center p-5 text-muted">Belum ada log aktivitas yang tercatat.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($logs->hasPages())
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-center">
                        {{ $logs->appends(request()->input())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Details Modal -->
    <div class="modal fade" id="logModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Audit Trail</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="modalLoading" class="text-center p-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <div id="modalData" class="d-none">
                        <h6 class="mb-3 text-muted">METADATA</h6>
                        <div class="row bg-light rounded p-3 mb-4 mx-0 mr-lg-1">
                            <div class="col-sm-6 mb-2">
                                <p class="text-muted text-small mb-0">Waktu</p>
                                <p class="mb-0 font-weight-bold" id="logTime"></p>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <p class="text-muted text-small mb-0">User</p>
                                <p class="mb-0 font-weight-bold" id="logUser"></p>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <p class="text-muted text-small mb-0">IP Address</p>
                                <p class="mb-0 font-weight-bold" id="logIp"></p>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <p class="text-muted text-small mb-0">User Agent</p>
                                <p class="mb-0 font-weight-bold text-truncate" id="logUa" title=""></p>
                            </div>
                        </div>

                        <div id="diffContainer">
                            <h6 class="mb-2 text-muted uppercase">DATA LAMA</h6>
                            <pre id="oldData" class="bg-light p-3 rounded text-small text-danger mb-4" style="max-height: 200px; overflow-y: auto; border-left: 5px solid #dc3545;"></pre>
                            
                            <h6 class="mb-2 text-muted uppercase">DATA BARU</h6>
                            <pre id="newData" class="bg-light p-3 rounded text-small text-success mb-2" style="max-height: 200px; overflow-y: auto; border-left: 5px solid #28a745;"></pre>
                        </div>
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
        function showDetails(id) {
            $('#logModal').modal('show');
            $('#modalLoading').removeClass('d-none');
            $('#modalData').addClass('d-none');

            $.get(`/admin/log-aktivitas/${id}`, function(log) {
                $('#logTime').text(log.created_at);
                $('#logUser').text(log.user ? log.user.username : 'System');
                $('#logIp').text(log.ip_address);
                $('#logUa').text(log.user_agent).attr('title', log.user_agent);

                const parseJson = (str) => {
                    if(!str || str === '[]' || str === '{}') return '-';
                    try { 
                        return JSON.stringify(JSON.parse(str), null, 2); 
                    } catch(e) { 
                        return str; 
                    }
                };

                $('#oldData').text(parseJson(log.data_lama));
                $('#newData').text(parseJson(log.data_baru));

                $('#modalLoading').addClass('d-none');
                $('#modalData').removeClass('d-none');
            });
        }
    </script>
@endpush
