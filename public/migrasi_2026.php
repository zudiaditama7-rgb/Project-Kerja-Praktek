<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();

    // 1. Matikan Tahun Ajaran lama
    DB::table('tahun_ajarans')->update(['is_active' => false]);

    // 2. Buat atau aktifkan Tahun Ajaran 2026/2027
    $ta = DB::table('tahun_ajarans')->where('nama_tahun', '2026/2027')->first();
    if (!$ta) {
        DB::table('tahun_ajarans')->insert([
            'nama_tahun' => '2026/2027',
            'is_active' => true,
            'semester' => 'Ganjil',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    } else {
        DB::table('tahun_ajarans')->where('nama_tahun', '2026/2027')->update(['is_active' => true]);
    }

    $semesterCode = '2026/2027-Ganjil';

    // 3. Pastikan Kelas 1 - 6 untuk 2026/2027 tersedia
    for ($i = 1; $i <= 6; $i++) {
        $kelas = DB::table('kelas')
            ->where('nama_kelas', (string)$i)
            ->where('semester', $semesterCode)
            ->first();
            
        if (!$kelas) {
            // Ambil wali kelas dari tahun sebelumnya (opsional, jika ada)
            $waliLama = DB::table('kelas')
                ->where('nama_kelas', (string)$i)
                ->whereNotNull('wali_kelas_id')
                ->orderBy('id', 'desc')
                ->first();

            DB::table('kelas')->insert([
                'nama_kelas' => (string)$i,
                'semester' => $semesterCode,
                'wali_kelas_id' => $waliLama ? $waliLama->wali_kelas_id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    // 4. Proses Kenaikan Kelas untuk semua siswa aktif
    $siswas = DB::table('siswas')->where('status', 'Aktif')->get();
    $berhasil = 0;
    $lulus = 0;

    foreach ($siswas as $siswa) {
        // Cek rombel terakhir yang diikuti siswa tersebut
        $latestRombel = DB::table('rombels')
            ->join('kelas', 'rombels.kelas_id', '=', 'kelas.id')
            ->where('rombels.siswa_id', $siswa->id)
            ->orderBy('rombels.id', 'desc')
            ->select('rombels.*', 'kelas.nama_kelas')
            ->first();

        $targetKelasNama = '1'; // Default masuk kelas 1 jika belum pernah punya rombel
        
        if ($latestRombel) {
            // Jika dia sudah ada di rombel semester ini, skip (jangan didobel)
            if ($latestRombel->semester === $semesterCode) {
                continue;
            }
            // Siswa naik kelas (kelas lama + 1)
            $targetKelasNama = (string)(intval($latestRombel->nama_kelas) + 1);
        }

        // Jika kelas lebih dari 6, berarti siswa Lulus
        if (intval($targetKelasNama) > 6) {
            DB::table('siswas')->where('id', $siswa->id)->update(['status' => 'Lulus']);
            
            // Catat di riwayat siswa
            DB::table('riwayat_siswas')->insert([
                'siswa_id' => $siswa->id,
                'tanggal' => now(),
                'keterangan' => 'Lulus dari Kelas 6 ke Tahun Ajaran 2026/2027',
                'status' => 'Lulus',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $lulus++;
            continue;
        }

        // Daftarkan siswa ke kelas barunya
        $targetKelas = DB::table('kelas')
            ->where('nama_kelas', $targetKelasNama)
            ->where('semester', $semesterCode)
            ->first();
            
        if ($targetKelas) {
            DB::table('rombels')->insert([
                'siswa_id' => $siswa->id,
                'kelas_id' => $targetKelas->id,
                'semester' => $semesterCode,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Catat di riwayat
            DB::table('riwayat_siswas')->insert([
                'siswa_id' => $siswa->id,
                'tanggal' => now(),
                'keterangan' => 'Naik/Masuk ke Kelas ' . $targetKelasNama . ' Tahun Ajaran 2026/2027',
                'status' => 'Naik Kelas',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $berhasil++;
        }
    }

    DB::commit();
    echo "<div style='font-family:sans-serif; padding:20px; background:#d4edda; color:#155724; border:1px solid #c3e6cb; border-radius:5px;'>";
    echo "<h2>Proses Migrasi Tahun Ajaran Selesai!</h2>";
    echo "<p>Tahun Ajaran <b>2026/2027 (Ganjil)</b> berhasil diaktifkan.</p>";
    echo "<ul>";
    echo "<li>Siswa yang berhasil naik/masuk kelas: <b>$berhasil siswa</b></li>";
    echo "<li>Siswa Kelas 6 yang diluluskan: <b>$lulus siswa</b></li>";
    echo "</ul>";
    echo "<a href='/'>Kembali ke Dashboard</a>";
    echo "</div>";

} catch (\Exception $e) {
    DB::rollBack();
    echo "<div style='font-family:sans-serif; padding:20px; background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; border-radius:5px;'>";
    echo "<h2>Terjadi Kesalahan!</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}
