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
        Schema::table('tugas_penggantis', function (Blueprint $table) {
            $table->foreignId('guru_mapel_id')->nullable()->after('wali_kelas_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('wali_kelas_id')->nullable()->change();
            $table->time('waktu_mulai')->nullable()->after('tanggal');
            $table->time('waktu_selesai')->nullable()->after('waktu_mulai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tugas_penggantis', function (Blueprint $table) {
            $table->dropForeign(['guru_mapel_id']);
            $table->dropColumn(['guru_mapel_id', 'waktu_mulai', 'waktu_selesai']);
            $table->unsignedBigInteger('wali_kelas_id')->nullable(false)->change();
        });
    }
};
