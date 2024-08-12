<?php

namespace App\Http\Controllers;

use App\Http\Resources\SheetResource;
use App\Models\BuktiEvaluasi;
use App\Models\BuktiPelaksanaan;
use App\Models\Evaluasi;
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
        $sheets = Sheet::where('jurusan', '=', $jurusan)
                       ->where('periode', '=', $periode)
                       ->where('tipe_sheet', '=', $tipePendidikan)
                       ->get();

        if ($sheets->isEmpty()){
            return response()->json("Null");
        }

        $respond = [];
        foreach ($sheets as $shiit) {
            $penetapan = Penetapan::where('id_sheet', '=', $shiit->id)->first();
            $evaluasi = Evaluasi::where('id_sheet', '=', $shiit->id)->first();
            if ($penetapan) {
                $standars = Standar::where('id_penetapan', $penetapan->id)->where('tipe', '=', $tipe)->get();
                $indikator = Indikator::all();
                $target = Target::all();
                $bukti = BuktiPelaksanaan::all();
                $buktieval = BuktiEvaluasi::all();

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

                            $buk = '';
                            $idB = '';
                            $eva = '';
                            $adj = '';
                            $idE = '';
                            $idBE = '';
                            foreach ($bukti as $b){
                                if ($b->id_indikator == $i->id){
                                    $buk = $b->komentar;
                                    $idB = $b->id;

                                    foreach ($buktieval as $e) {
                                        if ($e->id_bukti_pelaksanaan == $b->id){
                                            $idBE = $e->id;
                                            $eva = $e->komentar;
                                            $adj = $e->adjustment;
                                            $idE = $e->id_evaluasi;
                                        }
                                    }
                                }
                            }

                            $newIndicator = [
                                'idPelaksanaan' =>$shiit->id,
                                'id' => $i->id,
                                'indicator' => $i->note,
                                'target' => $tar->value,
                                'bukti' => $buk,
                                'idBukti' => $idB,
                                'evaluasi' => $eva,
                                'adjusment' => $adj,
                                'idEvaluasi' => $idE,
                                'idBuktiEval' => $idBE,
                            ];
                            array_push($data['indicators'], $newIndicator);
                        }
                    }

                    array_push($respond, $data);
                }
            }
        }

        return response()->json($respond);
    }

    public function getPeriode($jurusan)
    {
        $sheets = Sheet::where('jurusan', $jurusan)->get()->unique('periode');
        // $responseData = new SheetResource($sheets);
        return response()->json($sheets);
    }

    public function submitPelaksanaan(Request $request){
        $data = $request->input('data');

        foreach ($data as $item) {
            $idIndikator = $item['idIndikator'];
            $bukti = $item['bukti'];
            $idPelaksanaan = $item['idPelaksanaan'];

            $buktiPelaksanaan = BuktiPelaksanaan::where('id_indikator', $idIndikator)->first();

            if ($buktiPelaksanaan) {
                $buktiPelaksanaan->komentar = $bukti;
                $buktiPelaksanaan->save();
            } else {
                BuktiPelaksanaan::create([
                    'id_pelaksanaan' => $idPelaksanaan,
                    'id_indikator' => $idIndikator,
                    'komentar' => $bukti
                ]);
            }
        }

        return $this->sendRespons($data,'iki datane');
    }
}
