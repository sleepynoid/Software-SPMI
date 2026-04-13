<?php

namespace App\Http\Controllers;

use App\Models\Peningkatan;
use App\Models\Sheet;
use Illuminate\Http\Request;

class PeningkatanController extends Controller
{
    public function getPeningkatan($jurusan, $periode, $tipePendidikan, $tipe) {
        $sheets = Sheet::with([
                'penetapan.standars' => function($q) use ($tipe) {
                    $q->where('tipe', $tipe);
                },
                'penetapan.standars.indikator.target',
                'penetapan.standars.indikator.buktiPelaksanaan.buktiEvaluasi.buktiPengendalian.peningkatan',
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
            return response()->json("Null");
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
                        $p = $bp ? $bp->peningkatan : null;

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

                            'idBuktiPengendalian' => $bp ? $bp->id : '',
                            'temuan' => $bp ? $bp->temuan : '',
                            'akar_masalah' => $bp ? $bp->akar_masalah : '',
                            'rtl' => $bp ? $bp->rtl : '',
                            'pelaksanaanRtl' => $bp ? $bp->pelaksanaan_rtl : '',
                            'editorPengendali' => $bp ? $bp->edited_by : '',

                            'idPeningkatan' => $p ? $p->id : '',
                            'komenPeningkatan' => $p ? $p->komentar : '',
                            'editorPeningkatan' => $p ? $p->edited_by : '',
                            'isUpdate' => false,
                        ];
                        $data['indicators'][] = $newIndicator;
                    }

                    $respond[] = $data;
                }
            }
        }

        return response()->json($respond);
    }

    public function submitPeningkatan(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'data.idBuktiPengendalian' => 'required|exists:bukti_pengendalians,id',
                'data.komenPeningkatan' => 'required|string',
                'data.userName' => 'required|string',
            ]);

            $idBuktiPengendalian = $validatedData['data']['idBuktiPengendalian'];
            $komen = $validatedData['data']['komenPeningkatan'];
            $editor = $validatedData['data']['userName'];

            $peningkatan = Peningkatan::updateOrCreate(
                ['id_pengendalian' => $idBuktiPengendalian],
                ['komentar' => $komen, 'edited_by' => $editor]
            );

            return response()->json([
                'message' => 'Peningkatan berhasil diproses',
                'data' => $peningkatan
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat memproses data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
