<?php

use App\Http\Controllers\Admin\MenuController as AdminMenuController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\BukuIndukController;
use App\Http\Controllers\EkstrakurikulerController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\JadwalPelajaranController;
use App\Http\Controllers\JadwalUjianController;
use App\Http\Controllers\JurnalPklController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\KenaikanKelasController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\KomponenNilaiController;
use App\Http\Controllers\KurikulumController;
use App\Http\Controllers\LeggerController;
use App\Http\Controllers\MataPelajaranController;
use App\Http\Controllers\MataPelajaranKelasController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\NilaiEkstrakurikulerController;
use App\Http\Controllers\NilaiPklController;
use App\Http\Controllers\NilaiSikapController;
use App\Http\Controllers\OrangTuaController;
use App\Http\Controllers\PelanggaranSiswaController;
use App\Http\Controllers\PerusahaanPklController;
use App\Http\Controllers\PklController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\PrestasiSiswaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RaportController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\TahunAkademikController;
use App\Http\Controllers\UjianSiswaController;
use App\Http\Controllers\BankSoalController;
use App\Http\Controllers\SoalController;
use App\Http\Controllers\ELearningController;
use App\Http\Controllers\MateriAjarController;
use App\Http\Controllers\TugasController;
use App\Http\Controllers\ForumDiskusiController;
use App\Http\Controllers\JurnalMengajarController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // 1. Profile
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    // 2. Admin Management
    Route::middleware('role:super-admin')->prefix('admin')->name('admin.')->group(function () {
        
        // Menu Management
        Route::prefix('menu')->name('menu.')->group(function () {
            Route::get('/', [AdminMenuController::class, 'index'])->name('index');
            Route::post('/', [AdminMenuController::class, 'store'])->name('store');
            Route::get('/{id}', [AdminMenuController::class, 'show'])->name('show');
            Route::put('/{id}', [AdminMenuController::class, 'update'])->name('update');
            Route::delete('/{id}', [AdminMenuController::class, 'destroy'])->name('destroy');
            Route::get('/parents/list', [AdminMenuController::class, 'getParents'])->name('parents');
            Route::get('/{id}/roles', [AdminMenuController::class, 'getRoles'])->name('roles.get');
            Route::post('/{id}/roles', [AdminMenuController::class, 'assignRole'])->name('roles.assign');
            Route::get('/{id}/permissions', [AdminMenuController::class, 'getPermissions'])->name('permissions.get');
            Route::post('/{id}/permissions', [AdminMenuController::class, 'assignPermission'])->name('permissions.assign');
        });

        // Role Management
        Route::prefix('role')->name('role.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::post('/', [RoleController::class, 'store'])->name('store');
            Route::get('/{id}', [RoleController::class, 'show'])->name('show');
            Route::put('/{id}', [RoleController::class, 'update'])->name('update');
            Route::delete('/{id}', [RoleController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/permissions', [RoleController::class, 'getPermissions'])->name('permissions.get');
            Route::post('/{id}/permissions', [RoleController::class, 'assignPermission'])->name('permissions.assign');
        });

        // Permission Management
        Route::prefix('permission')->name('permission.')->group(function () {
            Route::get('/', [PermissionController::class, 'index'])->name('index');
            Route::post('/', [PermissionController::class, 'store'])->name('store');
            Route::get('/{id}', [PermissionController::class, 'show'])->name('show');
            Route::put('/{id}', [PermissionController::class, 'update'])->name('update');
            Route::delete('/{id}', [PermissionController::class, 'destroy'])->name('destroy');
            Route::post('bulk-create', [PermissionController::class, 'bulkCreate'])->name('bulk-create');
            Route::get('{id}/roles', [PermissionController::class, 'getRoles'])->name('roles');
            Route::post('{id}/assign-role', [PermissionController::class, 'assignRole'])->name('assign-role');
        });

        // User Management
        Route::prefix('user')->name('user.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{id}', [UserController::class, 'show'])->name('show');
            Route::put('/{id}', [UserController::class, 'update'])->name('update');
            Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
            Route::get('/{id}/roles', [UserController::class, 'getRoles'])->name('roles.get');
            Route::post('/{id}/assign-role', [UserController::class, 'assignRole'])->name('assign-role');
            Route::get('/{id}/permissions', [UserController::class, 'getPermissions'])->name('permissions.get');
            Route::post('/{id}/assign-permission', [UserController::class, 'assignPermission'])->name('assign-permission');
            Route::post('/{id}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
        });
    });

    // 3. Master Data
    Route::resource('kurikulum', KurikulumController::class);
    Route::post('kurikulum/{kurikulum}/set-active', [KurikulumController::class, 'setActive'])->name('kurikulum.set-active');

    Route::resource('tahun-akademik', TahunAkademikController::class);
    Route::post('tahun-akademik/{tahunAkademik}/set-active', [TahunAkademikController::class, 'setActive'])->name('tahun-akademik.set-active');
    Route::get('get-kurikulum', [TahunAkademikController::class, 'getKurikulum'])->name('tahun-akademik.get-kurikulum');

    Route::resource('semester', SemesterController::class);
    Route::post('semester/{semester}/set-active', [SemesterController::class, 'setActive'])->name('semester.set-active');
    Route::get('get-tahun-akademik', [SemesterController::class, 'getTahunAkademik'])->name('semester.get-tahun-akademik');

    Route::resource('jurusan', JurusanController::class);
    Route::post('jurusan/{jurusan}/toggle-active', [JurusanController::class, 'toggleActive'])->name('jurusan.toggle-active');

    Route::prefix('kelas')->name('kelas.')->group(function () {
        Route::get('/', [KelasController::class, 'index'])->name('index');
        Route::get('/create', [KelasController::class, 'create'])->name('create');
        Route::post('/', [KelasController::class, 'store'])->name('store');
        Route::get('/{kelas}', [KelasController::class, 'show'])->name('show');
        Route::get('/{kelas}/edit', [KelasController::class, 'edit'])->name('edit');
        Route::put('/{kelas}', [KelasController::class, 'update'])->name('update');
        Route::delete('/{kelas}', [KelasController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-create', [KelasController::class, 'bulkCreate'])->name('bulk-create');
        Route::post('/copy-from-previous', [KelasController::class, 'copyFromPrevious'])->name('copy-from-previous');
        Route::post('/{kelas}/assign-wali-kelas', [KelasController::class, 'assignWaliKelas'])->name('assign-wali-kelas');
        Route::delete('/{kelas}/remove-wali-kelas', [KelasController::class, 'removeWaliKelas'])->name('remove-wali-kelas');
        Route::get('/{kelas}/siswa', [KelasController::class, 'getSiswa'])->name('get-siswa');
        Route::get('/{kelas}/mata-pelajaran', [MataPelajaranKelasController::class, 'index'])->name('mata-pelajaran.index');
        Route::post('/{kelas}/mata-pelajaran', [MataPelajaranKelasController::class, 'store'])->name('mata-pelajaran.store');
    });

    Route::prefix('mata-pelajaran-kelas')->name('mata-pelajaran-kelas.')->group(function () {
        Route::get('/{id}', [MataPelajaranKelasController::class, 'show'])->name('show');
        Route::put('/{id}', [MataPelajaranKelasController::class, 'update'])->name('update');
        Route::delete('/{id}', [MataPelajaranKelasController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/assign-guru', [MataPelajaranKelasController::class, 'assignGuru'])->name('assign-guru');
        Route::delete('/{id}/remove-guru/{guruId}', [MataPelajaranKelasController::class, 'removeGuru'])->name('remove-guru');
        Route::get('/search/gurus', [MataPelajaranKelasController::class, 'getGurus'])->name('get-gurus');
    });

    Route::resource('mata-pelajaran', MataPelajaranController::class);
    Route::post('mata-pelajaran/{mataPelajaran}/toggle-active', [MataPelajaranController::class, 'toggleActive'])->name('toggle-active');
    Route::post('mata-pelajaran/{mataPelajaran}/assign-to-kelas', [MataPelajaranController::class, 'assignToKelas'])->name('assign-to-kelas');

    Route::resource('guru', GuruController::class);
    Route::post('guru/{guru}/toggle-active', [GuruController::class, 'toggleActive'])->name('toggle-active');
    Route::get('guru/search/autocomplete', [GuruController::class, 'search'])->name('guru.search');
    Route::get('guru/{guru}/data', [GuruController::class, 'getById'])->name('guru.get-by-id');
    Route::get('guru/export/excel', [GuruController::class, 'export'])->name('guru.export');
    Route::post('guru/import/excel', [GuruController::class, 'import'])->name('guru.import');

    Route::resource('siswa', SiswaController::class);
    Route::post('siswa/{siswa}/assign-kelas', [SiswaController::class, 'assignKelas'])->name('siswa.assign-kelas');
    Route::delete('siswa/{siswa}/remove-kelas/{kelas}', [SiswaController::class, 'removeKelas'])->name('siswa.remove-kelas');
    Route::get('siswa/check-nisn', [SiswaController::class, 'checkNisn'])->name('siswa.check-nisn');
    Route::get('siswa/search/autocomplete', [SiswaController::class, 'search'])->name('siswa.search');
    Route::get('siswa/export/excel', [SiswaController::class, 'export'])->name('siswa.export');
    Route::post('siswa/import/excel', [SiswaController::class, 'import'])->name('siswa.import');

    Route::resource('orang-tua', OrangTuaController::class);
    Route::post('orang-tua/{orangTua}/create-account', [OrangTuaController::class, 'createAccount'])->name('orang-tua.create-account');
    Route::get('orang-tua/search/autocomplete', [OrangTuaController::class, 'search'])->name('orang-tua.search');
    Route::get('orang-tua/export/excel', [OrangTuaController::class, 'export'])->name('orang-tua.export');
    Route::post('orang-tua/import/excel', [OrangTuaController::class, 'import'])->name('orang-tua.import');

    // 4. Akademik / Pembelajaran
    Route::prefix('jadwal-pelajaran')->name('jadwal-pelajaran.')->group(function () {
        Route::get('/', [JadwalPelajaranController::class, 'index'])->name('index');
        Route::get('/create', [JadwalPelajaranController::class, 'create'])->name('create');
        Route::post('/', [JadwalPelajaranController::class, 'store'])->name('store');
        Route::get('/{jadwalPelajaran}', [JadwalPelajaranController::class, 'show'])->name('show');
        Route::get('/{jadwalPelajaran}/edit', [JadwalPelajaranController::class, 'edit'])->name('edit');
        Route::put('/{jadwalPelajaran}', [JadwalPelajaranController::class, 'update'])->name('update');
        Route::delete('/{jadwalPelajaran}', [JadwalPelajaranController::class, 'destroy'])->name('destroy');
        Route::get('/kelas/{kelas}', [JadwalPelajaranController::class, 'viewByKelas'])->name('by-kelas');
        Route::get('/guru/{guru}', [JadwalPelajaranController::class, 'viewByGuru'])->name('by-guru');
        Route::get('/get-mapel-by-kelas', [JadwalPelajaranController::class, 'getMataPelajaranByKelas'])->name('get-mapel-by-kelas');
    });

    Route::get('presensi/rekap', [PresensiController::class, 'rekap'])->name('presensi.rekap');
    Route::resource('presensi', PresensiController::class)->only(['index', 'create', 'store']);

    Route::resource('komponen-nilai', KomponenNilaiController::class);
    Route::resource('ekstrakurikuler', EkstrakurikulerController::class);
    Route::resource('nilai', NilaiController::class)->only(['index', 'create', 'store']);
    Route::resource('nilai-sikap', NilaiSikapController::class)->only(['index', 'create', 'store']);
    Route::resource('nilai-ekstrakurikuler', NilaiEkstrakurikulerController::class)->only(['index', 'create', 'store']);

    // CBT & Ujian
    Route::prefix('bank-soal')->name('bank-soal.')->group(function () {
        Route::post('/import-excel', [BankSoalController::class, 'importExcel'])->name('import');
        Route::get('/template', [BankSoalController::class, 'downloadTemplate'])->name('template');
        Route::post('/{id}/duplicate', [BankSoalController::class, 'duplicate'])->name('duplicate');
    });
    Route::resource('bank-soal', BankSoalController::class);
    Route::resource('soal', SoalController::class)->except(['index', 'show']);
    
    Route::prefix('jadwal-ujian')->name('jadwal-ujian.')->group(function () {
        Route::get('/{jadwal_ujian}/manage-soal', [JadwalUjianController::class, 'manageSoal'])->name('manage-soal');
        Route::post('/{jadwal_ujian}/add-soal', [JadwalUjianController::class, 'addSoal'])->name('add-soal');
        Route::delete('/remove-soal/{id}', [JadwalUjianController::class, 'removeSoal'])->name('remove-soal');
        Route::post('/reorder-soal', [JadwalUjianController::class, 'reorderSoal'])->name('reorder-soal');
        Route::post('/{jadwal_ujian}/regenerate-soal', [JadwalUjianController::class, 'regenerateSoalByDifficulty'])->name('regenerate-soal');
        Route::post('/{jadwal_ujian}/status', [JadwalUjianController::class, 'setStatus'])->name('status');
    });
    Route::resource('jadwal-ujian', JadwalUjianController::class);

    Route::prefix('ujian-siswa')->name('ujian-siswa.')->group(function () {
        Route::get('/', [UjianSiswaController::class, 'index'])->name('index');
        Route::get('/intro/{id}', [UjianSiswaController::class, 'show'])->name('show');
        Route::post('/start/{id}', [UjianSiswaController::class, 'start'])->name('start');
        Route::get('/take/{id}', [UjianSiswaController::class, 'take'])->name('take');
        Route::post('/save-answer', [UjianSiswaController::class, 'saveAnswer'])->name('save-answer');
        Route::post('/log-violation', [UjianSiswaController::class, 'logViolation'])->name('log-violation');
        Route::post('/finish/{id}', [UjianSiswaController::class, 'finish'])->name('finish');
    });

    // 5. PKL / Magang
    Route::resource('perusahaan-pkl', PerusahaanPklController::class);
    Route::resource('pkl', PklController::class);
    
    Route::prefix('jurnal-pkl')->name('jurnal-pkl.')->group(function () {
        Route::get('/pembimbing', [JurnalPklController::class, 'pembimbingIndex'])->name('pembimbing');
        Route::post('/{id}/status', [JurnalPklController::class, 'setStatus'])->name('set-status');
    });
    Route::resource('jurnal-pkl', JurnalPklController::class);

    Route::prefix('pkl-nilai')->name('pkl-nilai.')->group(function () {
        Route::get('/', [NilaiPklController::class, 'index'])->name('index');
        Route::get('/{pklId}/edit', [NilaiPklController::class, 'edit'])->name('edit');
        Route::post('/{pklId}', [NilaiPklController::class, 'update'])->name('update');
    });

    // 6. Reports (Raport & Legger)
    Route::prefix('raport')->name('raport.')->group(function () {
        Route::get('/', [RaportController::class, 'index'])->name('index');
        Route::post('/generate/{siswa_id}/{semester_id}', [RaportController::class, 'generate'])->name('generate');
        Route::get('/{id}', [RaportController::class, 'show'])->name('show');
        Route::post('/{id}', [RaportController::class, 'update'])->name('update');
        Route::post('/{id}/approve', [RaportController::class, 'approve'])->name('approve');
        Route::post('/{id}/publish', [RaportController::class, 'publish'])->name('publish');
        Route::get('/{id}/print', [RaportController::class, 'print'])->name('print');
    });

    Route::prefix('legger')->name('legger.')->group(function () {
        Route::get('/', [LeggerController::class, 'index'])->name('index');
        Route::post('/generate', [LeggerController::class, 'generate'])->name('generate');
        Route::get('/{id}/excel', [LeggerController::class, 'exportExcel'])->name('excel');
        Route::get('/{id}/pdf', [LeggerController::class, 'exportPdf'])->name('pdf');
        Route::get('/{id}', [LeggerController::class, 'show'])->name('show');
    });

    // 7. E-Learning
    Route::prefix('elearning')->name('elearning.')->group(function () {
        Route::get('/', [ELearningController::class, 'index'])->name('index');
        Route::get('/course/{id}', [ELearningController::class, 'course'])->name('course');
    });

    Route::prefix('materi')->name('materi.')->group(function () {
        Route::post('/', [MateriAjarController::class, 'store'])->name('store');
        Route::get('/{id}/download', [MateriAjarController::class, 'download'])->name('download');
        Route::delete('/{id}', [MateriAjarController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('tugas')->name('tugas.')->group(function () {
        Route::get('/{id}', [TugasController::class, 'show'])->name('show');
        Route::post('/', [TugasController::class, 'store'])->name('store');
        Route::post('/{id}/submit', [TugasController::class, 'submit'])->name('submit');
        Route::post('/submission/{id}/grade', [TugasController::class, 'grade'])->name('grade');
    });

    Route::prefix('forum')->name('forum.')->group(function () {
        Route::get('/{id}', [ForumDiskusiController::class, 'show'])->name('show');
        Route::post('/', [ForumDiskusiController::class, 'store'])->name('store');
        Route::post('/{id}/reply', [ForumDiskusiController::class, 'reply'])->name('reply');
    });

    // 8. Jurnal Mengajar
    Route::prefix('jurnal-mengajar')->name('jurnal-mengajar.')->group(function () {
        Route::get('/', [JurnalMengajarController::class, 'index'])->name('index');
        Route::get('/create', [JurnalMengajarController::class, 'create'])->name('create');
        Route::post('/', [JurnalMengajarController::class, 'store'])->name('store');
        Route::get('/{id}', [JurnalMengajarController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [JurnalMengajarController::class, 'approve'])->name('approve');
    });

    // 9. Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('markAllRead');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    // 10. Kenaikan Kelas
    Route::prefix('kenaikan-kelas')->name('kenaikan-kelas.')->group(function () {
        Route::get('/', [KenaikanKelasController::class, 'index'])->name('index');
        Route::get('/simulasi', [KenaikanKelasController::class, 'simulasi'])->name('simulasi');
        Route::post('/eksekusi', [KenaikanKelasController::class, 'eksekusi'])->name('eksekusi');
        Route::get('/{id}', [KenaikanKelasController::class, 'show'])->name('show');
    });

    // Other Modules
    Route::resource('buku-induk', BukuIndukController::class)->only(['index', 'show', 'edit', 'update']);
    Route::resource('prestasi-siswa', PrestasiSiswaController::class);
    Route::resource('pelanggaran-siswa', PelanggaranSiswaController::class);
});

require __DIR__ . '/auth.php';
