<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\User;
use App\Models\TahunAjaran;
use App\Models\Presensi;
use App\Models\TugasPengganti;
use Illuminate\Http\Request;
use App\Http\Requests\SiswaRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalSiswa = Siswa::where('status', 'aktif')->count();
        $totalWali = User::whereJsonContains('roles', 'wali_kelas')->count();
        $today = Carbon::today()->toDateString();
        
        $presensiToday = Presensi::where('tanggal', $today)->get();
        $stats = [
            'hadir' => $presensiToday->where('status', 'hadir')->count(),
            'izin' => $presensiToday->where('status', 'izin')->count(),
            'sakit' => $presensiToday->where('status', 'sakit')->count(),
            'alpha' => $presensiToday->where('status', 'alpha')->count(),
        ];

        $recentPresensi = Presensi::with(['siswa', 'user'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('totalSiswa', 'totalWali', 'stats', 'recentPresensi'));
    }

    // SISWA CRUD
    public function siswaIndex(Request $request)
    {
        $query = Siswa::query();
        if ($request->search) {
            $query->where('nama_siswa', 'like', '%' . $request->search . '%');
        }
        if ($request->kelas) {
            $activePeriod = TahunAjaran::getActiveSemesterPeriod();
            $periodCode = $activePeriod ? $activePeriod['period_code'] : '';
            $query->whereHas('rombels', function($q) use ($request, $periodCode) {
                $q->where('semester', $periodCode)
                  ->whereHas('kelas', function($qk) use ($request) {
                      $qk->where('nama_kelas', $request->kelas);
                  });
            });
        }
        
        $siswas = $query->latest()->paginate(10);
        $tahunAjarans = TahunAjaran::all();
        return view('admin.siswa.index', compact('siswas', 'tahunAjarans'));
    }

    public function siswaCreate()
    {
        $tahunAjarans = TahunAjaran::all();
        return view('admin.siswa.create', compact('tahunAjarans'));
    }

    public function siswaStore(SiswaRequest $request)
    {
        Siswa::create($request->validated());
        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function siswaEdit(Siswa $siswa)
    {
        $tahunAjarans = TahunAjaran::all();
        return view('admin.siswa.edit', compact('siswa', 'tahunAjarans'));
    }

    public function siswaUpdate(SiswaRequest $request, Siswa $siswa)
    {
        $oldKelas = $siswa->kelas;
        $oldTahun = $siswa->tahun_ajaran;
        
        $siswa->update($request->validated());
        
        if ($oldKelas !== $siswa->kelas || $oldTahun !== $siswa->tahun_ajaran) {
            \App\Models\RiwayatSiswa::create([
                'siswa_id' => $siswa->id,
                'tahun_ajaran' => $siswa->tahun_ajaran,
                'kelas_asal' => $oldKelas,
                'kelas_tujuan' => $siswa->kelas,
                'status' => 'Penyesuaian Manual',
            ]);
        }
        
        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function siswaDestroy(Siswa $siswa)
    {
        $siswa->delete();
        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil dihapus.');
    }

    // USER CRUD
    public function userIndex()
    {
        $users = User::where(function($query) {
            $query->whereJsonDoesntContain('roles', 'admin')
                  ->orWhereNull('roles');
        })->get();
        return view('admin.users.index', compact('users'));
    }

    public function userStore(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users',
            'password' => 'required|min:6',
            'roles' => 'required|array|min:1',
            'roles.*' => 'in:wali_kelas,kepala_sekolah,guru_pengganti,guru_mapel,admin',
            'kelas' => 'required_if:roles.*,wali_kelas',
            'kelas_mapel' => 'nullable|array',
        ]);

        DB::transaction(function() use ($request) {
            $uniqueRoles = array_values(array_unique($request->roles));
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => $uniqueRoles[0], // Set the first role as default active session role
                'roles' => $uniqueRoles,
                'kelas_mapel' => in_array('guru_mapel', $uniqueRoles) ? ($request->kelas_mapel ?? []) : null,
            ]);

            if (in_array('wali_kelas', $request->roles) && $request->kelas) {
                $activePeriod = TahunAjaran::getActiveSemesterPeriod();
                $periodCode = $activePeriod ? $activePeriod['period_code'] : '';

                // If another teacher was assigned to this class for the active semester, clear it
                \App\Models\Kelas::where('nama_kelas', $request->kelas)
                    ->where('semester', $periodCode)
                    ->update(['wali_kelas_id' => null]);

                // Update the kelas table to set this user as wali kelas
                \App\Models\Kelas::updateOrCreate([
                    'nama_kelas' => $request->kelas,
                    'semester' => $periodCode
                ], [
                    'wali_kelas_id' => $user->id
                ]);
            }
        });

        return back()->with('success', 'User berhasil ditambahkan.');
    }

    public function userEdit(User $user)
    {
        if ($user->role === 'admin') abort(403);
        return view('admin.users.edit', compact('user'));
    }

    public function userUpdate(Request $request, User $user)
    {
        if ($user->role === 'admin') abort(403);

        $rules = [
            'name' => 'required',
            'username' => 'required|unique:users,username,' . $user->id,
            'roles' => 'required|array|min:1',
            'roles.*' => 'in:wali_kelas,kepala_sekolah,guru_pengganti,guru_mapel',
            'kelas' => 'required_if:roles.*,wali_kelas',
            'kelas_mapel' => 'nullable|array',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'required|min:6';
        }

        $request->validate($rules);

        $isWaliKelas = in_array('wali_kelas', $request->roles);
        $newKelas = $isWaliKelas ? $request->kelas : null;
        $handoverMessage = null;

        DB::transaction(function() use ($request, $user, $newKelas, &$handoverMessage, $isWaliKelas) {
            $activePeriod = TahunAjaran::getActiveSemesterPeriod();
            $periodCode = $activePeriod ? $activePeriod['period_code'] : '';
            $oldKelas = $user->kelas;

            if ($isWaliKelas && $newKelas) {
                // Logika Auto-Handover: Cari wali kelas lain yang saat ini memegang kelas tersebut
                $previousWaliKelas = \App\Models\Kelas::where('nama_kelas', $newKelas)
                    ->where('semester', $periodCode)
                    ->whereNotNull('wali_kelas_id')
                    ->where('wali_kelas_id', '!=', $user->id)
                    ->first();

                if ($previousWaliKelas && $previousWaliKelas->waliKelas) {
                    $previousWali = $previousWaliKelas->waliKelas;
                    // Cabut kelas dari wali kelas sebelumnya
                    $previousWaliKelas->update(['wali_kelas_id' => null]);
                    $handoverMessage = "Jabatan Wali Kelas {$newKelas} telah diserahterimakan dari {$previousWali->name} ke {$user->name}.";
                }
            }

            $updateData = [
                'name' => $request->name,
                'username' => $request->username,
                'roles' => array_values(array_unique($request->roles)),
            ];
            
            if (in_array('guru_mapel', $request->roles)) {
                $updateData['kelas_mapel'] = $request->kelas_mapel ?? [];
            } else {
                $updateData['kelas_mapel'] = null;
            }
            
            // Only update the active role if the current one is no longer in the list
            if (!in_array($user->role, $request->roles)) {
                $updateData['role'] = $request->roles[0];
            }

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            // Sync the kelas table
            if ($oldKelas !== $newKelas) {
                // Clear old class
                if ($oldKelas) {
                    \App\Models\Kelas::where('nama_kelas', $oldKelas)
                        ->where('semester', $periodCode)
                        ->where('wali_kelas_id', $user->id)
                        ->update(['wali_kelas_id' => null]);
                }
                // Set new class
                if ($newKelas) {
                    \App\Models\Kelas::updateOrCreate([
                        'nama_kelas' => $newKelas,
                        'semester' => $periodCode
                    ], [
                        'wali_kelas_id' => $user->id
                    ]);
                }
            }
        });

        $successMessage = 'User berhasil diperbarui.';
        if ($handoverMessage) {
            $successMessage .= ' ' . $handoverMessage;
        }

        return redirect()->route('admin.users.index')->with('success', $successMessage);
    }

    public function userDestroy(User $user)
    {
        if ($user->role === 'admin') abort(403);
        
        DB::transaction(function() use ($user) {
            if ($user->role === 'wali_kelas' && $user->kelas) {
                \App\Models\Kelas::where('nama_kelas', $user->kelas)->where('wali_kelas_id', $user->id)->update(['wali_kelas_id' => null]);
            }
            $user->delete();
        });

        return back()->with('success', 'User berhasil dihapus.');
    }

    // KELAS CRUD
    public function kelasIndex()
    {
        $activePeriod = TahunAjaran::getActiveSemesterPeriod();
        $periodCode = $activePeriod ? $activePeriod['period_code'] : '';

        $kelases = \App\Models\Kelas::with('waliKelas')
            ->where('semester', $periodCode)
            ->orderBy('nama_kelas')
            ->get();
        $teachers = User::whereJsonContains('roles', 'wali_kelas')->get();
        
        $studentCounts = DB::table('rombels')
            ->join('siswas', 'rombels.siswa_id', '=', 'siswas.id')
            ->join('kelas', 'rombels.kelas_id', '=', 'kelas.id')
            ->where('siswas.status', 'aktif')
            ->where('rombels.semester', $periodCode)
            ->select('kelas.nama_kelas', DB::raw('count(*) as total'))
            ->groupBy('kelas.nama_kelas')
            ->pluck('total', 'kelas.nama_kelas');
            
        return view('admin.kelas.index', compact('kelases', 'teachers', 'studentCounts'));
    }

    public function kelasStore(Request $request)
    {
        $activePeriod = TahunAjaran::getActiveSemesterPeriod();
        $periodCode = $activePeriod ? $activePeriod['period_code'] : '';

        $request->validate([
            'nama_kelas' => [
                'required',
                'string',
                \Illuminate\Validation\Rule::unique('kelas', 'nama_kelas')->where('semester', $periodCode)
            ],
            'wali_kelas_id' => 'nullable|exists:users,id',
        ]);

        DB::transaction(function() use ($request, $periodCode) {
            $waliKelasId = $request->wali_kelas_id;
            $namaKelas = $request->nama_kelas;

            if ($waliKelasId) {
                \App\Models\Kelas::where('semester', $periodCode)
                    ->where('wali_kelas_id', $waliKelasId)
                    ->update(['wali_kelas_id' => null]);
            }

            \App\Models\Kelas::create([
                'nama_kelas' => $namaKelas,
                'wali_kelas_id' => $waliKelasId,
                'semester' => $periodCode,
            ]);
        });

        return back()->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function kelasUpdate(Request $request, $id)
    {
        $kelas = \App\Models\Kelas::findOrFail($id);
        $activePeriod = TahunAjaran::getActiveSemesterPeriod();
        $periodCode = $activePeriod ? $activePeriod['period_code'] : '';
        
        $request->validate([
            'nama_kelas' => [
                'required',
                'string',
                \Illuminate\Validation\Rule::unique('kelas', 'nama_kelas')
                    ->where('semester', $periodCode)
                    ->ignore($id)
            ],
            'wali_kelas_id' => 'nullable|exists:users,id',
        ]);

        DB::transaction(function() use ($request, $kelas, $periodCode) {
            $oldNamaKelas = $kelas->nama_kelas;
            $newNamaKelas = $request->nama_kelas;
            $newWaliKelasId = $request->wali_kelas_id;
            $oldWaliKelasId = $kelas->wali_kelas_id;

            if ($oldNamaKelas !== $newNamaKelas) {
                DB::table('tugas_penggantis')->where('kelas', $oldNamaKelas)->update(['kelas' => $newNamaKelas]);
                DB::table('pengajuan_presensis')->where('kelas', $oldNamaKelas)->update(['kelas' => $newNamaKelas]);
            }

            if ($newWaliKelasId !== $oldWaliKelasId) {
                if ($newWaliKelasId) {
                    \App\Models\Kelas::where('semester', $periodCode)
                        ->where('wali_kelas_id', $newWaliKelasId)
                        ->where('id', '!=', $kelas->id)
                        ->update(['wali_kelas_id' => null]);
                }
            }

            $kelas->update([
                'nama_kelas' => $newNamaKelas,
                'wali_kelas_id' => $newWaliKelasId,
            ]);
        });

        return back()->with('success', 'Kelas berhasil diperbarui.');
    }

    public function kelasDestroy($id)
    {
        $kelas = \App\Models\Kelas::findOrFail($id);

        $activeStudentsCount = DB::table('rombels')
            ->join('siswas', 'rombels.siswa_id', '=', 'siswas.id')
            ->where('rombels.kelas_id', $kelas->id)
            ->where('siswas.status', 'aktif')
            ->count();

        if ($activeStudentsCount > 0) {
            return back()->with('error', 'Tidak dapat menghapus kelas ini karena masih memiliki ' . $activeStudentsCount . ' siswa aktif.');
        }

        $kelas->delete();

        return back()->with('success', 'Kelas berhasil dihapus.');
    }

    // TAHUN AJARAN
    public function tahunAjaranIndex()
    {
        $tahunAjarans = TahunAjaran::latest()->get();
        return view('admin.tahun_ajaran.index', compact('tahunAjarans'));
    }

    public function tahunAjaranStore(Request $request)
    {
        $request->validate([
            'nama_tahun' => 'required',
            'tanggal_mulai_gasal' => 'nullable|date',
            'tanggal_selesai_gasal' => 'nullable|date',
            'tanggal_mulai_genap' => 'nullable|date',
            'tanggal_selesai_genap' => 'nullable|date',
        ]);
        
        TahunAjaran::create([
            'nama_tahun' => $request->nama_tahun,
            'tanggal_mulai_gasal' => $request->tanggal_mulai_gasal,
            'tanggal_selesai_gasal' => $request->tanggal_selesai_gasal,
            'tanggal_mulai_genap' => $request->tanggal_mulai_genap,
            'tanggal_selesai_genap' => $request->tanggal_selesai_genap,
        ]);
        
        return back()->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function tahunAjaranUpdate(Request $request, $id)
    {
        $request->validate([
            'nama_tahun' => 'required',
            'tanggal_mulai_gasal' => 'nullable|date',
            'tanggal_selesai_gasal' => 'nullable|date',
            'tanggal_mulai_genap' => 'nullable|date',
            'tanggal_selesai_genap' => 'nullable|date',
        ]);
        
        $ta = TahunAjaran::findOrFail($id);
        $ta->update([
            'nama_tahun' => $request->nama_tahun,
            'tanggal_mulai_gasal' => $request->tanggal_mulai_gasal,
            'tanggal_selesai_gasal' => $request->tanggal_selesai_gasal,
            'tanggal_mulai_genap' => $request->tanggal_mulai_genap,
            'tanggal_selesai_genap' => $request->tanggal_selesai_genap,
        ]);
        
        return back()->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function tahunAjaranActivate($id)
    {
        TahunAjaran::query()->update(['is_active' => false]);
        TahunAjaran::find($id)->update(['is_active' => true]);
        return back()->with('success', 'Tahun ajaran aktif berhasil diubah.');
    }

    // LOGIKA KENAIKAN KELAS
    public function getSiswaByKelas($kelas)
    {
        $activePeriod = TahunAjaran::getActiveSemesterPeriod();
        $periodCode = $activePeriod ? $activePeriod['period_code'] : '';

        $siswas = Siswa::where('status', 'aktif')
            ->whereHas('rombels', function($q) use ($kelas, $periodCode) {
                $q->where('semester', $periodCode)
                  ->whereHas('kelas', function($qk) use ($kelas) {
                      $qk->where('nama_kelas', $kelas);
                  });
            })
            ->get();
        return response()->json($siswas);
    }

    public function kenaikanKelas(Request $request)
    {
        $request->validate([
            'dari_kelas' => 'required',
            'ke_kelas' => 'required',
            'student_ids' => 'required|array'
        ]);
        
        $activeTahun = TahunAjaran::where('is_active', true)->first();
        $tahun_ajaran = $activeTahun ? $activeTahun->nama_tahun : null;
        
        $nextTahunAjaran = date('Y') . '/' . (date('Y')+1);
        if ($tahun_ajaran) {
            $parts = explode('/', $tahun_ajaran);
            if (count($parts) == 2) {
                $nextTahunAjaran = ($parts[0] + 1) . '/' . ($parts[1] + 1);
            }
        }

        $activePeriod = TahunAjaran::getActiveSemesterPeriod();
        $periodCode = $activePeriod ? $activePeriod['period_code'] : '';

        // Ambil semua siswa aktif di kelas asal
        $allStudents = Siswa::where('status', 'aktif')
            ->whereHas('rombels', function($q) use ($request, $periodCode) {
                $q->where('semester', $periodCode)
                  ->whereHas('kelas', function($qk) use ($request) {
                      $qk->where('nama_kelas', $request->dari_kelas);
                  });
            })
            ->get();

        DB::transaction(function() use ($request, $allStudents, $tahun_ajaran, $nextTahunAjaran) {
            foreach ($allStudents as $student) {
                $isPromoted = in_array($student->id, $request->student_ids);
                $kelasAsal = $student->kelas;

                if ($isPromoted) {
                    if ($request->ke_kelas === 'lulus') {
                        $student->update([
                            'status' => 'lulus',
                        ]);
                        
                        \App\Models\RiwayatSiswa::create([
                            'siswa_id' => $student->id,
                            'tahun_ajaran' => $tahun_ajaran ?? '',
                            'kelas_asal' => $kelasAsal,
                            'kelas_tujuan' => 'Lulus',
                            'status' => 'Lulus',
                        ]);
                    } else {
                        // Kenaikan kelas didaftarkan pada tahun ajaran baru di semester Ganjil
                        $targetSemester = $nextTahunAjaran . '-Ganjil';
                        
                        $kelasTujuan = \App\Models\Kelas::firstOrCreate([
                            'nama_kelas' => $request->ke_kelas,
                            'semester' => $targetSemester
                        ]);

                        \App\Models\Rombel::updateOrCreate([
                            'siswa_id' => $student->id,
                            'semester' => $targetSemester
                        ], [
                            'kelas_id' => $kelasTujuan->id
                        ]);

                        \App\Models\RiwayatSiswa::create([
                            'siswa_id' => $student->id,
                            'tahun_ajaran' => $tahun_ajaran ?? '',
                            'kelas_asal' => $kelasAsal,
                            'kelas_tujuan' => $request->ke_kelas,
                            'status' => 'Naik Kelas',
                        ]);
                    }
                } else {
                    // TIDAK NAIK KELAS: tetap di kelas asal pada semester Ganjil tahun ajaran baru
                    $targetSemester = $nextTahunAjaran . '-Ganjil';
                    
                    $kelasTujuan = \App\Models\Kelas::firstOrCreate([
                        'nama_kelas' => $kelasAsal,
                        'semester' => $targetSemester
                    ]);

                    \App\Models\Rombel::updateOrCreate([
                        'siswa_id' => $student->id,
                        'semester' => $targetSemester
                    ], [
                        'kelas_id' => $kelasTujuan->id
                    ]);

                    \App\Models\RiwayatSiswa::create([
                        'siswa_id' => $student->id,
                        'tahun_ajaran' => $tahun_ajaran ?? '',
                        'kelas_asal' => $kelasAsal,
                        'kelas_tujuan' => $kelasAsal,
                        'status' => 'Tidak Naik Kelas',
                    ]);
                }
            }
        });

        return back()->with('success', 'Proses kenaikan kelas berhasil untuk ' . count($request->student_ids) . ' siswa.');
    }

    public function monitoring(Request $request)
    {
        $today = $request->tanggal ?? Carbon::today()->toDateString();
        $kelas = $request->kelas;
        
        $query = Presensi::with(['siswa', 'user'])->where('tanggal', $today);
        
        if ($kelas) {
            $activePeriod = TahunAjaran::getActiveSemesterPeriod();
            $periodCode = $activePeriod ? $activePeriod['period_code'] : '';

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
        return view('admin.monitoring', compact('presensis', 'today', 'kelas'));
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

        $isSearched = $request->hasAny(['tanggal_mulai', 'tanggal_selesai', 'tanggal', 'status', 'kelas']);

        if ($isSearched) {
            $laporans = $query->latest()->paginate(50)->withQueryString();
        } else {
            $laporans = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 50, 1, ['path' => $request->url(), 'query' => $request->query()]);
        }
        
        $tahunAjarans = TahunAjaran::all();

        return view('admin.laporan', compact('laporans', 'tahunAjarans', 'isSearched'));
    }

    public function siswaImport(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
        Excel::import(new \App\Imports\SiswaImport, $request->file('file'));
        return back()->with('success', 'Data siswa berhasil diimport.');
    }

    public function siswaExportExcel(Request $request)
    {
        $kelas = $request->kelas;
        return Excel::download(new class($kelas) implements 
            \Maatwebsite\Excel\Concerns\FromCollection, 
            \Maatwebsite\Excel\Concerns\WithHeadings,
            \Maatwebsite\Excel\Concerns\WithMapping,
            \Maatwebsite\Excel\Concerns\ShouldAutoSize 
        {
            protected $kelas;
            private $rowNumber = 0;

            public function __construct($kelas) { $this->kelas = $kelas; }

            public function collection() { 
                $activePeriod = TahunAjaran::getActiveSemesterPeriod();
                $periodCode = $activePeriod ? $activePeriod['period_code'] : '';

                $query = \App\Models\Siswa::query();
                if ($this->kelas) {
                    $query->whereHas('rombels', function($q) use ($periodCode) {
                        $q->where('semester', $periodCode)
                          ->whereHas('kelas', function($qk) {
                              $qk->where('nama_kelas', $this->kelas);
                          });
                    });
                }
                return $query->get(); 
            }

            public function headings(): array {
                return ['NO', 'NIS', 'NAMA SISWA', 'KELAS', 'TAHUN AJARAN', 'STATUS'];
            }

            public function map($siswa): array {
                $this->rowNumber++;
                return [
                    $this->rowNumber,
                    $siswa->nis ?? '-',
                    strtoupper($siswa->nama_siswa),
                    'KELAS ' . $siswa->kelas,
                    $siswa->tahun_ajaran,
                    strtoupper($siswa->status)
                ];
            }
        }, 'rekap_siswa' . ($kelas ? '_kelas_'.$kelas : '') . '.xlsx');
    }

    public function siswaExportPdf(Request $request)
    {
        $activePeriod = TahunAjaran::getActiveSemesterPeriod();
        $periodCode = $activePeriod ? $activePeriod['period_code'] : '';

        $query = Siswa::query();
        if ($request->kelas) {
            $query->whereHas('rombels', function($q) use ($request, $periodCode) {
                $q->where('semester', $periodCode)
                  ->whereHas('kelas', function($qk) use ($request) {
                      $qk->where('nama_kelas', $request->kelas);
                  });
            });
        }
        $siswas = $query->get();
        $pdf = Pdf::loadView('reports.data_siswa_pdf', compact('siswas'));
        return $pdf->download('data_siswa' . ($request->kelas ? '_kelas_'.$request->kelas : '') . '.pdf');
    }

    public function siswaCard(Siswa $siswa)
    {
        return view('admin.siswa.card', compact('siswa'));
    }

    public function tugasPenggantiIndex()
    {
        $pengajuans = TugasPengganti::with(['waliKelas', 'guruPengganti'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Ambil semua guru (selain admin dan kepsek) agar bisa ditugaskan menjadi guru pengganti
        $gurus = User::where(function($query) {
            $query->whereJsonDoesntContain('roles', 'admin')
                  ->whereJsonDoesntContain('roles', 'kepala_sekolah');
        })->get();
        return view('admin.tugas_pengganti.index', compact('pengajuans', 'gurus'));
    }

    public function tugasPenggantiAssign(Request $request, $id)
    {
        $request->validate([
            'guru_pengganti_id' => 'required|exists:users,id'
        ]);

        $tugas = TugasPengganti::findOrFail($id);
        $tugas->guru_pengganti_id = $request->guru_pengganti_id;
        $tugas->status = 'disetujui';
        $tugas->save();

        // Otomatis tambahkan peran "guru_pengganti" ke guru yang ditugaskan jika belum punya
        $assignedUser = User::find($request->guru_pengganti_id);
        if ($assignedUser) {
            $roles = $assignedUser->roles ?? [];
            if (!in_array('guru_pengganti', $roles)) {
                $roles[] = 'guru_pengganti';
                $assignedUser->roles = $roles;
                $assignedUser->save();
            }
        }

        return redirect()->back()->with('success', 'Guru Pengganti berhasil ditugaskan.');
    }

    public function tugasPenggantiReject(Request $request, $id)
    {
        $request->validate([
            'keterangan_ditolak' => 'required|string|max:500'
        ]);

        $tugas = TugasPengganti::findOrFail($id);
        $tugas->status = 'ditolak';
        $tugas->keterangan_ditolak = $request->keterangan_ditolak;
        $tugas->save();

        return redirect()->back()->with('success', 'Pengajuan berhasil ditolak.');
    }

    public function getRiwayat($id)
    {
        $siswa = Siswa::findOrFail($id);
        $riwayat = \App\Models\RiwayatSiswa::where('siswa_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json([
            'siswa' => $siswa,
            'riwayat' => $riwayat
        ]);
    }

    public function riwayatIndex(Request $request)
    {
        $query = Siswa::whereHas('riwayatSiswa');

        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_siswa', 'like', '%' . $search . '%')
                  ->orWhere('nis', 'like', '%' . $search . '%');
            });
        }

        if ($request->kelas) {
            $activePeriod = TahunAjaran::getActiveSemesterPeriod();
            $periodCode = $activePeriod ? $activePeriod['period_code'] : '';
            $query->whereHas('rombels', function($q) use ($request, $periodCode) {
                $q->where('semester', $periodCode)
                  ->whereHas('kelas', function($qk) use ($request) {
                      $qk->where('nama_kelas', $request->kelas);
                  });
            });
        }

        if ($request->status_siswa) {
            $query->where('status', $request->status_siswa);
        }

        // Filter berdasarkan riwayat tertentu
        if ($request->status || $request->tahun_ajaran) {
            $query->whereHas('riwayatSiswa', function($q) use ($request) {
                if ($request->status) $q->where('status', $request->status);
                if ($request->tahun_ajaran) $q->where('tahun_ajaran', $request->tahun_ajaran);
            });
        }

        $siswas = $query->with(['riwayatSiswa' => function($q) {
            $q->orderBy('created_at', 'desc');
        }])->orderBy('nama_siswa')->paginate(20)->withQueryString();

        $tahunAjarans = TahunAjaran::all();

        return view('admin.riwayat.index', compact('siswas', 'tahunAjarans'));
    }
}
