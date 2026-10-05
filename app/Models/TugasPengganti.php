<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TugasPengganti extends Model
{
    use HasFactory;

    protected $fillable = [
        'wali_kelas_id',
        'guru_mapel_id',
        'guru_pengganti_id',
        'kelas',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'alasan',
        'document_path',
        'status',
        'keterangan_ditolak',
    ];

    public function waliKelas()
    {
        return $this->belongsTo(User::class, 'wali_kelas_id');
    }

    public function guruMapel()
    {
        return $this->belongsTo(User::class, 'guru_mapel_id');
    }

    public function guruPengganti()
    {
        return $this->belongsTo(User::class, 'guru_pengganti_id');
    }
}
