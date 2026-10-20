<?php

namespace App\Http\Controllers;

use App\Models\DokumenPengajuan;
use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Persyaratan;
use App\Models\RiwayatStatus;
use App\Services\SistemAService;
use App\Services\SuratGeneratorService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MahasiswaController extends Controller
{
    public function __construct(
        protected SistemAService $sistemA,
        protected SuratGeneratorService $suratGenerator
    ) {}

    /**
     * Dashboard Mahasiswa
     */
    public function dashboard(): View
    {
        $user = Auth::user();
        $query = Pengajuan::with(['layanan', 'riwayats'])->orderBy('submitted_at', 'desc');

        if ($user && $user->npm) {
            $query->where('npm', $user->npm);
        } elseif ($user) {
            $query->where('user_id', $user->id);
        }

        $pengajuans = $query->take(5)->get();

        $stats = [
            'total' => (clone $query)->count(),
            'menunggu' => (clone $query)->where('status', Pengajuan::STATUS_MENUNGGU)->count(),
            'diproses' => (clone $query)->where('status', Pengajuan::STATUS_DIPROSES)->count(),
            'perbaikan' => (clone $query)->where('status', Pengajuan::STATUS_PERLU_PERBAIKAN)->count(),
            'selesai' => (clone $query)->whereIn('status', [Pengajuan::STATUS_SELESAI, Pengajuan::STATUS_DISETUJUI])->count(),
        ];

        $layanans = Layanan::where('is_aktif', true)->with('persyaratans')->get();

        return view('mahasiswa.dashboard', compact('pengajuans', 'stats', 'layanans'));
    }

    /**
     * Katalog Informasi Layanan & Persyaratan
     */
    public function layananIndex(): View
    {
        $layanans = Layanan::where('is_aktif', true)->with('persyaratans')->get();

        return view('mahasiswa.layanan-index', compact('layanans'));
    }

    /**
     * Detail Layanan & Alur Pengajuan
     */
    public function layananShow(string $kode): View
    {
        $layanan = Layanan::where('kode', strtoupper($kode))->with('persyaratans')->firstOrFail();

        return view('mahasiswa.layanan-show', compact('layanan'));
    }

    /**
     * Form Pengajuan Surat Online
     */
    public function createPengajuan(?string $kode = null): View
    {
        $layanans = Layanan::where('is_aktif', true)->with('persyaratans')->get();
        $selectedLayanan = $kode ? Layanan::where('kode', strtoupper($kode))->with('persyaratans')->first() : null;

        // Prefill jika mahasiswa sudah login
        $user = Auth::user();
        $prefill = [
            'npm' => $user?->npm ?? '',
            'nama' => $user?->name ?? '',
            'prodi' => $user?->prodi ?? '',
            'email' => $user?->email ?? '',
            'phone' => $user?->phone ?? '',
        ];

        return view('mahasiswa.pengajuan-create', compact('layanans', 'selectedLayanan', 'prefill'));
    }

    /**
     * Endpoint AJAX internal untuk mencari mahasiswa dari Sistem A via REST API
     */
    public function apiLookupMhs(string $npm): JsonResponse
    {
        $result = $this->sistemA->findMahasiswaByNpm($npm);
        $statusCode = $result['success'] ? 200 : ($result['error_code'] === 'NOT_FOUND' ? 404 : 400);

        return response()->json($result, $statusCode);
    }

    /**
     * Menyimpan Pengajuan Surat Baru
     */
    public function storePengajuan(Request $request): RedirectResponse
    {
        $request->validate([
            'layanan_id' => 'required|exists:layanans,id',
            'npm' => 'required|string|min:4|max:30',
            'email' => 'required|email|max:100',
            'no_hp' => 'required|string|max:30',
            'keperluan' => 'required|string|min:10|max:1000',
        ], [
            'layanan_id.required' => 'Pilih jenis layanan surat.',
            'npm.required' => 'NPM mahasiswa wajib diisi.',
            'email.required' => 'Email aktif wajib diisi.',
            'no_hp.required' => 'Nomor WhatsApp/HP aktif wajib diisi.',
            'keperluan.required' => 'Rincian keperluan surat wajib diisi minimal 10 karakter.',
        ]);

        // 1. Verifikasi mahasiswa ke Sistem A (Pangkalan Data Mahasiswa)
        $mhsResult = $this->sistemA->findMahasiswaByNpm($request->input('npm'));

        if (! $mhsResult['success']) {
            return back()->withInput()->withErrors([
                'npm' => $mhsResult['message'] ?? 'Verifikasi data mahasiswa ke Sistem A gagal. Periksa kembali NPM Anda.',
            ]);
        }

        $mhsData = $mhsResult['data'];
        $layanan = Layanan::with('persyaratans')->findOrFail($request->input('layanan_id'));

        // 2. Validasi dokumen persyaratan yang diunggah
        $persyaratans = $layanan->persyaratans;
        $dokumenInputs = $request->file('dokumen', []);

        foreach ($persyaratans as $syarat) {
            $fieldKey = $syarat->id;
            $file = $dokumenInputs[$fieldKey] ?? null;

            if ($syarat->is_wajib && ! $file) {
                return back()->withInput()->withErrors([
                    "dokumen.{$fieldKey}" => "Dokumen '{$syarat->nama_persyaratan}' wajib diunggah.",
                ]);
            }

            if ($file) {
                // Periksa batas ukuran
                $maxKilobytes = $syarat->max_size_kb ?: 2048;
                if ($file->getSize() > ($maxKilobytes * 1024)) {
                    return back()->withInput()->withErrors([
                        "dokumen.{$fieldKey}" => "Ukuran file '{$syarat->nama_persyaratan}' melebihi batas maksimum ({$maxKilobytes} KB).",
                    ]);
                }

                // Periksa ekstensi berkas yang diizinkan
                $allowedExtensions = explode(',', str_replace(' ', '', strtolower($syarat->tipe_file ?: 'pdf,jpg,jpeg,png')));
                $clientExt = strtolower($file->getClientOriginalExtension());
                if (! in_array($clientExt, $allowedExtensions)) {
                    return back()->withInput()->withErrors([
                        "dokumen.{$fieldKey}" => "Format file '{$syarat->nama_persyaratan}' tidak diizinkan. Gunakan format: ".implode(', ', $allowedExtensions),
                    ]);
                }
            }
        }

        // 3. Generate Nomor Tiket Unik
        $nomorTiket = 'SILAM-'.date('Ym').'-'.strtoupper(substr(uniqid(), -5));

        DB::beginTransaction();
        try {
            // 4. Simpan Record Pengajuan
            $pengajuan = Pengajuan::create([
                'nomor_tiket' => $nomorTiket,
                'layanan_id' => $layanan->id,
                'user_id' => Auth::id(),
                'npm' => $mhsData['npm'],
                'nama_mahasiswa' => $mhsData['nama'],
                'prodi' => $mhsData['prodi'],
                'angkatan' => $mhsData['angkatan'],
                'email' => $request->input('email'),
                'no_hp' => $request->input('no_hp'),
                'keperluan' => $request->input('keperluan'),
                'uri_semantik_mahasiswa' => $mhsData['uri_semantik'],
                'status' => Pengajuan::STATUS_MENUNGGU,
                'submitted_at' => now(),
            ]);

            // 5. Simpan Berkas Dokumen Persyaratan
            foreach ($persyaratans as $syarat) {
                $fieldKey = $syarat->id;
                $file = $dokumenInputs[$fieldKey] ?? null;

                if ($file) {
                    $originalName = $file->getClientOriginalName();
                    $mimeType = $file->getClientMimeType() ?: $file->getMimeType();
                    $sizeKb = ceil($file->getSize() / 1024);
                    $path = $file->store("dokumen_pengajuan/{$nomorTiket}", 'public');

                    DokumenPengajuan::create([
                        'pengajuan_id' => $pengajuan->id,
                        'persyaratan_id' => $syarat->id,
                        'nama_file_asli' => $originalName,
                        'file_path' => $path,
                        'mime_type' => $mimeType,
                        'file_size_kb' => $sizeKb,
                        'status_verifikasi' => 'Menunggu',
                    ]);
                }
            }

            // 6. Catat Riwayat Status Awal
            RiwayatStatus::create([
                'pengajuan_id' => $pengajuan->id,
                'status_sebelumnya' => null,
                'status_baru' => Pengajuan::STATUS_MENUNGGU,
                'catatan' => 'Permohonan surat berhasil diajukan dan masuk ke antrean verifikasi petugas.',
                'user_id' => Auth::id(),
                'actor_name' => $mhsData['nama'].' (Mahasiswa)',
            ]);

            DB::commit();

            return redirect()->route('pengajuan.show', $nomorTiket)->with([
                'success' => 'Pengajuan surat berhasil dikirim!',
                'tiket_baru' => $nomorTiket,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan pengajuan: '.$e->getMessage());

            return back()->withInput()->withErrors([
                'general' => 'Terjadi kesalahan sistem saat menyimpan pengajuan: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Halaman Lacak Status Pengajuan (Publik / Mahasiswa)
     */
    public function lacak(Request $request): View
    {
        $tiket = trim($request->input('tiket', ''));
        $npm = trim($request->input('npm', Auth::user()?->npm ?? '2415061120'));

        $query = Pengajuan::with(['layanan', 'dokumens.persyaratan', 'riwayats'])->orderBy('submitted_at', 'desc');
        $pengajuans = $query->get();

        $selectedPengajuan = null;
        if (! empty($tiket)) {
            $selectedPengajuan = $pengajuans->firstWhere('nomor_tiket', $tiket);
        }
        if (! $selectedPengajuan) {
            $selectedPengajuan = $pengajuans->firstWhere('nomor_tiket', 'SILAM-2025-0418')
                ?: $pengajuans->firstWhere('status', Pengajuan::STATUS_DIPROSES)
                ?: $pengajuans->first();
        }

        $revisionPengajuan = $pengajuans->firstWhere('nomor_tiket', 'SILAM-2025-0392')
            ?: $pengajuans->firstWhere('status', Pengajuan::STATUS_PERLU_PERBAIKAN);

        return view('mahasiswa.lacak', compact('tiket', 'npm', 'pengajuans', 'selectedPengajuan', 'revisionPengajuan'));
    }

    /**
     * Detail Pengajuan Surat Berdasarkan Nomor Tiket
     */
    public function showPengajuan(string $nomorTiket): View
    {
        $pengajuan = Pengajuan::with(['layanan.persyaratans', 'dokumens.persyaratan', 'riwayats'])->where('nomor_tiket', $nomorTiket)->firstOrFail();

        return view('mahasiswa.pengajuan-detail', compact('pengajuan'));
    }

    /**
     * Mahasiswa mengunggah ulang dokumen revisi (jika status "Perlu Perbaikan")
     */
    public function revisiDokumen(Request $request, string $nomorTiket): RedirectResponse
    {
        $pengajuan = Pengajuan::where('nomor_tiket', $nomorTiket)->firstOrFail();

        if ($pengajuan->status !== Pengajuan::STATUS_PERLU_PERBAIKAN) {
            return back()->with('error', 'Pengajuan ini tidak dalam status Perlu Perbaikan.');
        }

        $request->validate([
            'dokumen_id' => 'required|exists:dokumen_pengajuans,id',
            'file_revisi' => 'required|file|max:5120',
            'catatan_mahasiswa' => 'nullable|string|max:500',
        ]);

        $dokumen = DokumenPengajuan::where('id', $request->input('dokumen_id'))
            ->where('pengajuan_id', $pengajuan->id)
            ->firstOrFail();

        $file = $request->file('file_revisi');
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getClientMimeType() ?: $file->getMimeType();
        $sizeKb = ceil($file->getSize() / 1024);

        // Hapus file lama jika ada
        if (Storage::disk('public')->exists($dokumen->file_path)) {
            Storage::disk('public')->delete($dokumen->file_path);
        }

        $path = $file->store("dokumen_pengajuan/{$pengajuan->nomor_tiket}", 'public');

        $dokumen->update([
            'nama_file_asli' => $originalName,
            'file_path' => $path,
            'mime_type' => $mimeType,
            'file_size_kb' => $sizeKb,
            'status_verifikasi' => 'Menunggu',
            'catatan' => 'Berkas perbaikan telah diunggah oleh mahasiswa.',
        ]);

        // Kembalikan status pengajuan ke Menunggu Verifikasi
        $statusLama = $pengajuan->status;
        $pengajuan->update([
            'status' => Pengajuan::STATUS_MENUNGGU,
        ]);

        RiwayatStatus::create([
            'pengajuan_id' => $pengajuan->id,
            'status_sebelumnya' => $statusLama,
            'status_baru' => Pengajuan::STATUS_MENUNGGU,
            'catatan' => 'Mahasiswa mengunggah berkas revisi untuk '.($dokumen->persyaratan->nama_persyaratan ?? 'dokumen').'. Catatan: '.($request->input('catatan_mahasiswa') ?: 'Telah diperbaiki sesuai arahan petugas.'),
            'user_id' => Auth::id(),
            'actor_name' => $pengajuan->nama_mahasiswa.' (Mahasiswa)',
        ]);

        return redirect()->route('pengajuan.show', $nomorTiket)
            ->with('success', 'Dokumen perbaikan berhasil diunggah. Pengajuan dikembalikan ke antrean verifikasi petugas.');
    }

    /**
     * Unduh Surat Resmi yang telah disetujui / selesai
     */
    public function unduhSurat(string $nomorTiket): Response|RedirectResponse
    {
        $pengajuan = Pengajuan::with('layanan')->where('nomor_tiket', $nomorTiket)->firstOrFail();

        if (! in_array($pengajuan->status, [Pengajuan::STATUS_SELESAI, Pengajuan::STATUS_DISETUJUI])) {
            return back()->with('error', 'Surat belum selesai diproses atau belum disetujui petugas.');
        }

        // Jika petugas mengunggah berkas PDF surat hasil langsung
        if ($pengajuan->file_surat_hasil && Storage::disk('public')->exists($pengajuan->file_surat_hasil)) {
            return response()->download(
                Storage::disk('public')->path($pengajuan->file_surat_hasil),
                $pengajuan->layanan->kode.'_'.$pengajuan->npm.'.pdf'
            );
        }

        // Alternatif: Render tampilan cetak surat resmi ber-kop universitas
        $html = $this->suratGenerator->renderSuratHtml($pengajuan);

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }

    /**
     * Riwayat Pengajuan Mahasiswa
     */
    public function riwayat(Request $request): View
    {
        $tiket = trim($request->input('tiket', ''));
        $npm = trim($request->input('npm', Auth::user()?->npm ?? '2415061120'));

        $query = Pengajuan::with(['layanan', 'dokumens.persyaratan', 'riwayats'])->orderBy('submitted_at', 'desc');
        $pengajuans = $query->get();

        $selectedPengajuan = null;
        if (! empty($tiket)) {
            $selectedPengajuan = $pengajuans->firstWhere('nomor_tiket', $tiket);
        }
        if (! $selectedPengajuan) {
            $selectedPengajuan = $pengajuans->firstWhere('nomor_tiket', 'SILAM-2025-0418')
                ?: $pengajuans->firstWhere('status', Pengajuan::STATUS_DIPROSES)
                ?: $pengajuans->first();
        }

        $revisionPengajuan = $pengajuans->firstWhere('nomor_tiket', 'SILAM-2025-0392')
            ?: $pengajuans->firstWhere('status', Pengajuan::STATUS_PERLU_PERBAIKAN);

        return view('mahasiswa.riwayat', compact('tiket', 'npm', 'pengajuans', 'selectedPengajuan', 'revisionPengajuan'));
    }
}
