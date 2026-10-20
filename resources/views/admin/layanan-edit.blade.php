@extends('layouts.app')

@section('title', 'Edit Layanan ' . $layanan->nama)

@section('content')
<div class="container" style="max-width: 960px; padding-top: 2rem; padding-bottom: 3rem;">
    <!-- Breadcrumbs -->
    <div style="margin-bottom: 1.5rem; font-size: 0.85rem; color: var(--slate-500);">
        <a href="{{ route('admin.dashboard') }}" style="color: var(--primary-600); text-decoration: none;">Dashboard</a>
        <span style="margin: 0 0.4rem;">&gt;</span>
        <a href="{{ route('admin.layanan.index') }}" style="color: var(--primary-600); text-decoration: none;">Layanan</a>
        <span style="margin: 0 0.4rem;">&gt;</span>
        <span style="color: var(--slate-700); font-weight: 600;">Edit Layanan: {{ $layanan->kode }}</span>
    </div>

    <!-- Edit Form Card -->
    <div class="card" style="margin-bottom: 2.5rem; box-shadow: var(--shadow-md);">
        <div class="card-header" style="background: var(--slate-900); color: white;">
            <h2 style="font-size: 1.25rem; font-weight: 800; color: white;">Edit Informasi Layanan Surat</h2>
        </div>

        <div class="card-body" style="padding: 2rem;">
            <form action="{{ route('admin.layanan.update', $layanan->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2" style="gap: 1.25rem;">
                    <div class="form-group">
                        <label for="kode" class="form-label">Kode Layanan <span style="color: var(--rose-600);">*</span></label>
                        <input type="text" name="kode" id="kode" class="form-control" value="{{ old('kode', $layanan->kode) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="estimasi_hari" class="form-label">Estimasi Hari Kerja <span style="color: var(--rose-600);">*</span></label>
                        <input type="number" name="estimasi_hari" id="estimasi_hari" class="form-control" value="{{ old('estimasi_hari', $layanan->estimasi_hari) }}" min="1" max="30" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="nama" class="form-label">Nama Layanan Surat <span style="color: var(--rose-600);">*</span></label>
                    <input type="text" name="nama" id="nama" class="form-control" value="{{ old('nama', $layanan->nama) }}" required>
                </div>

                <div class="form-group">
                    <label for="deskripsi" class="form-label">Deskripsi Layanan <span style="color: var(--rose-600);">*</span></label>
                    <textarea name="deskripsi" id="deskripsi" rows="3" class="form-control" required>{{ old('deskripsi', $layanan->deskripsi) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="alur_pengajuan" class="form-label">Alur Pengajuan</label>
                    <textarea name="alur_pengajuan" id="alur_pengajuan" rows="4" class="form-control">{{ old('alur_pengajuan', $layanan->alur_pengajuan) }}</textarea>
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.9rem;">
                        <input type="checkbox" name="is_aktif" value="1" {{ old('is_aktif', $layanan->is_aktif) ? 'checked' : '' }}>
                        Layanan Aktif (Bisa Dipilih Mahasiswa)
                    </label>
                </div>

                <div style="border-top: 1px solid var(--slate-200); padding-top: 1.25rem; display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan Layanan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Kelola Persyaratan Dokumen -->
    <div class="card" style="box-shadow: var(--shadow-md);">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 class="card-title">Persyaratan Dokumen untuk {{ $layanan->nama }}</h3>
        </div>

        <div class="card-body">
            <!-- Table Existing Requirements -->
            <div class="table-responsive" style="margin-bottom: 2rem;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Urutan</th>
                            <th>Nama Persyaratan</th>
                            <th>Deskripsi / Panduan</th>
                            <th>Format File</th>
                            <th>Ukuran Maksimal</th>
                            <th>Sifat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($layanan->persyaratans as $syarat)
                            <tr>
                                <td>{{ $syarat->urutan }}</td>
                                <td><strong>{{ $syarat->nama_persyaratan }}</strong></td>
                                <td><span style="font-size: 0.8rem; color: var(--slate-600);">{{ $syarat->deskripsi ?: '-' }}</span></td>
                                <td><code>{{ strtoupper($syarat->tipe_file) }}</code></td>
                                <td>{{ round($syarat->max_size_kb / 1024, 1) }} MB</td>
                                <td>
                                    @if($syarat->is_wajib)
                                        <span class="badge badge-rose" style="font-size: 0.65rem;">Wajib</span>
                                    @else
                                        <span class="badge badge-slate" style="font-size: 0.65rem;">Opsional</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('admin.persyaratan.destroy', $syarat->id) }}" method="POST" onsubmit="return confirm('Hapus persyaratan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: var(--slate-500); padding: 1.5rem;">
                                    Belum ada persyaratan dokumen yang dikonfigurasikan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Form Tambah Persyaratan Baru -->
            <div style="background: var(--slate-50); border: 1px dashed var(--slate-300); border-radius: var(--radius-md); padding: 1.5rem;">
                <h4 style="font-size: 1rem; font-weight: 700; color: var(--slate-900); margin-bottom: 1rem;">+ Tambah Persyaratan Dokumen Baru</h4>
                <form action="{{ route('admin.persyaratan.store', $layanan->id) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-2" style="gap: 1rem; margin-bottom: 1rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="nama_persyaratan" class="form-label">Nama Persyaratan Dokumen <span style="color: var(--rose-600);">*</span></label>
                            <input type="text" name="nama_persyaratan" id="nama_persyaratan" class="form-control" required placeholder="Contoh: Kartu Tanda Mahasiswa (KTM)">
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="deskripsi_syarat" class="form-label">Keterangan / Panduan</label>
                            <input type="text" name="deskripsi" id="deskripsi_syarat" class="form-control" placeholder="Contoh: Scan KTM yang masih berlaku">
                        </div>
                    </div>

                    <div class="grid grid-cols-3" style="gap: 1rem; margin-bottom: 1rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="tipe_file" class="form-label">Ekstensi File (Pisahkan Koma) <span style="color: var(--rose-600);">*</span></label>
                            <input type="text" name="tipe_file" id="tipe_file" class="form-control" value="pdf,jpg,jpeg,png" required>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="max_size_kb" class="form-label">Ukuran Maksimal (KB) <span style="color: var(--rose-600);">*</span></label>
                            <input type="number" name="max_size_kb" id="max_size_kb" class="form-control" value="2048" min="256" max="10240" required>
                        </div>

                        <div class="form-group" style="margin-bottom: 0; display: flex; align-items: center; padding-top: 1.5rem;">
                            <label style="display: flex; align-items: center; gap: 0.4rem; cursor: pointer; font-size: 0.85rem;">
                                <input type="checkbox" name="is_wajib" value="1" checked>
                                Dokumen Wajib Diunggah
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm">
                        Simpan Persyaratan Dokumen
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
