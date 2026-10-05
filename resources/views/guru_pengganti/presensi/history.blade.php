<x-app-layout>
    <x-slot name="header">Riwayat Presensi</x-slot>

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
        <div class="p-8 border-b border-gray-100">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Riwayat Presensi</h1>
            <p class="text-gray-500 text-sm">Cari dan saring data riwayat pencatatan presensi yang telah Anda inputkan.</p>
        </div>
        <form action="{{ route('guru_pengganti.presensi.history') }}" method="GET" class="p-8 grid grid-cols-1 lg:grid-cols-5 gap-4 items-end bg-gray-50/30">
            
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Pilih Semester</label>
                <select @change="setSemester" class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
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
                <input type="date" name="tanggal_mulai" x-model="tanggalMulai" class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" x-model="tanggalSelesai" class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Status Kehadiran</label>
                <select name="status" class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    <option value="">Semua Status</option>
                    <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                    <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="alpha" {{ request('status') == 'alpha' ? 'selected' : '' }}>Alfa</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="w-full py-3 bg-[#065F46] text-white rounded-xl font-bold text-sm hover:bg-emerald-800 transition flex items-center justify-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    Filter
                </button>
                <a href="{{ route('guru_pengganti.presensi.history') }}" class="py-3 px-4 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition flex items-center justify-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-0">
                <thead>
                    <tr class="bg-[#065F46] text-white text-xs uppercase tracking-wider font-bold border-b-0 [&>th:first-child]:rounded-tl-2xl [&>th:last-child]:rounded-tr-2xl">
                        <th class="px-6 py-5 text-center w-16">No</th>
                        <th class="px-6 py-5">Siswa</th>
                        <th class="px-6 py-5">Kelas</th>
                        <th class="px-6 py-5">Tanggal</th>
                        <th class="px-6 py-5 text-center">Status</th>
                        <th class="px-6 py-5 text-center">Dokumen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($presensis as $presensi)
                        <tr class="hover:bg-emerald-50/50 transition">
                            <td class="px-6 py-4 text-center align-middle font-bold text-gray-400 text-sm">
                                {{ $loop->iteration + ($presensis->currentPage() - 1) * $presensis->perPage() }}
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-800 text-sm">{{ $presensi->siswa->nama_siswa }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-bold">
                                    Kelas {{ $presensi->siswa->kelas }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-800">
                                {{ \Carbon\Carbon::parse($presensi->tanggal)->format('d/m/Y') }}
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
                                <span class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest {{ $badges[$presensi->status] }}">
                                    {{ $presensi->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($presensi->document_path)
                                    <a href="{{ route('document.view', $presensi->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-50 text-emerald-600 rounded-lg text-xs font-bold hover:bg-emerald-100 transition">
                                        <i data-lucide="file-text" class="w-4 h-4"></i>
                                        Lihat
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400 font-medium">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-20">
                                <div class="flex flex-col items-center justify-center text-center">
                                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800/40 rounded-2xl flex items-center justify-center mb-4 text-slate-300 dark:text-slate-600">
                                        <i data-lucide="inbox" class="w-8 h-8"></i>
                                    </div>
                                    @if(isset($isSearched) && !$isSearched)
                                        <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Silakan Lakukan Pencarian</h4>
                                        <p class="text-xs text-slate-400 dark:text-slate-500 max-w-xs">Gunakan filter pencarian di atas untuk menampilkan data riwayat presensi yang Anda inginkan.</p>
                                    @else
                                        <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Tidak Ada Riwayat</h4>
                                        <p class="text-xs text-slate-400 dark:text-slate-500 max-w-xs">Tidak ditemukan riwayat pencatatan kehadiran dengan kriteria tersebut.</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-8 py-4 bg-gray-50/50">
            {{ $presensis->links() }}
        </div>
    </div>
</x-app-layout>
