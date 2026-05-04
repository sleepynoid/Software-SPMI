<?php

namespace App\Http\Controllers\Penetapan;

use App\Http\Controllers\Controller;
use App\Models\IndikatorMutu;
use App\Models\KategoriStandar;
use App\Models\PeriodeAMI;
use App\Models\StandarDikti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ImportStandarController extends Controller
{
    public function index()
    {
        return Inertia::render('Penetapan/Import', [
            'active_periode' => PeriodeAMI::where('status', '!=', 'Selesai')->latest()->first()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'data' => 'required|array',
            'periode_id' => 'required|exists:periode_ami,id'
        ]);

        $data = $request->input('data');
        $periodeId = $request->input('periode_id');

        DB::transaction(function () use ($data, $periodeId) {
            foreach ($data as $row) {
                // 1. Cari/Buat Kategori
                $namaKategori = $row['Kategori'] ?? 'Lainnya';
                $kategori = KategoriStandar::firstOrCreate(['nama_kategori' => $namaKategori]);

                // 2. Cari/Buat Standar
                $namaStandar = $row['Nama Standar'] ?? 'Standar Tanpa Nama';
                $standar = StandarDikti::firstOrCreate([
                    'periode_id' => $periodeId,
                    'kategori_id' => $kategori->id,
                    'nama_standar' => $namaStandar
                ]);

                // 3. Simpan Indikator
                IndikatorMutu::updateOrCreate(
                    [
                        'standar_id' => $standar->id,
                        'kode_indikator' => $row['Kode Indikator'] ?? '-'
                    ],
                    [
                        'isi_standar' => $row['Isi Indikator'] ?? '',
                        'jenis' => $row['Jenis'] ?? 'IKU'
                    ]
                );
            }
        });

        return redirect()->route('penetapan.standar.index')->with('success', 'Data standar berhasil diimport.');
    }
}
