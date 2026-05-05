<?php

namespace App\Http\Controllers;

use App\Models\PeriodeAMI;
use App\Models\StandarDikti;
use App\Models\IndikatorMutu;
use App\Models\KertasKerjaAudit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user()->load('role', 'unitKerja');
        
        // 1. Ambil semua periode aktif/selesai
        $periodes = PeriodeAMI::orderBy('tahun_akademik', 'desc')->get();
        
        // 2. Tentukan periode yang ditampilkan (default: latest non-Draft & non-Selesai)
        $selectedPeriodeId = $request->input('periode_id') ?? PeriodeAMI::whereNotIn('status', ['Draft', 'Selesai'])->latest()->first()?->id;
        
        // Jika tidak ada yang aktif, ambil yang paling baru apapun statusnya (kecuali Draft jika memungkinkan)
        if (!$selectedPeriodeId) {
            $selectedPeriodeId = PeriodeAMI::where('status', '!=', 'Draft')->latest()->first()?->id ?? PeriodeAMI::latest()->first()?->id;
        }

        $activePeriode = PeriodeAMI::find($selectedPeriodeId);

        // 3. Hitung statistik (Dummy logic, sesuaikan nanti dengan query real)
        $stats = [
            'total_standar' => StandarDikti::where('periode_id', $selectedPeriodeId)->count(),
            'total_indikator' => IndikatorMutu::whereHas('standar', function($q) use ($selectedPeriodeId) {
                $q->where('periode_id', $selectedPeriodeId);
            })->count(),
            'total_temuan' => KertasKerjaAudit::whereHas('capaianPelaksanaan.targetUnit.indikatorMutu.standar', function($q) use ($selectedPeriodeId) {
                $q->where('periode_id', $selectedPeriodeId);
            })->whereIn('kategori_temuan', ['KTS Minor', 'KTS Mayor', 'Observasi (OB)'])->count(),
        ];
        
        return Inertia::render('Dashboard/Index', [
            'user' => $user,
            'periodes' => $periodes,
            'active_periode' => $activePeriode,
            'stats' => $stats,
        ]);
    }
}
