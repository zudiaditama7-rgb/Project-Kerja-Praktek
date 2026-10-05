<x-app-layout>
    <x-slot name="header">Manajemen Tahun Ajaran</x-slot>

    <div x-data="{
        showEditModal: false,
        editId: null,
        editNamaTahun: '',
        editGasalMulai: '',
        editGasalSelesai: '',
        editGenapMulai: '',
        editGenapSelesai: '',
        editActionUrl: '',
        openEdit(ta) {
            this.editId = ta.id;
            this.editNamaTahun = ta.nama_tahun;
            this.editGasalMulai = ta.tanggal_mulai_gasal || '';
            this.editGasalSelesai = ta.tanggal_selesai_gasal || '';
            this.editGenapMulai = ta.tanggal_mulai_genap || '';
            this.editGenapSelesai = ta.tanggal_selesai_genap || '';
            this.editActionUrl = '{{ route('admin.tahun_ajaran.update', ':id') }}'.replace(':id', ta.id);
            this.showEditModal = true;
        }
    }" class="grid grid-cols-1 lg:grid-cols-3 gap-8 relative">
        
        {{-- Form Tambah/Set Aktif Tahun Ajaran --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-50 sticky top-24 max-h-[calc(100vh-8rem)] overflow-y-auto custom-scrollbar">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Tambah Tahun Ajaran</h3>
                
                <form action="{{ route('admin.tahun_ajaran.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Tahun Ajaran</label>
                        <input type="text" name="nama_tahun" required placeholder="Contoh: 2025/2026" class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                    
                    <div class="border-t border-gray-100 pt-4">
                        <h4 class="text-sm font-bold text-indigo-700 mb-3">Semester Gasal</h4>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 mb-1">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai_gasal" value="2025-07-14" class="w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-xs py-1.5 px-2">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 mb-1">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai_gasal" value="2025-12-19" class="w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-xs py-1.5 px-2">
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-4 mb-4">
                        <h4 class="text-sm font-bold text-emerald-700 mb-3">Semester Genap</h4>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 mb-1">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai_genap" value="2026-01-05" class="w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-xs py-1.5 px-2">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 mb-1">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai_genap" value="2026-06-26" class="w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-xs py-1.5 px-2">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-4 bg-[#065F46] text-white rounded-2xl font-bold hover:bg-emerald-800 shadow-xl shadow-emerald-100 transition">
                        Simpan Tahun Ajaran
                    </button>
                </form>
            </div>
        </div>

        {{-- Tabel Daftar Tahun Ajaran --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-50 overflow-hidden flex flex-col max-h-[calc(100vh-8rem)]">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between shrink-0">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Daftar Tahun Ajaran</h3>
                        <p class="text-xs text-gray-400 font-medium mt-1">Kelola data tahun akademik madrasah</p>
                    </div>
                </div>

                <div class="overflow-x-auto overflow-y-auto custom-scrollbar flex-1 relative">
                    <table class="w-full text-left border-separate border-spacing-0">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-[#065F46] text-white text-xs uppercase tracking-wider font-bold border-b-0 [&>th:first-child]:rounded-tl-2xl [&>th:last-child]:rounded-tr-2xl">
                                <th class="px-6 py-5 text-center w-16">No</th>
                                <th class="px-6 py-5">Tahun Ajaran</th>
                                <th class="px-6 py-5">Semester Gasal</th>
                                <th class="px-6 py-5">Semester Genap</th>
                                <th class="px-6 py-5">Status</th>
                                <th class="px-6 py-5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($tahunAjarans as $ta)
                                <tr class="hover:bg-emerald-50/50 transition">
                                    <td class="px-6 py-4 text-center align-middle font-bold text-gray-400 text-sm">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-800">
                                        {{ $ta->nama_tahun }}
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-600 font-medium">
                                        @if($ta->tanggal_mulai_gasal && $ta->tanggal_selesai_gasal)
                                            <span class="block text-indigo-700 font-bold">{{ \Carbon\Carbon::parse($ta->tanggal_mulai_gasal)->translatedFormat('d M Y') }}</span>
                                            <span class="text-gray-400">s.d</span>
                                            <span class="block text-indigo-700 font-bold">{{ \Carbon\Carbon::parse($ta->tanggal_selesai_gasal)->translatedFormat('d M Y') }}</span>
                                        @else
                                            <span class="text-gray-400 italic">Belum diatur</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-600 font-medium">
                                        @if($ta->tanggal_mulai_genap && $ta->tanggal_selesai_genap)
                                            <span class="block text-emerald-700 font-bold">{{ \Carbon\Carbon::parse($ta->tanggal_mulai_genap)->translatedFormat('d M Y') }}</span>
                                            <span class="text-gray-400">s.d</span>
                                            <span class="block text-emerald-700 font-bold">{{ \Carbon\Carbon::parse($ta->tanggal_selesai_genap)->translatedFormat('d M Y') }}</span>
                                        @else
                                            <span class="text-gray-400 italic">Belum diatur</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($ta->is_active)
                                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-black uppercase tracking-widest">AKTIF</span>
                                        @else
                                            <span class="px-3 py-1 bg-gray-100 text-gray-400 rounded-full text-[10px] font-bold uppercase tracking-widest">NON-AKTIF</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end items-center gap-2">
                                            <button @click="openEdit({{ json_encode($ta) }})" class="px-3 py-2 bg-amber-50 text-amber-700 rounded-xl text-xs font-bold hover:bg-amber-100 transition border border-amber-100 flex items-center gap-1">
                                                <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                                Atur Tanggal
                                            </button>
                                            @if(!$ta->is_active)
                                                <form action="{{ route('admin.tahun_ajaran.activate', $ta->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-2 bg-blue-50 text-blue-600 rounded-xl text-xs font-bold hover:bg-blue-100 transition border border-blue-100">
                                                        Aktifkan
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xs text-green-600 font-bold flex items-center gap-1">
                                                    <i data-lucide="check" class="w-3 h-3"></i>
                                                    Sedang Aktif
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" x-cloak class="fixed inset-0 z-[60] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-[2px] transition-opacity" @click="showEditModal = false"></div>
                <div class="relative bg-white dark:bg-[#0f172a] rounded-3xl max-w-md w-full p-8 shadow-2xl overflow-hidden flex flex-col text-left">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Atur Tanggal Semester</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-1">Konfigurasi rentang tanggal semester gasal dan genap</p>
                        </div>
                        <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    
                    <form :action="editActionUrl" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nama Tahun Ajaran</label>
                            <input type="text" name="nama_tahun" x-model="editNamaTahun" required class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                        
                        <div class="border-t border-gray-100 pt-4">
                            <h4 class="text-sm font-bold text-indigo-700 mb-3">Semester Gasal</h4>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 mb-1">Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulai_gasal" x-model="editGasalMulai" class="w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-xs py-1.5 px-2">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 mb-1">Tanggal Selesai</label>
                                    <input type="date" name="tanggal_selesai_gasal" x-model="editGasalSelesai" class="w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-xs py-1.5 px-2">
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-4 mb-4">
                            <h4 class="text-sm font-bold text-emerald-700 mb-3">Semester Genap</h4>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 mb-1">Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulai_genap" x-model="editGenapMulai" class="w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-xs py-1.5 px-2">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 mb-1">Tanggal Selesai</label>
                                    <input type="date" name="tanggal_selesai_genap" x-model="editGenapSelesai" class="w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-xs py-1.5 px-2">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-4 bg-blue-600 text-white rounded-2xl font-bold hover:bg-blue-700 shadow-xl shadow-blue-100 transition mt-2">
                            Simpan Perubahan
                        </button>
                    </form>
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
