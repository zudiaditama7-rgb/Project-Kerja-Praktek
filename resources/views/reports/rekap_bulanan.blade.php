@php
    $logoNuPath = public_path('images/logo_nu.jpeg');
    $logoNuBase64 = '';
    if (file_exists($logoNuPath)) {
        $logoNuData = file_get_contents($logoNuPath);
        $logoNuBase64 = 'data:image/' . pathinfo($logoNuPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode($logoNuData);
    }

    $logoTerbaruPath = public_path('images/logo_terbaru.png');
    $logoTerbaruBase64 = '';
    if (file_exists($logoTerbaruPath)) {
        $logoTerbaruData = file_get_contents($logoTerbaruPath);
        $logoTerbaruBase64 = 'data:image/' . pathinfo($logoTerbaruPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode($logoTerbaruData);
    }
@endphp
<!DOCTYPE html>
<html>
<head>
    <title>Rekap Presensi Siswa</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        
        /* Kop Surat Styling */
        .kop-surat {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }
        .kop-table td {
            border: none !important;
            padding: 0 !important;
        }
        .logo-cell {
            width: 12%;
            vertical-align: middle;
        }
        .logo-cell img {
            height: 70px;
            max-width: 100%;
        }
        .text-cell {
            width: 76%;
            text-align: center;
            font-family: Arial, Helvetica, sans-serif;
        }
        .text-cell h3 {
            margin: 0;
            font-size: 12px;
            font-weight: normal;
            text-transform: uppercase;
            color: #000;
        }
        .text-cell h2 {
            margin: 2px 0;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            color: #000;
        }
        .text-cell h4 {
            margin: 0 0 4px 0;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            color: #000;
        }
        .text-cell p {
            margin: 1px 0;
            font-size: 9px;
            color: #333;
        }

        /* Title Header */
        .title-header { 
            text-align: center; 
            margin-bottom: 15px; 
        }
        .title-header h3 {
            margin: 0;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .title-header p {
            margin: 4px 0 0 0;
            font-size: 11px;
            color: #333;
        }

        /* Data Table Styling */
        .data-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
        }
        .data-table th, .data-table td { 
            border: 1px solid #000; 
            padding: 6px; 
            text-align: left; 
        }
        .data-table th { 
            background-color: #f2f2f2; 
        }

        /* Signature Table */
        .footer { margin-top: 30px; }
        .signature-table { width: 100%; border: none; border-collapse: collapse; }
        .signature-table td { border: none !important; width: 50%; text-align: center; padding: 4px !important; }
    </style>
</head>
<body>
    <!-- Kop Surat -->
    <div class="kop-surat">
        <table class="kop-table">
            <tr>
                <td class="logo-cell" style="text-align: left;">
                    @if($logoNuBase64)
                        <img src="{{ $logoNuBase64 }}" alt="Logo NU">
                    @endif
                </td>
                <td class="text-cell">
                    <h3>LEMBAGA PENDIDIKAN MA'ARIF NU PCNU KAB.WONOSOBO</h3>
                    <h2>MADRASAH IBTIDAIYAH MA'ARIF CLENGKOM</h2>
                    <h4>KECAMATAN KALIWIRO KABUPATEN WONOSOBO</h4>
                    <p>Alamat: Jl. Prembun Km 29 Kecamatan Kaliwiro Kabupaten Wonosobo</p>
                    <p style="font-size: 9px; margin-top: 3px;">
                        email: mi.clengkom@yahoo.co.id &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Kode Pos 56364
                    </p>
                </td>
                <td class="logo-cell" style="text-align: right;">
                    @if($logoTerbaruBase64)
                        <img src="{{ $logoTerbaruBase64 }}" alt="Logo Terbaru">
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Title Header -->
    <div class="title-header">
        <h3>REKAPITULASI PRESENSI SISWA</h3>
        <p>
            Periode: @if($pengajuan->tipe === 'semester') Semester {{ ucfirst($pengajuan->bulan) }} ({{ $pengajuan->bulan === 'ganjil' ? 'Juli - Desember' : 'Januari - Juni' }}) @else {{ \Carbon\Carbon::create(null, $pengajuan->bulan)->translatedFormat('F') }} @endif {{ $pengajuan->tahun }} 
            &nbsp;|&nbsp; Kelas: {{ $pengajuan->kelas }} 
            &nbsp;|&nbsp; Wali Kelas: {{ $user->name }}
        </p>
    </div>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 35px; text-align: center;">No</th>
                <th>Nama Siswa</th>
                <th style="width: 50px; text-align: center;">Hadir</th>
                <th style="width: 50px; text-align: center;">Izin</th>
                <th style="width: 50px; text-align: center;">Sakit</th>
                <th style="width: 50px; text-align: center;">Alpha</th>
                <th style="width: 60px; text-align: center;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($siswas as $index => $siswa)
                @php
                    $d = $presensiData[$siswa->id];
                    $total = $d['hadir'] + $d['izin'] + $d['sakit'] + $d['alpha'];
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $siswa->nama_siswa }}</td>
                    <td style="text-align: center;">{{ $d['hadir'] }}</td>
                    <td style="text-align: center;">{{ $d['izin'] }}</td>
                    <td style="text-align: center;">{{ $d['sakit'] }}</td>
                    <td style="text-align: center;">{{ $d['alpha'] }}</td>
                    <td style="text-align: center;">{{ $total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Signature Footer -->
    <div class="footer">
        <p style="text-align: right; margin-bottom: 15px;">Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
        <table class="signature-table">
            <tr>
                <td>
                    Mengetahui,<br>
                    Kepala Sekolah<br><br><br><br>
                    <strong>{{ $kepsek->name ?? '.......................' }}</strong>
                </td>
                <td>
                    <br>
                    Wali Kelas {{ $pengajuan->kelas }}<br><br><br><br>
                    <strong>{{ $user->name }}</strong>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
