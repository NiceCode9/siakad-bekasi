@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">Pengaturan Sistem</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">Konfigurasi profile sekolah dan parameter aplikasi.</p>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <form action="{{ route('admin.pengaturan.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="flex flex-col md:flex-row">
                <!-- Tabs Navigation -->
                <div class="w-full md:w-64 bg-gray-50 dark:bg-gray-900/50 p-4 border-b md:border-b-0 md:border-r border-gray-100 dark:border-gray-700">
                    <nav class="space-y-1">
                        @foreach($pengaturan as $kategori => $items)
                        <button type="button" 
                                onclick="showTab('tab-{{ Str::slug($kategori) }}')" 
                                class="tab-btn w-full text-left px-4 py-2.5 rounded-lg text-sm font-medium transition-colors"
                                data-tab="tab-{{ Str::slug($kategori) }}">
                            {{ $kategori }}
                        </button>
                        @endforeach
                    </nav>
                </div>

                <!-- Tabs Content -->
                <div class="flex-1 p-6 md:p-8">
                    @foreach($pengaturan as $kategori => $items)
                    <div id="tab-{{ Str::slug($kategori) }}" class="tab-content hidden animate-fade-in">
                        <h3 class="text-lg font-semibold mb-6 text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                            Pengaturan {{ $kategori }}
                        </h3>
                        
                        <div class="space-y-6">
                            @foreach($items as $item)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ str_replace('_', ' ', ucwords($item->kunci, '_')) }}
                                    </label>
                                    @if($item->deskripsi)
                                    <p class="text-xs text-gray-500 mt-1">{{ $item->deskripsi }}</p>
                                    @endif
                                </div>
                                <div class="md:col-span-2">
                                    @if($item->kunci === 'logo_sekolah')
                                        <div class="flex items-center gap-4">
                                            @if($item->nilai)
                                                <img src="{{ asset('storage/' . $item->nilai) }}" class="h-16 w-16 object-contain rounded-lg border dark:border-gray-700" alt="Logo">
                                            @else
                                                <div class="h-16 w-16 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center text-gray-400">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                            @endif
                                            <input type="file" name="logo_sekolah" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                        </div>
                                    @elseif($item->tipe === 'boolean')
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="hidden" name="{{ $item->kunci }}" value="0">
                                            <input type="checkbox" name="{{ $item->kunci }}" value="1" {{ $item->nilai == '1' ? 'checked' : '' }} class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                        </label>
                                    @elseif($item->tipe === 'number')
                                        <input type="number" name="{{ $item->kunci }}" value="{{ $item->nilai }}" class="w-full px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                                    @else
                                        <input type="text" name="{{ $item->kunci }}" value="{{ $item->nilai }}" class="w-full px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                    <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors shadow-sm">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function showTab(tabId) {
        // Hide all contents
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        // Remove active styling from all buttons
        document.querySelectorAll('.tab-btn').forEach(el => {
            el.classList.remove('bg-white', 'dark:bg-gray-800', 'text-blue-600', 'shadow-sm');
            el.classList.add('text-gray-600', 'dark:text-gray-400', 'hover:bg-gray-100', 'dark:hover:bg-gray-800/50');
        });

        // Show target content
        document.getElementById(tabId).classList.remove('hidden');
        // Add active styling to target button
        const activeBtn = document.querySelector(`[data-tab="${tabId}"]`);
        activeBtn.classList.remove('text-gray-600', 'dark:text-gray-400', 'hover:bg-gray-100', 'dark:hover:bg-gray-800/50');
        activeBtn.classList.add('bg-white', 'dark:bg-gray-800', 'text-blue-600', 'shadow-sm');
    }

    // Default tab
    document.addEventListener('DOMContentLoaded', () => {
        const firstTabId = document.querySelector('.tab-content').id;
        showTab(firstTabId);
    });
</script>
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out forwards;
    }
</style>
@endpush
@endsection
