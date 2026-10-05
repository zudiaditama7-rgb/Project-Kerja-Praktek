<x-app-layout>
    <x-slot name="header">Laporan Seluruh Presensi</x-slot>

    <div x-data="{
        tanggalMulai: '{{ request('tanggal_mulai') }}',
        tanggalSelesai: '{{ request('tanggal_selesai') }}',
        setSemester(e) {
            const val = e.target.value;
            if (!val) return;
            const [mulai, selesai] = val.split('|');
            this.tanggalMulai = mulai;
            this.tanggalSelesai = selesai;
        }
    }" class="bg-white rounded-3xl shadow-sm border border-gray-50 overflow-hidden mb-8">
        <form action="{{ route('kepsek.laporan') }}" method="GET" class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Pilih Semester</label>
                    <select @change="setSemester" class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">-- Pilih Semester --</option>
                        @foreach($tahunAjarans as $ta)
                            @if($ta->tanggal_mulai_gasal && $ta->tanggal_selesai_gasal)
                                @php
                                    $gasalMulai = \Carbon\Carbon::parse($ta->tanggal_mulai_gasal)->format('Y-m-d');
                                    $gasalSelesai = \Carbon\Carbon::parse($ta->tanggal_selesai_gasal)->format('Y-m-d');
                                @endphp
                                <option value="{{ $gasalMulai }}|{{ $gasalSelesai }}" {{ request('tanggal_mulai') == $gasalMulai && request('tanggal_selesai') == $gasalSelesai ? 'selected' : '' }}>
                                    {{ $ta->nama_tahun }} - Gasal
                                </option>
                            @endif
                            @if($ta->tanggal_mulai_genap && $ta->tanggal_selesai_genap)
                                @php
                                    $genapMulai = \Carbon\Carbon::parse($ta->tanggal_mulai_genap)->format('Y-m-d');
                                    $genapSelesai = \Carbon\Carbon::parse($ta->tanggal_selesai_genap)->format('Y-m-d');
                                @endphp
                                <option value="{{ $genapMulai }}|{{ $genapSelesai }}" {{ request('tanggal_mulai') == $genapMulai && request('tanggal_selesai') == $genapSelesai ? 'selected' : '' }}>
                                    {{ $ta->nama_tahun }} - Genap
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" x-model="tanggalMulai" class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" x-model="tanggalSelesai" class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Kelas</label>
                    <select name="kelas" class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">Semua Kelas</option>
                        @foreach($globalKelasList as $kls)
                            <option value="{{ $kls->nama_kelas }}" {{ request('kelas') == $kls->nama_kelas ? 'selected' : '' }}>Kelas {{ $kls->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="px-5 py-2.5 bg-[#065F46] text-white rounded-xl font-bold text-sm hover:bg-emerald-800 transition inline-flex items-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    Filter
                </button>
                <a href="{{ route('kepsek.laporan') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition inline-flex items-center gap-2">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    Reset
                </a>
                <a href="{{ route('kepsek.laporan.export', request()->all()) }}" class="px-4 py-2.5 bg-emerald-100 text-emerald-700 rounded-xl font-bold text-sm hover:bg-emerald-200 transition inline-flex items-center gap-2">
                    <i data-lucide="sheet" class="w-4 h-4"></i>
                    Export Excel
                </a>
            </div>
        </form>
    </div>

    @if(isset($summary))
    <div class="mb-8 grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 text-center">
            <h4 class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-2">Total Presensi</h4>
            <div class="text-3xl font-black text-gray-800">{{ $summary['total'] }}</div>
        </div>
        <div class="bg-emerald-50 p-4 rounded-2xl shadow-sm border border-emerald-100 text-center">
            <h4 class="text-emerald-600 text-xs font-bold uppercase tracking-wider mb-2">Hadir</h4>
            <div class="text-3xl font-black text-emerald-700">{{ $summary['hadir'] }}</div>
        </div>
        <div class="bg-blue-50 p-4 rounded-2xl shadow-sm border border-blue-100 text-center">
            <h4 class="text-blue-600 text-xs font-bold uppercase tracking-wider mb-2">Sakit</h4>
            <div class="text-3xl font-black text-blue-700">{{ $summary['sakit'] }}</div>
        </div>
        <div class="bg-yellow-50 p-4 rounded-2xl shadow-sm border border-yellow-100 text-center">
            <h4 class="text-yellow-600 text-xs font-bold uppercase tracking-wider mb-2">Izin</h4>
            <div class="text-3xl font-black text-yellow-700">{{ $summary['izin'] }}</div>
        </div>
        <div class="bg-red-50 p-4 rounded-2xl shadow-sm border border-red-100 text-center">
            <h4 class="text-red-600 text-xs font-bold uppercase tracking-wider mb-2">Alpha</h4>
            <div class="text-3xl font-black text-red-700">{{ $summary['alpha'] }}</div>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-3xl shadow-sm border border-gray-50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-0">
                <thead>
                    <tr class="bg-[#065F46] text-white text-xs uppercase tracking-wider font-bold border-b-0 [&>th:first-child]:rounded-tl-2xl [&>th:last-child]:rounded-tr-2xl">
                        <th class="px-6 py-5 text-center w-16">No</th>
                        <th class="px-6 py-5">Tanggal</th>
                        <th class="px-6 py-5">Siswa</th>
                        <th class="px-6 py-5 text-center">Status</th>
                        <th class="px-6 py-5">Wali Kelas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($laporans as $l)
                        <tr class="hover:bg-emerald-50/50 transition">
                            <td class="px-6 py-4 text-center align-middle font-bold text-gray-400 text-sm">
                                {{ $laporans->firstItem() + $loop->index }}
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-800">
                                {{ \Carbon\Carbon::parse($l->tanggal)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-800 text-sm">{{ $l->siswa->nama_siswa }}</p>
                                <p class="text-xs text-gray-400">Kelas {{ $l->siswa->kelas }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $badges = [
                                        'hadir' => 'bg-green-100 text-green-700',
                                        'izin' => 'bg-yellow-100 text-yellow-700',
                                        'sakit' => 'bg-blue-100 text-blue-700',
                                        'alpha' => 'bg-red-100 text-red-700',
                                    ];
                                @endphp
                                <span class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest {{ $badges[$l->status] }}">
                                    {{ $l->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 font-medium">
                                {{ $l->user->name }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20">
                                <div class="flex flex-col items-center justify-center text-center">
                                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800/40 rounded-2xl flex items-center justify-center mb-4 text-slate-300 dark:text-slate-600">
                                        <i data-lucide="folder-x" class="w-8 h-8"></i>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Laporan Tidak Ditemukan</h4>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 max-w-xs">Belum ada data rekapitulasi presensi yang sesuai dengan kriteria penyaringan filter Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($laporans->hasPages())
        <div class="px-8 py-5 border-t border-gray-100 bg-gray-50/50">
            {{ $laporans->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
