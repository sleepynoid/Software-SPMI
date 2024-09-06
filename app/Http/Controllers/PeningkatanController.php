<?php

namespace App\Http\Controllers;

use App\Models\BuktiPengendalian;
use App\Models\Peningkatan;
use Illuminate\Http\Request;

class PeningkatanController extends Controller
{
    public function submitPeningkatan(Request $request)
    {
        $item = $request->input('data');

        $idBuktiPengendalian = $item['idBuktiPengendalian'];
        $komen = $item['komenPeningkatan'];

        $isPengendalianiExist = BuktiPengendalian::where('id', $idBuktiPengendalian)->exists();

        if (!$isPengendalianiExist) {
            return $this->sendError('Id bukti Pengendalian tidak valid', $item);
        }

        $peningkatan = Peningkatan::where('id_pengendalian', $idBuktiPengendalian)->first();

        if ($peningkatan) {
            $peningkatan->update([
                'komentar' => $komen
            ]);
        } else {
            Peningkatan::create([
                'id_pengendalian' => $idBuktiPengendalian,
                'komentar' => $komen,
            ]);
        }

        return response()->json(['message' => 'Peningkatan berhasil diproses'], 200);
    }
}
