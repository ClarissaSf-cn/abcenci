<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LemburController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StationeryController;
use App\Http\Controllers\StationeryTransactionController;
use App\Http\Controllers\LaporanStationeryController;
use App\Http\Controllers\PengadaanController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\KioskController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\EmployeeScheduleController;
use App\Http\Controllers\ClosingPeriodeController;
use App\Http\Controllers\PenilaianController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::get('/api/karyawan/{nik}', [KioskController::class, 'getKaryawanByNik'])->name('api.karyawan.nik');
Route::get('/api/karyawan-all', [KioskController::class, 'getAllKaryawan'])->name('api.karyawan.all');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/refresh-csrf', function () {
        return response()->json([
            'token'  => csrf_token(),
            'status' => 'active'
        ]);
    })->name('refresh.csrf');

    Route::get('/dashboard', [AttendanceController::class, 'index'])->name('dashboard');

    Route::get('/dashboard-karyawan', [AttendanceController::class, 'dashboardKaryawan'])->name('dashboard.karyawan');

    Route::get('/form-absen', function () {
        return redirect()->to(route('dashboard') . '#form-absen');
    })->name('absen.form');

    Route::prefix('employees')->group(function () {
        Route::post('/import', [AttendanceController::class, 'importEmployee'])->name('employees.import');
        Route::post('/store', [AttendanceController::class, 'storeEmployee'])->name('employees.store');
        Route::put('/{id}', [AttendanceController::class, 'updateEmployee'])->name('employees.update');
        Route::delete('/{id}', [AttendanceController::class, 'destroyEmployee'])->name('employees.destroy');
    });

    Route::prefix('attendance')->group(function () {
        Route::post('/store', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::post('/import', [AttendanceController::class, 'importExcel'])->name('attendance.import');
        Route::get('/export', [AttendanceController::class, 'exportExcel'])->name('attendance.export');
        Route::put('/{id}', [AttendanceController::class, 'update'])->name('attendance.update');
        Route::delete('/{id}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');
    });

    Route::prefix('lembur')->group(function () {
        Route::post('/store', [LemburController::class, 'store'])->name('lembur.store');
        Route::delete('/{id}', [LemburController::class, 'destroy'])->name('lembur.destroy');
    });

    Route::prefix('users')->group(function () {
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::put('/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/restore/{id}', [UserController::class, 'restore'])->name('users.restore');
        Route::delete('/force-delete/{id}', [UserController::class, 'forceDelete'])->name('users.force_delete');
    });

    Route::prefix('shifts')->group(function () {
        Route::get('/', [ShiftController::class, 'index'])->name('shifts.index');
        Route::post('/store', [ShiftController::class, 'store'])->name('shifts.store');
        Route::post('/import', [ShiftController::class, 'import'])->name('shifts.import');
        Route::get('/export-pdf', [ShiftController::class, 'exportPdf'])->name('shifts.export-pdf');
        Route::get('/export-excel', [ShiftController::class, 'exportExcel'])->name('shifts.export-excel');
        Route::delete('/bulk-destroy', [ShiftController::class, 'bulkDestroy'])->name('shifts.bulk-destroy');

        Route::put('/{id}', [ShiftController::class, 'update'])->name('shifts.update');
        Route::delete('/{id}', [ShiftController::class, 'destroy'])->name('shifts.destroy');
    });

    Route::prefix('schedules')->group(function () {
        Route::get('/', [EmployeeScheduleController::class, 'index'])->name('schedules.index');
        Route::post('/store', [EmployeeScheduleController::class, 'store'])->name('schedules.store');

        Route::post('/import', [EmployeeScheduleController::class, 'import'])->name('schedules.import');

        Route::get('/export-pdf', [EmployeeScheduleController::class, 'exportPdf'])->name('schedules.export-pdf');

        Route::get('/export-excel', [EmployeeScheduleController::class, 'exportExcel'])->name('schedules.export-excel');

        Route::put('/{id}', [EmployeeScheduleController::class, 'update'])->name('schedules.update');
        Route::delete('/{id}', [EmployeeScheduleController::class, 'destroy'])->name('schedules.destroy');
    });

    Route::prefix('stationery')->group(function () {
        Route::put('/bulk-status', [StationeryController::class, 'bulkStatus'])->name('stationery.bulk-status');
        Route::delete('/bulk-destroy', [StationeryController::class, 'bulkDestroy'])->name('stationery.bulk-destroy');
        Route::delete('/truncate', [StationeryController::class, 'truncate'])->name('stationery.truncate');

        Route::post('/import', [StationeryController::class, 'importExcel'])->name('stationery.import');
        Route::post('/store', [StationeryController::class, 'store'])->name('stationery.store');
        Route::put('/{id}', [StationeryController::class, 'update'])->name('stationery.update');
        Route::delete('/{id}', [StationeryController::class, 'destroy'])->name('stationery.destroy');

        Route::post('/pesan/{id}', [StationeryController::class, 'pesanBarang'])->name('stationery.pesan');

        Route::post('/transaction', [StationeryTransactionController::class, 'store'])->name('stationery.transaction.store');
        Route::put('/transaction/{id}', [StationeryTransactionController::class, 'update'])->name('stationery.transaction.update');
        Route::delete('/transaction/{id}', [StationeryTransactionController::class, 'destroy'])->name('stationery.transaction.destroy');

        Route::get('/export-order', [LaporanStationeryController::class, 'exportOrderBarang'])->name('stationery.export.order');
        Route::get('/export-pemakaian', [LaporanStationeryController::class, 'exportPemakaianBarang'])->name('stationery.export.pemakaian');
    });

    Route::prefix('pengadaan')->group(function () {
        Route::get('/', [PengadaanController::class, 'index'])->name('pengadaan.index');
        Route::post('/store', [PengadaanController::class, 'store'])->name('pengadaan.store');
        Route::put('/status/{id}', [PengadaanController::class, 'updateStatus'])->name('pengadaan.status');
        Route::put('/{id}', [PengadaanController::class, 'update'])->name('pengadaan.update');
        Route::delete('/{id}', [PengadaanController::class, 'destroy'])->name('pengadaan.destroy');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/closing/store', [ClosingPeriodeController::class, 'store'])->name('closing.store');
    Route::put('/closing/{id}', [ClosingPeriodeController::class, 'update'])->name('closing.update');
    Route::delete('/closing/{id}', [ClosingPeriodeController::class, 'destroy'])->name('closing.destroy');

    Route::post('/spl/store', [App\Http\Controllers\SplController::class, 'store'])->name('spl.store');
    Route::get('/spl', [App\Http\Controllers\SplController::class, 'index'])->name('spl.index');
    Route::get('/spl/{spl}', [App\Http\Controllers\SplController::class, 'show'])->name('spl.show');
    Route::get('/spl/{spl}/download', [App\Http\Controllers\SplController::class, 'download'])->name('spl.download');
    Route::delete('/spl/{spl}', [App\Http\Controllers\SplController::class, 'destroy'])->name('spl.destroy');
    Route::get('/spl/export', [App\Http\Controllers\SplController::class, 'export'])->name('spl.export');
    Route::get('/spl/export/xlsx', [App\Http\Controllers\SplController::class, 'exportXlsx'])->name('spl.export.xlsx');

    Route::prefix('penilaian')->group(function () {
        Route::post('/store', [PenilaianController::class, 'store'])->name('penilaian.store');
        Route::put('/{id}', [PenilaianController::class, 'update'])->name('penilaian.update');
        Route::delete('/{id}', [PenilaianController::class, 'destroy'])->name('penilaian.destroy');
        Route::get('/export-pdf', [PenilaianController::class, 'exportPdf'])->name('penilaian.export.pdf');
        Route::get('/export-excel', [PenilaianController::class, 'exportExcel'])->name('penilaian.export.excel');
        Route::get('/laporan', [PenilaianController::class, 'laporan'])->name('laporan.penilaian.bulanan');
        Route::get('/data', [PenilaianController::class, 'getData'])->name('penilaian.data');
    });
});

require __DIR__.'/auth.php';
