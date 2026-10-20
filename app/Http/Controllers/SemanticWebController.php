<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Services\SemanticWebService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class SemanticWebController extends Controller
{
    public function __construct(
        protected SemanticWebService $semanticWeb
    ) {}

    /**
     * Halaman Semantic Web Explorer (Visual Graf, Ontologi OWL, & SPARQL Simulator)
     */
    public function explorer(): View
    {
        $stats = [
            'total_pengajuan' => Pengajuan::count(),
            'total_layanan' => Layanan::count(),
            'total_triples' => (Pengajuan::count() * 8) + (Layanan::count() * 4) + 20, // Estimasi triple terdaftar
        ];

        $pengajuans = Pengajuan::with('layanan')->latest('submitted_at')->get();
        $sampleTurtle = $this->semanticWeb->generateFullGraphRdf();
        $sampleOwl = $this->semanticWeb->getOntologyOwl();
        $ontologyTtl = $this->semanticWeb->getOntologyTurtle();

        return view('semantik.explorer', compact('stats', 'pengajuans', 'sampleTurtle', 'sampleOwl', 'ontologyTtl'));
    }

    /**
     * Ekspor RDF Turtle untuk satu pengajuan spesifik
     * GET /api/rdf/pengajuan/{nomor_tiket}
     */
    public function exportPengajuanRdf(string $nomorTiket): Response|JsonResponse
    {
        $pengajuan = Pengajuan::with(['layanan', 'dokumens'])->where('nomor_tiket', $nomorTiket)->first();

        if (! $pengajuan) {
            return response()->json([
                'success' => false,
                'message' => "Pengajuan dengan nomor tiket '{$nomorTiket}' tidak ditemukan.",
            ], 404);
        }

        $ttl = $this->semanticWeb->generatePengajuanRdf($pengajuan);

        return response($ttl, 200, [
            'Content-Type' => 'text/turtle; charset=UTF-8',
            'Content-Disposition' => 'inline; filename="pengajuan_'.$nomorTiket.'.ttl"',
        ]);
    }

    /**
     * Ekspor seluruh graf RDF terintegrasi Sistem A & B
     * GET /api/rdf/graph
     */
    public function exportGraphRdf(): Response
    {
        $ttl = $this->semanticWeb->generateFullGraphRdf();

        return response($ttl, 200, [
            'Content-Type' => 'text/turtle; charset=UTF-8',
            'Content-Disposition' => 'inline; filename="silam_integrated_graph.ttl"',
        ]);
    }

    /**
     * Ekspor Berkas Ontologi OWL
     * GET /api/rdf/ontology
     */
    public function exportOntology(Request $request): Response
    {
        $format = $request->query('format', 'owl');

        if ($format === 'ttl') {
            $content = $this->semanticWeb->getOntologyTurtle();
            $contentType = 'text/turtle; charset=UTF-8';
            $filename = 'silam.ttl';
        } else {
            $content = $this->semanticWeb->getOntologyOwl();
            $contentType = 'application/rdf+xml; charset=UTF-8';
            $filename = 'silam.owl';
        }

        return response($content, 200, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }

    /**
     * Endpoint Simulasi SPARQL Query
     * POST /api/rdf/sparql
     */
    public function sparqlQuery(Request $request): JsonResponse
    {
        $queryCode = $request->input('query_code', 'ALL_PENGAJUAN');
        $results = $this->semanticWeb->executeSparqlQuery($queryCode);

        return response()->json([
            'success' => true,
            'query_code' => $queryCode,
            'count' => count($results),
            'results' => $results,
        ]);
    }
}
