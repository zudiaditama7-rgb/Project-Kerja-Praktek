<x-app-layout>
    <x-slot name="header">Input Presensi</x-slot>

    <style>
        tr.is-missing td {
            background-color: #fef2f2 !important; /* bg-red-50 */
            transition: background-color 0.3s ease;
        }
    </style>

    <div x-data="{ showScanner: false }" @keydown.escape.window="showScanner = false; if(typeof stopScanner === 'function') stopScanner();">
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Input Presensi</h1>
            <p class="text-gray-600">Pilih kelas untuk memasukkan data presensi siswa</p>
        </div>
        @if(!$selectedKelas)
            <button type="button" onclick="alert('Pilih Kelas Terlebih Dahulu')" class="w-full md:w-auto flex items-center justify-center gap-2 px-6 py-4 bg-[#065F46] text-white rounded-2xl font-bold hover:bg-emerald-800 transition shadow-lg shadow-[#065F46]/20 z-50">
                <i data-lucide="qr-code" class="w-6 h-6"></i>
                Scan QR Presensi
            </button>
        @else
            <a href="{{ route('guru_pengganti.presensi.scan') }}" class="w-full md:w-auto flex items-center justify-center gap-2 px-6 py-4 bg-[#065F46] text-white rounded-2xl font-bold hover:bg-emerald-800 transition shadow-lg shadow-[#065F46]/20 z-50">
                <i data-lucide="qr-code" class="w-6 h-6"></i>
                Scan QR Presensi
            </a>
        @endif
    </div>

    @if($selectedKelas)
        <div class="bg-emerald-50 border-l-4 border-[#065F46] p-4 mb-6 rounded flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <p class="text-emerald-800 font-medium">Anda sedang memasukkan presensi untuk Kelas <strong>{{ $selectedKelas }}</strong></p>
            <a href="{{ route('guru_pengganti.presensi.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white text-[#065F46] border border-[#065F46] hover:bg-emerald-50 rounded-xl text-sm font-bold transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali Pilih Kelas
            </a>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @if(!$selectedKelas)
        <!-- Sidebar Kelas -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Pilih Kelas</h2>
                <div class="space-y-2">
                    @forelse($kelasList as $kelas)
                        <a href="{{ route('guru_pengganti.presensi.index', ['kelas' => $kelas]) }}" 
                           class="block p-3 rounded-lg font-medium transition @if($selectedKelas === $kelas) bg-[#065F46] text-white shadow-md shadow-[#065F46]/20 @else bg-emerald-50 text-[#065F46] hover:bg-emerald-100 @endif">
                            Kelas {{ $kelas }}
                        </a>
                    @empty
                        <div class="p-4 bg-gray-50 rounded-xl text-center">
                            <i data-lucide="calendar-x" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i>
                            <p class="text-gray-500 text-sm font-medium">Belum ada kelas yang ditugaskan hari ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        @endif

        <!-- Form Presensi -->
        <div class="{{ $selectedKelas ? 'lg:col-span-3' : 'lg:col-span-2' }}">
            @if($selectedKelas && count($siswas) > 0)
                <form method="POST" action="{{ route('guru_pengganti.presensi.store') }}" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-lg p-6" id="formPresensi" novalidate onsubmit="return validatePresensi(event)">
                    @csrf
                    <input type="hidden" name="kelas" value="{{ $selectedKelas }}">
                    <input type="hidden" name="tanggal" value="{{ $today }}">

                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-gray-800">Data Presensi Kelas {{ $selectedKelas }}</h2>
                        <p class="text-gray-600 text-sm">Tanggal: {{ Carbon\Carbon::parse($today)->translatedFormat('l, d F Y') }}</p>
                    </div>

                    @if($isLocked)
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl flex items-center gap-4 shadow-sm">
                        <div class="w-12 h-12 bg-red-100 text-red-600 rounded-xl flex items-center justify-center shrink-0">
                            <i data-lucide="lock" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h4 class="text-red-800 font-bold">Presensi Kelas Terkunci</h4>
                            <p class="text-red-600 text-sm">Kelas ini sudah dipresensi hari ini. Guru Pengganti tidak memiliki hak akses untuk mengubah data yang sudah tersimpan.</p>
                        </div>
                    </div>
                    @else
                    {{-- Input Nama Guru Pengganti --}}
                    <div class="mb-6 rounded-2xl border border-emerald-200 overflow-hidden shadow-sm">
                        <div class="p-4 bg-emerald-50 border-b border-emerald-100 flex items-center gap-3">
                            <div class="w-10 h-10 bg-emerald-100 text-[#065F46] rounded-xl flex items-center justify-center shrink-0 shadow-sm border border-emerald-200/50">
                                <i data-lucide="user-check" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="text-[#065F46] font-bold text-sm uppercase tracking-wider">Identitas Pencatat Presensi</h4>
                                <p class="text-emerald-700 text-xs mt-0.5">Nama Anda akan tercatat di riwayat kehadiran kelas ini sebagai pihak yang bertanggung jawab.</p>
                            </div>
                        </div>
                        <table class="w-full text-left text-sm bg-white">
                            <tbody>
                                <tr>
                                    <td class="w-1/3 lg:w-1/4 px-6 py-5 bg-gray-50/50 font-bold text-gray-600 border-r border-gray-100 align-middle">
                                        Nama Lengkap <span class="text-red-500">*</span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <input type="text" name="nama_pengganti" id="nama_pengganti" required placeholder="Ketik nama lengkap Anda di sini..." value="{{ old('nama_pengganti', auth()->user()->name) }}" class="w-full rounded-xl border-gray-200 focus:border-[#065F46] focus:ring-[#065F46] text-base font-bold text-gray-900 placeholder:text-gray-300 placeholder:font-normal bg-white shadow-sm transition-all hover:border-emerald-300">
                                        @error('nama_pengganti')
                                            <p class="text-red-500 text-xs mt-1.5 font-semibold"><i data-lucide="alert-circle" class="w-3 h-3 inline mr-1"></i>{{ $message }}</p>
                                        @enderror
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @endif

                    <div class="overflow-x-auto rounded-xl border border-gray-200">
                        <table class="w-full text-left border-separate border-spacing-0">
                            <thead>
                                <tr class="bg-[#065F46] text-white text-xs uppercase tracking-wider font-bold border-b-0 [&>th:first-child]:rounded-tl-2xl [&>th:last-child]:rounded-tr-2xl">
                                    <th class="px-6 py-5 text-center w-16">No</th>
                                    <th class="px-6 py-5">Nama Siswa</th>
                                    <th class="px-6 py-5 text-center w-[460px]">Status Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach($siswas as $siswa)
                                    @php
                                        $currentStatus = old('presensi.' . $siswa->id, $presensiToday->get($siswa->id) ?? '');
                                    @endphp
                                    <tr x-data="{ status: '{{ $currentStatus }}' }" data-nis="{{ $siswa->nis }}" class="hover:bg-emerald-50/50 transition">
                                        <td class="px-6 py-4 text-center align-top pt-5 font-bold text-gray-400 text-sm">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="px-6 py-4 align-top pt-5">
                                            <div class="flex flex-col">
                                                <span class="font-bold text-gray-900 text-sm">{{ $siswa->nama_siswa }}</span>
                                                <span class="text-xs text-gray-400 mt-0.5">NIS: {{ $siswa->nis ?: '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 align-top">
                                            <div class="flex flex-col items-center {{ $isLocked ? 'opacity-60 pointer-events-none' : '' }}">
                                                <div class="flex bg-gray-100/80 p-1.5 rounded-2xl gap-2 w-full max-w-md border border-gray-200/50">
                                                    <!-- Hadir -->
                                                    <label class="flex-1">
                                                        <input type="radio" x-model="status" name="presensi[{{ $siswa->id }}]" value="hadir" required class="peer sr-only" {{ $isLocked ? 'disabled' : '' }}>
                                                        <div class="py-2.5 rounded-xl text-center text-xs font-extrabold text-gray-500 peer-checked:bg-[#065F46] peer-checked:text-white peer-checked:shadow-md peer-checked:shadow-[#065F46]/20 hover:bg-white hover:text-gray-700 transition-all cursor-pointer flex items-center justify-center gap-1.5">
                                                            <i data-lucide="check" class="w-3.5 h-3.5 hidden peer-checked:block"></i>
                                                            HADIR
                                                        </div>
                                                    </label>
                                                    
                                                    <!-- Izin -->
                                                    <label class="flex-1">
                                                        <input type="radio" x-model="status" name="presensi[{{ $siswa->id }}]" value="izin" class="peer sr-only" {{ $isLocked ? 'disabled' : '' }}>
                                                        <div class="py-2.5 rounded-xl text-center text-xs font-extrabold text-gray-500 peer-checked:bg-amber-500 peer-checked:text-white peer-checked:shadow-md peer-checked:shadow-amber-100 hover:bg-white hover:text-gray-700 transition-all cursor-pointer">
                                                            IZIN
                                                        </div>
                                                    </label>
                                                    
                                                    <!-- Sakit -->
                                                    <label class="flex-1">
                                                        <input type="radio" x-model="status" name="presensi[{{ $siswa->id }}]" value="sakit" class="peer sr-only" {{ $isLocked ? 'disabled' : '' }}>
                                                        <div class="py-2.5 rounded-xl text-center text-xs font-extrabold text-gray-500 peer-checked:bg-blue-500 peer-checked:text-white peer-checked:shadow-md peer-checked:shadow-blue-100 hover:bg-white hover:text-gray-700 transition-all cursor-pointer">
                                                            SAKIT
                                                        </div>
                                                    </label>
                                                    
                                                    <!-- Alpha -->
                                                    <label class="flex-1">
                                                        <input type="radio" x-model="status" name="presensi[{{ $siswa->id }}]" value="alpha" class="peer sr-only" {{ $isLocked ? 'disabled' : '' }}>
                                                        <div class="py-2.5 rounded-xl text-center text-xs font-extrabold text-gray-500 peer-checked:bg-rose-600 peer-checked:text-white peer-checked:shadow-md peer-checked:shadow-rose-100 hover:bg-white hover:text-gray-700 transition-all cursor-pointer flex items-center justify-center gap-1.5">
                                                            <i data-lucide="x" class="w-3.5 h-3.5 hidden peer-checked:block"></i>
                                                            ALPHA
                                                        </div>
                                                    </label>
                                                </div>

                                                <!-- Upload Dokumen (Izin/Sakit) -->
                                                <div x-show="status === 'izin' || status === 'sakit'" x-transition class="mt-3 w-full max-w-md">
                                                    <div class="p-3 bg-blue-50/50 rounded-xl border border-blue-100/80 flex flex-col gap-1.5">
                                                        <span class="text-[10px] font-bold text-blue-700 uppercase tracking-wider">Upload Surat (Opsional)</span>
                                                        <input type="file" name="dokumen[{{ $siswa->id }}]" accept=".jpg,.jpeg,.png,.pdf" class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200 cursor-pointer" {{ $isLocked ? 'disabled' : '' }}>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if(!$isLocked)
                    <div class="mt-6 flex gap-3">
                        <button type="submit" class="flex-1 py-3 bg-[#065F46] text-white rounded-lg font-bold hover:bg-emerald-800 transition flex items-center justify-center gap-2">
                            <i data-lucide="check" class="w-5 h-5"></i>
                            Simpan Presensi
                        </button>
                        <a href="{{ route('guru_pengganti.presensi.index') }}" class="flex-1 py-3 bg-gray-500 text-white rounded-lg font-bold hover:bg-gray-600 transition flex items-center justify-center gap-2">
                            <i data-lucide="x" class="w-5 h-5"></i>
                            Batal
                        </a>
                    </div>
                    @endif
                </form>
            @elseif($selectedKelas && count($siswas) === 0)
                <div class="bg-white dark:bg-[#0f172a] border border-amber-100 dark:border-amber-500/10 rounded-3xl p-12 text-center shadow-sm premium-card">
                    <div class="w-16 h-16 bg-amber-50 dark:bg-amber-500/10 text-amber-500 rounded-2xl flex items-center justify-center mb-4 mx-auto">
                        <i data-lucide="alert-circle" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-200 mb-2">Tidak Ada Siswa Aktif</h3>
                    <p class="text-xs text-slate-400 dark:text-slate-500 max-w-sm mx-auto">Kelas {{ $selectedKelas }} tidak memiliki siswa aktif yang terdaftar untuk tahun ajaran ini.</p>
                </div>
            @else
                <div class="bg-white dark:bg-[#0f172a] border border-gray-100 dark:border-white/5 rounded-3xl p-12 text-center shadow-sm premium-card">
                    <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center mb-4 mx-auto">
                        @if(count($kelasList) > 0)
                            <i data-lucide="clipboard-signature" class="w-8 h-8"></i>
                        @else
                            <i data-lucide="calendar-x" class="w-8 h-8"></i>
                        @endif
                    </div>
                    @if(count($kelasList) > 0)
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-200 mb-2">Pilih Kelas Terlebih Dahulu</h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500 max-w-sm mx-auto">Silakan pilih salah satu kelas pada panel sebelah kiri untuk memulai pencatatan presensi siswa.</p>
                    @else
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-200 mb-2">Tidak Ada Tugas Hari Ini</h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500 max-w-sm mx-auto">Anda belum diberikan jadwal untuk menggantikan wali kelas pada hari ini. Silakan periksa halaman <strong>Dashboard</strong> Anda untuk melihat detail tanggal penugasan dari Admin.</p>
                    @endif
                </div>
            @endif
        </div>
        </div>
    </div>
    

    
    </div> <!-- end x-data wrapper -->

</x-app-layout>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function validatePresensi(e) {
        const form = e.target;
        
        // Manual validation for all radio groups
        const radios = form.querySelectorAll('input[type="radio"][name^="presensi["]');
        const groups = new Set();
        radios.forEach(r => {
            const match = r.name.match(/presensi\[(\d+)\]/);
            if (match) groups.add(match[1]);
        });

        let isValid = true;
        let firstMissing = null;

        groups.forEach(id => {
            const checked = form.querySelector(`input[type="radio"][name="presensi[${id}]"]:checked`);
            const row = form.querySelector(`input[type="radio"][name="presensi[${id}]"]`).closest('tr');
            
            if (!checked) {
                isValid = false;
                if (row) {
                    row.classList.add('is-missing');
                }
                if (!firstMissing) {
                    firstMissing = form.querySelector(`input[type="radio"][name="presensi[${id}]"]`);
                }
            } else {
                if (row) {
                    row.classList.remove('is-missing');
                }
            }
        });

        // Attach event listeners to clear red highlight immediately when selected
        radios.forEach(radio => {
            if (!radio.dataset.listenerAttached) {
                radio.addEventListener('change', () => {
                    const row = radio.closest('tr');
                    if (row) {
                        row.classList.remove('is-missing');
                    }
                });
                radio.dataset.listenerAttached = 'true';
            }
        });

        if (!isValid) {
            e.preventDefault();
            
            Swal.fire({
                icon: 'warning',
                title: 'Presensi Belum Lengkap!',
                html: '<p class="text-gray-600">Ada siswa yang belum Anda tentukan status kehadirannya.</p><p class="text-sm text-gray-500 mt-2">Mohon periksa kembali tabel presensi dan pastikan semua baris sudah terpilih.</p>',
                confirmButtonColor: '#065F46', // emerald-600 to 065F46
                confirmButtonText: '<div class="flex items-center gap-2"><i data-lucide="check-circle" class="w-4 h-4"></i> Oke, Saya Lengkapi</div>',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl font-bold px-6 py-3',
                },
                didOpen: () => {
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }
            }).then(() => {
                if (firstMissing) {
                    firstMissing.closest('tr').scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
            
            // Also un-set the global isSubmitting state if it got triggered
            window.dispatchEvent(new CustomEvent('stop-submitting'));
            return false;
        }
        return true;
    }
</script>
@endpush
