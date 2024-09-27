<?php

namespace App\Http\Controllers;

use App\Models\BuktiPengendalian;
use App\Models\BuktiEvaluasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PengendalianController extends Controller
{
    public function index()
    {
        $pengendalians = BuktiPengendalian::all();
        return response()->json($pengendalians);
    }

    public function submitPengendalian(Request $request)
    {
        $item = $request->input('data');

            $idBuktiEvaluasi = $item['id_bukti_evaluasi'];
            $temuan = $item['temuan'];
            $akarMasalah = $item['akar_masalah'];
            $rtl = $item['rtl'];
            $pelaksanaanRtl = $item['pelaksanaan_rtl'];
            $userName = $item['userName'];


            $isEvaluasiExist = BuktiEvaluasi::where('id', $idBuktiEvaluasi)->exists();

            if (!$isEvaluasiExist) {
                return $this->sendError('Id bukti evaluasi tidak valid', $item);
            }

            $pengendalian = BuktiPengendalian::where('id_bukti_evaluasi', $idBuktiEvaluasi)->first();

            if ($pengendalian) {
                $pengendalian->update([
                    'temuan' => $temuan,
                    'akar_masalah' => $akarMasalah,
                    'rtl' => $rtl,
                    'pelaksanaan_rtl' => $pelaksanaanRtl,
                    'edited_by' => $userName,
                ]);
            } else {
                BuktiPengendalian::create([
                    'temuan' => $temuan,
                    'akar_masalah' => $akarMasalah,
                    'rtl' => $rtl,
                    'pelaksanaan_rtl' => $pelaksanaanRtl,
                    'id_bukti_evaluasi' => $idBuktiEvaluasi,
                    'edited_by' => $userName,
                ]);
            }

        return response()->json(['message' => 'Pengendalian berhasil diproses'], 200);
    }

    public function destroy($id)
    {
        $pengendalian = BuktiPengendalian::findOrFail($id);
        $pengendalian->delete();
        return response()->json(null, 204);
    }
}
