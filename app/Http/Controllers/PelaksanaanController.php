<?php

namespace App\Http\Controllers;

use App\Models\BuktiPelaksanaan;
use App\Models\link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PelaksanaanController extends Controller {
    //
    public function postComment(Request $request) {
        // Log::info('posting comment');

        // validasi request
        // $request->validate([
        //     'data.bukti' => 'required|string'
        // ]);
        // $data = $request->json()->all();

        // Retrieve the JSON data from the request
        $bukti_pelaksanaan = $request->json()->all()['data'];
        // Log::info($bukti_pelaksanaan);

        // Ensure $data is always an array for consistent processing
        $bukti_pelaksanaan = is_array($bukti_pelaksanaan) ? $bukti_pelaksanaan : [$bukti_pelaksanaan];

        // loop to check if id pelaksanaan & indikator valid
        $buktiValid = [];
        foreach ($bukti_pelaksanaan as $bukti) {
            // Check if each link contains the necessary fields
            if (!isset($bukti['idBukti']) || !isset($bukti['judul_link']) || !isset($bukti['link'])) {
                // Log::info('Invalid data format', $bukti);
                return $this->sendError('Data harus memiliki idBukti, judul_link, dan link yang valid', $bukti);
            }

            // query to check id is match with $id_pelaksanaan return boolean
            $id_bukti_pelaksanaan = $bukti['idBukti'];
            $isExist = BuktiPelaksanaan::where('id', $id_bukti_pelaksanaan)->exists();
            // Log::info($isExist);
            if (!$isExist) {
                // Log::warning($bukti);
                return $this->sendError('Id bukti pelaksanaan tidak valid', $bukti);
            }
            $linkValid[] = $bukti;
        }

        // create bukti pelaksanaan yang valid saja
        foreach ($buktiValid as $bukti) {
            // Log::info($bukti);
            BuktiPelaksanaan::create([
                'id_pelaksanaan' => $bukti_pelaksanaan['idPelaksanaan'],
                'id_indikator' => $bukti_pelaksanaan['idIndikator'],
                'komentar' => $bukti_pelaksanaan['komentar']
            ]);
        }
        return $this->sendRespons($buktiValid, 'create comment success');
    }

    public function getComment() {
        $data = BuktiPelaksanaan::all();
        return $this->sendRespons($data, 'this is comment data');
    }

    public function delComment(Request $request) {
        $idBukti = $request->input('idBukti');
        $bukti = BuktiPelaksanaan::find($idBukti);
        if (!$bukti) {
            return response()->json(['message' => 'Comment not found.'], 404);
        }
        $bukti->delete();
        return response()->json(['message' => 'Comment deleted successfully.']);
    }

    public function getLink($idBukti, $tipeLink) {
        $data = link::where('id_bukti', $idBukti)->where('tipe_link', $tipeLink)->get();

        return response()->json($data);
    }

    public function deleteLink(Request $request) {
        $idLink = $request->input('idLink');
        $id = $idLink['idL'];

        $link = link::find($id);

        if ($link) {
            $link->delete();
        }

        return response()->json("deleted");
    }

//    public function postLink(Request $request) {
//        // Log::info('posting link');
//        $link_bukti = $request->json()->all()['data'];
//
//        // Ensure $data is always an array for consistent processing
//        $link_bukti = is_array($link_bukti) && isset($data[0]['idBukti']) ? $link_bukti : [$link_bukti];
//
//        // loop to check if id bukti pelaksanaan valid
//        $linkValid = [];
//        foreach ($link_bukti as $link) {
//            // Check if each link contains the necessary fields
//            if (!isset($link['idBukti']) || !isset($link['judul_link']) || !isset($link['link'])) {
//                // Log::info('Invalid data format', $link);
//                if (!isset($link['idBukti']) || !isset($link['judul_link']) || !isset($link['link']) || !isset($link['tipeLink'])) {
//                    Log::info('Invalid data format', $link);
//                    return $this->sendError('Data harus memiliki idBukti, judul_link, dan link yang valid', $link);
//                }
//
//                // query to check id is match with $id_pelaksanaan return boolean
//                $id_bukti_pelaksanaan = $link['idBukti'];
//                $isExist = BuktiPelaksanaan::where('id', $id_bukti_pelaksanaan)->exists();
//                // Log::info($isExist);
//                if (!$isExist) {
//                    // Log::warning($link);
//                    return $this->sendError('Id bukti pelaksanaan tidak valid', $link);
//                }
//                $linkValid[] = $link;
//            }
//
//            // Create valid links
//            foreach ($linkValid as $link) {
//                // Log::info('Creating link: ', $link);
//                Link::create([
//                    'judul_link' => $link['judul_link'],
//                    'link' => $link['link'],
//                    'tipe_link' => $link['tipeLink'],
//                    'id_bukti' => $link['idBukti'],
//                ]);
//            }
//            return $this->sendRespons($link, 'create link success');
//        }
//
//
//    }

    public function postLink(Request $request){
        $data = $request->all();

        $idBukti = $data['idBukti'];
        $judul_link = $data['judul_link'];
        $link = $data['link'];
        $tipeLink = $data['tipeLink'];

        link::create([
            'id_bukti' => $idBukti,
            'judul_link' => $judul_link,
            'link' => $link,
            'tipe_link' => $tipeLink,
        ]);

            // Proses data lebih lanjut di sini

        return response()->json(['success' => 'Link submitted successfully']);

//        return response()->json("null");
    }
}
