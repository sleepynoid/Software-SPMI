<?php

namespace App\Http\Controllers;

use App\Models\BuktiEvaluasi;
use App\Models\BuktiPelaksanaan;
use App\Models\Indikator;
use App\Models\link;
use App\Models\Penetapan;
use App\Models\Sheet;
use App\Models\Standar;
use App\Models\Target;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PelaksanaanController extends Controller {

    public function getPelaksanaan($jurusan, $periode, $tipePendidikan, $tipe) {
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
                            foreach ($bukti as $b) {
                                if ($b->id_indikator == $i->id) {
                                    $buk = $b->komentar;
                                    $idB = $b->id;
                                    $pelaksanaanEditor = $b->edited_by;
                                }
                            }

                            $newIndicator = [
                                'idBuktiPelaksanaan' => $idB,
                                'idPelaksanaan' => $shiit->id,
                                'idIndikator' => $i->id,
                                'indicator' => $i->note,
                                'target' => $tar->value,
                                'komentarPelaksanaan' => $buk,
                                'editorPelaksanaan' => $pelaksanaanEditor,
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

    public function submitPelaksanaan(Request $request)
    {
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
        $data = link::where('id_bukti', $idBukti)->where('tipe_link', $tipeLink)->get();

        return response()->json($data);
    }

    public function deleteLink(Request $request) {
        $id = $request->input('idLink');
        $link = link::find($id);

        if ($link) {
            $link->delete();
        }

        return response()->json("deleted");
    }
    public function postLink(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'data.id' => 'nullable|string',
                'data.idBukti' => 'required|string',
                'data.judul_link' => 'required|string',
                'data.link' => 'required|url',
                'data.tipeLink' => 'required|string',
            ]);

            $id = $validatedData['data']['id'] ?? null;
            $idBukti = $validatedData['data']['idBukti'];
            $judulLink = $validatedData['data']['judul_link'];
            $link = $validatedData['data']['link'];
            $tipeLink = $validatedData['data']['tipeLink'];

            if ($id) {
                $linkData = Link::find($id);
                if ($linkData) {
                    $linkData->update([
//                        'id_bukti' => $idBukti,
//                        'tipe_link' => $tipeLink,
                        'link' => $link,
                        'judul_link' => $judulLink,
                    ]);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Data tidak ditemukan untuk diperbarui'
                    ], 404);
                }
            } else {
                Link::create([
                    'id_bukti' => $idBukti,
                    'tipe_link' => $tipeLink,
                    'link' => $link,
                    'judul_link' => $judulLink,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Link berhasil disimpan'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan link',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
