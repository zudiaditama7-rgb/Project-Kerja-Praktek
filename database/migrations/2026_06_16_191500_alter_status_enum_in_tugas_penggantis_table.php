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
        // Alter enum using raw DB statement because Schema builder has issues with modifying enums in Doctrine DBAL
        DB::statement("ALTER TABLE tugas_penggantis MODIFY COLUMN status ENUM('pending', 'disetujui', 'ditolak', 'selesai') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting back
        DB::statement("ALTER TABLE tugas_penggantis MODIFY COLUMN status ENUM('pending', 'disetujui', 'ditolak') DEFAULT 'pending'");
    }
};
