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
        if (!Schema::hasTable('kelas')) {
            Schema::create('kelas', function (Blueprint $table) {
                $table->id();
                $table->string('nama_kelas')->unique();
                $table->foreignId('wali_kelas_id')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });

            // Pre-seed classes 1 to 6 and preserve existing wali kelas
            for ($i = 1; $i <= 6; $i++) {
                $existingWali = DB::table('users')
                    ->where('role', 'wali_kelas')
                    ->where('kelas', (string)$i)
                    ->first();

                DB::table('kelas')->insert([
                    'nama_kelas' => (string)$i,
                    'wali_kelas_id' => $existingWali ? $existingWali->id : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
