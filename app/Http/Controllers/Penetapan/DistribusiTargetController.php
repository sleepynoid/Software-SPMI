<?php

namespace App\Http\Controllers\Penetapan;

use App\Http\Controllers\Controller;
use App\Models\IndikatorMutu;
use App\Models\UnitKerja;
use App\Models\TargetUnit;
use App\Models\PeriodeAMI;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DistribusiTargetController extends Controller
{
    public function index(Request $request)
    {
        $periode_id = $request->periode_id ?: PeriodeAMI::where('status', '!=', 'Selesai')->first()?->id;

        $indikators = IndikatorMutu::with('standar')
            ->whereHas('standar', fn($q) => $q->where('periode_id', $periode_id))
            ->get();

        $units = UnitKerja::where('jenis_unit', 'Program Studi')->get();

        $targets = TargetUnit::whereIn('indikator_id', $indikators->pluck('id'))
            ->get()
            ->groupBy('indikator_id')
            ->map(fn($item) => $item->keyBy('unit_kerja_id'));

        return Inertia::render('Penetapan/DistribusiTarget/Index', [
            'periodes' => PeriodeAMI::all(),
            'selectedPeriodeId' => (int) $periode_id,
            'indikators' => $indikators,
            'units' => $units,
            'existingTargets' => $targets,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'targets' => 'required|array',
            'targets.*.indikator_id' => 'required|exists:indikator_mutu,id',
            'targets.*.unit_kerja_id' => 'required|exists:unit_kerja,id',
            'targets.*.nilai_target' => 'required|numeric',
            'targets.*.satuan' => 'required|string|max:50',
        ]);

        foreach ($request->targets as $targetData) {
            TargetUnit::updateOrCreate(
                [
                    'indikator_id' => $targetData['indikator_id'],
                    'unit_kerja_id' => $targetData['unit_kerja_id'],
                ],
                [
                    'nilai_target' => $targetData['nilai_target'],
                    'satuan' => $targetData['satuan'],
                ]
            );
        }

        return redirect()->back()->with('success', 'Distribusi Target berhasil disimpan.');
    }
}
