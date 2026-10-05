<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';
    protected $fillable = ['nama_kelas', 'wali_kelas_id', 'semester'];

    public function waliKelas()
    {
        return $this->belongsTo(User::class, 'wali_kelas_id');
    }

    public function rombels()
    {
        return $this->hasMany(Rombel::class, 'kelas_id');
    }

    public function siswas()
    {
        return $this->belongsToMany(Siswa::class, 'rombels', 'kelas_id', 'siswa_id');
    }
}
