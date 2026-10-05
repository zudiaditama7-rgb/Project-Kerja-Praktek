<x-app-layout>
    <x-slot name="header">Input Presensi Harian</x-slot>

    <style>
        tr.is-missing td {
            background-color: #fef2f2 !important; /* bg-red-50 */
            transition: background-color 0.3s ease;
        }
    </style>

    <div class="max-w-5xl mx-auto" x-data="{ showScanner: false }" @keydown.escape.window="showScanner = false; if(typeof stopScanner === 'function') stopScanner();">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-50 overflow-hidden">
            <div class="p-8 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Kelas {{ auth()->user()->kelas }}</h3>
                    <p class="text-sm text-gray-500 font-medium">Tanggal: <span class="text-blue-600 font-bold">{{ \Carbon\Carbon::parse($today)->format('d F Y') }}</span></p>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('wali.presensi.scan') }}" class="flex items-center gap-2 px-6 py-3 bg-emerald-600 text-white rounded-2xl font-bold text-sm hover:bg-emerald-700 transition shadow-lg shadow-emerald-100 z-50">
                        <i data-lucide="qr-code" class="w-5 h-5"></i>
                        Scan QR Presensi
                    </a>
                    <div class="p-3 bg-blue-100 text-blue-700 rounded-2xl">
                        <i data-lucide="calendar" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>

            @if(count($presensiToday) > 0)
                @if($guruPengganti)
                {{-- Dipresensi oleh Guru Pengganti --}}
                <div class="mx-8 mt-8 p-5 bg-blue-50 border border-blue-200 rounded-2xl shadow-sm">
                    <div class="flex items-center gap-4 mb-3">
                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center shrink-0">
                            <i data-lucide="user-check" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h4 class="text-blue-800 font-bold">Presensi Diinput oleh Guru Pengganti</h4>
                            <p class="text-blue-600 text-sm">Presensi kelas ini hari ini telah dicatat oleh guru pengganti.</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-4 border border-blue-100 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-sm shadow-lg shadow-blue-100">
                            {{ substr($guruPengganti, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Pencatat Presensi</p>
                            <p class="text-base font-bold text-gray-800">{{ $guruPengganti }}</p>
                        </div>
                    </div>
                </div>
                @elseif($guruMapel)
                {{-- Dipresensi oleh Guru Mapel --}}
                <div class="mx-8 mt-8 p-5 bg-emerald-50 border border-emerald-200 rounded-2xl shadow-sm">
                    <div class="flex items-center gap-4 mb-3">
                        <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center shrink-0">
                            <i data-lucide="clipboard-list" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h4 class="text-emerald-800 font-bold">Presensi Diinput oleh Guru Mapel</h4>
                            <p class="text-emerald-600 text-sm">Presensi kelas ini telah dicatat oleh Guru Mapel. Anda dapat merubahnya jika terdapat penyesuaian kehadiran.</p>
                        </div>
                    </div>
                    <div class="mt-4 bg-white rounded-xl border border-emerald-100 overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-[#065F46] text-white text-[10px] uppercase tracking-wider font-bold">
                                    <th class="px-4 py-2 text-center w-12">#</th>
                                    <th class="px-4 py-2">Pencatat Presensi</th>
                                    <th class="px-4 py-2">Mata Pelajaran</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-emerald-50">
                                <tr class="hover:bg-emerald-50/50 transition">
                                    <td class="px-4 py-3 text-center">
                                        <div class="w-8 h-8 mx-auto rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white font-bold text-xs shadow-md shadow-emerald-100">
                                            {{ substr($guruMapel, 0, 1) }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="font-bold text-gray-800 text-sm">{{ $guruMapel }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100">
                                            <i data-lucide="book-open" class="w-3.5 h-3.5"></i>
                                            {{ $namaMapelDetail }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                {{-- Dipresensi oleh Wali Kelas sendiri --}}
                <div class="mx-8 mt-8 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-4">
                    <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center shrink-0">
                        <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="text-emerald-800 font-bold">Presensi Hari Ini Telah Disimpan</h4>
                        <p class="text-emerald-600 text-sm">Anda masih dapat mengubah status kehadiran siswa jika terdapat kesalahan sebelum hari berganti.</p>
                    </div>
                </div>
                @endif
            @endif

            <form action="{{ route('wali.presensi.store') }}" method="POST" enctype="multipart/form-data" class="{{ count($presensiToday) > 0 ? 'mt-4' : '' }}" id="formPresensi" novalidate onsubmit="return validatePresensi(event)">
                @csrf
                <input type="hidden" name="tanggal" value="{{ $today }}">
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-separate border-spacing-0">
                        <thead>
                            <tr class="bg-[#065F46] text-white text-xs uppercase tracking-wider font-bold border-b-0 [&>th:first-child]:rounded-tl-2xl [&>th:last-child]:rounded-tr-2xl">
                                <th class="px-6 py-5 text-center w-20">No</th>
                                <th class="px-6 py-5">Nama Siswa</th>
                                <th class="px-6 py-5 text-center w-[460px]">Status Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($siswas as $siswa)
                                @php
                                    $status = old('presensi.' . $siswa->id, $presensiToday[$siswa->id] ?? '');
                                @endphp
                                <tr x-data="{ status: '{{ $status }}' }" data-nis="{{ $siswa->nis }}" class="hover:bg-emerald-50/50 transition">
                                    <td class="px-6 py-4 text-center align-middle font-bold text-gray-400 text-sm">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-gray-900 text-sm">{{ $siswa->nama_siswa }}</span>
                                            <span class="text-xs text-gray-400 mt-0.5">NIS: {{ $siswa->nis ?: '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <div class="flex flex-col items-center">
                                            <div class="flex bg-gray-100/80 p-1.5 rounded-2xl gap-2 w-full max-w-md border border-gray-200/50">
                                                <!-- Hadir -->
                                                <label class="flex-1">
                                                    <input type="radio" x-model="status" name="presensi[{{ $siswa->id }}]" value="hadir" required class="peer sr-only">
                                                    <div class="py-2.5 rounded-xl text-center text-xs font-extrabold text-gray-500 peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:shadow-md peer-checked:shadow-emerald-100 hover:bg-white hover:text-gray-700 transition-all cursor-pointer flex items-center justify-center gap-1.5">
                                                        <i data-lucide="check" class="w-3.5 h-3.5 hidden peer-checked:block"></i>
                                                        HADIR
                                                    </div>
                                                </label>
                                                
                                                <!-- Izin -->
                                                <label class="flex-1">
                                                    <input type="radio" x-model="status" name="presensi[{{ $siswa->id }}]" value="izin" class="peer sr-only">
                                                    <div class="py-2.5 rounded-xl text-center text-xs font-extrabold text-gray-500 peer-checked:bg-amber-500 peer-checked:text-white peer-checked:shadow-md peer-checked:shadow-amber-100 hover:bg-white hover:text-gray-700 transition-all cursor-pointer">
                                                        IZIN
                                                    </div>
                                                </label>
                                                
                                                <!-- Sakit -->
                                                <label class="flex-1">
                                                    <input type="radio" x-model="status" name="presensi[{{ $siswa->id }}]" value="sakit" class="peer sr-only">
                                                    <div class="py-2.5 rounded-xl text-center text-xs font-extrabold text-gray-500 peer-checked:bg-blue-500 peer-checked:text-white peer-checked:shadow-md peer-checked:shadow-blue-100 hover:bg-white hover:text-gray-700 transition-all cursor-pointer">
                                                        SAKIT
                                                    </div>
                                                </label>
                                                
                                                <!-- Alpha -->
                                                <label class="flex-1">
                                                    <input type="radio" x-model="status" name="presensi[{{ $siswa->id }}]" value="alpha" class="peer sr-only">
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
                                                    <input type="file" name="dokumen[{{ $siswa->id }}]" accept=".jpg,.jpeg,.png,.pdf" class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200 cursor-pointer">
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-8 bg-gray-50/50 flex justify-end">
                    <button type="submit" class="px-10 py-4 bg-[#065F46] text-white rounded-2xl font-bold hover:bg-emerald-800 shadow-xl shadow-emerald-100 transition flex items-center gap-2">
                        <i data-lucide="{{ count($presensiToday) > 0 ? 'refresh-cw' : 'save' }}" class="w-5 h-5"></i>
                        {{ count($presensiToday) > 0 ? 'Perbarui Presensi' : 'Simpan Presensi' }}
                    </button>
                </div>
            </form>
        
    </div>
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
                confirmButtonColor: '#059669', // emerald-600
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
