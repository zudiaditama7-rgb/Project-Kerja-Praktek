<x-app-layout>
    <x-slot name="header">Manajemen Data Siswa</x-slot>

    <div x-data="{ 
        showKenaikan: false, 
        showImport: false, 
        loading: false,
        dariKelas: '',
        siswaList: [],
        loadingSiswa: false,
        async fetchSiswa() {
            if(!this.dariKelas) {
                this.siswaList = [];
                return;
            }
            this.loadingSiswa = true;
            try {
                const res = await fetch('{{ route('admin.siswa.get_by_kelas', ':kelas') }}'.replace(':kelas', this.dariKelas));
                this.siswaList = await res.json();
            } catch(e) {
                console.error(e);
            }
            this.loadingSiswa = false;
        },
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
            <div class="flex items-center gap-4 w-full md:w-auto">
                <form action="{{ route('admin.siswa.index') }}" method="GET" @submit="loading = true" class="relative w-full md:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa..." class="w-full pl-10 pr-4 py-2 rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <i data-lucide="search" class="absolute left-3 top-2.5 w-4 h-4 text-gray-400"></i>
                </form>
                
                <form action="{{ route('admin.siswa.index') }}" method="GET" @change="$el.submit(); loading = true" class="hidden md:block">
                    <select name="kelas" class="py-2 rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">Semua Kelas</option>
                        @foreach($globalKelasList as $kls)
                            <option value="{{ $kls->nama_kelas }}" {{ request('kelas') == $kls->nama_kelas ? 'selected' : '' }}>Kelas {{ $kls->nama_kelas }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto">
                <div class="flex gap-2">
                    <a href="{{ route('admin.siswa.export.excel', ['kelas' => request('kelas')]) }}" class="p-2 bg-green-50 text-green-600 rounded-xl hover:bg-green-100 transition" title="Export Excel">
                        <i data-lucide="file-spreadsheet" class="w-5 h-5"></i>
                    </a>
                    <a href="{{ route('admin.siswa.export.pdf', ['kelas' => request('kelas')]) }}" class="p-2 bg-red-50 text-red-600 rounded-xl hover:bg-red-100 transition" title="Export PDF">
                        <i data-lucide="file-type-2" class="w-5 h-5"></i>
                    </a>
                    <button @click="showImport = true" class="p-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 transition" title="Import Data">
                        <i data-lucide="upload" class="w-5 h-5"></i>
                    </button>
                </div>
                <button @click="showKenaikan = true" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-700 rounded-xl font-bold text-sm hover:bg-indigo-100 transition">
                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                    Kenaikan Kelas
                </button>
                <a href="{{ route('admin.siswa.create') }}" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-4 py-2 bg-[#065F46] text-white rounded-xl font-bold text-sm hover:bg-emerald-800 shadow-lg shadow-emerald-100 transition">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Tambah Siswa
                </a>
            </div>
        </div>

        <div class="overflow-x-auto relative">
            <!-- Skeleton Loader -->
            <div x-show="loading" x-cloak class="absolute inset-0 z-10 bg-white/80 backdrop-blur-[1px] p-6 space-y-4">
                @for($i=1; $i<=5; $i++)
                    <div class="flex items-center gap-6">
                        <div class="skeleton h-10 w-1/3"></div>
                        <div class="skeleton h-10 w-1/4"></div>
                        <div class="skeleton h-10 w-1/4"></div>
                        <div class="skeleton h-10 w-16 ml-auto"></div>
                    </div>
                @endfor
            </div>

            <table class="w-full text-left border-separate border-spacing-0" :class="{ 'opacity-20 transition-opacity duration-500': loading }">
                <thead>
                    <tr class="bg-[#065F46] text-white text-xs uppercase tracking-wider font-bold border-b-0 [&>th:first-child]:rounded-tl-2xl [&>th:last-child]:rounded-tr-2xl">
                        <th class="px-6 py-5 text-center w-16">No</th>
                        <th class="px-6 py-5">Nama Siswa</th>
                        <th class="px-6 py-5">Kelas</th>
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
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-bold">
                                    Kelas {{ $s->kelas }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 font-medium">
                                {{ $s->tahun_ajaran }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'aktif' => 'bg-green-100 text-green-700',
                                        'lulus' => 'bg-indigo-100 text-indigo-700',
                                        'pindah' => 'bg-red-100 text-red-700',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase {{ $statusColors[$s->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $s->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button @click="fetchRiwayat({{ $s->id }}, '{{ $s->nama_siswa }}')" class="p-2 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-950/20 rounded-lg transition" title="Lihat Riwayat">
                                        <i data-lucide="history" class="w-4 h-4"></i>
                                    </button>
                                    <a href="{{ route('admin.siswa.card', $s->id) }}" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="Cetak Kartu">
                                        <i data-lucide="contact-2" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('admin.siswa.edit', $s->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('admin.siswa.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus data siswa ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16">
                                <div class="flex flex-col items-center justify-center text-center">
                                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800/40 rounded-2xl flex items-center justify-center mb-4 text-slate-300 dark:text-slate-600">
                                        <i data-lucide="users" class="w-8 h-8"></i>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Data Siswa Tidak Ditemukan</h4>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 max-w-xs">Belum ada data siswa terdaftar untuk filter ini atau kata pencarian Anda tidak cocok.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 bg-gray-50/50">
            {{ $siswas->links() }}
        </div>
    
    <!-- Kenaikan Kelas Modal -->
    <div x-show="showKenaikan" x-cloak class="fixed inset-0 z-[60] overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50 transition-opacity" @click="showKenaikan = false"></div>
            <div class="relative bg-white rounded-3xl max-w-2xl w-full p-8 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Kenaikan Kelas</h3>
                    <button @click="showKenaikan = false" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
                
                <form action="{{ route('admin.kenaikan_kelas') }}" method="POST" class="space-y-6 overflow-y-auto pr-2">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Dari Kelas</label>
                            <select name="dari_kelas" x-model="dariKelas" @change="fetchSiswa()" required class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Pilih Kelas Asal</option>
                                @foreach($globalKelasList as $kls)
                                    <option value="{{ $kls->nama_kelas }}">Kelas {{ $kls->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Ke Kelas</label>
                            <select name="ke_kelas" required class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                                @foreach($globalKelasList as $kls)
                                    <option value="{{ $kls->nama_kelas }}">Kelas {{ $kls->nama_kelas }}</option>
                                @endforeach
                                <option value="lulus">LULUS</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="text-sm font-bold text-gray-700">Pilih Siswa yang Naik Kelas</label>
                            <div class="flex gap-2">
                                <button type="button" @click="siswaList.forEach(s => $refs['siswa_'+s.id].checked = true)" class="text-[10px] font-bold text-blue-600 uppercase">Pilih Semua</button>
                                <span class="text-gray-300">|</span>
                                <button type="button" @click="siswaList.forEach(s => $refs['siswa_'+s.id].checked = false)" class="text-[10px] font-bold text-gray-400 uppercase">Hapus Semua</button>
                            </div>
                        </div>
                        
                        <div class="border border-gray-100 rounded-2xl max-h-60 overflow-y-auto bg-gray-50/30 p-4">
                            <template x-if="loadingSiswa">
                                <div class="py-10 text-center">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                                    <p class="mt-2 text-xs text-gray-500 font-medium">Memuat data siswa...</p>
                                </div>
                            </template>
                            
                            <template x-if="!loadingSiswa && siswaList.length === 0">
                                <div class="py-10 text-center text-gray-400 text-xs italic">
                                    <i data-lucide="users" class="w-8 h-8 mx-auto mb-2 opacity-20"></i>
                                    Pilih kelas asal untuk melihat daftar siswa.
                                </div>
                            </template>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                <template x-for="siswa in siswaList" :key="siswa.id">
                                    <label class="flex items-center gap-3 p-3 bg-white rounded-xl border border-gray-50 hover:border-blue-200 transition cursor-pointer group">
                                        <input type="checkbox" name="student_ids[]" :value="siswa.id" checked :x-ref="'siswa_'+siswa.id" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <div>
                                            <p class="text-sm font-bold text-gray-800 group-hover:text-blue-700 transition" x-text="siswa.nama_siswa"></p>
                                            <p class="text-[10px] text-gray-400 font-medium" x-text="'NIS: ' + (siswa.nis || '-')"></p>
                                        </div>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100/80 space-y-3">
                        <div class="flex items-center gap-2 text-slate-800">
                            <i data-lucide="help-circle" class="w-4 h-4 text-emerald-600"></i>
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Alur Proses Kenaikan Kelas</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-[11px] text-slate-600 leading-relaxed">
                            <div class="bg-white p-3 rounded-xl border border-slate-100 flex items-start gap-2">
                                <i data-lucide="check-square" class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5"></i>
                                <span>Pilih (centang) siswa yang <strong>berhak naik</strong> ke kelas tujuan.</span>
                            </div>
                            <div class="bg-white p-3 rounded-xl border border-slate-100 flex items-start gap-2">
                                <i data-lucide="minus-square" class="w-4 h-4 text-rose-400 shrink-0 mt-0.5"></i>
                                <span>Siswa tanpa centang akan <strong>tetap tinggal</strong> di kelas saat ini.</span>
                            </div>
                            <div class="bg-white p-3 rounded-xl border border-slate-100 flex items-start gap-2">
                                <i data-lucide="calendar" class="w-4 h-4 text-blue-500 shrink-0 mt-0.5"></i>
                                <span>Tahun ajaran disesuaikan otomatis dengan <strong>periode aktif</strong>.</span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" :disabled="loadingSiswa || siswaList.length === 0" class="w-full py-4 bg-[#065F46] disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-2xl font-bold hover:bg-emerald-800 shadow-xl shadow-emerald-100 transition">
                        Proses Kenaikan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div x-show="showImport" x-cloak class="fixed inset-0 z-[60] overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50 transition-opacity" @click="showImport = false"></div>
            <div class="relative bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Import Data Siswa</h3>
                    <button @click="showImport = false" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
                
                <form action="{{ route('admin.siswa.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div class="border-2 border-dashed border-gray-200 rounded-2xl p-8 text-center hover:border-blue-500 transition-colors">
                        <i data-lucide="upload-cloud" class="w-12 h-12 text-gray-300 mx-auto mb-4"></i>
                        <label class="cursor-pointer block">
                            <span class="bg-blue-50 text-blue-600 px-4 py-2 rounded-lg font-bold text-sm">Pilih File Excel/CSV</span>
                            <input type="file" name="file" class="hidden" onchange="document.getElementById('fileNameDisplay').innerText = this.files[0].name">
                        </label>
                        <p id="fileNameDisplay" class="mt-4 text-xs text-gray-500 font-medium italic">Belum ada file dipilih</p>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-2xl">
                        <p class="text-[10px] text-blue-700 font-bold uppercase tracking-widest mb-1">Format Header Kolom:</p>
                        <p class="text-xs text-blue-900 font-medium mb-2">nama_siswa, nis, kelas, tahun_ajaran, status</p>
                    </div>

                    <button type="submit" class="w-full py-4 bg-[#065F46] text-white rounded-2xl font-bold hover:bg-emerald-800 shadow-xl shadow-emerald-100 transition">
                        Mulai Import
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Riwayat Siswa Modal -->
    <template x-teleport="body">
        <div x-show="showRiwayat" x-cloak class="fixed inset-0 z-[100] overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-[2px] transition-opacity" @click="showRiwayat = false"></div>
            <div class="relative bg-white dark:bg-[#0f172a] rounded-3xl max-w-md w-full p-8 shadow-2xl overflow-hidden flex flex-col max-h-[85vh]">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Riwayat Akademik</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-1" x-text="riwayatSiswaName"></p>
                    </div>
                    <button @click="showRiwayat = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="overflow-y-auto pr-2 flex-1 space-y-6">
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
