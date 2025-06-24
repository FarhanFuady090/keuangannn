<?php

use App\Http\Controllers\admin\Auth\LoginController;
use App\Http\Controllers\admin\Auth\RegisteredUserController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\admin\Auth\SiswaController;
use App\Http\Controllers\admin\Auth\KelasController;
use App\Http\Controllers\admin\Auth\KasController;
use App\Http\Controllers\Admin\Auth\UnitPendidikanController;
use App\Http\Controllers\Admin\Auth\DashboardController;
use App\Http\Controllers\Admin\Auth\TahunAjaranController;
use App\Http\Controllers\Admin\Auth\PerpindahanKelasController;
use App\Http\Controllers\Admin\Auth\JenisPembayaranController;
use App\Http\Controllers\AdminProfileController;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', action: [DashboardController::class, 'showUser'])->name(name: 'admin.dashboard')->middleware(['auth:admin', 'verified']);
Route::get('/dashboard', function () {
    return redirect('/admin/dashboard');
});

Route::prefix('admin')->middleware('guest:admin')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('admin.login');
    Route::post('login', [LoginController::class, 'store']);
});

Route::prefix('admin')->middleware('auth:admin', 'verified')->group(function () {

    Route::get('/dashboard', action: [DashboardController::class, 'showUser'])->name(name: 'admin.dashboard');

    Route::middleware('auth')->group(function () {
        Route::get('/perpindahan-kelas', [PerpindahanKelasController::class, 'index'])->name('admin.perpindahan-kelas');
        Route::post('/perpindahan-kelas', [PerpindahanKelasController::class, 'proses'])->name('admin.proses');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/manage-data-siswa', [SiswaController::class, 'index'])->name('admin.manage-data-siswa');
        Route::get('/detail-data-siswa/{id}', [SiswaController::class, 'detailSiswa'])->name('admin.detail-data-siswa');
        Route::get('/create-data-siswa', [SiswaController::class, 'createSiswa'])->name('admin.create-data-siswa');
        Route::post('/submit', [SiswaController::class, 'submitSiswa'])->name('admin.submitSiswa');
        Route::get('/edit/{id}', [SiswaController::class, 'editSiswa'])->name('admin.edit-data-siswa');
        Route::post('/update/{id}', [SiswaController::class, 'updateSiswa'])->name('admin.updateSiswa');

        Route::post('import-data-siswa', [SiswaController::class, 'importData'])->name('admin.import-data-siswa');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/manage-data-kas', [KasController::class, 'index'])->name('admin.manage-data-kas');
        Route::get('/create-data-kas', [KasController::class, 'createKas'])->name('admin.create-data-kas');
        Route::post('/submit-kas', [KasController::class, 'submitKas'])->name('admin.submitKas');
        Route::get('/edit-kas/{id}', [KasController::class, 'editKas'])->name('admin.edit-data-kas');
        Route::post('/update-kas/{id}', [KasController::class, 'updateKas'])->name('admin.updateKas');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/manage-kelas', [KelasController::class, 'index'])->name('admin.manage-kelas');
        Route::get('/create-kelas', [KelasController::class, 'create'])->name('admin.create-kelas');
        Route::post('/submitt', [KelasController::class, 'submitt'])->name('admin.submitt');
        Route::get('/kelas/{id}/edit', [KelasController::class, 'editt'])->name('admin.edit-kelas');
        Route::post('/{id}/updatee', [KelasController::class, 'updatee'])->name('admin.updatee');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/manage-unit-pendidikan', [UnitPendidikanController::class, 'index'])->name('admin.manage-unit-pendidikan');
        Route::get('/create-manage-unit-pendidikan', [UnitPendidikanController::class, 'createUnitPendidikan'])->name('admin.create-manage-unit-pendidikan');
        Route::post('/submittt', [UnitPendidikanController::class, 'submitUnitPendidikan'])->name('admin.submitUnitPendidikan');
        Route::get('/edittt/{id}', [UnitPendidikanController::class, 'editUnitPendidikan'])->name('admin.edit-manage-unit-pendidikan');
        Route::post('/updateee/{id}', [UnitPendidikanController::class, 'updateUnitPendidikan'])->name('admin.updateUnitPendidikan');
        Route::get('/admin/filter-unit-pendidikan', [UnitPendidikanController::class, 'filterData'])->name('admin.filter-unit-pendidikan');
        Route::get('/delete/{id}', [UnitPendidikanController::class, 'deleteUnitPendidikan'])->name('admin.delete');
    });

    Route::middleware('auth')->group(callback: function () {
        Route::get('/manage-user', [UserController::class, 'indexuser'])->name('admin.manage-user');
        Route::get('/create-user', [UserController::class, 'createuser'])->name('admin.create-user');
        Route::post('/submit-user', [UserController::class, 'submituser'])->name('admin.submitUser');
        Route::get('/edit-user/{id}', action: [UserController::class, 'edituser'])->name('admin.update-user');
        Route::post('/update-user/{id}', [UserController::class, 'updateuserr'])->name('admin.updateuserrr');

        Route::get('/delete-user/{id}', [UserController::class, 'deleteuserr'])->name('admin.deleteuserrr');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/profile', action: [AdminProfileController::class, 'edit'])->name('admin.profile.edit');
        Route::patch('/profile', action: [AdminProfileController::class, 'update'])->name('admin.profile.update');
        Route::delete('/profile', action: [AdminProfileController::class, 'destroy'])->name('admin.profile.destroy');
    });

    Route::middleware('auth')->group(function () {
        // Tampilkan halaman data awal (dengan pagination)
        Route::get('/manage-jenis-pembayaran', [JenisPembayaranController::class, 'index'])->name('admin.manage-jenis-pembayaran');
    
        // Form tambah data
        Route::get('/create-jenis-pembayaran', [JenisPembayaranController::class, 'create'])->name('admin.create-jenis-pembayaran');
    
        // Proses simpan data
        Route::post('/create-jenis-pembayaran', [JenisPembayaranController::class, 'store'])->name('admin.create-jenis-pembayaran-submit');
    
        // Form edit data
        Route::get('/edit-jenis-pembayaran/{id}', [JenisPembayaranController::class, 'editData'])->name('admin.edit-jenis-pembayaran');
    
        // Proses update data
        Route::post('/update-jenis-pembayaran/{id}', [JenisPembayaranController::class, 'updateData'])->name('admin.update-jenis-pembayaran');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/manage-tahun-ajaran', [TahunAjaranController::class, 'index'])->name('admin.manage-tahun-ajaran');
        Route::get('/create-tahun-ajaran', [TahunAjaranController::class, 'createTahunAjaran'])->name('admin.create-tahun-ajaran');
        Route::post('/submitttt', [TahunAjaranController::class, 'submitTahunAjaran'])->name('admin.submitTahunAjaran');
        Route::get('/editttt/{id}', [TahunAjaranController::class, 'editTahunAjaran'])->name('admin.edit-tahun-ajaran');
        Route::post('/updateeee/{id}', [TahunAjaranController::class, 'updateTahunAjaran'])->name('admin.updateTahunAjaran');
    });


    Route::post('logout', [LoginController::class, 'destroy'])->name('admin.logout');
});
