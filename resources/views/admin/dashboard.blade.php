@extends('layouts.app')

@section('title', 'Dasbor Administrator')

@section('content')
<div class="container" style="padding-top: 2rem; padding-bottom: 3rem;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <span class="badge badge-indigo">PORTAL ADMINISTRATOR</span>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--slate-900); margin-top: 0.35rem;">
                Administrasi Sistem SILAM
            </h1>
            <p style="font-size: 0.9rem; color: var(--slate-500);">
                Konfigurasi master layanan surat, persyaratan dokumen, akun pengguna, dan pemantauan integrasi Sistem A.
            </p>
        </div>

        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.layanan.index') }}" class="btn btn-outline btn-sm">
                <span>📁</span> Kelola Layanan
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-sm">
                <span>👥</span> Kelola Pengguna
            </a>
            <a href="{{ route('semantik.explorer') }}" class="btn btn-primary btn-sm">
                <span>🌐</span> Semantic Web Explorer
            </a>
        </div>
    </div>

    <!-- Sistem A Health Monitor Card -->
    <div class="card" style="margin-bottom: 2rem; border-left: 4px solid {{ $sistemAHealth['online'] ? 'var(--emerald-600)' : 'var(--rose-600)' }};">
        <div class="card-body" style="padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 44px; height: 44px; border-radius: var(--radius-md); background: {{ $sistemAHealth['online'] ? 'var(--emerald-50)' : 'var(--rose-50)' }}; color: {{ $sistemAHealth['online'] ? 'var(--emerald-700)' : 'var(--rose-700)' }}; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                    {{ $sistemAHealth['online'] ? '⚡' : '⚠️' }}
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <h4 style="font-size: 1rem; font-weight: 700; color: var(--slate-900);">Status Integrasi Sistem A (Pangkalan Data Mahasiswa)</h4>
                        @if($sistemAHealth['online'])
                            <span class="badge badge-emerald">Online &bull; HTTP {{ $sistemAHealth['status_code'] }}</span>
                        @else
                            <span class="badge badge-rose">Offline / Tidak Terjangkau</span>
                        @endif
                    </div>
                    <p style="font-size: 0.825rem; color: var(--slate-600); margin-top: 0.2rem;">
                        Endpoint URL: <code style="font-family: var(--font-mono); color: var(--primary-700);">{{ $sistemAHealth['url'] }}</code> &bull; {{ $sistemAHealth['message'] }}
                    </p>
                </div>
            </div>

            <button type="button" id="btn-ping-sistem-a" class="btn btn-outline btn-sm">
                <span>🔄</span> Uji Koneksi API
            </button>
        </div>
    </div>

    <!-- Stat Widgets -->
    <div class="grid grid-cols-4" style="margin-bottom: 2.5rem;">
        <div class="stat-card">
            <div class="stat-info">
                <span>Total Pengajuan</span>
                <h3>{{ $stats['total_pengajuan'] }}</h3>
            </div>
            <div class="stat-icon" style="background: var(--primary-50); color: var(--primary-600);">📋</div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <span>Katalog Layanan</span>
                <h3 style="color: var(--blue-700);">{{ $stats['total_layanan'] }}</h3>
            </div>
            <div class="stat-icon" style="background: var(--blue-50); color: var(--blue-600);">🏛️</div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <span>Persyaratan Berkas</span>
                <h3 style="color: var(--amber-700);">{{ $stats['total_persyaratan'] }}</h3>
            </div>
            <div class="stat-icon" style="background: var(--amber-50); color: var(--amber-600);">📁</div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <span>Petugas Akademik</span>
                <h3 style="color: var(--emerald-700);">{{ $stats['total_petugas'] }}</h3>
            </div>
            <div class="stat-icon" style="background: var(--emerald-50); color: var(--emerald-600);">👤</div>
        </div>
    </div>

    <!-- Grid Dua Kolom: Layanan & Pengajuan Terbaru -->
    <div class="grid grid-cols-2" style="gap: 1.5rem;">
        <!-- Layanan List -->
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="card-title">Katalog Master Layanan Surat</h3>
                <a href="{{ route('admin.layanan.create') }}" class="btn btn-primary btn-sm">+ Layanan</a>
            </div>

            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Layanan</th>
                                <th>Syarat</th>
                                <th>Pengajuan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($layanans as $lay)
                                <tr>
                                    <td>
                                        <span class="badge badge-indigo" style="font-family: var(--font-mono); font-size: 0.7rem;">{{ $lay->kode }}</span>
                                    </td>
                                    <td>
                                        <strong style="color: var(--slate-900); font-size: 0.875rem;">{{ $lay->nama }}</strong>
                                        <div style="font-size: 0.75rem; color: var(--slate-500);">Estimasi: {{ $lay->estimasi_hari }} Hari</div>
                                    </td>
                                    <td>{{ $lay->persyaratans_count }} Dokumen</td>
                                    <td>{{ $lay->pengajuans_count }} Kali</td>
                                    <td>
                                        <a href="{{ route('admin.layanan.edit', $lay->id) }}" class="btn btn-outline btn-sm">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Submissions -->
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="card-title">Pengajuan Masuk Terbaru</h3>
                <a href="{{ route('petugas.dashboard') }}" style="font-size: 0.85rem; color: var(--primary-600); text-decoration: none; font-weight: 600;">Lihat Antrean &rarr;</a>
            </div>

            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tiket</th>
                                <th>Mahasiswa</th>
                                <th>Layanan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentPengajuans as $item)
                                <tr>
                                    <td>
                                        <strong style="font-family: var(--font-mono); color: var(--primary-700); font-size: 0.85rem;">
                                            {{ $item->nomor_tiket }}
                                        </strong>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: var(--slate-900); font-size: 0.85rem;">{{ $item->nama_mahasiswa }}</div>
                                        <div style="font-size: 0.75rem; color: var(--slate-500);">NPM: {{ $item->npm }}</div>
                                    </td>
                                    <td>
                                        <span style="font-size: 0.85rem;">{{ $item->layanan->nama }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $item->status_badge_class }}" style="font-size: 0.7rem;">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('btn-ping-sistem-a').addEventListener('click', function() {
    this.disabled = true;
    this.innerHTML = '<span class="spinner"></span> Menguji...';

    fetch('{{ route("admin.sistem_a.check") }}')
        .then(response => response.json())
        .then(res => {
            alert(res.online ? '✓ ' + res.message : '✕ ' + res.message);
            location.reload();
        })
        .catch(err => {
            alert('✕ Terjadi kesalahan saat menghubungi API Sistem A.');
            location.reload();
        });
});
</script>
@endpush
