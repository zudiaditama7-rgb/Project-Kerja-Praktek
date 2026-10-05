<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Presensi;
use App\Models\TahunAjaran;
use App\Models\TugasPengganti;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GuruMapelController extends Controller
{
    public function dashboard()
    {
        $activePeriod = TahunAjaran::getActiveSemesterPeriod();
        $periodCode = $activePeriod ? $activePeriod['period_code'] : '';

        $userKelasMapel = Auth::user()->kelas_mapel ?? [];

        // Ambil semua kelas aktif untuk semester ini beserta jumlah siswa, HANYA yang ada di $userKelasMapel
        $kelases = Kelas::where('semester', $periodCode)
            ->whereIn('nama_kelas', $userKelasMapel)
            ->orderBy('nama_kelas')
            ->get();

        $stats = [];
        foreach ($kelases as $kelas) {
            $jumlahSiswa = Siswa::where('status', 'aktif')
                ->whereHas('rombels', function($q) use ($periodCode, $kelas) {
                    $q->where('semester', $periodCode)
                      ->where('kelas_id', $kelas->id);
                })->count();
            $stats[$kelas->id] = $jumlahSiswa;
        }

        return view('guru_mapel.dashboard', compact('kelases', 'stats'));
    }

    public function pilihKelas()
    {
        $activePeriod = TahunAjaran::getActiveSemesterPeriod();
        $periodCode = $activePeriod ? $activePeriod['period_code'] : '';

        $userKelasMapel = Auth::user()->kelas_mapel ?? [];

        // Tampilkan semua kelas aktif untuk semester ini HANYA yang ditugaskan
        $kelases = Kelas::where('semester', $periodCode)
            ->whereIn('nama_kelas', $userKelasMapel)
            ->orderBy('nama_kelas')
            ->get();

        return view('guru_mapel.presensi.pilih_kelas', compact('kelases'));
    }

    public function presensiIndex($kelas_id)
    {
        $activePeriod = TahunAjaran::getActiveSemesterPeriod();
        $periodCode = $activePeriod ? $activePeriod['period_code'] : '';

        $kelas = Kelas::findOrFail($kelas_id);
        if ($kelas->semester !== $periodCode) {
            abort(404, 'Kelas tidak aktif di periode ini.');
        }

        $userKelasMapel = Auth::user()->kelas_mapel ?? [];
        if (!in_array($kelas->nama_kelas, $userKelasMapel)) {
            abort(403, 'Anda tidak ditugaskan untuk mengajar di kelas ini.');
        }

        $siswas = Siswa::where('status', 'aktif')
            ->whereHas('rombels', function($q) use ($periodCode, $kelas_id) {
                $q->where('semester', $periodCode)
                  ->where('kelas_id', $kelas_id);
            })->get();

        $today = Carbon::today()->toDateString();
        $presensiToday = Presensi::where('tanggal', $today)
            ->whereIn('siswa_id', $siswas->pluck('id'))
            ->get()->pluck('status', 'siswa_id');

        return view('guru_mapel.presensi.index', compact('kelas', 'siswas', 'presensiToday', 'today'));
    }

    public function presensiStore(Request $request, $kelas_id)
    {
        $request->validate([
            'presensi' => 'required|array',
            'tanggal' => 'required|date',
            'dokumen.*' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'nama_pengganti' => 'required|string|max:255',
            'nama_mapel' => 'required|string|max:255',
        ]);

        $activePeriod = TahunAjaran::getActiveSemesterPeriod();
        $periodCode = $activePeriod ? $activePeriod['period_code'] : '';

        $kelas = Kelas::findOrFail($kelas_id);
        
        $userKelasMapel = Auth::user()->kelas_mapel ?? [];
        if (!in_array($kelas->nama_kelas, $userKelasMapel)) {
            abort(403, 'Anda tidak ditugaskan untuk mengajar di kelas ini.');
        }
        $siswas = Siswa::where('status', 'aktif')
            ->whereHas('rombels', function($q) use ($periodCode, $kelas_id) {
                $q->where('semester', $periodCode)
                  ->where('kelas_id', $kelas_id);
            })->get();

        if (count($request->presensi) < $siswas->count()) {
            return back()->with('error', 'Status kehadiran semua siswa harus ditentukan sebelum disimpan.')->withInput();
        }

        foreach ($request->presensi as $siswaId => $status) {
            if ($status) {
                $updateData = [
                    'status' => $status, 
                    'user_id' => Auth::id(),
                    'nama_pengganti' => $request->nama_pengganti,
                    'nama_mapel' => $request->nama_mapel
                ];

                if (in_array($status, ['izin', 'sakit']) && $request->hasFile("dokumen.$siswaId")) {
                    $file = $request->file("dokumen.$siswaId");
                    $filename = time() . '_' . $siswaId . '_' . $file->getClientOriginalName();
                    $updateData['document_path'] = $file->storeAs('dokumen_presensi', $filename, 'public');
                } elseif (!in_array($status, ['izin', 'sakit'])) {
                    $updateData['document_path'] = null;
                }

                Presensi::updateOrCreate(
                    ['siswa_id' => $siswaId, 'tanggal' => $request->tanggal],
                    $updateData
                );
            }
        }

        return redirect()->route('guru_mapel.dashboard')->with('success', 'Presensi Kelas ' . $kelas->nama_kelas . ' berhasil disimpan.');
    }

    public function presensiScan(Request $request, $kelas_id)
    {
        $activePeriod = TahunAjaran::getActiveSemesterPeriod();
        $periodCode = $activePeriod ? $activePeriod['period_code'] : '';

        $kelas = Kelas::findOrFail($kelas_id);
        if ($kelas->semester !== $periodCode) {
            abort(404, 'Kelas tidak aktif di periode ini.');
        }
        
        $userKelasMapel = Auth::user()->kelas_mapel ?? [];
        if (!in_array($kelas->nama_kelas, $userKelasMapel)) {
            abort(403, 'Anda tidak ditugaskan untuk mengajar di kelas ini.');
        }

        $nama_mapel = $request->query('nama_mapel');
        $nama_pengganti = $request->query('nama_pengganti');

        if (!$nama_mapel || !$nama_pengganti) {
            return redirect()->route('guru_mapel.presensi.index', $kelas_id)
                ->with('error', 'Silakan isi Nama Lengkap dan Mata Pelajaran terlebih dahulu sebelum melakukan Scan QR.');
        }

        return view('guru_mapel.presensi.scan', compact('kelas', 'nama_mapel', 'nama_pengganti'));
    }

    public function tugasPenggantiIndex()
    {
        $pengajuans = TugasPengganti::where('guru_mapel_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        $activePeriod = TahunAjaran::getActiveSemesterPeriod();
        $periodCode = $activePeriod ? $activePeriod['period_code'] : '';
        
        $userKelasMapel = Auth::user()->kelas_mapel ?? [];
        
        $kelases = Kelas::where('semester', $periodCode)
            ->whereIn('nama_kelas', $userKelasMapel)
            ->orderBy('nama_kelas')
            ->get();

        return view('guru_mapel.tugas_pengganti.index', compact('pengajuans', 'kelases'));
    }

    public function tugasPenggantiStore(Request $request)
    {
        $request->validate([
            'kelas' => 'required|string',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'alasan' => 'required|string|max:500',
            'dokumen' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        $dokumenPath = null;
        if ($request->hasFile('dokumen')) {
            $file = $request->file('dokumen');
            $filename = time() . '_tugas_pengganti_' . Auth::id() . '_' . $file->getClientOriginalName();
            $dokumenPath = $file->storeAs('dokumen_tugas_pengganti', $filename, 'public');
        }

        TugasPengganti::create([
            'guru_mapel_id' => Auth::id(),
            'kelas' => $request->kelas,
            'tanggal' => $request->tanggal,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'alasan' => $request->alasan,
            'document_path' => $dokumenPath,
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', 'Pengajuan Guru Pengganti berhasil dikirim.');
    }
}
