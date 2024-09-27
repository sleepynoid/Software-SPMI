<?php

namespace App\Http\Controllers;

use App\Models\BuktiEvaluasi;
use App\Models\BuktiPelaksanaan;
use App\Models\Indikator;
use App\Models\link;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Evaluasi;
use Illuminate\Support\Facades\Log;

class EvaluasiController extends Controller {
    //
    public function postComment(Request $request) {
        // Log::info('posting BuktiEvaluasi');
        $bukti_evaluasi = $request->json()->all()['data'];

        // Ensure $data is always an array for consistent processing
        $bukti_evaluasi = is_array($bukti_evaluasi) && isset($bukti_evaluasi[0]['idBuktiPelaksanaan']) ? $bukti_evaluasi : [$bukti_evaluasi];
//        $bukti_evaluasi = is_array($bukti_evaluasi) && isset($data[0]['idBuktiPelaksanaan']) ? $bukti_evaluasi : [$bukti_evaluasi];

        // loop to check if id bukti pelaksanaan valid
        $buktiValid = [];
        foreach ($bukti_evaluasi as $bukti) {
            // Check if each bukti contains the necessary fields
            if (!isset($bukti['adjustment']) || !isset($bukti['komentarEvaluasi']) || !isset($bukti['idBuktiPelaksanaan']) || !isset($bukti['idEvaluasi'])) {
                // Log::info('Invalid data format', $bukti);
                return $this->sendError('Data harus memiliki idBuktiPelaksanaan, komentarEvaluasi, dan adjustment yang valid', $bukti);
            }

            // query to check id is match with $id_pelaksanaan return boolean
            $id_bukti_pelaksanaan = $bukti['idBuktiPelaksanaan'];
            $isExist = BuktiPelaksanaan::where('id', $id_bukti_pelaksanaan)->exists();
            // Log::info($isExist);
            if (!$isExist) {
                // Log::warning($bukti);
                return $this->sendError('Id bukti pelaksanaan tidak valid', $bukti);
            }
            $buktiValid[] = $bukti;
        }

        // Create valid bukti evaluasi
        foreach ($buktiValid as $bukti) {
            // Log::info('Creating bukti: ', $bukti);
            BuktiEvaluasi::create([
                'adjustment' => $bukti['adjustment'],
                'komentar' => $bukti['komentarEvaluasi'],
                'id_evaluasi' => $bukti['idEvaluasi'],
                'id_bukti_pelaksanaan' => $bukti['idBuktiPelaksanaan'],
            ]);
        }
        return $this->sendRespons($bukti, 'create BuktiEvaluasi success');
    }

    public function submitEval(Request $request){
        $item = $request->input('data');

            $idBP = $item['idBuktiPelaksanaan'];
            $adjusment = $item['adjusment'];
            $komentarEvaluasi = $item['komentarEvaluasi'];
            $idEvaluasi = $item['idEvaluasi'];
            $userName = $item['userName'];
            $idInd = $item['idInd'];
            $indica = $item['indicator'];

            $isEval = BuktiEvaluasi::where('id_bukti_pelaksanaan', $idBP)->first();

            if ($isEval) {
                $isEval->komentar = $komentarEvaluasi;
                $isEval->adjustment = $adjusment;
                $isEval->edited_by  = $userName;

                $isEval->save();
            } else {
                BuktiEvaluasi::create([
                    'id_bukti_pelaksanaan' => $idBP,
                    'id_evaluasi' => $idEvaluasi,
                    'komentar' => $komentarEvaluasi,
                    'adjustment' => $adjusment,
                    'edited_by' => $userName,
                ]);
            }

            $indicator = Indikator::find($idInd);
            if ($indicator->note != $indica){
                $indicator->note = $indica;

                $indicator->save();
            }

        return response()->json('all good');
    }


    public function delComment(Request $request) {
        $idBukti = $request->input('idBukti');
        $bukti = BuktiEvaluasi::find($idBukti);
        if (!$bukti) {
            return response()->json(['message' => 'Comment not found.'], 404);
        }
        $bukti->delete();
        return response()->json(['message' => 'Comment deleted successfully.']);
    }

    public function getLink($idBukti) {
        $data = link::where('tipe_link', 'bukti_evaluasi')->where('id_bukti', $idBukti)->get();
        if ($data) {
            return response()->json([
                'success' => 'false',
                'message' => 'id bukti evaluasi not found'
            ]);
        }
        return response()->json([
            'success' => 'true'
        ]);
    }
}
