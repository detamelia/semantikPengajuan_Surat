@extends('layouts.app')

@section('title', 'Riwayat Pengajuan')

@section('content')
<!-- Page Header -->
<div class="page-header" style="margin-bottom: 1.75rem;">
    <div>
        <h1 class="page-title">Riwayat Pengajuan</h1>
        <p class="page-subtitle">Pantau progres seluruh pengajuan surat Anda.</p>
    </div>
</div>

<!-- Daftar Pengajuan Aktif (Table Card) -->
<div class="status-card-box">
    <div class="status-card-header">
        <div class="status-card-title">Daftar Pengajuan Aktif</div>
        <div class="status-search-wrap">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" id="search-pengajuan" class="status-search-input" placeholder="Cari pengajuan..." oninput="filterPengajuan(this.value)">
        </div>
    </div>

    <div class="table-wrap">
        <table class="status-table-clean" id="pengajuan-table">
            <thead>
                <tr>
                    <th style="width: 25%;">NOMOR PENGAJUAN</th>
                    <th style="width: 32%;">JENIS SURAT</th>
                    <th style="width: 18%;">TANGGAL</th>
                    <th style="width: 15%;">STATUS</th>
                    <th style="width: 10%; text-align: right;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $displayList = $pengajuans->isNotEmpty() ? $pengajuans : collect([
                        (object)[
                            'nomor_tiket' => 'SILAM-2025-0418',
                            'layanan' => (object)['nama' => 'Surat Keterangan Aktif Kuliah'],
                            'submitted_at' => \Carbon\Carbon::parse('2025-06-18 09:24:00'),
                            'status' => 'Diproses',
                            'catatan_petugas' => null,
                        ],
                        (object)[
                            'nomor_tiket' => 'SILAM-2025-0392',
                            'layanan' => (object)['nama' => 'Surat Rekomendasi Magang'],
                            'submitted_at' => \Carbon\Carbon::parse('2025-06-12 10:15:00'),
                            'status' => 'Perlu Revisi',
                            'catatan_petugas' => 'Mohon unggah kembali proposal magang yang sudah ditandatangani oleh dosen pembimbing.',
                        ],
                        (object)[
                            'nomor_tiket' => 'SILAM-2025-0351',
                            'layanan' => (object)['nama' => 'Surat Pengantar Penelitian'],
                            'submitted_at' => \Carbon\Carbon::parse('2025-06-04 08:30:00'),
                            'status' => 'Selesai',
                            'catatan_petugas' => null,
                        ],
                    ]);
                @endphp

                @foreach($displayList as $item)
                    @php
                        $st = strtolower($item->status);
                        $isDiproses = str_contains($st, 'diproses') || str_contains($st, 'proses');
                        $isRevisi = str_contains($st, 'revisi') || str_contains($st, 'perbaikan');
                        $isSelesai = str_contains($st, 'selesai') || str_contains($st, 'setujui');
                    @endphp
                    <tr class="pengajuan-row" data-nomor="{{ $item->nomor_tiket }}" data-layanan="{{ $item->layanan->nama }}">
                        <td>
                            <a href="{{ route('pengajuan.show', $item->nomor_tiket) }}" class="ticket-link-bold">
                                {{ $item->nomor_tiket }}
                            </a>
                        </td>
                        <td>{{ $item->layanan->nama }}</td>
                        <td>{{ $item->submitted_at ? $item->submitted_at->isoFormat('D MMMM Y') : '18 Juni 2025' }}</td>
                        <td>
                            @if($isRevisi)
                                <span class="pill-badge revisi"><span class="pill-dot"></span> Perlu Revisi</span>
                            @elseif($isSelesai)
                                <span class="pill-badge selesai"><span class="pill-dot"></span> Selesai</span>
                            @else
                                <span class="pill-badge diproses"><span class="pill-dot"></span> Diproses</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('pengajuan.show', $item->nomor_tiket) }}" class="btn-detail-outline">
                                Lihat Detail
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Filter pengajuan table rows via search input
function filterPengajuan(query) {
    const q = query.toLowerCase().trim();
    const rows = document.querySelectorAll('#pengajuan-table tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(q) ? '' : 'none';
    });
}
</script>
@endpush
