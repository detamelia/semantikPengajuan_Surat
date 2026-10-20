# PANDUAN PENGUJIAN INTEGRASI SEMANTIC WEB (RDF & OWL)
## Sistem Layanan Administrasi Mahasiswa (SILAM) & Sistem Informasi Data Mahasiswa

Dokumen ini menjelaskan rancangan ontologi, pemodelan relasi semantik, dan panduan teknis langkah demi langkah untuk menguji integrasi Semantic Web antara **Sistem A** (Pangkalan Data Mahasiswa) dan **Sistem B** (SILAM).

---

### 1. Arsitektur Namespace & Prefix

Kedua sistem menggunakan arsitektur namespace yang konsisten:

| Prefix | Namespace URI | Deskripsi |
|---|---|---|
| `mhs:` | `http://example.org/ontology/mahasiswa#` | Skema ontologi master mahasiswa (Sistem A) |
| `silam:` | `http://example.org/ontology/administrasi#` | Skema ontologi layanan administrasi surat (Sistem B) |
| `res_mhs:` | `http://example.org/resource/mahasiswa/` | Individual resource mahasiswa (e.g. `/22081010001`) |
| `res_layanan:` | `http://example.org/resource/layanan/` | Individual jenis layanan surat (e.g. `/sk-aktif`) |
| `res_pengajuan:` | `http://example.org/resource/pengajuan/` | Individual pengajuan surat (e.g. `/SILAM-202610-0001`) |
| `res_dokumen:` | `http://example.org/resource/dokumen/` | Individual dokumen persyaratan digital |

---

### 2. Relasi Semantik Antar-Entitas

Ontologi SILAM menghubungkan entitas lintas sistem secara terstruktur:

```
[ mhs:Mahasiswa (Sistem A) ] ── (silam:mengajukan) ──> [ silam:PengajuanSurat (Sistem B) ]
             │                                                    │
             │ (mhs:terdaftarPada)                                ├── (silam:untukLayanan) ──> [ silam:LayananAdministrasi ]
             ▼                                                    ├── (silam:memilikiStatus) ─> [ silam:StatusPengajuan ]
[ mhs:ProgramStudi (Sistem A) ]                                   └── (silam:melampirkanDokumen) -> [ silam:DokumenPersyaratan ]
```

1. **`silam:diajukanOleh` / `silam:mengajukan`**:
   - Menghubungkan pengajuan surat di SILAM ke URI individu mahasiswa di Sistem A (`res_mhs:{npm}`).
   - *Prinsip Data Minimization*: Database SILAM tidak perlu menduplikasi seluruh data pribadi mahasiswa, karena identitas semantik mahasiswa dapat di-dereference melalui URI semantiknya.
2. **`silam:untukLayanan`**:
   - Menghubungkan berkas permohonan dengan katalog layanan administrasi.
3. **`silam:memilikiStatus`**:
   - Menghubungkan pengajuan dengan status verifikasi saat ini.
4. **`silam:melampirkanDokumen`**:
   - Menghubungkan pengajuan dengan berkas digital pendukung yang telah diunggah.

---

### 3. Berkas-Berkas Ontologi & RDF

1. **`ontology/silam.owl`**: Berkas ontologi format standar W3C OWL/RDF-XML.
2. **`ontology/silam.ttl`**: Berkas ontologi format Turtle (Terbaca manusia).
3. **`ontology/contoh_integrasi_semantik.ttl`**: Sampel graf RDF terpadu yang memadukan data nyata Sistem A dan Sistem B.

---

### 4. Endpoint Ekspor RDF Dinamis di SILAM

Sistem B menyediakan endpoint ekspor RDF secara otomatis:

1. **Ekspor RDF Pengajuan Berdasarkan Tiket**:
   ```http
   GET /api/rdf/pengajuan/{nomor_tiket}
   Accept: text/turtle
   ```
   *Contoh:* `http://localhost:8001/api/rdf/pengajuan/SILAM-202610-0001`
   Mengembalikan triple RDF lengkap permohonan surat beserta relasi ke URI mahasiswa Sistem A.

2. **Ekspor Seluruh Graf RDF Terpadu**:
   ```http
   GET /api/rdf/graph
   Accept: text/turtle
   ```
   Mengembalikan seluruh data pengajuan, layanan, dokumen, dan tautan semantik mahasiswa dalam satu graf Turtle.

3. **Ekspor Definisi Ontologi OWL**:
   ```http
   GET /api/rdf/ontology
   Accept: application/rdf+xml (atau text/turtle)
   ```

---

### 5. Contoh Kueri SPARQL untuk Pengujian

Kueri-kueri ini dapat diuji melalui antarmuka **Semantic Explorer** di web SILAM (`http://localhost:8001/semantik`) atau menggunakan triplestore eksternal (Apache Jena Fuseki / GraphDB):

#### Kueri 1: Daftar Seluruh Pengajuan Beserta Nama Mahasiswa dan Layanan
```sparql
PREFIX silam: <http://example.org/ontology/administrasi#>
PREFIX mhs: <http://example.org/ontology/mahasiswa#>

SELECT ?tiket ?namaMahasiswa ?npm ?namaLayanan ?status ?tanggal
WHERE {
    ?pengajuan a silam:PengajuanSurat ;
               silam:nomorTiket ?tiket ;
               silam:diajukanOleh ?mhs ;
               silam:untukLayanan ?layanan ;
               silam:memilikiStatus ?status ;
               silam:tanggalPengajuan ?tanggal .
    
    ?mhs mhs:namaMahasiswa ?namaMahasiswa ;
         mhs:npm ?npm .
         
    ?layanan silam:namaLayanan ?namaLayanan .
}
ORDER BY DESC(?tanggal)
```

#### Kueri 2: Pengajuan Surat Berdasarkan Program Studi Mahasiswa
```sparql
PREFIX silam: <http://example.org/ontology/administrasi#>
PREFIX mhs: <http://example.org/ontology/mahasiswa#>

SELECT ?prodi ?namaMahasiswa ?namaLayanan ?status
WHERE {
    ?pengajuan a silam:PengajuanSurat ;
               silam:diajukanOleh ?mhs ;
               silam:untukLayanan ?layanan ;
               silam:memilikiStatus ?status .
               
    ?mhs mhs:namaMahasiswa ?namaMahasiswa ;
         mhs:programStudi ?prodi .
         
    ?layanan silam:namaLayanan ?namaLayanan .
}
ORDER BY ?prodi
```

#### Kueri 3: Pencarian Berkas yang Memerlukan Perbaikan
```sparql
PREFIX silam: <http://example.org/ontology/administrasi#>
PREFIX mhs: <http://example.org/ontology/mahasiswa#>

SELECT ?tiket ?namaMahasiswa ?catatanPetugas
WHERE {
    ?pengajuan a silam:PengajuanSurat ;
               silam:nomorTiket ?tiket ;
               silam:diajukanOleh ?mhs ;
               silam:memilikiStatus "Perlu Perbaikan" ;
               silam:catatanPetugas ?catatanPetugas .
               
    ?mhs mhs:namaMahasiswa ?namaMahasiswa .
}
```

---

### 6. Cara Menguji Menggunakan Protégé
1. Buka aplikasi **Protégé** (versi 5.x).
2. Pilih menu **File** -> **Open**, lalu arahkan ke berkas `Pengajuan Surat/ontology/silam.owl`.
3. Buka tab **Entities** -> **Classes** untuk melihat hirarki kelas (`PengajuanSurat`, `LayananAdministrasi`, `Mahasiswa`).
4. Buka tab **Object Properties** untuk memeriksa relasi `diajukanOleh`, `untukLayanan`, `memilikiStatus`, dan `melampirkanDokumen`.
5. Untuk menguji inference/reasoner, aktifkan reasoner **HermiT** atau **Pellet** melalui menu **Reasoner** -> **Start Reasoner**. Ontologi terbukti konsisten tanpa error kontradiksi.
