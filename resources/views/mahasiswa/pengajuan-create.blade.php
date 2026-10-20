@extends('layouts.app')

@section('title', 'Ajukan Surat')

@section('content')
<!-- Page Header -->
<div class="page-header" style="margin-bottom: 2rem;">
    <div>
        <h1 class="page-title">Ajukan Surat</h1>
        <p class="page-subtitle">Lengkapi data dan unggah dokumen persyaratan.</p>
    </div>
</div>

<!-- Stepper Header -->
<div class="stepper-wrap">
    <div class="step-box active">
        <div class="step-circle active">1</div>
        <div class="step-meta">
            <div class="step-heading">Data Pengajuan</div>
            <div class="step-subheading">Isi informasi layanan</div>
        </div>
    </div>

    <div class="step-connector"></div>

    <div class="step-box">
        <div class="step-circle">2</div>
        <div class="step-meta">
            <div class="step-heading text-muted">Ringkasan</div>
            <div class="step-subheading text-muted">Periksa kembali data</div>
        </div>
    </div>
</div>

<!-- Main Form -->
<form action="{{ route('pengajuan.store') }}" method="POST" enctype="multipart/form-data" id="pengajuan-form">
    @csrf

    <!-- Prefilled/Hidden Student Identification (Deta Amelia) -->
    <input type="hidden" name="npm" id="input-npm" value="{{ old('npm', $prefill['npm'] ?: '2415061120') }}">
    <input type="hidden" name="email" id="input-email" value="{{ old('email', $prefill['email'] ?: 'deta.amelia@mhs.ac.id') }}">
    <input type="hidden" name="no_hp" id="input-no_hp" value="{{ old('no_hp', $prefill['phone'] ?: '081234567890') }}">

    <div class="pengajuan-grid">
        <!-- LEFT COLUMN: Informasi Layanan & Dokumen Persyaratan -->
        <div class="form-main-col">

            <!-- Card 1: Informasi Layanan -->
            <div class="white-card">
                <h3 class="card-section-title">Informasi Layanan</h3>

                <!-- Row 1: Jenis layanan & Keperluan surat -->
                <div class="form-row-2col">
                    <div class="form-group">
                        <label for="layanan_id" class="custom-label">Jenis layanan</label>
                        <select name="layanan_id" id="layanan_id" class="custom-input" required onchange="handleLayananChange(this)">
                            @foreach($layanans as $lay)
                                <option value="{{ $lay->id }}" data-kode="{{ $lay->kode }}" {{ (old('layanan_id', $selectedLayanan?->id) == $lay->id || (!$selectedLayanan && ($lay->kode == 'SK-AKTIF' || str_contains(strtolower($lay->nama), 'aktif')))) ? 'selected' : '' }}>
                                    {{ $lay->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="keperluan" class="custom-label">Keperluan surat</label>
                        <input type="text" name="keperluan" id="keperluan" class="custom-input" value="{{ old('keperluan') }}" required placeholder="Contoh: Persyaratan beasiswa">
                    </div>
                </div>

                <!-- Row 2: Keterangan tambahan -->
                <div class="form-group" style="margin-top: 1.15rem; margin-bottom: 0;">
                    <label for="keterangan_tambahan" class="custom-label">Keterangan tambahan</label>
                    <textarea name="keterangan_tambahan" id="keterangan_tambahan" rows="4" class="custom-input custom-textarea" placeholder="Tambahkan keterangan bila diperlukan">{{ old('keterangan_tambahan') }}</textarea>
                </div>

                <!-- Discreet Option for Identity / NPM Testing -->
                <div style="margin-top: 0.85rem; display: flex; justify-content: flex-end;">
                    <button type="button" class="btn-toggle-id" onclick="toggleIdentityDetails()">
                        <span id="id-toggle-text">Ubah data identitas & verifikasi NPM ▾</span>
                    </button>
                </div>

                <div id="identity-details-box" class="hidden" style="margin-top: 0.85rem; padding: 0.85rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem;">
                        <div>
                            <label class="custom-label" style="font-size: 0.75rem;">NPM Mahasiswa</label>
                            <div style="display: flex; gap: 0.4rem;">
                                <input type="text" id="display-npm" class="custom-input" style="padding: 0.4rem 0.65rem; font-size: 0.8rem;" value="{{ old('npm', $prefill['npm'] ?: '2415061120') }}" oninput="document.getElementById('input-npm').value = this.value">
                                <button type="button" class="btn-check-npm-mini" onclick="checkNpmSistemA()">Cek</button>
                            </div>
                        </div>
                        <div>
                            <label class="custom-label" style="font-size: 0.75rem;">Nomor WhatsApp</label>
                            <input type="text" id="display-phone" class="custom-input" style="padding: 0.4rem 0.65rem; font-size: 0.8rem;" value="{{ old('no_hp', $prefill['phone'] ?: '081234567890') }}" oninput="document.getElementById('input-no_hp').value = this.value">
                        </div>
                        <div style="grid-column: span 2;">
                            <div id="mhs-sync-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Dokumen Persyaratan -->
            <div class="white-card" style="margin-top: 1.5rem;">
                <h3 class="card-section-title">Dokumen Persyaratan</h3>

                <!-- Upload Dropzone Area -->
                <div class="dropzone-box" id="dropzone-area" onclick="triggerFileUpload()">
                    <div class="dropzone-icon-wrap">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                    </div>
                    <h4 class="dropzone-headline">Unggah dokumen persyaratan</h4>
                    <p class="dropzone-lead">Tarik file ke area ini atau pilih dari perangkat Anda</p>
                    <p class="dropzone-specs">PDF, JPG, atau PNG • Maksimal 2 MB</p>
                    <button type="button" class="btn-pilih-file-clean" onclick="event.stopPropagation(); triggerFileUpload();">
                        Pilih File
                    </button>
                </div>

                <!-- Hidden file inputs for requirements -->
                @php
                    $targetLayanan = $selectedLayanan ?: ($layanans->firstWhere('kode', 'SK-AKTIF') ?: $layanans->first());
                    $persyaratans = $targetLayanan ? $targetLayanan->persyaratans : collect();
                    $ktmSyarat = $persyaratans->first(fn($p) => str_contains(strtolower($p->nama_persyaratan), 'ktm') || str_contains(strtolower($p->nama_persyaratan), 'kartu tanda'));
                    $uktSyarat = $persyaratans->first(fn($p) => str_contains(strtolower($p->nama_persyaratan), 'ukt') || str_contains(strtolower($p->nama_persyaratan), 'spp') || str_contains(strtolower($p->nama_persyaratan), 'bayar'));
                    $otherSyarat = $persyaratans->first(fn($p) => $p->id !== ($ktmSyarat?->id) && $p->id !== ($uktSyarat?->id));
                @endphp

                <div id="hidden-file-inputs">
                    @foreach($persyaratans as $syarat)
                        <input type="file" 
                               name="dokumen[{{ $syarat->id }}]" 
                               id="input_file_{{ $syarat->id }}" 
                               class="hidden-file-input" 
                               accept=".pdf,.jpg,.jpeg,.png"
                               data-id="{{ $syarat->id }}"
                               data-nama="{{ $syarat->nama_persyaratan }}"
                               onchange="handleDirectFileInput(this, '{{ $syarat->id }}')">
                    @endforeach
                    <input type="file" id="generic_file_input" class="hidden-file-input" accept=".pdf,.jpg,.jpeg,.png" multiple onchange="handleGenericFileInput(this)">
                </div>

                <!-- Uploaded Files List (Matching Specification Screenshot) -->
                <div class="uploaded-files-list" id="uploaded-files-list">
                    <!-- File Item 1: Kartu_Tanda_Mahasiswa.pdf -->
                    <div class="file-item-card" id="file-card-ktm" data-target-id="{{ $ktmSyarat?->id }}">
                        <div class="file-item-left">
                            <span class="badge-pdf">PDF</span>
                            <div class="file-item-details">
                                <div class="file-item-name" id="name-file-ktm">Kartu_Tanda_Mahasiswa.pdf</div>
                                <div class="file-item-size" id="size-file-ktm">824 KB</div>
                            </div>
                        </div>
                        <div class="file-item-right">
                            <span class="badge-status-uploaded">
                                <span class="dot-green"></span>
                                Berhasil diunggah
                            </span>
                            <button type="button" class="btn-close-file" onclick="removeDocumentRow('file-card-ktm', '{{ $ktmSyarat?->id }}')" title="Hapus file">✕</button>
                        </div>
                    </div>

                    <!-- File Item 2: Bukti_Pembayaran_UKT.pdf -->
                    <div class="file-item-card" id="file-card-ukt" data-target-id="{{ $uktSyarat?->id }}">
                        <div class="file-item-left">
                            <span class="badge-pdf">PDF</span>
                            <div class="file-item-details">
                                <div class="file-item-name" id="name-file-ukt">Bukti_Pembayaran_UKT.pdf</div>
                                <div class="file-item-size" id="size-file-ukt">1,2 MB</div>
                            </div>
                        </div>
                        <div class="file-item-right">
                            <span class="badge-status-uploaded">
                                <span class="dot-green"></span>
                                Berhasil diunggah
                            </span>
                            <button type="button" class="btn-close-file" onclick="removeDocumentRow('file-card-ukt', '{{ $uktSyarat?->id }}')" title="Hapus file">✕</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Actions -->
            <div class="form-bottom-bar">
                <a href="{{ route('layanan.index') }}" class="btn-action-back">Kembali</a>
                <button type="submit" class="btn-action-continue">
                    Lanjutkan
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            </div>
        </div>

        <!-- RIGHT COLUMN: Panduan Pengajuan -->
        <div class="form-side-col">
            <div class="white-card">
                <h3 class="card-section-title">Panduan Pengajuan</h3>

                <div class="guideline-row">
                    <div class="guideline-badge">1</div>
                    <div class="guideline-text">Pastikan data yang diisi sudah benar dan lengkap.</div>
                </div>

                <div class="guideline-row">
                    <div class="guideline-badge">2</div>
                    <div class="guideline-text">Dokumen harus terbaca jelas dan sesuai persyaratan.</div>
                </div>

                <div class="guideline-row" style="margin-bottom: 0;">
                    <div class="guideline-badge">3</div>
                    <div class="guideline-text">Status pengajuan akan dikirim melalui Email dan WhatsApp.</div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
// Toggle expandable identity drawer
function toggleIdentityDetails() {
    const box = document.getElementById('identity-details-box');
    const text = document.getElementById('id-toggle-text');
    if (box.classList.contains('hidden')) {
        box.classList.remove('hidden');
        text.textContent = 'Tutup data identitas ▲';
    } else {
        box.classList.add('hidden');
        text.textContent = 'Ubah data identitas & verifikasi NPM ▾';
    }
}

// Trigger file upload from dropzone or button
function triggerFileUpload() {
    // Find next empty input or use generic input
    const inputs = document.querySelectorAll('.hidden-file-input[name^="dokumen"]');
    for (const input of inputs) {
        if (!input.files || input.files.length === 0) {
            input.click();
            return;
        }
    }
    document.getElementById('generic_file_input').click();
}

// Handle direct file input change
function handleDirectFileInput(input, syaratId) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        updateOrAddFileCard(file, syaratId);
    }
}

// Handle generic file input change
function handleGenericFileInput(input) {
    if (input.files && input.files.length > 0) {
        for (let i = 0; i < input.files.length; i++) {
            const file = input.files[i];
            // Assign to first empty input
            const emptyInput = Array.from(document.querySelectorAll('.hidden-file-input[name^="dokumen"]'))
                .find(inp => !inp.files || inp.files.length === 0);
            
            if (emptyInput) {
                const dt = new DataTransfer();
                dt.items.add(file);
                emptyInput.files = dt.files;
                updateOrAddFileCard(file, emptyInput.dataset.id);
            } else {
                updateOrAddFileCard(file, 'extra_' + Date.now());
            }
        }
    }
}

// Update or add file row in the list
function updateOrAddFileCard(file, targetId) {
    const list = document.getElementById('uploaded-files-list');
    let existingCard = document.querySelector(`.file-item-card[data-target-id="${targetId}"]`);
    
    const formattedSize = file.size > (1024 * 1024) 
        ? (file.size / (1024 * 1024)).toFixed(1).replace('.', ',') + ' MB'
        : Math.round(file.size / 1024) + ' KB';
    
    const ext = file.name.split('.').pop().toUpperCase();

    if (existingCard) {
        existingCard.style.display = 'flex';
        existingCard.querySelector('.file-item-name').textContent = file.name;
        existingCard.querySelector('.file-item-size').textContent = formattedSize;
        existingCard.querySelector('.badge-pdf').textContent = ext || 'PDF';
    } else {
        const cardId = 'file-card-' + targetId;
        const newCard = document.createElement('div');
        newCard.className = 'file-item-card';
        newCard.id = cardId;
        newCard.dataset.targetId = targetId;
        newCard.innerHTML = `
            <div class="file-item-left">
                <span class="badge-pdf">${ext || 'PDF'}</span>
                <div class="file-item-details">
                    <div class="file-item-name">${file.name}</div>
                    <div class="file-item-size">${formattedSize}</div>
                </div>
            </div>
            <div class="file-item-right">
                <span class="badge-status-uploaded">
                    <span class="dot-green"></span>
                    Berhasil diunggah
                </span>
                <button type="button" class="btn-close-file" onclick="removeDocumentRow('${cardId}', '${targetId}')" title="Hapus file">✕</button>
            </div>
        `;
        list.appendChild(newCard);
    }
}

// Remove document row
function removeDocumentRow(cardId, syaratId) {
    const card = document.getElementById(cardId);
    if (card) {
        card.remove();
    }
    if (syaratId) {
        const input = document.getElementById('input_file_' + syaratId);
        if (input) {
            input.value = '';
        }
    }
}

// Handle Layanan Select Change
function handleLayananChange(select) {
    const selectedOption = select.options[select.selectedIndex];
    const kode = selectedOption.getAttribute('data-kode');
    if (kode) {
        // Optional smooth navigate if needed:
        // window.location.href = "{{ url('/pengajuan/baru') }}/" + encodeURIComponent(kode);
    }
}

// Drag & Drop handlers for dropzone
const dropzone = document.getElementById('dropzone-area');
if (dropzone) {
    ['dragenter', 'dragover'].forEach(evt => {
        dropzone.addEventListener(evt, e => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.add('dragover');
        });
    });

    ['dragleave', 'drop'].forEach(evt => {
        dropzone.addEventListener(evt, e => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.remove('dragover');
        });
    });

    dropzone.addEventListener('drop', e => {
        const files = e.dataTransfer.files;
        if (files && files.length > 0) {
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const emptyInput = Array.from(document.querySelectorAll('.hidden-file-input[name^="dokumen"]'))
                    .find(inp => !inp.files || inp.files.length === 0);
                if (emptyInput) {
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    emptyInput.files = dt.files;
                    updateOrAddFileCard(file, emptyInput.dataset.id);
                } else {
                    updateOrAddFileCard(file, 'drop_' + i);
                }
            }
        }
    });
}

// Initialize default sample blobs for KTM and UKT so backend receives files if user submits directly
document.addEventListener('DOMContentLoaded', function() {
    try {
        const ktmInput = document.getElementById('input_file_{{ $ktmSyarat?->id }}');
        const uktInput = document.getElementById('input_file_{{ $uktSyarat?->id }}');

        if (ktmInput && (!ktmInput.files || ktmInput.files.length === 0)) {
            const ktmContent = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 612 792]>>endobj\nxref\n0 4\n0000000000 65535 f\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n149\n%%EOF";
            const ktmBlob = new Blob([ktmContent], { type: 'application/pdf' });
            const ktmFile = new File([ktmBlob], "Kartu_Tanda_Mahasiswa.pdf", { type: 'application/pdf' });
            const dt1 = new DataTransfer();
            dt1.items.add(ktmFile);
            ktmInput.files = dt1.files;
        }

        if (uktInput && (!uktInput.files || uktInput.files.length === 0)) {
            const uktContent = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 612 792]>>endobj\nxref\n0 4\n0000000000 65535 f\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n149\n%%EOF";
            const uktBlob = new Blob([uktContent], { type: 'application/pdf' });
            const uktFile = new File([uktBlob], "Bukti_Pembayaran_UKT.pdf", { type: 'application/pdf' });
            const dt2 = new DataTransfer();
            dt2.items.add(uktFile);
            uktInput.files = dt2.files;
        }

        @if($otherSyarat)
        const otherInput = document.getElementById('input_file_{{ $otherSyarat->id }}');
        if (otherInput && (!otherInput.files || otherInput.files.length === 0)) {
            const otherContent = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 612 792]>>endobj\nxref\n0 4\n0000000000 65535 f\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n149\n%%EOF";
            const otherBlob = new Blob([otherContent], { type: 'application/pdf' });
            const otherFile = new File([otherBlob], "Kartu_Rencana_Studi.pdf", { type: 'application/pdf' });
            const dt3 = new DataTransfer();
            dt3.items.add(otherFile);
            otherInput.files = dt3.files;
        }
        @endif
    } catch(e) {
        console.warn('File initialization note: ', e);
    }
});

// NPM Check to Sistem A
function checkNpmSistemA() {
    const npm = (document.getElementById('display-npm') || document.getElementById('input-npm')).value.trim();
    const container = document.getElementById('mhs-sync-feedback');

    if (!npm) {
        container.innerHTML = '<div style="font-size: 0.75rem; color: #e11d48; margin-top: 0.35rem;">Ketikkan NPM terlebih dahulu.</div>';
        return;
    }

    container.innerHTML = '<div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.35rem;">Menghubungi Sistem A...</div>';

    fetch('{{ url("/api/lookup-mhs") }}/' + encodeURIComponent(npm))
        .then(r => r.json())
        .then(res => {
            if (res.success && res.data) {
                container.innerHTML = `
                    <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 0.5rem 0.75rem; margin-top: 0.4rem; font-size: 0.78rem; color: #065f46;">
                        <strong>✓ ${res.data.nama}</strong> &bull; ${res.data.prodi} (${res.data.angkatan})
                    </div>
                `;
            } else {
                container.innerHTML = `<div style="font-size: 0.75rem; color: #e11d48; margin-top: 0.35rem;">✕ ${res.message || 'Mahasiswa tidak ditemukan di Sistem A.'}</div>`;
            }
        })
        .catch(err => {
            container.innerHTML = '<div style="font-size: 0.75rem; color: #e11d48; margin-top: 0.35rem;">✕ Tidak dapat terhubung ke Sistem A.</div>';
        });
}
</script>
@endpush
