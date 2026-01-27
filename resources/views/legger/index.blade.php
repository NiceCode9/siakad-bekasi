@extends('layouts.app')

@section('title', 'Legger Nilai')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>Legger Nilai</h1>
            <div class="separator mb-5"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-4">Generate Legger Baru</h5>
                    <form action="{{ route('legger.generate') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Pilih Kelas</label>
                            <select name="kelas_id" class="form-control select2-single" required>
                                <option value="">Pilih...</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Pilih Semester</label>
                            <select name="semester_id" class="form-control select2-single" required>
                                <option value="">Pilih...</option>
                                @foreach($semesters as $semester)
                                    <option value="{{ $semester->id }}">{{ $semester->tahunAkademik->tahun }} - {{ $semester->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">GENERATE LEGGER</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-4">Daftar Legger Tersedia</h5>
                    <table class="data-table data-table-feature">
                        <thead>
                            <tr>
                                <th>Kelas</th>
                                <th>Semester</th>
                                <th>Tanggal Generate</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leggers as $l)
                            <tr>
                                <td>{{ $l->kelas->nama }}</td>
                                <td>{{ $l->semester->nama }}</td>
                                <td>{{ $l->tanggal_generate->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('legger.show', $l->id) }}" class="btn btn-xs btn-outline-primary">Lihat</a>
                                    <a href="{{ route('legger.excel', $l->id) }}" class="btn btn-xs btn-outline-success">Excel</a>
                                    <a href="{{ route('legger.pdf', $l->id) }}" class="btn btn-xs btn-outline-danger">PDF</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
