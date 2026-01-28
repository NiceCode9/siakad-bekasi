@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <h1>Pengaturan Sistem</h1>
            <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                <ol class="breadcrumb pt-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Pengaturan</li>
                </ol>
            </nav>
            <div class="separator mb-5"></div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('admin.pengaturan.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <ul class="nav nav-tabs separator-tabs ml-0 mb-5" role="tablist">
                            @foreach ($pengaturan as $kategori => $items)
                                <li class="nav-item">
                                    <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="{{ Str::slug($kategori) }}-tab"
                                        data-toggle="tab" href="#{{ Str::slug($kategori) }}" role="tab"
                                        aria-controls="{{ Str::slug($kategori) }}"
                                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ $kategori }}</a>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content">
                            @foreach ($pengaturan as $kategori => $items)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                    id="{{ Str::slug($kategori) }}" role="tabpanel"
                                    aria-labelledby="{{ Str::slug($kategori) }}-tab">
                                    
                                    <div class="row">
                                        <div class="col-12 col-lg-8">
                                            @foreach ($items as $item)
                                                <div class="form-group mb-4">
                                                    <label class="font-weight-bold">{{ str_replace('_', ' ', ucwords($item->kunci, '_')) }}</label>
                                                    
                                                    @if ($item->deskripsi)
                                                        <p class="text-muted text-small mb-2">{{ $item->deskripsi }}</p>
                                                    @endif

                                                    @if ($item->kunci === 'logo_sekolah')
                                                        <div class="d-flex align-items-center">
                                                            @if ($item->nilai)
                                                                <img src="{{ asset('storage/' . $item->nilai) }}" 
                                                                     class="img-thumbnail mr-3" 
                                                                     style="height: 80px; width: 80px; object-fit: contain;" 
                                                                     alt="Logo">
                                                            @else
                                                                <div class="bg-light d-flex align-items-center justify-center mr-3 rounded border" 
                                                                     style="height: 80px; width: 80px;">
                                                                    <i class="simple-icon-picture text-muted" style="font-size: 2rem;"></i>
                                                                </div>
                                                            @endif
                                                            <div class="custom-file">
                                                                <input type="file" class="custom-file-input" id="logo_sekolah" name="logo_sekolah">
                                                                <label class="custom-file-label" for="logo_sekolah">Pilih file...</label>
                                                            </div>
                                                        </div>
                                                    @elseif($item->tipe === 'boolean')
                                                        <div class="custom-switch custom-switch-primary mb-2">
                                                            <input type="hidden" name="{{ $item->kunci }}" value="0">
                                                            <input class="custom-switch-input" id="switch-{{ $item->kunci }}" 
                                                                   type="checkbox" name="{{ $item->kunci }}" value="1" 
                                                                   {{ $item->nilai == '1' ? 'checked' : '' }}>
                                                            <label class="custom-switch-btn" for="switch-{{ $item->kunci }}"></label>
                                                        </div>
                                                    @elseif($item->tipe === 'number')
                                                        <input type="number" name="{{ $item->kunci }}" value="{{ $item->nilai }}" 
                                                               class="form-control" placeholder="{{ str_replace('_', ' ', ucwords($item->kunci, '_')) }}">
                                                    @else
                                                        <input type="text" name="{{ $item->kunci }}" value="{{ $item->nilai }}" 
                                                               class="form-control" placeholder="{{ str_replace('_', ' ', ucwords($item->kunci, '_')) }}">
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 border-top pt-4">
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                <i class="simple-icon-check mr-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Update file input label
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    </script>
@endpush
