@extends('layouts.app')

@section('title', 'Nilai Sikap')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Nilai Sikap</h4>
            <small class="text-muted">Input nilai sikap Spiritual dan Sosial</small>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('nilai-sikap.create') }}" method="GET">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Pilih Kelas</label>
                            <select name="kelas_id" class="form-control" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                         <div class="form-group">
                            <label>Aspek Penilaian</label>
                            <select name="aspek" class="form-control" required>
                                <option value="">-- Pilih Aspek --</option>
                                <option value="spiritual">Sikap Spiritual</option>
                                <option value="sosial">Sikap Sosial</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                Input Nilai
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
