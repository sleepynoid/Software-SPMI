<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UnitKerjaController extends Controller
{
    public function index()
    {
        return Inertia::render('Master/UnitKerja/Index', [
            'units' => UnitKerja::with('kepalaUnit')->get(),
            'users' => User::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_unit' => 'required|string|max:255',
            'jenis_unit' => 'required|in:Fakultas,Program Studi,Biro,Lembaga',
            'kepala_unit_id' => 'nullable|exists:users,id',
        ]);

        UnitKerja::create($validated);

        return redirect()->back()->with('success', 'Unit Kerja berhasil ditambahkan.');
    }

    public function update(Request $request, UnitKerja $unitKerja)
    {
        $validated = $request->validate([
            'nama_unit' => 'required|string|max:255',
            'jenis_unit' => 'required|in:Fakultas,Program Studi,Biro,Lembaga',
            'kepala_unit_id' => 'nullable|exists:users,id',
        ]);

        $unitKerja->update($validated);

        return redirect()->back()->with('success', 'Unit Kerja berhasil diperbarui.');
    }

    public function destroy(UnitKerja $unitKerja)
    {
        $unitKerja->delete();

        return redirect()->back()->with('success', 'Unit Kerja berhasil dihapus.');
    }
}
