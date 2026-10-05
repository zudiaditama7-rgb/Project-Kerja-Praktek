<aside id="sidebar" class="fixed left-0 top-0 h-screen w-64 bg-gradient-to-b from-[#064e3b] to-[#022c22] dark:from-[#020617] dark:to-[#042f2e] text-white transition-all duration-300 z-50 rounded-r-3xl border-r border-white/5 shadow-2xl shadow-emerald-900/20 flex flex-col justify-between">
    <!-- Top Scrollable Content -->
    <div class="p-6 overflow-y-auto flex-1 overscroll-contain">
        <div class="flex flex-col items-center mb-6">
            <div class="w-16 h-16 mb-3 rounded-full bg-white flex items-center justify-center shadow-lg shadow-black/30 border-2 border-white/10 transition-all duration-300 hover:scale-105 hover:shadow-xl hover:shadow-emerald-900/40 hover:border-emerald-400/40 cursor-pointer group">
                <img src="{{ asset('images/logo.jpg') }}" class="w-12 h-12 object-contain transition-all duration-300 group-hover:rotate-3" alt="Logo" onerror="this.parentNode.style.display='none'">
            </div>
            <h1 class="text-xl font-bold tracking-wider text-center">PRESENSI MI</h1>
        </div>

        @php $uniqueRoles = is_array(auth()->user()->roles) ? array_unique(auth()->user()->roles) : []; @endphp
        @if(count($uniqueRoles) > 1)
        <div class="mb-6 px-2">
            <form action="{{ route('switch-role') }}" method="POST">
                @csrf
                <label class="block text-[10px] font-bold text-emerald-200 uppercase tracking-wider mb-1 px-1">Peran Aktif:</label>
                <select name="role" onchange="this.form.submit()" class="w-full bg-black/20 border border-white/10 text-white text-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500 py-2 transition-all">
                    @foreach($uniqueRoles as $r)
                        <option value="{{ $r }}" {{ auth()->user()->role === $r ? 'selected' : '' }} class="text-gray-900">
                            @if($r == 'admin') Administrator
                            @elseif($r == 'wali_kelas') Wali Kelas
                            @elseif($r == 'guru_mapel') Guru Mapel
                            @elseif($r == 'guru_pengganti') Guru Pengganti
                            @elseif($r == 'kepala_sekolah') Kepala Sekolah
                            @else {{ ucfirst(str_replace('_', ' ', $r)) }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
        @endif
        
        <nav class="space-y-2">
            @if(auth()->user()->role === 'admin')
                <x-sidebar-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')" icon="layout-dashboard">
                    Dashboard
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('admin.siswa.index') }}" :active="request()->routeIs('admin.siswa.*')" icon="users">
                    Data Siswa
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('admin.riwayat.index') }}" :active="request()->routeIs('admin.riwayat.*')" icon="history">
                    Riwayat Akademik
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.*')" icon="user-cog">
                    Manajemen User
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('admin.tahun_ajaran.index') }}" :active="request()->routeIs('admin.tahun_ajaran.*')" icon="calendar">
                    Tahun Ajaran
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('admin.kelas.index') }}" :active="request()->routeIs('admin.kelas.*')" icon="school">
                    Manajemen Kelas
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('admin.monitoring') }}" :active="request()->routeIs('admin.monitoring')" icon="eye">
                    Monitoring
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('admin.laporan') }}" :active="request()->routeIs('admin.laporan')" icon="file-text">
                    Laporan
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('admin.tugas_pengganti.index') }}" :active="request()->routeIs('admin.tugas_pengganti.*')" icon="user-plus">
                    Penugasan Guru
                </x-sidebar-link>
            @elseif(auth()->user()->role === 'wali_kelas')
                <x-sidebar-link href="{{ route('wali.dashboard') }}" :active="request()->routeIs('wali.dashboard')" icon="layout-dashboard">
                    Dashboard
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('wali.siswa.index') }}" :active="request()->routeIs('wali.siswa.*')" icon="users">
                    Data Siswa
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('wali.presensi.index') }}" :active="request()->routeIs('wali.presensi.index')" icon="check-square">
                    Input Presensi
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('wali.presensi.history') }}" :active="request()->routeIs('wali.presensi.history')" icon="history">
                    Riwayat
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('wali.pengajuan.index') }}" :active="request()->routeIs('wali.pengajuan.*')" icon="send">
                    Pengajuan Rekap
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('wali.tugas_pengganti.index') }}" :active="request()->routeIs('wali.tugas_pengganti.*')" icon="user-minus">
                    Guru Pengganti
                </x-sidebar-link>
            @elseif(auth()->user()->role === 'kepala_sekolah')
                <x-sidebar-link href="{{ route('kepsek.dashboard') }}" :active="request()->routeIs('kepsek.dashboard')" icon="layout-dashboard">
                    Dashboard
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('kepsek.monitoring') }}" :active="request()->routeIs('kepsek.monitoring')" icon="eye">
                    Monitoring
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('kepsek.approval.index') }}" :active="request()->routeIs('kepsek.approval.*')" icon="check-circle">
                    Approval
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('kepsek.laporan') }}" :active="request()->routeIs('kepsek.laporan')" icon="file-text">
                    Laporan
                </x-sidebar-link>
            @elseif(auth()->user()->role === 'guru_pengganti')
                <x-sidebar-link href="{{ route('guru_pengganti.dashboard') }}" :active="request()->routeIs('guru_pengganti.dashboard')" icon="layout-dashboard">
                    Dashboard
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('guru_pengganti.siswa.index') }}" :active="request()->routeIs('guru_pengganti.siswa.*')" icon="users">
                    Data Siswa
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('guru_pengganti.presensi.index') }}" :active="request()->routeIs('guru_pengganti.presensi.index')" icon="check-square">
                    Input Presensi
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('guru_pengganti.presensi.history') }}" :active="request()->routeIs('guru_pengganti.presensi.history')" icon="history">
                    Riwayat
                </x-sidebar-link>
            @elseif(auth()->user()->role === 'guru_mapel')
                <x-sidebar-link href="{{ route('guru_mapel.dashboard') }}" :active="request()->routeIs('guru_mapel.dashboard')" icon="layout-dashboard">
                    Dashboard
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('guru_mapel.presensi.pilih_kelas') }}" :active="request()->routeIs('guru_mapel.presensi.*')" icon="check-square">
                    Input Presensi
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('guru_mapel.tugas_pengganti.index') }}" :active="request()->routeIs('guru_mapel.tugas_pengganti.*')" icon="user-minus">
                    Guru Pengganti
                </x-sidebar-link>
            @endif
        </nav>
    </div>
    
    <!-- Footer Section (Profil Saya & Logout) -->
    <div class="p-6 border-t border-white/10 bg-black/10 rounded-br-3xl space-y-2">
        <x-sidebar-link href="{{ route('profile.edit') }}" :active="request()->routeIs('profile.edit')" icon="user">
            Profil Saya
        </x-sidebar-link>
        
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center gap-3 px-4 py-2.5 w-full rounded-lg hover:bg-white/10 transition group text-red-100 hover:text-white">
                <i data-lucide="log-out" class="w-5 h-5"></i>
                <span class="font-medium">Logout</span>
            </button>
        </form>
    </div>
</aside>
