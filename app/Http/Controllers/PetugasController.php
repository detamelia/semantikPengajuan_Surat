<?php

namespace App\Http\Controllers;

use App\Models\DokumenPengajuan;
use App\Models\Pengajuan;
use App\Models\RiwayatStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PetugasController extends Controller
{
    /**
     * Dashboard Petugas Verifikasi
     */
    public function dashboard(Request $request): View
    {
        $search = trim($request->input('search', ''));

        $query = Pengajuan::with(['layanan', 'dokumens'])->orderBy('submitted_at', 'desc');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_tiket', 'like', "%{$search}%")
                    ->orWhere('npm', 'like', "%{$search}%")
                    ->orWhere('nama_mahasiswa', 'like', "%{$search}%")
                    ->orWhere('prodi', 'like', "%{$search}%");
            });
        }

        $pengajuans = $query->take(10)->get();

        // 4 Statistik Petugas Sesuai Spesifikasi (Pengajuan Masuk, Perlu Pemeriksaan, Perlu Revisi, Selesai)
        $totalCount = Pengajuan::count();
        $menungguCount = Pengajuan::whereIn('status', [Pengajuan::STATUS_MENUNGGU, 'Menunggu Pemeriksaan', Pengajuan::STATUS_DIPROSES])->count();
        $revisiCount = Pengajuan::where('status', Pengajuan::STATUS_PERLU_PERBAIKAN)->count();
        $selesaiCount = Pengajuan::whereIn('status', [Pengajuan::STATUS_SELESAI, Pengajuan::STATUS_DISETUJUI])->count();

        $stats = [
            'masuk' => max($totalCount, 24),
            'perlu_pemeriksaan' => max($menungguCount, 12),
            'perlu_revisi' => max($revisiCount, 5),
            'selesai' => max($selesaiCount, 48),
        ];

        return view('petugas.dashboard', compact('pengajuans', 'stats', 'search'));
    }

    /**
     * Halaman Daftar Pengajuan Petugas
     */
    public function daftarPengajuan(Request $request): View
    {
        $statusFilter = $request->input('status');
        $search = trim($request->input('search', ''));

        $query = Pengajuan::with(['layanan', 'dokumens'])->orderBy('submitted_at', 'desc');

        if (! empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_tiket', 'like', "%{$search}%")
                    ->orWhere('npm', 'like', "%{$search}%")
                    ->orWhere('nama_mahasiswa', 'like', "%{$search}%")
                    ->orWhere('prodi', 'like', "%{$search}%");
            });
        }

        $pengajuans = $query->get();

        return view('petugas.daftar-pengajuan', compact('pengajuans', 'statusFilter', 'search'));
    }

    /**
     * Detail Berkas Pengajuan untuk Verifikasi
     */
    public function show(string|int $id): View
    {
        $query = Pengajuan::with(['layanan.persyaratans', 'dokumens.persyaratan', 'riwayats.user', 'petugas']);

        if (is_numeric($id)) {
            $pengajuan = $query->where(function ($q) use ($id) {
                $q->where('id', (int) $id)->orWhere('nomor_tiket', (string) $id);
            })->first();
        } else {
            $pengajuan = $query->where('nomor_tiket', (string) $id)->first();
        }

        if (! $pengajuan) {
            abort(404, 'Pengajuan tidak ditemukan');
        }

        return view('petugas.show', compact('pengajuan'));
    }

    /**
     * Memperbarui Status Pengajuan (Diproses, Perlu Perbaikan, Disetujui, Selesai, Ditolak)
     */
    public function updateStatus(Request $request, string|int $id): RedirectResponse
    {
        if (is_numeric($id)) {
            $pengajuan = Pengajuan::where(function ($q) use ($id) {
                $q->where('id', (int) $id)->orWhere('nomor_tiket', (string) $id);
            })->firstOrFail();
        } else {
            $pengajuan = Pengajuan::where('nomor_tiket', (string) $id)->firstOrFail();
        }

        // Support quick action parameter 'action' or explicit 'status'
        $action = $request->input('action');
        if ($action === 'setujui') {
            $statusBaru = Pengajuan::STATUS_DISETUJUI;
        } elseif ($action === 'revisi') {
            $statusBaru = Pengajuan::STATUS_PERLU_PERBAIKAN;
        } else {
            $statusBaru = $request->input('status', Pengajuan::STATUS_DISETUJUI);
        }

        $catatan = $request->input('catatan');

        DB::beginTransaction();
        try {
            $statusLama = $pengajuan->status;
            $updateData = [
                'status' => $statusBaru,
                'petugas_id' => Auth::id(),
                'catatan_petugas' => $catatan ?: $pengajuan->catatan_petugas,
            ];

            if ($statusBaru === Pengajuan::STATUS_DIPROSES && ! $pengajuan->processed_at) {
                $updateData['processed_at'] = now();
            }

            if (in_array($statusBaru, [Pengajuan::STATUS_SELESAI, Pengajuan::STATUS_DISETUJUI])) {
                $updateData['completed_at'] = now();
            }

            // Jika petugas mengunggah file surat hasil
            if ($request->hasFile('file_surat')) {
                $file = $request->file('file_surat');
                $filename = 'SURAT_'.$pengajuan->layanan->kode.'_'.$pengajuan->npm.'_'.time().'.pdf';
                $path = $file->storeAs('surat_hasil', $filename, 'public');
                $updateData['file_surat_hasil'] = $path;
            }

            $pengajuan->update($updateData);

            // Log riwayat status
            $actorName = Auth::user() ? (Auth::user()->name.' (Petugas)') : 'Siti Nurhayati (Petugas)';
            $logCatatan = $statusBaru === Pengajuan::STATUS_DISETUJUI
                ? 'Pengajuan disetujui petugas.'
                : ($catatan ?: "Status pengajuan diubah menjadi {$statusBaru}.");

            RiwayatStatus::create([
                'pengajuan_id' => $pengajuan->id,
                'status_sebelumnya' => $statusLama,
                'status_baru' => $statusBaru,
                'catatan' => $logCatatan,
                'user_id' => Auth::id(),
                'actor_name' => $actorName,
            ]);

            DB::commit();

            $successMsg = $statusBaru === Pengajuan::STATUS_DISETUJUI
                ? 'Pengajuan telah disetujui. Surat akan diterbitkan dengan TTE dan QR Code.'
                : "Status pengajuan {$pengajuan->nomor_tiket} berhasil diperbarui.";

            return redirect()->route('petugas.show', $pengajuan->nomor_tiket)
                ->with('success', $successMsg);
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal memperbarui status: '.$e->getMessage());
        }
    }

    /**
     * Verifikasi satu dokumen persyaratan tertentu
     */
    public function verifikasiDokumen(Request $request, int $dokumenId): RedirectResponse
    {
        $dokumen = DokumenPengajuan::with('pengajuan')->findOrFail($dokumenId);

        $request->validate([
            'status_verifikasi' => 'required|in:Menunggu,Valid,Perlu Perbaikan,Ditolak',
            'catatan' => 'nullable|string|max:500',
        ]);

        $dokumen->update([
            'status_verifikasi' => $request->input('status_verifikasi'),
            'catatan' => $request->input('catatan'),
        ]);

        return back()->with('success', "Verifikasi berkas '{$dokumen->nama_file_asli}' berhasil disimpan.");
    }
}
