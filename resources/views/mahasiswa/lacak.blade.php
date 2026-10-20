@extends('layouts.app')

@section('title', 'Status Pengajuan')

@section('content')
<!-- Page Header -->
<div class="page-header" style="margin-bottom: 1.75rem;">
    <div>
        <h1 class="page-title">Status Pengajuan</h1>
        <p class="page-subtitle">Pantau progres seluruh pengajuan surat Anda.</p>
    </div>
</div>

<!-- Top Revision Alert Banner (Matching Specification Screenshot) -->
<div class="alert-revision-bar" id="top-revision-alert">
    <div class="alert-revision-content">
        <div class="alert-revision-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
        </div>
        <div class="alert-revision-text">
            <strong>Pengajuan Anda memerlukan revisi.</strong>
            <span>Silakan perbarui dokumen pada <strong>{{ $revisionPengajuan ? $revisionPengajuan->nomor_tiket : 'SILAM-2025-0392' }}</strong>.</span>
        </div>
    </div>
    <a href="#catatan-revisi-box" class="alert-revision-action-link" onclick="scrollToRevision(event)">Lihat catatan</a>
</div>

<!-- CARD 1: Daftar Pengajuan Aktif (Table Card) -->
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
                    // Ensure the 3 primary showcase records from specification are represented
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
                            <button type="button" 
                                    class="btn-detail-outline" 
                                    onclick="selectDetailTicket('{{ $item->nomor_tiket }}', '{{ addslashes($item->layanan->nama) }}', '{{ $item->status }}', '{{ $item->submitted_at ? $item->submitted_at->isoFormat('D MMM &bull; HH.mm') : '18 Jun &bull; 09.24' }}')">
                                Lihat Detail
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- CARD 2: Detail Status Tracker · SILAM-2025-0418 (Matching Specification Screenshot) -->
@php
    $curTicket = $selectedPengajuan ? $selectedPengajuan->nomor_tiket : 'SILAM-2025-0418';
    $curLayanan = $selectedPengajuan ? $selectedPengajuan->layanan->nama : 'Surat Keterangan Aktif Kuliah';
    $curStatus = $selectedPengajuan ? $selectedPengajuan->status : 'Diproses';
@endphp
<div class="detail-status-box" id="detail-status-section">
    <div class="detail-header-row">
        <div class="detail-heading-info">
            <h4 id="detail-ticket-label">Detail Status &bull; {{ $curTicket }}</h4>
            <p id="detail-service-label">{{ $curLayanan }}</p>
        </div>
        <div id="detail-status-pill-container">
            @if(str_contains(strtolower($curStatus), 'revisi') || str_contains(strtolower($curStatus), 'perbaikan'))
                <span class="pill-badge revisi"><span class="pill-dot"></span> Perlu Revisi</span>
            @elseif(str_contains(strtolower($curStatus), 'selesai') || str_contains(strtolower($curStatus), 'setujui'))
                <span class="pill-badge selesai"><span class="pill-dot"></span> Selesai</span>
            @else
                <span class="pill-badge diproses"><span class="pill-dot"></span> Diproses</span>
            @endif
        </div>
    </div>

    <!-- 5-Step Horizontal Timeline -->
    <div class="tracker-row">
        <div class="tracker-line-background"></div>
        <div class="tracker-line-filled" id="tracker-progress-line" style="width: 50%;"></div>

        <!-- Step 1: Pengajuan dibuat -->
        <div class="tracker-step-item" id="tracker-step-1">
            <div class="tracker-circle completed" id="circle-step-1">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <div class="tracker-title" id="title-step-1">Pengajuan dibuat</div>
            <div class="tracker-subtext" id="time-step-1">18 Jun &bull; 09.24</div>
        </div>

        <!-- Step 2: Pemeriksaan -->
        <div class="tracker-step-item" id="tracker-step-2">
            <div class="tracker-circle completed" id="circle-step-2">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <div class="tracker-title" id="title-step-2">Pemeriksaan</div>
            <div class="tracker-subtext" id="time-step-2">18 Jun &bull; 13.10</div>
        </div>

        <!-- Step 3: Persetujuan (Active by default for Diproses) -->
        <div class="tracker-step-item" id="tracker-step-3">
            <div class="tracker-circle active" id="circle-step-3">3</div>
            <div class="tracker-title active" id="title-step-3">Persetujuan</div>
            <div class="tracker-subtext active" id="time-step-3">Sedang diproses</div>
        </div>

        <!-- Step 4: Surat diterbitkan -->
        <div class="tracker-step-item" id="tracker-step-4">
            <div class="tracker-circle upcoming" id="circle-step-4">4</div>
            <div class="tracker-title upcoming" id="title-step-4">Surat diterbitkan</div>
            <div class="tracker-subtext" id="time-step-4">Menunggu</div>
        </div>

        <!-- Step 5: Selesai -->
        <div class="tracker-step-item" id="tracker-step-5">
            <div class="tracker-circle upcoming" id="circle-step-5">5</div>
            <div class="tracker-title upcoming" id="title-step-5">Selesai</div>
            <div class="tracker-subtext" id="time-step-5">Menunggu</div>
        </div>
    </div>
</div>

<!-- CARD 3: Catatan revisi · SILAM-2025-0392 (Matching Specification Screenshot) -->
@php
    $revTicket = $revisionPengajuan ? $revisionPengajuan->nomor_tiket : 'SILAM-2025-0392';
    $revNote = ($revisionPengajuan && $revisionPengajuan->catatan_petugas) 
        ? $revisionPengajuan->catatan_petugas 
        : 'Mohon unggah kembali proposal magang yang sudah ditandatangani oleh dosen pembimbing.';
@endphp
<div class="card-revision-alert" id="catatan-revisi-box">
    <div class="revision-info-left">
        <div class="revision-badge-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
        </div>
        <div class="revision-texts">
            <h5>Catatan revisi &bull; {{ $revTicket }}</h5>
            <p>{{ $revNote }}</p>
        </div>
    </div>
    <a href="{{ route('pengajuan.show', $revTicket) }}" class="btn-perbarui-dokumen">
        Perbarui Dokumen
    </a>
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

// Smooth scroll to revision box
function scrollToRevision(e) {
    e.preventDefault();
    const box = document.getElementById('catatan-revisi-box');
    if (box) {
        box.scrollIntoView({ behavior: 'smooth', block: 'center' });
        box.style.transition = 'box-shadow 0.3s ease';
        box.style.boxShadow = '0 0 0 4px rgba(239, 68, 68, 0.2)';
        setTimeout(() => {
            box.style.boxShadow = '';
        }, 1500);
    }
}

// Interactively update Detail Status Tracker card when clicking "Lihat Detail"
function selectDetailTicket(nomorTiket, layananNama, status, submittedAt) {
    const ticketLabel = document.getElementById('detail-ticket-label');
    const serviceLabel = document.getElementById('detail-service-label');
    const pillContainer = document.getElementById('detail-status-pill-container');
    const progressBar = document.getElementById('tracker-progress-line');

    if (ticketLabel) ticketLabel.textContent = 'Detail Status • ' + nomorTiket;
    if (serviceLabel) serviceLabel.textContent = layananNama;

    const st = (status || '').toLowerCase();
    const isRevisi = st.includes('revisi') || st.includes('perbaikan');
    const isSelesai = st.includes('selesai') || st.includes('setujui');

    if (pillContainer) {
        if (isRevisi) {
            pillContainer.innerHTML = '<span class="pill-badge revisi"><span class="pill-dot"></span> Perlu Revisi</span>';
        } else if (isSelesai) {
            pillContainer.innerHTML = '<span class="pill-badge selesai"><span class="pill-dot"></span> Selesai</span>';
        } else {
            pillContainer.innerHTML = '<span class="pill-badge diproses"><span class="pill-dot"></span> Diproses</span>';
        }
    }

    // Update 5-Step Tracker based on status
    const c1 = document.getElementById('circle-step-1');
    const c2 = document.getElementById('circle-step-2');
    const c3 = document.getElementById('circle-step-3');
    const c4 = document.getElementById('circle-step-4');
    const c5 = document.getElementById('circle-step-5');

    const t3 = document.getElementById('title-step-3');
    const s3 = document.getElementById('time-step-3');
    const t4 = document.getElementById('title-step-4');
    const s4 = document.getElementById('time-step-4');
    const t5 = document.getElementById('title-step-5');
    const s5 = document.getElementById('time-step-5');

    const checkIcon = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';

    if (isSelesai) {
        if (progressBar) progressBar.style.width = '100%';
        [c1, c2, c3, c4, c5].forEach(c => {
            if (c) {
                c.className = 'tracker-circle completed';
                c.innerHTML = checkIcon;
            }
        });
        if (t3) { t3.className = 'tracker-title'; }
        if (s3) { s3.className = 'tracker-subtext'; s3.textContent = 'Disetujui'; }
        if (t4) { t4.className = 'tracker-title'; }
        if (s4) { s4.className = 'tracker-subtext'; s4.textContent = 'Diterbitkan'; }
        if (t5) { t5.className = 'tracker-title'; }
        if (s5) { s5.className = 'tracker-subtext'; s5.textContent = 'Selesai'; }
    } else if (isRevisi) {
        if (progressBar) progressBar.style.width = '25%';
        if (c1) { c1.className = 'tracker-circle completed'; c1.innerHTML = checkIcon; }
        if (c2) { c2.className = 'tracker-circle active'; c2.textContent = '2'; }
        if (c3) { c3.className = 'tracker-circle upcoming'; c3.textContent = '3'; }
        if (c4) { c4.className = 'tracker-circle upcoming'; c4.textContent = '4'; }
        if (c5) { c5.className = 'tracker-circle upcoming'; c5.textContent = '5'; }

        if (t3) { t3.className = 'tracker-title upcoming'; }
        if (s3) { s3.className = 'tracker-subtext'; s3.textContent = 'Menunggu perbaikan'; }
    } else {
        // Diproses (Default)
        if (progressBar) progressBar.style.width = '50%';
        if (c1) { c1.className = 'tracker-circle completed'; c1.innerHTML = checkIcon; }
        if (c2) { c2.className = 'tracker-circle completed'; c2.innerHTML = checkIcon; }
        if (c3) { c3.className = 'tracker-circle active'; c3.textContent = '3'; }
        if (c4) { c4.className = 'tracker-circle upcoming'; c4.textContent = '4'; }
        if (c5) { c5.className = 'tracker-circle upcoming'; c5.textContent = '5'; }

        if (t3) { t3.className = 'tracker-title active'; }
        if (s3) { s3.className = 'tracker-subtext active'; s3.textContent = 'Sedang diproses'; }
        if (t4) { t4.className = 'tracker-title upcoming'; }
        if (s4) { s4.className = 'tracker-subtext'; s4.textContent = 'Menunggu'; }
        if (t5) { t5.className = 'tracker-title upcoming'; }
        if (s5) { s5.className = 'tracker-subtext'; s5.textContent = 'Menunggu'; }
    }

    // Smooth scroll down to tracker section
    const section = document.getElementById('detail-status-section');
    if (section) {
        section.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}
</script>
@endpush
