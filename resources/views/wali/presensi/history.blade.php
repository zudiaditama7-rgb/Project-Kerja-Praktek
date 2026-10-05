<x-app-layout>
    <x-slot name="header">Riwayat Presensi Kelas {{ auth()->user()->kelas }}</x-slot>

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
        <form action="{{ route('wali.presensi.history') }}" method="GET" class="p-8 grid grid-cols-1 lg:grid-cols-5 gap-4 items-end">
            
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
                <a href="{{ route('wali.presensi.history') }}" class="py-3 px-4 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition flex items-center justify-center">
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
                        <th class="px-6 py-5">Tanggal</th>
                        <th class="px-6 py-5">Nama Siswa</th>
                        <th class="px-6 py-5 text-center">Status</th>
                        <th class="px-6 py-5 text-center">Dokumen</th>
                        <th class="px-6 py-5">Dicatat Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($presensis as $p)
                        <tr class="hover:bg-emerald-50/50 transition">
                            <td class="px-6 py-4 text-center align-middle font-bold text-gray-400 text-sm">
                                {{ $loop->iteration + ($presensis->currentPage() - 1) * $presensis->perPage() }}
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-800">
                                {{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-700">
                                {{ $p->siswa->nama_siswa }}
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
                                <span class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest {{ $badges[$p->status] }}">
                                    {{ $p->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($p->document_path)
                                    <a href="{{ route('document.view', $p->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-50 text-emerald-600 rounded-lg text-xs font-bold hover:bg-emerald-100 transition">
                                        <i data-lucide="file-text" class="w-4 h-4"></i>
                                        Lihat
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400 font-medium">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($p->user && $p->user->role === 'guru_pengganti')
                                    @php $namaPengganti = $p->nama_pengganti ?? $p->user->name; @endphp
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-[10px] font-bold shadow-sm">
                                            {{ substr($namaPengganti, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-gray-800">{{ $namaPengganti }}</p>
                                            <span class="text-[9px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">GURU PENGGANTI</span>
                                        </div>
                                    </div>
                                @elseif($p->user && $p->user->role === 'guru_mapel')
                                    @php 
                                        $namaMapel = $p->nama_pengganti ?? $p->user->name; 
                                        $mapel = $p->nama_mapel ? 'MAPEL: ' . strtoupper($p->nama_mapel) : 'GURU MAPEL';
                                    @endphp
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white text-[10px] font-bold shadow-sm">
                                            {{ substr($namaMapel, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-gray-800">{{ $namaMapel }}</p>
                                            <span class="text-[9px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">{{ $mapel }}</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 font-medium">Wali Kelas</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-20">
                                <div class="flex flex-col items-center justify-center text-center">
                                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800/40 rounded-2xl flex items-center justify-center mb-4 text-slate-300 dark:text-slate-600">
                                        <i data-lucide="calendar" class="w-8 h-8"></i>
                                    </div>
                                    @if(isset($isSearched) && !$isSearched)
                                        <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Silakan Lakukan Pencarian</h4>
                                        <p class="text-xs text-slate-400 dark:text-slate-500 max-w-xs">Gunakan filter pencarian di atas untuk menampilkan data riwayat presensi yang Anda inginkan.</p>
                                    @else
                                        <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Riwayat Presensi Kosong</h4>
                                        <p class="text-xs text-slate-400 dark:text-slate-500 max-w-xs">Tidak ditemukan riwayat pencatatan kehadiran presensi dengan kriteria pencarian ini.</p>
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
