<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dokumen_pengajuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuans')->cascadeOnDelete();
            $table->foreignId('persyaratan_id')->constrained('persyaratans')->cascadeOnDelete();
            $table->string('nama_file_asli', 255);
            $table->string('file_path', 500);
            $table->string('mime_type', 100);
            $table->integer('file_size_kb');
            $table->string('status_verifikasi', 50)->default('Menunggu'); // Menunggu, Valid, Perlu Perbaikan, Ditolak
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_pengajuans');
    }
};
