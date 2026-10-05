<x-app-layout>
    <x-slot name="header">Lihat Dokumen Presensi</x-slot>

    <div class="max-w-5xl mx-auto">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-50 overflow-hidden mb-6">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Surat Keterangan {{ ucfirst($presensi->status) }}</h2>
                    <p class="text-sm text-gray-500 font-medium">Siswa: <span class="font-bold text-gray-700">{{ $presensi->siswa->nama_siswa }}</span> | Tanggal: {{ \Carbon\Carbon::parse($presensi->tanggal)->format('d F Y') }}</p>
                </div>
                <button onclick="window.history.back()" class="flex items-center gap-2 px-6 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition shadow-sm">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Kembali
                </button>
            </div>
            
            <div class="p-6 bg-white flex justify-center items-center min-h-[60vh]">
                @php
                    $ext = pathinfo($presensi->document_path, PATHINFO_EXTENSION);
                @endphp
                
                @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png']))
                    <img src="{{ asset('storage/' . $presensi->document_path) }}" alt="Dokumen Presensi" class="max-w-full rounded-xl shadow-sm border border-gray-100 object-contain max-h-[70vh]">
                @elseif(strtolower($ext) === 'pdf')
                    <iframe src="{{ asset('storage/' . $presensi->document_path) }}" class="w-full rounded-xl border border-gray-200 shadow-sm" style="height: 75vh;"></iframe>
                @else
                    <div class="text-center p-12 bg-gray-50 rounded-2xl border border-gray-100">
                        <i data-lucide="file" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
                        <h3 class="text-lg font-bold text-gray-700 mb-2">Preview Tidak Tersedia</h3>
                        <p class="text-gray-500 text-sm mb-4">Format file ini tidak dapat ditampilkan langsung di browser.</p>
                        <a href="{{ asset('storage/' . $presensi->document_path) }}" target="_blank" class="inline-flex items-center gap-2 px-6 py-3 bg-[#065F46] text-white rounded-xl font-bold hover:bg-[#044e39] transition">
                            <i data-lucide="download" class="w-4 h-4"></i>
                            Download File
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
