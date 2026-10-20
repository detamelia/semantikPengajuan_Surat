<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengajuan extends Model
{
    use HasFactory;

    public const ONTOLOGY_NAMESPACE = 'http://example.org/ontology/administrasi#';

    public const RESOURCE_BASE_URI = 'http://example.org/resource/pengajuan/';

    // Status Constants
    public const STATUS_MENUNGGU = 'Menunggu Verifikasi';

    public const STATUS_DIPROSES = 'Diproses';

    public const STATUS_PERLU_PERBAIKAN = 'Perlu Perbaikan';

    public const STATUS_DISETUJUI = 'Disetujui';

    public const STATUS_SELESAI = 'Selesai';

    public const STATUS_DITOLAK = 'Ditolak';

    public const ALL_STATUSES = [
        self::STATUS_MENUNGGU,
        self::STATUS_DIPROSES,
        self::STATUS_PERLU_PERBAIKAN,
        self::STATUS_DISETUJUI,
        self::STATUS_SELESAI,
        self::STATUS_DITOLAK,
    ];

    protected $fillable = [
        'nomor_tiket',
        'layanan_id',
        'user_id',
        'npm',
        'nama_mahasiswa',
        'prodi',
        'angkatan',
        'email',
        'no_hp',
        'keperluan',
        'uri_semantik_mahasiswa',
        'status',
        'catatan_petugas',
        'petugas_id',
        'file_surat_hasil',
        'submitted_at',
        'processed_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'processed_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function dokumens(): HasMany
    {
        return $this->hasMany(DokumenPengajuan::class);
    }

    public function riwayats(): HasMany
    {
        return $this->hasMany(RiwayatStatus::class)->orderBy('created_at', 'desc');
    }

    public function getSemanticUriAttribute(): string
    {
        return self::RESOURCE_BASE_URI.$this->nomor_tiket;
    }

    public function getMahasiswaSemanticUri(): string
    {
        if (! empty($this->uri_semantik_mahasiswa)) {
            return $this->uri_semantik_mahasiswa;
        }

        return 'http://example.org/resource/mahasiswa/'.$this->npm;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_MENUNGGU => 'badge-amber',
            self::STATUS_DIPROSES => 'badge-blue',
            self::STATUS_PERLU_PERBAIKAN => 'badge-orange',
            self::STATUS_DISETUJUI => 'badge-indigo',
            self::STATUS_SELESAI => 'badge-emerald',
            self::STATUS_DITOLAK => 'badge-rose',
            default => 'badge-slate',
        };
    }

    /**
     * Menghasilkan representasi RDF Turtle individual untuk pengajuan ini,
     * terhubung secara semantik dengan data master mahasiswa Sistem A.
     */
    public function toRdfTurtle(): string
    {
        $mhsUri = $this->getMahasiswaSemanticUri();
        $pengajuanUri = $this->semantic_uri;
        $layananUri = $this->layanan ? $this->layanan->semantic_uri : 'http://example.org/resource/layanan/'.$this->layanan_id;
        $submittedIso = $this->submitted_at ? $this->submitted_at->toIso8601String() : now()->toIso8601String();
        $safeStatus = addslashes($this->status);
        $safeCatatan = addslashes($this->catatan_petugas ?? '-');
        $safeKeperluan = addslashes($this->keperluan ?? '-');
        $safeTiket = addslashes($this->nomor_tiket);

        $ttl = "@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .\n".
               "@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .\n".
               "@prefix owl: <http://www.w3.org/2002/07/owl#> .\n".
               "@prefix xsd: <http://www.w3.org/2001/XMLSchema#> .\n".
               "@prefix mhs: <http://example.org/ontology/mahasiswa#> .\n".
               "@prefix silam: <http://example.org/ontology/administrasi#> .\n".
               "@prefix res_mhs: <http://example.org/resource/mahasiswa/> .\n".
               "@prefix res_silam: <http://example.org/resource/pengajuan/> .\n\n";

        $ttl .= "# Individual Mahasiswa (Referensi dari Sistem A)\n";
        $ttl .= "<{$mhsUri}>\n";
        $ttl .= "    a mhs:Mahasiswa ;\n";
        $ttl .= '    mhs:npm "'.addslashes($this->npm)."\"^^xsd:string ;\n";
        $ttl .= '    mhs:namaMahasiswa "'.addslashes($this->nama_mahasiswa)."\"^^xsd:string ;\n";
        $ttl .= '    mhs:programStudi "'.addslashes($this->prodi)."\"^^xsd:string ;\n";
        $ttl .= '    mhs:angkatan "'.addslashes($this->angkatan)."\"^^xsd:gYear .\n\n";

        if ($this->layanan) {
            $ttl .= "# Layanan Administrasi Terkait\n";
            $ttl .= "<{$layananUri}>\n";
            $ttl .= "    a silam:LayananAdministrasi ;\n";
            $ttl .= '    silam:kodeLayanan "'.addslashes($this->layanan->kode)."\"^^xsd:string ;\n";
            $ttl .= '    silam:namaLayanan "'.addslashes($this->layanan->nama)."\"^^xsd:string .\n\n";
        }

        $ttl .= "# Individual Pengajuan Surat (Sistem B)\n";
        $ttl .= "<{$pengajuanUri}>\n";
        $ttl .= "    a silam:PengajuanSurat ;\n";
        $ttl .= "    silam:nomorTiket \"{$safeTiket}\"^^xsd:string ;\n";
        $ttl .= "    silam:diajukanOleh <{$mhsUri}> ;\n";
        $ttl .= "    silam:untukLayanan <{$layananUri}> ;\n";
        $ttl .= '    silam:memilikiStatus "'.$safeStatus."\"^^xsd:string ;\n";
        $ttl .= "    silam:tanggalPengajuan \"{$submittedIso}\"^^xsd:dateTime ;\n";
        $ttl .= "    silam:keperluan \"{$safeKeperluan}\"^^xsd:string ;\n";
        $ttl .= "    silam:catatanPetugas \"{$safeCatatan}\"^^xsd:string ;\n";

        if ($this->dokumens && $this->dokumens->count() > 0) {
            foreach ($this->dokumens as $dok) {
                $dokUri = $dok->semantic_uri;
                $ttl .= "    silam:melampirkanDokumen <{$dokUri}> ;\n";
            }
        }

        $ttl = rtrim($ttl, " ;\n")." .\n\n";

        // Tambahkan individual dokumen jika ada
        if ($this->dokumens && $this->dokumens->count() > 0) {
            $ttl .= "# Dokumen Persyaratan yang Dilampirkan\n";
            foreach ($this->dokumens as $dok) {
                $ttl .= "<{$dok->semantic_uri}>\n";
                $ttl .= "    a silam:DokumenPersyaratan ;\n";
                $ttl .= '    silam:namaDokumen "'.addslashes($dok->nama_file_asli)."\"^^xsd:string ;\n";
                $ttl .= '    silam:statusVerifikasi "'.addslashes($dok->status_verifikasi)."\"^^xsd:string ;\n";
                $ttl .= '    silam:ukuranKb '.intval($dok->file_size_kb)." .\n\n";
            }
        }

        return $ttl;
    }
}
