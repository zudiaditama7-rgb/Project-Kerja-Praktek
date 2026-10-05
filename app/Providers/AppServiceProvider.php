<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (\Illuminate\Support\Facades\Schema::hasTable('kelas')) {
            $query = \App\Models\Kelas::orderBy('nama_kelas');
            if (\Illuminate\Support\Facades\Schema::hasColumn('kelas', 'semester')) {
                $activePeriod = \App\Models\TahunAjaran::getActiveSemesterPeriod();
                $periodCode = $activePeriod ? $activePeriod['period_code'] : '';
                $query->where('semester', $periodCode);
            }
            \Illuminate\Support\Facades\View::share('globalKelasList', $query->get());
        } else {
            \Illuminate\Support\Facades\View::share('globalKelasList', collect());
        }

        // Share data ONLY for the main layout view to prevent N+1 query performance issues
        \Illuminate\Support\Facades\View::composer('layouts.app', function ($view) {
            $hasTugasTable = \Illuminate\Support\Facades\Schema::hasTable('tugas_penggantis');
            $hasPengajuanTable = \Illuminate\Support\Facades\Schema::hasTable('pengajuan_presensis');
            $isLoggedIn = \Illuminate\Support\Facades\Auth::check();
            $userRole = $isLoggedIn ? \Illuminate\Support\Facades\Auth::user()->role : null;

            // === ADMIN NOTIFICATIONS: Pending tugas pengganti ===
            if ($hasTugasTable) {
                $pendingPenggantiQuery = \App\Models\TugasPengganti::with('waliKelas')->where('status', 'pending')->latest();
                $view->with('pendingPenggantiCount', $pendingPenggantiQuery->count());
                $view->with('pendingPenggantiList', $pendingPenggantiQuery->take(5)->get());
            } else {
                $view->with('pendingPenggantiCount', 0);
                $view->with('pendingPenggantiList', collect());
            }

            // === KEPSEK NOTIFICATIONS: Pending pengajuan rekap presensi ===
            if ($isLoggedIn && $userRole === 'kepala_sekolah' && $hasPengajuanTable) {
                $pendingPengajuanQuery = \App\Models\PengajuanPresensi::with('user')->where('status', 'pending')->latest();
                $view->with('pendingPengajuanCount', $pendingPengajuanQuery->count());
                $view->with('pendingPengajuanList', $pendingPengajuanQuery->take(5)->get());
            } else {
                $view->with('pendingPengajuanCount', 0);
                $view->with('pendingPengajuanList', collect());
            }

            // === GURU PENGGANTI NOTIFICATIONS: Assigned tasks ===
            if ($isLoggedIn && $userRole === 'guru_pengganti' && $hasTugasTable) {
                $guruId = \Illuminate\Support\Facades\Auth::id();
                $assignedTugasQuery = \App\Models\TugasPengganti::with('waliKelas')
                    ->where('guru_pengganti_id', $guruId)
                    ->where('status', 'disetujui')
                    ->where('tanggal', '>=', \Carbon\Carbon::today()->toDateString())
                    ->latest();
                
                $view->with('assignedTugasCount', $assignedTugasQuery->count());
                $view->with('assignedTugasList', $assignedTugasQuery->take(5)->get());
            } else {
                $view->with('assignedTugasCount', 0);
                $view->with('assignedTugasList', collect());
            }

            // === WALI KELAS NOTIFICATIONS: Confirmed pengajuan & tugas pengganti ===
            if ($isLoggedIn && $userRole === 'wali_kelas') {
                $userId = \Illuminate\Support\Facades\Auth::id();
                $notifs = collect();

                if ($hasPengajuanTable) {
                    $pengajuans = \App\Models\PengajuanPresensi::where('user_id', $userId)
                        ->whereIn('status', ['disetujui', 'ditolak'])
                        ->where('updated_at', '>=', \Carbon\Carbon::now()->subDays(7))
                        ->latest('updated_at')
                        ->take(5)
                        ->get();
                        
                    foreach ($pengajuans as $p) {
                        $notifs->push([
                            'type' => 'pengajuan',
                            'title' => 'Rekap Presensi',
                            'status' => $p->status,
                            'date' => $p->updated_at,
                            'desc' => 'Pengajuan rekap presensi untuk ' . ucfirst($p->tipe) . ' ' . $p->bulan . ' tahun ' . $p->tahun . ' telah ' . $p->status . ' oleh Kepala Sekolah.',
                        ]);
                    }
                }

                if ($hasTugasTable) {
                    $tugas = \App\Models\TugasPengganti::where('wali_kelas_id', $userId)
                        ->whereIn('status', ['disetujui', 'ditolak'])
                        ->where('updated_at', '>=', \Carbon\Carbon::now()->subDays(7))
                        ->latest('updated_at')
                        ->take(5)
                        ->get();
                        
                    foreach ($tugas as $t) {
                        $notifs->push([
                            'type' => 'tugas',
                            'title' => 'Guru Pengganti',
                            'status' => $t->status,
                            'date' => $t->updated_at,
                            'desc' => 'Pengajuan guru pengganti untuk tanggal ' . \Carbon\Carbon::parse($t->tanggal)->format('d M Y') . ' telah ' . $t->status . ' oleh Admin.',
                        ]);
                    }
                }

                $sortedNotifs = $notifs->sortByDesc('date')->take(5)->values();
                $view->with('waliNotifCount', $sortedNotifs->count());
                $view->with('waliNotifList', $sortedNotifs);
            } else {
                $view->with('waliNotifCount', 0);
                $view->with('waliNotifList', collect());
            }
        });
    }
}
