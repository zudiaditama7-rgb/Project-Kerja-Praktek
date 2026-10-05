<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Digital - {{ $siswa->nama_siswa }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Force colors when printing */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        @media print {
            .no-print { display: none !important; }
            body { background: white; padding: 0; margin: 0; }
            .card-container { 
                box-shadow: none !important; 
                border: 1px solid #f3f4f6 !important;
                margin: 0 auto;
                -webkit-print-color-adjust: exact;
            }
            .glass { background: white !important; backdrop-filter: none !important; }
        }
        
        .card-bg {
            background: linear-gradient(135deg, #065F46 0%, #10B981 100%);
        }
        .glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center p-6">

    <div class="no-print mb-8 flex gap-4">
        <button onclick="window.print()" class="flex items-center gap-2 px-6 py-3 bg-[#065F46] text-white rounded-xl font-bold hover:bg-emerald-800 transition shadow-lg shadow-emerald-100">
            <i data-lucide="printer" class="w-5 h-5"></i>
            Cetak Kartu
        </button>
        @php
            $backRoute = match(auth()->user()->role) {
                'admin' => route('admin.siswa.index'),
                'wali_kelas' => route('wali.siswa.index'),
                'guru_pengganti' => route('guru_pengganti.siswa.index'),
                default => url()->previous(),
            };
        @endphp
        <a href="{{ $backRoute }}" class="flex items-center gap-2 px-6 py-3 bg-white text-gray-600 rounded-xl font-bold hover:bg-gray-50 transition border border-gray-200">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
            Kembali
        </a>
    </div>

    <!-- ID Card -->
    <div class="card-container relative w-[380px] h-[640px] glass rounded-[3rem] shadow-2xl overflow-hidden border border-white flex flex-col">
        <!-- Top Wave Decor -->
        <div class="card-bg h-44 w-full relative flex-shrink-0 flex flex-col items-center justify-center text-white p-6">
            <div class="absolute top-6 left-8 opacity-10">
                <i data-lucide="graduation-cap" class="w-32 h-32"></i>
            </div>
            <div class="z-10 text-center">
                <h1 class="text-lg font-800 tracking-tight leading-tight uppercase">Madrasah Ibtidaiyah</h1>
                <p class="text-[10px] font-bold text-emerald-100 tracking-[0.2em] uppercase mt-1">Sistem Presensi Digital</p>
            </div>
        </div>

        <!-- Student Photo Placeholder -->
        <div class="absolute top-32 left-1/2 -translate-x-1/2 z-20">
            <div class="w-36 h-36 rounded-[2.5rem] bg-white p-2 shadow-2xl">
                <div class="w-full h-full rounded-[2rem] bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center overflow-hidden border border-gray-100">
                    <i data-lucide="user" class="w-20 h-20 text-gray-300"></i>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="flex-1 flex flex-col justify-between pt-32 pb-6 px-10 text-center">
            <!-- Name & Status -->
            <div>
                <h2 class="text-2xl font-800 text-gray-900 leading-tight uppercase tracking-tight">{{ $siswa->nama_siswa }}</h2>
                <div class="inline-block px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-800 uppercase tracking-widest mt-2">
                    Siswa Aktif
                </div>
            </div>
            
            <!-- Details -->
            <div class="flex gap-3 my-4">
                <div class="flex-1 bg-gray-50/50 p-3 rounded-2xl border border-gray-100">
                    <p class="text-[9px] text-gray-400 font-800 uppercase mb-0.5">NIS</p>
                    <p class="text-xs font-bold text-gray-700">{{ $siswa->nis ?? '-' }}</p>
                </div>
                <div class="flex-1 bg-gray-50/50 p-3 rounded-2xl border border-gray-100">
                    <p class="text-[9px] text-gray-400 font-800 uppercase mb-0.5">Kelas</p>
                    <p class="text-xs font-bold text-gray-700">Kelas {{ $siswa->kelas }}</p>
                </div>
            </div>

            <!-- QR Code Container -->
            <div class="flex flex-col items-center">
                <div class="p-5 bg-white border-2 border-emerald-50 rounded-[2.5rem] shadow-sm mb-2">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $siswa->nis ?? $siswa->id }}" alt="QR Code" class="w-32 h-32">
                </div>
                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">Digital Student Identity</p>
            </div>
        </div>

        <!-- Bottom Footer -->
        <div class="w-full py-5 text-center bg-gray-50/50 border-t border-gray-100 flex-shrink-0">
            <p class="text-[10px] font-800 text-gray-400 tracking-widest uppercase">
                Tahun Ajaran {{ $siswa->tahun_ajaran }}
            </p>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
