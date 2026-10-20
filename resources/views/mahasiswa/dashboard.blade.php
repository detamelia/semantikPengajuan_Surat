@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">
            Selamat datang kembali, {{ explode(' ', Auth::user()?->name ?? 'Mahasiswa')[0] }}. Berikut ringkasan pengajuan Anda.
        </p>
    </div>
    <a href="{{ route('pengajuan.create') }}" class="btn-brand">
        <span>+</span> Ajukan Surat
    </a>
</div>

<!-- Info Alert Banner -->
@php
    $latestActive = $pengajuans->first();
@endphp
@if($latestActive)
    <div class="banner-info">
        <div class="banner-content">
            <div class="banner-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
            </div>
            <div>
                <strong>Pengajuan surat Anda telah diperbarui.</strong> 
                {{ $latestActive->nomor_tiket }} sedang 
                @if($latestActive->status === \App\Models\Pengajuan::STATUS_PERLU_PERBAIKAN)
                    memerlukan perbaikan berkas dari Anda.
                @elseif($latestActive->status === \App\Models\Pengajuan::STATUS_SELESAI)
                    selesai dan siap diunduh.
                @else
                    diproses oleh petugas administrasi.
                @endif
            </div>
        </div>
        <a href="{{ route('pengajuan.show', $latestActive->nomor_tiket) }}" class="banner-link">Lihat status</a>
    </div>
@endif

<!-- Stats Row of 3 Cards -->
<div class="stats-grid">
    <div class="stat-box">
        <div class="stat-icon-wrap green-light">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
        <div class="stat-meta">
            <div class="stat-label">Pengajuan Diproses</div>
            <div class="stat-number">{{ $stats['diproses'] ?? 2 }}</div>
        </div>
    </div>

    <div class="stat-box">
        <div class="stat-icon-wrap orange">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
        </div>
        <div class="stat-meta">
            <div class="stat-label">Perlu Revisi</div>
            <div class="stat-number">{{ $stats['perbaikan'] ?? 1 }}</div>
        </div>
    </div>

    <div class="stat-box">
        <div class="stat-icon-wrap emerald">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
        </div>
        <div class="stat-meta">
            <div class="stat-label">Selesai</div>
            <div class="stat-number">{{ $stats['selesai'] ?? 8 }}</div>
        </div>
    </div>
</div>

<!-- Table Card: Pengajuan Terbaru -->
<div class="content-card">
    <div class="card-top">
        <div class="card-top-title">
            <h3>Pengajuan Terbaru</h3>
            <p>Pantau perkembangan pengajuan surat terakhir Anda.</p>
        </div>
        <a href="{{ route('mahasiswa.riwayat') }}" class="card-link">
            Lihat Semua &gt;
        </a>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nomor Pengajuan</th>
                    <th>Jenis Surat</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuans as $item)
                    <tr>
                        <td>
                            <a href="{{ route('pengajuan.show', $item->nomor_tiket) }}" class="ticket-code">
                                {{ $item->nomor_tiket }}
                            </a>
                        </td>
                        <td>
                            <strong style="color: var(--text-primary); font-weight: 600;">
                                {{ $item->layanan->nama }}
                            </strong>
                        </td>
                        <td>
                            {{ $item->submitted_at ? $item->submitted_at->isoFormat('D MMMM Y') : '-' }}
                        </td>
                        <td>
                            @if($item->status === \App\Models\Pengajuan::STATUS_DIPROSES)
                                <span class="status-pill blue"><span class="dot"></span> Diproses</span>
                            @elseif($item->status === \App\Models\Pengajuan::STATUS_PERLU_PERBAIKAN)
                                <span class="status-pill red"><span class="dot"></span> Perlu Revisi</span>
                            @elseif(in_array($item->status, [\App\Models\Pengajuan::STATUS_SELESAI, \App\Models\Pengajuan::STATUS_DISETUJUI]))
                                <span class="status-pill green"><span class="dot"></span> Selesai</span>
                            @else
                                <span class="status-pill amber"><span class="dot"></span> Menunggu</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('pengajuan.show', $item->nomor_tiket) }}" class="btn-table-action">
                                Lihat Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 3rem;">
                            Belum ada permohonan surat yang diajukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Notification Note at bottom -->
<div class="dashboard-note">
    <span>✉️</span>
    <span>📱</span>
    <span>Pembaruan status juga dikirimkan melalui <strong>Email</strong> dan <strong>WhatsApp</strong> yang terdaftar.</span>
</div>
@endsection
