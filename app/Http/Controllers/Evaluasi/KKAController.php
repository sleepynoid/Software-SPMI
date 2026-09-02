<?php

namespace App\Http\Controllers\Evaluasi;

use App\Http\Controllers\Controller;
use App\Models\CapaianPelaksanaan;
use App\Models\KertasKerjaAudit;
use App\Models\PeriodeAMI;
use App\Models\TargetUnit;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KKAController extends Controller
{
    public function show(Request $request, UnitKerja $unit)
    {
        $periode_id = $request->periode_id ?: PeriodeAMI::whereNotIn('status', ['Draft', 'Selesai'])->latest()->first()?->id
            ?? PeriodeAMI::where('status', '!=', 'Draft')->latest()->first()?->id
            ?? PeriodeAMI::latest()->first()?->id;

        $data = TargetUnit::with([
            'indikatorMutu:id,kode_indikator,isi_standar,standar_id',
            'indikatorMutu.standar:id,nama_standar,kategori_id,periode_id',
            'indikatorMutu.standar.kategori:id,nama_kategori',
            'capaianPelaksanaan:id,target_unit_id,nilai_aktual',
            'capaianPelaksanaan.kertasKerjaAudit:id,capaian_id,kategori_temuan,deskripsi_temuan',
        ])
            ->join('indikator_mutu', 'indikator_mutu.id', '=', 'target_unit.indikator_id')
            ->join('standar_dikti', 'standar_dikti.id', '=', 'indikator_mutu.standar_id')
            ->where('target_unit.unit_kerja_id', $unit->id)
            ->where('standar_dikti.periode_id', $periode_id)
            ->select('target_unit.*')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Evaluasi/KKA/Show', [
            'unit' => $unit,
            'periodes' => PeriodeAMI::all(),
            'selectedPeriodeId' => (int) $periode_id,
            'auditData' => $data,
        ]);
    }

    public function store(Request $request, CapaianPelaksanaan $capaian)
    {
        $validated = $request->validate([
            'unit_kerja_id' => 'required|exists:unit_kerja,id',
            'kategori_temuan' => 'required|in:Sesuai,Melampaui,Observasi (OB),KTS Minor,KTS Mayor',
            'deskripsi_temuan' => 'nullable|string',
        ]);

        $belongsToUnit = CapaianPelaksanaan::query()
            ->whereKey($capaian->id)
            ->whereHas('targetUnit', fn ($q) => $q->where('unit_kerja_id', $validated['unit_kerja_id']))
            ->exists();

        if (! $belongsToUnit) {
            abort(403);
        }

        unset($validated['unit_kerja_id']);

        KertasKerjaAudit::updateOrCreate(
            ['capaian_id' => $capaian->id],
            array_merge($validated, [
                'auditor_id' => $request->user()->id,
            ])
        );

        return redirect()->back()->with('success', 'Hasil audit berhasil disimpan.');
    }
}
