<?php

namespace App\Http\Controllers\Pengendalian;

use App\Http\Controllers\Controller;
use App\Models\KertasKerjaAudit;
use App\Models\PeriodeAMI;
use App\Models\TindakLanjutPtk;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RtlController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $periode_id = $request->periode_id ?: PeriodeAMI::orderBy('id', 'desc')->first()?->id;

        $findings = KertasKerjaAudit::with([
            'tindakLanjut',
            'capaianPelaksanaan:id,target_unit_id,nilai_aktual',
            'capaianPelaksanaan.targetUnit:id,unit_kerja_id,indikator_id',
            'capaianPelaksanaan.targetUnit.indikatorMutu:id,kode_indikator,isi_standar',
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
        $belongsToUserUnit = KertasKerjaAudit::query()
            ->whereKey($kka->id)
            ->whereHas('capaianPelaksanaan', fn ($q) => $q->whereHas('targetUnit', fn ($sq) => $sq->where('unit_kerja_id', $request->user()->unit_kerja_id)))
            ->exists();

        if (! $belongsToUserUnit) {
            abort(403);
        }

        $validated = $request->validate([
            'rencana_tindak_lanjut' => 'required|string',
            'jadwal_penyelesaian' => 'required|date',
            'akar_masalah' => 'nullable|string',
        ]);

        TindakLanjutPtk::updateOrCreate(
            ['kka_id' => $kka->id],
            array_merge($validated, [
                'status_verifikasi' => 'Open',
            ])
        );

        return redirect()->back()->with('success', 'Rencana Tindak Lanjut berhasil disimpan.');
    }
}
