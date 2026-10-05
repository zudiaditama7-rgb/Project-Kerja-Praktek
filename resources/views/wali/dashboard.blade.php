<x-app-layout>
    <x-slot name="header">Dashboard Wali Kelas</x-slot>

    <!-- Header Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        @php
            $hour = \Carbon\Carbon::now('Asia/Jakarta')->hour;
            if ($hour >= 4 && $hour < 11) {
                $greeting = 'Selamat Pagi';
                $theme = 'morning';
                $gradient = 'bg-gradient-to-br from-emerald-100 via-emerald-50/80 to-amber-100/80 dark:from-emerald-900/60 dark:via-[#0f172a] dark:to-amber-900/40';
                $textMain = 'text-emerald-950 dark:text-emerald-50';
                $textSub = 'text-emerald-800 dark:text-emerald-200';
                $iconBg = 'bg-emerald-200/50 dark:bg-emerald-800/60 text-emerald-700 dark:text-emerald-300';
            } elseif ($hour >= 11 && $hour < 15) {
                $greeting = 'Selamat Siang';
                $theme = 'afternoon';
                $gradient = 'bg-gradient-to-br from-emerald-100 via-emerald-50/50 to-sky-100/80 dark:from-emerald-900/60 dark:via-[#0f172a] dark:to-sky-900/40';
                $textMain = 'text-emerald-950 dark:text-emerald-50';
                $textSub = 'text-emerald-800 dark:text-emerald-200';
                $iconBg = 'bg-emerald-200/50 dark:bg-emerald-800/60 text-emerald-700 dark:text-emerald-300';
            } elseif ($hour >= 15 && $hour < 18) {
                $greeting = 'Selamat Sore';
                $theme = 'evening';
                $gradient = 'bg-gradient-to-br from-emerald-200/60 via-emerald-100/50 to-orange-100/80 dark:from-emerald-900/60 dark:via-[#0f172a] dark:to-orange-900/40';
                $textMain = 'text-emerald-950 dark:text-emerald-50';
                $textSub = 'text-emerald-800 dark:text-emerald-200';
                $iconBg = 'bg-orange-200/50 dark:bg-orange-900/60 text-orange-700 dark:text-orange-300';
            } else {
                $greeting = 'Selamat Malam';
                $theme = 'night';
                $gradient = 'bg-gradient-to-br from-emerald-900 via-[#0f172a] to-indigo-900 dark:from-emerald-950 dark:via-[#0a0f1d] dark:to-indigo-950';
                $textMain = 'text-emerald-50';
                $textSub = 'text-emerald-200';
                $iconBg = 'bg-indigo-800/60 text-indigo-200';
            }
        @endphp

        <!-- Premium Welcome Card -->
        <div class="lg:col-span-2 {{ $gradient }} p-8 md:p-10 rounded-[24px] premium-card shadow-lg border border-gray-50 dark:border-white/5 relative overflow-hidden group">
            
            <!-- Dynamic Background Illustration -->
            <div class="absolute right-0 bottom-0 top-0 w-1/2 md:w-1/3 opacity-30 dark:opacity-20 pointer-events-none transition-opacity duration-700 group-hover:opacity-40 dark:group-hover:opacity-30 flex items-end justify-end">
                <svg viewBox="0 0 200 150" class="w-full h-full object-contain object-right-bottom transform translate-y-2 translate-x-4">
                    <defs>
                        <linearGradient id="treeGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="currentColor" class="text-emerald-400" />
                            <stop offset="100%" stop-color="currentColor" class="text-emerald-600" />
                        </linearGradient>
                        <linearGradient id="buildingGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="currentColor" class="text-emerald-500" />
                            <stop offset="100%" stop-color="currentColor" class="text-emerald-700" />
                        </linearGradient>
                    </defs>

                    <!-- Sky Elements based on theme -->
                    @if($theme === 'morning')
                        <circle cx="160" cy="40" r="24" fill="#fef08a" opacity="0.6" />
                        <circle cx="160" cy="40" r="16" fill="#fde047" opacity="0.9" />
                    @elseif($theme === 'afternoon')
                        <circle cx="150" cy="25" r="20" fill="#bae6fd" opacity="0.6" />
                        <circle cx="150" cy="25" r="14" fill="#38bdf8" opacity="0.9" />
                    @elseif($theme === 'evening')
                        <circle cx="130" cy="65" r="28" fill="#fed7aa" opacity="0.6" />
                        <circle cx="130" cy="65" r="20" fill="#fb923c" opacity="0.9" />
                    @elseif($theme === 'night')
                        <path d="M 155 20 A 15 15 0 1 0 170 35 A 18 18 0 0 1 155 20 Z" fill="#e2e8f0" opacity="0.9" />
                        <circle cx="110" cy="30" r="1.5" fill="#fff" opacity="0.8" />
                        <circle cx="180" cy="50" r="2" fill="#fff" opacity="0.6" />
                        <circle cx="130" cy="20" r="1.5" fill="#fff" opacity="0.7" />
                        <circle cx="170" cy="25" r="1" fill="#fff" opacity="0.9" />
                    @endif

                    <!-- Clouds -->
                    <path d="M 40 50 A 10 10 0 0 1 60 50 A 15 15 0 0 1 85 55 A 10 10 0 0 1 85 70 L 40 70 A 10 10 0 0 1 40 50 Z" fill="currentColor" class="text-white dark:text-slate-700" opacity="0.8"/>
                    <path d="M 130 40 A 8 8 0 0 1 146 40 A 12 12 0 0 1 166 45 A 8 8 0 0 1 166 57 L 130 57 A 8 8 0 0 1 130 40 Z" fill="currentColor" class="text-white dark:text-slate-700" opacity="0.6"/>
                    
                    <!-- School Building -->
                    <rect x="70" y="80" width="70" height="60" rx="4" fill="url(#buildingGrad)" opacity="0.9"/>
                    <path d="M 60 80 L 150 80 L 105 55 Z" fill="currentColor" class="text-emerald-700 dark:text-emerald-800" opacity="0.95"/>
                    <path d="M 95 140 L 95 115 A 10 10 0 0 1 115 115 L 115 140 Z" fill="currentColor" class="text-emerald-900 dark:text-emerald-950" opacity="0.9"/>
                    <rect x="80" y="95" width="10" height="12" rx="2" fill="currentColor" class="text-white dark:text-emerald-100" opacity="0.9"/>
                    <rect x="120" y="95" width="10" height="12" rx="2" fill="currentColor" class="text-white dark:text-emerald-100" opacity="0.9"/>
                    
                    <!-- Trees -->
                    <path d="M 45 140 L 45 115 L 50 115 L 50 140 Z" fill="currentColor" class="text-emerald-900 dark:text-emerald-950" opacity="0.9"/>
                    <circle cx="47" cy="105" r="16" fill="url(#treeGrad)" opacity="0.95"/>
                    <circle cx="40" cy="115" r="12" fill="url(#treeGrad)" opacity="0.95"/>
                    <circle cx="55" cy="115" r="12" fill="url(#treeGrad)" opacity="0.95"/>
                    
                    <path d="M 160 140 L 160 120 L 164 120 L 164 140 Z" fill="currentColor" class="text-emerald-900 dark:text-emerald-950" opacity="0.9"/>
                    <circle cx="162" cy="112" r="14" fill="url(#treeGrad)" opacity="0.95"/>
                    <circle cx="154" cy="122" r="10" fill="url(#treeGrad)" opacity="0.95"/>
                    <circle cx="170" cy="122" r="10" fill="url(#treeGrad)" opacity="0.95"/>
                </svg>
            </div>

            <!-- Content -->
            <div class="relative z-10 w-full md:w-3/4 lg:w-2/3">
                <h3 class="text-2xl md:text-[28px] font-extrabold {{ $textMain }} mb-2 tracking-tight">
                    {{ $greeting }}, {{ ucwords(strtolower(auth()->user()->name)) }}!
                </h3>
                <p class="text-sm md:text-base {{ $textSub }} font-medium mb-8 leading-relaxed">
                    Semoga aktivitas belajar hari ini berjalan lancar.
                </p>
                
                <div class="flex flex-wrap items-center gap-3 md:gap-4 text-xs md:text-sm font-semibold {{ $textSub }}">
                    <div class="flex items-center gap-2.5 px-3 md:px-4 py-2 rounded-xl bg-white/40 dark:bg-black/20 backdrop-blur-sm border border-white/30 dark:border-white/5 shadow-sm">
                        <div class="w-6 h-6 md:w-7 md:h-7 rounded-lg flex items-center justify-center {{ $iconBg }}">
                            <i data-lucide="calendar" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
                        </div>
                        <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                    
                    <div class="flex items-center gap-2.5 px-3 md:px-4 py-2 rounded-xl bg-white/40 dark:bg-black/20 backdrop-blur-sm border border-white/30 dark:border-white/5 shadow-sm">
                        <div class="w-6 h-6 md:w-7 md:h-7 rounded-lg flex items-center justify-center {{ $iconBg }}">
                            <i data-lucide="bookmark" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
                        </div>
                        <span>Kelas {{ auth()->user()->kelas }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-[#0f172a] p-6 rounded-3xl premium-card shadow-lg border border-gray-50 dark:border-white/5 flex flex-col justify-center">
            <div class="flex justify-between items-end mb-4">
                <h4 class="font-bold text-gray-800 dark:text-white">Progress Presensi Hari Ini</h4>
                <span class="text-emerald-600 dark:text-emerald-400 font-bold text-xl">{{ $hadirPercentage }}%</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 mb-3">
                <div class="bg-[#065F46] h-3 rounded-full" style="width: {{ $hadirPercentage }}%"></div>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $stats['hadir'] }} / {{ $totalSiswa }} siswa sudah dipresensi</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <!-- Total Siswa -->
        <div class="bg-white dark:bg-[#0f172a] p-4 rounded-2xl shadow-md border border-gray-100 dark:border-white/5 flex flex-col justify-between items-start hover:shadow-xl transition-shadow">
            <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-3">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Total Siswa</p>
                <h4 class="text-xl font-bold text-gray-800 dark:text-white">{{ $totalSiswa }}</h4>
                <p class="text-[10px] text-gray-400 mt-1">Siswa</p>
            </div>
        </div>
        
        <!-- Hadir -->
        <div class="bg-white dark:bg-[#0f172a] p-4 rounded-2xl shadow-md border border-gray-100 dark:border-white/5 flex flex-col justify-between items-start hover:shadow-xl transition-shadow">
            <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 flex items-center justify-center mb-3">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Hadir</p>
                <h4 class="text-xl font-bold text-gray-800 dark:text-white">{{ $stats['hadir'] }}</h4>
                <p class="text-[10px] text-gray-400 mt-1">{{ $totalSiswa > 0 ? round(($stats['hadir']/$totalSiswa)*100,2) : 0 }}%</p>
            </div>
        </div>

        <!-- Izin -->
        <div class="bg-white dark:bg-[#0f172a] p-4 rounded-2xl shadow-md border border-gray-100 dark:border-white/5 flex flex-col justify-between items-start hover:shadow-xl transition-shadow">
            <div class="w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 flex items-center justify-center mb-3">
                <i data-lucide="info" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Izin</p>
                <h4 class="text-xl font-bold text-gray-800 dark:text-white">{{ $stats['izin'] }}</h4>
                <p class="text-[10px] text-gray-400 mt-1">{{ $totalSiswa > 0 ? round(($stats['izin']/$totalSiswa)*100,2) : 0 }}%</p>
            </div>
        </div>

        <!-- Sakit -->
        <div class="bg-white dark:bg-[#0f172a] p-4 rounded-2xl shadow-md border border-gray-100 dark:border-white/5 flex flex-col justify-between items-start hover:shadow-xl transition-shadow">
            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-3">
                <i data-lucide="plus-square" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Sakit</p>
                <h4 class="text-xl font-bold text-gray-800 dark:text-white">{{ $stats['sakit'] }}</h4>
                <p class="text-[10px] text-gray-400 mt-1">{{ $totalSiswa > 0 ? round(($stats['sakit']/$totalSiswa)*100,2) : 0 }}%</p>
            </div>
        </div>

        <!-- Alpha -->
        <div class="bg-white dark:bg-[#0f172a] p-4 rounded-2xl shadow-md border border-gray-100 dark:border-white/5 flex flex-col justify-between items-start hover:shadow-xl transition-shadow">
            <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center mb-3">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Alpha</p>
                <h4 class="text-xl font-bold text-gray-800 dark:text-white">{{ $stats['alpha'] }}</h4>
                <p class="text-[10px] text-gray-400 mt-1">{{ $totalSiswa > 0 ? round(($stats['alpha']/$totalSiswa)*100,2) : 0 }}%</p>
            </div>
        </div>

        <!-- Belum Absen -->
        <div class="bg-white dark:bg-[#0f172a] p-4 rounded-2xl shadow-md border border-gray-100 dark:border-white/5 flex flex-col justify-between items-start hover:shadow-xl transition-shadow">
            <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 flex items-center justify-center mb-3">
                <i data-lucide="clock" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Belum Absen</p>
                <h4 class="text-xl font-bold text-gray-800 dark:text-white">{{ $stats['belum_presensi'] }}</h4>
                <p class="text-[10px] text-gray-400 mt-1">{{ $totalSiswa > 0 ? round(($stats['belum_presensi']/$totalSiswa)*100,2) : 0 }}%</p>
            </div>
        </div>
    </div>

    <!-- Charts and Calendar Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Line Chart: 7 Hari Terakhir -->
        <div class="bg-white dark:bg-[#0f172a] p-6 rounded-3xl shadow-lg border border-gray-100 dark:border-white/5">
            <div class="flex justify-between items-center mb-6">
                <h4 class="font-bold text-gray-800 dark:text-white">Grafik Kehadiran 7 Hari Terakhir</h4>
                <span class="text-xs bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 py-1 px-3 rounded-full">7 Hari Terakhir</span>
            </div>
            <div class="relative h-48 w-full">
                <canvas id="attendanceLineChart"></canvas>
            </div>
        </div>

        <!-- Doughnut Chart: Persentase Hari Ini -->
        <div class="bg-white dark:bg-[#0f172a] p-6 rounded-3xl shadow-lg border border-gray-100 dark:border-white/5">
            <h4 class="font-bold text-gray-800 dark:text-white mb-6">Persentase Presensi Hari Ini</h4>
            <div class="flex items-center justify-center h-48 relative">
                <canvas id="attendancePieChart"></canvas>
                <div class="absolute flex flex-col items-center justify-center">
                    <span class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalSiswa }}</span>
                    <span class="text-xs text-gray-500">Total</span>
                </div>
            </div>
            <!-- Legend -->
            <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                <div class="flex items-center justify-between">
                    <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-[#10b981] mr-2"></span><span class="text-gray-600 dark:text-gray-400">Hadir</span></div>
                    <span class="font-semibold">{{ $totalSiswa > 0 ? round(($stats['hadir']/$totalSiswa)*100,2) : 0 }}% ({{ $stats['hadir'] }})</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-[#f59e0b] mr-2"></span><span class="text-gray-600 dark:text-gray-400">Izin</span></div>
                    <span class="font-semibold">{{ $totalSiswa > 0 ? round(($stats['izin']/$totalSiswa)*100,2) : 0 }}% ({{ $stats['izin'] }})</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-[#3b82f6] mr-2"></span><span class="text-gray-600 dark:text-gray-400">Sakit</span></div>
                    <span class="font-semibold">{{ $totalSiswa > 0 ? round(($stats['sakit']/$totalSiswa)*100,2) : 0 }}% ({{ $stats['sakit'] }})</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-[#ef4444] mr-2"></span><span class="text-gray-600 dark:text-gray-400">Alpha</span></div>
                    <span class="font-semibold">{{ $totalSiswa > 0 ? round(($stats['alpha']/$totalSiswa)*100,2) : 0 }}% ({{ $stats['alpha'] }})</span>
                </div>
            </div>
        </div>

        <!-- Calendar -->
        <div class="bg-white dark:bg-[#0f172a] p-6 rounded-3xl shadow-lg border border-gray-100 dark:border-white/5">
            <div class="flex justify-between items-center mb-6">
                <h4 class="font-bold text-gray-800 dark:text-white">Kalender</h4>
                <div class="flex items-center space-x-2">
                    <button id="prev-month" class="p-1 rounded bg-gray-50 dark:bg-gray-800 text-gray-600 hover:bg-gray-200 transition-colors"><i data-lucide="chevron-left" class="w-4 h-4"></i></button>
                    <span id="calendar-month-year" class="text-sm font-medium w-32 text-center">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</span>
                    <button id="next-month" class="p-1 rounded bg-gray-50 dark:bg-gray-800 text-gray-600 hover:bg-gray-200 transition-colors"><i data-lucide="chevron-right" class="w-4 h-4"></i></button>
                </div>
            </div>
            
            <div class="grid grid-cols-7 gap-1 text-center text-xs mb-2">
                <div class="text-gray-400">Sen</div><div class="text-gray-400">Sel</div><div class="text-gray-400">Rab</div>
                <div class="text-gray-400">Kam</div><div class="text-gray-400">Jum</div><div class="text-gray-400">Sab</div>
                <div class="text-gray-400">Min</div>
            </div>
            <div class="grid grid-cols-7 gap-1 text-center text-sm" id="mini-calendar">
                <!-- Disuntikkan melalui JavaScript -->
            </div>
        </div>
    </div>

    <!-- Bottom Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Info Siswa Belum Absen -->
        <div class="bg-white dark:bg-[#0f172a] p-6 rounded-3xl shadow-lg border border-gray-100 dark:border-white/5 flex flex-col">
            <h4 class="font-bold text-gray-800 dark:text-white mb-6">Siswa Belum Absen Hari Ini</h4>
            @if($stats['belum_presensi'] == 0)
            <div class="flex-1 bg-emerald-50 dark:bg-emerald-900/10 rounded-2xl p-6 flex items-center space-x-4 border border-emerald-100 dark:border-emerald-800/30">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-800/50 text-emerald-600 rounded-full flex items-center justify-center shrink-0">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
                <div>
                    <h5 class="font-bold text-emerald-800 dark:text-emerald-400">Semua siswa sudah dipresensi.</h5>
                    <p class="text-sm text-emerald-600 dark:text-emerald-500">Semangat! 👍</p>
                </div>
            </div>
            @else
            <div class="flex-1 bg-yellow-50 dark:bg-yellow-900/10 rounded-2xl p-6 flex items-center space-x-4 border border-yellow-100 dark:border-yellow-800/30">
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-800/50 text-yellow-600 rounded-full flex items-center justify-center shrink-0">
                    <i data-lucide="clock" class="w-6 h-6"></i>
                </div>
                <div>
                    <h5 class="font-bold text-yellow-800 dark:text-yellow-400">Ada {{ $stats['belum_presensi'] }} siswa</h5>
                    <p class="text-sm text-yellow-600 dark:text-yellow-500">Belum dipresensi hari ini.</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Riwayat Presensi Terakhir -->
        <div class="bg-white dark:bg-[#0f172a] p-6 rounded-3xl shadow-lg border border-gray-100 dark:border-white/5">
            <h4 class="font-bold text-gray-800 dark:text-white mb-6">Riwayat Presensi Terakhir</h4>
            <div class="space-y-4">
                @forelse($riwayatPresensi as $riwayat)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 dark:border-gray-800 last:border-0">
                    <span class="text-gray-600 dark:text-gray-300 text-sm font-medium">{{ $riwayat['tanggal'] }}</span>
                    <span class="text-xs font-semibold text-emerald-600 bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400 py-1 px-3 rounded-md">{{ $riwayat['status'] }}</span>
                </div>
                @empty
                <div class="text-center text-gray-500 text-sm py-4">Belum ada riwayat presensi.</div>
                @endforelse
            </div>
            @if(count($riwayatPresensi) > 0)
            <div class="mt-4 text-center">
                <a href="{{ route('wali.presensi.history') }}" class="text-sm text-[#065F46] font-semibold hover:underline">Lihat Semua</a>
            </div>
            @endif
        </div>

        <!-- Aksi Cepat -->
        <div class="bg-white dark:bg-[#0f172a] p-6 rounded-3xl shadow-lg border border-gray-100 dark:border-white/5">
            <h4 class="font-bold text-gray-800 dark:text-white mb-6">Aksi Cepat</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('wali.presensi.index') }}" class="flex flex-col items-center p-4 border border-gray-100 dark:border-gray-700 rounded-2xl hover:border-emerald-500 hover:shadow-md transition-all group bg-white dark:bg-gray-800 text-center">
                    <div class="w-12 h-12 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <i data-lucide="clipboard-check" class="w-6 h-6"></i>
                    </div>
                    <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Input Presensi</span>
                </a>
                
                <a href="{{ route('wali.siswa.index') }}" class="flex flex-col items-center p-4 border border-gray-100 dark:border-gray-700 rounded-2xl hover:border-blue-500 hover:shadow-md transition-all group bg-white dark:bg-gray-800 text-center">
                    <div class="w-12 h-12 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <i data-lucide="users" class="w-6 h-6"></i>
                    </div>
                    <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Data Siswa</span>
                </a>
                
                <a href="{{ route('wali.presensi.history') }}" class="flex flex-col items-center p-4 border border-gray-100 dark:border-gray-700 rounded-2xl hover:border-purple-500 hover:shadow-md transition-all group bg-white dark:bg-gray-800 text-center">
                    <div class="w-12 h-12 rounded-full bg-purple-50 dark:bg-purple-900/30 text-purple-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <i data-lucide="history" class="w-6 h-6"></i>
                    </div>
                    <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Riwayat</span>
                </a>
                
                <a href="{{ route('wali.pengajuan.index') }}" class="flex flex-col items-center p-4 border border-gray-100 dark:border-gray-700 rounded-2xl hover:border-teal-500 hover:shadow-md transition-all group bg-white dark:bg-gray-800 text-center">
                    <div class="w-12 h-12 rounded-full bg-teal-50 dark:bg-teal-900/30 text-teal-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <i data-lucide="send" class="w-6 h-6"></i>
                    </div>
                    <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Pengajuan</span>
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data from backend
            const chartLabels = {!! json_encode($chartLabels) !!};
            const chartData = {!! json_encode($chartData) !!};
            
            const totalSiswa = {{ $totalSiswa }};
            const stats = {!! json_encode($stats) !!};

            // Line Chart
            const ctxLine = document.getElementById('attendanceLineChart');
            if (ctxLine) {
                new Chart(ctxLine.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            label: 'Persentase Kehadiran (%)',
                            data: chartData,
                            borderColor: '#065F46',
                            backgroundColor: 'rgba(6, 95, 70, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#065F46',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.parsed.y + '%';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                ticks: {
                                    callback: function(value) { return value + '%' }
                                },
                                grid: { borderDash: [5, 5], color: 'rgba(0,0,0,0.05)' }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            }

            // Doughnut Chart
            const ctxPie = document.getElementById('attendancePieChart');
            if (ctxPie) {
                new Chart(ctxPie.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
                        datasets: [{
                            data: [stats.hadir, stats.izin, stats.sakit, stats.alpha],
                            backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#ef4444'],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '75%',
                        plugins: {
                            legend: { display: false },
                        }
                    }
                });
            }

            // Calendar Interactive Logic
            const calendarMonthYear = document.getElementById('calendar-month-year');
            const miniCalendarGrid = document.getElementById('mini-calendar');
            let currentDate = new Date();

            function renderCalendar(date) {
                if (!miniCalendarGrid || !calendarMonthYear) return;
                
                const year = date.getFullYear();
                const month = date.getMonth();
                
                const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                calendarMonthYear.textContent = `${monthNames[month]} ${year}`;
                
                const firstDayIndex = new Date(year, month, 1).getDay();
                const adjustedFirstDay = firstDayIndex === 0 ? 6 : firstDayIndex - 1;
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                
                const today = new Date();
                const isCurrentMonth = today.getMonth() === month && today.getFullYear() === year;
                const currentDay = today.getDate();
                
                let html = '';
                
                for (let i = 0; i < adjustedFirstDay; i++) {
                    html += '<div class="p-2 text-gray-300 dark:text-gray-700"></div>';
                }
                
                for (let day = 1; day <= daysInMonth; day++) {
                    if (isCurrentMonth && day === currentDay) {
                        html += `<div class="p-2 bg-[#065F46] text-white rounded-full font-bold w-8 h-8 flex items-center justify-center mx-auto shadow-md">${day}</div>`;
                    } else {
                        html += `<div class="p-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full w-8 h-8 flex items-center justify-center mx-auto cursor-pointer transition-colors">${day}</div>`;
                    }
                }
                
                miniCalendarGrid.innerHTML = html;
            }

            document.getElementById('prev-month')?.addEventListener('click', () => {
                currentDate.setMonth(currentDate.getMonth() - 1);
                renderCalendar(currentDate);
            });
            document.getElementById('next-month')?.addEventListener('click', () => {
                currentDate.setMonth(currentDate.getMonth() + 1);
                renderCalendar(currentDate);
            });

            renderCalendar(currentDate);
        });
    </script>
    @endpush
</x-app-layout>
