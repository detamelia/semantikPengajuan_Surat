<?php

namespace Database\Seeders;

use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Persyaratan;
use App\Models\RiwayatStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with master data and sample records.
     */
    public function run(): void
    {
        // 1. Buat Pengguna (Admin, Petugas, Mahasiswa)
        $admin = User::firstOrCreate(
            ['email' => 'admin@silam.ac.id'],
            [
                'name' => 'Administrator SILAM',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'phone' => '081234567890',
            ]
        );

        $petugas = User::where('role', User::ROLE_PETUGAS)
            ->orWhere('email', 'petugas@silam.ac.id')
            ->first();

        if ($petugas) {
            $petugas->update([
                'name' => 'Siti Nurhayati',
                'email' => 'petugas@silam.ac.id',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PETUGAS,
            ]);
        } else {
            $petugas = User::create([
                'name' => 'Siti Nurhayati',
                'email' => 'petugas@silam.ac.id',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PETUGAS,
                'phone' => '081298765432',
            ]);
        }

        $mahasiswa = User::where('npm', '2415061120')
            ->orWhere('email', 'deta.amelia@mhs.ac.id')
            ->orWhere('email', 'mahasiswa@silam.ac.id')
            ->first();

        if ($mahasiswa) {
            $mahasiswa->update([
                'name' => 'Deta Amelia',
                'npm' => '2415061120',
                'email' => 'deta.amelia@mhs.ac.id',
                'role' => User::ROLE_MAHASISWA,
                'prodi' => 'Informatika',
                'phone' => '081234567890',
            ]);
        } else {
            $mahasiswa = User::create([
                'name' => 'Deta Amelia',
                'email' => 'deta.amelia@mhs.ac.id',
                'password' => Hash::make('password'),
                'role' => User::ROLE_MAHASISWA,
                'npm' => '2415061120',
                'prodi' => 'Informatika',
                'phone' => '081234567890',
            ]);
        }

        // 2. Buat Master Layanan dan Persyaratan
        $layananData = [
            [
                'kode' => 'SK-AKTIF',
                'nama' => 'Surat Keterangan Aktif Kuliah',
                'deskripsi' => 'Surat resmi yang menerangkan bahwa mahasiswa bersangkutan terdaftar aktif mengikuti perkuliahan pada semester berjalan, dapat digunakan untuk tunjangan gaji orang tua, BPJS, atau keperluan kedinasan.',
                'alur_pengajuan' => "1. Masukkan NPM untuk verifikasi data mahasiswa via Sistem A.\n2. Lengkapi nomor kontak dan rincian keperluan surat.\n3. Unggah berkas persyaratan (KTM, KRS, Bukti UKT).\n4. Petugas memeriksa dan memvalidasi berkas.\n5. Unduh surat resmi yang telah bertanda tangan digital.",
                'estimasi_hari' => 1,
                'icon' => 'academic-cap',
                'persyaratans' => [
                    ['nama' => 'Kartu Tanda Mahasiswa (KTM)', 'deskripsi' => 'Scan/Foto KTM yang masih berlaku', 'tipe' => 'pdf,jpg,jpeg,png', 'size' => 2048, 'wajib' => true, 'urutan' => 1],
                    ['nama' => 'Kartu Rencana Studi (KRS) Semester Berjalan', 'deskripsi' => 'KRS yang telah disetujui Dosen Pembimbing Akademik', 'tipe' => 'pdf,jpg,jpeg,png', 'size' => 2048, 'wajib' => true, 'urutan' => 2],
                    ['nama' => 'Bukti Pembayaran UKT / SPP Terakhir', 'deskripsi' => 'Slip setoran bank atau bukti transfer resmi', 'tipe' => 'pdf,jpg,jpeg,png', 'size' => 2048, 'wajib' => true, 'urutan' => 3],
                ],
            ],
            [
                'kode' => 'SR-BEASISWA',
                'nama' => 'Surat Rekomendasi Beasiswa',
                'deskripsi' => 'Surat rekomendasi resmi dari pimpinan fakultas/program studi untuk pengajuan beasiswa prestasi, KIP Kuliah, Bank Indonesia, Djarum, atau mitra beasiswa eksternal.',
                'alur_pengajuan' => "1. Masukkan NPM dan periksa sinkronisasi profil Sistem A.\n2. Masukkan nama beasiswa yang dituju pada kolom keperluan.\n3. Unggah berkas persyaratan (KTM, Transkrip, Slip Gaji).\n4. Verifikasi berkas oleh Petugas dan validasi Dekanat.\n5. Surat rekomendasi diterbitkan.",
                'estimasi_hari' => 2,
                'icon' => 'award',
                'persyaratans' => [
                    ['nama' => 'Kartu Tanda Mahasiswa (KTM)', 'deskripsi' => 'Scan kartu mahasiswa aktif', 'tipe' => 'pdf,jpg,jpeg,png', 'size' => 2048, 'wajib' => true, 'urutan' => 1],
                    ['nama' => 'Transkrip Nilai Sementara', 'deskripsi' => 'Transkrip nilai ber-IPK terbaru dari program studi', 'tipe' => 'pdf', 'size' => 3072, 'wajib' => true, 'urutan' => 2],
                    ['nama' => 'Surat Keterangan Penghasilan Orang Tua / Slip Gaji', 'deskripsi' => 'Bukti penghasilan orang tua dari instansi/kelurahan', 'tipe' => 'pdf,jpg,jpeg,png', 'size' => 2048, 'wajib' => true, 'urutan' => 3],
                    ['nama' => 'Sertifikat Prestasi (Opsional)', 'deskripsi' => 'Piagam lomba / kegiatan akademik pendukung', 'tipe' => 'pdf,jpg,jpeg,png', 'size' => 4096, 'wajib' => false, 'urutan' => 4],
                ],
            ],
            [
                'kode' => 'SK-MAGANG',
                'nama' => 'Surat Pengantar Magang / Kerja Praktik',
                'deskripsi' => 'Surat permohonan izin atau pengantar magang industri/instansi yang ditujukan kepada pimpinan instansi tempat mahasiswa akan melaksanakan Magang/Kerja Praktik.',
                'alur_pengajuan' => "1. Masukkan NPM dan data tujuan magang.\n2. Tuliskan nama instansi, divisi, dan periode magang pada keperluan.\n3. Unggah KRS dan proposal/kerangka acuan singkat.\n4. Petugas memeriksa kesesuaian persyaratan akademik.\n5. Surat pengantar magang resmi diterbitkan.",
                'estimasi_hari' => 2,
                'icon' => 'briefcase',
                'persyaratans' => [
                    ['nama' => 'Kartu Rencana Studi (KRS) Mata Kuliah Magang', 'deskripsi' => 'KRS memuat mata kuliah Kerja Praktik / Magang', 'tipe' => 'pdf,jpg,jpeg,png', 'size' => 2048, 'wajib' => true, 'urutan' => 1],
                    ['nama' => 'Proposal Singkat Magang / TOR', 'deskripsi' => 'Ringkasan rencana kegiatan dan profil tempat magang', 'tipe' => 'pdf', 'size' => 5120, 'wajib' => true, 'urutan' => 2],
                    ['nama' => 'Surat Penerimaan / Konfirmasi Mitra (Opsional)', 'deskripsi' => 'Jika mitra sudah memberikan surat balasan awal', 'tipe' => 'pdf,jpg,jpeg,png', 'size' => 3072, 'wajib' => false, 'urutan' => 3],
                ],
            ],
            [
                'kode' => 'SK-CUTI',
                'nama' => 'Surat Izin Cuti Akademik',
                'deskripsi' => 'Permohonan resmi berhenti kuliah sementara (cuti akademik) dengan alasan kesehatan, pekerjaan, atau keperluan keluarga.',
                'alur_pengajuan' => "1. Konsultasi terlebih dahulu dengan Dosen Wali.\n2. Isi permohonan melalui SILAM dengan melampirkan formulir persetujuan Dosen Wali.\n3. Unggah bukti bebas perpustakaan dan pernyataan orang tua.\n4. Verifikasi petugas dan penerbitan SK Cuti.",
                'estimasi_hari' => 3,
                'icon' => 'clock',
                'persyaratans' => [
                    ['nama' => 'Formulir Persetujuan Cuti dari Dosen Wali', 'deskripsi' => 'Formulir resmi bertanda tangan Dosen Pembimbing Akademik', 'tipe' => 'pdf,jpg,jpeg,png', 'size' => 2048, 'wajib' => true, 'urutan' => 1],
                    ['nama' => 'Surat Bebas Pustaka & Tanggungan Kampus', 'deskripsi' => 'Keterangan tidak memiliki tanggungan buku perpustakaan', 'tipe' => 'pdf,jpg,jpeg,png', 'size' => 2048, 'wajib' => true, 'urutan' => 2],
                    ['nama' => 'Surat Pernyataan Orang Tua / Wali', 'deskripsi' => 'Surat pernyataan bermaterai 10.000', 'tipe' => 'pdf,jpg,jpeg,png', 'size' => 3072, 'wajib' => true, 'urutan' => 3],
                ],
            ],
            [
                'kode' => 'SK-LULUS',
                'nama' => 'Surat Keterangan Lulus (SKL)',
                'deskripsi' => 'Surat keterangan sementara pengganti ijazah yang menerangkan bahwa mahasiswa telah dinyatakan lulus yudisium program sarjana/diploma.',
                'alur_pengajuan' => "1. Masukkan NPM mahasiswa yang telah dinyatakan lulus yudisium.\n2. Unggah berita acara yudisium dan bebas tanggungan.\n3. Petugas memverifikasi kelengkapan berkas kelulusan.\n4. Dekan/Wakil Dekan menyetujui dan SKL diterbitkan.",
                'estimasi_hari' => 2,
                'icon' => 'certificate',
                'persyaratans' => [
                    ['nama' => 'Berita Acara Yudisium / Kelulusan', 'deskripsi' => 'Dokumen resmi pengumuman hasil sidang kelulusan', 'tipe' => 'pdf,jpg,jpeg,png', 'size' => 3072, 'wajib' => true, 'urutan' => 1],
                    ['nama' => 'Surat Bebas Pinjaman Lab & Pustaka', 'deskripsi' => 'Keterangan bebas tanggungan dari laboratorium dan perpustakaan', 'tipe' => 'pdf,jpg,jpeg,png', 'size' => 2048, 'wajib' => true, 'urutan' => 2],
                    ['nama' => 'Pasfoto Resmi 4x6 Berjas', 'deskripsi' => 'Pasfoto terbaru dengan latar belakang merah format formal', 'tipe' => 'jpg,jpeg,png', 'size' => 2048, 'wajib' => true, 'urutan' => 3],
                ],
            ],
        ];

        foreach ($layananData as $item) {
            $persyaratans = $item['persyaratans'];
            unset($item['persyaratans']);

            $layanan = Layanan::updateOrCreate(['kode' => $item['kode']], $item);

            foreach ($persyaratans as $p) {
                Persyaratan::updateOrCreate(
                    [
                        'layanan_id' => $layanan->id,
                        'nama_persyaratan' => $p['nama'],
                    ],
                    [
                        'deskripsi' => $p['deskripsi'],
                        'tipe_file' => $p['tipe'],
                        'max_size_kb' => $p['size'],
                        'is_wajib' => $p['wajib'],
                        'urutan' => $p['urutan'],
                    ]
                );
            }
        }

        // 3. Buat Data Sampel Pengajuan Matching Specification Screenshot
        $layananAktif = Layanan::where('kode', 'SK-AKTIF')->first();
        $layananBeasiswa = Layanan::where('kode', 'SR-BEASISWA')->first();
        $layananMagang = Layanan::where('kode', 'SK-MAGANG')->first();

        // 3.1. SILAM-2025-0418: Surat Keterangan Aktif Kuliah (Status: Diproses)
        if ($layananAktif && ! Pengajuan::where('nomor_tiket', 'SILAM-2025-0418')->exists()) {
            $p0418 = Pengajuan::create([
                'nomor_tiket' => 'SILAM-2025-0418',
                'layanan_id' => $layananAktif->id,
                'user_id' => $mahasiswa->id,
                'npm' => '2415061120',
                'nama_mahasiswa' => 'Deta Amelia',
                'prodi' => 'Informatika',
                'angkatan' => '2024',
                'email' => 'deta.amelia@mhs.ac.id',
                'no_hp' => '081234567890',
                'keperluan' => 'Persyaratan administrasi pengajuan beasiswa dan tunjangan.',
                'uri_semantik_mahasiswa' => 'http://silam.ft.ac.id/resource/mahasiswa/2415061120',
                'status' => Pengajuan::STATUS_DIPROSES,
                'submitted_at' => '2025-06-18 09:24:00',
                'processed_at' => '2025-06-18 13:10:00',
            ]);

            RiwayatStatus::create([
                'pengajuan_id' => $p0418->id,
                'status_sebelumnya' => null,
                'status_baru' => Pengajuan::STATUS_MENUNGGU,
                'catatan' => 'Pengajuan dibuat oleh mahasiswa.',
                'actor_name' => 'Deta Amelia (Mahasiswa)',
                'created_at' => '2025-06-18 09:24:00',
            ]);

            RiwayatStatus::create([
                'pengajuan_id' => $p0418->id,
                'status_sebelumnya' => Pengajuan::STATUS_MENUNGGU,
                'status_baru' => Pengajuan::STATUS_DIPROSES,
                'catatan' => 'Pemeriksaan berkas persyaratan oleh petugas verifikasi.',
                'user_id' => $petugas->id,
                'actor_name' => $petugas->name,
                'created_at' => '2025-06-18 13:10:00',
            ]);
        }

        // 3.2. SILAM-2025-0392: Surat Rekomendasi Magang (Status: Perlu Revisi)
        if ($layananMagang && ! Pengajuan::where('nomor_tiket', 'SILAM-2025-0392')->exists()) {
            $p0392 = Pengajuan::create([
                'nomor_tiket' => 'SILAM-2025-0392',
                'layanan_id' => $layananMagang->id,
                'user_id' => $mahasiswa->id,
                'npm' => '2415061120',
                'nama_mahasiswa' => 'Deta Amelia',
                'prodi' => 'Informatika',
                'angkatan' => '2024',
                'email' => 'deta.amelia@mhs.ac.id',
                'no_hp' => '081234567890',
                'keperluan' => 'Permohonan rekomendasi kegiatan magang industri / kerja praktik.',
                'uri_semantik_mahasiswa' => 'http://silam.ft.ac.id/resource/mahasiswa/2415061120',
                'status' => Pengajuan::STATUS_PERLU_PERBAIKAN,
                'catatan_petugas' => 'Mohon unggah kembali proposal magang yang sudah ditandatangani oleh dosen pembimbing.',
                'petugas_id' => $petugas->id,
                'submitted_at' => '2025-06-12 10:15:00',
                'processed_at' => '2025-06-12 14:30:00',
            ]);

            RiwayatStatus::create([
                'pengajuan_id' => $p0392->id,
                'status_sebelumnya' => null,
                'status_baru' => Pengajuan::STATUS_MENUNGGU,
                'catatan' => 'Pengajuan diajukan oleh mahasiswa.',
                'actor_name' => 'Deta Amelia (Mahasiswa)',
                'created_at' => '2025-06-12 10:15:00',
            ]);

            RiwayatStatus::create([
                'pengajuan_id' => $p0392->id,
                'status_sebelumnya' => Pengajuan::STATUS_MENUNGGU,
                'status_baru' => Pengajuan::STATUS_PERLU_PERBAIKAN,
                'catatan' => 'Mohon unggah kembali proposal magang yang sudah ditandatangani oleh dosen pembimbing.',
                'user_id' => $petugas->id,
                'actor_name' => $petugas->name,
                'created_at' => '2025-06-12 14:30:00',
            ]);
        }

        // 3.3. SILAM-2025-0351: Surat Pengantar Penelitian (Status: Selesai)
        $layananPenelitian = Layanan::where('kode', 'SK-PENELITIAN')->first() ?: Layanan::create([
            'kode' => 'SK-PENELITIAN',
            'nama' => 'Surat Pengantar Penelitian',
            'deskripsi' => 'Surat izin atau pengantar penelitian skripsi/tugas akhir ke instansi tujuan.',
            'alur_pengajuan' => "1. Isi data tujuan penelitian.\n2. Unggah berkas persyaratan.\n3. Verifikasi dan penerbitan surat.",
            'estimasi_hari' => 1,
            'icon' => 'document-search',
            'is_aktif' => true,
        ]);

        if (! Pengajuan::where('nomor_tiket', 'SILAM-2025-0351')->exists()) {
            $p0351 = Pengajuan::create([
                'nomor_tiket' => 'SILAM-2025-0351',
                'layanan_id' => $layananPenelitian->id,
                'user_id' => $mahasiswa->id,
                'npm' => '2415061120',
                'nama_mahasiswa' => 'Deta Amelia',
                'prodi' => 'Informatika',
                'angkatan' => '2024',
                'email' => 'deta.amelia@mhs.ac.id',
                'no_hp' => '081234567890',
                'keperluan' => 'Pengambilan data skripsi/penelitian tugas akhir.',
                'uri_semantik_mahasiswa' => 'http://silam.ft.ac.id/resource/mahasiswa/2415061120',
                'status' => Pengajuan::STATUS_SELESAI,
                'catatan_petugas' => 'Surat pengantar penelitian telah disetujui dan ditandatangani digital.',
                'petugas_id' => $petugas->id,
                'submitted_at' => '2025-06-04 08:30:00',
                'processed_at' => '2025-06-04 11:20:00',
                'completed_at' => '2025-06-04 15:45:00',
            ]);

            RiwayatStatus::create([
                'pengajuan_id' => $p0351->id,
                'status_sebelumnya' => null,
                'status_baru' => Pengajuan::STATUS_MENUNGGU,
                'catatan' => 'Pengajuan diajukan oleh mahasiswa.',
                'actor_name' => 'Deta Amelia (Mahasiswa)',
                'created_at' => '2025-06-04 08:30:00',
            ]);

            RiwayatStatus::create([
                'pengajuan_id' => $p0351->id,
                'status_sebelumnya' => Pengajuan::STATUS_MENUNGGU,
                'status_baru' => Pengajuan::STATUS_SELESAI,
                'catatan' => 'Surat selesai diproses dan siap diunduh.',
                'user_id' => $petugas->id,
                'actor_name' => $petugas->name,
                'created_at' => '2025-06-04 15:45:00',
            ]);
        }
    }
}
