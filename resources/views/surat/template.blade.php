<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Resmi - {{ $pengajuan->nomor_tiket }}</title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111;
            line-height: 1.5;
            background: #f1f5f9;
            margin: 0;
            padding: 20px;
        }

        .paper {
            background: white;
            max-width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 25mm 20mm;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            box-sizing: border-box;
            position: relative;
        }

        /* Kop Surat */
        .kop-surat {
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 20px;
            text-align: center;
        }

        .logo-univ {
            width: 75px;
            height: 75px;
            border: 2px solid #333;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: bold;
            font-family: Arial, sans-serif;
            background: #f8fafc;
        }

        .kop-text {
            flex: 1;
        }

        .kop-text h2 {
            margin: 0;
            font-size: 15pt;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .kop-text h3 {
            margin: 2px 0;
            font-size: 13pt;
            text-transform: uppercase;
            font-weight: bold;
        }

        .kop-text p {
            margin: 2px 0 0 0;
            font-size: 9.5pt;
            font-family: Arial, sans-serif;
            color: #444;
        }

        /* Metadata Surat */
        .meta-surat {
            margin-bottom: 20px;
            font-size: 11pt;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table td {
            vertical-align: top;
            padding: 2px 0;
        }

        .judul-surat {
            text-align: center;
            margin: 25px 0 20px 0;
        }

        .judul-surat h4 {
            margin: 0;
            font-size: 13pt;
            text-transform: uppercase;
            text-decoration: underline;
            font-weight: bold;
        }

        .judul-surat span {
            font-size: 10.5pt;
            display: block;
            margin-top: 3px;
        }

        /* Isi Surat */
        .isi-surat {
            font-size: 11pt;
            text-align: justify;
            line-height: 1.6;
        }

        .table-data-mhs {
            margin: 15px 0 15px 30px;
            font-size: 11pt;
        }

        .table-data-mhs td {
            padding: 3px 0;
            vertical-align: top;
        }

        /* Tanda Tangan */
        .ttd-container {
            margin-top: 40px;
            display: flex;
            justify-content: flex-end;
        }

        .ttd-box {
            width: 250px;
            text-align: center;
            font-size: 11pt;
        }

        .qr-placeholder {
            width: 80px;
            height: 80px;
            margin: 12px auto;
            border: 1px solid #ccc;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fafafa;
            font-size: 9px;
            font-family: Arial, sans-serif;
            color: #666;
            text-align: center;
        }

        /* Print Controls */
        .print-toolbar {
            max-width: 210mm;
            margin: 0 auto 15px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #1e293b;
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            font-family: Arial, sans-serif;
        }

        .btn-print {
            background: #4f46e5;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            font-size: 13px;
        }

        .btn-print:hover {
            background: #4338ca;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .paper {
                box-shadow: none;
                padding: 0;
                margin: 0;
                width: 100%;
            }
            .print-toolbar {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="print-toolbar">
        <div>
            <strong>Pratinjau Surat Resmi SILAM</strong> &bull; Nomor Tiket: {{ $pengajuan->nomor_tiket }}
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn-print">🖨️ Cetak / Simpan PDF</button>
            <button onclick="window.close()" class="btn-print" style="background: #475569;">✕ Tutup</button>
        </div>
    </div>

    <div class="paper">
        <!-- Kop Surat Resmi -->
        <div class="kop-surat">
            <div class="logo-univ">U</div>
            <div class="kop-text">
                <h2>Kementerian Pendidikan Tinggi, Riset, dan Teknologi</h2>
                <h3>Universitas Pembangunan Nasional "Veteran"</h3>
                <h3>Fakultas Ilmu Komputer &bull; Bagian Administrasi Akademik</h3>
                <p>Jalan Raya Rungkut Madya No. 1, Gunung Anyar, Surabaya &bull; Telp. (031) 8706369 &bull; Laman: silam.upnjatim.ac.id</p>
            </div>
        </div>

        <!-- Judul Surat -->
        <div class="judul-surat">
            <h4>{{ strtoupper($pengajuan->layanan->nama) }}</h4>
            <span>Nomor: {{ $nomorSurat }}</span>
        </div>

        <!-- Isi Surat -->
        <div class="isi-surat">
            <p>
                Yang bertanda tangan di bawah ini, Wakil Dekan Bidang Akademik dan Kemahasiswaan Fakultas Ilmu Komputer Universitas Pembangunan Nasional "Veteran", dengan ini menerangkan dengan sebenarnya bahwa:
            </p>

            <table class="table-data-mhs">
                <tr>
                    <td style="width: 180px;">Nama Mahasiswa</td>
                    <td style="width: 15px;">:</td>
                    <td><strong>{{ strtoupper($pengajuan->nama_mahasiswa) }}</strong></td>
                </tr>
                <tr>
                    <td>Nomor Pokok Mahasiswa (NPM)</td>
                    <td>:</td>
                    <td><strong>{{ $pengajuan->npm }}</strong></td>
                </tr>
                <tr>
                    <td>Program Studi</td>
                    <td>:</td>
                    <td>{{ $pengajuan->prodi }}</td>
                </tr>
                <tr>
                    <td>Tahun Angkatan</td>
                    <td>:</td>
                    <td>{{ $pengajuan->angkatan }}</td>
                </tr>
                <tr>
                    <td>Status Mahasiswa</td>
                    <td>:</td>
                    <td>Terdaftar Aktif pada Pangkalan Data Mahasiswa (Sistem A)</td>
                </tr>
            </table>

            <p>
                Berdasarkan data verifikasi pangkalan data akademik terpadu, yang bersangkutan adalah benar-benar mahasiswa aktif yang sedang menempuh perkuliahan pada semester berjalan tahun akademik {{ date('Y') }}/{{ date('Y')+1 }}.
            </p>

            <p>
                Surat keterangan ini diberikan atas permohonan yang bersangkutan melalui Sistem Layanan Administrasi Mahasiswa (SILAM) dengan rincian keperluan:
                <br>
                <em>"{{ $pengajuan->keperluan }}"</em>
            </p>

            <p>
                Demikian surat keterangan ini diterbitkan dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.
            </p>
        </div>

        <!-- Tanda Tangan & QR Verifikasi -->
        <div class="ttd-container">
            <div class="ttd-box">
                <div>Surabaya, {{ $tanggalSurat }}</div>
                <div style="margin-top: 5px;">a.n. Dekan</div>
                <div style="font-weight: bold;">Wakil Dekan Bidang Akademik,</div>

                <div class="qr-placeholder">
                    <span>TTE DIGITAL<br>VALID BY<br>SILAM SYSTEM</span>
                </div>

                <div style="font-weight: bold; text-decoration: underline;">Dr. Ir. Hendro Prasetyo, M.Kom.</div>
                <div style="font-size: 9.5pt;">NIP. 19780512 200312 1 002</div>
            </div>
        </div>

        <!-- Catatan Kaki Semantik / Keamanan -->
        <div style="position: absolute; bottom: 15mm; left: 20mm; right: 20mm; font-size: 8pt; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 5px; font-family: Arial, sans-serif; display: flex; justify-content: space-between;">
            <div>
                Nomor Tiket: {{ $pengajuan->nomor_tiket }} &bull; Verifikasi Digital: {{ url('/lacak?tiket=' . $pengajuan->nomor_tiket) }}
            </div>
            <div>
                Semantic URI: {{ $pengajuan->semantic_uri }}
            </div>
        </div>
    </div>

</body>
</html>
