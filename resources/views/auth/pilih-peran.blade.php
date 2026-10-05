<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Akun Anda memiliki lebih dari satu peran. Silakan pilih peran yang ingin Anda gunakan untuk sesi ini.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if ($errors->any())
        <div class="mb-4 text-sm text-red-600">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('switch-role') }}">
        @csrf

        <div class="grid grid-cols-1 gap-4 mt-4">
            @foreach($roles as $role)
                <button type="submit" name="role" value="{{ $role }}" class="flex items-center justify-between px-4 py-3 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out">
                    <span class="text-sm font-medium text-gray-700">
                        @if($role == 'admin') Administrator
                        @elseif($role == 'wali_kelas') Wali Kelas
                        @elseif($role == 'guru_mapel') Guru Mata Pelajaran
                        @elseif($role == 'guru_pengganti') Guru Pengganti
                        @elseif($role == 'kepala_sekolah') Kepala Sekolah
                        @else {{ ucfirst(str_replace('_', ' ', $role)) }}
                        @endif
                    </span>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            @endforeach
        </div>

        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('logout') }}"
               onclick="event.preventDefault();
                             document.getElementById('logout-form').submit();">
                {{ __('Batal & Logout') }}
            </a>
        </div>
    </form>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</x-guest-layout>
