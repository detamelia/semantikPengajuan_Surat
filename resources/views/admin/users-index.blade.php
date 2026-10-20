@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="container" style="padding-top: 2rem; padding-bottom: 3rem;">
    <!-- Breadcrumbs -->
    <div style="margin-bottom: 1.5rem; font-size: 0.85rem; color: var(--slate-500);">
        <a href="{{ route('admin.dashboard') }}" style="color: var(--primary-600); text-decoration: none;">Dashboard</a>
        <span style="margin: 0 0.4rem;">&gt;</span>
        <span style="color: var(--slate-700); font-weight: 600;">Manajemen Pengguna</span>
    </div>

    <div class="grid grid-cols-3" style="gap: 1.5rem; align-items: flex-start;">
        <!-- Kolom Kiri: Tabel Pengguna -->
        <div style="grid-column: span 2;">
            <div class="card" style="box-shadow: var(--shadow-md);">
                <div class="card-header">
                    <h2 class="card-title">Daftar Akun Pengguna Terdaftar ({{ $users->total() }})</h2>
                </div>

                <div class="card-body" style="padding: 0;">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama Pengguna</th>
                                    <th>Email</th>
                                    <th>Peran (Role)</th>
                                    <th>NPM / Prodi</th>
                                    <th>Terdaftar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $u)
                                    <tr>
                                        <td>
                                            <strong style="color: var(--slate-900);">{{ $u->name }}</strong>
                                            @if($u->phone)
                                                <div style="font-size: 0.75rem; color: var(--slate-500);">HP: {{ $u->phone }}</div>
                                            @endif
                                        </td>
                                        <td><span style="font-size: 0.85rem; color: var(--slate-700);">{{ $u->email }}</span></td>
                                        <td>
                                            @if($u->isAdmin())
                                                <span class="badge badge-indigo">Administrator</span>
                                            @elseif($u->isPetugas())
                                                <span class="badge badge-blue">Petugas Verifikasi</span>
                                            @else
                                                <span class="badge badge-slate">Mahasiswa</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($u->npm)
                                                <strong style="font-family: var(--font-mono); font-size: 0.8rem;">{{ $u->npm }}</strong>
                                                <div style="font-size: 0.75rem; color: var(--slate-500);">{{ $u->prodi }}</div>
                                            @else
                                                <span style="color: var(--slate-400);">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span style="font-size: 0.75rem; color: var(--slate-500);">{{ $u->created_at->format('d/m/Y') }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($users->hasPages())
                    <div class="card-footer">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Kolom Kanan: Form Tambah Pengguna Baru -->
        <div>
            <div class="card" style="box-shadow: var(--shadow-md);">
                <div class="card-header" style="background: var(--slate-900); color: white;">
                    <h3 class="card-title" style="color: white; font-size: 1rem;">+ Tambah Pengguna Baru</h3>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="name" class="form-label">Nama Lengkap <span style="color: var(--rose-600);">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" required placeholder="Contoh: Rina Marlina, S.T.">
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email <span style="color: var(--rose-600);">*</span></label>
                            <input type="email" name="email" id="email" class="form-control" required placeholder="rina@silam.ac.id">
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">Kata Sandi <span style="color: var(--rose-600);">*</span></label>
                            <input type="password" name="password" id="password" class="form-control" required placeholder="Minimal 6 karakter">
                        </div>

                        <div class="form-group">
                            <label for="role" class="form-label">Peran (Role) <span style="color: var(--rose-600);">*</span></label>
                            <select name="role" id="role" class="form-control" required>
                                <option value="petugas">Petugas Verifikasi Akademik</option>
                                <option value="admin">Administrator Sistem</option>
                                <option value="mahasiswa">Mahasiswa</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="phone" class="form-label">Nomor HP</label>
                            <input type="text" name="phone" id="phone" class="form-control" placeholder="08xxxxxxxxxx">
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            Simpan Pengguna Baru
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
