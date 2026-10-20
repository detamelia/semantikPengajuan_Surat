<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenPengajuan extends Model
{
    use HasFactory;

    public const RESOURCE_BASE_URI = 'http://example.org/resource/dokumen/';

    protected $fillable = [
        'pengajuan_id',
        'persyaratan_id',
        'nama_file_asli',
        'file_path',
        'mime_type',
        'file_size_kb',
        'status_verifikasi',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'file_size_kb' => 'integer',
        ];
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function persyaratan(): BelongsTo
    {
        return $this->belongsTo(Persyaratan::class);
    }

    public function getSemanticUriAttribute(): string
    {
        return self::RESOURCE_BASE_URI.$this->id;
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }
}
