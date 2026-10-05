<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Presensi;
use App\Models\PengajuanPresensi;
use App\Models\TugasPengganti;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class WaliKelasController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $activePeriod = TahunAjaran::getActiveSemesterPeriod();
        $periodCode = $activePeriod ? $activePeriod['period_code'] : '';
        
        $totalSiswa = Siswa::where('status', 'aktif')
            ->whereHas('rombels', function($q) use ($periodCode, $user) {
                $q->where('semester', $periodCode)
                  ->whereHas('kelas', function($qk) use ($user) {
                      $qk->where('wali_kelas_id', $user->id);
                  });
            })->count();
            
        $today = Carbon::today()->toDateString();
        
        $presensiToday = Presensi::whereHas('siswa', function($q) use ($periodCode, $user) {
            $q->whereHas('rombels', function($qr) use ($periodCode, $user) {
                $qr->where('semester', $periodCode)
                  ->whereHas('kelas', function($qk) use ($user) {
                      $qk->where('wali_kelas_id', $user->id);
                  });
            });
        })->where('tanggal', $today)->get();
        
        $stats = [
            'hadir' => $presensiToday->where('status', 'hadir')->count(),
            'izin' => $presensiToday->where('status', 'izin')->count(),
            'sakit' => $presensiToday->where('status', 'sakit')->count(),
            'alpha' => $presensiToday->where('status', 'alpha')->count(),
            'belum_presensi' => max(0, $totalSiswa - $presensiToday->count())
        ];

        // Hitung persentase kehadiran hari ini
        $hadirPercentage = $totalSiswa > 0 ? round(($stats['hadir'] / $totalSiswa) * 100, 2) : 0;
        
        // Data Grafik Kehadiran 7 Hari Terakhir
        $chartData = [];
        $chartLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->translatedFormat('D');
            
            $presensiHarian = Presensi::whereHas('siswa', function($q) use ($periodCode, $user) {
                $q->whereHas('rombels', function($qr) use ($periodCode, $user) {
                    $qr->where('semester', $periodCode)
                      ->whereHas('kelas', function($qk) use ($user) {
                          $qk->where('wali_kelas_id', $user->id);
                      });
                });
            })->where('tanggal', $date->toDateString())->get();
            
            $hadirCount = $presensiHarian->where('status', 'hadir')->count();
            $percentage = $totalSiswa > 0 ? round(($hadirCount / $totalSiswa) * 100) : 0;
            $chartData[] = $percentage;
        }

        // Riwayat Presensi Terakhir (5 hari terakhir yang ada presensinya)
        $riwayatPresensi = Presensi::select('tanggal')
            ->whereHas('siswa', function($q) use ($periodCode, $user) {
                $q->whereHas('rombels', function($qr) use ($periodCode, $user) {
                    $qr->where('semester', $periodCode)
                      ->whereHas('kelas', function($qk) use ($user) {
                          $qk->where('wali_kelas_id', $user->id);
                      });
                });
            })
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'tanggal' => Carbon::parse($item->tanggal)->translatedFormat('d F Y'),
                    'status' => 'Sudah Diproses'
                ];
            });

        return view('wali.dashboard', compact('totalSiswa', 'stats', 'hadirPercentage', 'chartData', 'chartLabels', 'riwayatPresensi'));
    }

    public function presensiIndex()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        
        $activePeriod = TahunAjaran::getActiveSemesterPeriod();
        $periodCode = $activePeriod ? $activePeriod['period_code'] : '';

        $siswas = Siswa::where('status', 'aktif')
            ->whereHas('rombels', function($q) use ($periodCode, $user) {
                $q->where('semester', $periodCode)
                  ->whereHas('kelas', function($qk) use ($user) {
                      $qk->where('wali_kelas_id', $user->id);
                  });
            })->get();

        $presensiToday = Presensi::where('tanggal', $today)
            ->whereIn('siswa_id', $siswas->pluck('id'))
            ->get()->pluck('status', 'siswa_id');

        // Cek apakah presensi diinput oleh guru pengganti atau guru mapel
        $guruPengganti = null;
        $guruMapel = null;
        $namaMapelDetail = null;
        if ($presensiToday->count() > 0) {
            $firstPresensi = Presensi::with('user')
                ->where('tanggal', $today)
                ->whereIn('siswa_id', $siswas->pluck('id'))
                ->first();
            if ($firstPresensi && $firstPresensi->user) {
                if ($firstPresensi->user->role === 'guru_pengganti') {
                    $guruPengganti = $firstPresensi->nama_pengganti ?? $firstPresensi->user->name;
                } elseif ($firstPresensi->user->role === 'guru_mapel') {
                    $guruMapel = $firstPresensi->nama_pengganti ?? $firstPresensi->user->name;
                    $namaMapelDetail = $firstPresensi->nama_mapel ?? '-';
                }
            }
        }

        return view('wali.presensi.index', compact('siswas', 'presensiToday', 'today', 'guruPengganti', 'guruMapel', 'namaMapelDetail'));
    }

    public function presensiStore(Request $request)
    {
        $request->validate([
            'presensi' => 'required|array',
            'tanggal' => 'required|date',
            'dokumen.*' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $user = Auth::user();
        $activePeriod = TahunAjaran::getActiveSemesterPeriod();
        $periodCode = $activePeriod ? $activePeriod['period_code'] : '';

        $siswas = Siswa::where('status', 'aktif')
            ->whereHas('rombels', function($q) use ($periodCode, $user) {
                $q->where('semester', $periodCode)
                  ->whereHas('kelas', function($qk) use ($user) {
                      $qk->where('wali_kelas_id', $user->id);
                  });
            })->get();

        if (count($request->presensi) < $siswas->count()) {
            return back()->with('error', 'Status kehadiran semua siswa harus ditentukan sebelum disimpan.')->withInput();
        }

        foreach ($request->presensi as $siswaId => $status) {
            if ($status) {
                $updateData = [
                    'status' => $status, 
                    'user_id' => Auth::id()
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

        return back()->with('success', 'Presensi berhasil disimpan.');
    }

    public function presensiHistory(Request $request)
    {
        $user = Auth::user();
        $activePeriod = TahunAjaran::getActiveSemesterPeriod();
        $periodCode = $activePeriod ? $activePeriod['period_code'] : '';

        $query = Presensi::with(['siswa', 'user'])
            ->whereHas('siswa', function($q) use ($periodCode, $user) {
                $q->whereHas('rombels', function($qr) use ($periodCode, $user) {
                    $qr->where('semester', $periodCode)
                      ->whereHas('kelas', function($qk) use ($user) {
                          $qk->where('wali_kelas_id', $user->id);
                      });
                });
            });

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

        return view('wali.presensi.history', compact('presensis', 'tahunAjarans', 'isSearched'));
    }

    public function pengajuanIndex()
    {
        $user = Auth::user();
        $pengajuans = PengajuanPresensi::where('user_id', $user->id)->latest()->get();
        return view('wali.pengajuan.index', compact('pengajuans'));
    }

    public function pengajuanStore(Request $request)
    {
        $request->validate([
            'tipe' => 'required|in:bulanan,semester',
            'bulan' => 'required',
            'tahun' => 'required',
        ]);

        $user = Auth::user();
        
        // Cek apakah sudah ada pengajuan untuk tipe/bulan/tahun/kelas ini
        $exists = PengajuanPresensi::where('user_id', $user->id)
            ->where('tipe', $request->tipe)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Pengajuan untuk periode ini sudah ada.');
        }

        PengajuanPresensi::create([
            'user_id' => $user->id,
            'kelas' => $user->kelas,
            'tipe' => $request->tipe,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Pengajuan rekap berhasil dikirim.');
    }

    public function downloadPdf($id)
    {
        $pengajuan = PengajuanPresensi::findOrFail($id);
        if ($pengajuan->status !== 'disetujui') {
            return back()->with('error', 'Laporan belum disetujui Kepala Sekolah.');
        }

        $user = User::find($pengajuan->user_id);
        
        $tahun = $pengajuan->tahun;
        if ($pengajuan->tipe === 'semester') {
            $semester = ucfirst(strtolower($pengajuan->bulan));
        } else {
            $month = intval($pengajuan->bulan);
            $semester = ($month >= 7 && $month <= 12) ? 'Ganjil' : 'Genap';
        }

        $siswas = Siswa::whereHas('rombels', function($q) use ($pengajuan, $tahun, $semester) {
            $q->where('semester', 'like', "%{$tahun}%")
              ->where('semester', 'like', "%{$semester}%")
              ->whereHas('kelas', function($qk) use ($pengajuan) {
                  $qk->where('nama_kelas', $pengajuan->kelas);
              });
        })->where('status', 'aktif')->get();
        $kepsek = User::whereJsonContains('roles', 'kepala_sekolah')->first();

        // Ambil data presensi untuk periode tersebut
        $presensiData = [];
        foreach ($siswas as $siswa) {
            $query = Presensi::where('siswa_id', $siswa->id)
                ->whereYear('tanggal', $pengajuan->tahun);
                
            if ($pengajuan->tipe === 'semester') {
                $months = $pengajuan->bulan === 'ganjil' ? [7, 8, 9, 10, 11, 12] : [1, 2, 3, 4, 5, 6];
                $query->whereIn(\DB::raw('MONTH(tanggal)'), $months);
            } else {
                $query->whereMonth('tanggal', $pengajuan->bulan);
            }
            
            $stats = $query->get();
            
            $presensiData[$siswa->id] = [
                'hadir' => $stats->where('status', 'hadir')->count(),
                'izin' => $stats->where('status', 'izin')->count(),
                'sakit' => $stats->where('status', 'sakit')->count(),
                'alpha' => $stats->where('status', 'alpha')->count(),
            ];
        }

        $pdf = Pdf::loadView('reports.rekap_bulanan', compact('pengajuan', 'user', 'siswas', 'presensiData', 'kepsek'));
        $filename = $pengajuan->tipe === 'semester' 
            ? "Rekap_Semester_{$pengajuan->bulan}_Kelas_{$pengajuan->kelas}_{$pengajuan->tahun}.pdf" 
            : "Rekap_Bulan_{$pengajuan->bulan}_Kelas_{$pengajuan->kelas}_{$pengajuan->tahun}.pdf";
            
        return $pdf->download($filename);
    }

    public function siswaIndex(Request $request)
    {
        $user = Auth::user();
        $activePeriod = TahunAjaran::getActiveSemesterPeriod();
        $periodCode = $activePeriod ? $activePeriod['period_code'] : '';

        $query = Siswa::whereHas('rombels', function($q) use ($periodCode, $user) {
            $q->where('semester', $periodCode)
              ->whereHas('kelas', function($qk) use ($user) {
                  $qk->where('wali_kelas_id', $user->id);
              });
        });
        
        if ($request->search) {
            $query->where('nama_siswa', 'like', '%' . $request->search . '%');
        }
        
        $siswas = $query->latest()->paginate(15);
        return view('wali.siswa.index', compact('siswas'));
    }

    public function siswaCard(Siswa $siswa)
    {
        if ($siswa->kelas !== Auth::user()->kelas) {
            abort(403, 'Anda hanya dapat mengakses data siswa di kelas Anda.');
        }
        return view('admin.siswa.card', compact('siswa'));
    }

    public function presensiScan()
    {
        return view('shared.presensi.scan');
    }

    public function tugasPenggantiIndex()
    {
        $pengajuans = TugasPengganti::with('guruPengganti')
            ->where('wali_kelas_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('wali.tugas_pengganti.index', compact('pengajuans'));
    }

    public function tugasPenggantiStore(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date|after_or_equal:today',
            'alasan' => 'required|string',
            'dokumen' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $documentPath = null;
        if ($request->hasFile('dokumen')) {
            $file = $request->file('dokumen');
            $filename = time() . '_' . Auth::id() . '_' . $file->getClientOriginalName();
            $documentPath = $file->storeAs('tugas_pengganti', $filename, 'public');
        }

        TugasPengganti::create([
            'wali_kelas_id' => Auth::id(),
            'kelas' => Auth::user()->kelas,
            'tanggal' => $request->tanggal,
            'alasan' => $request->alasan,
            'document_path' => $documentPath,
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', 'Pengajuan Guru Pengganti berhasil dikirim ke Admin.');
    }
}
