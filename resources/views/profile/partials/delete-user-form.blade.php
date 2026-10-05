<section class="space-y-6">
    <div class="bg-red-50/50 dark:bg-red-950/20 border border-red-100 dark:border-red-900/40 p-6 rounded-2xl">
        <p class="text-sm text-red-800 dark:text-red-300 leading-relaxed font-medium">
            Setelah akun Anda dihapus, semua data dan informasi yang terkait akan dihapus secara permanen. Sebelum menghapus akun, pastikan Anda telah mengunduh data atau informasi yang ingin disimpan.
        </p>
    </div>

    <button
        type="button"
        class="px-6 py-3 bg-red-600 text-white font-bold text-sm rounded-xl hover:bg-red-700 shadow-lg shadow-red-100 dark:shadow-none transition flex items-center gap-2"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >
        <i data-lucide="trash-2" class="w-4 h-4"></i>
        Hapus Akun
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-8 bg-white dark:bg-[#0f172a]">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i data-lucide="alert-triangle" class="text-red-500 w-5 h-5"></i>
                Apakah Anda yakin ingin menghapus akun ini?
            </h2>

            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                Setelah akun dihapus, semua data akan hilang secara permanen dan tidak dapat dikembalikan. Silakan masukkan password Anda untuk mengonfirmasi penghapusan akun.
            </p>

            <div class="mt-6">
                <label for="password" class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-white/10 dark:bg-gray-800/40 text-gray-900 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500 transition"
                    placeholder="Masukkan Password Anda..."
                    required
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-xs text-red-600" />
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-5 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-sm rounded-xl transition">
                    Batal
                </button>

                <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold text-sm rounded-xl transition shadow-lg shadow-red-100 dark:shadow-none flex items-center gap-2">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    Ya, Hapus Akun
                </button>
            </div>
        </form>
    </x-modal>
</section>
