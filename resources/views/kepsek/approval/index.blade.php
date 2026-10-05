<x-app-layout>
    <x-slot name="header">Persetujuan Rekap Presensi</x-slot>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-50 overflow-hidden">
        <div class="p-8 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Daftar Pengajuan Wali Kelas</h3>
                <p class="text-sm text-gray-500 font-medium">Verifikasi dan setujui rekapitulasi presensi bulanan.</p>
            </div>
            <div class="p-3 bg-indigo-100 text-indigo-700 rounded-2xl">
                <i data-lucide="shield-check" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-0">
                <thead>
                    <tr class="bg-[#065F46] text-white text-xs uppercase tracking-wider font-bold border-b-0 [&>th:first-child]:rounded-tl-2xl [&>th:last-child]:rounded-tr-2xl">
                        <th class="px-6 py-5 text-center w-16">No</th>
                        <th class="px-6 py-5">Wali Kelas</th>
                        <th class="px-6 py-5">Periode</th>
                        <th class="px-6 py-5">Status</th>
                        <th class="px-6 py-5">Tanggal Pengajuan</th>
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
                                <p class="font-bold text-gray-800 text-sm">{{ $p->user->name }}</p>
                                <p class="text-[10px] text-gray-400 uppercase font-medium">Kelas {{ $p->kelas }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    if ($p->tipe === 'semester') {
                                        $periodeLabel = "Semester " . ucfirst($p->bulan) . " (" . ($p->bulan === 'ganjil' ? 'Juli - Des' : 'Jan - Jun') . ") " . $p->tahun;
                                    } else {
                                        $periodeLabel = (is_numeric($p->bulan) ? \Carbon\Carbon::create(null, $p->bulan)->translatedFormat('F') : ucfirst($p->bulan)) . " " . $p->tahun;
                                    }
                                @endphp
                                <span class="text-sm font-bold text-gray-700">
                                    {{ $periodeLabel }}
                                </span>
                                <div class="mt-1">
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider {{ $p->tipe === 'semester' ? 'bg-purple-100 text-purple-700 border border-purple-200' : 'bg-blue-100 text-blue-700 border border-blue-200' }}">
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
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $p->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($p->status === 'pending')
                                    <button onclick="openApprovalModal({{ $p->id }}, '{{ $p->user->name }}', '{{ $periodeLabel }}')" class="px-4 py-2 bg-[#065F46] text-white rounded-xl text-xs font-bold hover:bg-emerald-800 transition shadow-lg shadow-emerald-100">
                                        Proses
                                    </button>
                                @else
                                    <div class="flex flex-col items-end">
                                        <span class="text-[10px] text-gray-400 font-bold uppercase italic">Sudah Diproses</span>
                                        <span class="text-[9px] text-gray-400">{{ $p->acc_date ? \Carbon\Carbon::parse($p->acc_date)->format('d/m/Y') : '' }}</span>
                                    </div>
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
                                    <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Tidak Ada Pengajuan Masuk</h4>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 max-w-xs">Saat ini tidak ada permohonan persetujuan rekapitulasi dari wali kelas.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Approval Modal -->
    <div id="approvalModal" class="fixed inset-0 z-[60] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="closeModal('approvalModal')"></div>
            <div class="relative bg-white rounded-3xl max-w-lg w-full p-8 shadow-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Proses Persetujuan</h3>
                    <button onclick="closeModal('approvalModal')" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
                
                <div class="bg-emerald-50 p-6 rounded-2xl mb-8 border border-emerald-100">
                    <div class="flex justify-between mb-2">
                        <span class="text-xs font-bold text-emerald-400 uppercase tracking-widest">Wali Kelas</span>
                        <span class="text-xs font-bold text-emerald-400 uppercase tracking-widest">Periode</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <p class="font-bold text-emerald-900" id="modalWaliName"></p>
                        <p class="font-bold text-emerald-900" id="modalPeriode"></p>
                    </div>
                </div>

                <form id="approvalForm" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Keputusan</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="relative block cursor-pointer">
                                <input type="radio" name="status" value="disetujui" checked class="peer sr-only">
                                <div class="p-4 border-2 border-gray-100 rounded-2xl peer-checked:border-green-500 peer-checked:bg-green-50 transition text-center">
                                    <i data-lucide="check-circle" class="w-6 h-6 mx-auto mb-2 text-green-500"></i>
                                    <span class="font-bold text-sm text-gray-700">Setujui</span>
                                </div>
                            </label>
                            <label class="relative block cursor-pointer">
                                <input type="radio" name="status" value="ditolak" class="peer sr-only">
                                <div class="p-4 border-2 border-gray-100 rounded-2xl peer-checked:border-red-500 peer-checked:bg-red-50 transition text-center">
                                    <i data-lucide="x-circle" class="w-6 h-6 mx-auto mb-2 text-red-500"></i>
                                    <span class="font-bold text-sm text-gray-700">Tolak</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Keterangan / Catatan</label>
                        <textarea name="keterangan" rows="3" placeholder="Masukkan catatan jika diperlukan..." class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm"></textarea>
                    </div>

                    <button type="submit" class="w-full py-4 bg-[#065F46] text-white rounded-2xl font-bold hover:bg-emerald-800 shadow-xl shadow-emerald-100 transition">
                        Simpan Keputusan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openApprovalModal(id, name, periode) {
            const modal = document.getElementById('approvalModal');
            const form = document.getElementById('approvalForm');
            const nameEl = document.getElementById('modalWaliName');
            const periodeEl = document.getElementById('modalPeriode');
            
            form.action = `/kepsek/approval/${id}`;
            nameEl.innerText = name;
            periodeEl.innerText = periode;
            
            modal.classList.remove('hidden');
            lucide.createIcons();
        }
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }
    </script>
</x-app-layout>
