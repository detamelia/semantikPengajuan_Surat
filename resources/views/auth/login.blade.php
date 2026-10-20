<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk - Sistem Layanan Administrasi Mahasiswa (SILAM)</title>
    <link rel="stylesheet" href="{{ asset('css/silam.css') }}">
    <style>
        /* Smooth circle ripple animation & subtle glowing effects */
        .login-bg-circles circle {
            transition: all 0.5s ease;
        }
        .login-split-page {
            position: relative;
        }
    </style>
</head>
<body>
    <div class="login-split-page">
        <!-- ==================== LEFT HERO PANEL ==================== -->
        <div class="login-hero-panel">
            <!-- Concentric Circles Graphic Overlay matching screenshot -->
            <svg class="login-bg-circles" viewBox="0 0 1000 1000" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="20" cy="530" r="140" fill="rgba(255, 255, 255, 0.02)" />
                <circle cx="20" cy="530" r="220" stroke="rgba(255, 255, 255, 0.08)" stroke-width="36" />
                <circle cx="20" cy="530" r="330" stroke="rgba(255, 255, 255, 0.06)" stroke-width="48" />
                <circle cx="20" cy="530" r="460" stroke="rgba(255, 255, 255, 0.05)" stroke-width="60" />
                <circle cx="20" cy="530" r="610" stroke="rgba(255, 255, 255, 0.04)" stroke-width="72" />
                <circle cx="20" cy="530" r="780" stroke="rgba(255, 255, 255, 0.03)" stroke-width="84" />
                <circle cx="20" cy="530" r="970" stroke="rgba(255, 255, 255, 0.02)" stroke-width="96" />
                <circle cx="20" cy="530" r="1180" stroke="rgba(255, 255, 255, 0.015)" stroke-width="110" />
            </svg>

            <div class="login-hero-inner">
                <!-- Top Brand -->
                <a href="{{ route('home') }}" class="login-brand" title="Beranda SILAM">
                    <div class="login-brand-shield">
                        <span>S</span>
                    </div>
                    <div class="login-brand-text">
                        <div class="login-brand-title">SILAM</div>
                        <div class="login-brand-subtitle">FAKULTAS TEKNIK</div>
                    </div>
                </a>

                <!-- Hero Middle Content -->
                <div class="login-hero-content">
                    <div class="login-univ-tag">UNIVERSITAS LAMPUNG</div>
                    <h1 class="login-hero-headline">Layanan administrasi akademik yang lebih mudah.</h1>
                    <p class="login-hero-desc">
                        Ajukan, pantau, dan unduh surat administrasi mahasiswa dalam satu sistem terpadu.
                    </p>
                </div>

                <!-- Footer Left -->
                <div class="login-hero-footer">
                    Fakultas Teknik · Universitas Lampung
                </div>
            </div>
        </div>

        <!-- ==================== RIGHT FORM PANEL ==================== -->
        <div class="login-form-panel">
            <div class="login-form-wrapper">
                <!-- Header -->
                <div class="login-form-header">
                    <div class="login-welcome-badge">SELAMAT DATANG</div>
                    <h2 class="login-form-title">Sistem Layanan Administrasi Mahasiswa</h2>
                    <p class="login-form-subtitle">Fakultas Teknik Universitas Lampung</p>
                </div>

                <!-- Flash Alert Messages -->
                @if(session('info'))
                    <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 0.75rem 1rem; color: #1e40af; font-size: 13px; margin-bottom: 1.25rem;">
                        {{ session('info') }}
                    </div>
                @endif

                @if(session('error') || $errors->any())
                    <div style="background: #fff1f2; border: 1px solid #fecdd3; border-radius: 8px; padding: 0.75rem 1rem; color: #be123c; font-size: 13px; margin-bottom: 1.25rem;">
                        @if(session('error'))
                            <div>{{ session('error') }}</div>
                        @else
                            <div>{{ $errors->first() }}</div>
                        @endif
                    </div>
                @endif

                <!-- Login Form -->
                <form action="{{ route('login.post') }}" method="POST" id="loginForm">
                    @csrf
                    <input type="hidden" name="role" id="selectedRole" value="{{ old('role', 'mahasiswa') }}">

                    <!-- Akun Kampus Input -->
                    <div class="login-input-group">
                        <label for="akun_kampus" class="login-label">Akun Kampus</label>
                        <input type="text" 
                               name="akun_kampus" 
                               id="akun_kampus" 
                               class="login-input" 
                               placeholder="Masukkan NPM atau NIP" 
                               value="{{ old('akun_kampus', old('email')) }}" 
                               required 
                               autofocus 
                               autocomplete="username">
                    </div>

                    <!-- Masuk Sebagai Segmented Pill Tabs -->
                    <div class="login-input-group" style="margin-top: 1.25rem;">
                        <label class="login-label">Masuk sebagai</label>
                        <div class="role-segmented-control" role="tablist">
                            <button type="button" 
                                    class="role-tab {{ old('role', 'mahasiswa') === 'mahasiswa' ? 'active' : '' }}" 
                                    data-role="mahasiswa" 
                                    id="tab-mahasiswa"
                                    role="tab" 
                                    aria-selected="{{ old('role', 'mahasiswa') === 'mahasiswa' ? 'true' : 'false' }}">
                                Mahasiswa
                            </button>
                            <button type="button" 
                                    class="role-tab {{ old('role') === 'petugas' ? 'active' : '' }}" 
                                    data-role="petugas" 
                                    id="tab-petugas"
                                    role="tab" 
                                    aria-selected="{{ old('role') === 'petugas' ? 'true' : 'false' }}">
                                Petugas
                            </button>
                            <button type="button" 
                                    class="role-tab {{ old('role') === 'admin' ? 'active' : '' }}" 
                                    data-role="admin" 
                                    id="tab-admin"
                                    role="tab" 
                                    aria-selected="{{ old('role') === 'admin' ? 'true' : 'false' }}">
                                Admin
                            </button>
                        </div>
                    </div>

                    <!-- Optional Password Input Group (Toggleable) -->
                    <div id="passwordFieldGroup" style="display: none; margin-top: 1.15rem;">
                        <label for="password" class="login-label">Kata Sandi</label>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="login-input" 
                               placeholder="Masukkan kata sandi akun"
                               autocomplete="current-password">
                    </div>

                    <!-- Submit SSO Button -->
                    <button type="submit" class="login-submit-btn" id="btnSubmit">
                        <span id="btnText">Login melalui SSO</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </button>

                    <!-- Info Box -->
                    <div class="login-info-card">
                        <div class="info-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2528c7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                        </div>
                        <span class="info-text">Login menggunakan akun kampus melalui SSO.</span>
                    </div>

                    <!-- Testing & Demo Helper Controls -->
                    <div class="demo-helper-wrapper">
                        <div class="demo-helper-title">
                            <span>Akun Demo Cepat:</span>
                            <a href="javascript:void(0)" id="togglePasswordMode" style="color: #2528c7; text-decoration: none; font-size: 11.5px;">
                                + Gunakan Kata Sandi
                            </a>
                        </div>
                        <div class="demo-helper-pills">
                            <button type="button" class="demo-pill-btn" onclick="fillDemo('mahasiswa', '2415061120')">
                                Mahasiswa: <strong>2415061120</strong>
                            </button>
                            <button type="button" class="demo-pill-btn" onclick="fillDemo('petugas', 'petugas@silam.ac.id')">
                                Petugas: <strong>petugas@silam.ac.id</strong>
                            </button>
                            <button type="button" class="demo-pill-btn" onclick="fillDemo('admin', 'admin@silam.ac.id')">
                                Admin: <strong>admin@silam.ac.id</strong>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer Centered at Bottom of Right Column -->
            <div class="login-form-footer">
                Sistem resmi Fakultas Teknik Universitas Lampung
            </div>
        </div>
    </div>

    <!-- Interactive script for tabs and quick fills -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.querySelectorAll('.role-tab');
            const roleInput = document.getElementById('selectedRole');
            const akunInput = document.getElementById('akun_kampus');
            const btnText = document.getElementById('btnText');
            const togglePasswordMode = document.getElementById('togglePasswordMode');
            const passwordGroup = document.getElementById('passwordFieldGroup');
            const passwordInput = document.getElementById('password');

            const placeholders = {
                mahasiswa: 'Masukkan NPM atau NIP',
                petugas: 'Masukkan NIP atau Email Petugas',
                admin: 'Masukkan NIP atau Email Admin'
            };

            // Switch tabs
            tabs.forEach(tab => {
                tab.addEventListener('click', function () {
                    const role = this.getAttribute('data-role');
                    selectRole(role);
                });
            });

            window.selectRole = function (role) {
                tabs.forEach(t => {
                    t.classList.remove('active');
                    t.setAttribute('aria-selected', 'false');
                });

                const activeTab = document.querySelector(`.role-tab[data-role="${role}"]`);
                if (activeTab) {
                    activeTab.classList.add('active');
                    activeTab.setAttribute('aria-selected', 'true');
                }

                roleInput.value = role;
                akunInput.placeholder = placeholders[role] || 'Masukkan NPM atau NIP';
            };

            // Quick demo filler
            window.fillDemo = function (role, account) {
                selectRole(role);
                akunInput.value = account;
                if (passwordGroup.style.display !== 'none') {
                    passwordInput.value = 'password';
                }
                akunInput.focus();
            };

            // Toggle password field
            togglePasswordMode.addEventListener('click', function () {
                if (passwordGroup.style.display === 'none') {
                    passwordGroup.style.display = 'block';
                    togglePasswordMode.textContent = '- Sembunyikan Kata Sandi';
                    btnText.textContent = 'Masuk dengan Kata Sandi';
                    passwordInput.focus();
                } else {
                    passwordGroup.style.display = 'none';
                    passwordInput.value = '';
                    togglePasswordMode.textContent = '+ Gunakan Kata Sandi';
                    btnText.textContent = 'Login melalui SSO';
                }
            });
        });
    </script>
</body>
</html>
