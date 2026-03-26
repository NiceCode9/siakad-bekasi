@extends('layouts.app')

@section('title', 'Audit Trail (Log Aktivitas)')

@section('content')
<div class="row">
    <div class="col-12">
        <h1>Audit Trail / Log Aktivitas Sistem</h1>
        <div class="separator mb-5"></div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-4">Filter Log</h5>
                <form method="GET" action="{{ route('admin.logs.index') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Pengguna</label>
                                <select name="user_id" class="form-control select2-single">
                                    <option value="">-- Semua Pengguna --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->username }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tanggal</label>
                                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary d-block">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover" id="logTable">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Pengguna</th>
                                <th>Aktivitas</th>
                                <th>Modul/Tabel</th>
                                <th>Data Lama</th>
                                <th>Data Baru</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            <tr>
                                <td class="text-nowrap">{{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '-' }}</td>
                                <td><strong>{{ $log->user->username ?? 'Guest' }}</strong></td>
                                <td>{{ $log->aktivitas }}</td>
                                <td><span class="badge badge-outline-secondary">{{ $log->tabel }}</span></td>
                                <td>
                                    @if($log->data_lama)
                                        <button class="btn btn-xs btn-outline-info show-data" data-content="{{ $log->data_lama }}">Lihat</button>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($log->data_baru)
                                        <button class="btn btn-xs btn-outline-primary show-data" data-content="{{ $log->data_baru }}">Lihat</button>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td><small>{{ $log->ip_address }}</small></td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center">Tidak ada log aktivitas ditemukan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Data -->
<div class="modal fade shadow-lg" id="modalData" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <pre class="bg-light p-3 border rounded"><code id="jsonContent"></code></pre>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.show-data').on('click', function() {
            var content = $(this).data('content');
            try {
                var json = JSON.parse(JSON.stringify(content));
                if (typeof json === 'string') json = JSON.parse(json);
                $('#jsonContent').text(JSON.stringify(json, null, 4));
            } catch (e) {
                $('#jsonContent').text(content);
            }
            $('#modalData').modal('show');
        });
    });
</script>
@endpush
