<?php

namespace App\Http\Controllers;

use App\Models\BuktiEvaluasi;
use App\Models\BuktiPelaksanaan;
use App\Models\BuktiPengendalian;
use App\Models\Indikator;
use App\Models\link;
use App\Models\Penetapan;
use App\Models\Peningkatan;
use App\Models\Sheet;
use App\Models\Standar;
use App\Models\Target;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Evaluasi;
use Illuminate\Support\Facades\Log;

class EvaluasiController extends Controller {

    public function getEvaluasi($jurusan, $periode, $tipePendidikan, $tipe) {
        $sheets = Sheet::where('jurusan', '=', $jurusan)
            ->where('periode', '=', $periode)
            ->where('tipe_sheet', '=', $tipePendidikan)
            ->get();

        if ($sheets->isEmpty()) {
            return response()->json("Null");
        }

        $respond = [];
        foreach ($sheets as $shiit) {
            $penetapan = Penetapan::where('id_sheet', '=', $shiit->id)->first();
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

                    foreach ($indikator as $i) {
                        if ($i->id_standar == $s->id) {
                            $tar = null;
                            foreach ($target as $t) {
                                if ($t->id_indikator == $i->id) {
                                    $tar = $t;
                                }
                            }

                            $buk = '';
                            $idB = '';
                            $pelaksanaanEditor = '';
                            $eva = '';
                            $adj = '';
                            $evalEditor = '';
                            $idE = '';
                            $idBE = '';
                            foreach ($bukti as $b) {
                                if ($b->id_indikator == $i->id) {
                                    $buk = $b->komentar;
                                    $idB = $b->id;
                                    $pelaksanaanEditor = $b->edited_by;

                                    foreach ($buktieval as $e) {
                                        if ($e->id_bukti_pelaksanaan == $b->id) {
                                            $idBE = $e->id;
                                            $eva = $e->komentar;
                                            $adj = $e->adjustment;
                                            $idE = $e->id_evaluasi;
                                            $evalEditor = $e->edited_by;
                                        }
                                    }
                                }
                            }

                            $newIndicator = [
                                'idBuktiPelaksanaan' => $idB,
                                'idPelaksanaan' => $shiit->id,
                                'komentarEvaluasi' => $eva,
                                'idIndikator' => $i->id,
                                'idEvaluasi' => $shiit->id,
                                'adjusment' => $adj,
                                'indicator' => $i->note,
                                'target' => $tar->value,

                                'bukti' => $buk,
                                'editorPelaksanaan' => $pelaksanaanEditor,
                                'idBuktiEval' => $idBE,
                                'editorEval' => $evalEditor,
                                'isUpdate' => false,
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

    public function submitEval(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'data.idBuktiPelaksanaan' => 'required|exists:bukti_pelaksanaans,id',
                'data.idEvaluasi'         => 'required',
                'data.komentarEvaluasi'   => 'required|string',
                'data.adjusment'          => 'required|string',
                'data.userName'           => 'required|string',
                'data.idIndikator'        => 'required|exists:indikators,id',
                'data.indicator'          => 'nullable|string',
            ]);

            $idBP             = $validatedData['data']['idBuktiPelaksanaan'];
            $idEvaluasi       = $validatedData['data']['idEvaluasi'];
            $komentarEvaluasi = $validatedData['data']['komentarEvaluasi'];
            $adjusment        = $validatedData['data']['adjusment'];
            $userName         = $validatedData['data']['userName'];
            $idInd            = $validatedData['data']['idIndikator'];
            $indica           = $validatedData['data']['indicator'];

            $buktiEvaluasi = BuktiEvaluasi::updateOrCreate(
                ['id_bukti_pelaksanaan' => $idBP],
                [
                    'id_evaluasi'  => $idEvaluasi,
                    'komentar'     => $komentarEvaluasi,
                    'adjustment'   => $adjusment,
                    'edited_by'    => $userName,
                ]
            );

            $indicator = Indikator::find($idInd);
            if ($indicator && $indicator->note !== $indica) {
                $indicator->update(['note' => $indica]);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Data berhasil disimpan',
                'data'    => $buktiEvaluasi
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
