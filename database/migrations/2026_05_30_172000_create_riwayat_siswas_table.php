<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('riwayat_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->string('tahun_ajaran');
            $table->string('kelas_asal')->nullable();
            $table->string('kelas_tujuan');
            $table->string('status'); // 'Baru', 'Naik Kelas', 'Tidak Naik Kelas', 'Lulus', 'Penyesuaian Manual'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_siswas');
    }
};
