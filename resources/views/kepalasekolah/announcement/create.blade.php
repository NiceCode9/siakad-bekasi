@extends('layouts.app')

@section('title', 'Tulis Pengumuman Baru')

@section('content')
<div class="row">
    <div class="col-12">
        <h1>Tulis Pengumuman Resmi Baru</h1>
        <div class="separator mb-5"></div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 col-sm-12 offset-md-2">
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('admin.announcement.store') }}" method="POST">
                    @csrf
                    <div class="form-group mb-4">
                        <label>Judul Pengumuman</label>
                        <input type="text" name="judul" class="form-control" placeholder="Contoh: Libur Nasional" value="{{ old('judul') }}" required>
                        <small class="text-muted">Maksimal 100 karakter.</small>
                    </div>

                    <div class="form-group mb-4">
                        <label>Isi Pesan / Pengumuman</label>
                        <textarea name="pesan" class="form-control" rows="6" placeholder="Tulis rincian pengumuman di sini..." required>{{ old('pesan') }}</textarea>
                    </div>

                    <div class="form-group mb-4">
                        <label>Target Penerima</label>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="checkAll" name="target_roles[]" value="all">
                            <label class="custom-control-label font-weight-bold" for="checkAll">SEMUA PENGGUNA</label>
                        </div>
                        <div class="mt-2 ml-4">
                            @foreach($roles as $role)
                                <div class="custom-control custom-checkbox d-inline-block mr-3">
                                    <input type="checkbox" class="custom-control-input role-checkbox" id="role_{{ $role->id }}" name="target_roles[]" value="{{ $role->name }}">
                                    <label class="custom-control-label" for="role_{{ $role->id }}">{{ ucfirst($role->name) }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="text-right">
                        <a href="{{ route('admin.announcement.index') }}" class="btn btn-outline-secondary">BATAL</a>
                        <button type="submit" class="btn btn-primary ml-2">SEBARKAN PENGUMUMAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#checkAll').on('change', function() {
            $('.role-checkbox').prop('checked', this.checked);
        });

        $('.role-checkbox').on('change', function() {
            if (!this.checked) {
                $('#checkAll').prop('checked', false);
            }
            if ($('.role-checkbox:checked').length == $('.role-checkbox').length) {
                $('#checkAll').prop('checked', true);
            }
        });
    });
</script>
@endpush
