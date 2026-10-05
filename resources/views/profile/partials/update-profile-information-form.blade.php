<section>
    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('patch')

        <!-- Avatar Upload Section -->
        <div x-data="{ 
            avatarPreview: '{{ $user->avatar ? asset('storage/' . $user->avatar) : '' }}',
            removeAvatar: false,
            triggerFileSelect() {
                this.$refs.avatarInput.click();
            },
            onFileChange(e) {
                const file = e.target.files[0];
                if (file) {
                    this.removeAvatar = false;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.avatarPreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            },
            clearAvatar() {
                this.avatarPreview = '';
                this.removeAvatar = true;
                this.$refs.avatarInput.value = '';
            }
        }" class="flex flex-col sm:flex-row items-center gap-6 p-4 bg-gray-50/50 dark:bg-gray-800/10 rounded-2xl border border-gray-100 dark:border-white/5 mb-6">
            <!-- Avatar Preview -->
            <div class="relative shrink-0">
                <template x-if="avatarPreview">
                    <img :src="avatarPreview" class="w-20 h-20 rounded-2xl object-cover border-2 border-emerald-500/20 shadow-md" alt="Avatar Preview">
                </template>
                <template x-if="!avatarPreview">
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-[#065F46] to-[#10B981] flex items-center justify-center text-white text-2xl font-extrabold shadow-md">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                </template>
            </div>

            <!-- Controls -->
            <div class="flex-1 text-center sm:text-left space-y-2">
                <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Foto Profil</label>
                <div class="flex flex-wrap gap-2 justify-center sm:justify-start">
                    <!-- Hidden input for file selection -->
                    <input type="file" x-ref="avatarInput" name="avatar" class="hidden" accept="image/*" @change="onFileChange">
                    <!-- Hidden input to flag removal -->
                    <input type="hidden" name="remove_avatar" :value="removeAvatar ? '1' : '0'">

                    <button type="button" @click="triggerFileSelect" class="px-4 py-2.5 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 rounded-xl text-xs font-bold hover:bg-emerald-100 dark:hover:bg-emerald-500/20 transition flex items-center gap-1.5 border border-emerald-100/30">
                        <i data-lucide="upload" class="w-4 h-4"></i>
                        Pilih Foto Baru
                    </button>

                    <button type="button" x-show="avatarPreview" @click="clearAvatar" class="px-4 py-2.5 bg-red-50 dark:bg-red-500/10 text-red-700 dark:text-red-400 rounded-xl text-xs font-bold hover:bg-red-100 dark:hover:bg-red-500/20 transition flex items-center gap-1.5 border border-red-100/30" style="display: none;">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                        Hapus Foto
                    </button>
                </div>
                <p class="text-[10px] text-gray-400">Format PNG, JPG atau JPEG. Maksimal 2MB.</p>
                <x-input-error class="mt-1 text-xs text-red-600" :messages="$errors->get('avatar')" />
            </div>
        </div>

        <div>
            <label for="name" class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Nama Lengkap</label>
            <input id="name" name="name" type="text" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-white/10 dark:bg-gray-800/40 text-gray-900 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500 transition" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            <x-input-error class="mt-2 text-xs text-red-600" :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="username" class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Username</label>
            <input id="username" name="username" type="text" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-white/10 dark:bg-gray-800/40 text-gray-900 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500 transition" value="{{ old('username', $user->username) }}" required autocomplete="username" />
            <x-input-error class="mt-2 text-xs text-red-600" :messages="$errors->get('username')" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="px-6 py-3 bg-[#065F46] text-white font-bold text-sm rounded-xl hover:bg-emerald-800 shadow-lg shadow-emerald-100 dark:shadow-none transition flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                Simpan Perubahan
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-xs font-semibold text-emerald-600 flex items-center gap-1"
                >
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    Berhasil disimpan!
                </p>
            @endif
        </div>
    </form>
</section>
