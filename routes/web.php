<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\WaliKelasController;
use App\Http\Controllers\GuruPenggantiController;
use App\Http\Controllers\KepalaSekolahController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\RoleSwitchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        return match($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'wali_kelas' => redirect()->route('wali.dashboard'),
            'kepala_sekolah' => redirect()->route('kepsek.dashboard'),
            'guru_pengganti' => redirect()->route('guru_pengganti.dashboard'),
            'guru_mapel' => redirect()->route('guru_mapel.dashboard'),
            default => redirect()->route('login'),
        };
    }
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    // Role Switching Routes
    Route::get('/pilih-peran', [RoleSwitchController::class, 'showPilihPeran'])->name('pilih-peran');
    Route::post('/switch-role', [RoleSwitchController::class, 'switchRole'])->name('switch-role');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        Route::get('/siswa', [AdminController::class, 'siswaIndex'])->name('siswa.index');
        Route::get('/siswa/create', [AdminController::class, 'siswaCreate'])->name('siswa.create');
        Route::post('/siswa', [AdminController::class, 'siswaStore'])->name('siswa.store');
        Route::get('/siswa/{siswa}/edit', [AdminController::class, 'siswaEdit'])->name('siswa.edit');
        Route::put('/siswa/{siswa}', [AdminController::class, 'siswaUpdate'])->name('siswa.update');
        Route::delete('/siswa/{siswa}', [AdminController::class, 'siswaDestroy'])->name('siswa.destroy');
        Route::post('/siswa/import', [AdminController::class, 'siswaImport'])->name('siswa.import');
        Route::get('/siswa/export/excel', [AdminController::class, 'siswaExportExcel'])->name('siswa.export.excel');
        Route::get('/siswa/export/pdf', [AdminController::class, 'siswaExportPdf'])->name('siswa.export.pdf');
        Route::get('/siswa/{siswa}/card', [AdminController::class, 'siswaCard'])->name('siswa.card');


        Route::get('/users', [AdminController::class, 'userIndex'])->name('users.index');
        Route::post('/users', [AdminController::class, 'userStore'])->name('users.store');
        Route::get('/users/{user}/edit', [AdminController::class, 'userEdit'])->name('users.edit');
        Route::put('/users/{user}', [AdminController::class, 'userUpdate'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'userDestroy'])->name('users.destroy');

        Route::get('/tahun-ajaran', [AdminController::class, 'tahunAjaranIndex'])->name('tahun_ajaran.index');
        Route::post('/tahun-ajaran', [AdminController::class, 'tahunAjaranStore'])->name('tahun_ajaran.store');
        Route::post('/tahun-ajaran/{id}/activate', [AdminController::class, 'tahunAjaranActivate'])->name('tahun_ajaran.activate');
        Route::put('/tahun-ajaran/{id}', [AdminController::class, 'tahunAjaranUpdate'])->name('tahun_ajaran.update');
        
        Route::get('/kelas', [AdminController::class, 'kelasIndex'])->name('kelas.index');
        Route::post('/kelas', [AdminController::class, 'kelasStore'])->name('kelas.store');
        Route::put('/kelas/{id}', [AdminController::class, 'kelasUpdate'])->name('kelas.update');
        Route::delete('/kelas/{id}', [AdminController::class, 'kelasDestroy'])->name('kelas.destroy');
        
        Route::get('/siswa/get-by-kelas/{kelas}', [AdminController::class, 'getSiswaByKelas'])->name('siswa.get_by_kelas');
        Route::post('/kenaikan-kelas', [AdminController::class, 'kenaikanKelas'])->name('kenaikan_kelas');
        Route::get('/riwayat-akademik', [AdminController::class, 'riwayatIndex'])->name('riwayat.index');
        
        Route::get('/monitoring', [AdminController::class, 'monitoring'])->name('monitoring');
        Route::get('/laporan', [AdminController::class, 'laporan'])->name('laporan');
        
        Route::get('/tugas-pengganti', [AdminController::class, 'tugasPenggantiIndex'])->name('tugas_pengganti.index');
        Route::post('/tugas-pengganti/{id}/assign', [AdminController::class, 'tugasPenggantiAssign'])->name('tugas_pengganti.assign');
        Route::post('/tugas-pengganti/{id}/reject', [AdminController::class, 'tugasPenggantiReject'])->name('tugas_pengganti.reject');
    });

    // Wali Kelas Routes
    Route::middleware('role:wali_kelas')->prefix('wali')->name('wali.')->group(function () {
        Route::get('/dashboard', [WaliKelasController::class, 'dashboard'])->name('dashboard');
        Route::get('/presensi', [WaliKelasController::class, 'presensiIndex'])->name('presensi.index');
        Route::post('/presensi', [WaliKelasController::class, 'presensiStore'])->name('presensi.store');
        Route::get('/presensi/history', [WaliKelasController::class, 'presensiHistory'])->name('presensi.history');
        Route::get('/presensi/scan', [WaliKelasController::class, 'presensiScan'])->name('presensi.scan');
        Route::post('/presensi/scan', [ScanController::class, 'processScan'])->name('presensi.scan_submit');
        Route::get('/pengajuan', [WaliKelasController::class, 'pengajuanIndex'])->name('pengajuan.index');
        Route::post('/pengajuan', [WaliKelasController::class, 'pengajuanStore'])->name('pengajuan.store');
        Route::get('/pengajuan/{id}/pdf', [WaliKelasController::class, 'downloadPdf'])->name('pengajuan.pdf');
        Route::get('/siswa', [WaliKelasController::class, 'siswaIndex'])->name('siswa.index');
        Route::get('/siswa/{siswa}/card', [WaliKelasController::class, 'siswaCard'])->name('siswa.card');
        
        Route::get('/tugas-pengganti', [WaliKelasController::class, 'tugasPenggantiIndex'])->name('tugas_pengganti.index');
        Route::post('/tugas-pengganti', [WaliKelasController::class, 'tugasPenggantiStore'])->name('tugas_pengganti.store');
    });

    // Kepala Sekolah Routes
    Route::middleware('role:kepala_sekolah')->prefix('kepsek')->name('kepsek.')->group(function () {
        Route::get('/dashboard', [KepalaSekolahController::class, 'dashboard'])->name('dashboard');
        Route::get('/monitoring', [KepalaSekolahController::class, 'monitoring'])->name('monitoring');
        Route::get('/approval', [KepalaSekolahController::class, 'approvalIndex'])->name('approval.index');
        Route::post('/approval/{id}', [KepalaSekolahController::class, 'approvalProcess'])->name('approval.process');
        Route::get('/laporan', [KepalaSekolahController::class, 'laporan'])->name('laporan');
        Route::get('/laporan/export', [KepalaSekolahController::class, 'laporanExportExcel'])->name('laporan.export');
    });

    // Guru Pengganti Routes
    Route::middleware('role:guru_pengganti')->prefix('guru-pengganti')->name('guru_pengganti.')->group(function () {
        Route::get('/dashboard', [GuruPenggantiController::class, 'dashboard'])->name('dashboard');
        Route::get('/presensi', [GuruPenggantiController::class, 'presensiIndex'])->name('presensi.index');
        Route::post('/presensi', [GuruPenggantiController::class, 'presensiStore'])->name('presensi.store');
        Route::get('/presensi/history', [GuruPenggantiController::class, 'presensiHistory'])->name('presensi.history');
        Route::get('/presensi/scan', [GuruPenggantiController::class, 'presensiScan'])->name('presensi.scan');
        Route::post('/presensi/scan', [ScanController::class, 'processScan'])->name('presensi.scan_submit');
        Route::get('/siswa', [GuruPenggantiController::class, 'siswaIndex'])->name('siswa.index');
        Route::get('/siswa/{siswa}/card', [GuruPenggantiController::class, 'siswaCard'])->name('siswa.card');
    });

    // Guru Mapel Routes
    Route::middleware('role:guru_mapel')->prefix('guru-mapel')->name('guru_mapel.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\GuruMapelController::class, 'dashboard'])->name('dashboard');
        Route::get('/presensi', [\App\Http\Controllers\GuruMapelController::class, 'pilihKelas'])->name('presensi.pilih_kelas');
        Route::get('/presensi/{kelas_id}', [\App\Http\Controllers\GuruMapelController::class, 'presensiIndex'])->name('presensi.index');
        Route::post('/presensi/{kelas_id}', [\App\Http\Controllers\GuruMapelController::class, 'presensiStore'])->name('presensi.store');
        Route::get('/presensi/{kelas_id}/scan', [\App\Http\Controllers\GuruMapelController::class, 'presensiScan'])->name('presensi.scan');
        Route::post('/presensi/{kelas_id}/scan', [\App\Http\Controllers\ScanController::class, 'processScanMapel'])->name('presensi.scan_submit');
        
        Route::get('/tugas-pengganti', [\App\Http\Controllers\GuruMapelController::class, 'tugasPenggantiIndex'])->name('tugas_pengganti.index');
        Route::post('/tugas-pengganti', [\App\Http\Controllers\GuruMapelController::class, 'tugasPenggantiStore'])->name('tugas_pengganti.store');
    });

    Route::get('/siswa/{id}/riwayat', [AdminController::class, 'getRiwayat'])->name('siswa.riwayat');
});

require __DIR__.'/auth.php';

// Fix for Windows php artisan serve 403 Forbidden error on storage symlinks
Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) {
        abort(404);
    }
    return response()->file($fullPath);
})->where('path', '.*');

// Route to view document with back button
Route::get('/document/{id}', function ($id) {
    $presensi = \App\Models\Presensi::with('siswa')->findOrFail($id);
    if (!$presensi->document_path) {
        abort(404);
    }
    return view('shared.document.view', compact('presensi'));
})->name('document.view')->middleware('auth');

// Route to view tugas pengganti document with back button
Route::get('/tugas-pengganti/{id}/dokumen', function ($id) {
    $tugas = \App\Models\TugasPengganti::with(['waliKelas', 'guruMapel'])->findOrFail($id);
    if (!$tugas->document_path) {
        abort(404);
    }
    return view('shared.document.tugas_pengganti', compact('tugas'));
})->name('tugas_pengganti.dokumen')->middleware('auth');

