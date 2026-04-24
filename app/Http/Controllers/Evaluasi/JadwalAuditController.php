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

        // List units that are Program Studi
        $units = UnitKerja::where('jenis_unit', 'Program Studi')->get();

        // Count how many indicators each unit has reported
        $stats = CapaianPelaksanaan::whereHas('targetUnit', function($q) use ($periode_id) {
            $q->whereHas('indikatorMutu.standar', fn($sq) => $sq->where('periode_id', $periode_id));
        })
        ->get()
        ->groupBy('targetUnit.unit_kerja_id');

        return Inertia::render('Evaluasi/JadwalAudit/Index', [
            'periodes' => PeriodeAMI::all(),
            'selectedPeriodeId' => (int) $periode_id,
            'units' => $units,
            'reportingStats' => $stats->map(fn($item) => $item->count()),
        ]);
    }
}
