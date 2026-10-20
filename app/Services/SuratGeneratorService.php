<?php

namespace App\Services;

use App\Models\Pengajuan;
use Carbon\Carbon;

class SuratGeneratorService
{
    /**
     * Menghasilkan tampilan dokumen surat resmi berbasis HTML yang siap cetak / simpan sebagai PDF.
     */
    public function renderSuratHtml(Pengajuan $pengajuan): string
    {
        $nomorSurat = 'B/'.($pengajuan->id + 100).'/UN.AC.ID/AKAD/'.date('Y');
        $tanggalSurat = Carbon::parse($pengajuan->completed_at ?? now())->isoFormat('D MMMM Y');

        return view('surat.template', [
            'pengajuan' => $pengajuan,
            'nomorSurat' => $nomorSurat,
            'tanggalSurat' => $tanggalSurat,
        ])->render();
    }
}
