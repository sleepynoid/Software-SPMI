<?php

namespace App\Http\Controllers;

use App\Models\BuktiPengendalian;
use App\Models\Sheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PengendalianController extends Controller
{
    public function getPengendalianData($jurusan, $periode, $tipePendidikan, $tipe) {
        $sheets = Sheet::with([
                'penetapan.standars' => function($q) use ($tipe) {
                    $q->where('tipe', $tipe);
                },
                'penetapan.standars.indikator.target',
                'penetapan.standars.indikator.buktiPelaksanaan.buktiEvaluasi.buktiPengendalian',
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
                        $bp = $e ? $e->buktiPengendalian : null;

                        $newIndicator = [
                            'idPelaksanaan' => $shiit->id,
                            'id' => $i->id,
                            'indicator' => $i->note,
                            'target' => $tar ? $tar->value : null,
                            'idBukti' => $b ? $b->id : '',
                            'bukti' => $b ? $b->komentar : '',
                            'editorPelaksanaan' => $b ? $b->edited_by : '',

                            'idEvaluasi' => $shiit->id,
                            'idBuktiEvaluasi' => $e ? $e->id : '',
                            'evaluasi' => $e ? $e->komentar : '',
                            'adjusment' => $e ? $e->adjustment : '',
                            'editorEval' => $e ? $e->edited_by : '',

                            'idBPengendalian' => $bp ? $bp->id : '',
                            'temuan' => $bp ? $bp->temuan : '',
                            'akarMasalah' => $bp ? $bp->akar_masalah : '',
                            'rtl' => $bp ? $bp->rtl : '',
                            'pelaksanaanRtl' => $bp ? $bp->pelaksanaan_rtl : '',
                            'editorPengendali' => $bp ? $bp->edited_by : '',
                        ];
                        $data['indicators'][] = $newIndicator;
                    }

                    $respond[] = $data;
                }
            }
        }

        return $respond;
    }

    public function getPengendalian($jurusan, $periode, $tipePendidikan, $tipe) {
        $respond = $this->getPengendalianData($jurusan, $periode, $tipePendidikan, $tipe);
        return response()->json($respond ?: "Null");
    }

    public function submitPengendalian(Request $request) {
        $validatedData = $request->validate([
            'data.idBuktiEvaluasi'   => 'required|exists:bukti_evaluasis,id',
            'data.temuan'            => 'required|string',
            'data.akarMasalah'       => 'nullable|string',
            'data.rtl'               => 'nullable|string',
            'data.pelaksanaanRtl'    => 'nullable|string',
            'data.userName'          => 'required|string',
        ]);

        $idBuktiEvaluasi = $validatedData['data']['idBuktiEvaluasi'];
        $temuan          = $validatedData['data']['temuan'];
        $akarMasalah     = $validatedData['data']['akarMasalah'];
        $rtl             = $validatedData['data']['rtl'];
        $pelaksanaanRtl  = $validatedData['data']['pelaksanaanRtl'];
        $userName        = $validatedData['data']['userName'];

        BuktiPengendalian::updateOrCreate(
            ['id_bukti_evaluasi' => $idBuktiEvaluasi],
            [
                'temuan'           => $temuan,
                'akar_masalah'     => $akarMasalah,
                'rtl'              => $rtl,
                'pelaksanaan_rtl'  => $pelaksanaanRtl,
                'edited_by'        => $userName,
            ]
        );

        return back()->with('success', 'Pengendalian berhasil diproses');
    }
}
