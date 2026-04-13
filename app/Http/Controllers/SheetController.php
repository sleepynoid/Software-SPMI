<?php

namespace App\Http\Controllers;

use App\Http\Resources\SheetResource;
use App\Models\BuktiEvaluasi;
use App\Models\BuktiPelaksanaan;
use App\Models\BuktiPengendalian;
use App\Models\Evaluasi;
use App\Models\Indikator;
use App\Models\Penetapan;
use App\Models\Peningkatan;
use App\Models\Sheet;
use App\Models\Standar;
use App\Models\Target;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class SheetController extends Controller {

    public function getPeriode($jurusan) {
        $sheets = Sheet::where('jurusan', $jurusan)->get()->unique('periode');
        // $responseData = new SheetResource($sheets);
        return response()->json($sheets);
    }

    public function getAllSheet() {
        $sheets = Sheet::all();
        $responseData = SheetResource::collection($sheets);
        if ($sheets->isEmpty()) {
            return response()->json(['message' => 'Data not found'], 404);
        }
        return response()->json($responseData, 200);
    }

    public function getAllSheetForInertia(): array {
        return Sheet::with('jurusan')
            ->get()
            ->map(fn ($s) => [
                'id'      => $s->id,
                'jurusan' => $s->jurusan?->nama ?? $s->jurusan?->kode ?? '-',
                'tipe'    => $s->tipe_sheet,
                'periode' => $s->periode,
            ])
            ->toArray();
    }

    public function downloadExcel() {
        return response()->download(storage_path('../dokumentasi/example.xlsx'));
    }

    public function show($jurusan, $periode, $tipeSheet, $step = 'input') {
        $role = \Illuminate\Support\Facades\Auth::user()->role;
        $data = [];

        if ($role === 'Pelaksanaan') {
            $data = app(PelaksanaanController::class)->getPelaksanaanData($jurusan, $periode, $tipeSheet, $step);
        } elseif ($role === 'Evaluasi') {
            $data = app(EvaluasiController::class)->getEvaluasiData($jurusan, $periode, $tipeSheet, $step);
        } elseif ($role === 'Pengendalian') {
            $data = app(PengendalianController::class)->getPengendalianData($jurusan, $periode, $tipeSheet, $step);
        } elseif ($role === 'Peningkatan') {
            $data = app(PeningkatanController::class)->getPeningkatanData($jurusan, $periode, $tipeSheet, $step);
        }

        // Return Inertia request dengan prop `sheetData`
        return \Inertia\Inertia::render('sheet', [
            'jurusan' => $jurusan,
            'periode' => $periode,
            'tipeSheet' => $tipeSheet,
            'currentStep' => $step,
            'role' => $role,
            'userName' => \Illuminate\Support\Facades\Auth::user()->name,
            'sheetData' => $data
        ]);
    }
}
