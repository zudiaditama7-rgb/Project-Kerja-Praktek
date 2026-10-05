<x-app-layout>
    <x-slot name="header">Manajemen User</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Form Add User -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-50 sticky top-24 max-h-[calc(100vh-8rem)] overflow-y-auto custom-scrollbar">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Tambah User</h3>
                
                <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" required class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Username</label>
                        <input type="text" name="username" required class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Password</label>
                        <input type="password" name="password" required class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Role (Bisa pilih lebih dari satu)</label>
                        <div class="space-y-2">
                            <label class="inline-flex items-center w-full">
                                <input type="checkbox" name="roles[]" value="wali_kelas" class="role-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Wali Kelas</span>
                            </label>
                            <label class="inline-flex items-center w-full">
                                <input type="checkbox" name="roles[]" value="guru_mapel" class="role-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Guru Mapel</span>
                            </label>
                            <label class="inline-flex items-center w-full">
                                <input type="checkbox" name="roles[]" value="guru_pengganti" class="role-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Guru Pengganti</span>
                            </label>
                            <label class="inline-flex items-center w-full">
                                <input type="checkbox" name="roles[]" value="kepala_sekolah" class="role-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Kepala Sekolah</span>
                            </label>
                            <label class="inline-flex items-center w-full">
                                <input type="checkbox" name="roles[]" value="admin" class="role-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Admin</span>
                            </label>
                        </div>
                    </div>
                    <div id="kelasSection" class="hidden">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Kelas (untuk Wali Kelas)</label>
                        <select name="kelas" class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($globalKelasList as $kls)
                                <option value="{{ $kls->nama_kelas }}">Kelas {{ $kls->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="kelasMapelSection" class="hidden">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Kelas yang Diampu (untuk Guru Mapel)</label>
                        <div class="grid grid-cols-2 gap-2 border border-gray-200 rounded-xl p-3 bg-gray-50/50">
                            @foreach($globalKelasList as $kls)
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="kelas_mapel[]" value="{{ $kls->nama_kelas }}" class="rounded border-gray-300 text-emerald-600 shadow-sm focus:border-emerald-300 focus:ring focus:ring-emerald-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-xs font-medium text-gray-700">Kelas {{ $kls->nama_kelas }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1 italic">Bisa pilih lebih dari satu kelas</p>
                    </div>
                    <button type="submit" class="w-full py-4 bg-[#065F46] text-white rounded-2xl font-bold hover:bg-emerald-800 shadow-xl shadow-emerald-100 transition">
                        Simpan User
                    </button>
                </form>
            </div>
        </div>

        <!-- User List Table -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-50 overflow-hidden flex flex-col max-h-[calc(100vh-8rem)]">
                <div class="p-6 border-b border-gray-100 shrink-0">
                    <h3 class="text-xl font-bold text-gray-900">Daftar Pengguna</h3>
                </div>
                <div class="overflow-x-auto overflow-y-auto custom-scrollbar flex-1 relative">
                    <table class="w-full text-left border-separate border-spacing-0">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-[#065F46] text-white text-xs uppercase tracking-wider font-bold border-b-0 [&>th:first-child]:rounded-tl-2xl [&>th:last-child]:rounded-tr-2xl">
                                <th class="px-6 py-5 text-center w-16 whitespace-nowrap">No</th>
                                <th class="px-6 py-5 whitespace-nowrap">User</th>
                                <th class="px-6 py-5 whitespace-nowrap">Role</th>
                                <th class="px-6 py-5 whitespace-nowrap">Kelas</th>
                                <th class="px-6 py-5 text-right whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($users as $user)
                                <tr class="hover:bg-emerald-50/50 transition">
                                    <td class="px-6 py-4 text-center align-middle font-bold text-gray-400 text-sm">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-gray-800 text-sm">{{ $user->name }}</p>
                                        <p class="text-[10px] text-gray-400 font-mono italic">{{ $user->username }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1.5 items-center">
                                        @if(is_array($user->roles))
                                            @foreach(array_unique($user->roles) as $r)
                                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide border shadow-sm @if($r === 'wali_kelas') bg-blue-50 text-blue-700 border-blue-200 @elseif($r === 'kepala_sekolah') bg-purple-50 text-purple-700 border-purple-200 @elseif($r === 'guru_pengganti') bg-orange-50 text-orange-700 border-orange-200 @elseif($r === 'admin') bg-red-50 text-red-700 border-red-200 @else bg-emerald-50 text-emerald-700 border-emerald-200 @endif">
                                                    {{ str_replace('_', ' ', $r) }}
                                                </span>
                                            @endforeach
                                        @elseif($user->role)
                                            <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide border bg-gray-50 text-gray-700 border-gray-200 shadow-sm">
                                                {{ str_replace('_', ' ', $user->role) }}
                                            </span>
                                        @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-600">
                                        {{ $user->kelas ? 'Kelas '.$user->kelas : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit / Rotasi">
                                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                                            </a>
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus user ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        const roleCheckboxes = document.querySelectorAll('.role-checkbox');
        const kelasSection = document.getElementById('kelasSection');
        const kelasMapelSection = document.getElementById('kelasMapelSection');

        function toggleKelasSection() {
            let isWaliKelas = false;
            let isGuruMapel = false;
            
            roleCheckboxes.forEach(cb => {
                if (cb.value === 'wali_kelas' && cb.checked) {
                    isWaliKelas = true;
                }
                if (cb.value === 'guru_mapel' && cb.checked) {
                    isGuruMapel = true;
                }
            });
            
            if (isWaliKelas) {
                kelasSection.classList.remove('hidden');
            } else {
                kelasSection.classList.add('hidden');
            }
            
            if (isGuruMapel) {
                kelasMapelSection.classList.remove('hidden');
            } else {
                kelasMapelSection.classList.add('hidden');
            }
        }

        roleCheckboxes.forEach(cb => {
            cb.addEventListener('change', toggleKelasSection);
        });

        // Trigger on page load
        toggleKelasSection();
    </script>
    
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
