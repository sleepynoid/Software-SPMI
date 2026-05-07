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

        // Findings for this unit that are NOT "Sesuai" or "Melampaui" with optimized query
        $findings = KertasKerjaAudit::with([
                'tindakLanjut',
                'capaianPelaksanaan:id,target_unit_id,nilai_capaian',
                'capaianPelaksanaan.targetUnit:id,unit_kerja_id,indikator_mutu_id',
                'capaianPelaksanaan.targetUnit.indikatorMutu:id,kode_indikator,nama_indikator',
            ])
            ->join('capaian_pelaksanaan', 'capaian_pelaksanaan.id', '=', 'kertas_kerja_audit.capaian_id')
            ->join('target_unit', 'target_unit.id', '=', 'capaian_pelaksanaan.target_unit_id')
            ->join('indikator_mutu', 'indikator_mutu.id', '=', 'target_unit.indikator_id')
            ->join('standar_dikti', 'standar_dikti.id', '=', 'indikator_mutu.standar_id')
            ->where('target_unit.unit_kerja_id', $user->unit_kerja_id)
            ->where('standar_dikti.periode_id', $periode_id)
            ->whereNotIn('kategori_temuan', ['Sesuai', 'Melampaui'])
            ->select('kertas_kerja_audit.*')
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
