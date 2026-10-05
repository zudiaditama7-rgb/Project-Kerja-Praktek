<x-app-layout>
    <x-slot name="header">Riwayat & Track Record Akademik Siswa</x-slot>

    <div class="space-y-6">
        <!-- Filter Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
                <i data-lucide="filter" class="w-4 h-4 text-emerald-600"></i>
                Pencarian & Filter
            </h3>

            <form action="{{ route('admin.riwayat.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                <!-- Search Name / NIS -->
                <div class="relative col-span-1 md:col-span-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa atau NIS..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <div class="absolute left-3 top-3">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                    </div>
                </div>

                <!-- Kelas -->
                <div>
                    <select name="kelas" class="w-full py-2.5 rounded-xl border border-gray-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Semua Kelas</option>
                        @foreach($globalKelasList as $kls)
                            <option value="{{ $kls->nama_kelas }}" {{ request('kelas') == $kls->nama_kelas ? 'selected' : '' }}>Kelas {{ $kls->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Siswa -->
                <div>
                    <select name="status_siswa" class="w-full py-2.5 rounded-xl border border-gray-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status_siswa') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="lulus" {{ request('status_siswa') === 'lulus' ? 'selected' : '' }}>Lulus</option>
                    </select>
                </div>

                <!-- Tahun Ajaran -->
                <div>
                    <select name="tahun_ajaran" class="w-full py-2.5 rounded-xl border border-gray-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Semua Tahun Ajaran</option>
                        @foreach($tahunAjarans as $ta)
                            <option value="{{ $ta->nama_tahun }}" {{ request('tahun_ajaran') === $ta->nama_tahun ? 'selected' : '' }}>
                                {{ $ta->nama_tahun }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold shadow-md shadow-emerald-900/10 transition flex items-center justify-center gap-1.5">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        Cari
                    </button>
                    @if(request()->anyFilled(['search', 'kelas', 'status_siswa', 'tahun_ajaran']))
                        <a href="{{ route('admin.riwayat.index') }}" class="py-2.5 px-3 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-sm font-bold transition flex items-center justify-center" title="Reset Filter">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Info Summary -->
        <div class="flex items-center gap-3 px-1">
            <span class="text-sm text-gray-500 font-medium">
                Menampilkan <span class="font-bold text-gray-800">{{ $siswas->total() }}</span> siswa dengan riwayat akademik
            </span>
        </div>

        <!-- Student List with Expandable Timeline -->
        <div class="space-y-3">
            @forelse($siswas as $siswa)
                <div x-data="{ open: false }" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-300" :class="open ? 'ring-2 ring-emerald-100' : ''">
                    <!-- Student Summary Row -->
                    <button @click="open = !open" class="w-full px-6 py-4 flex items-center gap-4 hover:bg-emerald-50/50 transition text-left cursor-pointer">
                        <!-- Number -->
                        <span class="text-sm font-bold text-gray-300 w-8 text-center shrink-0">
                            {{ $loop->iteration + ($siswas->currentPage() - 1) * $siswas->perPage() }}
                        </span>

                        <!-- Avatar -->
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-sm shrink-0 {{ $siswa->status === 'lulus' ? 'bg-gradient-to-br from-indigo-500 to-indigo-600' : 'bg-gradient-to-br from-emerald-500 to-emerald-600' }}">
                            {{ strtoupper(substr($siswa->nama_siswa, 0, 1)) }}
                        </div>

                        <!-- Student Info -->
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-800 text-sm truncate">{{ $siswa->nama_siswa }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">NIS: {{ $siswa->nis ?? '-' }}</p>
                        </div>

                        <!-- Current Class -->
                        <div class="hidden sm:flex items-center gap-2">
                            <span class="px-3 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-lg text-xs font-bold">
                                Kelas {{ $siswa->kelas }}
                            </span>
                        </div>

                        <!-- Status Badge -->
                        <div class="hidden sm:block">
                            @if($siswa->status === 'lulus')
                                <span class="px-3 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-full text-[10px] font-extrabold uppercase">Lulus</span>
                            @else
                                <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full text-[10px] font-extrabold uppercase">Aktif</span>
                            @endif
                        </div>

                        <!-- Timeline Count -->
                        <div class="text-center shrink-0">
                            <span class="text-lg font-black text-gray-700">{{ $siswa->riwayatSiswa->count() }}</span>
                            <p class="text-[10px] text-gray-400 font-medium uppercase">Riwayat</p>
                        </div>

                        <!-- Expand Arrow -->
                        <div class="shrink-0 text-gray-400 transition-transform duration-300" :class="open ? 'rotate-180' : ''">
                            <i data-lucide="chevron-down" class="w-5 h-5"></i>
                        </div>
                    </button>

                    <!-- Expandable Journey Map & Timeline -->
                    <div x-show="open" x-collapse x-cloak>
                        <div class="px-6 pb-6 pt-3 border-t border-gray-100 bg-gray-50/40">
                            @php
                                $statusPerKelas = [];
                                // Set basic status based on current class
                                for ($g = 1; $g <= 6; $g++) {
                                    if ($siswa->status === 'lulus') {
                                        if ($g === 6) {
                                            $statusPerKelas[$g] = [
                                                'status' => 'Alumni',
                                                'detail' => 'Lulus Madrasah',
                                                'color' => 'bg-gradient-to-br from-indigo-500 to-indigo-600 text-white border-transparent shadow-sm shadow-indigo-900/10',
                                                'icon' => 'graduation-cap'
                                            ];
                                        } else {
                                            $statusPerKelas[$g] = [
                                                'status' => 'Selesai',
                                                'detail' => 'Sudah masuk madrasah',
                                                'color' => 'bg-emerald-50/30 text-emerald-600 border-emerald-100',
                                                'icon' => 'check'
                                            ];
                                        }
                                    } elseif ($siswa->kelas == $g) {
                                        $statusPerKelas[$g] = [
                                            'status' => 'Sedang Ditempuh',
                                            'detail' => 'T.A ' . $siswa->tahun_ajaran,
                                            'color' => 'bg-amber-50 text-amber-700 border-amber-100 ring-2 ring-amber-400/20',
                                            'icon' => 'play'
                                        ];
                                    } elseif ($siswa->kelas > $g) {
                                        $statusPerKelas[$g] = [
                                            'status' => 'Selesai',
                                            'detail' => 'Sudah masuk madrasah',
                                            'color' => 'bg-emerald-50/30 text-emerald-600 border-emerald-100',
                                            'icon' => 'check'
                                        ];
                                    } else {
                                        $statusPerKelas[$g] = [
                                            'status' => 'Belum Ditempuh',
                                            'detail' => 'Belum sampai jenjang ini',
                                            'color' => 'bg-gray-50 text-gray-400 border-gray-100',
                                            'icon' => 'lock'
                                        ];
                                    }
                                }

                                // Apply history details to override/augment
                                foreach ($siswa->riwayatSiswa->sortBy('created_at') as $riwayat) {
                                    $ta = $riwayat->tahun_ajaran;
                                    
                                    if ($riwayat->kelas_asal) {
                                        $ka = intval($riwayat->kelas_asal);
                                        if ($ka >= 1 && $ka <= 6) {
                                            if ($riwayat->status === 'Naik Kelas') {
                                                $statusPerKelas[$ka] = [
                                                    'status' => 'Naik Kelas',
                                                    'detail' => 'Lulus & Naik Kelas (T.A ' . $ta . ')',
                                                    'color' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                                    'icon' => 'arrow-up-right',
                                                    'has_riwayat' => true
                                                ];
                                            } elseif ($riwayat->status === 'Tidak Naik Kelas') {
                                                $statusPerKelas[$ka] = [
                                                    'status' => 'Tinggal Kelas',
                                                    'detail' => 'Mengulang di Kelas ' . $ka . ' (T.A ' . $ta . ')',
                                                    'color' => 'bg-rose-50 text-rose-700 border-rose-100',
                                                    'icon' => 'alert-triangle',
                                                    'has_riwayat' => true
                                                ];
                                            } elseif ($riwayat->status === 'Lulus') {
                                                $statusPerKelas[$ka] = [
                                                    'status' => 'Alumni',
                                                    'detail' => 'Lulus T.A ' . $ta,
                                                    'color' => 'bg-gradient-to-br from-indigo-500 to-indigo-600 text-white border-transparent shadow-sm shadow-indigo-900/10',
                                                    'icon' => 'graduation-cap',
                                                    'has_riwayat' => true
                                                ];
                                            } elseif ($riwayat->status === 'Penyesuaian Manual') {
                                                $statusPerKelas[$ka] = [
                                                    'status' => 'Disesuaikan',
                                                    'detail' => 'Manual ke Kelas ' . $riwayat->kelas_tujuan . ' (T.A ' . $ta . ')',
                                                    'color' => 'bg-slate-100 text-slate-700 border-slate-200',
                                                    'icon' => 'settings',
                                                    'has_riwayat' => true
                                                ];
                                            }
                                        }
                                    }
                                    
                                    if ($riwayat->status === 'Baru' || $riwayat->status === 'Aktif Kembali (Import)') {
                                        $kt = intval($riwayat->kelas_tujuan);
                                        if ($kt >= 1 && $kt <= 6) {
                                            $statusPerKelas[$kt] = [
                                                'status' => $riwayat->status === 'Baru' ? 'Baru Masuk' : 'Aktif Kembali',
                                                'detail' => 'Mulai di Kelas ' . $kt . ' (T.A ' . $ta . ')',
                                                'color' => 'bg-blue-50 text-blue-700 border-blue-100',
                                                'icon' => 'user-plus',
                                                'has_riwayat' => true
                                            ];
                                        }
                                    }
                                }
                            @endphp

                            <!-- Section Title -->
                            <div class="flex items-center gap-2 mb-4 border-b border-gray-100 pb-2">
                                <i data-lucide="map" class="w-4 h-4 text-emerald-600"></i>
                                <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Jalur Perkembangan Akademik Siswa</h4>
                            </div>

                            <!-- Grade Progress Grid -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
                                @for($g = 1; $g <= 6; $g++)
                                    @php
                                        $info = $statusPerKelas[$g];
                                    @endphp
                                    <div class="p-4 bg-white border border-gray-100 rounded-2xl shadow-sm flex flex-col justify-between min-h-[120px] relative overflow-hidden transition-all hover:shadow-md {{ $g === 6 && $siswa->status === 'lulus' ? 'ring-2 ring-indigo-500 bg-indigo-50' : '' }}">
                                        <div>
                                            <span class="text-xs font-semibold uppercase tracking-wider {{ $g === 6 && $siswa->status === 'lulus' ? 'text-indigo-600' : 'text-gray-400' }}">
                                                Kelas {{ $g }}
                                            </span>
                                            <h5 class="text-sm font-bold mt-1.5 leading-tight {{ $g === 6 && $siswa->status === 'lulus' ? 'text-indigo-900' : 'text-gray-800' }}">
                                                {{ $info['status'] }}
                                            </h5>
                                        </div>

                                        <p class="text-xs mt-2 font-medium {{ $g === 6 && $siswa->status === 'lulus' ? 'text-indigo-700' : 'text-gray-500' }}">
                                            {{ $info['detail'] }}
                                        </p>

                                        <!-- Badge/Icon Overlay -->
                                        <div class="absolute right-3 top-3">
                                            <span class="w-7 h-7 rounded-xl flex items-center justify-center border {{ $info['color'] }} {{ $g === 6 && $siswa->status === 'lulus' ? 'bg-white border-indigo-200 text-indigo-600' : '' }}">
                                                <i data-lucide="{{ $info['icon'] }}" class="w-3.5 h-3.5"></i>
                                            </span>
                                        </div>
                                    </div>
                                @endfor
                            </div>

                            <!-- Raw logs summary toggle -->
                            <div x-data="{ showLogs: false }" class="mt-4">
                                <button @click="showLogs = !showLogs" class="flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800 transition font-bold cursor-pointer bg-slate-50 hover:bg-slate-100 px-4 py-2 rounded-xl border border-slate-100">
                                    <i data-lucide="list" class="w-4 h-4"></i>
                                    <span x-text="showLogs ? 'Sembunyikan Log Aktivitas' : 'Lihat Detail Log Riwayat Transaksi (' + {{ $siswa->riwayatSiswa->count() }} + ')'"></span>
                                </button>

                                <div x-show="showLogs" x-collapse class="mt-4">
                                    <div class="overflow-hidden border border-gray-100 rounded-2xl bg-white shadow-sm">
                                        <table class="w-full text-left border-separate border-spacing-0 text-sm">
                                            <thead>
                                                <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 font-bold text-xs uppercase tracking-wider">
                                                    <th class="px-6 py-4 rounded-tl-2xl">Tahun Ajaran</th>
                                                    <th class="px-6 py-4">Transisi</th>
                                                    <th class="px-6 py-4">Status</th>
                                                    <th class="px-6 py-4 text-right rounded-tr-2xl">Tanggal Perubahan</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-50 text-gray-700 bg-white">
                                                @foreach($siswa->riwayatSiswa as $riwayat)
                                                    <tr class="hover:bg-gray-50/50 transition">
                                                        <td class="px-6 py-4 font-bold text-indigo-900">{{ $riwayat->tahun_ajaran }}</td>
                                                        <td class="px-6 py-4 font-medium">
                                                            @if($riwayat->kelas_asal)
                                                                Kelas {{ $riwayat->kelas_asal }} <i data-lucide="arrow-right" class="w-3.5 h-3.5 inline-block mx-1 text-gray-400"></i> <span class="font-bold {{ $riwayat->kelas_tujuan === 'Lulus' ? 'text-indigo-600' : 'text-gray-900' }}">{{ $riwayat->kelas_tujuan === 'Lulus' ? 'Lulus' : 'Kelas ' . $riwayat->kelas_tujuan }}</span>
                                                            @else
                                                                <span class="text-emerald-600 font-bold">Baru Masuk</span> <i data-lucide="arrow-right" class="w-3.5 h-3.5 inline-block mx-1 text-gray-400"></i> <span class="font-bold text-gray-900">Kelas {{ $riwayat->kelas_tujuan }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wide border {{ $riwayat->status === 'Baru' || $riwayat->status === 'Naik Kelas' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : ($riwayat->status === 'Tidak Naik Kelas' ? 'bg-rose-50 text-rose-700 border-rose-100' : ($riwayat->status === 'Lulus' ? 'bg-indigo-50 text-indigo-700 border-indigo-100' : ($riwayat->status === 'Pindah' ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-slate-100 text-slate-700 border-slate-200'))) }}">
                                                                {{ $riwayat->status }}
                                                            </span>
                                                        </td>
                                                        <td class="px-6 py-4 text-right text-gray-500 font-medium text-xs">
                                                            {{ \Carbon\Carbon::parse($riwayat->created_at)->translatedFormat('d M Y - H:i') }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16">
                    <div class="flex flex-col items-center justify-center text-center">
                        <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4 text-slate-300">
                            <i data-lucide="clock" class="w-8 h-8"></i>
                        </div>
                        <h4 class="text-sm font-bold text-slate-700 mb-1">Riwayat Tidak Ditemukan</h4>
                        <p class="text-xs text-slate-400 max-w-xs">Data track record untuk parameter pencarian ini tidak tersedia.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($siswas->hasPages())
            <div class="px-2">
                {{ $siswas->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
