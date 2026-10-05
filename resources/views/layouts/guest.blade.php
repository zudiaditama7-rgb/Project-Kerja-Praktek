<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://unpkg.com/lucide@latest"></script>
        
        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
            
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-6px); }
            }
            
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            .login-card {
                animation: fadeInUp 0.5s ease-out;
            }
            
            .logo-float {
                animation: float 4s ease-in-out infinite;
            }

            /* Custom Chrome Autofill Style Fix */
            input:-webkit-autofill,
            input:-webkit-autofill:hover, 
            input:-webkit-autofill:focus, 
            input:-webkit-autofill:active {
                -webkit-background-clip: text;
                -webkit-text-fill-color: #ffffff !important;
                transition: background-color 5000s ease-in-out 0s;
                box-shadow: inset 0 0 20px 20px rgba(255, 255, 255, 0.05);
            }
        </style>
    </head>
    <body class="antialiased">
        <div class="min-h-screen flex flex-col justify-center items-center p-4 relative overflow-hidden" style="background: url('/images/foto sekolah.png') center/cover no-repeat fixed;">
            
            <!-- Dark Overlay for Premium Contrast -->
            <div class="absolute inset-0 bg-gradient-to-br from-black/70 via-emerald-950/60 to-black/75"></div>
            
            <!-- Decorative Floating Orbs -->
            <div class="absolute top-20 left-20 w-72 h-72 bg-emerald-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-20 w-96 h-96 bg-emerald-400/5 rounded-full blur-3xl"></div>

            <!-- Login Card (Compact Max-Width for standard school feel) -->
            <div class="z-10 w-full sm:max-w-[340px] login-card">
                <div class="w-full px-6 py-8 bg-gradient-to-b from-[#064e3b]/95 to-[#022c22]/95 backdrop-blur-xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] overflow-hidden rounded-[2rem] border border-white/10">
                    
                    <!-- Logo & Title (Slightly smaller & more elegant) -->
                    <div class="flex flex-col items-center mb-6">
                        <div class="bg-white p-3 rounded-2xl shadow-xl shadow-emerald-900/30 mb-4 logo-float">
                            <x-application-logo class="w-12 h-12 object-contain" />
                        </div>
                        <h1 class="text-xl font-extrabold text-white tracking-tight">PRESENSI MI</h1>
                        <p class="text-emerald-200/60 font-medium mt-1 text-xs">Sistem Informasi Presensi Siswa</p>
                    </div>
                    
                    {{ $slot }}
                </div>
            </div>
        </div>
        <script>
            lucide.createIcons();
        </script>
    </body>
</html>
