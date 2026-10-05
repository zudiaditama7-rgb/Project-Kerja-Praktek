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
        Schema::table('tahun_ajarans', function (Blueprint $table) {
            $table->date('tanggal_mulai_gasal')->nullable();
            $table->date('tanggal_selesai_gasal')->nullable();
            $table->date('tanggal_mulai_genap')->nullable();
            $table->date('tanggal_selesai_genap')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tahun_ajarans', function (Blueprint $table) {
            $table->dropColumn([
                'tanggal_mulai_gasal',
                'tanggal_selesai_gasal',
                'tanggal_mulai_genap',
                'tanggal_selesai_genap'
            ]);
        });
    }
};
