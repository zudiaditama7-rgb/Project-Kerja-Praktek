<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Kepala Sekolah
        User::create([
            'name' => 'MUNIF S.Pd.I',
            'username' => 'kepsek',
            'password' => Hash::make('123456'),
            'role' => 'kepala_sekolah',
        ]);

        // Wali Kelas
        $waliKelas = [
            ['username' => 'wali1', 'name' => 'TOYIMAH', 'kelas' => '1'],
            ['username' => 'wali2', 'name' => 'WAHYU VIDIYATIE', 'kelas' => '2'],
            ['username' => 'wali3', 'name' => 'SAPTINAR MENTARI NP', 'kelas' => '3'],
            ['username' => 'wali4', 'name' => 'IBNU HASIM', 'kelas' => '4'],
            ['username' => 'wali5', 'name' => 'INDAH', 'kelas' => '5'],
            ['username' => 'wali6', 'name' => 'KHOTIMAH', 'kelas' => '6'],
        ];

        foreach ($waliKelas as $wali) {
            User::create([
                'name' => $wali['name'],
                'username' => $wali['username'],
                'password' => Hash::make('123456'),
                'role' => 'wali_kelas',
                'kelas' => $wali['kelas'],
            ]);
        }

        // Tahun Ajaran Aktif
        TahunAjaran::create([
            'nama_tahun' => '2023/2024 Genap',
            'is_active' => true,
        ]);
    }
}
