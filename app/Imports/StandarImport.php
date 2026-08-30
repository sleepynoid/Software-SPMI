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

    public function headingRowFormatter(): string
    {
        return 'none';
    }

    public function rules(): array
    {
        return [
            'Nama Standar' => 'required|string',
            'Kode Indikator' => 'required|string',
            'Isi Indikator' => 'required|string',
            'Jenis' => 'nullable|string|in:IKU,IKT',
            'Target' => 'nullable|numeric|min:0',
            'Satuan' => 'nullable|string',
            'Kategori' => 'nullable|string',
            'Unit Kerja' => 'nullable|string',
        ];
    }

    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) {
            throw ValidationException::withMessages(['file' => 'File Excel kosong atau tidak terbaca.']);
        }

        $requiredColumns = ['Nama Standar', 'Kode Indikator', 'Isi Indikator', 'Target', 'Satuan'];
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
                $namaKategori = trim($row['Kategori'] ?? 'Lainnya');
                $kategoriKey = strtolower($namaKategori);
                if (! isset($kategoriMap[$kategoriKey])) {
                    $kategori = KategoriStandar::create(['nama_kategori' => $namaKategori]);
                    $kategoriMap[$kategoriKey] = $kategori->id;
                }
                $kategoriId = $kategoriMap[$kategoriKey];

                $namaStandar = trim($row['Nama Standar'] ?? '');
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

            if (! empty($indikatorUpserts)) {
                $indikatorUpserts = collect($indikatorUpserts)->keyBy(fn ($i) => $i['standar_id'].'_'.$i['kode_indikator'])->values()->toArray();
                IndikatorMutu::upsert($indikatorUpserts, ['standar_id', 'kode_indikator'], ['isi_standar', 'jenis', 'updated_at']);

                $allIndikators = IndikatorMutu::whereIn('standar_id', array_values($standarMap))->get()->groupBy(fn ($i) => $i->standar_id.'_'.$i->kode_indikator);

                foreach ($rows as $row) {
                    $namaStandar = trim($row['Nama Standar'] ?? '');
                    if (empty($namaStandar)) {
                        continue;
                    }

                    $standarId = $standarMap[strtolower($namaStandar)];
                    $kodeIndikator = trim($row['Kode Indikator'] ?? $row['Kode'] ?? '-');
                    $indikator = $allIndikators->get($standarId.'_'.$kodeIndikator)?->first();

                    if (! $indikator) {
                        continue;
                    }

                    $nilaiTarget = (float) ($row['Target'] ?? 0);
                    $satuan = trim($row['Satuan'] ?? '-');
                    $unitString = strtolower(trim($row['Unit Kerja'] ?? $row['PIC / Unit Kerja'] ?? ''));

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
