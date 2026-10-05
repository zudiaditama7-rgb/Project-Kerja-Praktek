<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $tempKelas;
    protected $tempTahunAjaran;

    public function presensis()
    {
        return $this->hasMany(Presensi::class);
    }

    public function riwayatSiswa()
    {
        return $this->hasMany(RiwayatSiswa::class)->orderBy('created_at', 'desc');
    }

    public function rombels()
    {
        return $this->hasMany(Rombel::class, 'siswa_id');
    }

    public function getKelasAttribute()
    {
        $activePeriod = TahunAjaran::getActiveSemesterPeriod();
        $periodCode = $activePeriod ? $activePeriod['period_code'] : '';
        $rombel = $this->rombels()->where('semester', $periodCode)->first();
        return $rombel && $rombel->kelas ? $rombel->kelas->nama_kelas : null;
    }

    public function setKelasAttribute($value)
    {
        $this->tempKelas = $value;
    }

    public function getTahunAjaranAttribute()
    {
        $activePeriod = TahunAjaran::getActiveSemesterPeriod();
        $periodCode = $activePeriod ? $activePeriod['period_code'] : '';
        $rombel = $this->rombels()->where('semester', $periodCode)->first();
        if ($rombel) {
            $parts = explode('-', $rombel->semester);
            return $parts[0] ?? '';
        }
        return $activePeriod ? $activePeriod['tahun_ajaran'] : null;
    }

    public function setTahunAjaranAttribute($value)
    {
        $this->tempTahunAjaran = $value;
    }

    protected static function booted()
    {
        static::saved(function ($siswa) {
            $activePeriod = TahunAjaran::getActiveSemesterPeriod();
            
            if ($siswa->tempKelas || $siswa->tempTahunAjaran) {
                $targetKelas = $siswa->tempKelas ?? $siswa->kelas;
                $targetTahun = $siswa->tempTahunAjaran ?? $siswa->tahun_ajaran;
                
                $semesterName = $activePeriod ? $activePeriod['semester'] : 'Ganjil';
                $siswaPeriodCode = $targetTahun . '-' . $semesterName;
                
                if ($targetKelas) {
                    $kelas = Kelas::firstOrCreate([
                        'nama_kelas' => $targetKelas,
                        'semester' => $siswaPeriodCode
                    ]);
                    
                    Rombel::updateOrCreate([
                        'siswa_id' => $siswa->id,
                        'semester' => $siswaPeriodCode
                    ], [
                        'kelas_id' => $kelas->id
                    ]);
                }
            }
        });

        static::created(function ($siswa) {
            \App\Models\RiwayatSiswa::create([
                'siswa_id' => $siswa->id,
                'tahun_ajaran' => $siswa->tahun_ajaran ?? '',
                'kelas_asal' => null,
                'kelas_tujuan' => $siswa->kelas ?? '',
                'status' => 'Baru',
            ]);
        });
    }
}
