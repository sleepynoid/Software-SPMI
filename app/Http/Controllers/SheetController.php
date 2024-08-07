<?php

namespace App\Http\Controllers;

use App\Http\Resources\SheetResource;
use App\Models\Indikator;
use App\Models\Penetapan;
use App\Models\Sheet;
use App\Models\Standar;
use App\Models\Target;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SheetController extends Controller {
    //
    public function indexSheet() {
        $data = Sheet::all();
        $responseData = SheetResource::collection($data);
        return $this->sendRespons($responseData,'all sheet data');
    }

    public function getPenetapan($jurusan, $periode, $tipePendidikan, $tipe){

        $shiit = Sheet::where('jurusan', '=', $jurusan)->where('periode', '=',$periode)->where('tipe_sheet', '=',$tipePendidikan)->first();

        $penetapan = Penetapan::where('id_sheet', '=', $shiit->id)->first();
        $standars = Standar::where('id_penetapan', $penetapan->id)->where('tipe', '=', $tipe)->get();
        $indikator = Indikator::all();
        $target = Target::all();

        $respond = [];
        foreach ($standars as $s) {

            $data = [
                'standar' => $s->note,
                'indicators' => []
            ];


            foreach ($indikator as $i){
                if ($i->id_standar == $s->id){
                    $tar = null;
                    foreach ($target as $t){
                        if ($t->id_indikator == $i->id){
                            $tar = $t;
                        }
                    }

                    $newIndicator = ['id' => $i->id, 'indicator' => $i->note, 'target' => $tar->value, 'bukti' => ['']];
                    array_push($data['indicators'], $newIndicator);
                }
            }

            array_push($respond, $data);
        }


        return response()->json($respond);
    }

    public function getPeriode($jurusan)
    {
        $sheets = Sheet::where('jurusan', $jurusan)->get();
        // $responseData = new SheetResource($sheets);
        return response()->json($sheets);
    }

    public function submit(Request $request){
        $data = $request->json()->all();

//        $da = [];
//
//        foreach ($data as $d){
//            $newData = ['idIndikator' => $d['idIndikator'], 'bukti' => $d['bukti']];
//            array_push($da, $newData);
//        }


        return $this->sendRespons($data,'iki datane');
    }
}
