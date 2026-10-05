<x-app-layout>
    <x-slot name="header">Scan QR Presensi Siswa</x-slot>

    <div class="max-w-xl mx-auto">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-50 overflow-hidden">
            <div class="p-8 text-center border-b border-gray-50">
                <h3 class="text-xl font-bold text-gray-900 mb-1">Arahkan QR Code ke Kamera</h3>
                <p class="text-sm text-gray-400 font-medium italic">Pastikan QR Code terlihat jelas dan pencahayaan cukup</p>
            </div>

            <div class="p-8">
                <!-- Scanner Container -->
                <div id="reader" style="width: 100%;"></div>

                <!-- Result Message -->
                <div id="scan-result" class="hidden mt-4">
                    <div id="result-alert" class="p-4 rounded-2xl flex items-center gap-4 mb-4">
                        <div id="result-icon" class="w-12 h-12 rounded-xl flex items-center justify-center text-white shadow-lg"></div>
                        <div class="flex-1">
                            <h4 id="result-title" class="font-bold text-sm"></h4>
                            <p id="result-message" class="text-xs font-medium"></p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 mt-4">
                    @php
                        $backRoute = auth()->user()->role === 'wali_kelas' ? route('wali.presensi.index') : route('guru_pengganti.presensi.index');
                        $submitRoute = auth()->user()->role === 'wali_kelas' ? route('wali.presensi.scan_submit') : route('guru_pengganti.presensi.scan_submit');
                    @endphp
                    <a href="{{ $backRoute }}" class="flex-1 py-4 bg-gray-100 text-gray-600 rounded-2xl font-bold hover:bg-gray-200 transition text-center text-sm">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var scanResult = document.getElementById('scan-result');
            var resultAlert = document.getElementById('result-alert');
            var resultIcon = document.getElementById('result-icon');
            var resultTitle = document.getElementById('result-title');
            var resultMessage = document.getElementById('result-message');
            var submitRoute = "{{ $submitRoute }}";
            var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            var isScanning = true;

            function showResult(success, message) {
                scanResult.classList.remove('hidden');
                resultTitle.innerText = success ? 'BERHASIL' : 'GAGAL';
                resultMessage.innerText = message;
                if (success) {
                    resultAlert.className = 'p-4 rounded-2xl flex items-center gap-4 mb-4 bg-green-50 border border-green-100';
                    resultIcon.className = 'w-12 h-12 rounded-xl flex items-center justify-center text-white shadow-lg bg-green-600';
                    resultIcon.innerHTML = '<i data-lucide="check-circle" class="w-6 h-6"></i>';
                } else {
                    resultAlert.className = 'p-4 rounded-2xl flex items-center gap-4 mb-4 bg-red-50 border border-red-100';
                    resultIcon.className = 'w-12 h-12 rounded-xl flex items-center justify-center text-white shadow-lg bg-red-600';
                    resultIcon.innerHTML = '<i data-lucide="alert-circle" class="w-6 h-6"></i>';
                }
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }

            function hideResult() {
                scanResult.classList.add('hidden');
            }

            function onScanSuccess(decodedText) {
                if (!isScanning) return;
                isScanning = false;

                try {
                    var audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
                    audio.play().catch(function(){});
                } catch(e) {}

                fetch(submitRoute, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ qr_data: decodedText })
                })
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    showResult(data.success, data.message);
                    setTimeout(function() {
                        hideResult();
                        isScanning = true;
                    }, 3000);
                })
                .catch(function(error) {
                    showResult(false, 'Terjadi kesalahan sistem.');
                    setTimeout(function() {
                        hideResult();
                        isScanning = true;
                    }, 3000);
                });
            }

            // Gunakan Html5QrcodeScanner yang lebih stabil menangani izin dan hardware
            var html5QrcodeScanner = new Html5QrcodeScanner(
                "reader",
                { 
                    fps: 15, 
                    qrbox: { width: 300, height: 300 },
                    rememberLastUsedCamera: true,
                    showTorchButtonIfSupported: true,
                    videoConstraints: {
                        width: { min: 640, ideal: 1280, max: 1920 },
                        height: { min: 480, ideal: 720, max: 1080 }
                    }
                },
                false
            );
            html5QrcodeScanner.render(onScanSuccess, function(errorMessage) { /* ignore */ });
        });
    </script>
</x-app-layout>
