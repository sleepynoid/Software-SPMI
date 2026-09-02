<?php

namespace App\Imports;

use App\Models\IndikatorMutu;
use App\Models\KategoriStandar;
use App\Models\StandarDikti;
use App\Models\TargetUnit;
use App\Models\UnitKerja;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StandarImport implements SkipsEmptyRows, ToCollection, WithHeadingRow, WithValidation
{
    public function __construct(public int $periodeId) {}

    public function rules(): array
    {
        return [
            'nama_standar' => 'required|string',
            'kode_indikator' => 'required|string',
            'isi_indikator' => 'required|string',
            'jenis' => 'nullable|string|in:IKU,IKT',
            'target' => 'nullable|numeric|min:0',
            'satuan' => 'nullable|string',
            'kategori' => 'nullable|string',
            'unit_kerja' => 'nullable|string',
        ];
    }

    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) {
            throw ValidationException::withMessages(['file' => 'File Excel kosong atau tidak terbaca.']);
        }

        $requiredColumns = ['nama_standar', 'kode_indikator', 'isi_indikator', 'target', 'satuan'];
        $firstRowKeys = array_keys($rows->first()->toArray());
        $missingColumns = array_diff($requiredColumns, $firstRowKeys);

        if (! empty($missingColumns)) {
            throw ValidationException::withMessages([
                'file' => 'Format Excel tidak valid. Kolom berikut tidak ditemukan: '.implode(', ', $missingColumns).'. Pastikan header sesuai dengan template.',
            ]);
        }

        $allUnits = UnitKerja::all();
        $unitNameMap = $allUnits->mapWithKeys(fn ($u) => [strtolower(trim($u->nama_unit)) => $u->id])->toArray();
        $unitTypeMap = $allUnits->groupBy(fn ($u) => strtolower(trim($u->jenis_unit)))->map->pluck('id')->toArray();

        DB::transaction(function () use ($rows, $allUnits, $unitNameMap, $unitTypeMap) {
            $indikatorUpserts = [];
            $targetUpserts = [];

            $kategoriMap = KategoriStandar::all()->mapWithKeys(fn ($k) => [strtolower(trim($k->nama_kategori)) => $k->id])->toArray();
            $standarMap = StandarDikti::where('periode_id', $this->periodeId)->get()->mapWithKeys(fn ($s) => [strtolower(trim($s->nama_standar)) => $s->id])->toArray();

            foreach ($rows as $row) {
                $namaKategori = trim($row['kategori'] ?? 'Lainnya');
                $kategoriKey = strtolower($namaKategori);
                if (! isset($kategoriMap[$kategoriKey])) {
                    $kategori = KategoriStandar::create(['nama_kategori' => $namaKategori]);
                    $kategoriMap[$kategoriKey] = $kategori->id;
                }
                $kategoriId = $kategoriMap[$kategoriKey];

                $namaStandar = trim($row['nama_standar'] ?? '');
                if (empty($namaStandar)) {
                    continue;
                }

                $standarKey = strtolower($namaStandar);
                if (! isset($standarMap[$standarKey])) {
                    $standar = StandarDikti::create([
                        'periode_id' => $this->periodeId,
                        'kategori_id' => $kategoriId,
                        'nama_standar' => $namaStandar,
                    ]);
                    $standarMap[$standarKey] = $standar->id;
                }
                $standarId = $standarMap[$standarKey];

                $kodeIndikator = trim($row['kode_indikator'] ?? $row['kode'] ?? '-');
                $indikatorUpserts[] = [
                    'standar_id' => $standarId,
                    'kode_indikator' => $kodeIndikator,
                    'isi_standar' => trim($row['isi_indikator'] ?? $row['isi_indikator_ambil_paling_kanan'] ?? ''),
                    'jenis' => trim($row['jenis'] ?? 'IKU'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (! empty($indikatorUpserts)) {
                $indikatorUpserts = collect($indikatorUpserts)->keyBy(fn ($i) => $i['standar_id'].'_'.$i['kode_indikator'])->values()->toArray();
                IndikatorMutu::upsert($indikatorUpserts, ['standar_id', 'kode_indikator'], ['isi_standar', 'jenis', 'updated_at']);

                $allIndikators = IndikatorMutu::whereIn('standar_id', array_values($standarMap))->get()->groupBy(fn ($i) => $i->standar_id.'_'.$i->kode_indikator);

                foreach ($rows as $row) {
                    $namaStandar = trim($row['nama_standar'] ?? '');
                    if (empty($namaStandar)) {
                        continue;
                    }

                    $standarId = $standarMap[strtolower($namaStandar)];
                    $kodeIndikator = trim($row['kode_indikator'] ?? $row['kode'] ?? '-');
                    $indikator = $allIndikators->get($standarId.'_'.$kodeIndikator)?->first();

                    if (! $indikator) {
                        continue;
                    }

                    $nilaiTarget = (float) ($row['target'] ?? 0);
                    $satuan = trim($row['satuan'] ?? '-');
                    $unitString = strtolower(trim($row['unit_kerja'] ?? $row['pic_unit_kerja'] ?? ''));

                    if ($nilaiTarget > 0) {
                        $targetUnitIds = [];

                        if (! empty($unitString)) {
                            if (isset($unitNameMap[$unitString])) {
                                $targetUnitIds[] = $unitNameMap[$unitString];
                            } elseif (isset($unitTypeMap[$unitString])) {
                                $targetUnitIds = $unitTypeMap[$unitString];
                            }
                        } else {
                            $targetUnitIds = $allUnits->pluck('id')->toArray();
                        }

                        foreach ($targetUnitIds as $unitId) {
                            $targetUpserts[$indikator->id.'_'.$unitId] = [
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

            if (! empty($targetUpserts)) {
                foreach (array_chunk(array_values($targetUpserts), 1000) as $chunk) {
                    TargetUnit::upsert($chunk, ['indikator_id', 'unit_kerja_id'], ['nilai_target', 'satuan', 'updated_at']);
                }
            }
        });
    }
}
