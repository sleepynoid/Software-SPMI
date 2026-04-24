<?php

namespace App\Http\Controllers\Penetapan;

use App\Http\Controllers\Controller;
use App\Models\IndikatorMutu;
use App\Models\StandarDikti;
use App\Models\PeriodeAMI;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IndikatorMutuController extends Controller
{
    public function index(Request $request)
    {
        $periode_id = $request->periode_id ?: PeriodeAMI::where('status', '!=', 'Selesai')->first()?->id;
        $standar_id = $request->standar_id;

        $standars = StandarDikti::when($periode_id, fn($q) => $q->where('periode_id', $periode_id))->get();

        return Inertia::render('Penetapan/Indikator/Index', [
            'indikators' => IndikatorMutu::with('standar')
                ->when($standar_id, fn($q) => $q->where('standar_id', $standar_id))
                ->when(!$standar_id && $periode_id, function($q) use ($periode_id) {
                    $q->whereHas('standar', fn($sq) => $sq->where('periode_id', $periode_id));
                })
                ->get(),
            'standars' => $standars,
            'periodes' => PeriodeAMI::all(),
            'selectedPeriodeId' => (int) $periode_id,
            'selectedStandarId' => (int) $standar_id,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'standar_id' => 'required|exists:standar_dikti,id',
            'kode_indikator' => 'required|string|max:50',
            'isi_standar' => 'required|string',
            'jenis' => 'required|in:IKU,IKT',
        ]);

        IndikatorMutu::create($validated);

        return redirect()->back()->with('success', 'Indikator Mutu berhasil ditambahkan.');
    }

    public function update(Request $request, IndikatorMutu $indikator)
    {
        $validated = $request->validate([
            'standar_id' => 'required|exists:standar_dikti,id',
            'kode_indikator' => 'required|string|max:50',
            'isi_standar' => 'required|string',
            'jenis' => 'required|in:IKU,IKT',
        ]);

        $indikator->update($validated);

        return redirect()->back()->with('success', 'Indikator Mutu berhasil diperbarui.');
    }

    public function destroy(IndikatorMutu $indikator)
    {
        $indikator->delete();
        return redirect()->back()->with('success', 'Indikator Mutu berhasil dihapus.');
    }
}
