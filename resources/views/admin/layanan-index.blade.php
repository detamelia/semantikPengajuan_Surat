@extends('layouts.app')

@section('title', 'Kelola Layanan Administrasi')

@section('content')
<div class="container" style="padding-top: 2rem; padding-bottom: 3rem;">
    <!-- Breadcrumbs -->
    <div style="margin-bottom: 1.5rem; font-size: 0.85rem; color: var(--slate-500);">
        <a href="{{ route('admin.dashboard') }}" style="color: var(--primary-600); text-decoration: none;">Admin Dashboard</a>
        <span style="margin: 0 0.4rem;">&gt;</span>
        <span style="color: var(--slate-700); font-weight: 600;">Kelola Layanan Surat</span>
    </div>

    <div class="card" style="box-shadow: var(--shadow-md);">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
            <div>
                <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--slate-900);">Katalog Master Layanan Surat</h2>
                <p style="font-size: 0.825rem; color: var(--slate-500); margin-top: 0.2rem;">Tambah jenis surat baru, atur alur pengajuan, dan tentukan berkas persyaratannya</p>
            </div>
            <a href="{{ route('admin.layanan.create') }}" class="btn btn-primary btn-sm">
                + Tambah Layanan Baru
            </a>
        </div>

        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Layanan</th>
                            <th>Estimasi</th>
                            <th>Persyaratan Dokumen</th>
                            <th>Total Pengajuan</th>
                            <th>Status Aktif</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($layanans as $lay)
                            <tr>
                                <td>
                                    <span class="badge badge-indigo" style="font-family: var(--font-mono); font-size: 0.75rem;">
                                        {{ $lay->kode }}
                                    </span>
                                </td>
                                <td>
                                    <strong style="color: var(--slate-900); font-size: 0.95rem;">{{ $lay->nama }}</strong>
                                    <div style="font-size: 0.8rem; color: var(--slate-500); margin-top: 0.2rem;">
                                        {{ \Illuminate\Support\Str::limit($lay->deskripsi, 80) }}
                                    </div>
                                </td>
                                <td>{{ $lay->estimasi_hari }} Hari Kerja</td>
                                <td>
                                    <span class="badge badge-slate">{{ $lay->persyaratans_count }} Dokumen</span>
                                </td>
                                <td>{{ $lay->pengajuans_count }} Permohonan</td>
                                <td>
                                    @if($lay->is_aktif)
                                        <span class="badge badge-emerald">Aktif</span>
                                    @else
                                        <span class="badge badge-slate">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.35rem;">
                                        <a href="{{ route('admin.layanan.edit', $lay->id) }}" class="btn btn-outline btn-sm">
                                            Edit &amp; Syarat
                                        </a>
                                        @if($lay->pengajuans_count === 0)
                                            <form action="{{ route('admin.layanan.destroy', $lay->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus layanan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: var(--slate-500); padding: 2.5rem;">
                                    Belum ada layanan yang ditambahkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
