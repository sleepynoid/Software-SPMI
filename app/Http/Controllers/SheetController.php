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
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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
                $buktiPengendalian = BuktiPengendalian::all();
                $buktiPeningkatan = Peningkatan::all();

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
                            $pelaksanaanEditor = '';
                            $eva = '';
                            $adj = '';
                            $idE = '';
                            $idBE = '';
                            $idBPengendalian = '';
                            $temuan = '';
                            $akar_masalah = '';
                            $rtl = '';
                            $pelaksanaan_rtl = '';
                            $idPeningkatan = '';
                            $komenPeningkatan='';
                            foreach ($bukti as $b){
                                if ($b->id_indikator == $i->id){
                                    $buk = $b->komentar;
                                    $idB = $b->id;
                                    $pelaksanaanEditor = $b->edited_by;

                                    foreach ($buktieval as $e) {
                                        if ($e->id_bukti_pelaksanaan == $b->id){
                                            $idBE = $e->id;
                                            $eva = $e->komentar;
                                            $adj = $e->adjustment;
                                            $idE = $e->id_evaluasi;

                                            foreach ($buktiPengendalian as $bp) {
                                                if ($bp->id_bukti_evaluasi == $e->id){
                                                    $idBPengendalian = $bp->id;
                                                    $temuan = $bp->temuan;
                                                    $akar_masalah = $bp->akar_masalah;
                                                    $rtl = $bp->rtl;
                                                    $pelaksanaan_rtl = $bp->pelaksanaan_rtl;

                                                    foreach ($buktiPeningkatan as $p){
                                                        if ($p->id_pengendalian == $bp->id){
                                                            $idPeningkatan = $p->id;
                                                            $komenPeningkatan = $p->komentar;
                                                        }
                                                    }

                                                }
                                            }

                                        }
                                    }
                                }
                            }

                            $newIndicator = [
                                'idPelaksanaan'    =>$shiit->id,
                                'id'               => $i->id,
                                'indicator'        => $i->note,
                                'target'           => $tar->value,
                                'idBukti'          => $idB,
                                'bukti'            => $buk,
                                'editorPelaksanaan'=> $pelaksanaanEditor,
                                'idEvaluasi'       => $idE,
                                'idBuktiEval'      => $idBE,
                                'evaluasi'         => $eva,
                                'adjusment'        => $adj,
                                'idBPengendalian'  => $idBPengendalian,
                                'temuan'           => $temuan,
                                'akar_masalah'     => $akar_masalah,
                                'rtl'              => $rtl,
                                'pelaksanaan_rtl'  => $pelaksanaan_rtl,
                                'idPeningkatan'    => $idPeningkatan,
                                'komenPeningkatan' => $komenPeningkatan,
                                'isUpdate'           => false,
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
        $item = $request->input('data');

            $idIndikator = $item['idIndikator'];
            $bukti = $item['bukti'];
            $idPelaksanaan = $item['idPelaksanaan'];
            $userName = $item['userName'];

            $buktiPelaksanaan = BuktiPelaksanaan::where('id_indikator', $idIndikator)->first();

            if ($buktiPelaksanaan) {
                if ($bukti !== null) {
                    $buktiPelaksanaan->komentar = $bukti;
                    $buktiPelaksanaan->edited_by = $userName;
                    $buktiPelaksanaan->save();
                }
            } else {
                if ($bukti !== null) {
                    BuktiPelaksanaan::create([
                        'id_pelaksanaan' => $idPelaksanaan,
                        'id_indikator' => $idIndikator,
                        'komentar' => $bukti,
                        'edited_by' => $userName,
                    ]);
                }
            }

        return response('all good');
    }

    public function downloadExcel(){
        return response()->download(storage_path('../dokumentasi/example.xlsx'));
    }
}
