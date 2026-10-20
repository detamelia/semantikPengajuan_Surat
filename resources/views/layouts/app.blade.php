<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - SILAM Fakultas Teknik</title>
    <link rel="stylesheet" href="{{ asset('css/silam.css') }}">
    @stack('styles')
</head>
<body>
    <div class="app-layout">
        <!-- SIDEBAR (LEFT) -->
        <aside class="sidebar">
            <div>
                <!-- Brand Header -->
                <a href="{{ route('home') }}" class="sidebar-brand">
                    <div class="brand-shield">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L4.5 5.5V11.2C4.5 16.5 7.7 21.4 12 22.8C16.3 21.4 19.5 16.5 19.5 11.2V5.5L12 2Z" fill="#1e22be"/>
                            <path d="M12 4.2L6.2 6.8V11.4C6.2 15.4 8.7 19.3 12 20.4C15.3 19.3 17.8 15.4 17.8 11.4V6.8L12 4.2Z" fill="#ffffff" fill-opacity="0.25"/>
                            <path d="M12 7.2C10.2 7.2 8.8 8.8 8.8 10.8C8.8 13.5 11 15.8 12 16.4C13 15.8 15.2 13.5 15.2 10.8C15.2 8.8 13.8 7.2 12 7.2Z" fill="#ffffff"/>
                        </svg>
                    </div>
                    <div class="brand-info">
                        <h2>SILAM</h2>
                        <span>FAKULTAS TEKNIK</span>
                    </div>
                </a>

                <!-- Navigation Menu -->
                <div class="sidebar-section-title">MENU UTAMA</div>
                <ul class="sidebar-menu">
                    @if(Auth::check() && Auth::user()->isAdmin() && !request()->routeIs('mahasiswa.*', 'pengajuan.*', 'layanan.*', 'home'))
    @php
        $adminMenu = [
            ['admin.dashboard', 'admin.dashboard', 'Dashboard',
                '<rect x="3" y="3" width="7" height="7" rx="1.5"></rect><rect x="14" y="3" width="7" height="7" rx="1.5"></rect><rect x="14" y="14" width="7" height="7" rx="1.5"></rect><rect x="3" y="14" width="7" height="7" rx="1.5"></rect>'],
            ['admin.layanan.index', 'admin.layanan.*', 'Kelola Layanan',
                '<path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>'],
            ['admin.users.index', 'admin.users.*', 'Kelola Pengguna',
                '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>'],
            ['petugas.pengajuan.index', 'petugas.*', 'Semua Pengajuan',
                '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line>'],
            ['semantik.explorer', 'semantik.*', 'Semantic Web Explorer',
                '<circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>'],
        ];
    @endphp
    @foreach($adminMenu as [$routeName, $activePattern, $label, $icon])
        <li>
            <a href="{{ route($routeName) }}" class="sidebar-link {{ request()->routeIs($activePattern) ? 'active' : '' }}">
                <span class="sidebar-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $icon !!}</svg>
                </span>
                {{ $label }}
            </a>
        </li>
    @endforeach
@elseif(request()->routeIs('petugas.*') || (Auth::check() && Auth::user()->isPetugas() && !request()->routeIs('mahasiswa.*', 'pengajuan.*', 'layanan.*', 'home')))

                    @elseif(request()->routeIs('petugas.*') || (Auth::check() && Auth::user()->isPetugas() && !request()->routeIs('mahasiswa.*', 'pengajuan.*', 'layanan.*', 'home')))
                        <li>
                            <a href="{{ route('petugas.dashboard') }}" class="sidebar-link {{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}">
                                <span class="sidebar-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"></rect><rect x="14" y="3" width="7" height="7" rx="1.5"></rect><rect x="14" y="14" width="7" height="7" rx="1.5"></rect><rect x="3" y="14" width="7" height="7" rx="1.5"></rect></svg>
                                </span>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('petugas.pengajuan.index') }}" class="sidebar-link {{ (request()->routeIs('petugas.pengajuan.*') || request()->routeIs('petugas.show')) ? 'active' : '' }}">
                                <span class="sidebar-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                </span>
                                Daftar Pengajuan
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('mahasiswa.dashboard') }}" class="sidebar-link {{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}">
                                <span class="sidebar-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"></rect><rect x="14" y="3" width="7" height="7" rx="1.5"></rect><rect x="14" y="14" width="7" height="7" rx="1.5"></rect><rect x="3" y="14" width="7" height="7" rx="1.5"></rect></svg>
                                </span>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('layanan.index') }}" class="sidebar-link {{ request()->routeIs('layanan.*') ? 'active' : '' }}">
                                <span class="sidebar-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                </span>
                                Informasi Layanan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pengajuan.create') }}" class="sidebar-link {{ request()->routeIs('pengajuan.create') ? 'active' : '' }}">
                                <span class="sidebar-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="18" x2="12" y2="12"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg>
                                </span>
                                Ajukan Surat
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pengajuan.lacak') }}" class="sidebar-link {{ request()->routeIs('pengajuan.lacak') ? 'active' : '' }}">
                                <span class="sidebar-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                </span>
                                Status Pengajuan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('mahasiswa.riwayat') }}" class="sidebar-link {{ request()->routeIs('mahasiswa.riwayat') ? 'active' : '' }}">
                                <span class="sidebar-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path><polyline points="12 7 12 12 15 15"></polyline></svg>
                                </span>
                                Riwayat Pengajuan
                            </a>
                        </li>

                        @auth
                            @if(Auth::user()->isAdmin())
                                <li style="margin-top: 1rem; border-top: 1px solid rgba(255,255,255,0.15); padding-top: 0.75rem;">
                                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                                        <span class="sidebar-icon">⚙️</span>
                                        Admin Panel
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->isPetugas() || Auth::user()->isAdmin())
                                <li>
                                    <a href="{{ route('petugas.dashboard') }}" class="sidebar-link {{ request()->routeIs('petugas.*') ? 'active' : '' }}">
                                        <span class="sidebar-icon">🛡️</span>
                                        Petugas Verifikasi
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->isAdmin() || Auth::user()->isPetugas())
                                <li>
                                    <a href="{{ route('semantik.explorer') }}" class="sidebar-link {{ request()->routeIs('semantik.*') ? 'active' : '' }}">
                                        <span class="sidebar-icon">🌐</span>
                                        Semantic Web Explorer
                                    </a>
                                </li>
                            @endif
                        @endauth
                    @endif
                </ul>
            </div>

            <!-- Help Widget at bottom -->
            <div class="sidebar-help-card">
                <div class="help-card-inner">
                    <div class="help-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    </div>
                    <div class="help-texts">
                        <h4>Butuh bantuan?</h4>
                        <p>Hubungi bagian administrasi fakultas.</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- MAIN WRAPPER (RIGHT) -->
        <div class="main-wrapper">
            <!-- Topbar Header -->
            <header class="topbar">
                <div class="topbar-breadcrumb">
                    @if(request()->routeIs('petugas.*') || (Auth::check() && Auth::user()->isPetugas() && !request()->routeIs('mahasiswa.*', 'pengajuan.*', 'layanan.*', 'home')))
                        Portal Petugas
                    @elseif(Auth::check() && Auth::user()->isAdmin())
                        Portal Administrator
                    @else
                        Portal Mahasiswa
                    @endif
                </div>

                <div class="topbar-right">
                    <!-- Notification Bell -->
                    <button type="button" class="btn-bell-clean" title="Notifikasi">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <span class="badge-dot-red"></span>
                    </button>

                    <!-- User Profile & Avatar -->
                    @auth
                        <div class="user-profile">
                            <div class="user-avatar-navy" style="{{ (Auth::user()->isPetugas() || request()->routeIs('petugas.*')) ? 'background: #e2e8f0; color: #1e293b;' : '' }}">
                                @php
                                    $parts = preg_split('/\s+/', trim(Auth::user()->name));
                                    $firstInitial = mb_substr($parts[0] ?? 'S', 0, 1);
                                    $secondInitial = isset($parts[1]) ? mb_substr($parts[1], 0, 1) : (mb_strlen($parts[0] ?? '') > 1 ? mb_substr($parts[0], 1, 1) : 'N');
                                    $initials = strtoupper($firstInitial . $secondInitial);
                                @endphp
                                {{ $initials }}
                            </div>
                            <div class="user-details">
                                <div class="user-name">{{ Auth::user()->name }}</div>
                                <div class="user-role">{{ Auth::user()->isPetugas() ? 'Petugas Administrasi' : (Auth::user()->isAdmin() ? 'Administrator' : 'Mahasiswa') }}</div>
                            </div>
                        </div>

                        <!-- Logout Button -->
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn-logout-clean" title="Keluar">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                            </button>
                        </form>
                    @else
                        @if(request()->routeIs('petugas.*'))
                            <div class="user-profile">
                                <div class="user-avatar-navy" style="background: #e2e8f0; color: #1e293b;">SN</div>
                                <div class="user-details">
                                    <div class="user-name">Siti Nurhayati</div>
                                    <div class="user-role">Petugas Administrasi</div>
                                </div>
                            </div>
                        @else
                            <div class="user-profile">
                                <div class="user-avatar-navy">DA</div>
                                <div class="user-details">
                                    <div class="user-name">Deta Amelia</div>
                                    <div class="user-role">Mahasiswa</div>
                                </div>
                            </div>
                        @endif
                        <a href="{{ route('login') }}" class="btn-logout-clean" title="Masuk / Keluar">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        </a>
                    @endauth
                </div>
            </header>

            <!-- Page Body -->
            <div class="content-body">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; color: #047857; display: flex; align-items: center; gap: 0.75rem;">
                        <span>✓</span>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                @if(session('error'))
                    <div style="background: #fff1f2; border: 1px solid #fecdd3; border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; color: #be123c; display: flex; align-items: center; gap: 0.75rem;">
                        <span>✕</span>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                @if(session('warning'))
                    <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; color: #b45309; display: flex; align-items: center; gap: 0.75rem;">
                        <span>⚠️</span>
                        <div>{{ session('warning') }}</div>
                    </div>
                @endif

                @if($errors->any())
                    <div style="background: #fff1f2; border: 1px solid #fecdd3; border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; color: #be123c;">
                        <strong style="display: block; margin-bottom: 0.25rem;">Terdapat kesalahan pada isian:</strong>
                        <ul style="padding-left: 1.25rem; font-size: 0.85rem;">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Floating Help Button -->
    <a href="{{ route('pengajuan.lacak') }}" class="floating-help-btn" title="Bantuan & Lacak Status">?</a>

    @stack('scripts')
</body>
</html>
