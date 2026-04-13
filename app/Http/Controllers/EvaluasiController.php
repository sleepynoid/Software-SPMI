<?php

namespace App\Http\Controllers;

use App\Models\BuktiEvaluasi;
use App\Models\Indikator;
use App\Models\Sheet;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EvaluasiController extends Controller {

    public function getEvaluasiData($jurusan, $periode, $tipePendidikan, $tipe) {
        $sheets = Sheet::with([
                'penetapan.standars' => function($q) use ($tipe) {
                    $q->where('tipe', $tipe);
                },
                'penetapan.standars.indikator.target',
                'penetapan.standars.indikator.buktiPelaksanaan.buktiEvaluasi',
            ])
            ->whereHas('jurusan', function($q) use ($jurusan) {
                if (is_numeric($jurusan)) {
                    $q->where('id', $jurusan);
                } else {
                    $q->where('nama', $jurusan)->orWhere('kode', $jurusan);
                }
            })
            ->where('periode', '=', $periode)
            ->where('tipe_sheet', '=', $tipePendidikan)
            ->get();

        if ($sheets->isEmpty()) {
            return [];
        }

        $respond = [];
        foreach ($sheets as $shiit) {
            $penetapan = $shiit->penetapan;
            if ($penetapan) {
                foreach ($penetapan->standars as $s) {
                    $data = [
                        'standar' => $s->note,
                        'indicators' => []
                    ];

                    foreach ($s->indikator as $i) {
                        $tar = $i->target;
                        $b = $i->buktiPelaksanaan;
                        $e = $b ? $b->buktiEvaluasi : null;

                        $newIndicator = [
                            'idBuktiPelaksanaan' => $b ? $b->id : '',
                            'idPelaksanaan' => $shiit->id,
                            'komentarEvaluasi' => $e ? $e->komentar : '',
                            'idIndikator' => $i->id,
                            'idEvaluasi' => $shiit->id, 
                            'adjusment' => $e ? $e->adjustment : '',
                            'indicator' => $i->note,
                            'target' => $tar ? $tar->value : null,

                            'bukti' => $b ? $b->komentar : '',
                            'editorPelaksanaan' => $b ? $b->edited_by : '',
                            'idBuktiEval' => $e ? $e->id : '',
                            'editorEval' => $e ? $e->edited_by : '',
                            'isUpdate' => false,
                        ];
                        $data['indicators'][] = $newIndicator;
                    }
                    $respond[] = $data;
                }
            }
        }

        return $respond;
    }

    public function getEvaluasi($jurusan, $periode, $tipePendidikan, $tipe) {
        $respond = $this->getEvaluasiData($jurusan, $periode, $tipePendidikan, $tipe);
        return response()->json($respond ?: "Null");
    }

    public function submitEval(Request $request) {
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

        BuktiEvaluasi::updateOrCreate(
            ['id_bukti_pelaksanaan' => $idBP],
            [
                'id_evaluasi'  => $idEvaluasi,
                'komentar'     => $komentarEvaluasi,
                'adjustment'   => $adjusment,
                'edited_by'    => $userName,
            ]
        );

        $indicator = Indikator::find($idInd);
        if ($indicator && !empty($indica) && $indicator->note !== $indica) {
            $indicator->update(['note' => $indica]);
        }

        return back()->with('success', 'Data berhasil disimpan');
    }
}
