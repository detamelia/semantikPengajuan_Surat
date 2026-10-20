@extends('layouts.app')

@section('title', $layanan->nama)

@section('content')
<!-- Breadcrumbs -->
<div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem;">
    <a href="{{ route('layanan.index') }}" style="color: var(--text-muted); text-decoration: none;">Informasi Layanan</a>
    <span style="margin: 0 0.4rem;">&gt;</span>
    <span style="color: var(--brand-blue); font-weight: 600;">{{ $layanan->nama }}</span>
</div>

<!-- Header -->
<div class="page-header" style="margin-bottom: 2rem;">
    <div>
        <h1 class="page-title">{{ $layanan->nama }}</h1>
        <p class="page-subtitle">Informasi lengkap layanan dan dokumen yang perlu disiapkan.</p>
    </div>
    <a href="{{ route('pengajuan.create', $layanan->kode) }}" class="btn-brand">
        Ajukan Layanan
    </a>
</div>

<!-- Two-Column Layout -->
<div class="service-detail-grid">
    <!-- LEFT MAIN COLUMN -->
    <div>
        <!-- 01: Deskripsi Layanan -->
        <div class="numbered-section">
            <div class="numbered-header">
                <span class="badge-num">01</span>
                <h3>Deskripsi Layanan</h3>
            </div>
            <p style="font-size: 0.925rem; color: var(--text-secondary); line-height: 1.7;">
                {{ $layanan->deskripsi }}
            </p>
        </div>

        <!-- 02: Persyaratan Dokumen -->
        <div class="numbered-section">
            <div class="numbered-header">
                <span class="badge-num">02</span>
                <h3>Persyaratan Dokumen</h3>
            </div>
            <div>
                @forelse($layanan->persyaratans as $syarat)
                    <div class="req-box-item">
                        <span class="req-check">✓</span>
                        <div class="req-texts">
                            <strong>{{ $syarat->nama_persyaratan }}</strong>
                            <span>Format {{ strtoupper($syarat->tipe_file) }}, maksimal {{ round($syarat->max_size_kb / 1024) }} MB</span>
                        </div>
                    </div>
                @empty
                    <div class="req-box-item">
                        <span class="req-check">✓</span>
                        <div class="req-texts">
                            <strong>Kartu Tanda Mahasiswa (KTM)</strong>
                            <span>Format PDF/JPG, maksimal 2 MB</span>
                        </div>
                    </div>
                    <div class="req-box-item">
                        <span class="req-check">✓</span>
                        <div class="req-texts">
                            <strong>Bukti pembayaran UKT semester berjalan</strong>
                            <span>Format PDF, maksimal 2 MB</span>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 03: Alur Layanan -->
        <div class="numbered-section">
            <div class="numbered-header">
                <span class="badge-num">03</span>
                <h3>Alur Layanan</h3>
            </div>
            <div class="flow-steps">
                <div class="flow-step-item">
                    <span class="step-circle">1</span>
                    <span>Ajukan layanan</span>
                </div>
                <span class="flow-arrow">&gt;</span>

                <div class="flow-step-item">
                    <span class="step-circle">2</span>
                    <span>Pemeriksaan</span>
                </div>
                <span class="flow-arrow">&gt;</span>

                <div class="flow-step-item">
                    <span class="step-circle">3</span>
                    <span>Persetujuan</span>
                </div>
                <span class="flow-arrow">&gt;</span>

                <div class="flow-step-item">
                    <span class="step-circle">4</span>
                    <span>Surat diterbitkan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT SUMMARY COLUMN -->
    <div>
        <!-- Ringkasan Layanan -->
        <div class="side-summary-box">
            <h3>Ringkasan Layanan</h3>

            <div class="summary-spec-item">
                <span>Estimasi proses</span>
                <strong>{{ $layanan->estimasi_hari }}&ndash;{{ $layanan->estimasi_hari + 1 }} hari kerja</strong>
            </div>

            <div class="summary-spec-item">
                <span>Biaya layanan</span>
                <strong style="color: #059669;">Gratis</strong>
            </div>

            <div class="summary-spec-item" style="margin-bottom: 0;">
                <span>Format hasil</span>
                <strong>PDF dengan TTE</strong>
            </div>
        </div>

        <!-- Template Dokumen -->
        <div class="side-summary-box">
            <div style="display: flex; gap: 0.85rem; align-items: flex-start; margin-bottom: 1.25rem;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #eef2ff; color: var(--brand-blue); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                </div>
                <div>
                    <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary);">Template Dokumen</h4>
                    <p style="font-size: 0.775rem; color: var(--text-muted); margin-top: 0.15rem;">Formulir pendukung layanan</p>
                </div>
            </div>

            <a href="#" class="btn-white-outline" style="width: 100%; justify-content: center;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                Unduh
            </a>
        </div>
    </div>
</div>
@endsection
