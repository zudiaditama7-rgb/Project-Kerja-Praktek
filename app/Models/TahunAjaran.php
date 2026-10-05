<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'tanggal_mulai_gasal' => 'date',
        'tanggal_selesai_gasal' => 'date',
        'tanggal_mulai_genap' => 'date',
        'tanggal_selesai_genap' => 'date',
    ];

    public static function getActiveSemesterPeriod()
    {
        $activeTA = self::where('is_active', true)->first();
        if (!$activeTA) {
            $year = date('Y');
            $nextYear = $year + 1;
            return [
                'tahun_ajaran' => "{$year}/{$nextYear}",
                'semester' => 'Ganjil',
                'period_code' => "{$year}/{$nextYear}-Ganjil"
            ];
        }

        $today = now()->toDateString();

        if ($activeTA->tanggal_mulai_gasal && $activeTA->tanggal_selesai_gasal) {
            $start = $activeTA->tanggal_mulai_gasal->toDateString();
            $end = $activeTA->tanggal_selesai_gasal->toDateString();
            if ($today >= $start && $today <= $end) {
                return [
                    'tahun_ajaran' => $activeTA->nama_tahun,
                    'semester' => 'Ganjil',
                    'period_code' => $activeTA->nama_tahun . '-Ganjil'
                ];
            }
        }

        if ($activeTA->tanggal_mulai_genap && $activeTA->tanggal_selesai_genap) {
            $start = $activeTA->tanggal_mulai_genap->toDateString();
            $end = $activeTA->tanggal_selesai_genap->toDateString();
            if ($today >= $start && $today <= $end) {
                return [
                    'tahun_ajaran' => $activeTA->nama_tahun,
                    'semester' => 'Genap',
                    'period_code' => $activeTA->nama_tahun . '-Genap'
                ];
            }
        }

        // Fallback berdasarkan bulan
        $month = now()->month;
        $semester = ($month >= 7 && $month <= 12) ? 'Ganjil' : 'Genap';
        return [
            'tahun_ajaran' => $activeTA->nama_tahun,
            'semester' => $semester,
            'period_code' => $activeTA->nama_tahun . '-' . $semester
        ];
    }
}
