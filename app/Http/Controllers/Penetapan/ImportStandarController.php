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
use Illuminate\Validation\ValidationException;
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

        if (empty($data)) {
            throw ValidationException::withMessages(['data' => 'File Excel kosong atau tidak terbaca.']);
        }

        // 1. Validate Required Headers based on Screenshot
        $firstRow = $data[0];
        $requiredKeys = ['Nama Standar', 'Kode Indikator', 'Isi Indikator', 'Target', 'Satuan'];
        
        foreach ($requiredKeys as $key) {
            if (!isset($firstRow[$key]) && !array_key_exists($key, $firstRow)) {
                throw ValidationException::withMessages([
                    'data' => "Format Excel tidak valid. Kolom '$key' tidak ditemukan. Pastikan header sesuai dengan template."
                ]);
            }
        }
        
        // 2. Build Unit Maps (Normalized)
        $allUnits = UnitKerja::all();
        $unitNameMap = $allUnits->mapWithKeys(fn($u) => [strtolower(trim($u->nama_unit)) => $u->id])->toArray();
        $unitTypeMap = $allUnits->groupBy(fn($u) => strtolower(trim($u->jenis_unit)))->map->pluck('id')->toArray();

        DB::transaction(function () use ($data, $periodeId, $unitNameMap, $unitTypeMap, $allUnits) {
            $indikatorUpserts = [];
            $targetUpserts = [];

            // Cache Kategori & Standar
            $kategoriMap = KategoriStandar::all()->mapWithKeys(fn($k) => [strtolower(trim($k->nama_kategori)) => $k->id])->toArray();
            $standarMap = StandarDikti::where('periode_id', $periodeId)->get()->mapWithKeys(fn($s) => [strtolower(trim($s->nama_standar)) => $s->id])->toArray();

            foreach ($data as $row) {
                // 1. Kategori
                $namaKategori = trim($row['Kategori'] ?? 'Lainnya');
                $kategoriKey = strtolower($namaKategori);
                if (!isset($kategoriMap[$kategoriKey])) {
                    $kategori = KategoriStandar::create(['nama_kategori' => $namaKategori]);
                    $kategoriMap[$kategoriKey] = $kategori->id;
                }
                $kategoriId = $kategoriMap[$kategoriKey];

                // 2. Standar
                $namaStandar = trim($row['Nama Standar'] ?? '');
                if (empty($namaStandar)) continue;

                $standarKey = strtolower($namaStandar);
                if (!isset($standarMap[$standarKey])) {
                    $standar = StandarDikti::create([
                        'periode_id' => $periodeId,
                        'kategori_id' => $kategoriId,
                        'nama_standar' => $namaStandar
                    ]);
                    $standarMap[$standarKey] = $standar->id;
                }
                $standarId = $standarMap[$standarKey];

                // 3. Indikator
                $kodeIndikator = trim($row['Kode Indikator'] ?? $row['Kode'] ?? '-');
                $indikatorUpserts[] = [
                    'standar_id' => $standarId,
                    'kode_indikator' => $kodeIndikator,
                    'isi_standar' => trim($row['Isi Indikator'] ?? $row['Isi Indikator (Ambil Paling Kanan)'] ?? ''),
                    'jenis' => trim($row['Jenis'] ?? 'IKU'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Execute Bulk Upsert Indikator
            if (!empty($indikatorUpserts)) {
                $indikatorUpserts = collect($indikatorUpserts)->keyBy(fn($i) => $i['standar_id'] . '_' . $i['kode_indikator'])->values()->toArray();
                IndikatorMutu::upsert($indikatorUpserts, ['standar_id', 'kode_indikator'], ['isi_standar', 'jenis', 'updated_at']);
                
                $allIndikators = IndikatorMutu::whereIn('standar_id', array_values($standarMap))->get()->groupBy(fn($i) => $i->standar_id . '_' . $i->kode_indikator);

                foreach ($data as $row) {
                    $namaStandar = trim($row['Nama Standar'] ?? '');
                    if (empty($namaStandar)) continue;

                    $standarId = $standarMap[strtolower($namaStandar)];
                    $kodeIndikator = trim($row['Kode Indikator'] ?? $row['Kode'] ?? '-');
                    $indikator = $allIndikators->get($standarId . '_' . $kodeIndikator)?->first();

                    if (!$indikator) continue;

                    $nilaiTarget = (float) ($row['Target'] ?? 0);
                    $satuan = trim($row['Satuan'] ?? '-');
                    $unitString = strtolower(trim($row['Unit Kerja'] ?? $row['PIC / Unit Kerja'] ?? ''));

                    if ($nilaiTarget > 0) {
                        $targetUnitIds = [];
                        
                        if (!empty($unitString)) {
                            // Mapping ke Unit Kerja spesifik atau Jenis Unit
                            if (isset($unitNameMap[$unitString])) {
                                $targetUnitIds[] = $unitNameMap[$unitString];
                            } elseif (isset($unitTypeMap[$unitString])) {
                                $targetUnitIds = $unitTypeMap[$unitString];
                            }
                        } else {
                            // Jika Unit Kerja kosong, default ke SEMUA unit kerja (Backward Compatibility)
                            $targetUnitIds = $allUnits->pluck('id')->toArray();
                        }

                        foreach ($targetUnitIds as $unitId) {
                            $targetUpserts[$indikator->id . '_' . $unitId] = [
                                'indikator_id' => $indikator->id,
                                'unit_kerja_id' => $unitId,
                                'nilai_target' => $nilaiTarget,
                                'satuan' => $satuan,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }
                }
            }

            // Execute Bulk Upsert Target Unit
            if (!empty($targetUpserts)) {
                foreach (array_chunk(array_values($targetUpserts), 1000) as $chunk) {
                    TargetUnit::upsert($chunk, ['indikator_id', 'unit_kerja_id'], ['nilai_target', 'satuan', 'updated_at']);
                }
            }
        });

        return redirect()->route('penetapan.standar.index')->with('success', 'Data standar, indikator, dan target berhasil diimport.');
    }
}
