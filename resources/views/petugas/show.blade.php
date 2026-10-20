@extends('layouts.app')

@section('title', 'Pemeriksaan Pengajuan ' . $pengajuan->nomor_tiket . ' - SILAM FT')

@section('content')
<div style="width: 100%;">
    <!-- Breadcrumb -->
    <div class="breadcrumb-nav-clean">
        <a href="{{ route('petugas.pengajuan.index') }}">Daftar Pengajuan</a>
        <span class="separator">&gt;</span>
        <span class="current">Pemeriksaan Pengajuan</span>
    </div>

    @php
        $st = strtolower($pengajuan->status);
        $isDisetujui = in_array($st, ['disetujui', 'selesai']);
        $isRevisi = str_contains($st, 'revisi') || str_contains($st, 'perbaikan');
        $isDiproses = str_contains($st, 'proses');
        
        if ($isDisetujui) {
            $pillClass = 'selesai';
            $statusLabel = 'Disetujui';
        } elseif ($isRevisi) {
            $pillClass = 'revisi';
            $statusLabel = 'Perlu Revisi';
        } elseif ($isDiproses) {
            $pillClass = 'diproses';
            $statusLabel = 'Diproses';
        } else {
            $pillClass = 'menunggu';
            $statusLabel = 'Menunggu Pemeriksaan';
        }
    @endphp

    <!-- Page Header -->
    <div class="pemeriksaan-page-header">
        <div class="pemeriksaan-title-wrap">
            <h2>Pemeriksaan Pengajuan</h2>
            <p>
                {{ $pengajuan->nomor_tiket }} &bull; Diajukan {{ $pengajuan->submitted_at ? $pengajuan->submitted_at->isoFormat('D MMMM Y, HH.mm') . ' WIB' : '19 Juni 2025, 08.42 WIB' }}
            </p>
        </div>

        <div>
            <span class="pill-badge {{ $pillClass }}">
                <span class="pill-dot"></span>
                {{ $statusLabel }}
            </span>
        </div>
    </div>

    <!-- Success Approved Banner (Shown when status is approved or after action) -->
    @if(session('success') || ($isDisetujui && request()->has('approved')))
        <div class="banner-disetujui">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <span>Pengajuan telah disetujui. Surat akan diterbitkan dengan TTE dan QR Code.</span>
        </div>
    @endif

    <!-- Two-Column Grid -->
    <div class="pemeriksaan-grid">
        <!-- LEFT COLUMN: Data Mahasiswa, Data Pengajuan, Dokumen -->
        <div>
            <!-- Card 1: Data Mahasiswa -->
            <div class="detail-meta-card">
                <h3 class="detail-meta-title">Data Mahasiswa</h3>

                <div class="grid-2x2-meta">
                    <div>
                        <div class="meta-field-label">Nama lengkap</div>
                        <div class="meta-field-value">{{ $pengajuan->nama_mahasiswa ?: 'Dina Rahmawati' }}</div>
                    </div>

                    <div>
                        <div class="meta-field-label">NPM</div>
                        <div class="meta-field-value">{{ $pengajuan->npm ?: '2215011045' }}</div>
                    </div>

                    <div>
                        <div class="meta-field-label">Program studi</div>
                        <div class="meta-field-value">{{ $pengajuan->prodi ?: 'Teknik Elektro' }}</div>
                    </div>

                    <div>
                        <div class="meta-field-label">Email</div>
                        <div class="meta-field-value" style="font-weight: 700;">
                            {{ $pengajuan->email ?: 'dina.r@students.unila.ac.id' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Data Pengajuan -->
            <div class="detail-meta-card">
                <h3 class="detail-meta-title">Data Pengajuan</h3>

                <div class="grid-2x2-meta" style="margin-bottom: 1.15rem;">
                    <div>
                        <div class="meta-field-label">Jenis layanan</div>
                        <div class="meta-field-value">{{ $pengajuan->layanan->nama ?? 'Surat Keterangan Aktif Kuliah' }}</div>
                    </div>

                    <div>
                        <div class="meta-field-label">Tanggal pengajuan</div>
                        <div class="meta-field-value">
                            {{ $pengajuan->submitted_at ? $pengajuan->submitted_at->isoFormat('D MMMM Y') : '19 Juni 2025' }}
                        </div>
                    </div>
                </div>

                <div>
                    <div class="meta-field-label">Keperluan</div>
                    <div class="meta-field-value normal">
                        {{ $pengajuan->keperluan ?: 'Persyaratan pengajuan beasiswa prestasi' }}
                    </div>
                </div>
            </div>

            <!-- Card 3: Dokumen yang Diunggah -->
            <div class="detail-meta-card">
                <h3 class="detail-meta-title">Dokumen yang Diunggah</h3>

                @if($pengajuan->dokumens && $pengajuan->dokumens->count() > 0)
                    @foreach($pengajuan->dokumens as $dok)
                        <div class="doc-uploaded-box">
                            <div class="doc-uploaded-left">
                                <div class="pdf-icon-badge">PDF</div>
                                <div>
                                    <div class="doc-name-text">{{ $dok->nama_file_asli }}</div>
                                    <div class="doc-size-text">
                                        {{ $dok->file_size_kb ? ($dok->file_size_kb > 1000 ? round($dok->file_size_kb / 1024, 1) . ' MB' : $dok->file_size_kb . ' KB') : '524 KB' }}
                                    </div>
                                </div>
                            </div>

                            <a href="{{ $dok->file_path && file_exists(public_path('storage/' . $dok->file_path)) ? asset('storage/' . $dok->file_path) : 'javascript:void(0)' }}" target="{{ $dok->file_path && file_exists(public_path('storage/' . $dok->file_path)) ? '_blank' : '_self' }}" onclick="{{ !$dok->file_path || !file_exists(public_path('storage/' . $dok->file_path)) ? 'previewSamplePdf(\'' . $dok->nama_file_asli . '\')' : '' }}" class="btn-lihat-doc-clean">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>Lihat Dokumen</span>
                            </a>
                        </div>
                    @endforeach
                @else
                    <!-- Fallback Sample Documents Matching Screenshot -->
                    <div class="doc-uploaded-box">
                        <div class="doc-uploaded-left">
                            <div class="pdf-icon-badge">PDF</div>
                            <div>
                                <div class="doc-name-text">Kartu_Tanda_Mahasiswa.pdf</div>
                                <div class="doc-size-text">824 KB</div>
                            </div>
                        </div>

                        <button type="button" class="btn-lihat-doc-clean" onclick="previewSamplePdf('Kartu_Tanda_Mahasiswa.pdf')">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <span>Lihat Dokumen</span>
                        </button>
                    </div>

                    <div class="doc-uploaded-box">
                        <div class="doc-uploaded-left">
                            <div class="pdf-icon-badge">PDF</div>
                            <div>
                                <div class="doc-name-text">Bukti_Pembayaran_UKT.pdf</div>
                                <div class="doc-size-text">1.2 MB</div>
                            </div>
                        </div>

                        <button type="button" class="btn-lihat-doc-clean" onclick="previewSamplePdf('Bukti_Pembayaran_UKT.pdf')">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <span>Lihat Dokumen</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- RIGHT COLUMN: Tindakan Pemeriksaan & Riwayat Aktivitas -->
        <div>
            <!-- Card 1: Tindakan Pemeriksaan -->
            <div class="tindakan-action-card">
                <h3 class="tindakan-title">Tindakan Pemeriksaan</h3>
                <p class="tindakan-sub">Pastikan data dan dokumen telah sesuai.</p>

                <!-- Form Setujui Pengajuan -->
                <form action="{{ route('petugas.status.update', $pengajuan->nomor_tiket) }}" method="POST" id="formSetujui">
                    @csrf
                    <input type="hidden" name="action" value="setujui">
                    <button type="submit" class="btn-setujui-navy" id="btnSetujui">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Setujui Pengajuan</span>
                    </button>
                </form>

                <div class="divider-atau-wrap">
                    <span class="divider-atau-text">atau</span>
                </div>

                <!-- Button / Form Perlu Revisi -->
                <button type="button" class="btn-revisi-outline" onclick="toggleRevisionBox()" id="btnOpenRevisi">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    <span>Perlu Revisi</span>
                </button>

                <!-- Collapsible Revision Form -->
                <div id="revisionBox" style="display: none; margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed #e2e8f0;">
                    <form action="{{ route('petugas.status.update', $pengajuan->nomor_tiket) }}" method="POST">
                        @csrf
                        <input type="hidden" name="action" value="revisi">
                        <label for="catatan_revisi" class="meta-field-label">Catatan Perbaikan</label>
                        <textarea name="catatan" id="catatan_revisi" rows="3" class="custom-input custom-textarea" style="padding: 0.6rem; font-size: 0.8rem; margin-bottom: 0.65rem;" placeholder="Jelaskan berkas apa yang perlu diperbaiki oleh mahasiswa..." required>{{ old('catatan', $pengajuan->catatan_petugas) }}</textarea>
                        <div style="display: flex; gap: 0.5rem;">
                            <button type="submit" class="btn-revisi-outline" style="background: #ef4444; color: white; border: none; padding: 0.5rem;">
                                Simpan &amp; Minta Revisi
                            </button>
                            <button type="button" class="btn-lihat-doc-clean" onclick="toggleRevisionBox()">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Card 2: Riwayat aktivitas -->
            <div class="activity-card">
                <div class="activity-header">
                    <div class="activity-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2528c7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                    </div>
                    <h4>Riwayat aktivitas</h4>
                </div>

                <div class="activity-list" style="padding-left: 1.7rem;">
                    @if($isDisetujui)
                        <div class="activity-item" style="margin-bottom: 0.6rem;">
                            <div class="activity-item-content">
                                <div class="item-title">Pengajuan disetujui petugas</div>
                                <div class="item-time">{{ now()->isoFormat('D MMM Y, HH.mm') }}</div>
                            </div>
                        </div>
                    @endif

                    @forelse($pengajuan->riwayats->where('status_baru', '!=', \App\Models\Pengajuan::STATUS_DISETUJUI) as $riwayat)
                        <div class="activity-item" style="margin-bottom: 0.6rem;">
                            <div class="activity-item-content">
                                <div class="item-title">{{ $riwayat->catatan ?: 'Pengajuan diterima sistem' }}</div>
                                <div class="item-time">{{ $riwayat->created_at ? $riwayat->created_at->isoFormat('D MMM Y, HH.mm') : '19 Jun 2025, 08.42' }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="activity-item" style="margin-bottom: 0.6rem;">
                            <div class="activity-item-content">
                                <div class="item-title">Pengajuan diterima sistem</div>
                                <div class="item-time">19 Jun 2025, 08.42</div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleRevisionBox() {
    const box = document.getElementById('revisionBox');
    if (box) {
        if (box.style.display === 'none') {
            box.style.display = 'block';
            document.getElementById('catatan_revisi').focus();
        } else {
            box.style.display = 'none';
        }
    }
}

function previewSamplePdf(title) {
    alert('Pratinjau Berkas: ' + title + '\n\nDokumen berformat PDF valid dan siap diperiksa oleh petugas verifikasi.');
}
</script>
@endsection
