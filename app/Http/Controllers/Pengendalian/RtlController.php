<?php

namespace App\Http\Controllers\Pengendalian;

use App\Http\Controllers\Controller;
use App\Models\KertasKerjaAudit;
use App\Models\TindakLanjutPtk;
use App\Models\PeriodeAMI;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RtlController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $periode_id = $request->periode_id ?: PeriodeAMI::orderBy('id', 'desc')->first()?->id;

        // Findings for this unit that are NOT "Sesuai" or "Melampaui"
        $findings = KertasKerjaAudit::with(['capaianPelaksanaan.targetUnit.indikatorMutu.standar', 'tindakLanjut'])
            ->whereHas('capaianPelaksanaan.targetUnit', fn($q) => $q->where('unit_kerja_id', $user->unit_kerja_id))
            ->whereHas('capaianPelaksanaan.targetUnit.indikatorMutu.standar', fn($q) => $q->where('periode_id', $periode_id))
            ->whereNotIn('kategori_temuan', ['Sesuai', 'Melampaui'])
            ->get();

        return Inertia::render('Pengendalian/IsiRtl/Index', [
            'periodes' => PeriodeAMI::all(),
            'selectedPeriodeId' => (int) $periode_id,
            'findings' => $findings,
        ]);
    }

    public function store(Request $request, KertasKerjaAudit $kka)
    {
        $validated = $request->validate([
            'rencana_tindak_lanjut' => 'required|string',
            'jadwal_penyelesaian' => 'required|date',
            'penanggung_jawab' => 'required|string|max:255',
        ]);

        TindakLanjutPtk::updateOrCreate(
            ['kka_id' => $kka->id],
            array_merge($validated, [
                'status_tl' => 'Open',
            ])
        );

        return redirect()->back()->with('success', 'Rencana Tindak Lanjut berhasil disimpan.');
    }
}
