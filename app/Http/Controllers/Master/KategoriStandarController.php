<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\KategoriStandar;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KategoriStandarController extends Controller
{
    public function index()
    {
        return Inertia::render('Master/KategoriStandar/Index', [
            'categories' => KategoriStandar::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'is_default' => 'required|boolean',
        ]);

        KategoriStandar::create($validated);

        return redirect()->back()->with('success', 'Kategori Standar berhasil ditambahkan.');
    }

    public function update(Request $request, KategoriStandar $kategoriStandar)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'is_default' => 'required|boolean',
        ]);

        $kategoriStandar->update($validated);

        return redirect()->back()->with('success', 'Kategori Standar berhasil diperbarui.');
    }

    public function destroy(KategoriStandar $kategoriStandar)
    {
        if ($kategoriStandar->standarDiktis()->exists()) {
            return redirect()->back()->with('error', 'Kategori tidak dapat dihapus karena masih dipakai oleh standar.');
        }

        $kategoriStandar->delete();

        return redirect()->back()->with('success', 'Kategori Standar berhasil dihapus.');
    }
}
