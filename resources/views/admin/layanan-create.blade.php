@extends('layouts.app')

@section('title', 'Tambah Layanan Baru')

@section('content')
<div class="container" style="max-width: 760px; padding-top: 2rem; padding-bottom: 3rem;">
    <!-- Breadcrumbs -->
    <div style="margin-bottom: 1.5rem; font-size: 0.85rem; color: var(--slate-500);">
        <a href="{{ route('admin.dashboard') }}" style="color: var(--primary-600); text-decoration: none;">Dashboard</a>
        <span style="margin: 0 0.4rem;">&gt;</span>
        <a href="{{ route('admin.layanan.index') }}" style="color: var(--primary-600); text-decoration: none;">Layanan</a>
        <span style="margin: 0 0.4rem;">&gt;</span>
        <span style="color: var(--slate-700); font-weight: 600;">Tambah Layanan Baru</span>
    </div>

    <div class="card" style="box-shadow: var(--shadow-md);">
        <div class="card-header" style="background: var(--slate-900); color: white;">
            <h2 style="font-size: 1.25rem; font-weight: 800; color: white;">Tambah Layanan Surat Baru</h2>
        </div>

        <div class="card-body" style="padding: 2rem;">
            <form action="{{ route('admin.layanan.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-2" style="gap: 1.25rem;">
                    <div class="form-group">
                        <label for="kode" class="form-label">Kode Layanan (Unik) <span style="color: var(--rose-600);">*</span></label>
                        <input type="text" name="kode" id="kode" class="form-control" value="{{ old('kode') }}" required placeholder="Contoh: SK-AKTIF">
                        <span class="form-help">Digunakan juga sebagai identifier individual URI Semantik.</span>
                    </div>

                    <div class="form-group">
                        <label for="estimasi_hari" class="form-label">Estimasi Hari Pengerjaan <span style="color: var(--rose-600);">*</span></label>
                        <input type="number" name="estimasi_hari" id="estimasi_hari" class="form-control" value="{{ old('estimasi_hari', 2) }}" min="1" max="30" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="nama" class="form-label">Nama Layanan Surat <span style="color: var(--rose-600);">*</span></label>
                    <input type="text" name="nama" id="nama" class="form-control" value="{{ old('nama') }}" required placeholder="Contoh: Surat Keterangan Aktif Kuliah">
                </div>

                <div class="form-group">
                    <label for="deskripsi" class="form-label">Deskripsi &amp; Informasi Layanan <span style="color: var(--rose-600);">*</span></label>
                    <textarea name="deskripsi" id="deskripsi" rows="3" class="form-control" required placeholder="Jelaskan fungsi surat ini dan kegunaannya...">{{ old('deskripsi') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="alur_pengajuan" class="form-label">Alur &amp; Prosedur Pengajuan</label>
                    <textarea name="alur_pengajuan" id="alur_pengajuan" rows="4" class="form-control" placeholder="Tuliskan poin-poin alur pengajuan...">{{ old('alur_pengajuan') }}</textarea>
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.9rem;">
                        <input type="checkbox" name="is_aktif" value="1" {{ old('is_aktif', true) ? 'checked' : '' }}>
                        Aktifkan layanan ini agar dapat dipilih oleh mahasiswa
                    </label>
                </div>

                <div style="border-top: 1px solid var(--slate-200); padding-top: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                    <a href="{{ route('admin.layanan.index') }}" class="btn btn-outline">&larr; Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Layanan Baru &rarr;</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
