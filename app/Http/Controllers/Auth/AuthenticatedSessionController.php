<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();
        
        // If user has no roles array but has a single role, migrate them on the fly
        $roles = $user->roles ?? [];
        if (empty($roles) && $user->role) {
            $roles = [$user->role];
            $user->roles = $roles;
            $user->save();
        }

        // If user has multiple roles but no active role set, set the first one
        if (count($roles) > 0 && !in_array($user->role, $roles)) {
            $user->role = $roles[0];
            $user->save();
        }

        // If user has only 1 role, ensure it's set as active and redirect
        if (count($roles) === 1) {
            if ($user->role !== $roles[0]) {
                $user->role = $roles[0];
                $user->save();
            }
        }

        $role = $user->role;
        if ($role === 'admin') {
            return redirect()->intended('/admin/dashboard');
        } elseif ($role === 'wali_kelas') {
            return redirect()->intended('/wali/dashboard');
        } elseif ($role === 'kepala_sekolah') {
            return redirect()->intended('/kepsek/dashboard');
        } elseif ($role === 'guru_pengganti') {
            return redirect()->intended('/guru-pengganti/dashboard');
        } elseif ($role === 'guru_mapel') {
            return redirect()->intended('/guru-mapel/dashboard');
        }

        return redirect()->intended('/');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
