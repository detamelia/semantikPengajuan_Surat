<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Layanan extends Model
{
    use HasFactory;

    public const ONTOLOGY_NAMESPACE = 'http://example.org/ontology/administrasi#';

    public const RESOURCE_BASE_URI = 'http://example.org/resource/layanan/';

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'alur_pengajuan',
        'estimasi_hari',
        'icon',
        'is_aktif',
    ];

    protected function casts(): array
    {
        return [
            'is_aktif' => 'boolean',
            'estimasi_hari' => 'integer',
        ];
    }

    public function persyaratans(): HasMany
    {
        return $this->hasMany(Persyaratan::class)->orderBy('urutan', 'asc');
    }

    public function pengajuans(): HasMany
    {
        return $this->hasMany(Pengajuan::class);
    }

    public function getSemanticUriAttribute(): string
    {
        return self::RESOURCE_BASE_URI.strtolower($this->kode);
    }
}
