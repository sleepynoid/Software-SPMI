<?php

namespace App\Http\Controllers\Penetapan;

use App\Http\Controllers\Controller;
use App\Models\PeriodeAMI;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PeriodeController extends Controller
{
    public function index()
    {
        return Inertia::render('Penetapan/Periode/Index', [
            'periodes' => PeriodeAMI::orderBy('tahun_akademik', 'desc')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun_akademik' => 'required|string|max:255',
            'tgl_mulai_audit' => 'required|date',
            'tgl_selesai_audit' => 'required|date|after:tgl_mulai_audit',
            'status' => 'required|in:Draft,Pelaksanaan EDOM,Audit Lapangan,RTM,Selesai',
        ]);

        PeriodeAMI::create($validated);

        return redirect()->back()->with('success', 'Periode AMI berhasil dibuka.');
    }

    public function update(Request $request, PeriodeAMI $periode)
    {
        $validated = $request->validate([
            'tahun_akademik' => 'required|string|max:255',
            'tgl_mulai_audit' => 'required|date',
            'tgl_selesai_audit' => 'required|date|after:tgl_mulai_audit',
            'status' => 'required|in:Draft,Pelaksanaan EDOM,Audit Lapangan,RTM,Selesai',
        ]);

        $periode->update($validated);

        return redirect()->back()->with('success', 'Periode AMI berhasil diperbarui.');
    }

    public function destroy(PeriodeAMI $periode)
    {
        $periode->delete();

        return redirect()->back()->with('success', 'Periode AMI berhasil dihapus.');
    }
}
