<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route::get('/menus', [App\Http\Controllers\MenuController::class, 'index'])->name('menus.index');
    Route::get('/users', [App\Http\Controllers\Admin\MenuController::class, 'users'])->name('menus.users');
    Route::get('/roles', [App\Http\Controllers\Admin\MenuController::class, 'roles'])->name('menus.roles');
    Route::get('/permissions', [App\Http\Controllers\Admin\MenuController::class, 'permissions'])->name('menus.permissions');
    Route::get('/reports', [App\Http\Controllers\Admin\MenuController::class, 'reports'])->name('menus.reports');

    Route::middleware('role:super-admin')->prefix('admin')->name('admin.')->group(function () {
        // menu management
        Route::prefix('menu')->name('menu.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\MenuController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\MenuController::class, 'store'])->name('store');
            Route::get('/{id}', [App\Http\Controllers\Admin\MenuController::class, 'show'])->name('show');
            Route::put('/{id}', [App\Http\Controllers\Admin\MenuController::class, 'update'])->name('update');
            Route::delete('/{id}', [App\Http\Controllers\Admin\MenuController::class, 'destroy'])->name('destroy');

            Route::get('/parents/list', [App\Http\Controllers\Admin\MenuController::class, 'getParents'])->name('parents');

            // Role & Permission Assignment
            Route::get('/{id}/roles', [App\Http\Controllers\Admin\MenuController::class, 'getRoles'])->name('roles.get');
            Route::post('/{id}/roles', [App\Http\Controllers\Admin\MenuController::class, 'assignRole'])->name('roles.assign');

            Route::get('/{id}/permissions', [App\Http\Controllers\Admin\MenuController::class, 'getPermissions'])->name('permissions.get');
            Route::post('/{id}/permissions', [App\Http\Controllers\Admin\MenuController::class, 'assignPermission'])->name('permissions.assign');
        });
        // Role Management
        Route::prefix('role')->name('role.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\RoleController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\RoleController::class, 'store'])->name('store');
            Route::get('/{id}', [App\Http\Controllers\Admin\RoleController::class, 'show'])->name('show');
            Route::put('/{id}', [App\Http\Controllers\Admin\RoleController::class, 'update'])->name('update');
            Route::delete('/{id}', [App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('destroy');

            Route::get('/{id}/permissions', [App\Http\Controllers\Admin\RoleController::class, 'getPermissions'])->name('permissions.get');
            Route::post('/{id}/permissions', [App\Http\Controllers\Admin\RoleController::class, 'assignPermission'])->name('permissions.assign');
        });
        //Role Permission
        Route::prefix('permission')->name('permission.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\PermissionController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\PermissionController::class, 'store'])->name('store');
            Route::get('/{id}', [App\Http\Controllers\Admin\PermissionController::class, 'show'])->name('show');
            Route::put('/{id}', [App\Http\Controllers\Admin\PermissionController::class, 'update'])->name('update');
            Route::delete('/{id}', [App\Http\Controllers\Admin\PermissionController::class, 'destroy'])->name('destroy');
            Route::post('bulk-create', [App\Http\Controllers\Admin\PermissionController::class, 'bulkCreate'])
                ->name('bulk-create');
            Route::get('{id}/roles', [App\Http\Controllers\Admin\PermissionController::class, 'getRoles'])
                ->name('roles');
            Route::post('{id}/assign-role', [App\Http\Controllers\Admin\PermissionController::class, 'assignRole'])
                ->name('assign-role');
        });
        // User Management Routes
        Route::prefix('user')->name('user.')->group(function () {

            // Main CRUD Routes
            Route::get('/', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('store');
            Route::get('/{id}', [App\Http\Controllers\Admin\UserController::class, 'show'])->name('show');
            Route::put('/{id}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('update');
            Route::delete('/{id}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('destroy');

            // Toggle User Status (Active/Inactive)
            Route::post('/{id}/toggle-status', [App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('toggle-status');

            // Assign Roles to User
            Route::get('/{id}/roles', [App\Http\Controllers\Admin\UserController::class, 'getRoles'])->name('roles.get');
            Route::post('/{id}/assign-role', [App\Http\Controllers\Admin\UserController::class, 'assignRole'])->name('assign-role');

            // Assign Permissions to User
            Route::get('/{id}/permissions', [App\Http\Controllers\Admin\UserController::class, 'getPermissions'])->name('permissions.get');
            Route::post('/{id}/assign-permission', [App\Http\Controllers\Admin\UserController::class, 'assignPermission'])->name('assign-permission');

            // Reset Password
            Route::post('/{id}/reset-password', [App\Http\Controllers\Admin\UserController::class, 'resetPassword'])->name('reset-password');
        });
    });

    // Kurikulum
    Route::resource('kurikulum', App\Http\Controllers\KurikulumController::class);
    Route::post('kurikulum/{kurikulum}/set-active', [App\Http\Controllers\KurikulumController::class, 'setActive'])
        ->name('kurikulum.set-active');
    // Tahun Akademik
    Route::resource('tahun-akademik', App\Http\Controllers\TahunAkademikController::class);
    Route::post('tahun-akademik/{tahunAkademik}/set-active', [App\Http\Controllers\TahunAkademikController::class, 'setActive'])
        ->name('tahun-akademik.set-active');
    Route::get('get-kurikulum', [App\Http\Controllers\TahunAkademikController::class, 'getKurikulum'])
        ->name('tahun-akademik.get-kurikulum');
    // Semester
    Route::resource('semester', App\Http\Controllers\SemesterController::class);
    Route::post('semester/{semester}/set-active', [App\Http\Controllers\SemesterController::class, 'setActive'])
        ->name('semester.set-active');
    Route::get('get-tahun-akademik', [App\Http\Controllers\SemesterController::class, 'getTahunAkademik'])->name('semester.get-tahun-akademik');

    // Jurusan
    Route::resource('jurusan', App\Http\Controllers\JurusanController::class);
    Route::post('jurusan/{jurusan}/toggle-active', [App\Http\Controllers\JurusanController::class, 'toggleActive'])
        ->name('jurusan.toggle-active');

    // Kelas Routes
    Route::prefix('kelas')->name('kelas.')->group(function () {
        Route::get('/', [App\Http\Controllers\KelasController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\KelasController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\KelasController::class, 'store'])->name('store');
        Route::get('/{kelas}', [App\Http\Controllers\KelasController::class, 'show'])->name('show');
        Route::get('/{kelas}/edit', [App\Http\Controllers\KelasController::class, 'edit'])->name('edit');
        Route::put('/{kelas}', [App\Http\Controllers\KelasController::class, 'update'])->name('update');
        Route::delete('/{kelas}', [App\Http\Controllers\KelasController::class, 'destroy'])->name('destroy');

        // Additional routes
        Route::post('/bulk-create', [App\Http\Controllers\KelasController::class, 'bulkCreate'])->name('bulk-create');
        Route::post('/copy-from-previous', [App\Http\Controllers\KelasController::class, 'copyFromPrevious'])->name('copy-from-previous');
        Route::post('/{kelas}/assign-wali-kelas', [App\Http\Controllers\KelasController::class, 'assignWaliKelas'])->name('assign-wali-kelas');
        Route::delete('/{kelas}/remove-wali-kelas', [App\Http\Controllers\KelasController::class, 'removeWaliKelas'])->name('remove-wali-kelas');
        Route::get('/{kelas}/siswa', [App\Http\Controllers\KelasController::class, 'getSiswa'])->name('get-siswa');
    });

    // Mata Pelajaran Routes
    Route::prefix('mata-pelajaran')->name('mata-pelajaran.')->group(function () {
        Route::get('/', [App\Http\Controllers\MataPelajaranController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\MataPelajaranController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\MataPelajaranController::class, 'store'])->name('store');
        Route::get('/{mataPelajaran}', [App\Http\Controllers\MataPelajaranController::class, 'show'])->name('show');
        Route::get('/{mataPelajaran}/edit', [App\Http\Controllers\MataPelajaranController::class, 'edit'])->name('edit');
        Route::put('/{mataPelajaran}', [App\Http\Controllers\MataPelajaranController::class, 'update'])->name('update');
        Route::delete('/{mataPelajaran}', [App\Http\Controllers\MataPelajaranController::class, 'destroy'])->name('destroy');

        // Additional routes
        Route::post('/{mataPelajaran}/toggle-active', [App\Http\Controllers\MataPelajaranController::class, 'toggleActive'])->name('toggle-active');
        Route::post('/{mataPelajaran}/assign-to-kelas', [App\Http\Controllers\MataPelajaranController::class, 'assignToKelas'])->name('assign-to-kelas');
    });

    // Jadwal Pelajaran Routes
    Route::prefix('jadwal-pelajaran')->name('jadwal-pelajaran.')->group(function () {
        Route::get('/', [App\Http\Controllers\JadwalPelajaranController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\JadwalPelajaranController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\JadwalPelajaranController::class, 'store'])->name('store');
        Route::get('/{jadwalPelajaran}', [App\Http\Controllers\JadwalPelajaranController::class, 'show'])->name('show');
        Route::get('/{jadwalPelajaran}/edit', [App\Http\Controllers\JadwalPelajaranController::class, 'edit'])->name('edit');
        Route::put('/{jadwalPelajaran}', [App\Http\Controllers\JadwalPelajaranController::class, 'update'])->name('update');
        Route::delete('/{jadwalPelajaran}', [App\Http\Controllers\JadwalPelajaranController::class, 'destroy'])->name('destroy');

        // Additional routes
        Route::get('/kelas/{kelas}', [App\Http\Controllers\JadwalPelajaranController::class, 'viewByKelas'])->name('by-kelas');
        Route::get('/guru/{guru}', [App\Http\Controllers\JadwalPelajaranController::class, 'viewByGuru'])->name('by-guru');
        Route::get('/get-mapel-by-kelas', [App\Http\Controllers\JadwalPelajaranController::class, 'getMataPelajaranByKelas'])->name('get-mapel-by-kelas');
    });

    // Guru Routes
    Route::prefix('guru')->name('guru.')->group(function () {
        Route::get('/', [App\Http\Controllers\GuruController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\GuruController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\GuruController::class, 'store'])->name('store');
        Route::get('/{guru}', [App\Http\Controllers\GuruController::class, 'show'])->name('show');
        Route::get('/{guru}/edit', [App\Http\Controllers\GuruController::class, 'edit'])->name('edit');
        Route::put('/{guru}', [App\Http\Controllers\GuruController::class, 'update'])->name('update');
        Route::delete('/{guru}', [App\Http\Controllers\GuruController::class, 'destroy'])->name('destroy');

        // Additional routes
        Route::post('/{guru}/toggle-active', [App\Http\Controllers\GuruController::class, 'toggleActive'])->name('toggle-active');
        Route::get('/search/autocomplete', [App\Http\Controllers\GuruController::class, 'search'])->name('search');
        Route::get('/{guru}/data', [App\Http\Controllers\GuruController::class, 'getById'])->name('get-by-id');
        Route::get('/export/excel', [App\Http\Controllers\GuruController::class, 'export'])->name('export');
        Route::post('/import/excel', [App\Http\Controllers\GuruController::class, 'import'])->name('import');
    });

    // Routes untuk Siswa
    Route::prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/', [App\Http\Controllers\SiswaController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\SiswaController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\SiswaController::class, 'store'])->name('store');
        Route::get('/{siswa}', [App\Http\Controllers\SiswaController::class, 'show'])->name('show');
        Route::get('/{siswa}/edit', [App\Http\Controllers\SiswaController::class, 'edit'])->name('edit');
        Route::put('/{siswa}', [App\Http\Controllers\SiswaController::class, 'update'])->name('update');
        Route::delete('/{siswa}', [App\Http\Controllers\SiswaController::class, 'destroy'])->name('destroy');

        // Additional routes
        Route::post('/{siswa}/assign-kelas', [App\Http\Controllers\SiswaController::class, 'assignKelas'])->name('assign-kelas');
        Route::delete('/{siswa}/remove-kelas/{kelas}', [App\Http\Controllers\SiswaController::class, 'removeKelas'])->name('remove-kelas');
        Route::get('/check-nisn', [App\Http\Controllers\SiswaController::class, 'checkNisn'])->name('check-nisn');
        Route::get('/search/autocomplete', [App\Http\Controllers\SiswaController::class, 'search'])->name('search');
        Route::get('/export/excel', [App\Http\Controllers\SiswaController::class, 'export'])->name('export');
        Route::post('/import/excel', [App\Http\Controllers\SiswaController::class, 'import'])->name('import');
    });

    // Routes untuk Orang Tua
    Route::prefix('orang-tua')->name('orang-tua.')->group(function () {
        Route::get('/', [App\Http\Controllers\OrangTuaController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\OrangTuaController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\OrangTuaController::class, 'store'])->name('store');
        Route::get('/{orangTua}', [App\Http\Controllers\OrangTuaController::class, 'show'])->name('show');
        Route::get('/{orangTua}/edit', [App\Http\Controllers\OrangTuaController::class, 'edit'])->name('edit');
        Route::put('/{orangTua}', [App\Http\Controllers\OrangTuaController::class, 'update'])->name('update');
        Route::delete('/{orangTua}', [App\Http\Controllers\OrangTuaController::class, 'destroy'])->name('destroy');

        // Additional routes
        Route::post('/{orangTua}/create-account', [App\Http\Controllers\OrangTuaController::class, 'createAccount'])->name('create-account');
        Route::get('/search/autocomplete', [App\Http\Controllers\OrangTuaController::class, 'search'])->name('search');
        Route::get('/export/excel', [App\Http\Controllers\OrangTuaController::class, 'export'])->name('export');
        Route::post('/import/excel', [App\Http\Controllers\OrangTuaController::class, 'import'])->name('import');
    });
});

require __DIR__ . '/auth.php';
