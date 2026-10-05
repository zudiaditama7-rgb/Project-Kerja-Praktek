<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Presensi;
use App\Models\PengajuanPresensi;
use App\Models\User;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class KepalaSekolahController extends Controller
{
    public function dashboard()
    {
        $totalSiswa = Siswa::where('status', 'aktif')->count();
        $totalKelas = User::whereJsonContains('roles', 'wali_kelas')->count();
        $today = Carbon::today()->toDateString();
        
        $presensiToday = Presensi::where('tanggal', $today)->get();
        $stats = [
            'hadir' => $presensiToday->where('status', 'hadir')->count(),
            'izin' => $presensiToday->where('status', 'izin')->count(),
            'sakit' => $presensiToday->where('status', 'sakit')->count(),
            'alpha' => $presensiToday->where('status', 'alpha')->count(),
        ];

        $pendingApproval = PengajuanPresensi::where('status', 'pending')->count();

        // Calculate attendance progress per class
        $kelasProgress = [];
        $kelasSemua = \App\Models\Kelas::all();
        $activePeriodCode = \App\Models\TahunAjaran::getActiveSemesterPeriod()['period_code'] ?? '';
        
        foreach ($kelasSemua as $kls) {
            $namaKelas = $kls->nama_kelas;

            // Dapatkan ID siswa yang statusnya AKTIF dan masuk rombel kelas ini di semester aktif
            $siswaIds = \App\Models\Siswa::where('status', 'aktif')
                ->whereHas('rombels', function($q) use ($activePeriodCode, $kls) {
                    $q->where('semester', $activePeriodCode)
                      ->where('kelas_id', $kls->id);
                })->pluck('id')->toArray();
                
            $totalSiswaKelas = count($siswaIds);

            $presensiCount = $presensiToday->whereIn('siswa_id', $siswaIds)->count();

            // Group by class name to prevent duplicates
            if (!isset($kelasProgress[$namaKelas])) {
                $kelasProgress[$namaKelas] = [
                    'nama_kelas' => $namaKelas,
                    'presensi_count' => 0,
                    'total_siswa' => 0,
                ];
            }

            $kelasProgress[$namaKelas]['presensi_count'] += $presensiCount;
            $kelasProgress[$namaKelas]['total_siswa'] += $totalSiswaKelas;
        }

        // Calculate final percentages
        foreach ($kelasProgress as $key => $progress) {
            $percentage = $progress['total_siswa'] > 0 ? round(($progress['presensi_count'] / $progress['total_siswa']) * 100) : 0;
            if ($percentage > 100) $percentage = 100;

            $kelasProgress[$key]['persentase'] = $percentage;
            $kelasProgress[$key]['terpantau'] = $progress['presensi_count'] > 0;
        }

        // Sort by class name naturally (Kelas 1, Kelas 2, dll)
        uasort($kelasProgress, function($a, $b) {
            return strnatcasecmp($a['nama_kelas'], $b['nama_kelas']);
        });

        return view('kepsek.dashboard', compact('totalSiswa', 'totalKelas', 'stats', 'pendingApproval', 'kelasProgress'));
    }

    public function monitoring(Request $request)
    {
        $today = $request->tanggal ?? Carbon::today()->toDateString();
        $kelas = $request->kelas;
        
        $query = Presensi::with(['siswa', 'user'])->where('tanggal', $today);
        
        $activePeriod = TahunAjaran::getActiveSemesterPeriod();
        $periodCode = $activePeriod ? $activePeriod['period_code'] : '';
        
        $kelasSemua = \App\Models\Kelas::where('semester', $periodCode)->get();

        $kelasProgress = [];
        $presensiToday = Presensi::where('tanggal', $today)->get();
        
        foreach ($kelasSemua as $kls) {
            $namaKelas = $kls->nama_kelas;

            $siswaIds = \App\Models\Siswa::where('status', 'aktif')
                ->whereHas('rombels', function($q) use ($periodCode, $kls) {
                    $q->where('semester', $periodCode)
                      ->where('kelas_id', $kls->id);
                })->pluck('id')->toArray();

            $presensiKelas = $presensiToday->whereIn('siswa_id', $siswaIds);
            
            $kelasProgress[$namaKelas] = [
                'nama_kelas' => $namaKelas,
                'total_siswa' => count($siswaIds),
                'hadir' => 0,
                'izin' => 0,
                'sakit' => 0,
                'alpha' => 0,
                'sudah_absen' => false
            ];

            if ($presensiKelas->count() > 0) {
                $kelasProgress[$namaKelas]['sudah_absen'] = true;
                $kelasProgress[$namaKelas]['hadir'] = $presensiKelas->where('status', 'hadir')->count();
                $kelasProgress[$namaKelas]['izin'] = $presensiKelas->where('status', 'izin')->count();
                $kelasProgress[$namaKelas]['sakit'] = $presensiKelas->where('status', 'sakit')->count();
                $kelasProgress[$namaKelas]['alpha'] = $presensiKelas->where('status', 'alpha')->count();
            }
        }

        uasort($kelasProgress, function($a, $b) {
            return strnatcasecmp($a['nama_kelas'], $b['nama_kelas']);
        });

        if ($kelas) {
            $query->whereHas('siswa', function($q) use ($kelas, $periodCode) {
                $q->whereHas('rombels', function($qr) use ($kelas, $periodCode) {
                    $qr->where('semester', $periodCode)
                      ->whereHas('kelas', function($qk) use ($kelas) {
                          $qk->where('nama_kelas', $kelas);
                      });
                });
            });
        }
        
        $presensis = $query->get();
        return view('kepsek.monitoring', compact('presensis', 'today', 'kelas', 'kelasProgress'));
    }

    public function approvalIndex()
    {
        $pengajuans = PengajuanPresensi::with('user')->latest()->get();
        return view('kepsek.approval.index', compact('pengajuans'));
    }

    public function approvalProcess(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'keterangan' => 'nullable'
        ]);
        
        $pengajuan = PengajuanPresensi::findOrFail($id);
        $pengajuan->update([
            'status' => $request->status,
            'keterangan' => $request->keterangan,
            'acc_by' => Auth::id(),
            'acc_date' => now()
        ]);

        return back()->with('success', 'Status pengajuan berhasil diperbarui.');
    }

    public function laporan(Request $request)
    {
        $query = Presensi::with(['siswa', 'user']);
        
        if ($request->tanggal_mulai && $request->tanggal_selesai) {
            $query->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_selesai]);
        } elseif ($request->tanggal) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        
        if ($request->bulan) {
            $query->whereMonth('tanggal', $request->bulan);
        }
        
        if ($request->kelas) {
            $activePeriod = TahunAjaran::getActiveSemesterPeriod();
            $periodCode = $activePeriod ? $activePeriod['period_code'] : '';

            $query->whereHas('siswa', function($q) use ($request, $periodCode) {
                $q->whereHas('rombels', function($qr) use ($request, $periodCode) {
                    $qr->where('semester', $periodCode)
                      ->whereHas('kelas', function($qk) use ($request) {
                          $qk->where('nama_kelas', $request->kelas);
                      });
                });
            });
        }

        $summaryQuery = clone $query;
        $summaryData = $summaryQuery->get();
        $summary = [
            'hadir' => $summaryData->where('status', 'hadir')->count(),
            'izin' => $summaryData->where('status', 'izin')->count(),
            'sakit' => $summaryData->where('status', 'sakit')->count(),
            'alpha' => $summaryData->where('status', 'alpha')->count(),
            'total' => $summaryData->count()
        ];

        $laporans = $query->latest()->paginate(50)->withQueryString();
        $tahunAjarans = TahunAjaran::all();
        
        return view('kepsek.laporan', compact('laporans', 'tahunAjarans', 'summary'));
    }

    public function laporanExportExcel(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new class($request) implements 
            \Maatwebsite\Excel\Concerns\FromCollection, 
            \Maatwebsite\Excel\Concerns\WithHeadings,
            \Maatwebsite\Excel\Concerns\WithMapping,
            \Maatwebsite\Excel\Concerns\ShouldAutoSize 
        {
            protected $request;
            private $rowNumber = 0;

            public function __construct($request) { $this->request = $request; }

            public function collection() { 
                $query = Presensi::with(['siswa', 'user']);
        
                if ($this->request->tanggal_mulai && $this->request->tanggal_selesai) {
                    $query->whereBetween('tanggal', [$this->request->tanggal_mulai, $this->request->tanggal_selesai]);
                } elseif ($this->request->tanggal) {
                    $query->whereDate('tanggal', $this->request->tanggal);
                }
                
                if ($this->request->bulan) {
                    $query->whereMonth('tanggal', $this->request->bulan);
                }
                
                if ($this->request->kelas) {
                    $activePeriod = TahunAjaran::getActiveSemesterPeriod();
                    $periodCode = $activePeriod ? $activePeriod['period_code'] : '';

                    $query->whereHas('siswa', function($q) use ($periodCode) {
                        $q->whereHas('rombels', function($qr) use ($periodCode) {
                            $qr->where('semester', $periodCode)
                              ->whereHas('kelas', function($qk) {
                                  $qk->where('nama_kelas', $this->request->kelas);
                              });
                        });
                    });
                }
                return $query->latest()->get(); 
            }

            public function headings(): array {
                return ['NO', 'TANGGAL', 'NIS', 'NAMA SISWA', 'KELAS', 'STATUS', 'WALI KELAS'];
            }

            public function map($row): array {
                $this->rowNumber++;
                return [
                    $this->rowNumber,
                    \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y'),
                    $row->siswa->nis,
                    $row->siswa->nama_siswa,
                    $row->siswa->kelas,
                    strtoupper($row->status),
                    $row->user->name
                ];
            }
        }, 'Laporan_Presensi_Sekolah.xlsx');
    }
}
