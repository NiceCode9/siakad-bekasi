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
    });
});

require __DIR__ . '/auth.php';
