<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Persyaratan;
use App\Models\User;
use App\Services\SistemAService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __construct(
        protected SistemAService $sistemA
    ) {}

    /**
     * Dashboard Admin
     */
    public function dashboard(): View
    {
        $stats = [
            'total_pengajuan' => Pengajuan::count(),
            'total_layanan' => Layanan::count(),
            'total_persyaratan' => Persyaratan::count(),
            'total_petugas' => User::where('role', User::ROLE_PETUGAS)->count(),
            'total_mahasiswa' => User::where('role', User::ROLE_MAHASISWA)->count(),
        ];

        $sistemAHealth = $this->sistemA->checkHealth();
        $recentPengajuans = Pengajuan::with('layanan')->latest('submitted_at')->take(5)->get();
        $layanans = Layanan::withCount('persyaratans', 'pengajuans')->get();

        return view('admin.dashboard', compact('stats', 'sistemAHealth', 'recentPengajuans', 'layanans'));
    }

    /**
     * Kelola Master Layanan
     */
    public function layananIndex(): View
    {
        $layanans = Layanan::withCount('persyaratans', 'pengajuans')->get();

        return view('admin.layanan-index', compact('layanans'));
    }

    public function layananCreate(): View
    {
        return view('admin.layanan-create');
    }

    public function layananStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:50|unique:layanans,kode',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'alur_pengajuan' => 'nullable|string',
            'estimasi_hari' => 'required|integer|min:1|max:30',
            'icon' => 'nullable|string|max:50',
            'is_aktif' => 'boolean',
        ]);

        $validated['kode'] = strtoupper(trim($validated['kode']));
        $validated['is_aktif'] = $request->boolean('is_aktif', true);

        Layanan::create($validated);

        return redirect()->route('admin.layanan.index')->with('success', "Layanan '{$validated['nama']}' berhasil ditambahkan.");
    }

    public function layananEdit(int $id): View
    {
        $layanan = Layanan::with('persyaratans')->findOrFail($id);

        return view('admin.layanan-edit', compact('layanan'));
    }

    public function layananUpdate(Request $request, int $id): RedirectResponse
    {
        $layanan = Layanan::findOrFail($id);

        $validated = $request->validate([
            'kode' => 'required|string|max:50|unique:layanans,kode,'.$id,
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'alur_pengajuan' => 'nullable|string',
            'estimasi_hari' => 'required|integer|min:1|max:30',
            'icon' => 'nullable|string|max:50',
            'is_aktif' => 'boolean',
        ]);

        $validated['kode'] = strtoupper(trim($validated['kode']));
        $validated['is_aktif'] = $request->boolean('is_aktif');

        $layanan->update($validated);

        return redirect()->route('admin.layanan.index')->with('success', "Layanan '{$layanan->nama}' berhasil diperbarui.");
    }

    public function layananDestroy(int $id): RedirectResponse
    {
        $layanan = Layanan::withCount('pengajuans')->findOrFail($id);

        if ($layanan->pengajuans_count > 0) {
            return back()->with('error', "Layanan ini tidak dapat dihapus karena sudah memiliki {$layanan->pengajuans_count} riwayat pengajuan surat. Anda dapat menonaktifkannya.");
        }

        $layanan->delete();

        return redirect()->route('admin.layanan.index')->with('success', 'Layanan berhasil dihapus.');
    }

    /**
     * Tambah Persyaratan Dokumen ke Layanan
     */
    public function persyaratanStore(Request $request, int $layananId): RedirectResponse
    {
        $layanan = Layanan::findOrFail($layananId);

        $validated = $request->validate([
            'nama_persyaratan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tipe_file' => 'required|string|max:100',
            'max_size_kb' => 'required|integer|min:256|max:10240',
            'is_wajib' => 'boolean',
            'urutan' => 'nullable|integer',
        ]);

        $validated['layanan_id'] = $layanan->id;
        $validated['is_wajib'] = $request->boolean('is_wajib', true);
        $validated['urutan'] = $validated['urutan'] ?? ($layanan->persyaratans()->count() + 1);

        Persyaratan::create($validated);

        return back()->with('success', "Persyaratan '{$validated['nama_persyaratan']}' berhasil ditambahkan ke layanan {$layanan->nama}.");
    }

    public function persyaratanDestroy(int $id): RedirectResponse
    {
        $syarat = Persyaratan::with('layanan')->findOrFail($id);
        $nama = $syarat->nama_persyaratan;
        $syarat->delete();

        return back()->with('success', "Persyaratan '{$nama}' berhasil dihapus.");
    }

    /**
     * Manajemen Pengguna (Admin, Petugas)
     */
    public function usersIndex(): View
    {
        $users = User::orderBy('role')->orderBy('name')->paginate(15);

        return view('admin.users-index', compact('users'));
    }

    public function userStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,petugas,mahasiswa',
            'phone' => 'nullable|string|max:30',
            'npm' => 'nullable|string|max:30',
            'prodi' => 'nullable|string|max:100',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);

        return back()->with('success', "Pengguna {$validated['name']} ({$validated['role']}) berhasil ditambahkan.");
    }

    /**
     * AJAX Check Health Sistem A
     */
    public function checkSistemA(): JsonResponse
    {
        $result = $this->sistemA->checkHealth();

        return response()->json($result);
    }
}
