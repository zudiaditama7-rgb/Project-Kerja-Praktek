<x-app-layout>
    <x-slot name="header">Monitoring Presensi Realtime</x-slot>

    <!-- Filter Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-50 overflow-hidden mb-8">
        <form action="{{ route('admin.monitoring') }}" method="GET" class="p-8 flex flex-col md:flex-row items-end gap-6">
            <div class="flex-1">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Pilih Tanggal</label>
                <input type="date" name="tanggal" value="{{ $today ?? date('Y-m-d') }}" class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
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
                Filter
            </button>
        </form>
    </div>

    <!-- Data Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-50 overflow-hidden">
        <div class="p-8 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Aktivitas Presensi</h3>
                <p class="text-sm text-gray-500 font-medium">{{ \Carbon\Carbon::parse($today ?? date('Y-m-d'))->format('d F Y') }}</p>
            </div>
            <div class="flex gap-2">
                <div class="px-4 py-2 bg-green-50 text-green-700 rounded-xl text-xs font-bold border border-green-100">
                    Hadir: {{ $presensis->where('status', 'hadir')->count() }}
                </div>
                <div class="px-4 py-2 bg-red-50 text-red-700 rounded-xl text-xs font-bold border border-red-100">
                    Alpha: {{ $presensis->where('status', 'alpha')->count() }}
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-0">
                <thead>
                    <tr class="bg-[#065F46] text-white text-xs uppercase tracking-wider font-bold border-b-0 [&>th:first-child]:rounded-tl-2xl [&>th:last-child]:rounded-tr-2xl">
                        <th class="px-6 py-5 text-center w-16">No</th>
                        <th class="px-6 py-5">Waktu</th>
                        <th class="px-6 py-5">Siswa</th>
                        <th class="px-6 py-5">Kelas</th>
                        <th class="px-6 py-5 text-center">Status</th>
                        <th class="px-6 py-5 text-center">Dokumen</th>
                        <th class="px-6 py-5">Dicatat Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($presensis as $p)
                        <tr class="hover:bg-emerald-50/50 transition">
                            <td class="px-6 py-4 text-center align-middle font-bold text-gray-400 text-sm">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-6 py-4 text-xs font-mono text-gray-400">
                                {{ $p->created_at->format('H:i:s') }}
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-800 text-sm">{{ $p->siswa->nama_siswa }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-[10px] font-black uppercase">
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
                            <td class="px-6 py-4 text-sm text-gray-500 italic">
                                {{ $p->user->name }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-8 py-20">
                                <div class="flex flex-col items-center justify-center text-center">
                                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800/40 rounded-2xl flex items-center justify-center mb-4 text-slate-300 dark:text-slate-600">
                                        <i data-lucide="clipboard-x" class="w-8 h-8"></i>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Aktivitas Presensi Kosong</h4>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 max-w-xs">Belum ada pencatatan kehadiran siswa pada tanggal atau kelas yang Anda pilih.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
