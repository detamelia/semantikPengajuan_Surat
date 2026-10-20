<?php

namespace App\Services;

use App\Models\Layanan;
use App\Models\Pengajuan;

class SemanticWebService
{
    /**
     * Menghasilkan RDF Turtle untuk satu pengajuan spesifik.
     */
    public function generatePengajuanRdf(Pengajuan $pengajuan): string
    {
        return $pengajuan->toRdfTurtle();
    }

    /**
     * Menghasilkan graf RDF Turtle lengkap dari seluruh data SILAM dan referensi Sistem A.
     */
    public function generateFullGraphRdf(): string
    {
        $ttl = "@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .\n".
               "@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .\n".
               "@prefix owl: <http://www.w3.org/2002/07/owl#> .\n".
               "@prefix xsd: <http://www.w3.org/2001/XMLSchema#> .\n".
               "@prefix mhs: <http://example.org/ontology/mahasiswa#> .\n".
               "@prefix silam: <http://example.org/ontology/administrasi#> .\n".
               "@prefix res_mhs: <http://example.org/resource/mahasiswa/> .\n".
               "@prefix res_layanan: <http://example.org/resource/layanan/> .\n".
               "@prefix res_pengajuan: <http://example.org/resource/pengajuan/> .\n".
               "@prefix res_dokumen: <http://example.org/resource/dokumen/> .\n\n";

        $ttl .= "# ==============================================================================\n";
        $ttl .= "# GRAF RDF TERINTEGRASI: SILAM (SISTEM B) & SISTEM INFORMASI DATA MAHASISWA (A)\n";
        $ttl .= '# Dihasilkan secara dinamis pada '.now()->toIso8601String()."\n";
        $ttl .= "# ==============================================================================\n\n";

        // 1. Ekspor Master Layanan Administrasi
        $layanans = Layanan::all();
        $ttl .= "# KATALOG LAYANAN ADMINISTRASI\n";
        foreach ($layanans as $layanan) {
            $ttl .= "<{$layanan->semantic_uri}>\n".
                   "    a silam:LayananAdministrasi ;\n".
                   '    silam:kodeLayanan "'.addslashes($layanan->kode)."\"^^xsd:string ;\n".
                   '    silam:namaLayanan "'.addslashes($layanan->nama)."\"^^xsd:string ;\n".
                   '    rdfs:label "'.addslashes($layanan->nama)."\" .\n\n";
        }

        // 2. Ekspor Seluruh Pengajuan Surat & Relasi Mahasiswa
        $pengajuans = Pengajuan::with(['layanan', 'dokumens'])->orderBy('submitted_at', 'desc')->get();
        $uniqueMahasiswa = [];

        foreach ($pengajuans as $p) {
            $mhsUri = $p->getMahasiswaSemanticUri();
            if (! isset($uniqueMahasiswa[$mhsUri])) {
                $uniqueMahasiswa[$mhsUri] = [
                    'npm' => $p->npm,
                    'nama' => $p->nama_mahasiswa,
                    'prodi' => $p->prodi,
                    'angkatan' => $p->angkatan,
                    'uri' => $mhsUri,
                ];
            }
        }

        $ttl .= "# INDIVIDUAL MAHASISWA (REFERENSI SISTEM A DEREFERENCED)\n";
        foreach ($uniqueMahasiswa as $m) {
            $ttl .= "<{$m['uri']}>\n".
                   "    a mhs:Mahasiswa ;\n".
                   '    mhs:npm "'.addslashes($m['npm'])."\"^^xsd:string ;\n".
                   '    mhs:namaMahasiswa "'.addslashes($m['nama'])."\"^^xsd:string ;\n".
                   '    mhs:programStudi "'.addslashes($m['prodi'])."\"^^xsd:string ;\n".
                   '    mhs:angkatan "'.addslashes($m['angkatan'])."\"^^xsd:gYear ;\n".
                   '    rdfs:label "'.addslashes($m['nama'])."\" .\n\n";
        }

        $ttl .= "# TRANSAKSI PENGAJUAN SURAT (SISTEM B)\n";
        foreach ($pengajuans as $p) {
            $pUri = $p->semantic_uri;
            $mhsUri = $p->getMahasiswaSemanticUri();
            $layananUri = $p->layanan ? $p->layanan->semantic_uri : 'http://example.org/resource/layanan/'.$p->layanan_id;
            $subIso = $p->submitted_at ? $p->submitted_at->toIso8601String() : now()->toIso8601String();

            $ttl .= "<{$pUri}>\n".
                   "    a silam:PengajuanSurat ;\n".
                   '    silam:nomorTiket "'.addslashes($p->nomor_tiket)."\"^^xsd:string ;\n".
                   "    silam:diajukanOleh <{$mhsUri}> ;\n".
                   "    silam:untukLayanan <{$layananUri}> ;\n".
                   '    silam:memilikiStatus "'.addslashes($p->status)."\"^^xsd:string ;\n".
                   "    silam:tanggalPengajuan \"{$subIso}\"^^xsd:dateTime ;\n".
                   '    silam:keperluan "'.addslashes($p->keperluan ?? '-')."\"^^xsd:string ;\n".
                   '    silam:catatanPetugas "'.addslashes($p->catatan_petugas ?? '-')."\"^^xsd:string ;\n";

            if ($p->dokumens && $p->dokumens->count() > 0) {
                foreach ($p->dokumens as $dok) {
                    $ttl .= "    silam:melampirkanDokumen <{$dok->semantic_uri}> ;\n";
                }
            }
            $ttl = rtrim($ttl, " ;\n")." .\n\n";

            // Dokumen
            if ($p->dokumens && $p->dokumens->count() > 0) {
                foreach ($p->dokumens as $dok) {
                    $ttl .= "<{$dok->semantic_uri}>\n".
                           "    a silam:DokumenPersyaratan ;\n".
                           '    silam:namaDokumen "'.addslashes($dok->nama_file_asli)."\"^^xsd:string ;\n".
                           '    silam:statusVerifikasi "'.addslashes($dok->status_verifikasi)."\"^^xsd:string ;\n".
                           '    silam:ukuranKb '.intval($dok->file_size_kb)." .\n\n";
                }
            }
        }

        return $ttl;
    }

    /**
     * Membaca isi berkas OWL ontology.
     */
    public function getOntologyOwl(): string
    {
        $path = base_path('ontology/silam.owl');
        if (file_exists($path)) {
            return file_get_contents($path);
        }

        return '';
    }

    /**
     * Membaca isi berkas Turtle ontology.
     */
    public function getOntologyTurtle(): string
    {
        $path = base_path('ontology/silam.ttl');
        if (file_exists($path)) {
            return file_get_contents($path);
        }

        return '';
    }

    /**
     * Eksekusi simulasi SPARQL untuk query visual pada browser.
     */
    public function executeSparqlQuery(string $queryCode): array
    {
        // Menyediakan hasil kueri interaktif berdasarkan query code
        $pengajuans = Pengajuan::with(['layanan', 'dokumens'])->orderBy('submitted_at', 'desc')->get();

        $rows = [];
        foreach ($pengajuans as $p) {
            $rows[] = [
                'tiket' => $p->nomor_tiket,
                'pengajuan_uri' => $p->semantic_uri,
                'npm' => $p->npm,
                'namaMahasiswa' => $p->nama_mahasiswa,
                'prodi' => $p->prodi,
                'angkatan' => $p->angkatan,
                'mhs_uri' => $p->getMahasiswaSemanticUri(),
                'kodeLayanan' => $p->layanan ? $p->layanan->kode : '-',
                'namaLayanan' => $p->layanan ? $p->layanan->nama : '-',
                'status' => $p->status,
                'tanggal' => $p->submitted_at ? $p->submitted_at->format('Y-m-d H:i') : '-',
                'catatan' => $p->catatan_petugas ?? '-',
                'jumlahDokumen' => $p->dokumens->count(),
            ];
        }

        return $rows;
    }
}
