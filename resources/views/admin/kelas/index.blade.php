<x-app-layout>
    <x-slot name="header">Manajemen Kelas & Wali Kelas</x-slot>

    <div x-data="{
        showEditModal: false,
        editId: null,
        editNamaKelas: '',
        editWaliKelasId: '',
        editActionUrl: '',
        openEdit(kelas) {
            this.editId = kelas.id;
            this.editNamaKelas = kelas.nama_kelas;
            this.editWaliKelasId = kelas.wali_kelas_id || '';
            this.editActionUrl = '{{ route('admin.kelas.update', ':id') }}'.replace(':id', kelas.id);
            this.showEditModal = true;
        }
    }" class="grid grid-cols-1 lg:grid-cols-3 gap-8 relative">

        {{-- Form Tambah Kelas --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-50 sticky top-24 max-h-[calc(100vh-8rem)] overflow-y-auto custom-scrollbar">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white shadow-lg shadow-emerald-100">
                        <i data-lucide="school" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Tambah Kelas</h3>
                        <p class="text-xs text-gray-400 font-medium">Daftarkan kelas baru ke sistem</p>
                    </div>
                </div>

                <form action="{{ route('admin.kelas.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Kelas</label>
                        <input type="text" name="nama_kelas" required placeholder="Contoh: 1, 2, 3..." class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm" value="{{ old('nama_kelas') }}">
                        @error('nama_kelas')
                            <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Wali Kelas</label>
                        <select name="wali_kelas_id" class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            <option value="">— Belum Ditentukan —</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}" {{ old('wali_kelas_id') == $t->id ? 'selected' : '' }}>
                                    {{ $t->name }}
                                    @if($t->kelas)
                                        (Kelas {{ $t->kelas }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-gray-400 mt-1.5 font-medium">Jika guru sudah menjadi wali kelas lain, sistem akan otomatis merotasi.</p>
                    </div>

                    <button type="submit" class="w-full py-4 bg-[#065F46] text-white rounded-2xl font-bold hover:bg-emerald-800 shadow-xl shadow-emerald-100 transition flex items-center justify-center gap-2">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        Simpan Kelas Baru
                    </button>
                </form>
            </div>

            {{-- Info Card --}}
            <div class="bg-gradient-to-br from-slate-50 to-white rounded-3xl p-6 shadow-sm border border-gray-100 mt-6">
                <div class="flex items-center gap-2 mb-3">
                    <i data-lucide="info" class="w-4 h-4 text-blue-500"></i>
                    <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">Panduan</span>
                </div>
                <div class="space-y-3 text-[11px] text-gray-500 leading-relaxed">
                    <div class="flex items-start gap-2 bg-white p-3 rounded-xl border border-gray-50">
                        <i data-lucide="shield-check" class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5"></i>
                        <span>Saat <strong>Wali Kelas</strong> dipindahkan ke kelas baru, jabatan kelas lama akan otomatis <strong>dikosongkan</strong>.</span>
                    </div>
                    <div class="flex items-start gap-2 bg-white p-3 rounded-xl border border-gray-50">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-500 shrink-0 mt-0.5"></i>
                        <span>Kelas yang masih memiliki <strong>siswa aktif</strong> tidak dapat dihapus. Pindahkan atau luluskan siswa terlebih dahulu.</span>
                    </div>
                    <div class="flex items-start gap-2 bg-white p-3 rounded-xl border border-gray-50">
                        <i data-lucide="refresh-cw" class="w-4 h-4 text-blue-500 shrink-0 mt-0.5"></i>
                        <span>Jika nama kelas diubah, seluruh data siswa dan guru terkait akan <strong>otomatis ter-update</strong>.</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Daftar Kelas --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-50 overflow-hidden flex flex-col max-h-[calc(100vh-8rem)]">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between shrink-0">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Daftar Kelas</h3>
                        <p class="text-xs text-gray-400 font-medium mt-1">Total {{ $kelases->count() }} kelas terdaftar</p>
                    </div>
                    <div class="px-4 py-2 bg-emerald-50 text-emerald-700 rounded-xl text-xs font-bold border border-emerald-100">
                        <i data-lucide="users" class="w-3.5 h-3.5 inline -mt-0.5"></i>
                        {{ $studentCounts->sum() }} Siswa Aktif
                    </div>
                </div>

                <div class="overflow-x-auto overflow-y-auto custom-scrollbar flex-1 relative">
                    <table class="w-full text-left border-separate border-spacing-0">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-[#065F46] text-white text-xs uppercase tracking-wider font-bold border-b-0 [&>th:first-child]:rounded-tl-2xl [&>th:last-child]:rounded-tr-2xl">
                                <th class="px-6 py-5 text-center w-16">No</th>
                                <th class="px-6 py-5">Kelas</th>
                                <th class="px-6 py-5">Wali Kelas</th>
                                <th class="px-6 py-5 text-center">Jumlah Siswa</th>
                                <th class="px-6 py-5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($kelases as $k)
                                <tr class="hover:bg-emerald-50/50 transition group">
                                    <td class="px-6 py-4 text-center align-middle font-bold text-gray-400 text-sm">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                                {{ $k->nama_kelas }}
                                            </div>
                                            <span class="font-bold text-gray-800 text-sm">Kelas {{ $k->nama_kelas }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($k->waliKelas)
                                            <div class="flex items-center gap-2">
                                                <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-black">
                                                    {{ strtoupper(substr($k->waliKelas->name, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-gray-700">{{ $k->waliKelas->name }}</p>
                                                    <p class="text-[10px] text-gray-400 font-medium">{{ $k->waliKelas->username }}</p>
                                                </div>
                                            </div>
                                        @else
                                            <span class="px-3 py-1 bg-amber-50 text-amber-600 rounded-lg text-[10px] font-bold border border-amber-100">
                                                <i data-lucide="alert-circle" class="w-3 h-3 inline -mt-0.5"></i>
                                                Belum Ditentukan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-xl text-xs font-bold border border-blue-100">
                                            <i data-lucide="users" class="w-3.5 h-3.5"></i>
                                            {{ $studentCounts[$k->nama_kelas] ?? 0 }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end items-center gap-2">
                                            <button @click="openEdit({{ json_encode($k) }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-xl transition" title="Edit">
                                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                                            </button>
                                            @if(($studentCounts[$k->nama_kelas] ?? 0) === 0)
                                                <form action="{{ route('admin.kelas.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Hapus kelas {{ $k->nama_kelas }}?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-xl transition" title="Hapus">
                                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="p-2 text-gray-300 cursor-not-allowed" title="Tidak bisa dihapus, masih ada siswa aktif">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16">
                                        <div class="flex flex-col items-center justify-center text-center">
                                            <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4 text-slate-300">
                                                <i data-lucide="school" class="w-8 h-8"></i>
                                            </div>
                                            <h4 class="text-sm font-bold text-slate-700 mb-1">Belum Ada Kelas</h4>
                                            <p class="text-xs text-slate-400 max-w-xs">Silakan tambahkan kelas baru menggunakan form di samping.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Edit Modal --}}
        <div x-show="showEditModal" x-cloak class="fixed inset-0 z-[60] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-[2px] transition-opacity" @click="showEditModal = false"></div>
                <div class="relative bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl overflow-hidden flex flex-col text-left">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Edit Kelas</h3>
                            <p class="text-xs text-gray-500 font-medium mt-1">Ubah nama kelas atau rotasi wali kelas</p>
                        </div>
                        <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form :action="editActionUrl" method="POST" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nama Kelas</label>
                            <input type="text" name="nama_kelas" x-model="editNamaKelas" required class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Wali Kelas</label>
                            <select name="wali_kelas_id" x-model="editWaliKelasId" class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <option value="">— Belum Ditentukan —</option>
                                @foreach($teachers as $t)
                                    <option value="{{ $t->id }}">
                                        {{ $t->name }}
                                        @if($t->kelas)
                                            (Kelas {{ $t->kelas }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="bg-amber-50 border border-amber-100 rounded-2xl p-4">
                            <div class="flex items-start gap-2 text-[11px] text-amber-700">
                                <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0 mt-0.5"></i>
                                <span>Jika nama kelas diubah, seluruh data siswa, guru, dan riwayat yang terkait akan otomatis diperbarui ke nama kelas yang baru.</span>
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
