@extends('layouts.app')

@section('title', 'Detail Pengajuan ' . $pengajuan->nomor_tiket)

@section('content')
<!-- Page Header -->
<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.35rem;">
            <a href="{{ route('mahasiswa.dashboard') }}" style="color: var(--text-muted); text-decoration: none;">Dashboard</a>
            <span style="margin: 0 0.3rem;">&gt;</span>
            <span style="color: var(--brand-blue); font-weight: 600;">Detail Pengajuan</span>
        </div>
        <h1 class="page-title">{{ $pengajuan->nomor_tiket }}</h1>
        <p class="page-subtitle">{{ $pengajuan->layanan->nama }} &bull; Diajukan pada {{ $pengajuan->submitted_at ? $pengajuan->submitted_at->isoFormat('D MMMM Y, HH:mm') : '-' }}</p>
    </div>

    <div style="display: flex; gap: 0.75rem; align-items: center;">
        <a href="{{ route('api.rdf.pengajuan', $pengajuan->nomor_tiket) }}" target="_blank" class="btn-white-outline">
            <span>🌐</span> Ekspor RDF Turtle
        </a>
        @if(in_array($pengajuan->status, [\App\Models\Pengajuan::STATUS_SELESAI, \App\Models\Pengajuan::STATUS_DISETUJUI]))
            <a href="{{ route('pengajuan.unduh', $pengajuan->nomor_tiket) }}" target="_blank" class="btn-brand" style="background: #059669;">
                <span>📥</span> Unduh Surat Resmi
            </a>
        @endif
    </div>
</div>

<div class="service-detail-grid">
    <!-- LEFT MAIN COLUMN -->
    <div>
        <!-- Status Banner -->
        <div class="numbered-section" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <span style="font-size: 0.75rem; color: var(--text-light); text-transform: uppercase; font-weight: 700;">Status Verifikasi Saat Ini</span>
                <div style="margin-top: 0.35rem;">
                    @if($pengajuan->status === \App\Models\Pengajuan::STATUS_DIPROSES)
                        <span class="status-pill blue" style="font-size: 0.95rem; padding: 0.4rem 1rem;"><span class="dot"></span> Diproses</span>
                    @elseif($pengajuan->status === \App\Models\Pengajuan::STATUS_PERLU_PERBAIKAN)
                        <span class="status-pill red" style="font-size: 0.95rem; padding: 0.4rem 1rem;"><span class="dot"></span> Perlu Revisi</span>
                    @elseif(in_array($pengajuan->status, [\App\Models\Pengajuan::STATUS_SELESAI, \App\Models\Pengajuan::STATUS_DISETUJUI]))
                        <span class="status-pill green" style="font-size: 0.95rem; padding: 0.4rem 1rem;"><span class="dot"></span> Selesai</span>
                    @else
                        <span class="status-pill amber" style="font-size: 0.95rem; padding: 0.4rem 1rem;"><span class="dot"></span> Menunggu Verifikasi</span>
                    @endif
                </div>
            </div>

            @if($pengajuan->catatan_petugas)
                <div style="max-width: 450px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 0.75rem 1rem; font-size: 0.85rem; color: #92400e;">
                    <strong>Catatan Petugas:</strong> {{ $pengajuan->catatan_petugas }}
                </div>
            @endif
        </div>

        <!-- Form Revisi jika Perlu Perbaikan -->
        @if($pengajuan->status === \App\Models\Pengajuan::STATUS_PERLU_PERBAIKAN)
            <div class="numbered-section" style="border: 2px dashed #f87171; background: #fff1f2;">
                <div class="numbered-header">
                    <span class="badge-num" style="background: #ffe4e6; color: #e11d48;">!</span>
                    <h3 style="color: #be123c;">Unggah Dokumen Perbaikan (Revisi)</h3>
                </div>
                <p style="font-size: 0.85rem; color: #9f1239; margin-bottom: 1.25rem;">
                    Petugas meminta Anda memperbaiki berkas permohonan. Pilih berkas dan unggah dokumen revisi terbaru.
                </p>

                <form action="{{ route('pengajuan.revisi', $pengajuan->nomor_tiket) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label for="dokumen_id" class="form-label">Pilih Dokumen yang Direvisi <span style="color: #ef4444;">*</span></label>
                            <select name="dokumen_id" id="dokumen_id" class="form-control" required style="border-radius: var(--radius-md);">
                                @foreach($pengajuan->dokumens as $dok)
                                    <option value="{{ $dok->id }}">
                                        {{ $dok->persyaratan->nama_persyaratan ?? $dok->nama_file_asli }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="file_revisi" class="form-label">Berkas Pengganti Baru <span style="color: #ef4444;">*</span></label>
                            <input type="file" name="file_revisi" id="file_revisi" class="form-control" required style="border-radius: var(--radius-md);">
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label for="catatan_mahasiswa" class="form-label">Catatan Tambahan Mahasiswa (Opsional)</label>
                        <input type="text" name="catatan_mahasiswa" id="catatan_mahasiswa" class="form-control" placeholder="Contoh: Berkas sudah diperbarui sesuai arahan." style="border-radius: var(--radius-md);">
                    </div>

                    <button type="submit" class="btn-brand">
                        Kirim Berkas Perbaikan
                    </button>
                </form>
            </div>
        @endif

        <!-- Daftar Dokumen yang Diunggah -->
        <div class="numbered-section">
            <div class="numbered-header">
                <span class="badge-num">📁</span>
                <h3>Berkas Persyaratan yang Diunggah</h3>
            </div>

            <div>
                @forelse($pengajuan->dokumens as $dok)
                    <div class="uploaded-file-row">
                        <div class="file-info-group">
                            <span class="file-badge-type">{{ strtoupper(pathinfo($dok->nama_file_asli, PATHINFO_EXTENSION)) ?: 'PDF' }}</span>
                            <div class="file-names">
                                <strong>{{ $dok->persyaratan->nama_persyaratan ?? $dok->nama_file_asli }}</strong>
                                <span>{{ $dok->nama_file_asli }} &bull; {{ $dok->file_size_kb }} KB</span>
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            @if($dok->status_verifikasi === 'Valid')
                                <span class="status-pill green"><span class="dot"></span> Valid</span>
                            @elseif($dok->status_verifikasi === 'Perlu Perbaikan')
                                <span class="status-pill red"><span class="dot"></span> Perlu Revisi</span>
                            @else
                                <span class="status-pill amber"><span class="dot"></span> Terunggah</span>
                            @endif

                            <a href="{{ asset('storage/' . $dok->file_path) }}" target="_blank" class="btn-white-outline" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">
                                Buka File
                            </a>
                        </div>
                    </div>
                @empty
                    <p style="color: var(--text-muted); font-size: 0.85rem;">Tidak ada dokumen yang dilampirkan.</p>
                @endforelse
            </div>
        </div>

        <!-- Timeline Riwayat Status -->
        <div class="numbered-section">
            <div class="numbered-header">
                <span class="badge-num">🕒</span>
                <h3>Riwayat Status Pengajuan</h3>
            </div>

            <div class="timeline">
                @foreach($pengajuan->riwayats as $r)
                    <div class="timeline-item">
                        <div class="timeline-dot" style="border-color: var(--brand-blue);"></div>
                        <div class="timeline-content">
                            <h4>{{ $r->status_baru }}</h4>
                            <time>{{ $r->created_at->isoFormat('D MMMM Y, HH:mm') }} &bull; Oleh {{ $r->actor_name ?: 'Sistem' }}</time>
                            <p>{{ $r->catatan ?: '-' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- RIGHT SUMMARY COLUMN -->
    <div>
        <div class="side-summary-box">
            <h3>Informasi Pemohon</h3>

            <div class="summary-spec-item">
                <span>Nama Mahasiswa</span>
                <strong>{{ $pengajuan->nama_mahasiswa }}</strong>
            </div>

            <div class="summary-spec-item">
                <span>NPM</span>
                <strong>{{ $pengajuan->npm }}</strong>
            </div>

            <div class="summary-spec-item">
                <span>Program Studi</span>
                <strong>{{ $pengajuan->prodi }} ({{ $pengajuan->angkatan }})</strong>
            </div>

            <div class="summary-spec-item">
                <span>Email &amp; HP</span>
                <strong style="font-size: 0.85rem;">{{ $pengajuan->email }}<br>{{ $pengajuan->no_hp }}</strong>
            </div>

            <div class="summary-spec-item" style="margin-bottom: 0;">
                <span>URI Semantik (Sistem A)</span>
                <a href="{{ $pengajuan->getMahasiswaSemanticUri() }}" target="_blank" style="font-family: var(--font-mono); font-size: 0.72rem; color: var(--brand-blue); word-break: break-all;">
                    {{ $pengajuan->getMahasiswaSemanticUri() }}
                </a>
            </div>
        </div>

        <div class="side-summary-box">
            <h3>Keperluan Permohonan</h3>
            <p style="font-size: 0.9rem; color: var(--text-secondary); line-height: 1.6;">
                "{{ $pengajuan->keperluan }}"
            </p>
        </div>
    </div>
</div>
@endsection
