@extends('layouts.app')

@section('title', 'Semantic Web Explorer - RDF & OWL')

@section('content')
<div class="container" style="max-width: 1200px; padding-top: 2rem; padding-bottom: 4rem;">
    <!-- Hero Banner -->
    <div class="card" style="margin-bottom: 2.5rem; background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 60%, #312e81 100%); color: white; border: none; box-shadow: var(--shadow-xl);">
        <div class="card-body" style="padding: 2.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1.5rem;">
                <div style="max-width: 700px;">
                    <span class="badge-tag" style="margin-bottom: 0.75rem; display: inline-block;">W3C SEMANTIC WEB COMPLIANT</span>
                    <h1 style="font-size: 2rem; font-weight: 800; color: white; line-height: 1.25; margin-bottom: 0.75rem;">
                        Integrasi Semantic Web (RDF &amp; OWL) <br>
                        <span style="background: linear-gradient(to right, #38bdf8, #818cf8); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Sistem A &bull; Sistem B (SILAM)</span>
                    </h1>
                    <p style="font-size: 0.95rem; color: #cbd5e1; line-height: 1.6;">
                        SILAM memodelkan data permohonan surat administrasi ke dalam standar Linked Data (RDF &amp; OWL). Relasi semantik menghubungkan transaksi pengajuan surat di Sistem B langsung ke URI entitas mahasiswa di Sistem A.
                    </p>
                </div>

                <div style="display: grid; gap: 0.5rem; min-width: 260px;">
                    <a href="{{ route('api.rdf.graph') }}" target="_blank" class="btn btn-emerald" style="width: 100%;">
                        <span>📥</span> Unduh Graf RDF Turtle (.ttl)
                    </a>
                    <a href="{{ route('api.rdf.ontology', ['format' => 'owl']) }}" target="_blank" class="btn btn-white" style="width: 100%;">
                        <span>📥</span> Unduh Ontologi OWL (.owl)
                    </a>
                    <a href="{{ route('api.rdf.ontology', ['format' => 'ttl']) }}" target="_blank" class="btn btn-outline-white" style="width: 100%;">
                        <span>📄</span> Lihat Ontologi Turtle (.ttl)
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats & Namespace Cards -->
    <div class="grid grid-cols-2" style="gap: 1.5rem; margin-bottom: 2.5rem;">
        <!-- Namespace Reference -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Namespace &amp; Prefix Terstandarisasi</h3>
            </div>
            <div class="card-body" style="padding: 1.25rem 1.5rem;">
                <div style="display: grid; gap: 0.75rem; font-size: 0.85rem;">
                    <div style="background: var(--slate-50); padding: 0.65rem 0.85rem; border-radius: var(--radius-sm); border: 1px solid var(--slate-200);">
                        <code style="font-family: var(--font-mono); font-weight: 700; color: var(--primary-700);">mhs:</code>
                        <span style="font-family: var(--font-mono); color: var(--slate-600);">&lt;http://example.org/ontology/mahasiswa#&gt;</span>
                        <div style="font-size: 0.75rem; color: var(--slate-500); margin-top: 0.2rem;">Ontologi Master Mahasiswa (Sistem A)</div>
                    </div>

                    <div style="background: var(--slate-50); padding: 0.65rem 0.85rem; border-radius: var(--radius-sm); border: 1px solid var(--slate-200);">
                        <code style="font-family: var(--font-mono); font-weight: 700; color: #0284c7;">silam:</code>
                        <span style="font-family: var(--font-mono); color: var(--slate-600);">&lt;http://example.org/ontology/administrasi#&gt;</span>
                        <div style="font-size: 0.75rem; color: var(--slate-500); margin-top: 0.2rem;">Ontologi Layanan Administrasi Surat (Sistem B)</div>
                    </div>

                    <div style="background: var(--slate-50); padding: 0.65rem 0.85rem; border-radius: var(--radius-sm); border: 1px solid var(--slate-200);">
                        <code style="font-family: var(--font-mono); font-weight: 700; color: #059669;">res_mhs:</code>
                        <span style="font-family: var(--font-mono); color: var(--slate-600);">&lt;http://example.org/resource/mahasiswa/{npm}&gt;</span>
                        <div style="font-size: 0.75rem; color: var(--slate-500); margin-top: 0.2rem;">Individual Resource Mahasiswa di Sistem A</div>
                    </div>

                    <div style="background: var(--slate-50); padding: 0.65rem 0.85rem; border-radius: var(--radius-sm); border: 1px solid var(--slate-200);">
                        <code style="font-family: var(--font-mono); font-weight: 700; color: #d97706;">res_silam:</code>
                        <span style="font-family: var(--font-mono); color: var(--slate-600);">&lt;http://example.org/resource/pengajuan/{tiket}&gt;</span>
                        <div style="font-size: 0.75rem; color: var(--slate-500); margin-top: 0.2rem;">Individual Resource Pengajuan Surat di Sistem B</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Semantic Relationships Diagram -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Struktur Graf Relasi Semantik Antarsistem</h3>
            </div>
            <div class="card-body" style="padding: 1.5rem; font-size: 0.875rem;">
                <div style="background: #0f172a; color: #e2e8f0; padding: 1.25rem; border-radius: var(--radius-md); font-family: var(--font-mono); font-size: 0.8rem; line-height: 1.7;">
                    <div style="color: #38bdf8; font-weight: 700;"># Relasi Antar-Entitas:</div>
                    <div>[mhs:Mahasiswa (Sistem A)] ──(silam:mengajukan)──&gt; [silam:PengajuanSurat (B)]</div>
                    <div style="padding-left: 20px; color: #94a3b8;">├── (silam:untukLayanan) ──&gt; [silam:LayananAdministrasi]</div>
                    <div style="padding-left: 20px; color: #94a3b8;">├── (silam:memilikiStatus) ──&gt; [silam:StatusPengajuan]</div>
                    <div style="padding-left: 20px; color: #94a3b8;">└── (silam:melampirkanDokumen) ──&gt; [silam:DokumenPersyaratan]</div>
                    <div style="margin-top: 0.5rem; color: #a5b4fc;">[mhs:Mahasiswa] ──(mhs:terdaftarPada)──&gt; [mhs:ProgramStudi]</div>
                </div>

                <div style="margin-top: 1rem; font-size: 0.8rem; color: var(--slate-600); line-height: 1.5;">
                    ✓ <strong>Data Minimization:</strong> SILAM tidak menyalin seluruh basis data mahasiswa. SILAM menghubungkan berkas pengajuan surat ke URI semantik mahasiswa yang dikelola oleh Sistem A.
                </div>
            </div>
        </div>
    </div>

    <!-- SPARQL Query Simulator Card -->
    <div class="card" style="margin-bottom: 2.5rem; box-shadow: var(--shadow-lg);">
        <div class="card-header" style="background: linear-gradient(135deg, var(--slate-900), var(--primary-900)); color: white; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
            <div>
                <h3 class="card-title" style="color: white; font-size: 1.15rem;">Simulasi Kueri SPARQL (Interaktif)</h3>
                <p style="font-size: 0.8rem; color: #cbd5e1; margin-top: 0.2rem;">Uji kueri Semantic Web secara langsung pada graf data terpadu Sistem A dan B</p>
            </div>

            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <select id="sparql-preset" class="form-control" style="width: auto; background: white; color: var(--slate-800); font-size: 0.85rem; padding: 0.4rem 0.75rem;">
                    <option value="ALL_PENGAJUAN">Kueri 1: Seluruh Pengajuan &amp; Relasi Mahasiswa (Sistem A)</option>
                    <option value="BY_PRODI">Kueri 2: Pengajuan Berdasarkan Program Studi</option>
                    <option value="PERLU_PERBAIKAN">Kueri 3: Berkas yang Memerlukan Revisi / Perbaikan</option>
                    <option value="SELESAI">Kueri 4: Berkas yang Sudah Disetujui / Selesai</option>
                </select>
                <button type="button" id="btn-run-sparql" class="btn btn-primary btn-sm">
                    <span>⚡</span> Eksekusi SPARQL
                </button>
            </div>
        </div>

        <div class="card-body">
            <!-- SPARQL Query Code Display -->
            <div style="margin-bottom: 1.25rem;">
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--slate-500); text-transform: uppercase; margin-bottom: 0.35rem;">Sintaks Kueri SPARQL (Standar W3C):</div>
                <div class="code-box" id="sparql-query-display" style="max-height: 200px;">
PREFIX silam: &lt;http://example.org/ontology/administrasi#&gt;
PREFIX mhs: &lt;http://example.org/ontology/mahasiswa#&gt;

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
                </div>
            </div>

            <!-- SPARQL Results Table -->
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <div style="font-size: 0.75rem; font-weight: 700; color: var(--slate-500); text-transform: uppercase;">
                        Hasil Kueri (<span id="result-count">3</span> Rekod Ditemukan):
                    </div>
                </div>

                <div class="table-responsive" style="border: 1px solid var(--slate-200); border-radius: var(--radius-sm); max-height: 320px; overflow-y: auto;">
                    <table class="table" id="sparql-results-table">
                        <thead>
                            <tr>
                                <th>?tiket</th>
                                <th>?namaMahasiswa</th>
                                <th>?npm</th>
                                <th>?prodi</th>
                                <th>?namaLayanan</th>
                                <th>?status</th>
                                <th>?mhs_uri (Sistem A)</th>
                            </tr>
                        </thead>
                        <tbody id="sparql-results-body">
                            <!-- Populated via Javascript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Live RDF Turtle Code Viewer Card -->
    <div class="card" style="box-shadow: var(--shadow-md);">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
            <div>
                <h3 class="card-title">Pratinjau Triple RDF Terintegrasi (Turtle Syntax)</h3>
                <p style="font-size: 0.8rem; color: var(--slate-500); margin-top: 0.2rem;">Representasi triple RDF yang dihasilkan secara dinamis oleh backend SILAM</p>
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="button" class="btn btn-outline btn-sm" id="btn-copy-rdf">
                    <span>📋</span> Salin Turtle
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="code-box" id="rdf-code-view" style="max-height: 400px; font-size: 0.8rem;">
{{ $sampleTurtle }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const sparqlQueries = {
    'ALL_PENGAJUAN': `PREFIX silam: <http://example.org/ontology/administrasi#>
PREFIX mhs: <http://example.org/ontology/mahasiswa#>

SELECT ?tiket ?namaMahasiswa ?npm ?namaLayanan ?status ?tanggal ?mhsUri
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
ORDER BY DESC(?tanggal)`,

    'BY_PRODI': `PREFIX silam: <http://example.org/ontology/administrasi#>
PREFIX mhs: <http://example.org/ontology/mahasiswa#>

SELECT ?prodi ?tiket ?namaMahasiswa ?namaLayanan ?status
WHERE {
    ?pengajuan a silam:PengajuanSurat ;
               silam:nomorTiket ?tiket ;
               silam:diajukanOleh ?mhs ;
               silam:untukLayanan ?layanan ;
               silam:memilikiStatus ?status .
               
    ?mhs mhs:namaMahasiswa ?namaMahasiswa ;
         mhs:programStudi ?prodi .
         
    ?layanan silam:namaLayanan ?namaLayanan .
}
ORDER BY ?prodi`,

    'PERLU_PERBAIKAN': `PREFIX silam: <http://example.org/ontology/administrasi#>
PREFIX mhs: <http://example.org/ontology/mahasiswa#>

SELECT ?tiket ?namaMahasiswa ?catatanPetugas
WHERE {
    ?pengajuan a silam:PengajuanSurat ;
               silam:nomorTiket ?tiket ;
               silam:diajukanOleh ?mhs ;
               silam:memilikiStatus "Perlu Perbaikan" ;
               silam:catatanPetugas ?catatanPetugas .
               
    ?mhs mhs:namaMahasiswa ?namaMahasiswa .
}`,

    'SELESAI': `PREFIX silam: <http://example.org/ontology/administrasi#>
PREFIX mhs: <http://example.org/ontology/mahasiswa#>

SELECT ?tiket ?namaMahasiswa ?namaLayanan ?status
WHERE {
    ?pengajuan a silam:PengajuanSurat ;
               silam:nomorTiket ?tiket ;
               silam:diajukanOleh ?mhs ;
               silam:untukLayanan ?layanan ;
               silam:memilikiStatus "Selesai" .
               
    ?mhs mhs:namaMahasiswa ?namaMahasiswa .
    ?layanan silam:namaLayanan ?namaLayanan .
}`
};

document.getElementById('sparql-preset').addEventListener('change', function() {
    const code = this.value;
    document.getElementById('sparql-query-display').textContent = sparqlQueries[code] || '';
    runSparqlQuery(code);
});

function runSparqlQuery(code) {
    const btn = document.getElementById('btn-run-sparql');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span> Menjalankan...';

    fetch('{{ route("api.rdf.sparql") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ query_code: code })
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = '<span>⚡</span> Eksekusi SPARQL';

        const tbody = document.getElementById('sparql-results-body');
        document.getElementById('result-count').textContent = res.count;

        let filtered = res.results;
        if (code === 'PERLU_PERBAIKAN') {
            filtered = res.results.filter(r => r.status === 'Perlu Perbaikan');
        } else if (code === 'SELESAI') {
            filtered = res.results.filter(r => r.status === 'Selesai' || r.status === 'Disetujui');
        }

        document.getElementById('result-count').textContent = filtered.length;

        let html = '';
        filtered.forEach(row => {
            html += `
                <tr>
                    <td><strong style="font-family: var(--font-mono); color: var(--primary-700);">${row.tiket}</strong></td>
                    <td>${row.namaMahasiswa}</td>
                    <td><code>${row.npm}</code></td>
                    <td>${row.prodi}</td>
                    <td>${row.namaLayanan}</td>
                    <td><span class="badge ${row.status === 'Selesai' ? 'badge-emerald' : (row.status === 'Perlu Perbaikan' ? 'badge-orange' : 'badge-amber')}">${row.status}</span></td>
                    <td><a href="${row.mhs_uri}" target="_blank" style="font-family: var(--font-mono); font-size: 0.75rem; color: var(--primary-600);">${row.mhs_uri}</a></td>
                </tr>
            `;
        });

        tbody.innerHTML = html || '<tr><td colspan="7" style="text-align: center; color: var(--slate-500); padding: 1.5rem;">Tidak ada triple yang cocok dengan kueri.</td></tr>';
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<span>⚡</span> Eksekusi SPARQL';
        alert('Gagal mengeksekusi SPARQL query.');
    });
}

document.getElementById('btn-run-sparql').addEventListener('click', function() {
    runSparqlQuery(document.getElementById('sparql-preset').value);
});

document.getElementById('btn-copy-rdf').addEventListener('click', function() {
    const text = document.getElementById('rdf-code-view').innerText;
    navigator.clipboard.writeText(text).then(() => {
        alert('✓ Triple RDF Turtle berhasil disalin ke clipboard!');
    });
});

// Run initial
runSparqlQuery('ALL_PENGAJUAN');
</script>
@endpush
