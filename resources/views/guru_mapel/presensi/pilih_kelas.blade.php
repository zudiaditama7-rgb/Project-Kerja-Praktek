<x-app-layout>
    <x-slot name="header">Input Presensi Harian</x-slot>

    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-3xl font-black text-gray-900 tracking-tight">Pilih Kelas Presensi</h2>
            <p class="text-gray-500 mt-2 font-medium">Silakan pilih kelas yang akan Anda presensi pada jam pelajaran pertama hari ini.</p>
        </div>
        <div class="bg-emerald-50 px-4 py-2 rounded-xl border border-emerald-100 flex items-center gap-3 shadow-sm">
            <div class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center text-white">
                <i data-lucide="calendar" class="w-4 h-4"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-emerald-600 uppercase tracking-widest">Hari Ini</p>
                <p class="text-sm font-bold text-gray-900">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Classes Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($kelases as $kelas)
            <a href="{{ route('guru_mapel.presensi.index', $kelas->id) }}" class="group relative bg-white rounded-3xl p-6 shadow-md hover:shadow-2xl hover:-translate-y-2 border border-gray-100 hover:border-emerald-300 transition-all duration-300 overflow-hidden block">
                
                <!-- Background decorative shape -->
                <div class="absolute -right-8 -top-8 w-32 h-32 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-full blur-2xl opacity-50 group-hover:opacity-100 transition-opacity"></div>

                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white shadow-lg shadow-emerald-200 group-hover:scale-110 transition-transform duration-300">
                            <i data-lucide="school" class="w-7 h-7"></i>
                        </div>
                        <div class="px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-[10px] font-black uppercase tracking-widest border border-gray-200">
                            Tersedia
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-2xl font-black text-gray-900 mb-1 group-hover:text-emerald-700 transition-colors">Kelas {{ $kelas->nama_kelas }}</h3>
                        <p class="text-sm text-gray-500 font-medium">Mulai Input Presensi</p>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-emerald-600 text-sm font-bold flex items-center gap-2 group-hover:translate-x-2 transition-transform">
                            Pilih Kelas Ini
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </span>
                        <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                            <i data-lucide="check" class="w-4 h-4"></i>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-16 bg-white rounded-3xl border border-gray-100 shadow-sm">
                <div class="w-20 h-20 bg-gray-50 text-gray-300 rounded-3xl flex items-center justify-center mx-auto mb-4 rotate-3">
                    <i data-lucide="folder-search" class="w-10 h-10"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Belum Ada Kelas</h3>
                <p class="text-gray-500 mt-2">Sistem belum mendata kelas yang aktif untuk periode ini.</p>
            </div>
        @endforelse
    </div>
</x-app-layout>
