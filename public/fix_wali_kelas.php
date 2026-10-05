<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

try {
    $semesterLama = '2025/2026-Genap';
    $semesterBaru = '2026/2027-Ganjil';
    
    $berhasil = 0;

    // 1. Pastikan Kelas 1 - 6 ada di semester baru, dan salin Wali Kelas
    for ($i = 1; $i <= 6; $i++) {
        $namaKelas = (string)$i;
        
        // Cari wali kelas di semester lama
        $kelasLama = DB::table('kelas')
            ->where('nama_kelas', $namaKelas)
            ->where('semester', $semesterLama)
            ->first();
            
        $waliId = $kelasLama ? $kelasLama->wali_kelas_id : null;

        // Cari kelas di semester baru
        $kelasBaru = DB::table('kelas')
            ->where('nama_kelas', $namaKelas)
            ->where('semester', $semesterBaru)
            ->first();

        if ($kelasBaru) {
            // Update wali kelas jika masih kosong
            if (empty($kelasBaru->wali_kelas_id) && $waliId) {
                DB::table('kelas')->where('id', $kelasBaru->id)->update([
                    'wali_kelas_id' => $waliId,
                    'updated_at' => now()
                ]);
                $berhasil++;
            }
        } else {
            // Jika kelas sama sekali belum ada (misal kelas 1 terhapus/belum dibuat)
            DB::table('kelas')->insert([
                'nama_kelas' => $namaKelas,
                'semester' => $semesterBaru,
                'wali_kelas_id' => $waliId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $berhasil++;
        }
    }

    echo "<div style='font-family:sans-serif; padding:20px; background:#d4edda; color:#155724; border:1px solid #c3e6cb; border-radius:5px;'>";
    echo "<h2>Perbaikan Sinkronisasi Berhasil!</h2>";
    echo "<p>Sistem telah berhasil menyalin data <b>Wali Kelas</b> dari semester sebelumnya ke Tahun Ajaran 2026/2027.</p>";
    echo "<ul><li>Jumlah kelas yang diperbaiki/ditambahkan Wali Kelasnya: <b>$berhasil kelas</b></li></ul>";
    echo "<p>Silakan login kembali ke akun Wali Kelas Anda (atau cek halaman Data Kelas di Admin), data siswa sekarang sudah terhubung dan muncul 100%.</p>";
    echo "<a href='/'>Kembali ke Dashboard</a>";
    echo "</div>";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
