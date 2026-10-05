<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleSwitchController extends Controller
{
    /**
     * Tampilkan halaman pilih peran setelah login
     */
    public function showPilihPeran()
    {
        $user = Auth::user();
        $roles = $user->roles ?? [];
        
        // Jika hanya 1 peran, arahkan ke halaman utama
        if (count($roles) <= 1) {
            return redirect('/');
        }
        
        return view('auth.pilih-peran', compact('roles'));
    }

    /**
     * Proses pemilihan / pergantian peran
     */
    public function switchRole(Request $request)
    {
        $request->validate([
            'role' => 'required|string'
        ]);

        $user = Auth::user();
        $roles = $user->roles ?? [];

        // Pastikan role yang dipilih ada di dalam daftar roles yang dimiliki user
        if (in_array($request->role, $roles)) {
            $user->role = $request->role;
            $user->save();

            // Redirect ke dashboard sesuai role
            return match($request->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'wali_kelas' => redirect()->route('wali.dashboard'),
                'kepala_sekolah' => redirect()->route('kepsek.dashboard'),
                'guru_pengganti' => redirect()->route('guru_pengganti.dashboard'),
                'guru_mapel' => redirect()->route('guru_mapel.dashboard'),
                default => redirect('/'),
            };
        }

        return back()->withErrors(['role' => 'Anda tidak memiliki akses ke peran tersebut.']);
    }
}
