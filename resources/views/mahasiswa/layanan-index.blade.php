@extends('layouts.app')

@section('title', 'Informasi Layanan')

@section('content')
<!-- Page Header -->
<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <h1 class="page-title">Informasi Layanan</h1>
        <p class="page-subtitle">
            Temukan informasi dan persyaratan layanan administrasi mahasiswa.
        </p>
    </div>
</div>

<!-- Search & Counter Bar -->
<div class="search-filter-bar">
    <div class="search-input-box">
        <span class="search-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        </span>
        <input type="text" id="service-search-input" placeholder="Cari layanan administrasi...">
    </div>

    <div style="font-size: 0.885rem; color: var(--text-muted); font-weight: 500;">
        <span id="service-count">{{ $layanans->count() }}</span> layanan tersedia
    </div>
</div>

<!-- Services List -->
<div class="services-list" id="services-container">
    @forelse($layanans as $layanan)
        <div class="service-card-item" data-title="{{ strtolower($layanan->nama) }}" data-desc="{{ strtolower($layanan->deskripsi) }}">
            <div class="service-doc-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            </div>

            <div class="service-main">
                <h3>{{ $layanan->nama }}</h3>
                <p>{{ $layanan->deskripsi }}</p>

                <div class="service-specs-grid">
                    <div class="spec-col">
                        <span>Persyaratan</span>
                        <strong>
                            @if($layanan->persyaratans->count() > 0)
                                {{ $layanan->persyaratans->pluck('nama_persyaratan')->take(2)->join(' dan ') }}
                            @else
                                KTM dan formulir permohonan
                            @endif
                        </strong>
                    </div>

                    <div class="spec-col">
                        <span>Alur Layanan</span>
                        <strong>Verifikasi &rarr; Persetujuan &rarr; Terbit</strong>
                    </div>

                    <div class="spec-col">
                        <span>Estimasi</span>
                        <strong>{{ $layanan->estimasi_hari }}&ndash;{{ $layanan->estimasi_hari + 1 }} hari kerja</strong>
                    </div>
                </div>

                <div class="service-actions-bottom">
                    <a href="{{ route('layanan.show', $layanan->kode) }}" class="service-template-link">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        Template dokumen tersedia
                    </a>

                    <a href="{{ route('layanan.show', $layanan->kode) }}" class="btn-white-outline">
                        Lihat Detail &gt;
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div style="background: white; border-radius: var(--radius-lg); border: 1px dashed var(--border-color); padding: 3rem; text-align: center; color: var(--text-muted);">
            Belum ada informasi layanan yang ditambahkan.
        </div>
    @endforelse
</div>
@endsection

@push('scripts')
<script>
document.getElementById('service-search-input').addEventListener('input', function() {
    const q = this.value.toLowerCase().trim();
    const items = document.querySelectorAll('.service-card-item');
    let visibleCount = 0;

    items.forEach(item => {
        const title = item.getAttribute('data-title') || '';
        const desc = item.getAttribute('data-desc') || '';
        if (title.includes(q) || desc.includes(q)) {
            item.style.display = 'flex';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    document.getElementById('service-count').textContent = visibleCount;
});
</script>
@endpush
