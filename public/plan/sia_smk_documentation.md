# Dokumentasi Sistem Informasi Akademik SMK

## 📋 Daftar Isi
1. [Overview Sistem](#overview-sistem)
2. [Fitur-Fitur Utama](#fitur-fitur-utama)
3. [Role & Permission](#role--permission)
4. [Teknologi Stack](#teknologi-stack)
5. [Database Schema](#database-schema)

---

## Overview Sistem

Sistem Informasi Akademik SMK adalah platform terintegrasi untuk mengelola seluruh proses akademik di Sekolah Menengah Kejuruan, mulai dari manajemen data siswa, pembelajaran, penilaian, hingga pelaporan.

### Karakteristik Sistem
- **Multi-Kurikulum**: Support Kurikulum 2013 dan Kurikulum Merdeka
- **Data Berjenjang**: Tahun Akademik → Semester (Ganjil/Genap) → Kelas
- **Multi-Role**: 7 role pengguna dengan akses berbeda
- **Penilaian Skala**: 0-100 dengan konversi predikat otomatis
- **CBT Integration**: Computer Based Test terintegrasi penuh
- **PKL Management**: Tracking Praktik Kerja Lapangan lengkap

---

## Fitur-Fitur Utama

### 1. 👤 Manajemen User & Authentikasi

#### Deskripsi
Sistem login dan manajemen user dengan 7 role berbeda yang terintegrasi dengan data guru, siswa, dan orang tua.

#### Role Pengguna
1. **Admin** - Full access ke seluruh sistem
2. **Kepala Sekolah** - Monitoring, approval, dan reporting
3. **Guru** - Mengajar, penilaian, CBT
4. **Wali Kelas** - Kelola kelas, raport, nilai sikap
5. **TU (Tata Usaha)** - Manajemen data master, buku induk
6. **Siswa** - Akses materi, ujian, nilai
7. **Orang Tua** - Monitoring nilai dan presensi anak

#### Fitur Teknis
- Login dengan username/email + password
- Password recovery via email
- Session management
- Remember me token
- Last login tracking
- Email verification (optional)

#### Tabel Database
- `users` - Data akun user
- `password_reset_tokens` - Token reset password
- `sessions` - Session management

---

### 2. 📚 Manajemen Master Data

#### 2.1 Kurikulum & Tahun Akademik

##### Deskripsi
Setup kurikulum yang digunakan dan tahun akademik beserta pembagian semesternya.

##### Fitur
- CRUD Kurikulum (K13, Kurikulum Merdeka)
- CRUD Tahun Akademik (contoh: 2024/2025)
- CRUD Semester (Ganjil/Genap)
- Set active tahun akademik dan semester
- Tracking tanggal mulai dan selesai

##### Logika Bisnis
- Hanya 1 tahun akademik yang bisa aktif
- Hanya 1 semester yang bisa aktif per tahun akademik
- Setiap tahun akademik terikat dengan 1 kurikulum
- Komponen penilaian menyesuaikan kurikulum yang dipilih

##### Tabel Database
- `kurikulum`
- `tahun_akademik`
- `semester`

---

#### 2.2 Jurusan & Kelas

##### Deskripsi
Manajemen jurusan/kompetensi keahlian dan rombongan belajar (kelas).

##### Fitur
- CRUD Jurusan (RPL, TKJ, MM, dll)
- CRUD Kelas per semester
- Assign wali kelas
- Set kuota kelas
- Tentukan ruang kelas

##### Struktur Kelas
- Tingkat: X, XI, XII
- Nama kelas: [Tingkat] [Jurusan] [Nomor]
  - Contoh: X RPL 1, XI TKJ 2

##### Logika Bisnis
- Kelas dibuat per semester
- Satu guru bisa jadi wali kelas di beberapa kelas (berbeda semester)
- Siswa masuk kelas melalui tabel `siswa_kelas`

##### Tabel Database
- `jurusan`
- `kelas`
- `siswa_kelas`

---

#### 2.3 Data Guru

##### Deskripsi
Manajemen data lengkap guru dan tenaga pendidik.

##### Data yang Dikelola
- **Identitas**: NIP, NUPTK, nama lengkap, gelar
- **Personal**: Jenis kelamin, tempat/tanggal lahir, agama
- **Kontak**: Alamat, telepon, email
- **Kepegawaian**: Status (PNS/PPPK/GTY/GTT), tanggal masuk
- **Dokumen**: Foto profil

##### Fitur
- CRUD data guru
- Link ke user account
- Upload foto
- Status aktif/non-aktif
- Filter dan pencarian

##### Logika Bisnis
- Setiap guru wajib punya user account
- Satu user hanya untuk satu guru (unique)
- Guru bisa mengajar beberapa mata pelajaran
- Guru bisa jadi wali kelas

##### Tabel Database
- `guru`
- Relasi ke `users`

---

#### 2.4 Data Siswa & Orang Tua

##### Deskripsi
Manajemen data lengkap siswa dan data orang tua/wali.

##### Data Siswa
- **Identitas**: NISN, NIS, NIK, nama lengkap
- **Personal**: Jenis kelamin, tempat/tanggal lahir, agama
- **Keluarga**: Anak ke-, jumlah saudara
- **Alamat**: Lengkap dengan RT/RW, kelurahan, kecamatan, kota
- **Kontak**: Telepon, email
- **Kesehatan**: Tinggi, berat badan, golongan darah
- **Pendidikan**: Asal sekolah, tahun lulus SMP
- **Status**: Aktif, Lulus, Pindah, Keluar, DO

##### Data Orang Tua
- Data Ayah: NIK, nama, pekerjaan, pendidikan, penghasilan, telepon
- Data Ibu: NIK, nama, pekerjaan, pendidikan, penghasilan, telepon
- Data Wali: Nama, pekerjaan, telepon (jika ada)
- Alamat lengkap

##### Fitur
- CRUD data siswa
- CRUD data orang tua
- Link siswa dengan orang tua
- Upload foto siswa
- Tracking status siswa
- Mutasi siswa (masuk/pindah/keluar)

##### Logika Bisnis
- Siswa terikat dengan kelas melalui `siswa_kelas`
- Satu siswa bisa punya riwayat di beberapa kelas (naik kelas)
- Orang tua bisa punya akun untuk monitoring
- Status siswa berubah otomatis saat lulus/pindah

##### Tabel Database
- `siswa`
- `orang_tua`
- `siswa_kelas`

---

### 3. 📖 Buku Induk Siswa

#### Deskripsi
Dokumentasi lengkap riwayat siswa selama bersekolah, termasuk prestasi, pelanggaran, dan mutasi.

#### Komponen Buku Induk

##### 3.1 Data Induk Utama
- Nomor Induk (unik per siswa)
- Nomor Peserta Ujian
- Nomor Seri Ijazah
- Nomor Seri SKHUN
- Tanggal Lulus
- Riwayat Pendidikan
- Riwayat Kesehatan
- Catatan Khusus

##### 3.2 Prestasi Siswa
**Fitur:**
- Input prestasi akademik dan non-akademik
- Kategori: Akademik, Non-Akademik, Olahraga, Seni, Lainnya
- Tingkat: Kelas, Sekolah, Kecamatan, Kota, Provinsi, Nasional, Internasional
- Upload sertifikat
- Tracking penyelenggara dan tanggal

**Logika:**
- Prestasi bisa diinput oleh Guru, Wali Kelas, TU
- History prestasi tetap tersimpan meski siswa lulus

##### 3.3 Pelanggaran Siswa
**Fitur:**
- Catat pelanggaran dengan kategori (Ringan, Sedang, Berat)
- Sistem poin pelanggaran
- Kronologi kejadian
- Sanksi yang diberikan
- Tracking status (Proses/Selesai)
- Pelapor (guru)

**Logika:**
- Akumulasi poin bisa digunakan untuk sanksi bertingkat
- Pelanggaran berat bisa mempengaruhi kenaikan kelas

##### 3.4 Mutasi Siswa
**Fitur:**
- Jenis: Masuk, Pindah, Keluar, DO, Lulus
- Tracking asal/tujuan sekolah
- Alasan mutasi
- Nomor dan file surat

**Logika:**
- Mutasi keluar otomatis update status siswa
- Semua history tetap tersimpan untuk audit

#### Akses & Permission
- **Input**: Admin, TU, Wali Kelas, Guru (terbatas)
- **View**: Admin, Kepala Sekolah, TU, Wali Kelas, Siswa (data sendiri), Ortu (data anak)
- **Edit/Delete**: Admin, TU
- **Cetak**: Admin, Kepala Sekolah, TU

#### Tabel Database
- `buku_induk`
- `prestasi_siswa`
- `pelanggaran_siswa`
- `mutasi_siswa`

---

### 4. 📝 Mata Pelajaran & Jadwal

#### 4.1 Manajemen Mata Pelajaran

##### Deskripsi
Setup mata pelajaran berdasarkan kurikulum dengan pengelompokan yang sesuai struktur SMK.

##### Kelompok Mata Pelajaran
- **Kelompok A**: Umum wajib (Matematika, B. Indonesia, dll)
- **Kelompok B**: Umum tambahan (Seni, PJOK, dll)
- **Kelompok C1**: Dasar Kejuruan
- **Kelompok C2**: Dasar Program Keahlian
- **Kelompok C3**: Kompetensi Keahlian

##### Fitur
- CRUD Mata Pelajaran per kurikulum
- Set KKM per mata pelajaran
- Kategorisasi: Wajib, Peminatan, Lintas Minat
- Jenis: Umum, Kejuruan, Muatan Lokal
- Assign mata pelajaran ke kelas
- Set jam pelajaran per minggu
- Assign guru pengampu

##### Logika Bisnis
- Mata pelajaran terikat dengan kurikulum
- Satu mapel bisa diajarkan di beberapa kelas
- Satu mapel di satu kelas bisa diampu beberapa guru (team teaching)
- KKM bisa berbeda per mapel

##### Tabel Database
- `kelompok_mapel`
- `mata_pelajaran`
- `mata_pelajaran_kelas`
- `mata_pelajaran_guru`

---

#### 4.2 Jadwal Pelajaran

##### Deskripsi
Penjadwalan mata pelajaran per kelas dengan detail waktu dan ruangan.

##### Fitur
- CRUD Jadwal per mata pelajaran guru
- Set hari (Senin - Sabtu)
- Set jam mulai dan selesai
- Tentukan ruang kelas
- Cek bentrok jadwal (guru/ruangan)

##### Logika Bisnis
- Jadwal mengacu pada `mata_pelajaran_guru`
- Validasi bentrok:
  - Guru tidak bisa mengajar di 2 kelas di waktu yang sama
  - Ruangan tidak bisa dipakai 2 kelas di waktu yang sama
- Jadwal bisa di-export untuk ditempel

##### Tabel Database
- `jadwal_pelajaran`

---

### 5. 📊 Presensi

#### Deskripsi
Sistem absensi siswa per hari atau per mata pelajaran.

#### Fitur
- Input presensi harian
- Input presensi per mata pelajaran
- Status: Hadir, Sakit, Izin, Alpha
- Keterangan tambahan
- Tracking waktu presensi
- Rekap presensi per siswa/kelas
- Notifikasi ke ortu jika alpha

#### Logika Bisnis
- Presensi bisa linked ke jadwal pelajaran (opsional)
- Akumulasi alpha mempengaruhi kenaikan kelas
- Rekap otomatis untuk raport (Sakit/Izin/Alpha)

#### Akses & Permission
- **Input**: Guru, Wali Kelas
- **View**: Admin, Kepala Sekolah, Guru, Wali Kelas, Siswa (sendiri), Ortu (anak)
- **Edit**: Guru (sama hari), Wali Kelas, Admin
- **Rekap**: Semua role (sesuai scope)

#### Tabel Database
- `presensi`

---

### 6. 🖥️ CBT (Computer Based Test)

#### Deskripsi
Sistem ujian berbasis komputer yang terintegrasi dengan bank soal dan penilaian otomatis.

#### 6.1 Bank Soal

##### Fitur
- CRUD Bank Soal per mata pelajaran
- Kategorisasi tingkat kesulitan (Mudah, Sedang, Sulit)
- Kelola soal dalam bank
- Tipe soal:
  - Pilihan Ganda (A-E)
  - Essay
  - Benar/Salah
  - Menjodohkan
- Upload gambar untuk soal
- Pembahasan soal
- Bobot per soal

##### Logika Bisnis
- Bank soal dibuat oleh guru per mata pelajaran
- Satu bank soal bisa dipakai untuk beberapa ujian
- Soal bisa di-shuffle untuk setiap ujian
- Kunci jawaban encrypted

##### Tabel Database
- `bank_soal`
- `soal`

---

#### 6.2 Jadwal & Pelaksanaan Ujian

##### Fitur Jadwal Ujian
- Buat jadwal ujian (UH, UTS, UAS, Praktik, Ujian Sekolah)
- Set tanggal & waktu mulai-selesai
- Set durasi ujian (menit)
- Pilih bank soal
- Set jumlah soal yang akan keluar
- Acak soal & acak opsi
- Generate token ujian (opsional)
- Status: Draft, Aktif, Selesai

##### Fitur Pelaksanaan
- Login siswa dengan token (jika pakai)
- Timer countdown otomatis
- Auto-save jawaban setiap beberapa detik
- Tandai soal ragu-ragu
- Navigasi antar soal
- Auto-submit saat waktu habis
- Tracking IP & user agent
- Rekam waktu mulai & selesai

##### Fitur Penilaian
- Auto-grading untuk pilihan ganda & benar/salah
- Manual grading untuk essay
- Tampilkan nilai (jika diaktifkan)
- Review jawaban siswa
- Statistik ujian (rata-rata, tertinggi, terendah)

##### Logika Bisnis
- Siswa hanya bisa mulai ujian di rentang waktu yang ditentukan
- Sekali mulai, tidak bisa keluar sampai selesai/waktu habis
- Jawaban ter-save di database per soal
- Nilai otomatis masuk ke tabel `nilai`
- Soal essay dinilai manual oleh guru

##### Monitoring & Laporan
- Real-time monitoring siswa yang sedang ujian
- Status: Belum Mulai, Sedang Mengerjakan, Selesai, Tidak Hadir
- Export hasil ujian
- Analisis soal (tingkat kesulitan, daya pembeda)

##### Tabel Database
- `jadwal_ujian`
- `soal_ujian`
- `ujian_siswa`
- `jawaban_siswa`

---

### 7. 📈 Sistem Penilaian

#### Deskripsi
Manajemen penilaian siswa dengan berbagai komponen sesuai kurikulum yang dipilih.

#### 7.1 Komponen Nilai

##### Untuk Kurikulum 2013
- **KI-3 (Pengetahuan)**: Bobot 50%
  - Tugas
  - Ulangan Harian
  - UTS
  - UAS
- **KI-4 (Keterampilan)**: Bobot 50%
  - Praktik
  - Proyek
  - Portofolio

##### Untuk Kurikulum Merdeka
- **Sumatif**: Bobot 60%
- **Formatif**: Bobot 40%

#### 7.2 Input Nilai

##### Fitur
- Input nilai per komponen per siswa
- Input nilai dari CBT (otomatis)
- Input nilai praktik
- Input nilai tugas/proyek
- Bulk input (import Excel)
- Edit nilai (dengan log)

##### Logika Bisnis
- Setiap nilai tercatat jenis, tanggal, dan penginput
- Nilai CBT auto-input dari sistem ujian
- Perhitungan nilai akhir per mapel otomatis
- Formula: `(Total Nilai * Bobot) / Total Bobot`

#### 7.3 Nilai Sikap

##### Aspek Penilaian
- Sikap Spiritual
- Sikap Sosial

##### Fitur
- Input nilai sikap per semester per siswa
- Nilai skala 0-100
- Konversi ke predikat (A/B/C/D)
- Deskripsi kualitatif
- Input oleh wali kelas

#### 7.4 Nilai Ekstrakurikuler

##### Fitur
- Input nama ekstrakurikuler
- Nilai atau predikat
- Keterangan prestasi
- Input per semester

#### Akses & Permission
- **Input Nilai Mapel**: Guru pengampu
- **Input Nilai Sikap**: Wali Kelas
- **Input Nilai Ekskul**: Wali Kelas, Pembina Ekskul
- **Edit**: Guru pengampu, Wali Kelas (sebelum raport approved)
- **View**: Semua role (sesuai scope)

#### Tabel Database
- `komponen_nilai`
- `nilai`
- `nilai_sikap`
- `nilai_ekstrakurikuler`

---

### 8. 🏭 PKL (Praktik Kerja Lapangan)

#### Deskripsi
Manajemen lengkap program Praktik Kerja Lapangan/Industri untuk siswa SMK, dari penempatan hingga sertifikasi.

#### 8.1 Data Perusahaan/DU-DI

##### Fitur
- CRUD data perusahaan mitra
- Data: Nama, bidang usaha, alamat, kontak
- Kuota penempatan siswa
- Status aktif/non-aktif

#### 8.2 Penempatan PKL

##### Fitur
- Assign siswa ke perusahaan
- Tentukan pembimbing sekolah (guru)
- Input data pembimbing industri
- Set periode PKL (tanggal mulai - selesai)
- Set posisi/divisi siswa di perusahaan
- Status: Pending, Aktif, Selesai, Batal

##### Logika Bisnis
- Satu siswa bisa punya beberapa riwayat PKL (berbeda semester)
- Pembimbing sekolah bisa membimbing beberapa siswa
- Kuota perusahaan ter-check otomatis

#### 8.3 Monitoring PKL

##### Fitur
- Input jurnal monitoring per kunjungan
- Catat kegiatan siswa
- Identifikasi hambatan & solusi
- Upload foto dokumentasi
- Timeline monitoring

##### Logika Bisnis
- Minimal monitoring dilakukan 2x selama PKL
- Monitoring dicatat oleh pembimbing sekolah

#### 8.4 Penilaian PKL

##### Komponen Penilaian
1. **Nilai dari Industri (60%)**
   - Sikap Kerja: 30%
   - Keterampilan Teknis: 40%
   - Inisiatif: 30%

2. **Nilai dari Sekolah (20%)**
   - Monitoring & Bimbingan

3. **Nilai Laporan & Presentasi (20%)**
   - Kelengkapan laporan
   - Presentasi akhir

##### Formula Nilai Akhir
```
Nilai Akhir = (Nilai Industri × 60%) + (Nilai Sekolah × 20%) + (Nilai Laporan × 20%)
```

##### Fitur
- Input nilai per komponen
- Hitung nilai akhir otomatis
- Catatan dari industri & sekolah
- Upload laporan PKL
- Nilai masuk ke raport sebagai mata pelajaran produktif

#### 8.5 Sertifikat PKL

##### Fitur
- Generate nomor sertifikat unik
- Input tanggal terbit
- Upload file sertifikat
- Cetak sertifikat dengan template

#### Akses & Permission
- **Input Data Perusahaan**: Admin, TU
- **Penempatan**: Admin, Wali Kelas, TU
- **Monitoring**: Pembimbing Sekolah
- **Nilai Industri**: Admin (dari form yang diterima)
- **Nilai Sekolah**: Pembimbing Sekolah
- **Nilai Laporan**: Pembimbing Sekolah, Wali Kelas
- **Sertifikat**: Admin, TU

#### Tabel Database
- `perusahaan_pkl`
- `pkl`
- `monitoring_pkl`
- `nilai_pkl`
- `sertifikat_pkl`

---

### 9. 📄 Raport Online

#### Deskripsi
Sistem generate raport digital yang bisa diakses online oleh siswa dan orang tua.

#### 9.1 Komponen Raport

##### Identitas
- Nama siswa, NISN, NIS
- Kelas, semester, tahun akademik
- Nama sekolah

##### Nilai Akademik
- Nilai per mata pelajaran
- Nilai Pengetahuan (KI-3 / Sumatif)
- Nilai Keterampilan (KI-4)
- Nilai Akhir
- Predikat (A/B/C/D)
- Deskripsi capaian kompetensi

##### Nilai Non-Akademik
- Nilai Sikap Spiritual
- Nilai Sikap Sosial
- Nilai Ekstrakurikuler

##### Kehadiran
- Sakit: X hari
- Izin: X hari
- Alpha: X hari

##### Catatan
- Catatan Wali Kelas
- Prestasi/Keterangan lain

#### 9.2 Proses Generate Raport

##### Tahapan
1. **Validasi Data**
   - Cek kelengkapan nilai semua mapel
   - Cek nilai sikap
   - Cek data presensi
   
2. **Perhitungan Otomatis**
   - Hitung nilai akhir per mapel
   - Konversi ke predikat
   - Generate deskripsi (template)
   
3. **Input Manual**
   - Wali kelas input catatan
   - Wali kelas input nilai ekstrakurikuler
   
4. **Review & Approval**
   - Wali kelas review
   - Submit untuk approval
   - Kepala Sekolah approve
   
5. **Publish**
   - Raport bisa diakses siswa & ortu
   - Raport bisa dicetak

##### Logika Bisnis
- Raport di-generate per siswa per semester
- Status: Draft → Approved → Published
- Hanya raport yang approved bisa dicetak
- Nilai tidak bisa diubah setelah raport approved (butuh un-approve dulu)

#### 9.3 Akses Raport

##### Fitur
- View raport online (responsive)
- Download PDF
- Print raport
- Histori raport (semua semester)
- Perbandingan nilai antar semester

##### Template Raport
- Sesuai format Dapodik
- Bisa customizable per sekolah
- Include logo sekolah & tanda tangan digital

#### Akses & Permission
- **Generate**: Wali Kelas, Admin
- **Input Catatan**: Wali Kelas
- **Approve**: Kepala Sekolah, Admin
- **View**: Semua (sesuai scope)
- **Print**: Semua (jika status Published)

#### Tabel Database
- `raport`
- `raport_detail`

---

### 10. 📊 Legger Nilai

#### Deskripsi
Rekapitulasi nilai siswa dalam format legger/daftar kumpulan nilai yang merangkum seluruh nilai siswa dalam satu kelas atau mata pelajaran.

#### 10.1 Jenis Legger

##### Legger Per Kelas
- Menampilkan semua siswa di satu kelas
- Semua mata pelajaran
- Nilai akhir per mapel
- Rata-rata nilai siswa
- Ranking (opsional)
- Kehadiran

##### Legger Per Mata Pelajaran
- Menampilkan semua siswa yang ikut mapel tersebut
- Detail nilai per komponen
- Nilai akhir
- Statistik (tertinggi, terendah, rata-rata)

#### 10.2 Fitur

##### Generate Legger
- Pilih kelas atau mata pelajaran
- Pilih semester
- Auto-generate dari data nilai
- Save history generate

##### Format Output
- View di browser (table)
- Export ke Excel
- Export ke PDF
- Print langsung

##### Isi Legger
- Nomor urut
- NISN/NIS
- Nama siswa
- Nilai per mata pelajaran / komponen
- Jumlah nilai
- Rata-rata
- Ranking
- Kehadiran (S/I/A)

##### Statistik
- Nilai tertinggi
- Nilai terendah
- Rata-rata kelas
- Ketuntasan (jumlah siswa lulus KKM)

#### 10.3 Logika Bisnis

##### Perhitungan Ranking
- Berdasarkan rata-rata nilai akhir
- Bisa diaktifkan/nonaktifkan (setting)
- Update otomatis saat ada perubahan nilai

##### Validasi
- Legger hanya bisa di-generate jika minimal 70% nilai sudah terisi
- Peringatan jika ada siswa yang nilainya belum lengkap

#### Akses & Permission
- **Generate**: Wali Kelas, Admin, TU
- **View**: Kepala Sekolah, Wali Kelas, Guru (mapel-nya), Admin, TU
- **Export/Print**: Kepala Sekolah, Wali Kelas, Admin, TU

#### Tabel Database
- `legger`

---

### 11. ⬆️ Kenaikan Kelas

#### Deskripsi
Sistem otomatis untuk validasi dan proses kenaikan kelas siswa dengan kriteria yang bisa dikonfigurasi.

#### 11.1 Kriteria Kenaikan Kelas

##### Parameter Default (bisa diubah di Setting)
```
- Nilai Minimal (KKM): 75
- Maksimal Mapel Remidi: 3
- Rata-rata Nilai Minimal: 75
- Maksimal Absensi Tanpa Keterangan: 15 hari
- Nilai Sikap Minimal: 75
```

#### 11.2 Validasi Otomatis

##### Proses Validasi
1. **Cek Nilai**
   - Hitung jumlah mapel di bawah KKM
   - Hitung rata-rata nilai keseluruhan
   - Validasi apakah memenuhi syarat

2. **Cek Absensi**
   - Hitung total alpha (tanpa keterangan)
   - Validasi dengan batas maksimal

3. **Cek Nilai Sikap**
   - Validasi nilai spiritual & sosial
   - Minimal harus mencapai batas

4. **Hasil Validasi**
   - **NAIK**: Semua kriteria terpenuhi
   - **TIDAK NAIK**: Ada kriteria yang tidak terpenuhi
   - Detail alasan jika tidak naik

##### Contoh Logic
```javascript
if (jumlahMapelRemidi > 3) {
    status = "TIDAK NAIK";
    alasan = "4 mata pelajaran belum tuntas (max 3)";
}

if (rataRataNilai < 75) {
    status = "TIDAK NAIK";
    alasan = "Rata-rata 72.5 di bawah standar (min 75)";
}

if (totalAlpha > 15) {
    status = "TIDAK NAIK";
    alasan = "Absensi tanpa keterangan 18 hari (max 15)";
}
```

#### 11.3 Proses Kenaikan Kelas

##### Tahapan
1. **Persiapan**
   - Pastikan semua nilai semester genap sudah lengkap
   - Pastikan nilai sikap sudah diinput
   - Pastikan data presensi lengkap

2. **Simulasi**
   - Admin/TU jalankan validasi
   - Sistem tampilkan preview:
     - Berapa siswa yang naik
     - Berapa siswa yang tidak naik
     - Detail per siswa

3. **Review**
   - Wali kelas review hasil validasi
   - Kepala sekolah review
   - Jika ada keberatan, nilai bisa diperbaiki

4. **Eksekusi**
   - Admin eksekusi kenaikan kelas
   - Sistem otomatis:
     - Update status siswa
     - Pindahkan siswa ke kelas baru (X→XI, XI→XII)
     - Siswa kelas XII status jadi "Lulus"
     - Siswa tidak naik tetap di kelas yang sama
     - Generate laporan kenaikan kelas

5. **Laporan**
   - Rekap total siswa naik/tidak naik
   - Detail per kelas
   - Export ke PDF/Excel
   - Cetak Surat Keterangan Naik Kelas

##### Logika Kelas Baru
```
Kelas X RPL 1 (2024/2025) → Kelas XI RPL 1 (2025/2026)
Kelas XI RPL 1 (2024/2025) → Kelas XII RPL 1 (2025/2026)
Kelas XII RPL 1 (2024/2025) → STATUS: LULUS
```

#### 11.4 Handling Edge Cases

##### Siswa Tidak Naik
- Tetap di kelas yang sama (mengulang)
- Nilai semester genap tetap tersimpan
- Bisa ikut remedial
- Setelah remedial, validasi ulang

##### Siswa Pindah Jurusan
- Proses manual oleh Admin/TU
- Update data siswa_kelas
- Nilai ikut pindah

##### Siswa Mengundurkan Diri
- Status siswa diubah jadi "Keluar"
- Tidak diikutkan dalam proses kenaikan kelas
- Data tetap tersimpan

#### Akses & Permission
- **Simulasi Validasi**: Admin, TU, Kepala Sekolah
- **Eksekusi**: Admin, TU (dengan approval Kepala Sekolah)
- **View Hasil**: Semua role
- **Override Manual**: Admin (hanya jika ada kasus khusus)

#### Tabel Database
- `kenaikan_kelas`
- `kenaikan_kelas_detail`

---

### 12. 📚 E-Learning (Fitur Tambahan)

#### Deskripsi
Platform pembelajaran online sederhana yang terintegrasi dengan sistem akademik.

#### 12.1 Materi Ajar

##### Fitur
- Upload materi per mata pelajaran
- Tipe materi:
  - File PDF
  - Video (YouTube embed atau upload)
  - Slide presentasi
  - Link eksternal
  - Lainnya
- Pengaturan urutan materi
- Publish/unpublish
- Tracking view count
- Siswa bisa download

##### Logika Bisnis
- Hanya guru pengampu yang bisa upload
- Siswa hanya bisa akses materi kelas mereka
- Materi draft tidak tampil ke siswa

#### 12.2 Tugas Online

##### Fitur Guru
- Buat tugas dengan deskripsi
- Upload file lampiran (soal/panduan)
- Set deadline
- Set bobot nilai
- Publish/unpublish

##### Fitur Siswa
- Lihat daftar tugas
- Upload jawaban (file)
- atau Tulis jawaban langsung (text)
- Status: Tepat Waktu / Terlambat
- Lihat nilai & feedback

##### Fitur Penilaian
- Guru bisa download semua jawaban
- Input nilai per siswa
- Berikan feedback
- Nilai otomatis masuk ke sistem penilaian

##### Logika Bisnis
- Status "Terlambat" jika submit setelah deadline
- Siswa bisa edit jawaban sebelum deadline
- Setelah deadline, tidak bisa upload/edit
- Tugas yang dinilai masuk ke komponen nilai "Tugas"

#### 12.3 Forum Diskusi

##### Fitur
- Buat topik diskusi per kelas
- Reply/comment
- Pin penting thread
- Lock thread (tidak bisa dibalas)
- Tracking view count
- Notifikasi jika ada reply baru

##### Logika Bisnis
- Guru & siswa bisa buat thread
- Semua siswa di kelas bisa lihat & reply
- Guru bisa pin/unpin, lock/unlock
- Bisa digunakan untuk Q&A

#### Akses & Permission
- **Upload Materi**: Guru pengampu
- **Buat Tugas**: Guru pengampu
- **Submit Tugas**: Siswa
- **Nilai Tugas**: Guru pengampu
- **Buat Thread**: Guru, Siswa (di kelas mereka)
- **Reply**: Guru, Siswa
- **Moderate Forum**: Guru pengampu, Wali Kelas

#### Tabel Database
- `materi_ajar`
- `tugas`
- `pengumpulan_tugas`
- `forum_diskusi`
- `forum_komentar`

---

### 13. 📓 Jurnal Mengajar Guru (Fitur Tambahan)

#### Deskripsi
Dokumentasi kegiatan mengajar guru setiap pertemuan untuk memenuhi administrasi dan monitoring.

#### Fitur

##### Input Jurnal
- Pilih jadwal pelajaran
- Tanggal dan jam mengajar
- Materi yang diajarkan
- Metode pembelajaran
- Hambatan (jika ada)
- Solusi dari hambatan
- Catatan tambahan

##### Approval
- Wali kelas bisa review jurnal guru
- Kepala sekolah bisa approve/reject
- Status: Pending, Approved, Rejected

##### Laporan
- Rekap jurnal per guru
- Rekap per mata pelajaran
- Statistik keterlaksanaan pembelajaran
- Export ke PDF/Excel

#### Logika Bisnis
- Jurnal diisi setiap selesai mengajar
- Linked ke jadwal pelajaran
- Guru bisa edit sebelum diapprove
- Setelah approved, tidak bisa diedit
- Data untuk supervisi dan evaluasi guru

#### Akses & Permission
- **Input**: Guru
- **View**: Guru (jurnal sendiri), Wali Kelas, Kepala Sekolah, Admin
- **Approve**: Wali Kelas, Kepala Sekolah
- **Export**: Kepala Sekolah, Admin

#### Tabel Database
- `jurnal_mengajar`

---

### 14. ⚙️ Pengaturan Sistem

#### 14.1 Pengaturan Umum

##### Data Sekolah
- Nama Sekolah
- NPSN
- Alamat
- Telepon
- Email
- Logo Sekolah

#### 14.2 Pengaturan Akademik

##### Kriteria Kelulusan
- KKM Default
- Maksimal Mapel Remidi
- Minimal Rata-rata Nilai
- Maksimal Absensi
- Minimal Nilai Sikap

##### Komponen Penilaian
- Bobot Pengetahuan
- Bobot Keterampilan
- Bobot per jenis nilai (Tugas, UH, UTS, UAS)

#### 14.3 Pengaturan CBT

- Durasi Ujian Default
- Auto-submit saat waktu habis
- Tampilkan nilai setelah ujian
- Acak soal default
- Acak opsi default

#### 14.4 Template & Format

- Template Raport
- Template Surat
- Template Sertifikat
- Format nomor induk

#### Tabel Database
- `pengaturan`

---

### 15. 🔔 Sistem Notifikasi

#### Deskripsi
Sistem notifikasi real-time untuk menginformasikan event penting kepada user.

#### Jenis Notifikasi

##### Untuk Siswa
- Jadwal ujian baru
- Tugas baru
- Nilai sudah keluar
- Raport sudah bisa diakses
- Pesan dari guru/wali kelas

##### Untuk Orang Tua
- Anak tidak hadir (Alpha)
- Nilai anak di bawah KKM
- Raport anak sudah bisa diakses
- Undangan rapat orang tua

##### Untuk Guru
- Pengumpulan tugas baru
- Reminder jadwal mengajar
- Reminder input nilai
- Approval jurnal

##### Untuk Wali Kelas
- Siswa bermasalah (nilai/presensi)
- Reminder generate raport
- Approval diperlukan

##### Untuk Admin/TU
- Data siswa baru
- Mutasi siswa
- System alert

#### Fitur
- Notifikasi di aplikasi
- Email notification (optional)
- Mark as read
- Notifikasi counter
- Link ke halaman terkait
- History notifikasi

#### Tabel Database
- `notifikasi`

---

### 16. 📋 Log Aktivitas & Audit Trail

#### Deskripsi
Pencatatan semua aktivitas penting dalam sistem untuk keperluan audit dan tracking perubahan data.

#### Yang Dicatat
- User yang melakukan aksi
- Waktu aksi
- Jenis aktivitas (Create, Update, Delete)
- Tabel yang diubah
- ID record yang diubah
- Data lama (sebelum update)
- Data baru (setelah update)
- IP Address
- User Agent (browser/device)

#### Use Case
- Tracking perubahan nilai
- Tracking siapa yang approve raport
- Tracking perubahan data siswa
- Forensik jika ada masalah
- Compliance & accountability

#### Fitur
- View log per user
- View log per tabel/record
- Filter by date range
- Export log
- Search log

#### Tabel Database
- `log_aktivitas`

---

## Role & Permission

### Permission Matrix Detail

| Modul | Admin | Kepsek | Guru | Wali Kelas | TU | Siswa | Ortu |
|-------|-------|--------|------|------------|-----|-------|------|
| **Dashboard** | ✅ Full | ✅ View | ✅ View | ✅ View | ✅ View | ✅ View | ✅ View |
| **Master Data** |
| - Tahun Akademik | ✅ CRUD | ❌ | ❌ | ❌ | ✅ CRUD | ❌ | ❌ |
| - Kurikulum | ✅ CRUD | ❌ | ❌ | ❌ | ✅ View | ❌ | ❌ |
| - Jurusan | ✅ CRUD | ❌ | ❌ | ❌ | ✅ CRUD | ❌ | ❌ |
| - Kelas | ✅ CRUD | ✅ View | ❌ | ✅ View (kelasnya) | ✅ CRUD | ❌ | ❌ |
| - Mata Pelajaran | ✅ CRUD | ❌ | ✅ View | ❌ | ✅ CRUD | ❌ | ❌ |
| **Data Pengguna** |
| - Guru | ✅ CRUD | ✅ View | ✅ View (sendiri) | ❌ | ✅ CRUD | ❌ | ❌ |
| - Siswa | ✅ CRUD | ✅ View | ✅ View | ✅ View (kelasnya) | ✅ CRUD | ✅ View (sendiri) | ✅ View (anak) |
| - Orang Tua | ✅ CRUD | ❌ | ❌ | ❌ | ✅ CRUD | ❌ | ✅ View (sendiri) |
| **Buku Induk** |
| - Data Induk | ✅ CRUD | ✅ View | ❌ | ✅ View (kelasnya) | ✅ CRUD | ✅ View (sendiri) | ✅ View (anak) |
| - Prestasi | ✅ CRUD | ✅ CRUD | ✅ Create | ✅ CRUD | ✅ CRUD | ✅ View (sendiri) | ✅ View (anak) |
| - Pelanggaran | ✅ CRUD | ✅ CRUD | ✅ Create | ✅ CRUD | ✅ CRUD | ✅ View (sendiri) | ✅ View (anak) |
| **Jadwal** |
| - Jadwal Pelajaran | ✅ CRUD | ✅ View | ✅ View | ✅ View (kelasnya) | ✅ CRUD | ✅ View (kelasnya) | ✅ View (kelas anak) |
| **Presensi** |
| - Input Presensi | ✅ CRUD | ❌ | ✅ CRU | ✅ CRUD | ❌ | ❌ | ❌ |
| - View Presensi | ✅ | ✅ | ✅ (kelasnya) | ✅ (kelasnya) | ✅ | ✅ (sendiri) | ✅ (anak) |
| **CBT** |
| - Bank Soal | ✅ CRUD | ❌ | ✅ CRUD (mapelnya) | ✅ CRUD (mapelnya) | ❌ | ❌ | ❌ |
| - Jadwal Ujian | ✅ CRUD | ✅ View | ✅ CRUD (mapelnya) | ✅ View (kelasnya) | ❌ | ✅ View | ❌ |
| - Ikut Ujian | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ |
| - Koreksi Manual | ❌ | ❌ | ✅ (mapelnya) | ✅ (mapelnya) | ❌ | ❌ | ❌ |
| **Penilaian** |
| - Input Nilai | ✅ | ❌ | ✅ (mapelnya) | ✅ (mapelnya) | ❌ | ❌ | ❌ |
| - Input Nilai Sikap | ✅ | ❌ | ❌ | ✅ (kelasnya) | ❌ | ❌ | ❌ |
| - View Nilai | ✅ | ✅ | ✅ (mapelnya) | ✅ (kelasnya) | ✅ | ✅ (sendiri) | ✅ (anak) |
| **PKL** |
| - Data Perusahaan | ✅ CRUD | ✅ View | ❌ | ❌ | ✅ CRUD | ❌ | ❌ |
| - Penempatan PKL | ✅ CRUD | ✅ View | ❌ | ✅ Create | ✅ CRUD | ✅ View (sendiri) | ✅ View (anak) |
| - Monitoring | ✅ View | ✅ View | ✅ CRU (bimbingannya) | ✅ View | ❌ | ✅ View (sendiri) | ✅ View (anak) |
| - Nilai PKL | ✅ CRUD | ✅ View | ✅ CRU (bimbingannya) | ✅ View | ❌ | ✅ View (sendiri) | ✅ View (anak) |
| **Raport** |
| - Generate Raport | ✅ | ❌ | ❌ | ✅ (kelasnya) | ❌ | ❌ | ❌ |
| - Input Catatan | ✅ | ❌ | ❌ | ✅ (kelasnya) | ❌ | ❌ | ❌ |
| - Approve Raport | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ |
| - View/Print | ✅ | ✅ | ✅ (kelasnya) | ✅ (kelasnya) | ✅ | ✅ (sendiri) | ✅ (anak) |
| **Legger** |
| - Generate | ✅ | ❌ | ❌ | ✅ (kelasnya) | ✅ | ❌ | ❌ |
| - View/Export | ✅ | ✅ | ✅ (mapelnya) | ✅ (kelasnya) | ✅ | ❌ | ❌ |
| **Kenaikan Kelas** |
| - Simulasi | ✅ | ✅ | ❌ | ✅ View | ✅ | ❌ | ❌ |
| - Eksekusi | ✅ | ✅ Approve | ❌ | ❌ | ✅ | ❌ | ❌ |
| **E-Learning** |
| - Materi | ✅ View | ❌ | ✅ CRUD (mapelnya) | ✅ View | ❌ | ✅ View | ❌ |
| - Tugas | ✅ View | ❌ | ✅ CRUD (mapelnya) | ✅ View | ❌ | ✅ Submit | ❌ |
| - Forum | ✅ Moderate | ❌ | ✅ Moderate | ✅ Moderate | ❌ | ✅ Post/Reply | ❌ |
| **Jurnal Mengajar** |
| - Input | ❌ | ❌ | ✅ (sendiri) | ❌ | ❌ | ❌ | ❌ |
| - View | ✅ | ✅ | ✅ (sendiri) | ✅ | ❌ | ❌ | ❌ |
| - Approve | ✅ | ✅ | ❌ | ✅ | ❌ | ❌ | ❌ |
| **Laporan** |
| - Semua Laporan | ✅ | ✅ | ✅ (scope terbatas) | ✅ (kelasnya) | ✅ | ✅ (sendiri) | ✅ (anak) |
| **Pengaturan** |
| - System Setting | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| - Profile | ✅ (sendiri) | ✅ (sendiri) | ✅ (sendiri) | ✅ (sendiri) | ✅ (sendiri) | ✅ (sendiri) | ✅ (sendiri) |

**Keterangan:**
- ✅ = Akses penuh / sesuai keterangan
- ❌ = Tidak ada akses
- CRUD = Create, Read, Update, Delete
- CRU = Create, Read, Update (tanpa Delete)

---

## Teknologi Stack

### Backend
- **Framework**: Laravel 10.x / 11.x
- **PHP**: 8.1 atau lebih tinggi
- **Database**: MySQL 8.0 / MariaDB 10.x
- **Authentication**: Laravel Sanctum / Laravel Breeze
- **Authorization**: Laravel Policies & Gates
- **File Storage**: Laravel Storage (local/S3)
- **Queue**: Laravel Queue (untuk async tasks)
- **Scheduler**: Laravel Scheduler (untuk cron jobs)

### Frontend Admin Panel
- **CSS Framework**: Bootstrap 5.x
- **JavaScript**: 
  - jQuery 3.x (untuk interaksi dasar)
  - DataTables (untuk tabel dengan pagination, search, filter)
  - Select2 (untuk dropdown dengan search)
  - Chart.js / ApexCharts (untuk grafik)
- **Template**: AdminLTE / SB Admin / atau custom

### Frontend CBT
- **Framework**: Vanilla JavaScript atau Vue.js (untuk real-time timer)
- **Timer**: JavaScript Interval
- **Auto-save**: AJAX setiap 10 detik
- **Prevent Cheating**:
  - Disable right-click
  - Disable inspect element
  - Full-screen mode
  - Tab switch detection

### Additional Libraries
- **PDF Generation**: DomPDF / Laravel FPDF
- **Excel Export**: Maatwebsite/Laravel-Excel
- **Image Processing**: Intervention Image
- **Notification**: Laravel Notification + optional Pusher
- **QR Code**: SimpleSoftwareIO/simple-qrcode (untuk absen QR)

### Development Tools
- **Version Control**: Git / GitHub / GitLab
- **Package Manager**: Composer (PHP), NPM (JS)
- **Database Migration**: Laravel Migrations
- **Seeding**: Laravel Seeders
- **Testing**: PHPUnit (optional)

### Server Requirements
- **Web Server**: Apache / Nginx
- **PHP Extensions**: 
  - BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, GD
- **Database**: MySQL 8.0+ / MariaDB 10.3+
- **Memory**: Minimal 512MB RAM (recommended 2GB+)
- **Storage**: Minimal 10GB (untuk file upload)

---

## Database Schema

### Total Tabel: 50+

#### Kelompok Tabel

##### 1. Authentication (3 tabel)
- `users`
- `password_reset_tokens`
- `sessions`

##### 2. Master Data (11 tabel)
- `kurikulum`
- `tahun_akademik`
- `semester`
- `jurusan`
- `kelas`
- `kelompok_mapel`
- `mata_pelajaran`
- `mata_pelajaran_kelas`
- `mata_pelajaran_guru`
- `jadwal_pelajaran`
- `komponen_nilai`

##### 3. User Data (4 tabel)
- `guru`
- `siswa`
- `orang_tua`
- `siswa_kelas`

##### 4. Buku Induk (4 tabel)
- `buku_induk`
- `prestasi_siswa`
- `pelanggaran_siswa`
- `mutasi_siswa`

##### 5. Pembelajaran (2 tabel)
- `presensi`
- `jurnal_mengajar`

##### 6. CBT (7 tabel)
- `bank_soal`
- `soal`
- `jadwal_ujian`
- `soal_ujian`
- `ujian_siswa`
- `jawaban_siswa`

##### 7. Penilaian (3 tabel)
- `nilai`
- `nilai_sikap`
- `nilai_ekstrakurikuler`

##### 8. PKL (5 tabel)
- `perusahaan_pkl`
- `pkl`
- `monitoring_pkl`
- `nilai_pkl`
- `sertifikat_pkl`

##### 9. Raport & Legger (3 tabel)
- `raport`
- `raport_detail`
- `legger`

##### 10. Kenaikan Kelas (2 tabel)
- `kenaikan_kelas`
- `kenaikan_kelas_detail`

##### 11. E-Learning (5 tabel)
- `materi_ajar`
- `tugas`
- `pengumpulan_tugas`
- `forum_diskusi`
- `forum_komentar`

##### 12. System (3 tabel)
- `pengaturan`
- `log_aktivitas`
- `notifikasi`

### Relasi Utama

```
users → guru/siswa/orang_tua (one-to-one)
tahun_akademik → semester (one-to-many)
semester → kelas (one-to-many)
kelas → siswa_kelas → siswa (many-to-many)
kelas → mata_pelajaran_kelas ← mata_pelajaran (many-to-many)
mata_pelajaran_kelas → mata_pelajaran_guru ← guru (many-to-many)
siswa → nilai ← mata_pelajaran_kelas
siswa → ujian_siswa ← jadwal_ujian
siswa → raport
siswa → pkl
```

---

## Alur Penggunaan Sistem

### 1. Setup Awal (Admin/TU)
1. Login sebagai Admin
2. Setup Kurikulum
3. Buat Tahun Akademik baru
4. Buat Semester (Ganjil & Genap)
5. Setup Jurusan
6. Input Data Guru
7. Buat Kelas
8. Assign Wali Kelas
9. Input Mata Pelajaran
10. Assign Mapel ke Kelas
11. Assign Guru ke Mapel
12. Buat Jadwal Pelajaran

### 2. Penerimaan Siswa Baru (TU)
1. Input Data Orang Tua
2. Input Data Siswa
3. Link Siswa dengan Orang Tua
4. Assign Siswa ke Kelas (siswa_kelas)
5. Generate User Account untuk Siswa & Orang Tua
6. Buat Buku Induk Siswa

### 3. Proses Pembelajaran (Guru)
1. Login sebagai Guru
2. Lihat Jadwal Mengajar
3. Input Presensi Siswa
4. Upload Materi Ajar
5. Buat Tugas Online
6. Input Jurnal Mengajar

### 4. Penilaian (Guru)
1. Buat Bank Soal untuk CBT
2. Buat Jadwal Ujian (UH/UTS/UAS)
3. Siswa ikut ujian (auto-grading)
4. Koreksi manual soal essay
5. Input nilai non-CBT (tugas, praktik)
6. Review nilai yang sudah masuk

### 5. PKL (Wali Kelas & Pembimbing)
1. Input data perusahaan mitra
2. Tempatkan siswa ke perusahaan
3. Assign pembimbing sekolah
4. Monitoring berkala
5. Input nilai PKL
6. Generate sertifikat

### 6. Raport (Wali Kelas)
1. Cek kelengkapan nilai semua mapel
2. Input nilai sikap siswa
3. Input nilai ekstrakurikuler
4. Input catatan wali kelas
5. Generate raport
6. Submit untuk approval
7. Kepala Sekolah approve
8. Publish raport (siswa & ortu bisa akses)

### 7. Kenaikan Kelas (Admin/TU)
1. Pastikan raport semester genap sudah approved
2. Jalankan simulasi kenaikan kelas
3. Review hasil validasi
4. Perbaiki nilai jika ada yang perlu remedial
5. Eksekusi kenaikan kelas
6. Sistem otomatis pindahkan siswa ke kelas baru
7. Generate laporan kenaikan kelas

### 8. Monitoring (Kepala Sekolah)
1. Dashboard overview
2. Monitoring nilai siswa
3. Monitoring presensi
4. Approve raport
5. Approve kenaikan kelas
6. Review jurnal mengajar guru
7. Export laporan

### 9. View Nilai (Siswa/Orang Tua)
1. Login ke sistem
2. Lihat jadwal ujian
3. Ikut ujian CBT (siswa)
4. Lihat nilai per mata pelajaran
5. Lihat raport online
6. Download/print raport
7. Lihat presensi
8. Terima notifikasi penting

---

## Timeline Implementasi (Estimasi)

### Phase 1: Setup & Master Data (2-3 minggu)
- Setup project Laravel
- Database migration
- Authentication system
- Master data CRUD (Kurikulum, Tahun Akademik, Jurusan, Kelas)
- User management (Guru, Siswa, Orang Tua)
- Role & permission

### Phase 2: Pembelajaran Dasar (2-3 minggu)
- Mata pelajaran management
- Jadwal pelajaran
- Presensi
- Dashboard role-based

### Phase 3: CBT System (3-4 minggu)
- Bank soal & soal
- Jadwal ujian
- Interface ujian siswa
- Timer & auto-save
- Auto-grading
- Manual grading

### Phase 4: Penilaian (2 minggu)
- Input nilai
- Perhitungan nilai akhir
- Nilai sikap & ekstrakurikuler
- Integration nilai CBT

### Phase 5: PKL (2 minggu)
- Data perusahaan
- Penempatan & monitoring
- Penilaian PKL
- Sertifikat

### Phase 6: Raport & Legger (2-3 minggu)
- Generate raport
- Template raport PDF
- Approval workflow
- Legger nilai
- Export Excel/PDF

### Phase 7: Kenaikan Kelas (1-2 minggu)
- Validasi logic
- Simulasi
- Eksekusi
- Laporan

### Phase 8: E-Learning (2 minggu)
- Upload materi
- Tugas online
- Forum diskusi

### Phase 9: Fitur Pendukung (1-2 minggu)
- Buku induk
- Prestasi & pelanggaran
- Jurnal mengajar
- Notifikasi
- Log aktivitas

### Phase 10: Testing & Refinement (2-3 minggu)
- User acceptance testing
- Bug fixing
- Performance optimization
- Documentation

**Total Estimasi: 4-6 bulan** (tergantung kompleksitas dan tim)

---

## Best Practices & Recommendations

### Security
1. **Password Hashing**: Gunakan bcrypt Laravel default
2. **CSRF Protection**: Aktifkan CSRF token di semua form
3. **SQL Injection**: Gunakan Eloquent ORM / Query Builder
4. **XSS Protection**: Escape output dengan `{{ }}` di Blade
5. **File Upload**: Validasi tipe & size file
6. **Role-Based Access**: Gunakan Gates & Policies Laravel
7. **API Security**: Gunakan Sanctum untuk API authentication
8. **Session Management**: Set timeout session yang wajar

### Performance
1. **Database Indexing**: Index kolom yang sering di-query
2. **Eager Loading**: Gunakan `with()` untuk menghindari N+1 query
3. **Caching**: Cache data yang jarang berubah (master data)
4. **Pagination**: Gunakan pagination untuk list data besar
5. **Queue Jobs**: Proses berat (email, generate PDF) pakai queue
6. **Asset Optimization**: Minify CSS/JS, compress image
7. **Database Optimization**: Regular cleanup old sessions & logs

### Code Quality
1. **MVC Pattern**: Pisahkan logic (Model, View, Controller)
2. **Service Layer**: Buat Service class untuk business logic kompleks
3. **Repository Pattern**: Abstraksi database query (optional)
4. **Validation**: Gunakan Form Request untuk validasi
5. **Naming Convention**: Konsisten dengan Laravel standard
6. **Comments**: Comment untuk logic yang kompleks
7. **DRY Principle**: Hindari duplikasi code

### User Experience
1. **Responsive Design**: Mobile-friendly untuk semua role
2. **Loading Indicator**: Tampilkan saat proses async
3. **Error Message**: User-friendly & actionable
4. **Confirmation Dialog**: Untuk aksi delete/approve
5. **Breadcrumb**: Navigasi yang jelas
6. **Search & Filter**: Di semua list data
7. **Export Function**: Excel/PDF untuk laporan

### Data Integrity
1. **Foreign Key Constraints**: Enforce di database level
2. **Validation**: Double validation (client & server)
3. **Transaction**: Gunakan DB transaction untuk multi-table update
4. **Soft Delete**: Untuk data penting (optional)
5. **Backup**: Automated database backup
6. **Audit Trail**: Log semua perubahan data penting
7. **Data Migration**: Hati-hati saat update production

---

## API Endpoints (Optional - untuk Mobile App)

Jika kedepannya akan dikembangkan mobile app, berikut struktur API yang disarankan:

### Authentication
```
POST   /api/login
POST   /api/logout
POST   /api/refresh-token
POST   /api/forgot-password
POST   /api/reset-password
```

### User Profile
```
GET    /api/profile
PUT    /api/profile
POST   /api/profile/change-password
POST   /api/profile/upload-photo
```

### Siswa
```
GET    /api/siswa/jadwal                    // Jadwal kelas siswa
GET    /api/siswa/presensi                  // Rekap presensi
GET    /api/siswa/nilai                     // Daftar nilai
GET    /api/siswa/raport                    // Raport per semester
GET    /api/siswa/raport/{id}/download      // Download PDF
GET    /api/siswa/ujian                     // Daftar ujian
GET    /api/siswa/ujian/{id}                // Detail ujian
POST   /api/siswa/ujian/{id}/start          // Mulai ujian
POST   /api/siswa/ujian/{id}/jawab          // Submit jawaban
POST   /api/siswa/ujian/{id}/finish         // Selesai ujian
GET    /api/siswa/materi                    // Daftar materi
GET    /api/siswa/tugas                     // Daftar tugas
POST   /api/siswa/tugas/{id}/submit         // Submit tugas
GET    /api/siswa/notifikasi                // Notifikasi
```

### Guru
```
GET    /api/guru/jadwal                     // Jadwal mengajar
GET    /api/guru/kelas                      // Daftar kelas
POST   /api/guru/presensi                   // Input presensi
GET    /api/guru/presensi/{id}              // Detail presensi
GET    /api/guru/siswa/{kelas_id}           // Daftar siswa per kelas
POST   /api/guru/nilai                      // Input nilai
GET    /api/guru/ujian                      // Daftar ujian
GET    /api/guru/ujian/{id}/hasil           // Hasil ujian
POST   /api/guru/jurnal                     // Input jurnal mengajar
```

### Orang Tua
```
GET    /api/ortu/anak                       // Data anak
GET    /api/ortu/anak/{id}/nilai            // Nilai anak
GET    /api/ortu/anak/{id}/presensi         // Presensi anak
GET    /api/ortu/anak/{id}/raport           // Raport anak
GET    /api/ortu/notifikasi                 // Notifikasi
```

### Master Data (Admin/TU)
```
GET    /api/master/tahun-akademik
GET    /api/master/semester
GET    /api/master/jurusan
GET    /api/master/kelas
GET    /api/master/mata-pelajaran
```

---

## Modul Tambahan (Future Development)

### 1. Keuangan & Pembayaran SPP
- Input tagihan SPP per siswa
- Pembayaran SPP (cash/transfer)
- Cetak kwitansi
- Laporan keuangan
- Notifikasi tunggakan

### 2. Perpustakaan
- Katalog buku
- Peminjaman & pengembalian
- Denda keterlambatan
- Statistik peminjaman
- Integrasi dengan sistem akademik

### 3. Konseling BK
- Data konseling siswa
- Jadwal konseling
- Catatan kasus
- Follow-up treatment
- Laporan BK

### 4. Alumni
- Database alumni
- Tracking karir alumni
- Survey tracer study
- Networking alumni
- Event alumni

### 5. Inventaris
- Data barang inventaris
- Peminjaman alat/ruang
- Maintenance tracking
- Asset depreciation
- Laporan inventaris

### 6. PPDB (Penerimaan Peserta Didik Baru)
- Pendaftaran online
- Seleksi otomatis
- Pengumuman hasil
- Daftar ulang
- Integrasi ke sistem akademik

### 7. Surat Menyurat
- Template surat
- Generate surat otomatis
- Nomor surat otomatis
- Arsip surat
- Digital signature

### 8. Absensi QR Code
- Generate QR Code per kelas per jadwal
- Scan QR Code untuk absen
- Validasi lokasi (GPS)
- Prevent duplicate scan
- Real-time dashboard

---

## Troubleshooting Common Issues

### 1. Performance Lambat saat Generate Raport
**Solusi:**
- Gunakan queue untuk generate PDF
- Cache data yang sudah di-generate
- Optimize query dengan eager loading
- Pagination untuk raport massal

### 2. CBT Timer Tidak Akurat
**Solusi:**
- Gunakan server time, bukan client time
- Store remaining time di database
- Sync timer setiap beberapa detik
- Handle tab switch / browser close

### 3. Duplikasi Data saat Kenaikan Kelas
**Solusi:**
- Gunakan database transaction
- Validasi unique constraint
- Lock table saat proses
- Rollback jika error

### 4. File Upload Size Limit
**Solusi:**
- Set `upload_max_filesize` di php.ini
- Set `post_max_size` di php.ini
- Set `client_max_body_size` di nginx (jika pakai nginx)
- Compress file di client-side sebelum upload

### 5. Session Timeout saat Ujian
**Solusi:**
- Set session lifetime lebih lama untuk CBT
- Auto-extend session saat ujian aktif
- Simpan jawaban setiap beberapa detik
- Restore session jika timeout

### 6. Database Migration Error di Production
**Solusi:**
- Backup database sebelum migration
- Test migration di staging dulu
- Gunakan `--force` flag dengan hati-hati
- Rollback plan jika gagal

---

## Maintenance & Support

### Daily
- Monitor error logs
- Check server resources (CPU, RAM, Disk)
- Backup database
- Clear cache jika diperlukan

### Weekly
- Review log aktivitas
- Check slow queries
- Update data yang expired
- Test critical features

### Monthly
- Database optimization (OPTIMIZE TABLE)
- Clear old sessions & notifications
- Security patch update
- Performance review

### Semester
- Backup full system
- Audit data integrity
- User feedback review
- Feature enhancement planning

### Yearly
- Major update Laravel version
- Security audit
- Server upgrade (jika perlu)
- Disaster recovery drill

---

## Training & Documentation

### Training Materials

#### 1. Admin/TU
- Setup tahun akademik & semester
- Manajemen master data
- Input data guru & siswa
- Proses kenaikan kelas
- Generate laporan
- Backup & restore

#### 2. Kepala Sekolah
- Navigasi dashboard
- Monitoring akademik
- Approval raport & kenaikan kelas
- Export laporan
- Review jurnal mengajar

#### 3. Guru
- Input presensi
- Buat bank soal & ujian CBT
- Input nilai
- Upload materi & tugas
- Jurnal mengajar
- Lihat rekap kelas

#### 4. Wali Kelas
- Kelola data kelas
- Input nilai sikap
- Generate raport
- Input catatan raport
- Monitoring siswa bermasalah
- Komunikasi dengan orang tua

#### 5. Siswa
- Login & ganti password
- Lihat jadwal & materi
- Ikut ujian CBT
- Submit tugas
- Lihat nilai & raport
- Update profile

#### 6. Orang Tua
- Login & monitoring anak
- Lihat nilai & raport anak
- Lihat presensi anak
- Terima notifikasi
- Komunikasi dengan wali kelas

### User Manual
- Panduan per role (PDF)
- Video tutorial
- FAQ
- Screenshot step-by-step
- Contact support

---

## Success Metrics

### Key Performance Indicators (KPI)

#### Usage Metrics
- Daily Active Users (DAU)
- Monthly Active Users (MAU)
- Login frequency per role
- Feature usage statistics
- Average session duration

#### Academic Metrics
- Waktu rata-rata input nilai (berkurang)
- Waktu generate raport (berkurang)
- Akurasi data (meningkat)
- Ketepatan waktu pengumpulan nilai (meningkat)
- Kelengkapan data siswa (meningkat)

#### Efficiency Metrics
- Waktu proses kenaikan kelas (berkurang dari manual)
- Error rate dalam input data (menurun)
- Response time system (< 2 detik)
- Uptime system (> 99%)
- Ticket support (menurun setelah training)

#### User Satisfaction
- User satisfaction score (target > 4/5)
- Feature request count
- Bug report rate
- Training completion rate
- Adoption rate per feature

---

## Kesimpulan

Sistem Informasi Akademik SMK ini dirancang untuk:

✅ **Mengotomasi** proses administrasi akademik
✅ **Mengintegrasikan** semua data dalam satu sistem
✅ **Meningkatkan** efisiensi kerja guru dan staf
✅ **Mempermudah** monitoring untuk kepala sekolah
✅ **Memberikan** akses transparansi nilai untuk siswa & orang tua
✅ **Mengurangi** kesalahan input data manual
✅ **Menyediakan** laporan & analitik yang akurat
✅ **Mendukung** pembelajaran digital (e-learning & CBT)
✅ **Memenuhi** kebutuhan spesifik SMK (PKL, kejuruan)

Dengan implementasi yang baik, sistem ini dapat:
- Menghemat waktu administrasi hingga 60-70%
- Mengurangi error data hingga 80-90%
- Meningkatkan kepuasan user
- Mendukung akreditasi sekolah
- Menjadi aset digital sekolah

---

## Kontak & Support

Untuk informasi lebih lanjut:
- **Email**: support@siakadsmk.sch.id
- **Phone**: (021) XXX-XXXX
- **Website**: https://siakad.smk.sch.id
- **Documentation**: https://docs.siakad.smk.sch.id

---

**Versi Dokumen**: 1.0
**Tanggal**: Januari 2026
**Status**: Final Draft

---

© 2026 Sistem Informasi Akademik SMK. All rights reserved.