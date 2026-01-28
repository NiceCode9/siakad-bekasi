@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">Log Aktivitas & Audit Trail</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">Memantau seluruh aktivitas sistem dan perubahan data.</p>
        </div>
        <div>
            <form action="{{ route('admin.log-aktivitas.clear') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus seluruh log? Tindakan ini tidak dapat dibatalkan.')">
                @csrf
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm transition-colors shadow-sm flex items-center gap-2">
                    <i class="fas fa-trash"></i>
                    Bersihkan Log
                </button>
            </form>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6">
        <form action="{{ route('admin.log-aktivitas.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Cari User</label>
                <input type="text" name="user" value="{{ request('user') }}" placeholder="Username..." class="w-full px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Tabel</label>
                <select name="tabel" class="w-full px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">Semua Tabel</option>
                    @foreach($tables as $table)
                        <option value="{{ $table }}" {{ request('tabel') == $table ? 'selected' : '' }}>{{ $table }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Aktivitas</label>
                <input type="text" name="aktivitas" value="{{ request('aktivitas') }}" placeholder="Aktivitas (create, update...)" class="w-full px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm flex-1">
                    Filter
                </button>
                <a href="{{ route('admin.log-aktivitas.index') }}" class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">User</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Aktivitas</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Tabel</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">IP Address</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                            {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y H:i:s') }}
                            <div class="text-xs text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 text-xs font-bold">
                                    {{ substr($log->user->username ?? '?', 0, 1) }}
                                </div>
                                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $log->user->username ?? 'System' }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $badgeColor = 'bg-gray-100 text-gray-700';
                                if (strpos($log->aktivitas, 'create') !== false) $badgeColor = 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
                                elseif (strpos($log->aktivitas, 'update') !== false) $badgeColor = 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
                                elseif (strpos($log->aktivitas, 'delete') !== false) $badgeColor = 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $badgeColor }}">
                                {{ $log->aktivitas }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-500 dark:text-gray-400 font-mono">{{ $log->tabel }} #{{ $log->tabel_id }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 font-mono">
                            {{ $log->ip_address }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button onclick="showDetails({{ $log->id }})" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">Belum ada log aktivitas yang tercatat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Details Modal (Simplified approach using JS and Hidden inputs) -->
<div id="logModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center p-4 z-50">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-semibold dark:text-white">Detail Audit Trail</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-6 overflow-y-auto" id="modalContent">
            <!-- Content filled by AJAX or static JSON -->
            <div id="modalLoading" class="flex justify-center p-10 hidden">
                <i class="fas fa-spinner fa-spin text-2xl text-blue-500"></i>
            </div>
            <div id="modalData" class="space-y-4">
                <div>
                    <h4 class="text-xs font-bold text-gray-400 uppercase mb-2">Metadata</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm bg-gray-50 dark:bg-gray-900/50 p-3 rounded-lg">
                        <div><span class="text-gray-500">Waktu:</span> <span id="logTime" class="dark:text-gray-300"></span></div>
                        <div><span class="text-gray-500">User:</span> <span id="logUser" class="dark:text-gray-300"></span></div>
                        <div><span class="text-gray-500">IP:</span> <span id="logIp" class="dark:text-gray-300"></span></div>
                        <div><span class="text-gray-500">UA:</span> <span id="logUa" class="dark:text-gray-300 truncate block" title=""></span></div>
                    </div>
                </div>
                <div id="diffContainer" class="space-y-4">
                    <!-- JSON Diffs -->
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase mb-2">Data Lama</h4>
                        <pre id="oldData" class="text-xs bg-red-50 dark:bg-red-900/10 text-red-700 dark:text-red-400 p-3 rounded-lg overflow-x-auto font-mono"></pre>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase mb-2">Data Baru</h4>
                        <pre id="newData" class="text-xs bg-green-50 dark:bg-green-900/10 text-green-700 dark:text-green-400 p-3 rounded-lg overflow-x-auto font-mono"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showDetails(id) {
        const modal = document.getElementById('logModal');
        const loading = document.getElementById('modalLoading');
        const data = document.getElementById('modalData');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        loading.classList.remove('hidden');
        data.classList.add('hidden');

        fetch(`/admin/log-aktivitas/${id}`)
            .then(response => response.json())
            .then(log => {
                document.getElementById('logTime').textContent = log.created_at;
                document.getElementById('logUser').textContent = log.user ? log.user.username : 'System';
                document.getElementById('logIp').textContent = log.ip_address;
                document.getElementById('logUa').textContent = log.user_agent;
                document.getElementById('logUa').title = log.user_agent;

                const parseJson = (str) => {
                    try { return JSON.stringify(JSON.parse(str), null, 2); }
                    catch(e) { return str || '-'; }
                };

                document.getElementById('oldData').textContent = parseJson(log.data_lama);
                document.getElementById('newData').textContent = parseJson(log.data_baru);

                loading.classList.add('hidden');
                data.classList.remove('hidden');
            });
    }

    function closeModal() {
        const modal = document.getElementById('logModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Close on click outside
    window.onclick = function(event) {
        const modal = document.getElementById('logModal');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>
@endpush
@endsection
