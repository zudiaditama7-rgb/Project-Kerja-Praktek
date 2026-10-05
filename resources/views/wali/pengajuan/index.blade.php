<x-app-layout>
    <x-slot name="header">Pengajuan Rekap Presensi</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Form Pengajuan -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-50 sticky top-24 max-h-[calc(100vh-8rem)] overflow-y-auto custom-scrollbar">
                <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-6">
                    <i data-lucide="send" class="w-8 h-8"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Ajukan Rekap</h3>
                <p class="text-sm text-gray-500 mb-8 font-medium">Pilih periode rekapitulasi yang ingin diajukan ke Kepala Sekolah.</p>
                
                <form action="{{ route('wali.pengajuan.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tipe Pengajuan</label>
                        <select name="tipe" id="tipe-select" required class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                            <option value="bulanan">Bulanan</option>
                            <option value="semester">1 Semester (6 Bulan)</option>
                        </select>
                    </div>
                    <div id="container-bulan">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Bulan</label>
                        <select name="bulan" id="bulan-select" required class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                            @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $index => $bulan)
                                <option value="{{ $index + 1 }}" {{ date('n') == $index + 1 ? 'selected' : '' }}>{{ $bulan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="container-semester" class="hidden">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Semester</label>
                        <select name="bulan" id="semester-select" disabled class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                            <option value="ganjil">Semester Ganjil (Juli - Desember)</option>
                            <option value="genap">Semester Genap (Januari - Juni)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tahun</label>
                        <select name="tahun" required class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                            @for($y = date('Y'); $y >= date('Y')-2; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <button type="submit" class="w-full py-4 bg-[#065F46] text-white rounded-2xl font-bold hover:bg-emerald-800 shadow-xl shadow-emerald-100 transition">
                        Kirim Pengajuan
                    </button>
                </form>
            </div>
        </div>

        <!-- Tabel Riwayat Pengajuan -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-50 overflow-hidden flex flex-col max-h-[calc(100vh-8rem)]">
                <div class="p-8 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 shrink-0">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Riwayat Pengajuan</h3>
                        <p class="text-sm text-gray-500">Daftar permohonan izin/sakit siswa Anda</p>
                    </div>
                </div>
                
                <div class="overflow-x-auto overflow-y-auto custom-scrollbar flex-1 relative">
                    <table class="w-full text-left border-separate border-spacing-0">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-[#065F46] text-white text-xs uppercase tracking-wider font-bold border-b-0 [&>th:first-child]:rounded-tl-2xl [&>th:last-child]:rounded-tr-2xl">
                                <th class="px-6 py-5 text-center w-16">No</th>
                                <th class="px-6 py-5">Periode</th>
                                <th class="px-6 py-5">Status</th>
                                <th class="px-6 py-5">Keterangan</th>
                                <th class="px-6 py-5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($pengajuans as $p)
                                <tr class="hover:bg-emerald-50/50 transition">
                                    <td class="px-6 py-4 text-center align-middle font-bold text-gray-400 text-sm">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-gray-800 text-sm">
                                            @if($p->tipe === 'semester')
                                                Semester {{ ucfirst($p->bulan) }} ({{ $p->bulan === 'ganjil' ? 'Juli - Des' : 'Jan - Jun' }}) {{ $p->tahun }}
                                            @else
                                                {{ is_numeric($p->bulan) ? \Carbon\Carbon::create(null, $p->bulan)->translatedFormat('F') : ucfirst($p->bulan) }} {{ $p->tahun }}
                                            @endif
                                        </p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <p class="text-[10px] text-gray-400 uppercase font-medium">Kelas {{ $p->kelas }}</p>
                                            <span class="text-[9px] px-1.5 py-0.5 rounded font-black uppercase tracking-wider {{ $p->tipe === 'semester' ? 'bg-purple-50 text-purple-600 border border-purple-100' : 'bg-blue-50 text-blue-600 border border-blue-100' }}">
                                                {{ $p->tipe === 'semester' ? '1 Semester' : 'Bulanan' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-700',
                                                'disetujui' => 'bg-green-100 text-green-700',
                                                'ditolak' => 'bg-red-100 text-red-700',
                                            ];
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest {{ $statusColors[$p->status] }}">
                                            {{ $p->status === 'pending' ? 'direview' : $p->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-500 italic">
                                        {{ $p->keterangan ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if($p->status === 'disetujui')
                                            <a href="{{ route('wali.pengajuan.pdf', $p->id) }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-bold hover:bg-emerald-100 transition">
                                                <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                                PDF
                                            </a>
                                        @else
                                            <span class="text-[10px] text-gray-300 font-bold uppercase italic">Belum Tersedia</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16">
                                        <div class="flex flex-col items-center justify-center text-center">
                                            <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800/40 rounded-2xl flex items-center justify-center mb-4 text-slate-300 dark:text-slate-600">
                                                <i data-lucide="folder-open" class="w-8 h-8"></i>
                                            </div>
                                            <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Riwayat Pengajuan Kosong</h4>
                                            <p class="text-xs text-slate-400 dark:text-slate-500 max-w-xs">Anda belum pernah mengirim pengajuan rekapitulasi presensi ke Kepala Sekolah.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipeSelect = document.getElementById('tipe-select');
            const containerBulan = document.getElementById('container-bulan');
            const containerSemester = document.getElementById('container-semester');
            const bulanSelect = document.getElementById('bulan-select');
            const semesterSelect = document.getElementById('semester-select');
            
            tipeSelect.addEventListener('change', function() {
                if (this.value === 'semester') {
                    containerBulan.classList.add('hidden');
                    bulanSelect.disabled = true;
                    
                    containerSemester.classList.remove('hidden');
                    semesterSelect.disabled = false;
                } else {
                    containerBulan.classList.remove('hidden');
                    bulanSelect.disabled = false;
                    
                    containerSemester.classList.add('hidden');
                    semesterSelect.disabled = true;
                }
            });
        });
    </script>
    @endpush

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9; 
            border-radius: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1; 
            border-radius: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8; 
        }
    </style>
</x-app-layout>
