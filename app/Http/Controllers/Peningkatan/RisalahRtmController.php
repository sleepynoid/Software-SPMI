<?php

namespace App\Http\Controllers\Peningkatan;

use App\Http\Controllers\Controller;
use App\Models\KertasKerjaAudit;
use App\Models\PeriodeAMI;
use App\Models\RisalahRtm;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RisalahRtmController extends Controller
{
    public function index(Request $request)
    {
        $periode_id = $request->periode_id ?: PeriodeAMI::where('status', 'RTM')->first()?->id
            ?? PeriodeAMI::orderBy('id', 'desc')->first()?->id;

        $findingsCount = KertasKerjaAudit::selectRaw('kategori_temuan, COUNT(*) as count')
            ->join('capaian_pelaksanaan', 'capaian_pelaksanaan.id', '=', 'kertas_kerja_audit.capaian_id')
            ->join('target_unit', 'target_unit.id', '=', 'capaian_pelaksanaan.target_unit_id')
            ->join('indikator_mutu', 'indikator_mutu.id', '=', 'target_unit.indikator_id')
            ->join('standar_dikti', 'standar_dikti.id', '=', 'indikator_mutu.standar_id')
            ->where('standar_dikti.periode_id', $periode_id)
            ->groupBy('kategori_temuan')
            ->pluck('count', 'kategori_temuan');

        return Inertia::render('Peningkatan/Risalah/Index', [
            'periodes' => PeriodeAMI::all(),
            'selectedPeriodeId' => (int) $periode_id,
            'risalahs' => RisalahRtm::with('unitKerja')
                ->where('periode_id', $periode_id)
                ->get(),
            'units' => UnitKerja::where('jenis_unit', 'Program Studi')->get(),
            'stats' => $findingsCount,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'periode_id' => 'required|exists:periode_ami,id',
            'unit_kerja_id' => 'required|exists:unit_kerja,id',
            'tgl_rtm' => 'required|date',
            'pimpinan_rapat' => 'required|string|max:255',
            'isi_risalah' => 'required|string',
            'keputusan_peningkatan' => 'required|string',
        ]);

        RisalahRtm::create($validated);

        return redirect()->back()->with('success', 'Risalah RTM berhasil disimpan.');
    }

    public function update(Request $request, RisalahRtm $risalah)
    {
        $validated = $request->validate([
            'tgl_rtm' => 'required|date',
            'pimpinan_rapat' => 'required|string|max:255',
            'isi_risalah' => 'required|string',
            'keputusan_peningkatan' => 'required|string',
        ]);

        $risalah->update($validated);

        return redirect()->back()->with('success', 'Risalah RTM berhasil diperbarui.');
    }

    public function destroy(RisalahRtm $risalah)
    {
        $risalah->delete();

        return redirect()->back()->with('success', 'Risalah RTM berhasil dihapus.');
    }
}
