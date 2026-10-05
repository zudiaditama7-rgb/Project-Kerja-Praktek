<x-app-layout>
    <x-slot name="header">Lihat Dokumen Pengajuan</x-slot>

    <div class="max-w-5xl mx-auto">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6">
            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                        <i data-lucide="file-text" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Dokumen Pendukung Pengajuan</h2>
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500 font-medium mt-0.5">
                            <span>Pengaju: <span class="font-bold text-gray-700">{{ $tugas->waliKelas ? $tugas->waliKelas->name : ($tugas->guruMapel ? $tugas->guruMapel->name : '') }}</span></span>
                            <span class="text-gray-300">|</span>
                            <span>Kelas: <span class="font-bold text-gray-700">{{ $tugas->kelas }}</span></span>
                            <span class="text-gray-300">|</span>
                            <span>Tanggal: <span class="font-bold text-gray-700">{{ \Carbon\Carbon::parse($tugas->tanggal)->format('d F Y') }}</span></span>
                            @if($tugas->waktu_mulai && $tugas->waktu_selesai)
                                <span class="text-gray-300">|</span>
                                <span>Waktu: <span class="font-bold text-gray-700">{{ substr($tugas->waktu_mulai, 0, 5) }} - {{ substr($tugas->waktu_selesai, 0, 5) }}</span></span>
                            @endif
                        </div>
                    </div>
                </div>
                @php
                    $prevUrl = url()->previous();
                    if ($prevUrl === url()->current()) {
                        if (auth()->user()->role === 'admin') {
                            $backRoute = route('admin.tugas_pengganti.index');
                        } elseif (auth()->user()->role === 'wali_kelas') {
                            $backRoute = route('wali.tugas_pengganti.index');
                        } elseif (auth()->user()->role === 'guru_mapel') {
                            $backRoute = route('guru_mapel.tugas_pengganti.index');
                        } else {
                            $backRoute = route('kepsek.dashboard');
                        }
                    } else {
                        $backRoute = $prevUrl;
                    }
                @endphp
                <a href="{{ $backRoute }}" class="flex items-center gap-2 px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition shadow-sm text-sm">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Kembali
                </a>
            </div>

            <!-- Info Alasan -->
            <div class="px-6 py-4 bg-amber-50/60 border-b border-amber-100">
                <div class="flex items-start gap-2">
                    <i data-lucide="info" class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0"></i>
                    <div>
                        <span class="text-xs font-bold text-amber-700 uppercase tracking-wide">Alasan Berhalangan</span>
                        <p class="text-sm text-amber-900 mt-0.5">{{ $tugas->alasan }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Document Preview -->
            <div class="p-6 bg-white flex justify-center items-center min-h-[60vh]">
                @php
                    $ext = pathinfo($tugas->document_path, PATHINFO_EXTENSION);
                @endphp
                
                @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png']))
                    <img src="{{ asset('storage/' . $tugas->document_path) }}" alt="Dokumen Pengajuan" class="max-w-full rounded-xl shadow-sm border border-gray-100 object-contain max-h-[70vh]">
                @elseif(strtolower($ext) === 'pdf')
                    <iframe src="{{ asset('storage/' . $tugas->document_path) }}" class="w-full rounded-xl border border-gray-200 shadow-sm" style="height: 75vh;"></iframe>
                @else
                    <div class="text-center p-12 bg-gray-50 rounded-2xl border border-gray-100">
                        <i data-lucide="file" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
                        <h3 class="text-lg font-bold text-gray-700 mb-2">Preview Tidak Tersedia</h3>
                        <p class="text-gray-500 text-sm mb-4">Format file ini tidak dapat ditampilkan langsung di browser.</p>
                        <a href="{{ asset('storage/' . $tugas->document_path) }}" target="_blank" class="inline-flex items-center gap-2 px-6 py-3 bg-[#065F46] text-white rounded-xl font-bold hover:bg-[#044e39] transition">
                            <i data-lucide="download" class="w-4 h-4"></i>
                            Download File
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
