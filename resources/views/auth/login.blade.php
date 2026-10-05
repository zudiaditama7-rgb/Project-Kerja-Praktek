<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Username -->
        <div>
            <label for="username" class="block font-semibold text-xs text-emerald-100/90 mb-1.5 uppercase tracking-wider">
                <i data-lucide="user" class="w-3 h-3 inline mb-0.5 mr-1 opacity-70"></i>
                Username
            </label>
            <div class="relative">
                <x-text-input id="username" 
                    class="block w-full pl-4 pr-4 py-2.5 rounded-xl border-0 bg-white/10 text-white text-sm placeholder-white/30 focus:bg-white/15 focus:ring-2 focus:ring-emerald-400/50 transition-all duration-200" 
                    type="text" 
                    name="username" 
                    :value="old('username')" 
                    placeholder="Masukkan username..." 
                    required autofocus autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('username')" class="mt-2 text-xs" />
        </div>

        <!-- Password -->
        <div class="mt-4" x-data="{ showPassword: false }">
            <label for="password" class="block font-semibold text-xs text-emerald-100/90 mb-1.5 uppercase tracking-wider">
                <i data-lucide="lock" class="w-3 h-3 inline mb-0.5 mr-1 opacity-70"></i>
                Password
            </label>

            <div class="relative">
                <x-text-input id="password" 
                    class="block w-full pl-4 pr-11 py-2.5 rounded-xl border-0 bg-white/10 text-white text-sm placeholder-white/30 focus:bg-white/15 focus:ring-2 focus:ring-emerald-400/50 transition-all duration-200"
                    type="password"
                    x-bind:type="showPassword ? 'text' : 'password'"
                    name="password"
                    placeholder="Masukkan password..."
                    required autocomplete="current-password" />
                
                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-white/40 hover:text-emerald-300 transition-colors duration-200">
                    <i x-show="!showPassword" data-lucide="eye" class="w-4 h-4"></i>
                    <i x-show="showPassword" data-lucide="eye-off" class="w-4 h-4"></i>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-white/20 bg-white/10 text-emerald-500 shadow-sm focus:ring-emerald-500/50 focus:ring-offset-0 w-4 h-4" name="remember">
                <span class="ms-2 text-xs text-emerald-100/80 font-medium">Ingat Saya</span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="mt-5">
            <button type="submit" class="w-full py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl font-bold text-sm hover:from-emerald-400 hover:to-emerald-500 shadow-lg shadow-emerald-950/40 transition-all duration-300 flex items-center justify-center gap-2 active:scale-[0.98]">
                <i data-lucide="log-in" class="w-4 h-4"></i>
                Masuk Ke Sistem
            </button>
        </div>
    </form>
</x-guest-layout>
