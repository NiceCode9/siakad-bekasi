@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>Semua Notifikasi</h1>
            <div class="top-right-button-container">
                <form action="{{ route('notifications.markAllRead') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary btn-lg top-right-button">TANDAI SEMUA DIBACA</button>
                </form>
            </div>
            <div class="separator mb-5"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @forelse($notifications as $notif)
                <div class="card mb-3 {{ $notif->is_read ? 'opacity-75' : 'border-primary' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    @if($notif->tipe == 'success')
                                        <i class="simple-icon-check text-success" style="font-size: 24px;"></i>
                                    @elseif($notif->tipe == 'warning')
                                        <i class="simple-icon-exclamation text-warning" style="font-size: 24px;"></i>
                                    @elseif($notif->tipe == 'error')
                                        <i class="simple-icon-close text-danger" style="font-size: 24px;"></i>
                                    @else
                                        <i class="simple-icon-info text-info" style="font-size: 24px;"></i>
                                    @endif
                                </div>
                                <div>
                                    <h5 class="mb-1 {{ $notif->is_read ? '' : 'font-weight-bold' }}">{{ $notif->judul }}</h5>
                                    <p class="mb-0 text-muted">{{ $notif->pesan }}</p>
                                    <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                            <div class="d-flex">
                                @if(!$notif->is_read)
                                    <form action="{{ route('notifications.read', $notif->id) }}" method="POST" class="mr-2">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-primary">BACA</button>
                                    </form>
                                @elseif($notif->link)
                                    <a href="{{ $notif->link }}" class="btn btn-xs btn-outline-primary mr-2">LIHAT</a>
                                @endif
                                
                                <form action="{{ route('notifications.destroy', $notif->id) }}" method="POST" onsubmit="return confirm('Hapus notifikasi?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-outline-danger">HAPUS</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="simple-icon-bell text-muted mb-3 d-block" style="font-size: 48px;"></i>
                        <p class="text-muted">Tidak ada notifikasi untuk Anda.</p>
                    </div>
                </div>
            @endforelse

            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
