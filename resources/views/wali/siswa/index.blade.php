<x-app-layout>
    <x-slot name="header">Data Siswa Kelas {{ auth()->user()->kelas }}</x-slot>

    <div x-data="{
        showRiwayat: false,
        riwayatSiswaName: '',
        riwayatList: [],
        loadingRiwayat: false,
        async fetchRiwayat(siswaId, name) {
            this.riwayatSiswaName = name;
            this.showRiwayat = true;
            this.loadingRiwayat = true;
            try {
                const res = await fetch('{{ route('siswa.riwayat', ':id') }}'.replace(':id', siswaId));
                const data = await res.json();
                this.riwayatList = data.riwayat;
            } catch(e) {
                console.error(e);
            }
            this.loadingRiwayat = false;
        }
    }" class="bg-white rounded-2xl shadow-sm border border-gray-50 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <form action="{{ route('wali.siswa.index') }}" method="GET" class="relative w-full md:w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa..." class="w-full pl-10 pr-4 py-2 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                <i data-lucide="search" class="absolute left-3 top-2.5 w-4 h-4 text-gray-400"></i>
            </form>
            
            <div class="flex items-center gap-2 text-sm font-medium text-gray-500">
                <i data-lucide="info" class="w-4 h-4 text-blue-500"></i>
                Klik ikon kartu untuk melihat QR Code & Cetak Kartu
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-0">
                <thead>
                    <tr class="bg-[#065F46] text-white text-xs uppercase tracking-wider font-bold border-b-0 [&>th:first-child]:rounded-tl-2xl [&>th:last-child]:rounded-tr-2xl">
                        <th class="px-6 py-5 text-center w-16">No</th>
                        <th class="px-6 py-5">Nama Siswa</th>
                        <th class="px-6 py-5">Tahun Ajaran</th>
                        <th class="px-6 py-5">Status</th>
                        <th class="px-6 py-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($siswas as $s)
                        <tr class="hover:bg-emerald-50/50 transition">
                            <td class="px-6 py-4 text-center align-middle font-bold text-gray-400 text-sm">
                                {{ $loop->iteration + ($siswas->currentPage() - 1) * $siswas->perPage() }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-900 text-sm">{{ $s->nama_siswa }}</span>
                                    <span class="text-xs text-gray-400 mt-0.5">NIS: {{ $s->nis ?: '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 font-medium">
                                {{ $s->tahun_ajaran }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase {{ $s->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $s->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button @click="fetchRiwayat({{ $s->id }}, '{{ $s->nama_siswa }}')" class="inline-flex items-center gap-2 px-3 py-2 bg-indigo-50 text-indigo-700 dark:hover:bg-indigo-950/20 rounded-xl font-bold text-xs hover:bg-indigo-100 transition shadow-sm border border-indigo-100">
                                        <i data-lucide="history" class="w-4 h-4"></i>
                                        Riwayat
                                    </button>
                                    <a href="{{ route('wali.siswa.card', $s->id) }}" class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-50 text-emerald-700 rounded-xl font-bold text-xs hover:bg-emerald-100 transition shadow-sm border border-emerald-100">
                                        <i data-lucide="contact-2" class="w-4 h-4"></i>
                                        Cetak Kartu
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm italic">Data siswa tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 bg-gray-50/50">
            {{ $siswas->links() }}
        </div>
    <!-- Riwayat Siswa Modal -->
    <template x-teleport="body">
        <div x-show="showRiwayat" x-cloak class="fixed inset-0 z-[100] overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-[2px] transition-opacity" @click="showRiwayat = false"></div>
            <div class="relative bg-white dark:bg-[#0f172a] rounded-3xl max-w-md w-full shadow-2xl flex flex-col max-h-[85vh] text-left overflow-hidden">
                <div class="flex justify-between items-center p-6 md:p-8 border-b border-gray-100 dark:border-gray-800 shrink-0">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Riwayat Akademik</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-1" x-text="riwayatSiswaName"></p>
                    </div>
                    <button @click="showRiwayat = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition bg-gray-50 dark:bg-gray-800 p-2 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="p-6 md:p-8 overflow-y-auto flex-1 space-y-6 custom-scrollbar">
                    <template x-if="loadingRiwayat">
                        <div class="py-16 text-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
                            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400 font-medium">Memuat data riwayat...</p>
                        </div>
                    </template>
                    
                    <template x-if="!loadingRiwayat && riwayatList.length === 0">
                        <div class="py-16 text-center text-gray-400 dark:text-gray-500 text-xs italic">
                            <svg class="w-10 h-10 mx-auto mb-3 opacity-20 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Belum ada riwayat akademik tercatat untuk siswa ini.
                        </div>
                    </template>

                    <template x-if="!loadingRiwayat && riwayatList.length > 0">
                        <div class="relative border-l-2 border-gray-200 dark:border-gray-700 ml-3 space-y-6 pb-6 pt-2">
                            <template x-for="item in riwayatList" :key="item.id">
                                <div class="relative pl-6">
                                    <!-- Timeline Node Circle -->
                                    <div class="absolute -left-[9px] top-5 w-4 h-4 rounded-full border-2 border-white dark:border-[#0f172a] shadow-sm transition-all"
                                         :class="{ 'bg-emerald-500': item.status === 'Baru' || item.status === 'Naik Kelas', 'bg-rose-500': item.status === 'Tidak Naik Kelas', 'bg-indigo-500': item.status === 'Lulus', 'bg-amber-500': item.status === 'Pindah', 'bg-slate-400': item.status === 'Penyesuaian Manual' }">
                                    </div>
                                    
                                    <!-- Content -->
                                    <div class="bg-gray-50/50 dark:bg-gray-800/20 hover:bg-gray-50 dark:hover:bg-gray-800/40 p-4 rounded-2xl border border-gray-100 dark:border-gray-800 transition">
                                        <div class="flex justify-between items-start gap-2 mb-2">
                                            <span class="px-2.5 py-0.5 rounded-lg text-[9px] font-extrabold uppercase tracking-wide border"
                                                  :class="{ 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20': item.status === 'Baru' || item.status === 'Naik Kelas', 'bg-rose-50 text-rose-700 border-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20': item.status === 'Tidak Naik Kelas', 'bg-indigo-50 text-indigo-700 border-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/20': item.status === 'Lulus', 'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20': item.status === 'Pindah', 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-500/15 dark:text-slate-300 dark:border-slate-500/20': item.status === 'Penyesuaian Manual' }"
                                                  x-text="item.status">
                                            </span>
                                            <span class="text-[10px] text-gray-400 dark:text-gray-500 font-bold" x-text="new Date(item.created_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'})"></span>
                                        </div>
                                        
                                        <p class="text-xs text-gray-700 dark:text-gray-300 font-bold mb-1">
                                            Tahun Ajaran: <span class="text-indigo-600 dark:text-indigo-400" x-text="item.tahun_ajaran"></span>
                                        </p>
                                        
                                        <div class="flex items-center gap-2 text-[11px] text-gray-500 dark:text-gray-400 font-medium mt-2">
                                            <span class="px-2 py-0.5 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded text-gray-600 dark:text-gray-300" x-text="item.kelas_asal ? 'Kelas ' + item.kelas_asal : 'Mulai'"></span>
                                            <svg class="w-3.5 h-3.5 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                            <span class="px-2 py-0.5 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded text-gray-800 dark:text-gray-200 font-bold" x-text="item.kelas_tujuan === 'Lulus' ? 'Lulus' : 'Kelas ' + item.kelas_tujuan"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        </div>
    </template>
</x-app-layout>
