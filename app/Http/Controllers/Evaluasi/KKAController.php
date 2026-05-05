<?php

namespace App\Http\Controllers\Evaluasi;

use App\Http\Controllers\Controller;
use App\Models\UnitKerja;
use App\Models\IndikatorMutu;
use App\Models\TargetUnit;
use App\Models\CapaianPelaksanaan;
use App\Models\KertasKerjaAudit;
use App\Models\PeriodeAMI;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KKAController extends Controller
{
    public function show(Request $request, UnitKerja $unit)
    {
        $periode_id = $request->periode_id ?: PeriodeAMI::whereNotIn('status', ['Draft', 'Selesai'])->latest()->first()?->id
                    ?? PeriodeAMI::where('status', '!=', 'Draft')->latest()->first()?->id
                    ?? PeriodeAMI::latest()->first()?->id;

        $data = TargetUnit::with(['indikatorMutu.standar.kategori', 'capaianPelaksanaan.kertasKerjaAudit'])
            ->where('unit_kerja_id', $unit->id)
            ->whereHas('indikatorMutu.standar', fn($q) => $q->where('periode_id', $periode_id))
            ->get();

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
            'kategori_temuan' => 'required|in:Sesuai,Melampaui,Observasi (OB),KTS Minor,KTS Mayor',
            'deskripsi_temuan' => 'nullable|string',
        ]);

        KertasKerjaAudit::updateOrCreate(
            ['capaian_id' => $capaian->id],
            array_merge($validated, [
                'auditor_id' => $request->user()->id,
            ])
        );

        return redirect()->back()->with('success', 'Hasil audit berhasil disimpan.');
    }
}
