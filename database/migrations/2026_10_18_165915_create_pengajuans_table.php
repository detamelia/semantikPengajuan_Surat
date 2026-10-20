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
        Schema::create('pengajuans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tiket', 50)->unique()->index();
            $table->foreignId('layanan_id')->constrained('layanans')->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('npm', 30)->index();
            $table->string('nama_mahasiswa', 255);
            $table->string('prodi', 100);
            $table->string('angkatan', 10);
            $table->string('email', 100);
            $table->string('no_hp', 30);
            $table->text('keperluan');
            $table->string('uri_semantik_mahasiswa', 500)->nullable();
            $table->string('status', 50)->default('Menunggu Verifikasi')->index();
            $table->text('catatan_petugas')->nullable();
            $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('file_surat_hasil', 500)->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuans');
    }
};
