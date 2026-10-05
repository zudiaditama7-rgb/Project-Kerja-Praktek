<section>
    <form method="post" action="{{ route('password.update') }}" class="space-y-6" x-data="{ showCurrent: false, showNew: false, showConfirm: false }">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Password Saat Ini</label>
            <div class="relative text-gray-900 dark:text-white">
                <input id="update_password_current_password" name="current_password" type="password" :type="showCurrent ? 'text' : 'password'" class="w-full pl-4 pr-12 py-3 rounded-xl border border-gray-200 dark:border-white/10 dark:bg-gray-800/40 text-gray-900 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500 transition" placeholder="Masukkan Password Saat Ini..." autocomplete="current-password" />
                <button type="button" @click="showCurrent = !showCurrent" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-emerald-600 transition">
                    <i x-show="!showCurrent" data-lucide="eye" class="w-4 h-4"></i>
                    <i x-show="showCurrent" data-lucide="eye-off" class="w-4 h-4"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-xs text-red-600" />
        </div>

        <div>
            <label for="update_password_password" class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Password Baru</label>
            <div class="relative text-gray-900 dark:text-white">
                <input id="update_password_password" name="password" type="password" :type="showNew ? 'text' : 'password'" class="w-full pl-4 pr-12 py-3 rounded-xl border border-gray-200 dark:border-white/10 dark:bg-gray-800/40 text-gray-900 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500 transition" placeholder="Masukkan Password Baru..." autocomplete="new-password" />
                <button type="button" @click="showNew = !showNew" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-emerald-600 transition">
                    <i x-show="!showNew" data-lucide="eye" class="w-4 h-4"></i>
                    <i x-show="showNew" data-lucide="eye-off" class="w-4 h-4"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-xs text-red-600" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Konfirmasi Password</label>
            <div class="relative text-gray-900 dark:text-white">
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" :type="showConfirm ? 'text' : 'password'" class="w-full pl-4 pr-12 py-3 rounded-xl border border-gray-200 dark:border-white/10 dark:bg-gray-800/40 text-gray-900 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500 transition" placeholder="Masukkan Konfirmasi Password..." autocomplete="new-password" />
                <button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-emerald-600 transition">
                    <i x-show="!showConfirm" data-lucide="eye" class="w-4 h-4"></i>
                    <i x-show="showConfirm" data-lucide="eye-off" class="w-4 h-4"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-xs text-red-600" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="px-6 py-3 bg-[#065F46] text-white font-bold text-sm rounded-xl hover:bg-emerald-800 shadow-lg shadow-emerald-100 dark:shadow-none transition flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                Ubah Password
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-xs font-semibold text-emerald-600 flex items-center gap-1"
                >
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    Password berhasil diubah!
                </p>
            @endif
        </div>
    </form>
</section>
