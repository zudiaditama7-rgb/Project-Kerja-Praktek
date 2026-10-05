<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Presensi MI') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://unpkg.com/lucide@latest"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        
        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
            
            /* Global Floating Elements Effect (Thicker & Prominent) */
            main .bg-white, 
            main .dark\:bg-\[\#0f172a\],
            main .premium-card {
                box-shadow: 0 15px 40px -5px rgba(0,0,0,0.12), 0 8px 16px -8px rgba(0,0,0,0.08) !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
                border: 1px solid rgba(255, 255, 255, 0.8) !important;
            }
            
            .dark main .bg-white, 
            .dark main .dark\:bg-\[\#0f172a\],
            .dark main .premium-card {
                box-shadow: 0 15px 40px -5px rgba(0,0,0,0.4), 0 8px 16px -8px rgba(0,0,0,0.3) !important;
                border: 1px solid rgba(255, 255, 255, 0.05) !important;
            }
            
            main .bg-white:hover, 
            main .dark\:bg-\[\#0f172a\]:hover,
            main .premium-card:hover {
                transform: translateY(-5px) !important;
                box-shadow: 0 25px 50px -12px rgba(0,0,0,0.2), 0 15px 25px -10px rgba(0,0,0,0.1) !important;
            }
            
            .dark main .bg-white:hover, 
            .dark main .dark\:bg-\[\#0f172a\]:hover,
            .dark main .premium-card:hover {
                box-shadow: 0 25px 50px -12px rgba(0,0,0,0.6), 0 15px 25px -10px rgba(0,0,0,0.4) !important;
            }
            
            /* Interactive Elements Floating (Buttons) */
            main a.bg-emerald-600, main button.bg-emerald-600,
            main a.bg-blue-600, main button.bg-blue-600,
            main a.bg-red-600, main button.bg-red-600,
            main a.bg-white, main button.bg-white {
                box-shadow: 0 6px 16px -4px rgba(0,0,0,0.15) !important;
                transition: all 0.2s ease !important;
            }
            
            main a.bg-emerald-600:hover, main button.bg-emerald-600:hover,
            main a.bg-blue-600:hover, main button.bg-blue-600:hover,
            main a.bg-red-600:hover, main button.bg-red-600:hover,
            main a.bg-white:hover, main button.bg-white:hover {
                transform: translateY(-3px) !important;
                box-shadow: 0 12px 24px -6px rgba(0,0,0,0.25) !important;
            }
        </style>

        <!-- Early Dark Mode Detection -->
        <script>
            if (localStorage.getItem('darkMode') === 'true' || 
                (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900 dark:bg-[#020617] dark:text-gray-100 overflow-x-hidden transition-colors duration-300" 
          x-data="{ 
              darkMode: localStorage.getItem('darkMode') === 'true',
              isSubmitting: false,
              toggleDarkMode() {
                  this.darkMode = !this.darkMode;
                  localStorage.setItem('darkMode', this.darkMode);
              }
          }" 
          @submit.window="isSubmitting = true"
          x-on:stop-submitting.window="isSubmitting = false"
          :class="{ 'dark': darkMode }">
        <div class="flex min-h-screen">
            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Main Content -->
            <div class="flex-1 ml-64 min-h-screen flex flex-col dark:bg-[#020617]">
                <!-- Navbar -->
                <nav class="h-20 bg-white dark:bg-[#0f172a] border-b border-gray-100 dark:border-gray-800 sticky top-0 z-40 flex justify-between items-center px-8 shadow-sm transition-colors duration-300">
                    <div>
                        <h2 class="text-xl font-bold text-[#065F46] dark:text-emerald-400">
                            {{ $header ?? 'Dashboard' }}
                        </h2>
                    </div>

                    <div class="flex items-center gap-4 md:gap-6">
                        <!-- Notifications (Admin & Kepsek) -->
                        @php $bellNotifCount = (auth()->user()->role === 'kepala_sekolah') ? (($pendingPengajuanCount ?? 0) + ($pendingPenggantiCount ?? 0)) : ($pendingPenggantiCount ?? 0); @endphp
                        @if((auth()->user()->role === 'admin' || auth()->user()->role === 'kepala_sekolah') && isset($pendingPenggantiCount))
                            <div x-data="{ 
                                    open: sessionStorage.getItem('notifOpen') === 'true',
                                    toggle() { this.open = !this.open; sessionStorage.setItem('notifOpen', this.open); },
                                    close() { this.open = false; sessionStorage.setItem('notifOpen', false); }
                                }" class="relative">
                                <button @click="toggle()" @click.away="close()" 
                                   class="relative p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition shadow-sm border border-gray-100 dark:border-gray-700"
                                   title="{{ $bellNotifCount > 0 ? 'Ada '.$bellNotifCount.' notifikasi baru' : 'Tidak ada notifikasi baru' }}">
                                    <i data-lucide="bell" class="w-5 h-5 {{ $bellNotifCount > 0 ? 'animate-pulse text-amber-500' : 'text-gray-500 dark:text-gray-400' }}"></i>
                                    @if($bellNotifCount > 0)
                                        <span class="absolute -top-1 -right-1 flex h-4 w-4">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500 text-[9px] font-bold text-white items-center justify-center border-2 border-white dark:border-[#0f172a]">
                                                {{ $bellNotifCount > 9 ? '9+' : $bellNotifCount }}
                                            </span>
                                        </span>
                                    @endif
                                </button>

                                <!-- Dropdown Content -->
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave="transition ease-in duration-200"
                                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                     class="absolute right-0 mt-3 w-96 bg-white/95 dark:bg-[#1e293b]/95 backdrop-blur-xl rounded-3xl shadow-2xl shadow-emerald-900/10 dark:shadow-black/40 border border-gray-100 dark:border-white/10 overflow-hidden z-50 ring-1 ring-black/5 dark:ring-white/5"
                                     style="display: none;">
                                    
                                    <!-- Header -->
                                    <div class="px-6 py-5 bg-gradient-to-r from-emerald-50 via-white to-white dark:from-emerald-900/20 dark:via-gray-800 dark:to-gray-800 border-b border-gray-100 dark:border-gray-700/50 flex justify-between items-center relative overflow-hidden">
                                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl"></div>
                                        <h3 class="font-extrabold text-gray-900 dark:text-white text-sm tracking-widest relative z-10 flex items-center gap-2">
                                            <i data-lucide="bell" class="w-4 h-4 text-emerald-600 dark:text-emerald-400"></i>
                                            NOTIFIKASI
                                        </h3>
                                        @if($bellNotifCount > 0)
                                            <span class="relative z-10 px-3 py-1 rounded-full bg-gradient-to-r from-red-500 to-rose-500 text-white text-[10px] font-black uppercase tracking-widest shadow-lg shadow-red-500/30 animate-pulse">
                                                {{ $bellNotifCount }} Baru
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Content -->
                                    <div class="max-h-[400px] overflow-y-auto custom-scrollbar">
                                        @if($bellNotifCount > 0)
                                            <div class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                                {{-- Kepsek: Pengajuan Rekap Presensi --}}
                                                @if(auth()->user()->role === 'kepala_sekolah' && isset($pendingPengajuanList))
                                                    @foreach($pendingPengajuanList as $pengajuan)
                                                    <div class="p-5 hover:bg-emerald-50/50 dark:hover:bg-emerald-900/10 transition-all duration-300 group">
                                                        <div class="flex gap-4">
                                                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-100 to-teal-50 dark:from-emerald-900/40 dark:to-teal-900/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-sm border border-emerald-200/50 dark:border-emerald-700/30 group-hover:scale-110 transition-transform duration-300">
                                                                <i data-lucide="file-text" class="w-6 h-6"></i>
                                                            </div>
                                                            <div class="w-full">
                                                                <div class="flex justify-between items-start mb-1">
                                                                    <span class="inline-block px-2.5 py-0.5 bg-emerald-100 dark:bg-emerald-800 text-emerald-700 dark:text-emerald-300 rounded-md text-[10px] font-black uppercase tracking-wider">Rekap Presensi</span>
                                                                    <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 flex items-center gap-1"><i data-lucide="clock" class="w-3 h-3"></i>{{ $pengajuan->created_at->diffForHumans() }}</span>
                                                                </div>
                                                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-2 leading-relaxed">
                                                                    <span class="font-black text-gray-900 dark:text-white">{{ $pengajuan->user ? $pengajuan->user->name : 'Wali Kelas' }}</span> mengajukan rekap presensi <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ ucfirst($pengajuan->tipe) }} {{ $pengajuan->bulan }}/{{ $pengajuan->tahun }}</span> — Kelas {{ $pengajuan->kelas }}.
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                @endif
                                                @foreach($pendingPenggantiList as $pending)
                                                <div class="p-5 hover:bg-emerald-50/50 dark:hover:bg-emerald-900/10 transition-all duration-300 group">
                                                    <div class="flex gap-4">
                                                        <!-- Avatar Icon -->
                                                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-100 to-orange-50 dark:from-amber-900/40 dark:to-orange-900/20 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 shadow-sm border border-amber-200/50 dark:border-amber-700/30 group-hover:scale-110 transition-transform duration-300">
                                                            <i data-lucide="user-clock" class="w-6 h-6"></i>
                                                        </div>
                                                        
                                                        <div class="w-full">
                                                            <!-- Info Bar -->
                                                            <div class="flex justify-between items-start mb-1">
                                                                <span class="inline-block px-2.5 py-0.5 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-md text-[10px] font-black uppercase tracking-wider">
                                                                    Kelas {{ $pending->kelas }}
                                                                </span>
                                                                <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 flex items-center gap-1">
                                                                    <i data-lucide="clock" class="w-3 h-3"></i>
                                                                    {{ $pending->created_at->diffForHumans() }}
                                                                </span>
                                                            </div>
                                                            
                                                            <!-- Description -->
                                                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-2 leading-relaxed">
                                                                <span class="font-black text-gray-900 dark:text-white">{{ $pending->waliKelas ? $pending->waliKelas->name : ($pending->guruMapel ? $pending->guruMapel->name : 'Pengaju') }}</span> mengajukan guru pengganti untuk <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ \Carbon\Carbon::parse($pending->tanggal)->format('d M Y') }}</span>.
                                                            </p>
                                                            
                                                            <!-- Reason Box -->
                                                            <div class="mt-3 p-3.5 bg-gradient-to-br from-gray-50 to-white dark:from-gray-800/80 dark:to-gray-900 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-inner relative">
                                                                <i data-lucide="quote" class="absolute top-2 right-2 w-8 h-8 text-gray-100 dark:text-gray-700/50 rotate-180"></i>
                                                                <p class="text-[11px] font-semibold text-gray-700 dark:text-gray-300 italic relative z-10 leading-relaxed pr-6">
                                                                    "{{ $pending->alasan }}"
                                                                </p>
                                                                
                                                                <!-- Action Button -->
                                                                @if($pending->document_path)
                                                                    <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 relative z-10">
                                                                        <a href="{{ route('tugas_pengganti.dokumen', $pending->id) }}" class="inline-flex items-center justify-center gap-2 w-full py-2 bg-white dark:bg-gray-800 text-[#065F46] dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900 hover:bg-[#065F46] hover:text-white dark:hover:bg-emerald-600 hover:border-transparent rounded-xl text-xs font-bold transition-all duration-300 group/btn shadow-sm hover:shadow-md hover:shadow-emerald-500/20">
                                                                            <i data-lucide="file-badge" class="w-4 h-4 group-hover/btn:-translate-y-0.5 transition-transform"></i>
                                                                            Lihat Bukti Dokumen
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                            
                                            <!-- Footer -->
                                            @if(auth()->user()->role === 'admin')
                                                <div class="p-4 border-t border-gray-100 dark:border-gray-700/50 bg-gray-50/50 dark:bg-gray-800/50 text-center">
                                                    <a href="{{ route('admin.tugas_pengganti.index') }}" class="inline-flex items-center gap-2 text-xs font-extrabold text-[#065F46] hover:text-emerald-700 dark:text-emerald-400 transition-colors group">
                                                        TINJAU SEMUA PENGAJUAN 
                                                        <span class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center group-hover:bg-emerald-200 dark:group-hover:bg-emerald-800 transition-colors"><i data-lucide="arrow-right" class="w-3.5 h-3.5"></i></span>
                                                    </a>
                                                </div>
                                            @elseif(auth()->user()->role === 'kepala_sekolah')
                                                <div class="p-4 border-t border-gray-100 dark:border-gray-700/50 bg-gray-50/50 dark:bg-gray-800/50 text-center">
                                                    <a href="{{ route('kepsek.approval.index') }}" class="inline-flex items-center gap-2 text-xs font-extrabold text-[#065F46] hover:text-emerald-700 dark:text-emerald-400 transition-colors group">
                                                        TINJAU SEMUA PENGAJUAN 
                                                        <span class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center group-hover:bg-emerald-200 dark:group-hover:bg-emerald-800 transition-colors"><i data-lucide="arrow-right" class="w-3.5 h-3.5"></i></span>
                                                    </a>
                                                </div>
                                            @endif
                                        @else
                                            <div class="flex flex-col items-center justify-center py-12 text-center px-6">
                                                <div class="w-20 h-20 bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-full flex items-center justify-center mb-5 text-emerald-400 dark:text-emerald-500 shadow-inner">
                                                    @if(auth()->user()->role === 'kepala_sekolah')
                                                        <i data-lucide="folder-open" class="w-10 h-10"></i>
                                                    @else
                                                        <i data-lucide="check-circle-2" class="w-10 h-10"></i>
                                                    @endif
                                                </div>
                                                
                                                @if(auth()->user()->role === 'kepala_sekolah')
                                                    <h4 class="text-base font-extrabold text-gray-900 dark:text-white mb-2">Belum Ada Pengajuan</h4>
                                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 leading-relaxed">Saat ini tidak ada pengajuan rekap presensi atau guru pengganti dari Wali Kelas.</p>
                                                @else
                                                    <h4 class="text-base font-extrabold text-gray-900 dark:text-white mb-2">Semua Selesai!</h4>
                                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 leading-relaxed">Tidak ada pengajuan guru pengganti yang berstatus pending.</p>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Notifications (Guru Pengganti) -->
                        @if(auth()->user()->role === 'guru_pengganti' && isset($assignedTugasCount))
                            <div x-data="{ 
                                    open: sessionStorage.getItem('gpNotifOpen') === 'true',
                                    toggle() { this.open = !this.open; sessionStorage.setItem('gpNotifOpen', this.open); },
                                    close() { this.open = false; sessionStorage.setItem('gpNotifOpen', false); }
                                }" class="relative">
                                <button @click="toggle()" @click.away="close()" 
                                   class="relative p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition shadow-sm border border-gray-100 dark:border-gray-700"
                                   title="{{ $assignedTugasCount > 0 ? 'Ada '.$assignedTugasCount.' penugasan presensi' : 'Tidak ada penugasan baru' }}">
                                    <i data-lucide="bell" class="w-5 h-5 {{ $assignedTugasCount > 0 ? 'animate-pulse text-amber-500' : 'text-gray-500 dark:text-gray-400' }}"></i>
                                    @if($assignedTugasCount > 0)
                                        <span class="absolute -top-1 -right-1 flex h-4 w-4">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500 text-[9px] font-bold text-white items-center justify-center border-2 border-white dark:border-[#0f172a]">
                                                {{ $assignedTugasCount > 9 ? '9+' : $assignedTugasCount }}
                                            </span>
                                        </span>
                                    @endif
                                </button>

                                <!-- Dropdown Content -->
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave="transition ease-in duration-200"
                                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                     class="absolute right-0 mt-3 w-96 bg-white/95 dark:bg-[#1e293b]/95 backdrop-blur-xl rounded-3xl shadow-2xl shadow-blue-900/10 dark:shadow-black/40 border border-gray-100 dark:border-white/10 overflow-hidden z-50 ring-1 ring-black/5 dark:ring-white/5"
                                     style="display: none;">
                                    
                                    <!-- Header -->
                                    <div class="px-6 py-5 bg-gradient-to-r from-blue-50 via-white to-white dark:from-blue-900/20 dark:via-gray-800 dark:to-gray-800 border-b border-gray-100 dark:border-gray-700/50 flex justify-between items-center relative overflow-hidden">
                                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl"></div>
                                        <h3 class="font-extrabold text-gray-900 dark:text-white text-sm tracking-widest relative z-10 flex items-center gap-2">
                                            <i data-lucide="bell" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                                            PENUGASAN
                                        </h3>
                                        @if($assignedTugasCount > 0)
                                            <span class="relative z-10 px-3 py-1 rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 text-white text-[10px] font-black uppercase tracking-widest shadow-lg shadow-blue-500/30 animate-pulse">
                                                {{ $assignedTugasCount }} Aktif
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Content -->
                                    <div class="max-h-[400px] overflow-y-auto custom-scrollbar">
                                        @if($assignedTugasCount > 0 && isset($assignedTugasList))
                                            <div class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                                @foreach($assignedTugasList as $tugas)
                                                <div class="p-5 hover:bg-blue-50/50 dark:hover:bg-blue-900/10 transition-all duration-300 group">
                                                    <div class="flex gap-4">
                                                        <!-- Avatar Icon -->
                                                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-100 to-indigo-50 dark:from-blue-900/40 dark:to-indigo-900/20 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 shadow-sm border border-blue-200/50 dark:border-blue-700/30 group-hover:scale-110 transition-transform duration-300">
                                                            <i data-lucide="clipboard-check" class="w-6 h-6"></i>
                                                        </div>
                                                        
                                                        <div class="w-full">
                                                            <!-- Info Bar -->
                                                            <div class="flex justify-between items-start mb-1">
                                                                <span class="inline-block px-2.5 py-0.5 bg-blue-100 dark:bg-blue-800 text-blue-700 dark:text-blue-300 rounded-md text-[10px] font-black uppercase tracking-wider">
                                                                    Kelas {{ $tugas->kelas }}
                                                                </span>
                                                                <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 flex items-center gap-1">
                                                                    <i data-lucide="calendar" class="w-3 h-3"></i>
                                                                    {{ \Carbon\Carbon::parse($tugas->tanggal)->format('d M Y') }}
                                                                </span>
                                                            </div>
                                                            
                                                            <!-- Description -->
                                                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-2 leading-relaxed">
                                                                Anda ditugaskan oleh Admin untuk melakukan <span class="font-black text-blue-600 dark:text-blue-400">presensi</span> di <span class="font-bold text-gray-900 dark:text-white">Kelas {{ $tugas->kelas }}</span> menggantikan 
                                                                <span class="font-bold text-gray-900 dark:text-white">
                                                                    {{ $tugas->waliKelas ? $tugas->waliKelas->name . ' (Wali Kelas)' : ($tugas->guruMapel ? $tugas->guruMapel->name . ' (Guru Mapel)' : '-') }}
                                                                </span>.
                                                            </p>
                                                            
                                                            <!-- Reason Box -->
                                                            <div class="mt-3 p-3.5 bg-gradient-to-br from-gray-50 to-white dark:from-gray-800/80 dark:to-gray-900 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-inner relative">
                                                                <i data-lucide="quote" class="absolute top-2 right-2 w-8 h-8 text-gray-100 dark:text-gray-700/50 rotate-180"></i>
                                                                <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Alasan Berhalangan</p>
                                                                <p class="text-[11px] font-semibold text-gray-700 dark:text-gray-300 italic relative z-10 leading-relaxed pr-6">
                                                                    "{{ $tugas->alasan }}"
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="flex flex-col items-center justify-center py-12 text-center px-6">
                                                <div class="w-20 h-20 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-full flex items-center justify-center mb-5 text-blue-400 dark:text-blue-500 shadow-inner">
                                                    <i data-lucide="check-circle-2" class="w-10 h-10"></i>
                                                </div>
                                                <h4 class="text-base font-extrabold text-gray-900 dark:text-white mb-2">Tidak Ada Penugasan</h4>
                                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 leading-relaxed">Saat ini tidak ada tugas presensi pengganti yang ditugaskan kepada Anda.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Notifications (Wali Kelas) -->
                        @if(auth()->user()->role === 'wali_kelas' && isset($waliNotifCount))
                            @php 
                                $latestKey = $waliNotifCount > 0 ? \Carbon\Carbon::parse($waliNotifList->first()['date'])->timestamp : 0; 
                            @endphp
                            <div x-data="{ 
                                    open: sessionStorage.getItem('wkNotifOpen') === 'true',
                                    latestKey: '{{ $latestKey }}',
                                    hasNew: false,
                                    init() {
                                        this.hasNew = localStorage.getItem('wkLastViewed') !== this.latestKey && this.latestKey !== '0';
                                    },
                                    toggle() { 
                                        this.open = !this.open; 
                                        sessionStorage.setItem('wkNotifOpen', this.open); 
                                        if (this.open) {
                                            localStorage.setItem('wkLastViewed', this.latestKey);
                                            this.hasNew = false;
                                        }
                                    },
                                    close() { 
                                        this.open = false; 
                                        sessionStorage.setItem('wkNotifOpen', false); 
                                    }
                                }" class="relative">
                                <button @click="toggle()" @click.away="close()" 
                                   class="relative p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition shadow-sm border border-gray-100 dark:border-gray-700"
                                   :title="hasNew ? 'Ada notifikasi baru' : 'Tidak ada notifikasi baru'">
                                    <i data-lucide="bell" class="w-5 h-5 transition-colors" :class="hasNew ? 'animate-pulse text-amber-500' : 'text-gray-500 dark:text-gray-400'"></i>
                                    <template x-if="hasNew">
                                        <span class="absolute -top-1 -right-1 flex h-4 w-4">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500 text-[9px] font-bold text-white items-center justify-center border-2 border-white dark:border-[#0f172a]">
                                                {{ $waliNotifCount > 9 ? '9+' : $waliNotifCount }}
                                            </span>
                                        </span>
                                    </template>
                                </button>

                                <!-- Dropdown Content -->
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave="transition ease-in duration-200"
                                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                     class="absolute right-0 mt-3 w-96 bg-white/95 dark:bg-[#1e293b]/95 backdrop-blur-xl rounded-3xl shadow-2xl shadow-emerald-900/10 dark:shadow-black/40 border border-gray-100 dark:border-white/10 overflow-hidden z-50 ring-1 ring-black/5 dark:ring-white/5"
                                     style="display: none;">
                                    
                                    <!-- Header -->
                                    <div class="px-6 py-5 bg-gradient-to-r from-emerald-50 via-white to-white dark:from-emerald-900/20 dark:via-gray-800 dark:to-gray-800 border-b border-gray-100 dark:border-gray-700/50 flex justify-between items-center relative overflow-hidden">
                                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl"></div>
                                        <h3 class="font-extrabold text-gray-900 dark:text-white text-sm tracking-widest relative z-10 flex items-center gap-2">
                                            <i data-lucide="bell" class="w-4 h-4 text-emerald-600 dark:text-emerald-400"></i>
                                            NOTIFIKASI
                                        </h3>
                                        <template x-if="hasNew">
                                            <span class="relative z-10 px-3 py-1 rounded-full bg-gradient-to-r from-red-500 to-rose-500 text-white text-[10px] font-black uppercase tracking-widest shadow-lg shadow-red-500/30 animate-pulse">
                                                {{ $waliNotifCount }} Baru
                                            </span>
                                        </template>
                                    </div>

                                    <!-- Content -->
                                    <div class="max-h-[400px] overflow-y-auto custom-scrollbar">
                                        @if($waliNotifCount > 0 && isset($waliNotifList))
                                            <div class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                                @foreach($waliNotifList as $notif)
                                                <div class="p-5 hover:bg-emerald-50/50 dark:hover:bg-emerald-900/10 transition-all duration-300 group">
                                                    <div class="flex gap-4">
                                                        <!-- Avatar Icon -->
                                                        <div class="w-12 h-12 rounded-2xl {{ $notif['status'] === 'disetujui' ? 'bg-gradient-to-br from-emerald-100 to-teal-50 dark:from-emerald-900/40 dark:to-teal-900/20 text-emerald-600 dark:text-emerald-400 border-emerald-200/50 dark:border-emerald-700/30' : 'bg-gradient-to-br from-red-100 to-rose-50 dark:from-red-900/40 dark:to-rose-900/20 text-red-600 dark:text-red-400 border-red-200/50 dark:border-red-700/30' }} flex items-center justify-center shrink-0 shadow-sm border group-hover:scale-110 transition-transform duration-300">
                                                            @if($notif['type'] === 'pengajuan')
                                                                <i data-lucide="file-text" class="w-6 h-6"></i>
                                                            @else
                                                                <i data-lucide="user-plus" class="w-6 h-6"></i>
                                                            @endif
                                                        </div>
                                                        
                                                        <div class="w-full">
                                                            <!-- Info Bar -->
                                                            <div class="flex justify-between items-start mb-1">
                                                                <span class="inline-block px-2.5 py-0.5 {{ $notif['status'] === 'disetujui' ? 'bg-emerald-100 dark:bg-emerald-800 text-emerald-700 dark:text-emerald-300' : 'bg-red-100 dark:bg-red-800 text-red-700 dark:text-red-300' }} rounded-md text-[10px] font-black uppercase tracking-wider">
                                                                    {{ $notif['status'] }}
                                                                </span>
                                                                <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 flex items-center gap-1">
                                                                    <i data-lucide="clock" class="w-3 h-3"></i>
                                                                    {{ \Carbon\Carbon::parse($notif['date'])->diffForHumans() }}
                                                                </span>
                                                            </div>
                                                            
                                                            <!-- Description -->
                                                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-2 leading-relaxed">
                                                                <span class="font-black text-gray-900 dark:text-white">{{ $notif['title'] }}:</span> {{ $notif['desc'] }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="flex flex-col items-center justify-center py-12 text-center px-6">
                                                <div class="w-20 h-20 bg-gradient-to-br from-gray-50 to-slate-50 dark:from-gray-800/50 dark:to-gray-900/50 rounded-full flex items-center justify-center mb-5 text-gray-400 dark:text-gray-500 shadow-inner">
                                                    <i data-lucide="bell-off" class="w-10 h-10"></i>
                                                </div>
                                                <h4 class="text-base font-extrabold text-gray-900 dark:text-white mb-2">Belum Ada Notifikasi</h4>
                                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 leading-relaxed">Pembaruan status dari Kepsek dan Admin akan muncul di sini.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Dark Mode Toggle -->
                        <button @click="toggleDarkMode()" class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition shadow-sm border border-gray-100 dark:border-gray-700">
                            <i x-show="!darkMode" data-lucide="moon" class="w-5 h-5"></i>
                            <i x-show="darkMode" data-lucide="sun" class="w-5 h-5"></i>
                        </button>

                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-4 hover:opacity-80 transition cursor-pointer">
                            <div class="text-right hidden sm:block">
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                                <p class="text-[10px] uppercase tracking-wider font-semibold text-gray-400">
                                    {{ str_replace('_', ' ', auth()->user()->role) }}
                                </p>
                            </div>
                            @if(auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="w-10 h-10 rounded-xl object-cover shadow-lg shadow-emerald-100 dark:shadow-none border border-emerald-500/20" alt="Avatar">
                            @else
                                <div class="w-10 h-10 rounded-xl bg-[#065F46] flex items-center justify-center text-white font-bold shadow-lg shadow-[#065F46]/20">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            @endif
                        </a>
                    </div>
                </nav>

                <!-- Page Content -->
                <main class="p-8 page-fade-in">
                    {{ $slot }}
                </main>
                

            </div>
        </div>

        <!-- Global Loading Overlay -->
        <div x-show="isSubmitting" 
             x-transition.opacity.duration.300ms
             class="fixed inset-0 z-[70] flex flex-col items-center justify-center bg-white/80 dark:bg-[#020617]/80 backdrop-blur-sm"
             style="display: none;">
             
             <!-- Spinner Container -->
             <div class="relative flex items-center justify-center w-24 h-24 mb-6">
                <div class="absolute inset-0 border-4 border-emerald-100 dark:border-emerald-900 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-emerald-500 rounded-full border-t-transparent animate-spin"></div>
                <i data-lucide="loader-2" class="w-8 h-8 text-emerald-600 dark:text-emerald-400 animate-spin"></i>
             </div>
             <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Memproses Data...</h3>
             <p class="text-sm text-gray-500 dark:text-gray-400">Sistem sedang menyimpan perubahan Anda</p>
        </div>

        <!-- Global Premium Toast Notification -->
        @if(session('success') || session('error') || $errors->any())
            @php
                $isError = session('error') || $errors->any();
                $toastMessage = session('success') ?? (session('error') ?? $errors->first());
            @endphp
            <div x-data="{ 
                    show: true, 
                    type: '{{ $isError ? 'error' : 'success' }}',
                    message: '{{ $toastMessage }}',
                    init() {
                        setTimeout(() => this.show = false, 5000);
                    }
                 }"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-90"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-90"
                 class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[60] flex flex-col items-center justify-center gap-3 w-full max-w-sm p-8 bg-white dark:bg-[#1e293b] rounded-3xl shadow-2xl shadow-black/20 border border-gray-100 dark:border-white/10 text-center"
                 style="display: none;">
                 
                <!-- Icon -->
                <div class="flex-shrink-0 w-16 h-16 rounded-2xl flex items-center justify-center shadow-inner mb-2"
                     :class="type === 'success' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400' : 'bg-red-50 text-red-600 dark:bg-red-500/20 dark:text-red-400'">
                     @if(session('success'))
                         <i data-lucide="check-circle" class="w-8 h-8"></i>
                     @else
                         <i data-lucide="alert-circle" class="w-8 h-8"></i>
                     @endif
                </div>
                
                <!-- Message -->
                <div class="w-full">
                    <p class="text-lg font-bold text-gray-900 dark:text-white" x-text="type === 'success' ? 'Berhasil!' : 'Terjadi Kesalahan'"></p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" x-text="message"></p>
                </div>
                
                <!-- Close Button -->
                <button @click="show = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none rounded-lg p-1 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
                
                <!-- Progress Bar -->
                <div class="absolute bottom-0 left-0 w-full h-1.5 bg-gray-100 dark:bg-gray-800 rounded-b-3xl overflow-hidden">
                    <div class="h-full animate-progress"
                         :class="type === 'error' ? 'bg-red-500' : 'bg-emerald-500'"
                         style="animation: progress 3s linear forwards;"></div>
                </div>
            </div>
            
            <style>
                @keyframes progress {
                    from { width: 100%; }
                    to { width: 0%; }
                }
            </style>
        @endif


        <script>
            lucide.createIcons();
            
            // Re-render icons after dynamic content changes if needed
            document.addEventListener('DOMContentLoaded', () => {
                lucide.createIcons();
            });
        </script>
        @stack('scripts')
    </body>
</html>
