<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Persyaratan extends Model
{
    use HasFactory;

    public const RESOURCE_BASE_URI = 'http://example.org/resource/persyaratan/';

    protected $fillable = [
        'layanan_id',
        'nama_persyaratan',
        'deskripsi',
        'tipe_file',
        'max_size_kb',
        'is_wajib',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'is_wajib' => 'boolean',
            'max_size_kb' => 'integer',
            'urutan' => 'integer',
        ];
    }

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class);
    }

    public function dokumenPengajuans(): HasMany
    {
        return $this->hasMany(DokumenPengajuan::class);
    }

    public function getSemanticUriAttribute(): string
    {
        return self::RESOURCE_BASE_URI.$this->id;
    }
}
