<x-app-layout>
    <x-slot name="header">Monitoring Kehadiran Siswa</x-slot>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-50 overflow-hidden mb-8">
        <form action="{{ route('kepsek.monitoring') }}" method="GET" class="p-8 flex flex-col md:flex-row items-end gap-6">
            <div class="flex-1">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Pilih Tanggal</label>
                <input type="date" name="tanggal" value="{{ $today }}" class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
            <div class="w-full md:w-48">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Pilih Kelas</label>
                <select name="kelas" class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    <option value="">Semua Kelas</option>
                    @foreach($globalKelasList as $kls)
                        <option value="{{ $kls->nama_kelas }}" {{ ($kelas ?? '') == $kls->nama_kelas ? 'selected' : '' }}>Kelas {{ $kls->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="w-full md:w-auto px-8 py-3 bg-[#065F46] text-white rounded-xl font-bold text-sm hover:bg-emerald-800 transition flex items-center justify-center gap-2">
                <i data-lucide="filter" class="w-4 h-4"></i>
                Tampilkan
            </button>
        </form>
    </div>

    <div class="mb-8">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Status Presensi Kelas Hari Ini</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($kelasProgress as $kls)
                <div class="bg-white p-5 rounded-2xl border {{ $kls['sudah_absen'] ? 'border-emerald-200' : 'border-red-200' }} shadow-sm">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h4 class="font-bold text-gray-800 text-lg">Kelas {{ $kls['nama_kelas'] }}</h4>
                            <p class="text-xs text-gray-500">Total: {{ $kls['total_siswa'] }} Siswa</p>
                        </div>
                        @if($kls['sudah_absen'])
                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-bold flex items-center gap-1">
                                <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i>
                                Sudah
                            </span>
                        @else
                            <span class="px-2.5 py-1 bg-red-100 text-red-700 rounded-lg text-xs font-bold flex items-center gap-1">
                                <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                                Belum
                            </span>
                        @endif
                    </div>
                    
                    @if($kls['sudah_absen'])
                        <div class="grid grid-cols-4 gap-2 mt-4">
                            <div class="bg-emerald-50 rounded-xl p-2 text-center">
                                <div class="text-xs font-bold text-emerald-600 mb-1">H</div>
                                <div class="font-black text-emerald-700">{{ $kls['hadir'] }}</div>
                            </div>
                            <div class="bg-yellow-50 rounded-xl p-2 text-center">
                                <div class="text-xs font-bold text-yellow-600 mb-1">I</div>
                                <div class="font-black text-yellow-700">{{ $kls['izin'] }}</div>
                            </div>
                            <div class="bg-blue-50 rounded-xl p-2 text-center">
                                <div class="text-xs font-bold text-blue-600 mb-1">S</div>
                                <div class="font-black text-blue-700">{{ $kls['sakit'] }}</div>
                            </div>
                            <div class="bg-red-50 rounded-xl p-2 text-center">
                                <div class="text-xs font-bold text-red-600 mb-1">A</div>
                                <div class="font-black text-red-700">{{ $kls['alpha'] }}</div>
                            </div>
                        </div>
                    @else
                        <div class="mt-4 p-3 bg-red-50 rounded-xl text-center">
                            <p class="text-xs font-medium text-red-600">Wali kelas belum mengisi presensi.</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-50 overflow-hidden">
        <div class="p-8 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Rekapitulasi Kehadiran</h3>
                <p class="text-sm text-gray-500 font-medium">Tanggal: {{ \Carbon\Carbon::parse($today)->format('d F Y') }}</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-0">
                <thead>
                    <tr class="bg-[#065F46] text-white text-xs uppercase tracking-wider font-bold border-b-0 [&>th:first-child]:rounded-tl-2xl [&>th:last-child]:rounded-tr-2xl">
                        <th class="px-6 py-5 text-center w-16">No</th>
                        <th class="px-6 py-5">Siswa</th>
                        <th class="px-6 py-5">Kelas</th>
                        <th class="px-6 py-5 text-center">Status</th>
                        <th class="px-6 py-5 text-center">Dokumen</th>
                        <th class="px-6 py-5">Wali Kelas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($presensis as $p)
                        <tr class="hover:bg-emerald-50/50 transition">
                            <td class="px-6 py-4 text-center align-middle font-bold text-gray-400 text-sm">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-800 text-sm">{{ $p->siswa->nama_siswa }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full text-[10px] font-black uppercase">
                                    Kelas {{ $p->siswa->kelas }}
                                </span>
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
                            <td class="px-6 py-4 text-sm text-gray-500 font-medium">
                                {{ $p->user->name }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-20">
                                <div class="flex flex-col items-center justify-center text-center">
                                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800/40 rounded-2xl flex items-center justify-center mb-4 text-slate-300 dark:text-slate-600">
                                        <i data-lucide="clipboard-x" class="w-8 h-8"></i>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Aktivitas Presensi Kosong</h4>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 max-w-xs">Belum ada data pencatatan kehadiran presensi dari wali kelas untuk kelas atau tanggal ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
