2. Master Data
A. Kurikulum & Tahun Akademik

Kurikulum (K13, Merdeka)
Tahun Akademik (2024/2025)
Semester (Ganjil/Genep, set active)
Controller: KurikulumController, TahunAkademikController, SemesterController

B. Jurusan & Kelas

Jurusan/Kompetensi Keahlian
Kelas (per semester, dengan wali kelas)
Assignment siswa ke kelas
Controller: JurusanController, KelasController 

C. Mata Pelajaran

CRUD Mata Pelajaran
Komponen Penilaian (Pengetahuan/Keterampilan)
Assignment Guru ke Mapel & Kelas
Controller: MataPelajaranController, KomponenNilaiController, MataPelajaranGuruController

D. Data Guru

CRUD Guru (NIP, biodata, foto)
Link ke user account
Status aktif/non-aktif
Controller: GuruController

E. Data Siswa & Orang Tua

CRUD Siswa (NISN, NIS, biodata, foto)
CRUD Orang Tua (data ayah, ibu, wali)
Link siswa-orangtua
Status siswa (Aktif/Lulus/Pindah/Keluar/DO)
Controller: SiswaController, OrangTuaController


3. Buku Induk Siswa

Data Induk (nomor induk, ijazah, SKHUN)
Prestasi Siswa (akademik/non-akademik, upload sertifikat)
Pelanggaran Siswa (kategori, poin, sanksi)
Controller: BukuIndukController, PrestasiSiswaController, PelanggaranSiswaController


4. Jadwal Pelajaran

CRUD Jadwal (hari, jam, mapel, guru, kelas)
Validasi bentrok (guru/kelas)
Generate jadwal otomatis
Controller: JadwalPelajaranController


5. Presensi/Absensi

Input presensi (Hadir/Izin/Sakit/Alpha)
Bulk input per kelas
Rekap presensi (per siswa, per kelas, per periode)
Notifikasi alpha ke orang tua
Controller: PresensiController


6. Penilaian
A. Nilai Akademik

Input nilai per komponen (Tugas, UH, UTS, UAS)
Pengetahuan & Keterampilan
Bulk input nilai
Rekap nilai per kelas/mapel
Controller: NilaiController

B. Nilai Sikap

Spiritual & Sosial
Deskripsi per aspek
Input per siswa
Controller: NilaiSikapController


7. CBT (Computer Based Test)
A. Bank Soal

CRUD Soal (PG, Essay, Benar-Salah, Menjodohkan)
Tingkat kesulitan (Mudah/Sedang/Sulit)
Upload media soal
Import dari Excel
Duplicate soal
Controller: BankSoalController

B. Jadwal Ujian

CRUD Jadwal ujian
Multi-kelas assignment
Publish/unpublish
Setting acak soal/opsi
Controller: JadwalUjianController

C. Soal Ujian

Manage soal per ujian
Generate otomatis by tingkat kesulitan
Reorder soal
Controller: SoalUjianController

D. Ujian Siswa

Interface ujian siswa
Timer countdown
Auto-save jawaban
Auto-submit saat timeout
Koreksi otomatis (PG)
Koreksi manual (Essay)
Lihat hasil
Controller: UjianSiswaController


8. PKL (Praktik Kerja Lapangan)
A. Data Perusahaan

CRUD Perusahaan mitra
Kuota PKL
Kontak perusahaan
Controller: PerusahaanController

B. Penempatan PKL

CRUD PKL (siswa, perusahaan, periode)
Pembimbing guru
Status PKL
Rekap penempatan
Controller: PklController

C. Monitoring PKL

Laporan monitoring (harian/mingguan)
Upload foto kegiatan
Catatan pembimbing
Controller: MonitoringPklController

D. Nilai PKL

Penilaian dari industri
Penilaian dari sekolah
Multi aspek (kedisiplinan, kerjasama, dll)
Auto-calculate nilai akhir
Controller: NilaiPklController


9. E-Learning
A. Materi Pembelajaran

Upload materi (PDF, Video, Link)
Per mata pelajaran & kelas
Publish/unpublish
View counter
Controller: MateriPembelajaranController

B. Tugas

CRUD Tugas online
Deadline management
Upload file attachment
Status (Published/Draft)
Controller: TugasController

C. Pengumpulan Tugas

Submit tugas (upload file)
Deadline check
Status pengumpulan
Penilaian tugas
Feedback guru
Controller: PengumpulanTugasController

D. Forum Diskusi

Thread diskusi per mapel/kelas
Reply/komentar
Lock/pin thread
View counter
Controller: ForumDiskusiController


10. Raport

Generate raport otomatis
Agregasi nilai dari semua komponen
Input catatan wali kelas
Approve raport (Kepsek)
Print PDF raport
Controller: RaportController


11. Legger Nilai ⚠️ BELUM ADA

Generate legger nilai per kelas
Rekap nilai semua mapel
Export Excel/PDF
View by semester
Controller: LeggerController


12. Kenaikan Kelas

Simulasi kenaikan kelas
Kriteria: nilai rata-rata, kehadiran
Bulk eksekusi kenaikan
Update status siswa
Controller: KenaikanKelasController


13. Jurnal Mengajar

Input jurnal harian guru
Materi yang diajarkan
Kehadiran siswa
Approval wali kelas/kepsek
Export jurnal
Controller: JurnalMengajarController


14. Sistem Notifikasi

Notifikasi real-time
Untuk semua role
Mark as read
History notifikasi
Link ke halaman terkait
Controller: NotifikasiController


15. Log Aktivitas & Audit Trail

Tracking CRUD operations
User, waktu, IP address
Data lama vs baru
Filter & search
Export log
Controller: LogAktivitasController

16. Pengaturan Sistem ⏸️ SKIP DULU

Data sekolah (nama, NPSN, logo)
Kriteria kelulusan
Setting CBT default
Template raport/surat
Controller: PengaturanController
Status: ⏸️ Skip dulu sesuai permintaan Anda


17. Dashboard

Statistik per role
Chart & grafik
Quick links
Recent activities
Controller: DashboardController