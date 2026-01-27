<!DOCTYPE html>
<html>
<head>
    <title>LEGGER NILAI - {{ $legger->kelas->nama }}</title>
    <style>
        body { font-family: sans-serif; font-size: 9pt; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .table-legger { width: 100%; border-collapse: collapse; }
        .table-legger th, .table-legger td { border: 1px solid #000; padding: 4px; }
        .table-legger th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .footer { margin-top: 30px; width: 100%; }
        .footer td { width: 33%; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0">LEGGER NILAI HASIL BELAJAR SISWA</h2>
        <h4 style="margin:0">Kelas: {{ $legger->kelas->nama }} | Semester: {{ $legger->semester->nama }}</h4>
        <p style="margin:5px 0">Tahun Pelajaran: {{ $legger->semester->tahunAkademik->tahun }}</p>
    </div>

    <table class="table-legger">
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">NIS</th>
                <th rowspan="2">Nama Siswa</th>
                <th colspan="{{ $subjects->count() }}">Mata Pelajaran</th>
                <th rowspan="2">Rerata</th>
                <th rowspan="2">Rank</th>
                <th colspan="3">Absensi</th>
            </tr>
            <tr>
                @foreach($subjects as $mps)
                    <th>{{ $mps->mataPelajaran->kode }}</th>
                @endforeach
                <th>S</th>
                <th>I</th>
                <th>A</th>
            </tr>
        </thead>
        <tbody>
            @foreach($raports as $idx => $raport)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td class="text-center">{{ $raport->siswa->nis }}</td>
                <td class="text-left">{{ $raport->siswa->nama }}</td>
                
                @php 
                    $totalNilai = 0;
                    $countNilai = 0;
                @endphp
                
                @foreach($subjects as $mps)
                    @php 
                        $detail = $raport->raportDetail->where('mata_pelajaran_id', $mps->mata_pelajaran_id)->first();
                        $nilai = $detail ? round($detail->nilai_akhir) : 0;
                        if($nilai > 0) {
                            $totalNilai += $nilai;
                            $countNilai++;
                        }
                    @endphp
                    <td class="text-center">{{ $nilai > 0 ? $nilai : '-' }}</td>
                @endforeach

                <td class="text-center"><strong>{{ round($raport->average_score, 2) }}</strong></td>
                <td class="text-center">{{ $raport->ranking }}</td>
                <td class="text-center">{{ $raport->jumlah_sakit }}</td>
                <td class="text-center">{{ $raport->jumlah_izin }}</td>
                <td class="text-center">{{ $raport->jumlah_alpha }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 15px;">
        <p><small>Keterangan Kode Mapel:</small></p>
        <table style="width: 100%; font-size: 8pt;">
            <tr>
                @foreach($subjects->chunk(ceil($subjects->count() / 3)) as $chunk)
                <td style="vertical-align: top; width: 33%;">
                    @foreach($chunk as $mps)
                        <strong>{{ $mps->mataPelajaran->kode }}</strong>: {{ $mps->mataPelajaran->nama }}<br>
                    @endforeach
                </td>
                @endforeach
            </tr>
        </table>
    </div>

    <table class="footer">
        <tr>
            <td>
                Mengetahui,<br>Kepala Sekolah
                <br><br><br><br>
                <strong>................................</strong>
            </td>
            <td></td>
            <td>
                Bogor, {{ date('d F Y') }}<br>Wali Kelas
                <br><br><br><br>
                <strong>{{ $legger->kelas->waliKelas->nama ?? '................................' }}</strong>
            </td>
        </tr>
    </table>
</body>
</html>
