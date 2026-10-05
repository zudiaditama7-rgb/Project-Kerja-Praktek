<x-app-layout>
    <x-slot name="header">Pengajuan Guru Pengganti</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Form Pengajuan -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-50 sticky top-24 max-h-[calc(100vh-8rem)] overflow-y-auto custom-scrollbar">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i data-lucide="calendar-clock" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Buat Pengajuan</h3>
                </div>
                
                <form action="{{ route('guru_mapel.tugas_pengganti.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Kelas</label>
                        <select name="kelas" required class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelases as $kelas)
                                <option value="{{ $kelas->nama_kelas }}">{{ $kelas->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Berhalangan Hadir</label>
                        <input type="date" name="tanggal" min="{{ date('Y-m-d') }}" required class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Waktu Mulai</label>
                            <input type="time" name="waktu_mulai" required class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Waktu Selesai</label>
                            <input type="time" name="waktu_selesai" required class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Alasan</label>
                        <textarea name="alasan" required rows="3" placeholder="Contoh: Sedang sakit, Dinas luar kota, dll..." class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Dokumen Pendukung (Surat Cuti/Sakit)</label>
                        <input type="file" name="dokumen" required class="w-full rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm p-2 bg-white">
                        <p class="text-[10px] text-gray-500 mt-1">Format: JPG, JPEG, PNG, PDF (Maks. 2MB)</p>
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="w-full px-4 py-3 bg-[#065F46] text-white rounded-xl font-bold hover:bg-emerald-800 transition flex items-center justify-center gap-2 shadow-lg shadow-emerald-900/20">
                            <i data-lucide="send" class="w-4 h-4"></i>
                            Kirim Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Riwayat Pengajuan -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-50 overflow-hidden flex flex-col max-h-[calc(100vh-8rem)]">
                <div class="p-8 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 shrink-0">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Riwayat Pengajuan</h3>
                        <p class="text-sm text-gray-500">Daftar permohonan guru pengganti Anda</p>
                    </div>
                </div>

                <div class="overflow-x-auto overflow-y-auto custom-scrollbar flex-1 relative">
                    <table class="w-full text-left border-separate border-spacing-0">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-[#065F46] text-white text-xs uppercase tracking-wider font-bold border-b-0 [&>th:first-child]:rounded-tl-2xl [&>th:last-child]:rounded-tr-2xl">
                                <th class="px-6 py-5 text-center w-16">No</th>
                                <th class="px-6 py-5">Kelas & Waktu</th>
                                <th class="px-6 py-5">Alasan</th>
                                <th class="px-6 py-5">Dokumen</th>
                                <th class="px-6 py-5 text-center">Status</th>
                                <th class="px-6 py-5">Ditugaskan Kepada</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($pengajuans as $p)
                                <tr class="hover:bg-emerald-50/50 transition">
                                    <td class="px-6 py-4 text-center align-middle font-bold text-gray-400 text-sm">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-gray-800 text-sm">Kelas {{ $p->kelas }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5 font-medium">{{ \Carbon\Carbon::parse($p->tanggal)->format('d M Y') }}</p>
                                        <p class="text-[10px] bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full inline-block mt-1 border border-emerald-100 font-bold">
                                            {{ substr($p->waktu_mulai, 0, 5) }} - {{ substr($p->waktu_selesai, 0, 5) }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $p->alasan }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        @if($p->document_path)
                                            <a href="{{ route('tugas_pengganti.dokumen', $p->id) }}" class="inline-flex items-center gap-1.5 text-emerald-600 hover:text-emerald-800 font-bold transition">
                                                <i data-lucide="file-text" class="w-4 h-4"></i>
                                                Lihat Dokumen
                                            </a>
                                        @else
                                            <span class="text-gray-400 font-medium">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($p->status === 'pending')
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-[10px] font-black uppercase tracking-widest">Pending</span>
                                        @elseif($p->status === 'disetujui')
                                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-black uppercase tracking-widest">Disetujui</span>
                                        @elseif($p->status === 'selesai')
                                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-[10px] font-black uppercase tracking-widest">Selesai</span>
                                        @else
                                            <div class="flex flex-col items-center gap-1.5">
                                                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-[10px] font-black uppercase tracking-widest">Ditolak</span>
                                                @if($p->keterangan_ditolak)
                                                    <p class="text-xs text-red-600 mt-1 max-w-[200px] leading-relaxed">"{{ $p->keterangan_ditolak }}"</p>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        @if(in_array($p->status, ['disetujui', 'selesai']) && $p->guruPengganti)
                                            <span class="font-bold text-[#065F46]">{{ $p->guruPengganti->name }}</span>
                                        @elseif($p->status === 'pending')
                                            <span class="text-gray-400 italic font-medium">Menunggu Admin...</span>
                                        @else
                                            <span class="text-gray-400 font-medium text-xs">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-8 py-20">
                                        <div class="flex flex-col items-center justify-center text-center">
                                            <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4 text-slate-300">
                                                <i data-lucide="file-clock" class="w-8 h-8"></i>
                                            </div>
                                            <h4 class="text-sm font-bold text-slate-700 mb-1">Belum Ada Pengajuan</h4>
                                            <p class="text-xs text-slate-400 max-w-xs">Anda belum pernah mengajukan permohonan guru pengganti.</p>
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
