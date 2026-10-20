<?php

namespace Tests\Feature;

use App\Models\Layanan;
use App\Models\Pengajuan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SemanticWebTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $layanan = Layanan::create([
            'kode' => 'SK-AKTIF',
            'nama' => 'Surat Keterangan Aktif Kuliah',
            'deskripsi' => 'Surat keterangan mahasiswa aktif.',
            'alur_pengajuan' => 'Alur verifikasi.',
            'estimasi_hari' => 1,
            'is_aktif' => true,
        ]);

        Pengajuan::create([
            'nomor_tiket' => 'SILAM-202610-RDF1',
            'layanan_id' => $layanan->id,
            'npm' => '22081010001',
            'nama_mahasiswa' => 'Fajar Pratama',
            'prodi' => 'Informatika',
            'angkatan' => '2022',
            'email' => 'fajar@mhs.ac.id',
            'no_hp' => '081234567890',
            'keperluan' => 'Pengujian integrasi semantic web.',
            'uri_semantik_mahasiswa' => 'http://example.org/resource/mahasiswa/22081010001',
            'status' => Pengajuan::STATUS_SELESAI,
            'submitted_at' => now(),
        ]);
    }

    public function test_export_pengajuan_rdf_turtle(): void
    {
        $response = $this->get('/api/rdf/pengajuan/SILAM-202610-RDF1');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/turtle; charset=UTF-8');

        $content = $response->getContent();

        // Verifikasi keberadaan triples semantik kunci
        $this->assertStringContainsString('@prefix silam: <http://example.org/ontology/administrasi#>', $content);
        $this->assertStringContainsString('@prefix mhs: <http://example.org/ontology/mahasiswa#>', $content);
        $this->assertStringContainsString('a silam:PengajuanSurat', $content);
        $this->assertStringContainsString('silam:diajukanOleh <http://example.org/resource/mahasiswa/22081010001>', $content);
        $this->assertStringContainsString('silam:nomorTiket "SILAM-202610-RDF1"', $content);
        $this->assertStringContainsString('silam:memilikiStatus "Selesai"', $content);
    }

    public function test_export_full_graph_rdf(): void
    {
        $response = $this->get('/api/rdf/graph');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/turtle; charset=UTF-8');

        $content = $response->getContent();
        $this->assertStringContainsString('a silam:LayananAdministrasi', $content);
        $this->assertStringContainsString('a silam:PengajuanSurat', $content);
        $this->assertStringContainsString('a mhs:Mahasiswa', $content);
    }

    public function test_export_owl_ontology(): void
    {
        $response = $this->get('/api/rdf/ontology?format=owl');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/rdf+xml; charset=UTF-8');

        $content = $response->getContent();
        $this->assertStringContainsString('<owl:Ontology', $content);
        $this->assertStringContainsString('http://example.org/ontology/administrasi#LayananAdministrasi', $content);
        $this->assertStringContainsString('http://example.org/ontology/administrasi#diajukanOleh', $content);
    }

    public function test_sparql_query_endpoint(): void
    {
        $response = $this->postJson('/api/rdf/sparql', [
            'query_code' => 'ALL_PENGAJUAN',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('count', 1)
            ->assertJsonPath('results.0.tiket', 'SILAM-202610-RDF1')
            ->assertJsonPath('results.0.mhs_uri', 'http://example.org/resource/mahasiswa/22081010001');
    }
}
