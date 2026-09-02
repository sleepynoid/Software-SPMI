<?php

namespace App\Http\Controllers\Penetapan;

use App\Exports\StandarExport;
use App\Http\Controllers\Controller;
use App\Models\KategoriStandar;
use App\Models\PeriodeAMI;
use App\Models\StandarDikti;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class StandarController extends Controller
{
    public function index(Request $request)
    {
        $periode_id = $request->input('periode_id')
            ?? PeriodeAMI::where('status', '!=', 'Selesai')->first()?->id
            ?? PeriodeAMI::latest()->first()?->id;

        return Inertia::render('Penetapan/Standar/Index', [
            'standars' => StandarDikti::with('kategori', 'periode')
                ->when($periode_id, fn ($q) => $q->where('periode_id', $periode_id))
                ->get(),
            'periodes' => PeriodeAMI::all(),
            'categories' => KategoriStandar::all(),
            'selectedPeriodeId' => (int) $periode_id,
        ]);
    }

    public function export(Request $request)
    {
        $request->validate(['periode_id' => 'required|exists:periode_ami,id']);

        return Excel::download(new StandarExport($request->periode_id), 'export_standar_dikti.xlsx');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'periode_id' => 'required|exists:periode_ami,id',
            'kategori_id' => 'required|exists:kategori_standar,id',
            'nama_standar' => 'required|string|max:255',
        ]);

        StandarDikti::create($validated);

        return redirect()->back()->with('success', 'Standar Dikti berhasil ditambahkan.');
    }

    public function update(Request $request, StandarDikti $standar)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori_standar,id',
            'nama_standar' => 'required|string|max:255',
        ]);

        $standar->update($validated);

        return redirect()->back()->with('success', 'Standar Dikti berhasil diperbarui.');
    }

    public function destroy(StandarDikti $standar)
    {
        $standar->delete();

        return redirect()->back()->with('success', 'Standar Dikti berhasil dihapus.');
    }
}
