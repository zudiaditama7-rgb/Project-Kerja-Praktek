<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Presensi;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class GuruPenggantiController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today()->toDateString();
        $totalKelas = \App\Models\TugasPengganti::where('guru_pengganti_id', Auth::id())
            ->where('tanggal', $today)
            ->where('status', 'disetujui')
            ->count();
        
        $presensiToday = Presensi::where('tanggal', $today)
            ->where('user_id', Auth::id())
            ->count();

        $tugasList = \App\Models\TugasPengganti::with('waliKelas')
            ->where('guru_pengganti_id', Auth::id())
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('guru_pengganti.dashboard', compact('totalKelas', 'presensiToday', 'tugasList'));
    }

    public function presensiIndex()
    {
        $today = Carbon::today()->toDateString();
        $kelasList = \App\Models\TugasPengganti::where('guru_pengganti_id', Auth::id())
            ->where('tanggal', '>=', $today)
            ->where('status', 'disetujui')
            ->pluck('kelas')
            ->unique()
            ->sort();
        $selectedKelas = request('kelas');
        

        $siswas = collect();
        $presensiToday = collect();
        $isLocked = false;
        
        if ($selectedKelas) {
            $activePeriod = TahunAjaran::getActiveSemesterPeriod();
            $periodCode = $activePeriod ? $activePeriod['period_code'] : '';
            
            $siswas = Siswa::where('status', 'aktif')
                ->whereHas('rombels', function($q) use ($selectedKelas, $periodCode) {
                    $q->where('semester', $periodCode)
                      ->whereHas('kelas', function($qk) use ($selectedKelas) {
                          $qk->where('nama_kelas', $selectedKelas);
                      });
                })->get();

            $presensiToday = Presensi::where('tanggal', $today)
                ->whereIn('siswa_id', $siswas->pluck('id'))
                ->get()->pluck('status', 'siswa_id');
            $isLocked = $presensiToday->count() > 0;
        }

        return view('guru_pengganti.presensi.index', compact('kelasList', 'selectedKelas', 'siswas', 'presensiToday', 'today', 'isLocked'));
    }

    public function presensiStore(Request $request)
    {
        $request->validate([
            'kelas' => 'required',
            'presensi' => 'required|array',
            'tanggal' => 'required|date',
            'nama_pengganti' => 'required|string|max:255',
            'dokumen.*' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $activePeriod = TahunAjaran::getActiveSemesterPeriod();
        $periodCode = $activePeriod ? $activePeriod['period_code'] : '';

        // Cek apakah kelas ini sudah pernah dipresensi hari ini
        $siswas = Siswa::where('status', 'aktif')
            ->whereHas('rombels', function($q) use ($request, $periodCode) {
                $q->where('semester', $periodCode)
                  ->whereHas('kelas', function($qk) use ($request) {
                      $qk->where('nama_kelas', $request->kelas);
                  });
            })->pluck('id');

        $isLocked = Presensi::where('tanggal', $request->tanggal)
                            ->whereIn('siswa_id', $siswas)
                            ->exists();

        if ($isLocked) {
            return back()->with('error', 'Presensi kelas ' . $request->kelas . ' sudah dicatat sebelumnya dan tidak dapat diubah oleh Guru Pengganti.');
        }

        $activeSiswas = Siswa::where('status', 'aktif')
            ->whereHas('rombels', function($q) use ($request, $periodCode) {
                $q->where('semester', $periodCode)
                  ->whereHas('kelas', function($qk) use ($request) {
                      $qk->where('nama_kelas', $request->kelas);
                  });
            })->get();

        if (count($request->presensi) < $activeSiswas->count()) {
            return back()->with('error', 'Status kehadiran semua siswa harus ditentukan sebelum disimpan.')->withInput();
        }

        foreach ($request->presensi as $siswaId => $status) {
            if ($status) {
                $createData = [
                    'siswa_id' => $siswaId,
                    'tanggal' => $request->tanggal,
                    'status' => $status,
                    'user_id' => Auth::id(),
                    'nama_pengganti' => $request->nama_pengganti
                ];

                if (in_array($status, ['izin', 'sakit']) && $request->hasFile("dokumen.$siswaId")) {
                    $file = $request->file("dokumen.$siswaId");
                    $filename = time() . '_' . $siswaId . '_' . $file->getClientOriginalName();
                    $createData['document_path'] = $file->storeAs('dokumen_presensi', $filename, 'public');
                }

                Presensi::create($createData);
            }
        }

        // Tandai tugas pengganti sebagai selesai jika presensi berhasil dicatat
        $tugas = \App\Models\TugasPengganti::where('guru_pengganti_id', Auth::id())
            ->where('tanggal', $request->tanggal)
            ->where('kelas', $request->kelas)
            ->where('status', 'disetujui')
            ->first();

        if ($tugas) {
            $tugas->status = 'selesai';
            $tugas->save();
        }

        return back()->with('success', 'Presensi berhasil disimpan untuk kelas ' . $request->kelas . ' oleh ' . $request->nama_pengganti);
    }

    public function presensiHistory(Request $request)
    {
        $query = Presensi::with('siswa')
            ->where('user_id', Auth::id());

        if ($request->tanggal_mulai && $request->tanggal_selesai) {
            $query->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_selesai]);
        } elseif ($request->tanggal) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $isSearched = $request->hasAny(['tanggal_mulai', 'tanggal_selesai', 'tanggal', 'status']);

        if ($isSearched) {
            $presensis = $query->latest()->paginate(20)->withQueryString();
        } else {
            $presensis = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20, 1, ['path' => $request->url(), 'query' => $request->query()]);
        }
        
        $tahunAjarans = TahunAjaran::all();

        return view('guru_pengganti.presensi.history', compact('presensis', 'tahunAjarans', 'isSearched'));
    }

    public function siswaIndex(Request $request)
    {
        $activePeriod = TahunAjaran::getActiveSemesterPeriod();
        $periodCode = $activePeriod ? $activePeriod['period_code'] : '';

        $query = Siswa::query();
        if ($request->search) {
            $query->where('nama_siswa', 'like', '%' . $request->search . '%');
        }
        if ($request->kelas) {
            $query->whereHas('rombels', function($q) use ($request, $periodCode) {
                $q->where('semester', $periodCode)
                  ->whereHas('kelas', function($qk) use ($request) {
                      $qk->where('nama_kelas', $request->kelas);
                  });
            });
        }
        
        $siswas = $query->latest()->paginate(15);
        $kelasList = \App\Models\Kelas::where('semester', $periodCode)->orderBy('nama_kelas')->pluck('nama_kelas');
        return view('guru_pengganti.siswa.index', compact('siswas', 'kelasList'));
    }

    public function siswaCard(Siswa $siswa)
    {
        return view('admin.siswa.card', compact('siswa'));
    }

    public function presensiScan()
    {
        return view('shared.presensi.scan');
    }
}
