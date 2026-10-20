@extends('layouts.app')

@section('title', 'Daftar Pengajuan - SILAM FT')

@section('content')
<div style="width: 100%;">
    <!-- Page Header -->
    <div style="margin-bottom: 1.5rem;">
        <h2 style="font-size: 1.45rem; font-weight: 800; color: #0f172a; letter-spacing: -0.02em; margin-bottom: 0.25rem;">
            Daftar Pengajuan
        </h2>
        <p style="font-size: 0.825rem; color: #64748b;">
            Periksa dan tindak lanjuti pengajuan mahasiswa.
        </p>
    </div>

    <!-- Main Card: Daftar Pengajuan -->
    <div class="petugas-main-card">
        <div class="petugas-card-header">
            <div>
                <h3 class="petugas-card-title">Daftar Pengajuan</h3>
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
