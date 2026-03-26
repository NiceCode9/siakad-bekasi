@extends('layouts.app')

@section('title', 'Riwayat Pengumuman Resmi')

@section('content')
<div class="row">
    <div class="col-12">
        <h1>Manajemen Pengumuman Resmi</h1>
        <div class="top-right-button-container">
            <a href="{{ route('admin.announcement.create') }}" class="btn btn-primary btn-lg top-right-button text-uppercase">TULIS PENGUMUMAN BARU</a>
        </div>
        <div class="separator mb-5"></div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="mb-4">Riwayat Pengumuman</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="20%">Tanggal</th>
                                <th width="25%">Judul</th>
                                <th>Isi Pengumuman</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($announcements as $notif)
                            <tr>
                                <td>{{ $notif->created_at->format('d/m/Y H:i') }}</td>
                                <td><strong>{{ $notif->judul }}</strong></td>
                                <td>{{ Str::limit($notif->pesan, 150) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center">Belum ada pengumuman yang dikirim.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $announcements->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
