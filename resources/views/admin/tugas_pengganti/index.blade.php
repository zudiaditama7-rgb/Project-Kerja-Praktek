<x-app-layout>
    <x-slot name="header">Kelola Penugasan Guru</x-slot>

    <div x-data="{ assignModalOpen: false, rejectModalOpen: false, selectedId: null }">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                        <i data-lucide="users" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Daftar Pengajuan Substitusi</h3>
                        <p class="text-xs text-gray-500 font-medium">Kelola permohonan guru pengganti dari wali kelas dan guru mapel</p>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-separate border-spacing-0">
                    <thead>
                        <tr class="bg-[#065F46] text-white text-xs uppercase tracking-wider [&>th:first-child]:rounded-tl-2xl [&>th:last-child]:rounded-tr-2xl">
                            <th class="px-3 py-3 text-center font-semibold w-10">No</th>
                            <th class="px-3 py-3 font-semibold">Pengaju</th>
                            <th class="px-3 py-3 font-semibold text-center whitespace-nowrap">Kelas</th>
                            <th class="px-3 py-3 font-semibold whitespace-nowrap">Tanggal</th>
                            <th class="px-3 py-3 font-semibold">Alasan</th>
                            <th class="px-3 py-3 font-semibold text-center whitespace-nowrap">Dokumen</th>
                            <th class="px-3 py-3 font-semibold text-center whitespace-nowrap">Status</th>
                            <th class="px-3 py-3 font-semibold">Ditugaskan</th>
                            <th class="px-3 py-3 font-semibold text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengajuans as $p)
                            <tr class="{{ $loop->even ? 'bg-gray-50/80' : 'bg-white' }} hover:bg-emerald-50/50 transition-colors border-b border-gray-200">
                                <td class="px-3 py-3 text-center align-middle font-bold text-gray-500 whitespace-nowrap">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-3 py-3 align-middle">
                                    <span class="font-semibold text-gray-800" title="{{ $p->waliKelas ? $p->waliKelas->name : ($p->guruMapel ? $p->guruMapel->name : '') }}">{{ $p->waliKelas ? $p->waliKelas->name : ($p->guruMapel ? $p->guruMapel->name : '') }}</span>
                                    @if($p->guru_mapel_id)
                                        <p class="text-[10px] text-gray-500 font-medium mt-0.5">Guru Mapel</p>
                                    @else
                                        <p class="text-[10px] text-gray-500 font-medium mt-0.5">Wali Kelas</p>
                                    @endif
                                </td>
                                <td class="px-3 py-3 align-middle text-center whitespace-nowrap">
                                    <span class="inline-block px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-md text-[10px] font-black uppercase">
                                        Kelas {{ $p->kelas }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 align-middle whitespace-nowrap text-xs">
                                    <span class="font-semibold text-gray-700">{{ \Carbon\Carbon::parse($p->tanggal)->format('d M Y') }}</span>
                                    @if($p->waktu_mulai && $p->waktu_selesai)
                                        <p class="text-[10px] bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full inline-block mt-1 border border-emerald-100 font-bold">
                                            {{ substr($p->waktu_mulai, 0, 5) }} - {{ substr($p->waktu_selesai, 0, 5) }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-3 py-3 align-middle max-w-[150px]">
                                    <span class="text-gray-600 text-xs line-clamp-2" title="{{ $p->alasan }}">{{ $p->alasan }}</span>
                                </td>
                                <td class="px-3 py-3 align-middle text-center whitespace-nowrap">
                                    @if($p->document_path)
                                        <a href="{{ route('tugas_pengganti.dokumen', $p->id) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg text-xs font-bold transition whitespace-nowrap">
                                            <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                            Lihat
                                        </a>
                                    @else
                                        <span class="text-gray-300 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 align-middle text-center whitespace-nowrap">
                                    @if($p->status === 'pending')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-100 text-amber-700 rounded-md text-[10px] font-black uppercase tracking-wide">
                                            <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                                            Pending
                                        </span>
                                    @elseif($p->status === 'disetujui')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-100 text-green-700 rounded-md text-[10px] font-black uppercase tracking-wide">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                            Disetujui
                                        </span>
                                    @elseif($p->status === 'selesai')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-100 text-blue-700 rounded-md text-[10px] font-black uppercase tracking-wide">
                                            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                                            Selesai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-100 text-red-700 rounded-md text-[10px] font-black uppercase tracking-wide">
                                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                            Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 align-middle">
                                    @if(in_array($p->status, ['disetujui', 'selesai']) && $p->guruPengganti)
                                        <span class="font-bold text-[#065F46]" title="{{ $p->guruPengganti->name }}">{{ $p->guruPengganti->name }}</span>
                                    @else
                                        <span class="text-gray-300 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 align-middle text-center whitespace-nowrap">
                                    @if($p->status === 'pending')
                                        <div class="flex items-center justify-center gap-1">
                                            <button @click="selectedId = {{ $p->id }}; assignModalOpen = true" 
                                                    class="inline-flex items-center gap-1 px-2 py-1.5 bg-emerald-600 text-white hover:bg-emerald-700 rounded-lg text-xs font-bold transition shadow-sm" title="Tugaskan">
                                                <i data-lucide="user-check" class="w-3.5 h-3.5"></i>
                                                <span class="hidden xl:inline">Tugaskan</span>
                                            </button>
                                            <button @click="selectedId = {{ $p->id }}; rejectModalOpen = true" 
                                                    class="inline-flex items-center gap-1 px-2 py-1.5 bg-red-500 text-white hover:bg-red-600 rounded-lg text-xs font-bold transition shadow-sm" title="Tolak">
                                                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                                <span class="hidden xl:inline">Tolak</span>
                                            </button>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-20">
                                    <div class="flex flex-col items-center justify-center text-center">
                                        <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4 text-slate-300">
                                            <i data-lucide="users" class="w-8 h-8"></i>
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-700 mb-1">Belum Ada Pengajuan</h4>
                                        <p class="text-xs text-slate-400 max-w-xs">Belum ada wali kelas atau guru mapel yang mengajukan permohonan substitusi guru pengganti.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- End of Card -->

        <!-- Assign Modal -->
        <template x-teleport="body">
            <div x-show="assignModalOpen" style="display: none;" class="fixed inset-0 z-[60] overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="assignModalOpen" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div x-show="assignModalOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    
                    <!-- Dinamis Form Action menggunakan Alpine.js Bind -->
                    <form :action="`/admin/tugas-pengganti/${selectedId}/assign`" method="POST">
                        @csrf
                        <div class="bg-white px-8 pt-8 pb-6">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-emerald-50 sm:mx-0 sm:h-10 sm:w-10 text-emerald-600">
                                    <i data-lucide="user-plus" class="h-5 w-5"></i>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-bold text-gray-900 mb-4" id="modal-title">
                                        Pilih Guru Pengganti
                                    </h3>
                                    
                                    <div class="mt-2">
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Tugaskan Kepada:</label>
                                        <select name="guru_pengganti_id" required class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                            <option value="">-- Pilih Guru Pengganti --</option>
                                            @foreach($gurus as $guru)
                                                <option value="{{ $guru->id }}">{{ $guru->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-8 py-4 sm:flex sm:flex-row-reverse gap-3">
                            <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-[#065F46] text-base font-medium text-white hover:bg-emerald-800 focus:outline-none sm:w-auto sm:text-sm">
                                Tugaskan & Setujui
                            </button>
                            <button type="button" @click="assignModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        <!-- Reject Modal -->
        <template x-teleport="body">
            <div x-show="rejectModalOpen" style="display: none;" class="fixed inset-0 z-[60] overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="rejectModalOpen" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div x-show="rejectModalOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    
                    <form :action="`/admin/tugas-pengganti/${selectedId}/reject`" method="POST">
                        @csrf
                        <div class="bg-white px-8 pt-8 pb-6">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-50 sm:mx-0 sm:h-10 sm:w-10 text-red-600">
                                    <i data-lucide="alert-triangle" class="h-5 w-5"></i>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-bold text-gray-900 mb-2" id="modal-title">
                                        Tolak Pengajuan
                                    </h3>
                                    <p class="text-sm text-gray-500 mb-4">
                                        Apakah Anda yakin ingin menolak pengajuan ini? Wali kelas akan tetap diwajibkan melakukan presensi.
                                    </p>
                                    
                                    <div class="mt-4 text-left">
                                        <label for="keterangan_ditolak" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                                        <textarea name="keterangan_ditolak" id="keterangan_ditolak" rows="3" required placeholder="Tuliskan alasan mengapa pengajuan ini ditolak..." class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm placeholder:text-gray-400 resize-none"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-8 py-4 sm:flex sm:flex-row-reverse gap-3">
                            <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:w-auto sm:text-sm">
                                Ya, Tolak
                            </button>
                            <button type="button" @click="rejectModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        </template>
        </div>
    </div>
</x-app-layout>
