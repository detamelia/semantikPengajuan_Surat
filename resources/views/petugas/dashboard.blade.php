@extends('layouts.app')

@section('title', 'Dashboard Petugas - SILAM FT')

@section('content')
<div style="width: 100%;">
    <!-- Page Header -->
    <div style="margin-bottom: 1.5rem;">
        <h2 style="font-size: 1.45rem; font-weight: 800; color: #0f172a; letter-spacing: -0.02em; margin-bottom: 0.25rem;">
            Dashboard Petugas
        </h2>
        <p style="font-size: 0.825rem; color: #64748b;">
            Selamat datang, Siti. Kelola pengajuan mahasiswa hari ini.
        </p>
    </div>

    <!-- 4-Stat Cards Row -->
    <div class="petugas-stats-grid">
        <!-- Stat 1: Pengajuan Masuk -->
        <div class="petugas-stat-card">
            <div class="stat-icon-box blue">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
            </div>
            <div class="stat-content">
                <span class="stat-label-text">Pengajuan Masuk</span>
                <span class="stat-value-text">{{ $stats['masuk'] ?? 24 }}</span>
            </div>
        </div>

        <!-- Stat 2: Perlu Pemeriksaan -->
        <div class="petugas-stat-card">
            <div class="stat-icon-box amber">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    <polyline points="11 8 11 11 13 13"></polyline>
                </svg>
            </div>
            <div class="stat-content">
                <span class="stat-label-text">Perlu Pemeriksaan</span>
                <span class="stat-value-text">{{ $stats['perlu_pemeriksaan'] ?? 12 }}</span>
            </div>
        </div>

        <!-- Stat 3: Perlu Revisi -->
        <div class="petugas-stat-card">
            <div class="stat-icon-box red">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            </div>
            <div class="stat-content">
                <span class="stat-label-text">Perlu Revisi</span>
                <span class="stat-value-text">{{ $stats['perlu_revisi'] ?? 5 }}</span>
            </div>
        </div>

        <!-- Stat 4: Selesai -->
        <div class="petugas-stat-card">
            <div class="stat-icon-box green">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            <div class="stat-content">
                <span class="stat-label-text">Selesai</span>
                <span class="stat-value-text">{{ $stats['selesai'] ?? 48 }}</span>
            </div>
        </div>
    </div>

    <!-- Main Card: Daftar Pengajuan Terbaru -->
    <div class="petugas-main-card">
        <div class="petugas-card-header">
            <div>
                <h3 class="petugas-card-title">Daftar Pengajuan Terbaru</h3>
                <p class="petugas-card-sub">Pengajuan yang memerlukan tindak lanjut.</p>
            </div>

            <!-- Search input pill with interactive live filter -->
            <div class="search-input-pill-wrap">
                <svg class="search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="petugasSearchInput" class="search-input-pill" placeholder="Cari pengajuan..." oninput="filterPetugasTable(this.value)">
            </div>
        </div>

        <!-- Table -->
        <div style="overflow-x: auto;">
            <table class="petugas-table" id="petugasTable">
                <thead>
                    <tr>
                        <th>NOMOR PENGAJUAN</th>
                        <th>NAMA MAHASISWA</th>
                        <th>JENIS SURAT</th>
                        <th>TANGGAL PENGAJUAN</th>
                        <th>STATUS</th>
                        <th style="text-align: right; padding-right: 1.25rem;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengajuans as $pengajuan)
                        @php
                            $st = strtolower($pengajuan->status);
                            if (str_contains($st, 'menunggu')) {
                                $pillClass = 'menunggu';
                                $statusLabel = 'Menunggu Pemeriksaan';
                            } elseif (str_contains($st, 'proses')) {
                                $pillClass = 'diproses';
                                $statusLabel = 'Diproses';
                            } elseif (str_contains($st, 'revisi') || str_contains($st, 'perbaikan')) {
                                $pillClass = 'revisi';
                                $statusLabel = 'Perlu Revisi';
                            } elseif (str_contains($st, 'selesai') || str_contains($st, 'setuju')) {
                                $pillClass = 'selesai';
                                $statusLabel = 'Selesai';
                            } else {
                                $pillClass = 'menunggu';
                                $statusLabel = $pengajuan->status;
                            }
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('petugas.show', $pengajuan->nomor_tiket) }}" class="col-ticket-link">
                                    {{ $pengajuan->nomor_tiket }}
                                </a>
                            </td>
                            <td>
                                <div class="mhs-cell-name">{{ $pengajuan->nama_mahasiswa }}</div>
                                <div class="mhs-cell-prodi">{{ $pengajuan->prodi ?: 'Teknik Elektro' }}</div>
                            </td>
                            <td style="color: #334155;">
                                {{ $pengajuan->layanan->nama ?? 'Surat Keterangan Aktif Kuliah' }}
                            </td>
                            <td style="color: #64748b; white-space: nowrap;">
                                {{ $pengajuan->submitted_at ? $pengajuan->submitted_at->isoFormat('D MMM Y') : '19 Jun 2025' }}
                            </td>
                            <td>
                                <span class="pill-badge {{ $pillClass }}">
                                    <span class="pill-dot"></span>
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td style="text-align: right; padding-right: 1.25rem;">
                                <a href="{{ route('petugas.show', $pengajuan->nomor_tiket) }}" class="btn-periksa-clean">
                                    Periksa
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem; color: #94a3b8;">
                                Belum ada pengajuan masuk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>



<script>
function filterPetugasTable(query) {
    const q = query.toLowerCase().trim();
    const rows = document.querySelectorAll('#petugasTable tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(q) ? '' : 'none';
    });
}
</script>
@endsection
