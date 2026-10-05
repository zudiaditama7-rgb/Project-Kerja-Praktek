<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'password',
        'role',
        'roles',
        'avatar',
        'kelas_mapel',
    ];

    public function getKelasAttribute()
    {
        $userRoles = is_array($this->roles) ? $this->roles : ($this->role ? [$this->role] : []);
        if (!in_array('wali_kelas', $userRoles)) {
            return null;
        }
        $activePeriod = TahunAjaran::getActiveSemesterPeriod();
        $periodCode = $activePeriod ? $activePeriod['period_code'] : '';
        
        $kelas = Kelas::where('wali_kelas_id', $this->id)
            ->where('semester', $periodCode)
            ->first();
            
        return $kelas ? $kelas->nama_kelas : null;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'roles' => 'array',
            'kelas_mapel' => 'array',
        ];
    }

    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : null;
    }

    public function presensis()
    {
        return $this->hasMany(Presensi::class);
    }

    public function pengajuanPresensis()
    {
        return $this->hasMany(PengajuanPresensi::class, 'user_id');
    }
}
