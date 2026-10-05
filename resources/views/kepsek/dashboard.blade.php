<x-app-layout>
    <x-slot name="header">Dashboard Kepala Sekolah</x-slot>

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

    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 {{ $gradient }} p-8 md:p-10 rounded-[24px] premium-card shadow-lg border border-gray-50 dark:border-white/5 relative overflow-hidden group">
        <!-- Dynamic Background Illustration -->
        <div class="absolute right-0 bottom-0 top-0 w-1/2 md:w-1/3 opacity-30 dark:opacity-20 pointer-events-none transition-opacity duration-700 group-hover:opacity-40 dark:group-hover:opacity-30 flex items-end justify-end z-0">
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

        <div class="relative z-10 w-full md:w-2/3">
            <h3 class="text-2xl md:text-[28px] font-extrabold {{ $textMain }} mb-2 tracking-tight">
                {{ $greeting }}, {{ ucwords(strtolower(auth()->user()->name)) }}!
            </h3>
            <p class="text-sm md:text-base {{ $textSub }} font-medium mb-8 leading-relaxed">
                Monitoring seluruh kehadiran siswa MI hari ini.
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
                        <i data-lucide="user-check" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
                    </div>
                    <span>Kepala Sekolah</span>
                </div>
            </div>
        </div>

        @if($pendingApproval > 0)
            <div class="relative z-10">
                <a href="{{ route('kepsek.approval.index') }}" class="flex items-center gap-2 px-6 py-3 bg-red-50 text-red-600 rounded-2xl font-bold text-sm border border-red-100 shadow-md hover:shadow-lg transition-shadow animate-pulse">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                    {{ $pendingApproval }} Pengajuan Direview
                </a>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-stat-card label="Total Seluruh Siswa" value="{{ $totalSiswa }}" icon="users" color="emerald" />
        <x-stat-card label="Total Rombel" value="{{ $totalKelas }}" icon="school" color="green" />
        <x-stat-card label="Hadir (Hari Ini)" value="{{ $stats['hadir'] }}" icon="check-circle" color="green" />
        <x-stat-card label="Tanpa Keterangan" value="{{ $stats['alpha'] }}" icon="alert-circle" color="red" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Quick Actions -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-6 rounded-3xl premium-card shadow-lg border border-gray-50">
                <h4 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i data-lucide="zap" class="w-5 h-5 text-yellow-500"></i>
                    Aksi Cepat
                </h4>
                <div class="grid grid-cols-3 gap-3">
                    <a href="{{ route('kepsek.monitoring') }}" class="flex flex-col items-center p-3 border border-gray-100 dark:border-gray-700 rounded-2xl hover:border-emerald-500 hover:shadow-md transition-all group bg-white text-center">
                        <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                            <i data-lucide="eye" class="w-5 h-5"></i>
                        </div>
                        <span class="text-[11px] leading-tight font-bold text-gray-700">Monitoring Realtime</span>
                    </a>
                    <a href="{{ route('kepsek.approval.index') }}" class="flex flex-col items-center p-3 border border-gray-100 dark:border-gray-700 rounded-2xl hover:border-green-500 hover:shadow-md transition-all group bg-white text-center">
                        <div class="w-10 h-10 rounded-full bg-green-50 text-green-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                            <i data-lucide="check-square" class="w-5 h-5"></i>
                        </div>
                        <span class="text-[11px] leading-tight font-bold text-gray-700">Persetujuan Rekap</span>
                    </a>
                    <a href="{{ route('kepsek.laporan') }}" class="flex flex-col items-center p-3 border border-gray-100 dark:border-gray-700 rounded-2xl hover:border-teal-500 hover:shadow-md transition-all group bg-white text-center">
                        <div class="w-10 h-10 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                            <i data-lucide="file-text" class="w-5 h-5"></i>
                        </div>
                        <span class="text-[11px] leading-tight font-bold text-gray-700">Lihat Laporan</span>
                    </a>
                </div>
            </div>

            <!-- Chart -->
            <div class="bg-white p-6 rounded-3xl premium-card shadow-lg border border-gray-50">
                <h4 class="font-bold text-gray-900 mb-4">Persentase Kehadiran</h4>
                <div class="aspect-square">
                    <canvas id="kepsekChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Monitoring Rangkuman Section -->
        <div class="lg:col-span-2 bg-white dark:bg-[#0f172a] p-8 rounded-3xl premium-card shadow-lg border border-gray-50 dark:border-white/5">
            <div class="flex justify-between items-center mb-6">
                <h4 class="text-xl font-bold text-gray-900 dark:text-white">Monitoring Rangkuman</h4>
                <span class="px-4 py-1 bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 rounded-full text-xs font-bold">{{ date('d M Y') }}</span>
            </div>
            
            <div class="space-y-6">
                @foreach($kelasProgress as $progress)
                    <div class="group">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-bold text-gray-700 dark:text-gray-300">Kelas {{ $progress['nama_kelas'] }}</span>
                            @if($progress['persentase'] == 100)
                                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Selesai ({{ $progress['presensi_count'] }}/{{ $progress['total_siswa'] }})</span>
                            @elseif($progress['terpantau'])
                                <span class="text-xs font-bold text-blue-600 dark:text-blue-400">Proses ({{ $progress['presensi_count'] }}/{{ $progress['total_siswa'] }}) - {{ $progress['persentase'] }}%</span>
                            @else
                                <span class="text-xs font-bold text-gray-400 dark:text-gray-500">Belum (0/{{ $progress['total_siswa'] }})</span>
                            @endif
                        </div>
                        <div class="w-full h-3 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                            <div class="h-full @if($progress['persentase'] == 100) bg-gradient-to-r from-emerald-500 to-green-600 @elseif($progress['terpantau']) bg-gradient-to-r from-blue-500 to-indigo-600 @else bg-gray-300 dark:bg-gray-700 @endif rounded-full transition-all duration-1000 ease-out" style="width: {{ $progress['persentase'] }}%;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Kalender Section - Separate Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-8">
        <div class="lg:col-span-1 bg-white dark:bg-[#0f172a] p-6 rounded-3xl premium-card shadow-lg border border-gray-50 dark:border-white/5">
            <div class="mb-5">
                <h4 class="font-bold text-gray-800 dark:text-white flex items-center gap-2 mb-4">
                    <i data-lucide="calendar-days" class="w-5 h-5 text-emerald-600"></i>
                    Kalender
                </h4>
                <div class="flex items-center justify-between">
                    <button id="prev-month" class="p-1.5 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"><i data-lucide="chevron-left" class="w-4 h-4"></i></button>
                    <span id="calendar-month-year" class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</span>
                    <button id="next-month" class="p-1.5 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"><i data-lucide="chevron-right" class="w-4 h-4"></i></button>
                </div>
            </div>
            
            <div class="grid grid-cols-7 gap-1 text-center text-xs mb-2">
                <div class="text-gray-400 font-semibold">Sen</div><div class="text-gray-400 font-semibold">Sel</div><div class="text-gray-400 font-semibold">Rab</div>
                <div class="text-gray-400 font-semibold">Kam</div><div class="text-gray-400 font-semibold">Jum</div><div class="text-gray-400 font-semibold">Sab</div>
                <div class="text-gray-400 font-semibold">Min</div>
            </div>
            <div class="grid grid-cols-7 gap-1 text-center text-sm" id="mini-calendar">
                <!-- Disuntikkan melalui JavaScript -->
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('kepsekChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
                    datasets: [{
                        data: [
                            {{ $stats['hadir'] }}, 
                            {{ $stats['izin'] }}, 
                            {{ $stats['sakit'] }}, 
                            {{ $stats['alpha'] }}
                        ],
                        backgroundColor: ['#10B981', '#F59E0B', '#3B82F6', '#EF4444'],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    cutout: '70%',
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, font: { weight: 'bold' } } }
                    }
                }
            });

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
