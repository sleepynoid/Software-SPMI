<?php

namespace App\Http\Controllers;

use App\Models\BuktiPelaksanaan;
use App\Models\Indikator;
use App\Models\link;
use App\Models\Pelaksanaan;
use App\Models\Standar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PelaksanaanController extends Controller {
    //
    public function postComment(Request $request) {
        Log::info('posting comment');

        // validasi request
        // $request->validate([
        //     'data.bukti' => 'required|string'
        // ]);
        // $data = $request->json()->all();

        // Retrieve the JSON data from the request
        $bukti_pelaksanaan = $request->json()->all()['data'];
        // Log::info($bukti_pelaksanaan);

        // check if JSON data is array
        if (!is_array($bukti_pelaksanaan)) {
            Log::info('is  not an array');
            return $this->sendError('data harus array', $bukti_pelaksanaan);
        }

        // loop to check if id pelaksanaan & indikator valid
        $buktiValid = [];
        foreach ($bukti_pelaksanaan as $bukti) {
            $id_indikator = $bukti['id_pelaksanaan'];
            // query to check id is match with $id_indikator return boolean
            $isExist = Indikator::where('id', $id_indikator)->exists();
            if (!$isExist) {
                Log::warning($bukti);
                return $this->sendError('Id indikator tidak valid', $bukti);
            }
            // check id pelaksanaan, kalau id tidak ada return error
            $id_pelaksanaan = $bukti['id_pelaksanaan'];
            $isExist = Pelaksanaan::where('id', $id_pelaksanaan)->exists();
            if (!$isExist) {
                Log::warning($bukti);
                return $this->sendError('Id Pelaksanaan tidak valid', $bukti);
            }
            $buktiValid[] = $bukti;
        }
        // BuktiPelaksanaan::create($data);

        // check if JSON data is array
        // if (is_array($bukti_pelaksanaan)) {
        //     // Loop through each user and check if the address is in NY
        //     $validBukti = [];
        //     foreach ($bukti_pelaksanaan as $bukti) {
        //         if (isset($bukti['']['state']) && $bukti['address']['state'] === 'NY') {
        //             $validUsers[] = $bukti;
        //         }
        //     }
        // } else {

        // }
        // create bukti pelaksanaan yang valid saja
        foreach ($buktiValid as $bukti) {
            Log::info($bukti);
            BuktiPelaksanaan::create($bukti);
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

    public function getLink($idBukti) {
        $data = link::where('id_bukti_pelaksanaan', $idBukti)->get();

        if (!$data) {

            return response()->json("null");
        }

        return response()->json($data);
    }

    public function submitLink(Request $request) {
        $data = $request->input('data');

        if (is_array($data) && isset($data['idBukti']) && isset($data['judul_link']) && isset($data['link'])) {
            $idBukti = $data['idBukti'];
            $judul_link = $data['judul_link'];
            $link = $data['link'];

            link::create([
                'judul_link' => $judul_link,
                'link' => $link,
                'id_bukti_pelaksanaan' => $idBukti,
            ]);

            // Proses data lebih lanjut di sini

            return response()->json(['success' => 'Link submitted successfully']);
        }


        return response()->json("null");
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

    public function postLink(Request $request) {
        Log::info('posting link');
        $link_bukti = $request->json()->all()['data'];

        // Ensure $data is always an array for consistent processing
        $link_bukti = is_array($link_bukti) && isset($data[0]['idBukti']) ? $link_bukti : [$link_bukti];

        // loop to check if id bukti pelaksanaan valid
        $linkValid = [];
        foreach ($link_bukti as $link) {
            // Check if each link contains the necessary fields
            if (!isset($link['idBukti']) || !isset($link['judul_link']) || !isset($link['link'])) {
                Log::info('Invalid data format', $link);
                return $this->sendError('Data harus memiliki idBukti, judul_link, dan link yang valid', $link);
            }

            // query to check id is match with $id_pelaksanaan return boolean
            $id_bukti_pelaksanaan = $link['idBukti'];
            $isExist = BuktiPelaksanaan::where('id', $id_bukti_pelaksanaan)->exists();
            Log::info($isExist);
            if (!$isExist) {
                Log::warning($link);
                return $this->sendError('Id bukti pelaksanaan tidak valid', $link);
            }
            $linkValid[] = $link;
        }

        // Create valid links
        foreach ($linkValid as $link) {
            Log::info('Creating link: ', $link);
            Link::create([
                'judul_link' => $link['judul_link'],
                'link' => $link['link'],
                'id_bukti_pelaksanaan' => $link['idBukti'],
            ]);
        }
        return $this->sendRespons($link, 'create link success');
    }
}
