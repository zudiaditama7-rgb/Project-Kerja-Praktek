<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tugas_penggantis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wali_kelas_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('guru_pengganti_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('kelas');
            $table->date('tanggal');
            $table->text('alasan');
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tugas_penggantis');
    }
};
