<?php

namespace App\Http\Controllers;

use App\Models\BuktiPelaksanaan;
use App\Models\Indikator;
use App\Models\Link;
use App\Models\Penetapan;
use App\Models\Sheet;
use App\Models\Standar;
use App\Models\Target;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PelaksanaanController extends Controller {

    public function getPelaksanaan($jurusan, $periode, $tipePendidikan, $tipe) {
        $sheets = Sheet::with([
                'penetapan.standars' => function($q) use ($tipe) {
                    $q->where('tipe', $tipe);
                },
                'penetapan.standars.indikator.target',
                'penetapan.standars.indikator.buktiPelaksanaan',
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
                        $buk = $i->buktiPelaksanaan;

                        $newIndicator = [
                            'idBuktiPelaksanaan' => $buk ? $buk->id : '',
                            'idPelaksanaan' => $shiit->id,
                            'idIndikator' => $i->id,
                            'indicator' => $i->note,
                            'target' => $tar ? $tar->value : null,
                            'komentarPelaksanaan' => $buk ? $buk->komentar : '',
                            'editorPelaksanaan' => $buk ? $buk->edited_by : '',
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

    public function submitPelaksanaan(Request $request) {
        try {
            $validatedData = $request->validate([
                'data.idIndikator'         => 'required|exists:indikators,id',
                'data.komentarPelaksanaan' => 'required|string',
                'data.idPelaksanaan'       => 'required',
                'data.userName'            => 'required|string',
            ]);

            $idIndikator   = $validatedData['data']['idIndikator'];
            $bukti         = $validatedData['data']['komentarPelaksanaan'];
            $idPelaksanaan = $validatedData['data']['idPelaksanaan'];
            $userName      = $validatedData['data']['userName'];

            $buktiPelaksanaan = BuktiPelaksanaan::updateOrCreate(
                ['id_indikator' => $idIndikator],
                [
                    'id_pelaksanaan' => $idPelaksanaan,
                    'komentar'       => $bukti,
                    'edited_by'      => $userName,
                ]
            );

            return response()->json([
                'status'  => 'success',
                'message' => 'Data berhasil disimpan',
                'data'    => $buktiPelaksanaan,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function getLink($idBukti, $tipeLink) {
        $type = $tipeLink === 'Pelaksanaan' ? \App\Models\BuktiPelaksanaan::class : \App\Models\BuktiEvaluasi::class;
        $data = Link::where('linkable_id', $idBukti)->where('linkable_type', $type)->get();

        return response()->json($data);
    }

    public function deleteLink(Request $request) {
        $id = $request->input('idLink');
        $link = Link::find($id);

        if ($link) {
            $link->delete();
        }

        return response()->json("deleted");
    }

    public function postLink(Request $request) {
        try {
            $validatedData = $request->validate([
                'data.id' => 'nullable',
                'data.idBukti' => 'required',
                'data.judul_link' => 'required|string',
                'data.link' => 'required|url',
                'data.tipeLink' => 'required|string',
            ]);

            $id = $validatedData['data']['id'] ?? null;
            $idBukti = $validatedData['data']['idBukti'];
            $judulLink = $validatedData['data']['judul_link'];
            $linkUrl = $validatedData['data']['link'];
            $tipeLink = $validatedData['data']['tipeLink'];
            $type = $tipeLink === 'Pelaksanaan' ? \App\Models\BuktiPelaksanaan::class : \App\Models\BuktiEvaluasi::class;

            if ($id) {
                $linkData = Link::find($id);
                if ($linkData) {
                    $linkData->update([
                        'link' => $linkUrl,
                        'judul_link' => $judulLink,
                    ]);
                } else {
                    return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
                }
            } else {
                Link::create([
                    'linkable_id' => $idBukti,
                    'linkable_type' => $type,
                    'link' => $linkUrl,
                    'judul_link' => $judulLink,
                ]);
            }

            return response()->json(['status' => 'success', 'message' => 'Link berhasil disimpan'], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
