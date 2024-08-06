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
            return $this->sendError('data harus array',$bukti_pelaksanaan);
        }

        // loop to check if id pelaksanaan & indikator valid
        $buktiValid = [];
        foreach ($bukti_pelaksanaan as $bukti) {
            $id_indikator = $bukti['id_pelaksanaan'];
            // query to check id is match with $id_indikator return boolean
            $isExist = Indikator::where('id',$id_indikator)->exists();
            if (!$isExist) {
                Log::warning($bukti);
                return $this->sendError('Id indikator tidak valid',$bukti);
            }
            $id_pelaksanaan = $bukti['id_pelaksanaan'];
            $isExist = Pelaksanaan::where('id',$id_pelaksanaan)->exists();
            if (!$isExist) {
                Log::warning($bukti);
                return $this->sendError('Id Pelaksanaan tidak valid',$bukti);
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
        foreach ($buktiValid as $bukti) {
            Log::info($bukti);
            BuktiPelaksanaan::create($bukti);
        }
        return $this->sendRespons($buktiValid, 'create comment success');
    }

    public function getComment() {
        $data = BuktiPelaksanaan::all();
        return $this->sendRespons($data,'this is comment data');
    }

    public function getLink() {
        $data = link::all();
        return $this->sendRespons($data,'this is the link data');
    }

    public function postLink(Request $request) {
        Log::info('posting link');
        $link_bukti = $request->json()->all()['data'];

        // check if JSON data is array
        if (!is_array($link_bukti)) {
            Log::info('is  not an array');
            return $this->sendError('data harus array',$link_bukti);
        }

        // loop to check if id bukti pelaksanaan valid
        $linkValid = [];
        foreach ($link_bukti as $link) {
            $id_bukti_pelaksanaan = $link['id_bukti_pelaksanaan'];
            // query to check id is match with $id_pelaksanaan return boolean
            $isExist = link::where('id',$id_bukti_pelaksanaan)->exists();
            if (!$isExist) {
                Log::warning($link);
                return $this->sendError('Id bukti pelaksanaan tidak valid',$link);
            }
            $linkValid[] = $link;
        }
        foreach ($link_bukti as $link) {
            Log::info($link);
            BuktiPelaksanaan::create($link);
        }
        return $this->sendRespons($link, 'create link success');
    }

    public function storeData(Request $request) {
        Log::info('storeData method called'); // Debugging statement

        // Validate the incoming request
        $request->validate([
            'data' => 'required|string',
        ]);

        // Get current session data or initialize it as an empty array
        $storedData = $request->session()->get('stored_data', []);

        // Ensure $storedData is an array (in case it was not already)
        if (!is_array($storedData)) {
            $storedData = [$storedData];
        }

        // Append the new data to the session data
        $storedData[] = $request->input('data');

        // Store the updated data back in the session
        $request->session()->put('stored_data', $storedData);

        Log::info('Data stored successfully', ['user_id' => Auth::id(), 'data' => $storedData]);

        return response()->json(['message' => 'Data stored successfully']);
    }

    public function getData(Request $request) {
        Log::info('getData method called'); // Debugging statement

        // Retrieve data from the session
        $data = $request->session()->get('stored_data', 'No data found');
        Log::info('Data retrieved', ['data' => $data]);

        return response()->json(['data' => $data]);
    }
}
