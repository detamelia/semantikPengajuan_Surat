<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\SistemAService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request, SistemAService $sistemA): RedirectResponse
    {
        // 1. Jika password diisi, gunakan autentikasi manual dengan kredensial
        if ($request->filled('password')) {
            $loginInput = trim((string) ($request->input('akun_kampus') ?? $request->input('email')));
            $password = (string) $request->input('password');

            $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'npm';
            $credentials = [
                $fieldType => $loginInput,
                'password' => $password,
            ];

            $remember = $request->boolean('remember');

            if (Auth::attempt($credentials, $remember)) {
                $request->session()->regenerate();
                $user = Auth::user();

                if ($user->isAdmin()) {
                    return redirect()->intended(route('admin.dashboard'))->with('success', 'Selamat datang kembali, Administrator.');
                }

                if ($user->isPetugas()) {
                    return redirect()->intended(route('petugas.dashboard'))->with('success', 'Selamat datang, Petugas Administrasi.');
                }

                return redirect()->intended(route('mahasiswa.dashboard'))->with('success', 'Selamat datang di SILAM, '.$user->name.'.');
            }

            return back()->withErrors([
                'email' => 'Kombinasi akun kampus dan kata sandi tidak cocok.',
                'akun_kampus' => 'Kombinasi akun kampus dan kata sandi tidak cocok.',
            ])->withInput($request->only('akun_kampus', 'email', 'role'));
        }

        // 2. Alur SSO Kampus (Single Sign-On)
        $role = $request->input('role', 'mahasiswa');
        $akun = trim((string) ($request->input('akun_kampus') ?? $request->input('email')));

        if ($role === 'mahasiswa') {
            $user = null;

            if (! empty($akun)) {
                // Cari mahasiswa lokal berdasarkan NPM atau Email
                $user = User::where('npm', $akun)
                    ->orWhere('email', $akun)
                    ->first();

                // Jika belum ada di lokal SILAM, cek langsung ke Sistem A
                if (! $user) {
                    $mhsCheck = $sistemA->findMahasiswaByNpm($akun);
                    if ($mhsCheck['success']) {
                        $mhsData = $mhsCheck['data'];
                        $user = User::create([
                            'name' => $mhsData['nama'],
                            'email' => strtolower($mhsData['npm']).'@mhs.silam.ac.id',
                            'password' => Hash::make('password'),
                            'role' => User::ROLE_MAHASISWA,
                            'npm' => $mhsData['npm'],
                            'prodi' => $mhsData['prodi'],
                        ]);
                    } else {
                        return back()->withInput()->withErrors([
                            'akun_kampus' => "NPM '{$akun}' tidak ditemukan pada pangkalan data Sistem A. Pastikan NPM sudah benar.",
                        ]);
                    }
                }
            } else {
                // Akun demo mahasiswa default
                $user = User::where('role', User::ROLE_MAHASISWA)->first();
            }

            if ($user) {
                Auth::login($user);
                $request->session()->regenerate();

                return redirect()->intended(route('mahasiswa.dashboard'))
                    ->with('success', 'Berhasil masuk melalui SSO Kampus sebagai Mahasiswa ('.$user->name.').');
            }
        } elseif ($role === 'petugas') {
            $user = null;
            if (! empty($akun)) {
                $user = User::where('role', User::ROLE_PETUGAS)
                    ->where(function ($q) use ($akun) {
                        $q->where('email', $akun)->orWhere('name', 'like', "%{$akun}%");
                    })->first();
            }

            if (! $user) {
                $user = User::where('role', User::ROLE_PETUGAS)->first();
            }

            if ($user) {
                Auth::login($user);
                $request->session()->regenerate();

                return redirect()->intended(route('petugas.dashboard'))
                    ->with('success', 'Berhasil masuk melalui SSO Kampus sebagai Petugas Verifikasi.');
            }
        } elseif ($role === 'admin') {
            $user = null;
            if (! empty($akun)) {
                $user = User::where('role', User::ROLE_ADMIN)
                    ->where(function ($q) use ($akun) {
                        $q->where('email', $akun)->orWhere('name', 'like', "%{$akun}%");
                    })->first();
            }

            if (! $user) {
                $user = User::where('role', User::ROLE_ADMIN)->first();
            }

            if ($user) {
                Auth::login($user);
                $request->session()->regenerate();

                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Berhasil masuk melalui SSO Kampus sebagai Administrator.');
            }
        }

        return back()->withInput()->withErrors([
            'akun_kampus' => 'Akun pengguna tidak ditemukan.',
        ]);
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request, SistemAService $sistemA): RedirectResponse
    {
        $validated = $request->validate([
            'npm' => 'required|string|min:4|max:30',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
        ], [
            'npm.required' => 'NPM mahasiswa wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar dalam sistem.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        // Cek data mahasiswa ke Sistem A
        $mhsCheck = $sistemA->findMahasiswaByNpm($validated['npm']);
        $nama = 'Mahasiswa';
        $prodi = null;

        if ($mhsCheck['success']) {
            $nama = $mhsCheck['data']['nama'];
            $prodi = $mhsCheck['data']['prodi'];
        } else {
            // Jika mahasiswa tidak ditemukan di Sistem A
            return back()->withInput()->withErrors([
                'npm' => $mhsCheck['message'] ?? 'NPM tidak terdaftar pada pangkalan data Sistem A.',
            ]);
        }

        $user = User::create([
            'name' => $nama,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => User::ROLE_MAHASISWA,
            'npm' => $validated['npm'],
            'prodi' => $prodi,
            'phone' => $validated['phone'],
        ]);

        Auth::login($user);

        return redirect()->route('mahasiswa.dashboard')
            ->with('success', "Akun berhasil dibuat dan terverifikasi dengan data Sistem A ({$nama} - {$prodi}).");
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah berhasil keluar dari sistem.');
    }
}
