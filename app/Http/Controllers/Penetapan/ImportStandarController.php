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
            $indikatorUpserts = [];
            $targetUpserts = [];

            // Cache Kategori & Standar (Memory Lookup)
            $kategoriMap = KategoriStandar::pluck('id', 'nama_kategori')->toArray();
            $standarMap = StandarDikti::where('periode_id', $periodeId)->get()->groupBy('nama_standar')->map->first()->pluck('id', 'nama_standar')->toArray();

            foreach ($data as $row) {
                // 1. Cari/Buat Kategori (Memory Cache)
                $namaKategori = $row['Kategori'] ?? 'Lainnya';
                if (!isset($kategoriMap[$namaKategori])) {
                    $kategori = KategoriStandar::create(['nama_kategori' => $namaKategori]);
                    $kategoriMap[$namaKategori] = $kategori->id;
                }
                $kategoriId = $kategoriMap[$namaKategori];

                // 2. Cari/Buat Standar (Memory Cache)
                $namaStandar = $row['Nama Standar'] ?? 'Standar Tanpa Nama';
                if (!isset($standarMap[$namaStandar])) {
                    $standar = StandarDikti::create([
                        'periode_id' => $periodeId,
                        'kategori_id' => $kategoriId,
                        'nama_standar' => $namaStandar
                    ]);
                    $standarMap[$namaStandar] = $standar->id;
                }
                $standarId = $standarMap[$namaStandar];

                // 3. Collect Indikator untuk Bulk Upsert
                $kodeIndikator = $row['Kode Indikator'] ?? '-';
                $indikatorUpserts[] = [
                    'standar_id' => $standarId,
                    'kode_indikator' => $kodeIndikator,
                    'isi_standar' => $row['Isi Indikator'] ?? '',
                    'jenis' => $row['Jenis'] ?? 'IKU',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Execute Bulk Upsert Indikator (1 Query)
            if (!empty($indikatorUpserts)) {
                IndikatorMutu::upsert($indikatorUpserts, ['standar_id', 'kode_indikator'], ['isi_standar', 'jenis', 'updated_at']);
                
                // Ambil ID indikator yang baru saja di-upsert untuk Target
                $allIndikators = IndikatorMutu::whereIn('standar_id', array_values($standarMap))->get()->groupBy(fn($i) => $i->standar_id . '_' . $i->kode_indikator);

                foreach ($data as $row) {
                    $standarId = $standarMap[$row['Nama Standar'] ?? 'Standar Tanpa Nama'];
                    $kodeIndikator = $row['Kode Indikator'] ?? '-';
                    $indikator = $allIndikators->get($standarId . '_' . $kodeIndikator)?->first();

                    if (!$indikator) continue;

                    $nilaiTarget = $row['Target'] ?? 0;
                    $satuan = $row['Satuan'] ?? '-';

                    if ($nilaiTarget > 0) {
                        foreach ($units as $unit) {
                            $targetUpserts[] = [
                                'indikator_id' => $indikator->id,
                                'unit_kerja_id' => $unit->id,
                                'nilai_target' => $nilaiTarget,
                                'satuan' => $satuan,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }
                }
            }

            // Execute Bulk Upsert Target Unit (1 Query untuk ribuan baris)
            if (!empty($targetUpserts)) {
                foreach (array_chunk($targetUpserts, 1000) as $chunk) {
                    TargetUnit::upsert($chunk, ['indikator_id', 'unit_kerja_id'], ['nilai_target', 'satuan', 'updated_at']);
                }
            }
        });

        return redirect()->route('penetapan.standar.index')->with('success', 'Data standar, indikator, dan target berhasil diimport.');
    }
}
