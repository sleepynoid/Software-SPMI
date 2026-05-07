<?php

namespace App\Http\Controllers\Evaluasi;

use App\Http\Controllers\Controller;
use App\Models\UnitKerja;
use App\Models\PeriodeAMI;
use App\Models\CapaianPelaksanaan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JadwalAuditController extends Controller
{
    public function index(Request $request)
    {
        $periode_id = $request->periode_id ?: PeriodeAMI::where('status', 'Audit Lapangan')->first()?->id 
                    ?? PeriodeAMI::orderBy('id', 'desc')->first()?->id;

        // List units that are Program Studi with pagination
        $units = UnitKerja::where('jenis_unit', 'Program Studi')->paginate(15)->withQueryString();

        // Count how many indicators each unit has reported using SQL Aggregation
        $reportingStats = CapaianPelaksanaan::selectRaw('target_unit.unit_kerja_id, COUNT(*) as count')
            ->join('target_unit', 'target_unit.id', '=', 'capaian_pelaksanaan.target_unit_id')
            ->join('indikator_mutu', 'indikator_mutu.id', '=', 'target_unit.indikator_id')
            ->join('standar_dikti', 'standar_dikti.id', '=', 'indikator_mutu.standar_id')
            ->where('standar_dikti.periode_id', $periode_id)
            ->groupBy('target_unit.unit_kerja_id')
            ->pluck('count', 'unit_kerja_id');

        return Inertia::render('Evaluasi/JadwalAudit/Index', [
            'periodes' => PeriodeAMI::all(),
            'selectedPeriodeId' => (int) $periode_id,
            'units' => $units,
            'reportingStats' => $reportingStats,
        ]);
    }
}
