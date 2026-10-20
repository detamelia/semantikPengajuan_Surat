<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\SemanticWebController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SILAM (Sistem Layanan Administrasi Mahasiswa)
|--------------------------------------------------------------------------
*/

// Beranda & Katalog Layanan Publik
Route::get('/', [MahasiswaController::class, 'layananIndex'])->name('home');
Route::get('/layanan', [MahasiswaController::class, 'layananIndex'])->name('layanan.index');
Route::get('/layanan/{kode}', [MahasiswaController::class, 'layananShow'])->name('layanan.show');

// Formulir Pengajuan & Lacak Mandiri
Route::get('/pengajuan/baru/{kode?}', [MahasiswaController::class, 'createPengajuan'])->name('pengajuan.create');
Route::post('/pengajuan', [MahasiswaController::class, 'storePengajuan'])->name('pengajuan.store');
Route::get('/lacak', [MahasiswaController::class, 'lacak'])->name('pengajuan.lacak');
Route::get('/pengajuan/{nomor_tiket}', [MahasiswaController::class, 'showPengajuan'])->name('pengajuan.show');
Route::post('/pengajuan/{nomor_tiket}/revisi', [MahasiswaController::class, 'revisiDokumen'])->name('pengajuan.revisi');
Route::get('/pengajuan/{nomor_tiket}/unduh', [MahasiswaController::class, 'unduhSurat'])->name('pengajuan.unduh');

// Endpoint AJAX Lookup Mahasiswa via API Sistem A
Route::get('/api/lookup-mhs/{npm}', [MahasiswaController::class, 'apiLookupMhs'])->name('api.lookup.mhs');

// Autentikasi Pengguna
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute Mahasiswa Terautentikasi
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [MahasiswaController::class, 'dashboard'])->name('mahasiswa.dashboard');
    Route::get('/riwayat', [MahasiswaController::class, 'riwayat'])->name('mahasiswa.riwayat');
});

// Rute Petugas Verifikasi
Route::middleware(['auth', 'role:petugas,admin'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/', [PetugasController::class, 'dashboard'])->name('dashboard');
    Route::get('/pengajuan', [PetugasController::class, 'daftarPengajuan'])->name('pengajuan.index');
    Route::get('/pengajuan/{id}', [PetugasController::class, 'show'])->name('show');
    Route::post('/pengajuan/{id}/status', [PetugasController::class, 'updateStatus'])->name('status.update');
    Route::post('/dokumen/{id}/verifikasi', [PetugasController::class, 'verifikasiDokumen'])->name('dokumen.verifikasi');
});

// Rute Admin Pengelola Sistem
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // Manajemen Layanan
    Route::get('/layanan', [AdminController::class, 'layananIndex'])->name('layanan.index');
    Route::get('/layanan/create', [AdminController::class, 'layananCreate'])->name('layanan.create');
    Route::post('/layanan', [AdminController::class, 'layananStore'])->name('layanan.store');
    Route::get('/layanan/{id}/edit', [AdminController::class, 'layananEdit'])->name('layanan.edit');
    Route::put('/layanan/{id}', [AdminController::class, 'layananUpdate'])->name('layanan.update');
    Route::delete('/layanan/{id}', [AdminController::class, 'layananDestroy'])->name('layanan.destroy');

    // Manajemen Persyaratan
    Route::post('/layanan/{id}/persyaratan', [AdminController::class, 'persyaratanStore'])->name('persyaratan.store');
    Route::delete('/persyaratan/{id}', [AdminController::class, 'persyaratanDestroy'])->name('persyaratan.destroy');

    // Manajemen Pengguna
    Route::get('/users', [AdminController::class, 'usersIndex'])->name('users.index');
    Route::post('/users', [AdminController::class, 'userStore'])->name('users.store');

    // Health Check Sistem A
    Route::get('/sistem-a-check', [AdminController::class, 'checkSistemA'])->name('sistem_a.check');
});

// Rute Semantic Web (RDF, OWL & SPARQL Explorer)
Route::get('/semantik', [SemanticWebController::class, 'explorer'])->name('semantik.explorer');
Route::get('/api/rdf/pengajuan/{nomor_tiket}', [SemanticWebController::class, 'exportPengajuanRdf'])->name('api.rdf.pengajuan');
Route::get('/api/rdf/graph', [SemanticWebController::class, 'exportGraphRdf'])->name('api.rdf.graph');
Route::get('/api/rdf/ontology', [SemanticWebController::class, 'exportOntology'])->name('api.rdf.ontology');
Route::post('/api/rdf/sparql', [SemanticWebController::class, 'sparqlQuery'])->name('api.rdf.sparql');
