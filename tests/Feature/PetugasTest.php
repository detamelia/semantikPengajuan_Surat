<?php

namespace Tests\Feature;

use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetugasTest extends TestCase
{
    use RefreshDatabase;

    private User $petugas;

    private Pengajuan $pengajuan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->petugas = User::create([
            'name' => 'Siti Nurhayati',
            'email' => 'petugas@silam.ac.id',
            'password' => bcrypt('password'),
            'role' => User::ROLE_PETUGAS,
        ]);

        $layanan = Layanan::create([
            'kode' => 'SK-AKTIF',
            'nama' => 'Surat Keterangan Aktif Kuliah',
            'deskripsi' => 'Surat aktif kuliah',
            'alur_pengajuan' => 'Alur pengajuan',
            'estimasi_hari' => 1,
            'icon' => 'academic-cap',
            'is_aktif' => true,
        ]);

        $this->pengajuan = Pengajuan::create([
            'nomor_tiket' => 'SILAM-2025-0421',
            'layanan_id' => $layanan->id,
            'npm' => '2215011045',
            'nama_mahasiswa' => 'Dina Rahmawati',
            'prodi' => 'Teknik Elektro',
            'angkatan' => '2022',
            'email' => 'dina.r@students.unila.ac.id',
            'no_hp' => '081234567811',
            'keperluan' => 'Persyaratan pengajuan beasiswa prestasi',
            'status' => Pengajuan::STATUS_MENUNGGU,
            'submitted_at' => now(),
        ]);
    }

    public function test_petugas_dashboard_renders_stats_and_table(): void
    {
        $response = $this->actingAs($this->petugas)->get('/petugas');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Petugas');
        $response->assertSee('Selamat datang, Siti. Kelola pengajuan mahasiswa hari ini.');
        $response->assertSee('Pengajuan Masuk');
        $response->assertSee('Perlu Pemeriksaan');
        $response->assertSee('Perlu Revisi');
        $response->assertSee('Selesai');
        $response->assertSee('Daftar Pengajuan Terbaru');
        $response->assertSee('SILAM-2025-0421');
        $response->assertSee('Dina Rahmawati');
        $response->assertSee('Teknik Elektro');
        $response->assertSee('Menunggu Pemeriksaan');
    }

    public function test_petugas_daftar_pengajuan_renders(): void
    {
        $response = $this->actingAs($this->petugas)->get('/petugas/pengajuan');

        $response->assertStatus(200);
        $response->assertSee('Daftar Pengajuan');
        $response->assertSee('Periksa dan tindak lanjuti pengajuan mahasiswa.');
        $response->assertSee('SILAM-2025-0421');
        $response->assertSee('Dina Rahmawati');
    }

    public function test_petugas_show_pemeriksaan_pengajuan(): void
    {
        $response = $this->actingAs($this->petugas)->get('/petugas/pengajuan/SILAM-2025-0421');

        $response->assertStatus(200);
        $response->assertSee('Pemeriksaan Pengajuan');
        $response->assertSee('SILAM-2025-0421');
        $response->assertSee('Data Mahasiswa');
        $response->assertSee('Dina Rahmawati');
        $response->assertSee('2215011045');
        $response->assertSee('Teknik Elektro');
        $response->assertSee('Data Pengajuan');
        $response->assertSee('Surat Keterangan Aktif Kuliah');
        $response->assertSee('Persyaratan pengajuan beasiswa prestasi');
        $response->assertSee('Dokumen yang Diunggah');
        $response->assertSee('Tindakan Pemeriksaan');
        $response->assertSee('Setujui Pengajuan');
        $response->assertSee('Perlu Revisi');
    }

    public function test_petugas_can_setujui_pengajuan(): void
    {
        $response = $this->actingAs($this->petugas)->post('/petugas/pengajuan/SILAM-2025-0421/status', [
            'action' => 'setujui',
        ]);

        $response->assertRedirect('/petugas/pengajuan/SILAM-2025-0421');
        $this->assertDatabaseHas('pengajuans', [
            'nomor_tiket' => 'SILAM-2025-0421',
            'status' => Pengajuan::STATUS_DISETUJUI,
        ]);

        $followUp = $this->actingAs($this->petugas)->get('/petugas/pengajuan/SILAM-2025-0421');
        $followUp->assertSee('Disetujui');
        $followUp->assertSee('Pengajuan telah disetujui. Surat akan diterbitkan dengan TTE dan QR Code.');
    }

    public function test_petugas_can_request_revisi(): void
    {
        $response = $this->actingAs($this->petugas)->post('/petugas/pengajuan/SILAM-2025-0421/status', [
            'action' => 'revisi',
            'catatan' => 'Mohon scan KTM ulang yang lebih jelas.',
        ]);

        $response->assertRedirect('/petugas/pengajuan/SILAM-2025-0421');
        $this->assertDatabaseHas('pengajuans', [
            'nomor_tiket' => 'SILAM-2025-0421',
            'status' => Pengajuan::STATUS_PERLU_PERBAIKAN,
            'catatan_petugas' => 'Mohon scan KTM ulang yang lebih jelas.',
        ]);
    }

    public function test_petugas_can_show_pengajuan_by_string_and_numeric_id(): void
    {
        // Test with string ticket code
        $responseString = $this->actingAs($this->petugas)->get('/petugas/pengajuan/'.$this->pengajuan->nomor_tiket);
        $responseString->assertStatus(200);
        $responseString->assertSee('Pemeriksaan Pengajuan');
        $responseString->assertSee($this->pengajuan->nomor_tiket);

        // Test with numeric id
        $responseNumeric = $this->actingAs($this->petugas)->get('/petugas/pengajuan/'.$this->pengajuan->id);
        $responseNumeric->assertStatus(200);
        $responseNumeric->assertSee('Pemeriksaan Pengajuan');
        $responseNumeric->assertSee($this->pengajuan->nomor_tiket);
    }
}
