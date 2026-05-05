<?php

namespace App\Http\Controllers\Pelaksanaan;

use App\Http\Controllers\Controller;
use App\Models\TargetUnit;
use App\Models\CapaianPelaksanaan;
use App\Models\PeriodeAMI;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EvaluasiDiriController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $periode_id = $request->periode_id ?: PeriodeAMI::whereNotIn('status', ['Draft', 'Selesai'])->latest()->first()?->id 
                    ?? PeriodeAMI::where('status', '!=', 'Draft')->latest()->first()?->id
                    ?? PeriodeAMI::latest()->first()?->id;

        // Auditee only sees their unit's targets
        $targets = TargetUnit::with(['indikatorMutu.standar.kategori', 'capaianPelaksanaan'])
            ->where('unit_kerja_id', $user->unit_kerja_id)
            ->whereHas('indikatorMutu.standar', fn($q) => $q->where('periode_id', $periode_id))
            ->get();

        return Inertia::render('Pelaksanaan/EvaluasiDiri/Index', [
            'periodes' => PeriodeAMI::all(),
            'selectedPeriodeId' => (int) $periode_id,
            'targets' => $targets,
        ]);
    }

    public function store(Request $request, TargetUnit $targetUnit)
    {
        // Security check
        if ($request->user()->unit_kerja_id !== $targetUnit->unit_kerja_id) {
            abort(403);
        }

        $validated = $request->validate([
            'nilai_aktual' => 'required|numeric',
            'evaluasi_diri' => 'required|string',
            'link_dokumen_bukti' => 'required|url',
        ]);

        CapaianPelaksanaan::updateOrCreate(
            ['target_unit_id' => $targetUnit->id],
            array_merge($validated, [
                'submitted_by' => $request->user()->id,
                'submitted_at' => now(),
            ])
        );

        return redirect()->back()->with('success', 'Laporan capaian berhasil disimpan.');
    }
}
