<x-app-layout>
    <x-slot name="header">Profil Saya</x-slot>

    <div class="max-w-6xl mx-auto space-y-8">
        <!-- Top Profile Banner Card -->
        <div class="bg-white dark:bg-[#0f172a] rounded-3xl p-8 border border-gray-100 dark:border-white/5 shadow-sm premium-card flex flex-col md:flex-row items-center gap-6">
            <!-- Avatar -->
            @if(auth()->user()->avatar)
                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="w-24 h-24 rounded-3xl object-cover shadow-lg shadow-emerald-950/20 border-2 border-emerald-400/20" alt="Avatar">
            @else
                <div class="w-24 h-24 rounded-3xl bg-gradient-to-br from-[#065F46] to-[#10B981] flex items-center justify-center text-white text-3xl font-extrabold shadow-lg shadow-emerald-950/20">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            @endif
            
            <!-- User Info -->
            <div class="text-center md:text-left flex-1">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ auth()->user()->name }}</h3>
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-3">
                    <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 rounded-full text-xs font-bold uppercase tracking-wider">
                        {{ str_replace('_', ' ', auth()->user()->role) }}
                    </span>
                    @if(auth()->user()->kelas)
                        <span class="px-3 py-1 bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 rounded-full text-xs font-bold">
                            Kelas {{ auth()->user()->kelas }}
                        </span>
                    @endif
                    <span class="text-xs text-gray-400 dark:text-gray-500 font-mono">@username: {{ auth()->user()->username }}</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Column: Update Profile -->
            <div class="bg-white dark:bg-[#0f172a] p-8 rounded-3xl border border-gray-100 dark:border-white/5 shadow-sm premium-card">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-50 dark:border-white/5">
                    <div class="p-2.5 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-xl">
                        <i data-lucide="user-cog" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white">Informasi Profil</h4>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Perbarui nama lengkap dan username akun Anda.</p>
                    </div>
                </div>
                
                @include('profile.partials.update-profile-information-form')
            </div>

            <!-- Right Column: Update Password -->
            <div class="bg-white dark:bg-[#0f172a] p-8 rounded-3xl border border-gray-100 dark:border-white/5 shadow-sm premium-card">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-50 dark:border-white/5">
                    <div class="p-2.5 bg-yellow-50 dark:bg-yellow-500/10 text-yellow-600 dark:text-yellow-400 rounded-xl">
                        <i data-lucide="key-round" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white">Ubah Password</h4>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Pastikan akun Anda menggunakan password yang aman.</p>
                    </div>
                </div>

                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Danger Zone: Delete User -->
        <div class="bg-white dark:bg-[#0f172a] p-8 rounded-3xl border border-red-100/50 dark:border-red-500/10 shadow-sm premium-card">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-50 dark:border-white/5">
                <div class="p-2.5 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 rounded-xl">
                    <i data-lucide="shield-alert" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-red-900 dark:text-red-200">Hapus Akun</h4>
                    <p class="text-xs text-red-700/60 dark:text-red-400/60">Tindakan tidak dapat dibatalkan. Hapus akun Anda secara permanen.</p>
                </div>
            </div>

            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
