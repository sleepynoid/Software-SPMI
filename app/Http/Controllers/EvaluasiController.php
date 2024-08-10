<?php

namespace App\Http\Controllers;

use App\Models\BuktiEvaluasi;
use App\Models\BuktiPelaksanaan;
use Illuminate\Http\Request;
use App\Models\Evaluasi;
use Illuminate\Support\Facades\Log;

class EvaluasiController extends Controller {
    //
    public function postComment(Request $request) {
        Log::info('posting BuktiEvaluasi');
        $bukti_evaluasi = $request->json()->all()['data'];

        // Ensure $data is always an array for consistent processing
        $bukti_evaluasi = is_array($bukti_evaluasi) && isset($data[0]['idBuktiPelaksanaan']) ? $bukti_evaluasi : [$bukti_evaluasi];

        // loop to check if id bukti pelaksanaan valid
        $buktiValid = [];
        foreach ($bukti_evaluasi as $bukti) {
            // Check if each bukti contains the necessary fields
            if (!isset($bukti['adjustment']) || !isset($bukti['komentarEvaluasi']) || !isset($bukti['idBuktiPelaksanaan']) || !isset($bukti['idEvaluasi'])) {
                Log::info('Invalid data format', $bukti);
                return $this->sendError('Data harus memiliki idBuktiPelaksanaan, komentarEvaluasi, dan adjustment yang valid', $bukti);
            }

            // query to check id is match with $id_pelaksanaan return boolean
            $id_bukti_pelaksanaan = $bukti['idBuktiPelaksanaan'];
            $isExist = BuktiPelaksanaan::where('id', $id_bukti_pelaksanaan)->exists();
            Log::info($isExist);
            if (!$isExist) {
                Log::warning($bukti);
                return $this->sendError('Id bukti pelaksanaan tidak valid', $bukti);
            }
            $buktiValid[] = $bukti;
        }

        // Create valid bukti evaluasi
        foreach ($buktiValid as $bukti) {
            Log::info('Creating bukti: ', $bukti);
            BuktiEvaluasi::create([
                'adjustment' => $bukti['adjustment'],
                'komentar' => $bukti['komentarEvaluasi'],
                'Evaluasi' => $bukti['idEvaluasi'],
                'id_bukti_pelaksanaan' => $bukti['idBuktiPelaksanaan'],
            ]);
        }
        return $this->sendRespons($bukti, 'create BuktiEvaluasi success');
    }
}
