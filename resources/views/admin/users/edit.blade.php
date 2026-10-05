<x-app-layout>
    <x-slot name="header">Edit / Rotasi Pengguna</x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100/80">
            <!-- Header Section -->
            <div class="flex justify-between items-center mb-8 border-b border-gray-100 pb-5">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Edit Profil & Jabatan</h3>
                    <p class="text-xs text-gray-400 font-medium mt-1">Sesuaikan informasi pengguna atau lakukan rotasi wali kelas</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 bg-gray-50 text-gray-600 rounded-xl font-bold hover:bg-gray-100 hover:text-gray-800 transition text-xs flex items-center gap-1.5 border border-gray-100 shadow-sm">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Kembali
                </a>
            </div>


            
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Lengkap -->
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">Nama Lengkap</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                                <i data-lucide="user" class="w-4 h-4"></i>
                            </span>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                                class="w-full pl-11 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm py-3 transition" 
                                placeholder="Nama lengkap guru">
                        </div>
                    </div>

                    <!-- Username -->
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">Username</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                                <i data-lucide="at-sign" class="w-4 h-4"></i>
                            </span>
                            <input type="text" name="username" value="{{ old('username', $user->username) }}" required 
                                class="w-full pl-11 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm py-3 transition"
                                placeholder="Username untuk login">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Role / Jabatan -->
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">Role (Bisa pilih lebih dari satu)</label>
                        <div class="space-y-2 mt-2">
                            @php $userRoles = array_unique(is_array($user->roles) ? $user->roles : ($user->role ? [$user->role] : [])); @endphp
                            <label class="inline-flex items-center w-full">
                                <input type="checkbox" name="roles[]" value="wali_kelas" class="role-checkbox rounded border-gray-300 text-emerald-600 shadow-sm focus:border-emerald-300 focus:ring focus:ring-emerald-200 focus:ring-opacity-50" {{ in_array('wali_kelas', old('roles', $userRoles)) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Wali Kelas</span>
                            </label>
                            <label class="inline-flex items-center w-full">
                                <input type="checkbox" name="roles[]" value="guru_mapel" class="role-checkbox rounded border-gray-300 text-emerald-600 shadow-sm focus:border-emerald-300 focus:ring focus:ring-emerald-200 focus:ring-opacity-50" {{ in_array('guru_mapel', old('roles', $userRoles)) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Guru Mapel</span>
                            </label>
                            <label class="inline-flex items-center w-full">
                                <input type="checkbox" name="roles[]" value="guru_pengganti" class="role-checkbox rounded border-gray-300 text-emerald-600 shadow-sm focus:border-emerald-300 focus:ring focus:ring-emerald-200 focus:ring-opacity-50" {{ in_array('guru_pengganti', old('roles', $userRoles)) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Guru Pengganti</span>
                            </label>
                            <label class="inline-flex items-center w-full">
                                <input type="checkbox" name="roles[]" value="kepala_sekolah" class="role-checkbox rounded border-gray-300 text-emerald-600 shadow-sm focus:border-emerald-300 focus:ring focus:ring-emerald-200 focus:ring-opacity-50" {{ in_array('kepala_sekolah', old('roles', $userRoles)) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Kepala Sekolah</span>
                            </label>
                        </div>
                    </div>

                    <!-- Kelas Section -->
                    <div id="kelasSection" class="{{ !in_array('wali_kelas', is_array($user->roles) ? $user->roles : [$user->role]) ? 'hidden' : '' }}">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Kelas (untuk Wali Kelas)</label>
                        <select name="kelas" class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm py-3">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($globalKelasList as $kls)
                                <option value="{{ $kls->nama_kelas }}" {{ $user->kelas == $kls->nama_kelas ? 'selected' : '' }}>Kelas {{ $kls->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="kelasMapelSection" class="{{ !in_array('guru_mapel', is_array($user->roles) ? $user->roles : [$user->role]) ? 'hidden' : '' }}">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Kelas yang Diampu (untuk Guru Mapel)</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 border border-gray-200 rounded-xl p-4 bg-gray-50/50">
                            @php
                                $userKelasMapel = is_array($user->kelas_mapel) ? $user->kelas_mapel : [];
                            @endphp
                            @foreach($globalKelasList as $kls)
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="kelas_mapel[]" value="{{ $kls->nama_kelas }}" 
                                        {{ in_array($kls->nama_kelas, $userKelasMapel) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-emerald-600 shadow-sm focus:border-emerald-300 focus:ring focus:ring-emerald-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm font-medium text-gray-700">Kelas {{ $kls->nama_kelas }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 mt-2 italic">Centang kelas mana saja yang boleh diisi presensinya oleh Guru Mapel ini.</p>
                    </div>
                </div>

                <!-- Password Baru -->
                <div class="space-y-2 border-t border-gray-50 pt-5">
                    <label class="block text-sm font-bold text-gray-700">Password Baru <span class="text-xs text-gray-400 font-normal">(Kosongkan jika tidak ingin diganti)</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </span>
                        <input type="password" name="password" placeholder="Minimal 6 karakter" 
                            class="w-full pl-11 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm py-3 transition">
                    </div>
                </div>

                <!-- Submit Button (EMERALD GREEN THEME) -->
                <button type="submit" 
                    class="w-full mt-4 py-4 bg-[#065F46] text-white rounded-2xl font-bold hover:bg-[#044e39] shadow-xl shadow-emerald-100 hover:shadow-emerald-200/50 transition duration-300 flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    Simpan Perubahan & Rotasi
                </button>
            </form>
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
</x-app-layout>
