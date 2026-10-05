<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah kolom semester ke tabel kelas (sementara nullable)
        Schema::table('kelas', function (Blueprint $table) {
            $table->string('semester')->nullable()->after('wali_kelas_id');
        });

        // 2. Dapatkan Periode Aktif saat ini untuk seeding data migrasi
        $activeTA = DB::table('tahun_ajarans')->where('is_active', true)->first();
        $namaTahun = $activeTA ? $activeTA->nama_tahun : '2025/2026';
        
        // Tentukan semester default berdasarkan tanggal sekarang
        $month = now()->month;
        $semesterDefault = ($month >= 7 && $month <= 12) ? 'Ganjil' : 'Genap';
        $activePeriodCode = $namaTahun . '-' . $semesterDefault;

        // Set semester untuk kelas yang sudah ada
        DB::table('kelas')->update(['semester' => $activePeriodCode]);

        // 3. Ubah index unik pada tabel kelas
        Schema::table('kelas', function (Blueprint $table) {
            // Drop unique index lama
            $table->dropUnique('kelas_nama_kelas_unique');
            // Buat unique index komposit baru
            $table->unique(['nama_kelas', 'semester']);
        });

        // 4. Buat tabel rombels
        Schema::create('rombels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->string('semester');
            $table->timestamps();
        });

        // 5. Migrasi data siswa yang sudah ada ke tabel rombels
        $siswas = DB::table('siswas')->get();
        foreach ($siswas as $siswa) {
            if (!empty($siswa->kelas)) {
                // Tentukan semester untuk siswa ini berdasarkan tahun_ajaran miliknya
                $siswaTahun = $siswa->tahun_ajaran ?? $namaTahun;
                // Jika tahun ajaran siswa sama dengan tahun aktif, gunakan semester aktif, jika tidak default Ganjil
                $siswaSemester = ($siswaTahun === $namaTahun) ? $semesterDefault : 'Ganjil';
                $siswaPeriodCode = $siswaTahun . '-' . $siswaSemester;

                // Cari kelas yang cocok di tabel kelas untuk semester siswa tersebut
                $kelas = DB::table('kelas')
                    ->where('nama_kelas', $siswa->kelas)
                    ->where('semester', $siswaPeriodCode)
                    ->first();

                // Jika kelas belum ada untuk semester tersebut, buat kelas baru
                if (!$kelas) {
                    // Ambil wali kelas dari kelas yang sama di periode lain (jika ada) sebagai fallback
                    $existingKelas = DB::table('kelas')->where('nama_kelas', $siswa->kelas)->first();
                    $waliKelasId = $existingKelas ? $existingKelas->wali_kelas_id : null;

                    $kelasId = DB::table('kelas')->insertGetId([
                        'nama_kelas' => $siswa->kelas,
                        'wali_kelas_id' => $waliKelasId,
                        'semester' => $siswaPeriodCode,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $kelasId = $kelas->id;
                }

                // Masukkan siswa ke rombel
                DB::table('rombels')->insert([
                    'siswa_id' => $siswa->id,
                    'kelas_id' => $kelasId,
                    'semester' => $siswaPeriodCode,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 6. Migrasi wali_kelas_id dari tabel users ke tabel kelas (jika ada wali kelas yang belum tersinkronisasi)
        $usersWali = DB::table('users')->where('role', 'wali_kelas')->whereNotNull('kelas')->get();
        foreach ($usersWali as $user) {
            DB::table('kelas')
                ->where('nama_kelas', $user->kelas)
                ->where('semester', $activePeriodCode)
                ->update(['wali_kelas_id' => $user->id]);
        }

        // 7. Normalisasi: Hapus kolom kelas dan tahun_ajaran dari siswas, serta kelas dari users
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn(['kelas', 'tahun_ajaran']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('kelas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Untuk rollback, kembalikan kolom kelas ke users dan siswas
        Schema::table('users', function (Blueprint $table) {
            $table->string('kelas')->nullable();
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->string('kelas')->nullable();
            $table->string('tahun_ajaran')->nullable();
        });

        // Kembalikan data dari rombels ke tabel siswas dan users
        $rombels = DB::table('rombels')->get();
        foreach ($rombels as $rombel) {
            $kelas = DB::table('kelas')->find($rombel->kelas_id);
            if ($kelas) {
                // Update siswa
                // Potong semester code (misal 2025/2026-Ganjil menjadi 2025/2026)
                $parts = explode('-', $rombel->semester);
                $tahunAjaran = $parts[0] ?? '';

                DB::table('siswas')->where('id', $rombel->siswa_id)->update([
                    'kelas' => $kelas->nama_kelas,
                    'tahun_ajaran' => $tahunAjaran,
                ]);

                // Update wali kelas di users jika ada
                if ($kelas->wali_kelas_id) {
                    DB::table('users')->where('id', $kelas->wali_kelas_id)->update([
                        'kelas' => $kelas->nama_kelas,
                    ]);
                }
            }
        }

        Schema::dropIfExists('rombels');

        Schema::table('kelas', function (Blueprint $table) {
            $table->dropUnique('kelas_nama_kelas_semester_unique');
            $table->dropColumn('semester');
            $table->unique('nama_kelas');
        });
    }
};
