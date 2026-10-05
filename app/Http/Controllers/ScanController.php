<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ScanController extends Controller
{
    public function processScan(Request $request)
    {
        $request->validate([
            'qr_data' => 'required'
        ]);

        $today = Carbon::today()->toDateString();
        $qrData = $request->qr_data;

        // Cari siswa berdasarkan NIS atau ID
        $siswa = Siswa::where('nis', $qrData)->first();
        if (!$siswa) {
            $siswa = Siswa::find($qrData);
        }

        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan.'
            ], 404);
        }

        // Cek permission jika wali kelas
        if (Auth::user()->role === 'wali_kelas') {
            if ($siswa->kelas !== Auth::user()->kelas) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa ini bukan anggota kelas Anda.'
                ], 403);
            }
        }

        // Cek apakah sudah absen hari ini
        $exists = Presensi::where('siswa_id', $siswa->id)
            ->where('tanggal', $today)
            ->first();

        if ($exists && $exists->status === 'hadir') {
            return response()->json([
                'success' => true,
                'message' => "{$siswa->nama_siswa} sudah melakukan presensi hari ini.",
                'already_present' => true
            ]);
        }

        // Simpan presensi
        Presensi::updateOrCreate(
            ['siswa_id' => $siswa->id, 'tanggal' => $today],
            ['status' => 'hadir', 'user_id' => Auth::id()]
        );

        return response()->json([
            'success' => true,
            'message' => "Berhasil! {$siswa->nama_siswa} (Kelas {$siswa->kelas}) hadir.",
            'siswa_nama' => $siswa->nama_siswa
        ]);
    }

    public function processScanMapel(Request $request, $kelas_id)
    {
        $request->validate([
            'qr_data' => 'required',
            'nama_mapel' => 'required',
            'nama_pengganti' => 'required',
        ]);

        $today = Carbon::today()->toDateString();
        $qrData = $request->qr_data;

        $siswa = Siswa::where('nis', $qrData)->first();
        if (!$siswa) {
            $siswa = Siswa::find($qrData);
        }

        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan.'
            ], 404);
        }

        // Verify siswa is in the selected kelas_id in current semester
        $activePeriod = \App\Models\TahunAjaran::getActiveSemesterPeriod();
        $periodCode = $activePeriod ? $activePeriod['period_code'] : '';
        
        $inClass = $siswa->rombels()->where('semester', $periodCode)->where('kelas_id', $kelas_id)->exists();
        if (!$inClass) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa ini bukan anggota kelas tersebut.'
            ], 403);
        }

        $exists = Presensi::where('siswa_id', $siswa->id)
            ->where('tanggal', $today)
            ->first();

        // For Guru Mapel, they might overwrite or it's just 'hadir'.
        if ($exists && $exists->status === 'hadir' && $exists->user_id === Auth::id() && $exists->nama_mapel === $request->nama_mapel) {
            return response()->json([
                'success' => true,
                'message' => "{$siswa->nama_siswa} sudah melakukan presensi untuk Mapel ini.",
                'already_present' => true
            ]);
        }

        Presensi::updateOrCreate(
            ['siswa_id' => $siswa->id, 'tanggal' => $today],
            [
                'status' => 'hadir', 
                'user_id' => Auth::id(),
                'nama_pengganti' => $request->nama_pengganti,
                'nama_mapel' => $request->nama_mapel
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "Berhasil! {$siswa->nama_siswa} hadir (Mapel: {$request->nama_mapel}).",
            'siswa_nama' => $siswa->nama_siswa
        ]);
    }
}
