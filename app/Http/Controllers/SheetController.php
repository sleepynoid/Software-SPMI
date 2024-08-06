<?php

namespace App\Http\Controllers;

use App\Http\Resources\SheetResource;
use App\Models\Sheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SheetController extends Controller {
    //
    public function indexSheet() {
        $data = Sheet::all();
        $responseData = SheetResource::collection($data);
        return $this->sendRespons($responseData,'all sheet data');
    }

    public function getPeriode(string $jurusan) {
        $sheets = Sheet::where('jurusan', $jurusan)->get();
        // $responseData = new SheetResource($sheets);
        return $this->sendRespons($sheets,'periode sheet');
    }

    public function getSheet($jurusan, $periode, $tipe='pendidikan') {
        $data = Sheet::where('jurusan', $jurusan)->where('periode', $periode)->where('tipe_sheet', $tipe)->first();
        $responseData = new SheetResource($data);
        // Log::info($responseData);
        return $this->sendRespons($data,'data sheet');
    }
}
