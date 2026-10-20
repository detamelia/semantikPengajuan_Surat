<?php

namespace Tests\Feature;

use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Persyaratan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SilamFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed default service
        $layanan = Layanan::create([
            'kode' => 'SK-AKTIF',
            'nama' => 'Surat Keterangan Aktif Kuliah',
            'deskripsi' => 'Surat keterangan mahasiswa aktif.',
            'alur_pengajuan' => 'Isi formulir dan tunggu verifikasi.',
            'estimasi_hari' => 1,
            'is_aktif' => true,
        ]);

        Persyaratan::create([
            'layanan_id' => $layanan->id,
            'nama_persyaratan' => 'KTM Mahasiswa',
            'tipe_file' => 'pdf,jpg,png',
            'max_size_kb' => 2048,
            'is_wajib' => true,
            'urutan' => 1,
        ]);
    }

    public function test_katalog_layanan_page_is_accessible(): void
    {
        $response = $this->get('/layanan');
        $response->assertStatus(200);
        $response->assertSee('Surat Keterangan Aktif Kuliah');
    }

    public function test_api_lookup_mahasiswa_success(): void
    {
        Http::fake([
            '*/api/mahasiswa/22081010001' => Http::response([
                'success' => true,
                'data' => [
                    'npm' => '22081010001',
                    'nama' => 'Fajar Pratama',
                    'prodi' => 'Informatika',
                    'angkatan' => '2022',
                    'uri_semantik' => 'http://example.org/resource/mahasiswa/22081010001',
                ],
            ], 200),
        ]);

        $response = $this->getJson('/api/lookup-mhs/22081010001');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.npm', '22081010001')
            ->assertJsonPath('data.nama', 'Fajar Pratama');
    }

    public function test_api_lookup_mahasiswa_not_found(): void
    {
        Http::fake([
            '*/api/mahasiswa/99999999' => Http::response([
                'success' => false,
                'error_code' => 'MAHASISWA_NOT_FOUND',
                'message' => 'Mahasiswa tidak ditemukan',
            ], 404),
        ]);

        $response = $this->getJson('/api/lookup-mhs/99999999');
        $response->assertStatus(404)
            ->assertJsonPath('success', false);
    }

    public function test_mahasiswa_can_submit_pengajuan_surat(): void
    {
        Storage::fake('public');

        Http::fake([
            '*/api/mahasiswa/22081010001' => Http::response([
                'success' => true,
                'data' => [
                    'npm' => '22081010001',
                    'nama' => 'Fajar Pratama',
                    'prodi' => 'Informatika',
                    'angkatan' => '2022',
                    'uri_semantik' => 'http://example.org/resource/mahasiswa/22081010001',
                ],
            ], 200),
        ]);

        $layanan = Layanan::first();
        $persyaratan = $layanan->persyaratans->first();

        $file = UploadedFile::fake()->create('ktm_fajar.pdf', 500, 'application/pdf');

        $response = $this->post('/pengajuan', [
            'layanan_id' => $layanan->id,
            'npm' => '22081010001',
            'email' => 'fajar@mhs.ac.id',
            'no_hp' => '081234567890',
            'keperluan' => 'Untuk perpanjangan BPJS Kesehatan keluarga.',
            'dokumen' => [
                $persyaratan->id => $file,
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pengajuans', [
            'npm' => '22081010001',
            'nama_mahasiswa' => 'Fajar Pratama',
            'status' => Pengajuan::STATUS_MENUNGGU,
        ]);
    }

    public function test_lacak_pengajuan_by_ticket(): void
    {
        $layanan = Layanan::first();
        $pengajuan = Pengajuan::create([
            'nomor_tiket' => 'SILAM-202610-TEST1',
            'layanan_id' => $layanan->id,
            'npm' => '22081010001',
            'nama_mahasiswa' => 'Fajar Pratama',
            'prodi' => 'Informatika',
            'angkatan' => '2022',
            'email' => 'fajar@mhs.ac.id',
            'no_hp' => '081234567890',
            'keperluan' => 'Keperluan pengujian tiket.',
            'status' => Pengajuan::STATUS_MENUNGGU,
            'submitted_at' => now(),
        ]);

        $user = User::create([
            'name' => 'Fajar Pratama',
            'email' => 'fajar@mhs.ac.id',
            'password' => bcrypt('password'),
            'role' => User::ROLE_MAHASISWA,
            'npm' => '22081010001',
        ]);

        $response = $this->actingAs($user)->get('/lacak?tiket='.$pengajuan->nomor_tiket);
        $response->assertStatus(200);
        $response->assertSee('SILAM-202610-TEST1');
        $response->assertSee('Fajar Pratama');
    }

    public function test_petugas_can_update_status(): void
    {
        $petugas = User::create([
            'name' => 'Petugas Akademik',
            'email' => 'petugas@silam.ac.id',
            'password' => bcrypt('password'),
            'role' => User::ROLE_PETUGAS,
        ]);

        $layanan = Layanan::first();
        $pengajuan = Pengajuan::create([
            'nomor_tiket' => 'SILAM-202610-TEST2',
            'layanan_id' => $layanan->id,
            'npm' => '22081010001',
            'nama_mahasiswa' => 'Fajar Pratama',
            'prodi' => 'Informatika',
            'angkatan' => '2022',
            'email' => 'fajar@mhs.ac.id',
            'no_hp' => '081234567890',
            'keperluan' => 'Keperluan verifikasi status.',
            'status' => Pengajuan::STATUS_MENUNGGU,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($petugas)->post("/petugas/pengajuan/{$pengajuan->id}/status", [
            'status' => Pengajuan::STATUS_DIPROSES,
            'catatan' => 'Dokumen sedang diproses verifikator.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pengajuans', [
            'id' => $pengajuan->id,
            'status' => Pengajuan::STATUS_DIPROSES,
        ]);
        $this->assertDatabaseHas('riwayat_statuses', [
            'pengajuan_id' => $pengajuan->id,
            'status_baru' => Pengajuan::STATUS_DIPROSES,
        ]);
    }
}
