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

    public function downloadExcel() {
        return response()->download(storage_path('../dokumentasi/example.xlsx'));
    }
}
