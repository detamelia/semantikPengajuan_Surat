@extends('layouts.app')

@section('title', 'Pendaftaran Akun Mahasiswa')

@section('content')
<div class="container" style="max-width: 540px; padding-top: 2rem; padding-bottom: 3rem;">
    <div class="card" style="box-shadow: var(--shadow-xl);">
        <div class="card-header" style="background: linear-gradient(135deg, var(--primary-700), var(--primary-900)); color: white; padding: 1.5rem; text-align: center; display: block;">
            <h2 style="font-size: 1.35rem; font-weight: 700; color: white;">Pendaftaran Akun Mahasiswa</h2>
            <p style="font-size: 0.825rem; color: #c7d2fe; margin-top: 0.25rem;">Akun akan disinkronkan otomatis dengan data master Sistem A (Pangkalan Data Mahasiswa)</p>
        </div>

        <div class="card-body">
            <form action="{{ route('register.post') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="npm" class="form-label">Nomor Pokok Mahasiswa (NPM)</label>
                    <div style="display: flex; gap: 0.5rem;">
                        <input type="text" name="npm" id="reg-npm" class="form-control" value="{{ old('npm') }}" required placeholder="Contoh: 22081010001">
                        <button type="button" id="btn-check-npm" class="btn btn-outline" style="white-space: nowrap;">Cek Sistem A</button>
                    </div>
                    <div id="npm-feedback"></div>
                    <span class="form-help">Sistem akan memeriksa kevalidan data mahasiswa ke REST API Sistem A.</span>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Alamat Email Aktif</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required placeholder="email.anda@gmail.com / kampus">
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Nomor WhatsApp / HP</label>
                    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx">
                </div>

                <div class="grid grid-cols-2" style="gap: 1rem;">
                    <div class="form-group">
                        <label for="password" class="form-label">Kata Sandi</label>
                        <input type="password" name="password" id="password" class="form-control" required placeholder="Minimal 6 karakter">
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Ulangi Kata Sandi</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required placeholder="Ulangi kata sandi">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 0.5rem;">
                    Daftar Sekarang
                </button>
            </form>

            <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--slate-200); font-size: 0.85rem; color: var(--slate-600); text-align: center;">
                Sudah memiliki akun? <a href="{{ route('login') }}" style="color: var(--primary-600); font-weight: 600; text-decoration: none;">Masuk di sini</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('btn-check-npm').addEventListener('click', function() {
    const npm = document.getElementById('reg-npm').value.trim();
    const feedback = document.getElementById('npm-feedback');

    if (!npm) {
        feedback.innerHTML = '<div class="mhs-failed-box">Harap ketikkan NPM terlebih dahulu.</div>';
        return;
    }

    feedback.innerHTML = '<div style="padding: 0.5rem 0; font-size: 0.85rem; color: var(--slate-500);"><span class="spinner" style="border-top-color: var(--primary-600); border-color: #cbd5e1;"></span> Menghubungi Sistem A...</div>';

    fetch('{{ url("/api/lookup-mhs") }}/' + encodeURIComponent(npm))
        .then(response => response.json())
        .then(res => {
            if (res.success && res.data) {
                feedback.innerHTML = `
                    <div class="mhs-verified-box">
                        <div style="font-weight: 700; color: #065f46; display: flex; align-items: center; gap: 0.35rem;">
                            <span>✓</span> Mahasiswa Terverifikasi (Sistem A)
                        </div>
                        <div style="font-size: 0.85rem; color: #047857; margin-top: 0.25rem;">
                            <strong>${res.data.nama}</strong> &bull; ${res.data.prodi} (Angkatan ${res.data.angkatan})
                        </div>
                        <div style="font-size: 0.75rem; color: #059669; font-family: var(--font-mono); margin-top: 0.25rem;">
                            URI Semantik: ${res.data.uri_semantik}
                        </div>
                    </div>
                `;
            } else {
                feedback.innerHTML = `<div class="mhs-failed-box">✕ ${res.message || 'Mahasiswa tidak ditemukan di Sistem A.'}</div>`;
            }
        })
        .catch(err => {
            feedback.innerHTML = `<div class="mhs-failed-box">✕ Gagal terhubung ke API Sistem A. Pastikan server Sistem A berjalan.</div>`;
        });
});
</script>
@endpush
