<?php

namespace App\Http\Controllers;

use App\Models\IndikatorMutu;
use App\Models\KertasKerjaAudit;
use App\Models\PeriodeAMI;
use App\Models\StandarDikti;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user()->load('role', 'unitKerja');

        $periodes = PeriodeAMI::orderBy('tahun_akademik', 'desc')->get();

        $selectedPeriodeId = $request->input('periode_id')
            ?? $periodes->whereNotIn('status', ['Draft', 'Selesai'])->first()?->id
            ?? $periodes->first()?->id;

        $activePeriode = $periodes->firstWhere('id', $selectedPeriodeId);

        $stats = [
            'total_standar' => StandarDikti::where('periode_id', $selectedPeriodeId)->count(),
            'total_indikator' => IndikatorMutu::join('standar_dikti', 'standar_dikti.id', '=', 'indikator_mutu.standar_id')
                ->where('standar_dikti.periode_id', $selectedPeriodeId)
                ->count(),
            'total_temuan' => KertasKerjaAudit::join('capaian_pelaksanaan', 'capaian_pelaksanaan.id', '=', 'kertas_kerja_audit.capaian_id')
                ->join('target_unit', 'target_unit.id', '=', 'capaian_pelaksanaan.target_unit_id')
                ->join('indikator_mutu', 'indikator_mutu.id', '=', 'target_unit.indikator_id')
                ->join('standar_dikti', 'standar_dikti.id', '=', 'indikator_mutu.standar_id')
                ->where('standar_dikti.periode_id', $selectedPeriodeId)
                ->whereIn('kategori_temuan', ['KTS Minor', 'KTS Mayor', 'Observasi (OB)'])
                ->count(),
        ];

        return Inertia::render('Dashboard/Index', [
            'user' => $user,
            'periodes' => $periodes,
            'active_periode' => $activePeriode,
            'stats' => $stats,
        ]);
    }
}
