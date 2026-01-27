<!DOCTYPE html>
<html>
<head>
    <title>RAPORT - {{ $raport->siswa->nama }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11pt; color: #333; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 3px 5px; }
        .main-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .main-table th, .main-table td { border: 1px solid #000; padding: 8px; text-align: left; }
        .main-table th { background-color: #f2f2f2; text-align: center; }
        .text-center { text-align: center; }
        .section-title { font-weight: bold; margin-bottom: 10px; background: #eee; padding: 5px; border: 1px solid #000; }
        .footer { margin-top: 50px; width: 100%; }
        .footer td { width: 33%; text-align: center; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0">LAPORAN HASIL BELAJAR</h2>
        <h3 style="margin:0">(RAPORT)</h3>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%">Nama Siswa</td>
            <td width="2%">:</td>
            <td width="35%"><strong>{{ $raport->siswa->nama }}</strong></td>
            <td width="15%">Kelas</td>
            <td width="2%">:</td>
            <td width="31%">{{ $raport->kelas->nama }}</td>
        </tr>
        <tr>
            <td>NISN/NIS</td>
            <td>:</td>
            <td>{{ $raport->siswa->nisn }} / {{ $raport->siswa->nis }}</td>
            <td>Semester</td>
            <td>:</td>
            <td>{{ $raport->semester->nama }}</td>
        </tr>
        <tr>
            <td>Sekolah</td>
            <td>:</td>
            <td>SMK NEGERI CIARUTEUN ILIR</td>
            <td>Tahun Pelajaran</td>
            <td>:</td>
            <td>{{ $raport->semester->tahunAkademik->tahun }}</td>
        </tr>
    </table>

    <div class="section-title">A. Nilai Akademik</div>
    <table class="main-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="45%">Mata Pelajaran</th>
                <th width="15%">Pengetahuan</th>
                <th width="15%">Keterampilan</th>
                <th width="10%">Nilai Akhir</th>
                <th width="10%">Predikat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($raport->raportDetail as $idx => $detail)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>{{ $detail->mataPelajaran->nama }}</td>
                <td class="text-center">{{ round($detail->nilai_pengetahuan) }}</td>
                <td class="text-center">{{ round($detail->nilai_keterampilan) }}</td>
                <td class="text-center"><strong>{{ round($detail->nilai_akhir) }}</strong></td>
                <td class="text-center">{{ $detail->predikat }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">B. Praktik Kerja Lapangan</div>
    <table class="main-table">
        <thead>
            <tr>
                <th>Mitra Dunia Kerja</th>
                <th>Lokasi</th>
                <th>Lamanya (Bulan)</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @if($nilaiPkl)
            <tr>
                <td>{{ $nilaiPkl->pkl->perusahaanPkl->nama }}</td>
                <td>{{ $nilaiPkl->pkl->perusahaanPkl->alamat ?? '-' }}</td>
                <td class="text-center">3 Bulan</td>
                <td>{{ $nilaiPkl->catatan_industri ?? 'Sangat Baik' }}</td>
            </tr>
            @else
            <tr>
                <td colspan="4" class="text-center">Belum Melaksanakan PKL</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="section-title">C. Ekstrakurikuler</div>
    <table class="main-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Kegiatan Ekstrakurikuler</th>
                <th width="15%">Predikat</th>
                <th width="45%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($nilaiEkskul as $idx => $ekskul)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>{{ $ekskul->ekstrakurikuler->nama }}</td>
                <td class="text-center">{{ $ekskul->predikat }}</td>
                <td>{{ $ekskul->keterangan ?? 'Aktif mengikuti kegiatan' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">-</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="width: 100%;">
        <div style="width: 60%; float: left;">
            <div class="section-title" style="width: 90%">D. Ketidakhadiran</div>
            <table class="main-table" style="width: 90%">
                <tr>
                    <td width="60%">Sakit</td>
                    <td width="40%" class="text-center">{{ $raport->jumlah_sakit }} hari</td>
                </tr>
                <tr>
                    <td>Izin</td>
                    <td class="text-center">{{ $raport->jumlah_izin }} hari</td>
                </tr>
                <tr>
                    <td>Tanpa Keterangan</td>
                    <td class="text-center">{{ $raport->jumlah_alpha }} hari</td>
                </tr>
            </table>
        </div>
        <div style="width: 40%; float: left;">
             <div class="section-title">E. Catatan Wali Kelas</div>
             <div style="border: 1px solid #000; padding: 10px; height: 80px;">
                {{ $raport->catatan_wali_kelas }}
             </div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <table class="footer">
        <tr>
            <td>
                Mengetahui,<br>Orang Tua/Wali
                <br><br><br><br>
                (................................)
            </td>
            <td>
                Mengetahui,<br>Kepala Sekolah
                <br><br><br><br>
                <strong>{{ $raport->approvedBy->name ?? '................................' }}</strong>
            </td>
            <td>
                Bogor, {{ date('d F Y') }}<br>Wali Kelas
                <br><br><br><br>
                <strong>{{ $raport->kelas->waliKelas->nama ?? '................................' }}</strong>
            </td>
        </tr>
    </table>
</body>
</html>
