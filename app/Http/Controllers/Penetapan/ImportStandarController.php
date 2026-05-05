<?php

namespace App\Http\Controllers\Penetapan;

use App\Http\Controllers\Controller;
use App\Models\IndikatorMutu;
use App\Models\KategoriStandar;
use App\Models\PeriodeAMI;
use App\Models\StandarDikti;
use App\Models\TargetUnit;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ImportStandarController extends Controller
{
    public function index()
    {
        return Inertia::render('Penetapan/Import', [
            'periodes' => PeriodeAMI::where('status', '!=', 'Selesai')->orderBy('tahun_akademik', 'desc')->get(),
            'active_periode' => PeriodeAMI::whereNotIn('status', ['Draft', 'Selesai'])->latest()->first()
                ?? PeriodeAMI::where('status', '!=', 'Draft')->latest()->first()
                ?? PeriodeAMI::latest()->first()
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
        $units = UnitKerja::all();

        DB::transaction(function () use ($data, $periodeId, $units) {
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
                $indikator = IndikatorMutu::updateOrCreate(
                    [
                        'standar_id' => $standar->id,
                        'kode_indikator' => $row['Kode Indikator'] ?? '-'
                    ],
                    [
                        'isi_standar' => $row['Isi Indikator'] ?? '',
                        'jenis' => $row['Jenis'] ?? 'IKU'
                    ]
                );

                // 4. Simpan Target per Unit Kerja (Default semua unit dapat target yang sama dari Excel)
                $nilaiTarget = $row['Target'] ?? 0;
                $satuan = $row['Satuan'] ?? '-';

                if ($nilaiTarget > 0) {
                    foreach ($units as $unit) {
                        TargetUnit::updateOrCreate(
                            [
                                'indikator_id' => $indikator->id,
                                'unit_kerja_id' => $unit->id
                            ],
                            [
                                'nilai_target' => $nilaiTarget,
                                'satuan' => $satuan
                            ]
                        );
                    }
                }
            }
        });

        return redirect()->route('penetapan.standar.index')->with('success', 'Data standar, indikator, dan target berhasil diimport.');
    }
}
